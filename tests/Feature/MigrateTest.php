<?php

namespace Fureev\ActionEvents\Tests\Feature;

use Php\Support\Laravel\Database\Schema\Helpers\ColumnAssertions;
use Php\Support\Laravel\Database\Schema\Helpers\TableAssertions;

class MigrateTest extends AbstractTestCase
{
    use ColumnAssertions;
    use TableAssertions;

    public function testRecordsInDatabase(): void
    {
        $this->assertDatabaseCount('action_events', 0);
        $this->seeTable('action_events');
    }

    public function testColumnInTable(): void
    {
        $columns = [
            0  => 'id',
            1  => 'thread_id',
            2  => 'user_id',
            3  => 'name',
            4  => 'status',
            5  => 'type',
            6  => 'result',
            7  => 'model_type',
            8  => 'model_id',
            9  => 'original',
            10 => 'changes',
            11 => 'actionable_type',
            12 => 'actionable_id',
            13 => 'created_at',
        ];
        $this->assertSameTable($columns, 'action_events');
    }

}
