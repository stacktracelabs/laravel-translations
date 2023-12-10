<?php

use Illuminate\Database\Eloquent\Model;
use StackTrace\Translations\Casts\AsLocalizedValue;
use StackTrace\Translations\LocalizedValue;

beforeEach(fn () => setAppLocale());

function castableModel(array $attributes = []): Model
{
    return new class($attributes) extends Model
    {
        protected $guarded = false;

        protected $casts = [
            'title' => AsLocalizedValue::class,
            'labels' => AsLocalizedValue::class,
        ];
    };
}

it('should cast localized value', function () {
    $model = castableModel();

    $title = LocalizedValue::make([
        'sk' => 'SK',
        'en' => 'EN',
    ]);

    $model->title = $title;

    expect($model->getAttributes()['title'])->toBe('{"sk":"SK","en":"EN"}')
        ->and($model->title)->toBe($title);
});

it('should get casted localized value', function () {
    $model = castableModel([
        'title' => '{"sk":"SK","en":"EN"}',
    ]);

    expect($model->title)->toBeInstanceOf(LocalizedValue::class)
        ->getValueForLocale('sk', useFallback: false)->toBe('SK')
        ->getValueForLocale('en', useFallback: false)->toBe('EN');
});

it('should set localized object instead of primitive', function () {
    $model = castableModel();

    $labels = LocalizedValue::make([
        'sk' => ['value' => 1, 'label' => 'SK Label 1'],
        'en' => ['value' => 1, 'label' => 'EN Label 1'],
    ]);

    $model->labels = $labels;

    expect($model->getAttributes()['labels'])->toBe('{"sk":{"value":1,"label":"SK Label 1"},"en":{"value":1,"label":"EN Label 1"}}')
        ->and($model->labels)->toBe($labels);
});

it('should get localized object instead of primitive', function () {
    $model = castableModel([
        'labels' => '{"sk":{"value":1,"label":"SK Label 1"},"en":{"value":1,"label":"EN Label 1"}}',
    ]);

    expect($model->labels)->toBeInstanceOf(LocalizedValue::class)
        ->and($model->labels->getValueForLocale('sk', false))->toMatchArray(['value' => 1, 'label' => 'SK Label 1']);
});
