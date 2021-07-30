<?php

namespace Fureev\ActionEvents\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

abstract class AbstractTestCase extends \Fureev\ActionEvents\Tests\AbstractTestCase
{
    use InteractsWithDatabase;

    protected array $migrations = [
        'tests/database/migrations/2021_07_03_110000_create_test_user_table.php',
        'tests/database/migrations/2021_07_05_110000_create_test_stuff_table.php',
        'database/migrations/2021_07_29_091200_create_actions_events_table.php',
    ];

    /**
     * Define environment setup.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set(
            'database.connections.testing',
            [
                'driver'         => 'pgsql',
                'url'            => env('DATABASE_URL'),
                'host'           => env('DB_HOST', 'localhost'),
                'port'           => env('DB_PORT', '5432'),
                'database'       => env('DB_DATABASE', 'postgres'),
                'username'       => env('DB_USERNAME', 'postgres'),
                'password'       => env('DB_PASSWORD', 'postgres'),
                'charset'        => 'utf8',
                'prefix'         => '',
                'prefix_indexes' => true,
                'schema'         => 'public',
                'sslmode'        => 'prefer',
            ]
        );
    }


    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:wipe');

        $this->installMigrations();
    }

    protected function installMigrations(): void
    {
        foreach ($this->migrations as $migration) {
            $this->loadMigrationsFrom(self::rootPath($migration));
        }
    }
}
