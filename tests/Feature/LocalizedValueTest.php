<?php

use StackTrace\Translations\LocalizedValue;

beforeEach(fn () => setAppLocale());

function testStr(): LocalizedValue
{
    return LocalizedValue::make([
        'sk' => 'SK',
        'cs' => 'CS',
        'de' => 'DE',
        'hu' => 'HU',
        'pl' => 'PL',
        'en' => 'EN',
    ]);
}

it('should make for all locales', function () {
    expect(LocalizedValue::make([
        'cs' => 'CS',
        'fr' => 'FR',
    ]))->toArray()->toMatchArray(['cs' => 'CS', 'fr' => 'FR']);
});

it('should make for allowed locales', function () {
    expect(LocalizedValue::makeForAllowedLocales([
        'de' => 'DE',
        'cs' => 'CS',
        'fr' => 'FR',
    ], ['de']))->toArray()->toMatchArray(['de' => 'DE'])->not->toHaveKeys(['cs', 'fr']);
});

it('should create test string', function () {
    expect(testStr())->toArray()->toHaveKeys(['sk', 'cs', 'de', 'hu', 'pl', 'en']);
});

it('should create empty localized string', function () {
    expect(LocalizedValue::empty())->isEmpty()->toBeTrue();
});

it('should return empty array on empty string', function () {
    expect(LocalizedValue::empty())->toArray()->toBeEmpty();
});

it('should create localized string from allowed locales', function () {
    useLocales(['sk', 'en', 'de', 'cs']);

    expect(LocalizedValue::makeForAllowedLocales([
        'sk' => 'Test SK',
        'en' => 'Test EN',
        'de' => 'Test DE'
    ]))->toArray()->toHaveKeys(['sk', 'en', 'de'])->not->toHaveKey('cs');
});

it('should return string with default app locales when locales are not set', function () {
    useLocales([]);

    expect(LocalizedValue::makeForAllowedLocales([
        'sk' => 'Test SK',
        'en' => 'Test EN',
        'de' => 'Test DE'
    ]))->toArray()->toHaveKeys(['sk', 'en'])->not->toHaveKey('de');

    useLocales(['fr']);

    expect(LocalizedValue::makeForAllowedLocales([
        'de' => 'DE',
        'cs' => 'CS',
        'fr' => 'FR',
    ]))->toArray()->toMatchArray(['fr' => 'FR'])->not->toHaveKeys(['de', 'cs']);
});

it('should return value for given locale without using fallback', function () {
    expect(testStr())->getValueForLocale('sk')->toBe('SK');
});

it('should return null when locale is not present', function () {
    expect(testStr())->getValueForLocaleOrFallback('fr')->toBeNull();
});

it('should return fallback value', function () {
    expect(testStr())->getValueForLocaleOrFallback('fr', true, ['cs'])->toBe('CS');
});

it('should return fallback value from multiple fallbacks', function () {
    expect(testStr())->getValueForLocaleOrFallback('fr', true, ['es', 'sk'])->toBe('SK');
});

it('should return configured fallback value', function () {
    useFallbackLocales([
        'fr' => ['cs'],
    ]);

    expect(testStr())->getValueForLocale('fr', true)->toBe('CS');
});

it('should return value of application fallback locale when custom fallback is not set', function () {
    expect(testStr())->getValueForLocale('fr', true)->toBe('EN');
});

it('should return configured fallback value using multiple fallbacks', function () {
    useFallbackLocales([
        'fr' => ['es', 'cs'],
    ]);

    expect(testStr())->getValueForLocale('fr', true)->toBe('CS');
});

it('should return value for current locale without fallback', function () {
    setAppLocale('fr');

    expect(testStr())->getValue(false)->toBeNull();

    setAppLocale('en');

    expect(testStr())->getValue(false)->toBe('EN');
});

it('should return value for for current locale with fallback', function () {
    setAppLocale('fr');
    useFallbackLocales(['fr' => ['es', 'cs']]);

    expect(testStr())->getValue()->toBe('CS');

    setAppLocale('es');

    expect(testStr())->getValue()->toBe('EN');
});

it('should retrieve value using offset access', function () {
    expect(testStr()['sk'])->toBe('SK')
        ->and(fn() => testStr()['fr'])->toThrow(ErrorException::class);
});

it('should set value for locale', function () {
    expect(testStr()->setValueForLocale('sk', 'Test SK')['sk'])->toBe('Test SK');
});

it('should set value for current locale', function () {
    setAppLocale('cs');

    expect(testStr()->setValue('Test CS')['cs'])->toBe('Test CS');
});

it('should set value by offset access', function () {
    $str = testStr();
    $str['cs'] = 'Test CS';

    expect($str['cs'])->toBe('Test CS');
});

it('should return string as array', function () {
    expect(testStr()->toArray())->toMatchArray([
        'sk' => 'SK',
        'cs' => 'CS',
        'de' => 'DE',
        'hu' => 'HU',
        'pl' => 'PL',
        'en' => 'EN',
    ]);
});
