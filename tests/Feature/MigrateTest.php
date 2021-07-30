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
            'id',
            'thread_id',
            'user_id',
            'name',
            'status',
            'type',
            'result',
            'model_type',
            'model_id',
            'original',
            'changes',
            'extra',
            'actionable_type',
            'actionable_id',
            'created_at',
        ];
        
        $this->assertSameTable($columns, 'action_events');
    }

}
