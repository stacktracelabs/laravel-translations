<?php


namespace StackTrace\Translations;


use Illuminate\Support\ServiceProvider;

class TranslationsServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->app->singleton(Translations::class);
    }

    /**
     * Boot the package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/translations.php', 'translations');
        }
    }

    /**
     * Register the package's publishable resources.
     */
    public function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/translations.php' => config_path('translations.php'),
        ], 'stacktrace-translations');
    }
}
