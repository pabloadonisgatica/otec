<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\ProviderQuoteRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Solicitudes de cotización a proveedores (NCH 2728 — 7.4).
 *
 * La cotización que devuelve el proveedor se guarda en el disco privado
 * ("local"): solo se descarga con sesión iniciada.
 */
class ProviderQuoteRequestService
{
    private const DISK = 'local';

    private const FOLDER = 'quality/cotizaciones';

    public function create(array $data, ?UploadedFile $attachment, ?int $userId): ProviderQuoteRequest
    {
        $data = $this->normalize($data);
        $data['requested_by'] = $userId;

        if ($attachment) {
            $data = [...$data, ...$this->storeAttachment($attachment)];
        }

        return ProviderQuoteRequest::create($data);
    }

    public function update(
        ProviderQuoteRequest $quoteRequest,
        array $data,
        ?UploadedFile $attachment,
        bool $removeAttachment = false
    ): ProviderQuoteRequest {
        $data = $this->normalize($data);

        if ($attachment || $removeAttachment) {
            $this->deleteAttachment($quoteRequest);
            $data['attachment_path'] = null;
            $data['attachment_name'] = null;
        }

        if ($attachment) {
            $data = [...$data, ...$this->storeAttachment($attachment)];
        }

        $quoteRequest->update($data);

        return $quoteRequest;
    }

    public function delete(ProviderQuoteRequest $quoteRequest): void
    {
        $this->deleteAttachment($quoteRequest);
        $quoteRequest->delete();
    }

    public function attachmentExists(ProviderQuoteRequest $quoteRequest): bool
    {
        return $quoteRequest->attachment_path
            && Storage::disk(self::DISK)->exists($quoteRequest->attachment_path);
    }

    public function disk(): string
    {
        return self::DISK;
    }

    /**
     * Datos de la OTEC para el encabezado del PDF. El logo va embebido
     * (data URI) para no depender de rutas públicas en el servidor.
     */
    public function pdfHeader(): array
    {
        $logo = null;
        $logoPath = AppSetting::get('app_logo');

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $mime = Storage::disk('public')->mimeType($logoPath);

            if (in_array($mime, ['image/png', 'image/jpeg', 'image/gif'], true)) {
                $logo = 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($logoPath));
            }
        }

        return [
            'otecName' => AppSetting::get('otec_name', 'OTEC'),
            'logo' => $logo,
        ];
    }

    /**
     * Mientras la solicitud espera respuesta, no guarda datos de respuesta.
     */
    private function normalize(array $data): array
    {
        unset($data['attachment'], $data['remove_attachment']);

        $data['items'] = array_map(fn (array $item) => [
            'description' => $item['description'],
            'quantity' => (float) $item['quantity'],
            'unit' => $item['unit'] !== '' ? $item['unit'] : null,
        ], $data['items']);

        if ($data['status'] === 'sent') {
            $data['responded_at'] = null;
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

    private function deleteAttachment(ProviderQuoteRequest $quoteRequest): void
    {
        if ($quoteRequest->attachment_path) {
            Storage::disk(self::DISK)->delete($quoteRequest->attachment_path);
        }
    }
}
