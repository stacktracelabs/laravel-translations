<?php


namespace StackTrace\Translations\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static string getLocale()
 * @method static array getFallbackLocales(?string $locale = null)
 * @method static array getLocales()
 */
class Translations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \StackTrace\Translations\Translations::class;
    }
}
