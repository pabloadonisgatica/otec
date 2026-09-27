<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Datos fijos que aparecen en todos los diplomas: firmante, cargo,
 * imagen de firma con timbre y código del formulario de calidad
 * (ej: F38-Rev.00). Viven en app_settings para poder cambiarlos
 * desde Configuración sin tocar el código ni las plantillas.
 */
class DiplomaSettingsService
{
    public const SIGNER_NAME = 'diploma_signer_name';
    public const SIGNER_TITLE = 'diploma_signer_title';
    public const SIGNATURE = 'diploma_signature';
    public const FORM_CODE = 'diploma_form_code';

    private const SIGNATURE_DIR = 'diplomas/signatures';

    /**
     * Todos los datos de diploma en un solo arreglo.
     */
    public function all(): array
    {
        return [
            'signer_name' => AppSetting::get(self::SIGNER_NAME),
            'signer_title' => AppSetting::get(self::SIGNER_TITLE),
            'signature_path' => AppSetting::get(self::SIGNATURE),
            'form_code' => AppSetting::get(self::FORM_CODE),
        ];
    }

    public function update(array $data, ?UploadedFile $signature, bool $removeSignature): void
    {
        AppSetting::put(self::SIGNER_NAME, $data['signer_name'] ?? null);
        AppSetting::put(self::SIGNER_TITLE, $data['signer_title'] ?? null);
        AppSetting::put(self::FORM_CODE, $data['form_code'] ?? null);

        if ($signature) {
            $this->deleteSignatureFile();
            AppSetting::put(self::SIGNATURE, $signature->store(self::SIGNATURE_DIR, 'public'));

            return;
        }

        if ($removeSignature) {
            $this->deleteSignatureFile();
            AppSetting::put(self::SIGNATURE, null);
        }
    }

    private function deleteSignatureFile(): void
    {
        $current = AppSetting::get(self::SIGNATURE);

        if ($current && Storage::disk('public')->exists($current)) {
            Storage::disk('public')->delete($current);
        }
    }
}
