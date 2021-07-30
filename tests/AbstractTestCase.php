<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Tests;

use Fureev\ActionEvents\ServiceProvider;
use Orchestra\Testbench\TestCase;
use Php\Support\Laravel\Database\ServiceProvider as DBServiceProvider;

abstract class AbstractTestCase extends TestCase
{
    protected static function rootPath(string $path = null): string
    {
        return dirname(__DIR__) . '/' . ($path ? "/$path" : '');
    }

    protected function getPackageProviders($app): array
    {
        return [
            DBServiceProvider::class,
            ServiceProvider::class,
        ];
    }
}
