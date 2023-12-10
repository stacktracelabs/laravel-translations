<?php

use Illuminate\Support\Facades\App;

uses(Tests\TestCase::class)->in('Feature');

function setAppLocale(string $locale = 'sk', string $fallback = 'en'): void
{
    App::setLocale($locale);
    App::setFallbackLocale($fallback);
}

function useLocales(array $locales): void
{
    config()->set('translations::translations.locales', $locales);
}

function useFallbackLocales(array $locales): void
{
    config()->set('translations::translations.fallback_locales', $locales);
}
