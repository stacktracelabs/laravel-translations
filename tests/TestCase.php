<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use StackTrace\Translations\TranslationsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TranslationsServiceProvider::class,
        ];
    }
}
