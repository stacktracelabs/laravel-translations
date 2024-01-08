<?php


namespace StackTrace\Translations;


use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use JsonSerializable;

/**
 * @template TKey of string
 *
 * @template-covariant TValue
 *
 * @implements \ArrayAccess<TKey, TValue>
 */
class LocalizedValue implements Arrayable, ArrayAccess, JsonSerializable
{
    protected function __construct(
        protected array $value,
    ) { }

    /**
     * Retrieve value for given locale or use fallback locales when value is not localized.
     *
     * @return TValue|null
     */
    public function getValueForLocaleOrFallback(string $locale, bool $useFallback = false, array $fallbackLocales = [])
    {
        $value = Arr::get($this->value, $locale);

        if (! is_null($value)) {
            return $value;
        }

        if (! $useFallback) {
            return null;
        }

        foreach ($fallbackLocales as $fallbackLocale) {
            $value = Arr::get($this->value, $fallbackLocale);

            if (! is_null($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Retrieve value for given locale using application fallback locales.
     *
     * @return TValue|null
     */
    public function getValueForLocale(string $locale, bool $useFallback = false)
    {
        return $this->getValueForLocaleOrFallback(
            locale: $locale,
            useFallback: $useFallback,
            fallbackLocales: $useFallback ? Facades\Translations::getFallbackLocales($locale) : []
        );
    }

    /**
     * Set localized value for given locale.
     *
     * @param TValue|null $value
     */
    public function setValueForLocale(string $locale, $value): static
    {
        Arr::set($this->value, $locale, $value);

        return $this;
    }

    /**
     * Set value for current locale.
     *
     * @param TValue|null $value
     */
    public function setValue($value): static
    {
        return $this->setValueForLocale(Facades\Translations::getLocale(), $value);
    }

    /**
     * Retrieve the value of the string for current locale.
     *
     * @return TValue|null
     */
    public function getValue(bool $useFallback = true)
    {
        return $this->getValueForLocale(
            locale: Facades\Translations::getLocale(),
            useFallback: $useFallback
        );
    }

    /**
     * Retrieve the value of the string for current locale.
     *
     * @return TValue|null
     */
    public function value(bool $useFallback = true)
    {
        return $this->getValue($useFallback);
    }

    /**
     * Map each locale value through given callback, returning new value.
     */
    public function map(callable $callback): static
    {
        return new static(collect($this->value)->map($callback)->all());
    }

    /**
     * Remove translations for given locales.
     */
    public function forget(array|string $locales): static
    {
        $this->value = Arr::except($this->value, Arr::wrap($locales));

        return $this;
    }

    /**
     * Determine if the localized value is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    /**
     * Retrieve array representation of the localized value.
     */
    public function toArray()
    {
        return $this->value;
    }

    /**
     * Determine if the given offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->value[$offset]);
    }

    /**
     * Get the value at the given offset.
     *
     * @return TValue|null
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->value[$offset];
    }

    /**
     * Set the value at the given offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->value[$offset] = $value;
    }

    /**
     * Unset the value at the given offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->value[$offset]);
    }

    /**
     * Retrieve JSON serializable value.
     */
    public function jsonSerialize(): mixed
    {
        return empty($this->value) ? (object) [] : $this->value;
    }

    /**
     * Create new empty string.
     */
    public static function empty(): static
    {
        return static::make([]);
    }

    /**
     * Determine if given value is considered empty.
     */
    protected static function isValueEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    /**
     * Crate new localized value from array of values.
     */
    public static function make(array $value): static
    {
        return new static(
            array_filter($value, fn ($val) => !static::isValueEmpty($val), ARRAY_FILTER_USE_BOTH)
        );
    }

    /**
     * Create localized value of given value for current locale.
     *
     * @param TValue|null $value
     */
    public static function makeForCurrentLocale($value): static
    {
        if (static::isValueEmpty($value)) {
            return static::empty();
        }

        return static::make([
            Facades\Translations::getLocale() => $value,
        ]);
    }

    /**
     * Create new localized string from array of values.
     */
    public static function makeForAllowedLocales(array $value, ?array $allowedLocales = null): static
    {
        $allowedLocales = $allowedLocales ?: Facades\Translations::getLocales();

        return new static(
            array_filter($value, function ($value, $locale) use ($allowedLocales) {
                if (static::isValueEmpty($value)) {
                    return false;
                }

                if (! in_array($locale, $allowedLocales)) {
                    return false;
                }

                return true;
            }, ARRAY_FILTER_USE_BOTH)
        );
    }
}
