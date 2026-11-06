<?php

namespace App\Services\Questionnaires;

use App\Enums\QuestionnaireType;
use App\Models\Questionnaire;
use App\Models\SurveyTemplate;
use App\Services\Questionnaires\Instruments\SatisfactionInstrument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionnaireService
{
    private const BANNER_DIRECTORY = 'questionnaire-banners';

    public function __construct(private QuestionnaireBuilder $builder)
    {
    }

    /**
     * Crea o actualiza los datos generales de una encuesta.
     * Preguntas y respuestas se manejan aparte.
     */
    public function save(Questionnaire $questionnaire, array $data, ?UploadedFile $banner = null, bool $removeBanner = false): Questionnaire
    {
        if (! $questionnaire->exists) {
            $questionnaire->type = QuestionnaireType::from($data['type']);
        }

        $association = $data['association'] ?? 'none';

        $questionnaire->fill([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
            'closes_at' => $this->toUtc($data['closes_at'] ?? null),
            'company_id' => $association === 'company' ? $data['company_id'] : null,
            'course_id' => $association === 'course' ? $data['course_id'] : null,
            'execution_id' => $association === 'execution' ? $data['execution_id'] : null,
            'respondent_fields' => $this->respondentFields($questionnaire->type, $data['respondent_fields'] ?? []),
        ]);

        $this->syncSatisfactionSettings($questionnaire, $data);

        $this->syncBanner($questionnaire, $banner, $removeBanner);

        $questionnaire->save();

        return $questionnaire;
    }

    /**
     * Copia una encuesta con sus preguntas y un link nuevo. Sin respuestas
     * y sin fecha de cierre (la original probablemente ya pasó).
     */
    public function duplicate(Questionnaire $original): Questionnaire
    {
        return DB::transaction(function () use ($original) {
            $copy = $original->replicate(['token', 'closes_at', 'banner_path']);
            $copy->name = mb_substr('Copia de ' . $original->name, 0, 255);

            if ($original->banner_path && Storage::disk('public')->exists($original->banner_path)) {
                $copy->banner_path = self::BANNER_DIRECTORY . '/' . Str::random(40) . '.' . pathinfo($original->banner_path, PATHINFO_EXTENSION);
                Storage::disk('public')->copy($original->banner_path, $copy->banner_path);
            }

            $copy->save();

            $this->builder->copy($original, $copy);

            return $copy;
        });
    }

    public function delete(Questionnaire $questionnaire): void
    {
        // Borrado lógico: las respuestas se conservan.
        $questionnaire->delete();
    }

    /** Solo se guardan los campos que se piden. Los instrumentos usan los suyos. */
    private function respondentFields(QuestionnaireType $type, array $input): array
    {
        // [] también es fijo: la encuesta de satisfacción es anónima.
        if (($fixed = $type->fixedRespondentFields()) !== null) {
            return $fixed;
        }

        return collect($input)
            ->only(array_keys(Questionnaire::RESPONDENT_FIELDS))
            ->filter(fn ($mode) => in_array($mode, ['optional', 'required'], true))
            ->all();
    }

    /**
     * Encuesta de satisfacción libre: al crearla se congela una copia de la plantilla.
     * Después solo cambia el nombre del relator, y solo mientras no tenga respuestas.
     */
    private function syncSatisfactionSettings(Questionnaire $questionnaire, array $data): void
    {
        if ($questionnaire->type !== QuestionnaireType::Satisfaction) {
            return;
        }

        if (! $questionnaire->exists) {
            $template = SurveyTemplate::with('sections.fields')->findOrFail($data['survey_template_id']);
            $questionnaire->settings = SatisfactionInstrument::settingsFor($template, $data['instructor_name']);

            return;
        }

        if (filled($data['instructor_name'] ?? null) && ! $questionnaire->responses()->exists()) {
            $settings = (array) $questionnaire->settings;
            $settings['instructor_name'] = trim($data['instructor_name']);
            $questionnaire->settings = $settings;
        }
    }

    /** "2026-11-30T18:00" en hora de Chile → UTC. */
    private function toUtc(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value, Questionnaire::TIMEZONE)->utc() : null;
    }

    private function syncBanner(Questionnaire $questionnaire, ?UploadedFile $banner, bool $remove): void
    {
        $disk = Storage::disk('public');

        if (($banner || $remove) && $questionnaire->banner_path) {
            $disk->delete($questionnaire->banner_path);
            $questionnaire->banner_path = null;
        }

        if ($banner) {
            $questionnaire->banner_path = $banner->store(self::BANNER_DIRECTORY, 'public');
        }
    }
}
