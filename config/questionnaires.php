<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Copia oculta de los resultados
    |--------------------------------------------------------------------------
    | Correos que reciben copia (CCO) de cada resultado de los instrumentos
    | cerrados. Separados por coma. Vacío = sin copia.
    | .env: QUESTIONNAIRE_RESULTS_BCC="grupo@proyectohumano.cl"
    */

    'results_bcc' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('QUESTIONNAIRE_RESULTS_BCC', ''))
    ))),

];
