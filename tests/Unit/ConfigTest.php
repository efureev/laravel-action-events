<?php

namespace Fureev\ActionEvents\Tests\Unit;

class ConfigTest extends AbstractTestCase
{
    public function testLoadConfig(): void
    {
        $config = $this->app['config']->get('actionEvents');

        self::assertEquals(require static::rootPath('config/actionEvents.php'), $config);
    }

}
