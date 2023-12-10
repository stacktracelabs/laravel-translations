<?php


namespace StackTrace\Translations;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class Translations
{
    /**
     * Retrieve the current application locale.
     */
    public function getLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Retrieve list of fallback locales for given locale.
     */
    public function getFallbackLocales(?string $locale = null): array
    {
        return Arr::get($this->getAllFallbackLocales(), $locale ?: $this->getLocale(), [App::getFallbackLocale()]);
    }

    /**
     * Retrieve list of all fallback locales.
     */
    public function getAllFallbackLocales(): array
    {
        $locales = config('translations::translations.fallback_locales', []);

        if (empty($locales)) {
            return [App::getLocale() => [App::getFallbackLocale()]];
        }

        return $locales;
    }

    /**
     * Retrieve all supported locales.
     */
    public function getLocales(): array
    {
        $locales = config('translations::translations.locales', []);

        return empty($locales) ? array_unique([App::getLocale(), App::getFallbackLocale()]) : $locales;
    }
}
