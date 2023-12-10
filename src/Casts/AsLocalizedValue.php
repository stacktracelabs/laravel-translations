<?php


namespace StackTrace\Translations\Casts;


use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use StackTrace\Translations\LocalizedValue;

class AsLocalizedValue implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        if (is_string($value)) {
            $value = Json::decode($value ?? '' ?: '{}') ?: [];
        } else if (is_null($value)) {
            $value = [];
        }

        return $this->newValue($value);
    }

    /**
     * Transform the attribute to its underlying model values.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($value instanceof LocalizedValue) {
            $value = Json::encode($value);
        }

        return [
            $key => $value,
        ];
    }

    /**
     * Create new localized value.
     */
    public function newValue(?array $value): LocalizedValue
    {
        return $value === null
            ? LocalizedValue::empty()
            : LocalizedValue::make($value);
    }
}
