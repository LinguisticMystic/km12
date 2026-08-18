<?php

use Carbon\CarbonInterface;

if (! function_exists('localized_text')) {
    function localized_text(?string $latvian, ?string $english): string
    {
        $useEnglish = app()->getLocale() === 'en';
        $preferred = $useEnglish ? $english : $latvian;
        $fallback = $useEnglish ? $latvian : $english;

        return filled($preferred) ? $preferred : (string) ($fallback ?? '');
    }
}

if (! function_exists('localized_date')) {
    function localized_date(?CarbonInterface $date, string $format): string
    {
        if ($date === null) {
            return '';
        }

        if (app()->getLocale() === 'lv') {
            $format = preg_replace('/(?<!\\\\)j(?!\.)/', 'j.', $format) ?? $format;
        }

        return $date->copy()->locale(app()->getLocale())->translatedFormat($format);
    }
}
