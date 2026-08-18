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
}
