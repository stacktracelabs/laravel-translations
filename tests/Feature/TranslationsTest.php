<?php

use Illuminate\Support\Facades\App;
use StackTrace\Translations\Facades\Translations;

beforeEach(fn () => setAppLocale());

it('should retrieve current application locale and current application fallback locale', function () {
    setAppLocale('cs', 'de');

    expect(App::getLocale())->toBe('cs')
        ->and(Translations::getLocale())->toBe('cs')
        ->and(App::getFallbackLocale())->toBe('de')
        ->and(Translations::getFallbackLocales())->toMatchArray(['de']);
});

it('should retrieve custom fallback locales when configured', function () {
    setAppLocale('cs', 'de');

    useFallbackLocales([
        'cs' => ['sk', 'en'],
    ]);

    expect(App::getFallbackLocale())
        ->toBe('de')
        ->and(Translations::getFallbackLocales())
        ->toMatchArray(['sk', 'en']);
});

it('should return fallback locales for locale', function () {
    useFallbackLocales([
        'cs' => ['sk', 'en'],
        'de' => ['en'],
    ]);

    expect(Translations::getFallbackLocales('de'))
        ->toBe(['en'])
        ->and(Translations::getFallbackLocales('cs'))
        ->toMatchArray(['sk', 'en']);
});

it('should return default fallback locales for locale', function () {
    useFallbackLocales([
        'cs' => ['sk', 'en'],
        'de' => ['en'],
    ]);

    expect(Translations::getFallbackLocales('sk'))->toBe(['en']);
});

it('should return configured locales', function () {
    useLocales(['cs', 'de']);
    expect(Translations::getLocales())->toMatchArray(['cs', 'de']);

    // Default application locale and fallback locale
    useLocales([]);
    expect(Translations::getLocales())->toMatchArray(['sk', 'en']);

    // Default application locale same as fallback locale
    setAppLocale('en');
    expect(Translations::getLocales())->toMatchArray(['en']);
});
