<?php

namespace Tests\Unit;

use Tests\TestCase;

class LocalizedTextTest extends TestCase
{
    public function test_it_prefers_the_active_locale_and_falls_back(): void
    {
        app()->setLocale('lv');
        $this->assertSame('latviski', localized_text('latviski', 'english'));

        app()->setLocale('en');
        $this->assertSame('english', localized_text('latviski', 'english'));

        app()->setLocale('en');
        $this->assertSame('latviski', localized_text('latviski', null));

        app()->setLocale('lv');
        $this->assertSame('english', localized_text(null, 'english'));
    }

    public function test_latvian_dates_put_a_period_after_the_day_number(): void
    {
        $date = now()->setDate(2026, 8, 28)->setTime(20, 0);

        app()->setLocale('lv');
        $this->assertSame(
            $date->copy()->locale('lv')->translatedFormat('l, j. F Y · H:i'),
            localized_date($date, 'l, j F Y · H:i'),
        );

        app()->setLocale('en');
        $this->assertSame(
            $date->copy()->locale('en')->translatedFormat('l, j F Y · H:i'),
            localized_date($date, 'l, j F Y · H:i'),
        );
    }

    public function test_localized_date_returns_an_empty_string_for_null(): void
    {
        $this->assertSame('', localized_date(null, 'l, j F'));
    }
}
