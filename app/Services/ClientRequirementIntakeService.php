<?php

namespace App\Services;

use App\Models\ClientRequirementIntake;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Registro y actualización del Levantamiento del Requerimiento (NCH 2728 — 7.2).
 *
 * Los adjuntos van al disco privado ("local"): son documentos del cliente
 * y solo se descargan con sesión iniciada, nunca por URL pública.
 */
class ClientRequirementIntakeService
{
    private const DISK = 'local';

    private const FOLDER = 'quality/levantamientos';

    public function create(array $data, ?UploadedFile $attachment, ?int $userId): ClientRequirementIntake
    {
        $data = $this->normalizeReview($data);
        $data['created_by'] = $userId;

        if ($attachment) {
            $data = [...$data, ...$this->storeAttachment($attachment)];
        }

        return ClientRequirementIntake::create($data);
    }

    public function update(
        ClientRequirementIntake $intake,
        array $data,
        ?UploadedFile $attachment,
        bool $removeAttachment = false
    ): ClientRequirementIntake {
        $data = $this->normalizeReview($data);

        if ($attachment || $removeAttachment) {
            $this->deleteAttachment($intake);
            $data['attachment_path'] = null;
            $data['attachment_name'] = null;
        }

        if ($attachment) {
            $data = [...$data, ...$this->storeAttachment($attachment)];
        }

        $intake->update($data);

        return $intake;
    }

    public function delete(ClientRequirementIntake $intake): void
    {
        $this->deleteAttachment($intake);
        $intake->delete();
    }

    public function attachmentExists(ClientRequirementIntake $intake): bool
    {
        return $intake->attachment_path
            && Storage::disk(self::DISK)->exists($intake->attachment_path);
    }

    public function disk(): string
    {
        return self::DISK;
    }

    /**
     * Mientras el requerimiento está pendiente, no puede tener datos de revisión.
     */
    private function normalizeReview(array $data): array
    {
        unset($data['attachment'], $data['remove_attachment']);

        if (($data['review_status'] ?? 'pending') === 'pending') {
            $data['reviewed_by'] = null;
            $data['reviewed_at'] = null;
        }

        return $data;
    }

    private function storeAttachment(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = now()->format('YmdHis').'_'.Str::random(8).'.'.$extension;

        return [
            'attachment_path' => $file->storeAs(self::FOLDER, $filename, self::DISK),
            'attachment_name' => $file->getClientOriginalName(),
        ];
    }

    private function deleteAttachment(ClientRequirementIntake $intake): void
    {
        if ($intake->attachment_path) {
            Storage::disk(self::DISK)->delete($intake->attachment_path);
        }
    }
}
