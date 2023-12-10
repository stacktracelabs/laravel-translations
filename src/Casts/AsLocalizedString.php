<?php


namespace StackTrace\Translations\Casts;


use StackTrace\Translations\LocalizedString;
use StackTrace\Translations\LocalizedValue;

class AsLocalizedString extends AsLocalizedValue
{
    public function newValue(?array $value): LocalizedValue
    {
        return $value === null
            ? LocalizedString::empty()
            : LocalizedString::make($value);
    }
}
