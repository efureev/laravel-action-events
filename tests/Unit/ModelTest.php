<?php

namespace Fureev\ActionEvents\Tests\Unit;

use Fureev\ActionEvents\Entity\ActionEvent;
use Fureev\ActionEvents\Entity\ActionEventStatus;
use Fureev\ActionEvents\Entity\ActionEventType;
use Fureev\ActionEvents\Models\ActionEventModel;

class ModelTest extends AbstractTestCase
{
    public function testCreateModel(): void
    {
        $name  = 'test message';
        $event = ActionEvent::make($name);

        /** @var ActionEventModel $model */
        $model = ActionEventModel::make($event->toArray());

        $expectedFields = [
            'name'      => $name,
            'user_id'   => null,
            'type'      => ActionEventType::READ,
            'status'    => ActionEventStatus::DONE,
            'thread_id' => $model->thread_id,
            'changes'   => null,
            'original'  => null,
        ];

        static::assertEquals($expectedFields, $model->toArray());
    }

}
