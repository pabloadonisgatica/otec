<?php

/**
 * Paso 7.4 — Solicitud de Cotización a proveedores.
 * Aplica los cambios pequeños sobre archivos existentes. Requiere el Paso 7.4
 * (Evaluación de Proveedores) ya aplicado. Revisa todo antes de escribir:
 * si algo no calza, no modifica ningún archivo. Es idempotente.
 */

$patches = [
    'routes/web.php' => [
        'marker' => 'ProviderQuoteRequestController',
        'changes' => [
            [
                "use App\\Http\\Controllers\\ProviderEvaluationController;\n",
                "use App\\Http\\Controllers\\ProviderEvaluationController;\nuse App\\Http\\Controllers\\ProviderQuoteRequestController;\n",
            ],
            [
                "        // 7.4 Evaluación de proveedores (dentro de cada proveedor)\n",
                <<<'PHP'
        // 7.4 Solicitudes de cotización a proveedores
        Route::get('/solicitudes-cotizacion/{quoteRequest}/pdf', [ProviderQuoteRequestController::class, 'pdf'])
            ->name('quote-requests.pdf');

        Route::get('/solicitudes-cotizacion/{quoteRequest}/adjunto', [ProviderQuoteRequestController::class, 'attachment'])
            ->name('quote-requests.attachment');

        Route::resource('solicitudes-cotizacion', ProviderQuoteRequestController::class)
            ->parameters(['solicitudes-cotizacion' => 'quoteRequest'])
            ->names('quote-requests');

        // 7.4 Evaluación de proveedores (dentro de cada proveedor)

PHP,
            ],
        ],
    ],

    'app/Http/Controllers/QualityNormController.php' => [
        'marker' => 'quality.quote-requests.index',
        'changes' => [
            [
                "'label' => 'Formato de Solicitud de Cotizaciones', 'status' => 'done', 'route' => 'budgets.index', 'note' => 'Se realiza desde el módulo de Presupuestos']",
                "'label' => 'Formato de Solicitud de Cotizaciones', 'status' => 'done', 'route' => 'quality.quote-requests.index', 'note' => 'Solicitudes a proveedores, con PDF para enviar']",
            ],
        ],
    ],

    'app/Models/Provider.php' => [
        'marker' => 'function quoteRequests',
        'changes' => [
            [
                "    public function latestEvaluation(): HasOne\n",
                <<<'PHP'
    /**
     * Solicitudes de cotización enviadas a este proveedor (NCH 2728 — 7.4).
     */
    public function quoteRequests(): HasMany
    {
        return $this->hasMany(ProviderQuoteRequest::class);
    }

    public function latestEvaluation(): HasOne

PHP,
            ],
        ],
    ],

    'app/Http/Controllers/ProviderController.php' => [
        'marker' => "'quoteRequests' =>",
        'changes' => [
            [
                "        \$provider->load(['evaluations.evaluator:id,name']);\n",
                <<<'PHP'
        $provider->load([
            'evaluations.evaluator:id,name',
            'quoteRequests' => fn ($query) => $query
                ->with('execution:id,internal_code')
                ->latest('requested_at')
                ->latest('id')
                ->limit(10),
        ]);

PHP,
            ],
        ],
    ],

    'resources/views/quality/providers/show.blade.php' => [
        'marker' => "quality.quote-requests._provider",
        'changes' => [
            [
                "            @include('quality.providers.evaluations._history')\n",
                "            @include('quality.providers.evaluations._history')\n\n            @include('quality.quote-requests._provider')\n",
            ],
        ],
    ],
];

// 1) Verificar todo antes de escribir.
$pending = [];

foreach ($patches as $file => $patch) {
    if (! is_file($file)) {
        fwrite(STDERR, "✗ No existe {$file}. ¿Estás en ~/Sites/otec?\n");
        exit(1);
    }

    $content = file_get_contents($file);

    if (str_contains($content, $patch['marker'])) {
        echo "· {$file}: ya estaba aplicado\n";
        continue;
    }

    foreach ($patch['changes'] as [$search]) {
        if (substr_count($content, $search) !== 1) {
            fwrite(STDERR, "✗ {$file}: no encontré el punto exacto a modificar.\n");
            fwrite(STDERR, "  Este paso necesita el 7.4 (Evaluación de Proveedores) ya aplicado. No se cambió ningún archivo.\n");
            exit(1);
        }
    }

    $pending[$file] = $content;
}

// 2) Escribir.
foreach ($pending as $file => $content) {
    foreach ($patches[$file]['changes'] as [$search, $replace]) {
        $content = str_replace($search, $replace, $content);
    }

    file_put_contents($file, $content);
    echo "✓ {$file}\n";
}
