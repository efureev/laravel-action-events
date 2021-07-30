<?php

namespace Fureev\ActionEvents\Tests\Unit;

use Fureev\ActionEvents\Entity\ActionEvent;
use Fureev\ActionEvents\Entity\ActionEventStatus;
use Fureev\ActionEvents\Entity\ActionEventType;
use Fureev\ActionEvents\Tests\Entity\Events\CustomEvent;
use Fureev\ActionEvents\Tests\Entity\Events\Login;

class ActionEventTest extends AbstractTestCase
{
    public function testCreateInstance(): void
    {
        $name   = 'test message';
        $action = new ActionEvent(name: $name);

        static::assertEquals($name, $action->getName());
        static::assertEquals(ActionEventType::CHANGE, $action->getType());
        static::assertEquals(ActionEventType::EVENT, $action->typeEvent()->getType());
        static::assertEquals(ActionEventType::READ, $action->typeRead()->getType());

        static::assertEquals(ActionEventStatus::DONE, $action->getStatus());
        static::assertEquals(ActionEventStatus::FAILED, $action->failed()->getStatus());
        static::assertEquals(ActionEventStatus::RUNNING, $action->progress()->getStatus());

        static::assertNull($action->getUserId());

        $data = $action->toArray();
        unset($data['thread_id']);
        static::assertEquals(
            [
                'name'     => $name,
                'user_id'  => null,
                'type'     => ActionEventType::READ,
                'status'   => ActionEventStatus::RUNNING,
                'changes'  => null,
                'original' => null,
            ],
            $data
        );
    }

    public function testCreateInstanceFromString(): void
    {
        $name   = 'test message';
        $action = ActionEvent::make($name);

        static::assertEquals($name, $action->getName());
        static::assertEquals(ActionEventType::READ, $action->getType());
        static::assertEquals(ActionEventStatus::DONE, $action->getStatus());
        static::assertNull($action->getUserId());

        $data = $action->toArray();
        unset($data['thread_id']);
        static::assertEquals(
            [
                'name'     => $name,
                'user_id'  => null,
                'type'     => ActionEventType::READ,
                'status'   => ActionEventStatus::DONE,
                'changes'  => null,
                'original' => null,
            ],
            $data
        );
    }


    public function testCreateInstanceFromString2(): void
    {
        $name   = 'test message2';
        $action = ActionEvent::make($name, ActionEventType::READ);

        static::assertEquals($name, $action->getName());
        static::assertEquals(ActionEventType::READ, $action->getType());
        static::assertEquals(ActionEventStatus::DONE, $action->getStatus());
        static::assertNull($action->getUserId());

        $data = $action->toArray();
        unset($data['thread_id']);
        static::assertEquals(
            [
                'name'     => $name,
                'user_id'  => null,
                'type'     => ActionEventType::READ,
                'status'   => ActionEventStatus::DONE,
                'changes'  => null,
                'original' => null,
            ],
            $data
        );
    }

    public function testCreateInstanceFromEvent(): void
    {
        $event  = new Login();
        $action = ActionEvent::make($event);

        static::assertEquals($event->getName(), $action->getName());
        static::assertEquals(ActionEventType::EVENT, $action->getType());
        static::assertEquals(ActionEventStatus::DONE, $action->getStatus());
        static::assertNull($action->getUserId());

        $data = $action->toArray();
        unset($data['thread_id']);
        static::assertEquals(
            [
                'name'     => $event->getName(),
                'user_id'  => null,
                'type'     => ActionEventType::EVENT,
                'status'   => ActionEventStatus::DONE,
                'changes'  => null,
                'original' => null,
            ],
            $data
        );
    }

    public function testCreateInstanceFromCustomEvent(): void
    {
        $event  = new CustomEvent();
        $action = ActionEvent::make($event);

        static::assertEquals('CustomEvent', $action->getName());
        static::assertEquals(ActionEventType::EVENT, $action->getType());
        static::assertEquals(ActionEventStatus::DONE, $action->getStatus());
        static::assertNull($action->getUserId());

        $data = $action->toArray();
        unset($data['thread_id']);
        static::assertEquals(
            [
                'name'     => 'CustomEvent',
                'user_id'  => null,
                'type'     => ActionEventType::EVENT,
                'status'   => ActionEventStatus::DONE,
                'changes'  => null,
                'original' => null,
            ],
            $data
        );
    }

}
