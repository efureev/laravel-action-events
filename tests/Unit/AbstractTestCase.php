<?php

namespace Fureev\ActionEvents\Tests\Unit;

use Illuminate\Foundation\Application;

abstract class AbstractTestCase extends \Fureev\ActionEvents\Tests\AbstractTestCase
{
    /**
     * Define environment setup.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
    }
}
