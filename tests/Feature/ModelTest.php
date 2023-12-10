<?php

use StackTrace\Translations\LocalizedString;
use Tests\Support\TestModel;

beforeEach(fn () => setAppLocale());

function testModel(array $attributes = []): TestModel
{
    return new TestModel($attributes);
}

it('should set model translation', function () {
    $model = testModel();

    $model->setTranslation('title', 'sk', 'SK Title');

    expect($model->getTranslation('title', 'sk', useFallback: false))->toBe('SK Title');
});

it('should set model translation from array', function () {
    $model = testModel([
        'title' => [
            'sk' => 'SK',
            'en' => 'EN',
        ],
    ]);

    expect($model->getTranslation('title', 'sk', useFallback: false))->toBe('SK')
        ->and($model->getTranslation('title', 'en', useFallback: false))->toBe('EN');
});

it('should set model translation from localized string', function () {
    $model = testModel([
        'title' => LocalizedString::make([
            'sk' => 'SK',
            'en' => 'EN',
        ]),
    ]);

    expect($model->getTranslation('title', 'sk', useFallback: false))->toBe('SK')
        ->and($model->getTranslation('title', 'en', useFallback: false))->toBe('EN');
});

it('should return empty localized string when no translations are set', function () {
    expect(testModel()->getTranslations('title'))->isEmpty()->toBeTrue();
});

it('should allow null to be used as translation value', function () {
    $model = testModel([
        'title' => null,
    ]);

    expect($model->getTranslations('title'))->isEmpty()->toBeTrue();
});

it('should return null when translation is not set', function () {
    expect(testModel()->getTranslation('title', 'sk'))->toBeNull();
});

it('should set value for current locale', function () {
    $model = testModel([
        'title' => 'Test SK',
    ]);

    expect($model->getTranslation('title', 'sk'))->toBe('Test SK');
});

it('should set and get value for current locale', function () {
    $model = testModel([
        'title' => 'Test SK',
    ]);

    expect($model->title)->toBe('Test SK');
});
