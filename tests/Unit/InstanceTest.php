<?php

namespace Fureev\ActionEvents\Tests\Unit;

use Fureev\ActionEvents\ActionLogger;
use Fureev\ActionEvents\ServiceProvider;

class InstanceTest extends AbstractTestCase
{
    public function testLoadInstance(): void
    {
        static::assertInstanceOf(ActionLogger::class, app(ActionLogger::class));
        static::assertInstanceOf(ActionLogger::class, app(ServiceProvider::PACKAGE_NS));
    }

}
