<?php

namespace Fureev\ActionEvents\Tests\Feature;

use Fureev\ActionEvents\Entity\ActionEvent;
use Fureev\ActionEvents\Entity\ActionEventStatus;
use Fureev\ActionEvents\Entity\ActionEventType;
use Fureev\ActionEvents\Tests\Database\Factories\UserFactory;
use Fureev\ActionEvents\Tests\Entity\Models\User;

class ActionEventTest extends AbstractTestCase
{
    public function testCreateActionEventFromModel(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->create();

        $event = ActionEvent::makeByModelCreate($modelUser);

        static::assertEquals('Create', $event->getName());
        static::assertEquals(ActionEventType::CHANGE, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertEquals($modelUser->getRawOriginal(), $event->getChangedData());
    }

    public function testCreateActionEventFromModelWithCustomData(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->create();
        $data2save = $modelUser->toArray();
        unset($data2save['email_verified_at'], $data2save['updated_at'], $data2save['created_at']);
        $event = ActionEvent::makeByModelCreate($modelUser, $data2save);

        static::assertEquals('Create', $event->getName());
        static::assertEquals(ActionEventType::CHANGE, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertEquals($data2save, $event->getChangedData());
    }

    public function testCreateActionEventFromModelWithCallableData(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->create();

        $event = ActionEvent::makeByModelCreate(
            $modelUser,
            fn($data) => [
                'id'  => $data['id'],
                'fio' => $data['name'],
            ]
        );

        static::assertEquals('Create', $event->getName());
        static::assertEquals(ActionEventType::CHANGE, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertCount(2, $event->getChangedData());
        static::assertEquals(
            [
                'id'  => $modelUser['id'],
                'fio' => $modelUser['name'],
            ],
            $event->getChangedData()
        );
    }


    public function testUpdateActionEventFromModel(): void
    {
        /** @var User $modelUser */
        $modelUser  = UserFactory::new()->create();
        $changeData = [
            'name'  => 'Test Name',
            'email' => 'test@test.tst',
        ];
        $modelUser->fill($changeData);

        $event = ActionEvent::makeByModelUpdate($modelUser);

        static::assertEquals('Update', $event->getName());
        static::assertEquals(ActionEventType::CHANGE, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertEquals($changeData, $event->getChangedData());
        static::assertEquals($modelUser->getRawOriginal(), $event->getOriginalData());
        static::assertEquals($modelUser->getRawOriginal('name'), $event->getOriginalData()['name']);
        static::assertEquals($modelUser->getOriginal('name'), $event->getOriginalData()['name']);
    }

    public function testUpdateActionEventFromModelWithCustomData(): void
    {
        /** @var User $modelUser */
        $modelUser  = UserFactory::new()->create();
        $changeData = [
            'name'  => 'Test Name',
            'email' => 'test@test.tst',
        ];
        $modelUser->fill($changeData);
        $changeData['extra'] = 'test';

        $event = ActionEvent::makeByModelUpdate($modelUser, $changeData);

        static::assertEquals('Update', $event->getName());
        static::assertEquals(ActionEventType::CHANGE, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertEquals($changeData, $event->getChangedData());
        static::assertEquals($modelUser->getRawOriginal(), $event->getOriginalData());
        static::assertEquals($modelUser->getRawOriginal('name'), $event->getOriginalData()['name']);
        static::assertEquals($modelUser->getOriginal('name'), $event->getOriginalData()['name']);
    }

    public function testUpdateActionEventFromModelWithCallableData(): void
    {
        /** @var User $modelUser */
        $modelUser  = UserFactory::new()->create();
        $changeData = [
            'name'  => 'Test Name',
            'email' => 'test@test.tst',
        ];
        $modelUser->fill($changeData);

        $event = ActionEvent::makeByModelUpdate(
            $modelUser,
            static function ($changed, $original) {
                $changed['original'] = $original;

                return $changed;
            }
        );

        static::assertEquals('Update', $event->getName());
        static::assertEquals(ActionEventType::CHANGE, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertCount(3, $event->getChangedData());
        static::assertEquals(
            [
                'name'     => 'Test Name',
                'email'    => 'test@test.tst',
                'original' => $modelUser->getRawOriginal(),
            ],
            $event->getChangedData()
        );
        static::assertEquals($modelUser->getRawOriginal(), $event->getOriginalData());
        static::assertEquals($modelUser->getRawOriginal('name'), $event->getOriginalData()['name']);
        static::assertEquals($modelUser->getOriginal('name'), $event->getOriginalData()['name']);
    }

}
