<?php

declare(strict_types=1);

namespace Fureev\ActionEvents;

use Php\Support\Laravel\ServiceProviders\AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    public const PACKAGE_NS = 'ActionLogger';

    public function register(): void
    {
        $this
            ->registerService(ActionLogger::class, self::PACKAGE_NS, true);
    }

    protected function beforeBoot(): void
    {
        $this->registerConfig('actionEvents');
    }

    protected static function packageSourcePath(): string
    {
        return __DIR__;
    }
}
