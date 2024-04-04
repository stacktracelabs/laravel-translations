<?php


namespace StackTrace\Translations;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasTranslations
{
    /**
     * Initialize the HasTranslations trait.
     */
    public function initializeHasTranslations(): void
    {
        //
    }

    /**
     * Append a localized where clause to query.
     */
    public function scopeWhereLocalized(Builder $builder, string $column, $operator, $value, $boolean = "and", $collate = null): void
    {
        // TODO: Add support for multiple fallback locales.
        $locale = App::getLocale();
        $fallbackLocale = App::getFallbackLocale();

        $resolveJsonPath = function (string $locale) use ($column) {
            $path = collect(explode(".", $column));

            $column = $path->splice(0, 1)->first();

            $path = $path->push($locale)->map(fn ($it) => '"'.$it.'"')->join('.');

            return "`{$column}`->'$.{$path}'";
        };

        if ($locale == $fallbackLocale) {
            $clause = "JSON_UNQUOTE(".$resolveJsonPath($locale).")";
        } else {
            $clause = "JSON_UNQUOTE(IFNULL(".$resolveJsonPath($locale).", ".$resolveJsonPath($fallbackLocale)."))";
        }

        if ($collate) {
            $clause .= " COLLATE {$collate}";
        }

        $builder->where(DB::raw($clause), $operator, $value, $boolean);
    }

    /**
     * Retrieve the value of the attribute.
     */
    public function getAttributeValue($key)
    {
        return $this->isTranslatable($key)
            ? $this->getTranslations($key)->getValue()
            : parent::getAttributeValue($key);
    }

    /**
     * Set the value of the attribute.
     */
    public function setAttribute($key, $value)
    {
        if ($this->isTranslatable($key)) {
            if (is_array($value) || $value instanceof LocalizedString) {
                return $this->setTranslations($key, $value);
            }

            return $this->setTranslation($key, Facades\Translations::getLocale(), $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Retrieve translation of the attribute for given locale.
     */
    public function getTranslation(string $key, string $locale, bool $useFallback = true): ?string
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        return $this->getTranslations($key)->getValueForLocale($locale, $useFallback);
    }

    /**
     * Set translation of given attribute.
     */
    public function setTranslation(string $key, string $locale, ?string $value): static
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        $translations = LocalizedString::make($this->getAllTranslations($key));

        if ($this->hasSetMutator($key)) {
            $method = 'set'.Str::studly($key).'Attribute';
            $this->{$method}($value, $locale);
            $value = $this->attributes[$key];
        } else if ($this->hasAttributeMutator($key)) {
            $this->setAttributeMarkedMutatedAttributeValue($key, $value);
            $value = $this->attributes[$key];
        }

        $translations->setValueForLocale($locale, $value);

        $this->attributes[$key] = $this->asJson($translations);

        return $this;
    }

    /**
     * Retrieve allowed translations for given attribute.
     */
    public function getTranslations(string $key): LocalizedString
    {
        return LocalizedString::makeForAllowedLocales($this->getAllTranslations($key));
    }

    /**
     * Retrieve all translations of given attribute.
     */
    public function getAllTranslations(string $key): array
    {
        return json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true) ?: [];
    }

    /**
     * Set translations for given attribute.
     */
    public function setTranslations(string $key, array|LocalizedString $translations): static
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        $translations = $translations instanceof LocalizedString
            ? $translations
            : LocalizedString::make($translations);

        if ($translations->isEmpty()) {
            $this->attributes[$key] = $this->asJson([]);
        } else {
            foreach ($translations->toArray() as $locale => $value) {
                $this->setTranslation($key, $locale, $value);
            }
        }

        return $this;
    }

    /**
     * Determine if the given attribute is translatable.
     */
    public function isTranslatable(string $attribute): bool
    {
        return in_array($attribute, $this->getTranslatableAttributes());
    }

    /**
     * Retrieve list of translatable attributes.
     */
    public function getTranslatableAttributes(): array
    {
        return is_array($this->translatable) ? $this->translatable : [];
    }

    /**
     * Throws an exception when attribute is not translatable.
     */
    protected function guardAgainstNonTranslatableAttribute(string $key): void
    {
        if (! $this->isTranslatable($key)) {
            throw new \LogicException("The [$key] attribute is not translatable.");
        }
    }
}
