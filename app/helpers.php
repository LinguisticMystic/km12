<?php

if (! function_exists('localized_text')) {
    function localized_text(?string $latvian, ?string $english): string
    {
        $useEnglish = app()->getLocale() === 'en';
        $preferred = $useEnglish ? $english : $latvian;
        $fallback = $useEnglish ? $latvian : $english;

        return filled($preferred) ? $preferred : (string) ($fallback ?? '');
    }
}
