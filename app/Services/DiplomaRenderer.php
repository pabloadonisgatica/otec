<?php

namespace App\Services;

use App\Models\Diploma;

class DiplomaRenderer
{
    public function render(Diploma $diploma): string
    {
        $html = $diploma->template->content_html;

        $replacements = [
            '{{issued_at}}' => optional($diploma->issued_at)->format('d-m-Y'),
            '{{code}}'      => $diploma->code,
        ];

        foreach ($diploma->snapshot as $group => $values) {
            foreach ($values as $key => $value) {
                $replacements['{{'.$group.'.'.$key.'}}'] = e($value);
            }
        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $html
        );
    }
}
