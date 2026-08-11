<?php

namespace App\Http\Controllers;

use App\Models\DiplomaTemplate;
use App\Services\DiplomaRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiplomaTemplateController extends Controller
{
    public function index()
    {
        $templates = DiplomaTemplate::query()
            ->latest()
            ->paginate(15);

        return view('diploma_templates.index', compact('templates'));
    }

    public function create()
    {
        $template = new DiplomaTemplate();

        return view('diploma_templates.create', compact('template'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'background' => ['nullable', 'image', 'max:4096'], // 4MB
            'content_html' => ['required', 'string'],
        ]);

        $template = new DiplomaTemplate();
        $template->name = $data['name'];
        $template->is_active = (bool)($data['is_active'] ?? false);
        $template->content_html = $data['content_html'];
        $template->created_by = auth()->id();

        if ($request->hasFile('background')) {
            $path = $request->file('background')->store('diplomas/backgrounds', 'public');
            $template->background_path = $path;
        }

        $template->save();

        return redirect()
            ->route('diploma-templates.index')
            ->with('status', 'Plantilla creada correctamente.');
    }

    public function show(DiplomaTemplate $diploma_template)
    {
        // opcional: por ahora redirigimos a edición (más útil para este MVP)
        return redirect()->route('diploma-templates.edit', $diploma_template);
    }

    public function edit(DiplomaTemplate $diploma_template)
    {
        $template = $diploma_template;

        return view('diploma_templates.edit', compact('template'));
    }

    public function update(Request $request, DiplomaTemplate $diploma_template)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'background' => ['nullable', 'image', 'max:4096'],
            'content_html' => ['required', 'string'],
            'remove_background' => ['nullable', 'boolean'],
        ]);

        $template = $diploma_template;
        $template->name = $data['name'];
        $template->is_active = (bool)($data['is_active'] ?? false);
        $template->content_html = $data['content_html'];

        // eliminar fondo existente
        if (!empty($data['remove_background']) && $template->background_path) {
            Storage::disk('public')->delete($template->background_path);
            $template->background_path = null;
        }

        // reemplazar fondo
        if ($request->hasFile('background')) {
            if ($template->background_path) {
                Storage::disk('public')->delete($template->background_path);
            }
            $path = $request->file('background')->store('diplomas/backgrounds', 'public');
            $template->background_path = $path;
        }

        $template->save();

        return redirect()
            ->route('diploma-templates.edit', $template)
            ->with('status', 'Plantilla actualizada correctamente.');
    }

    public function destroy(DiplomaTemplate $diploma_template)
    {
        if ($diploma_template->background_path) {
            Storage::disk('public')->delete($diploma_template->background_path);
        }

        $diploma_template->delete();

        return redirect()
            ->route('diploma-templates.index')
            ->with('status', 'Plantilla eliminada.');
    }

    /**
     * Vista previa del HTML del formulario (sin necesidad de
     * guardar), con datos de muestra.
     */
    public function preview(Request $request, DiplomaRenderer $diplomaRenderer)
    {
        $request->validate([
            'content_html' => ['required', 'string'],
        ]);

        $content = $diplomaRenderer->renderPreview($request->input('content_html'));
        $html = $diplomaRenderer->wrapDocument($content);

        $pdf = Pdf::loadHTML($html)->setPaper('folio', 'landscape');

        return $pdf->stream('vista-previa-diploma.pdf');
    }

    /**
     * Igual que preview(), pero devuelve HTML plano en vez de
     * PDF — para la vista previa en vivo dentro del editor.
     */
    public function previewHtml(Request $request, DiplomaRenderer $diplomaRenderer)
    {
        $request->validate([
            'content_html' => ['required', 'string'],
        ]);

        return response(
            $diplomaRenderer->renderPreview($request->input('content_html'))
        );
    }
}
