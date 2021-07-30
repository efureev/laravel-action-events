<?php

namespace Fureev\ActionEvents\Tests\Feature;

use Fureev\ActionEvents\ActionLogger;
use Fureev\ActionEvents\Contracts\ActionEventable;
use Fureev\ActionEvents\Entity\ActionEvent;
use Fureev\ActionEvents\Entity\ActionEventStatus;
use Fureev\ActionEvents\Entity\ActionEventType;
use Fureev\ActionEvents\Models\ActionEventModel;
use Fureev\ActionEvents\Tests\Database\Factories\StuffFactory;
use Fureev\ActionEvents\Tests\Database\Factories\UserFactory;
use Fureev\ActionEvents\Tests\Entity\Events\Login;
use Fureev\ActionEvents\Tests\Entity\Models\Stuff;
use Fureev\ActionEvents\Tests\Entity\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Ramsey\Uuid\Uuid;

class ManagerPushingTest extends AbstractTestCase
{
    protected ActionLogger $pusher;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ActionLogger $pusher */
        $pusher = app(ActionLogger::class);

        $this->pusher = $pusher;
    }

    public function testBuildString(): void
    {
        $name = 'test event';

        /** @var ActionEventable $model */
        $event = $this->pusher->build($name);

        static::assertEquals($name, $event->getName());
        static::assertEquals(ActionEventType::READ, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertNull($event->getUserId());
    }

    public function testBuildActionEvent(): void
    {
        $name  = 'test event';
        $event = ActionEvent::make($name);

        /** @var ActionEventable $model */
        $event = $this->pusher->build($event);

        static::assertEquals($name, $event->getName());
        static::assertEquals(ActionEventType::READ, $event->getType());
        static::assertEquals(ActionEventStatus::DONE, $event->getStatus());
        static::assertNull($event->getUserId());
    }


    public function testPushString(): void
    {
        $name = 'test event';

        /** @var ActionEventModel $model */
        $model = $this->pusher->push($name);

        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals($name, $model->name);
        static::assertEquals(ActionEventType::READ, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNull($model->user_id);
        static::assertNotNull($model->created_at);

        static::assertNull($model->actionable_type);
        static::assertNull($model->actionable_id);

        static::assertCount(9, $model->toArray());
    }


    public function testPushModelCreate(): void
    {
        /** @var User $dataModel */
        $dataModel = UserFactory::new()->create();

        $this->be($dataModel);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelCreate($dataModel);

        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Create', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNotNull($model->user_id);
        static::assertNotNull($model->created_at);
        static::assertEquals($dataModel::class, $model->actionable_type);
        static::assertEquals($dataModel->id, $model->actionable_id);
        static::assertCount(11, $model->toArray());
    }

    public function testPushModelCreateWithCustomData(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->create();

        $data2save = $modelUser->toArray();
        unset($data2save['email_verified_at'], $data2save['updated_at'], $data2save['created_at']);

        $this->be($modelUser);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelCreate($modelUser, $data2save);

        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Create', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNotNull($model->user_id);
        static::assertNotNull($model->created_at);

        static::assertEquals($modelUser->name, $model->changes['name']);
        static::assertEquals($modelUser->last_name, $model->changes['last_name']);
        static::assertEquals($modelUser->first_name, $model->changes['first_name']);
        static::assertEquals($modelUser->email, $model->changes['email']);
        static::assertEquals($modelUser->id, $model->changes['id']);
        static::assertCount(5, $model->changes);

        static::assertCount(11, $model->toArray());
        static::assertEquals($modelUser::class, $model->actionable_type);
        static::assertEquals($modelUser->id, $model->actionable_id);
    }

    public function testPushModelCreateWithCallableData(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->create();

        $this->be($modelUser);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelCreate(
            $modelUser,
            fn($d) => [
                'id'    => $d['id'],
                'email' => $d['email'],
            ]
        );

        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Create', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNotNull($model->user_id);
        static::assertNotNull($model->created_at);

        static::assertEquals($modelUser->email, $model->changes['email']);
        static::assertEquals($modelUser->id, $model->changes['id']);
        static::assertCount(2, $model->changes);

        static::assertCount(11, $model->toArray());
        static::assertEquals($modelUser::class, $model->actionable_type);
        static::assertEquals($modelUser->id, $model->actionable_id);
    }

    public function testPushModelUpdate(): void
    {
        /** @var User $modelUser */
        $modelUser  = UserFactory::new()->create();
        $changeData = [
            'name'  => 'Test Name',
            'email' => 'test@test.tst',
        ];
        $modelUser->fill($changeData);

        $this->be($modelUser);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelUpdate($modelUser);

        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Update', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNotNull($model->user_id);
        static::assertNotNull($model->created_at);
        static::assertEquals($changeData, $model->changes);
        static::assertCount(count($changeData), $model->changes);
        static::assertCount(11, $model->toArray());
        static::assertCount(count($modelUser->getRawOriginal()), $model->original);
        static::assertEquals($modelUser->getRawOriginal('name'), $model->original['name']);
        static::assertEquals($modelUser->getOriginal('name'), $model->original['name']);
        static::assertEquals($modelUser::class, $model->actionable_type);
        static::assertEquals($modelUser->id, $model->actionable_id);
    }

    public function testPushModelUpdateWithCustomData(): void
    {
        /** @var User $modelUser */
        $modelUser  = UserFactory::new()->create();
        $changeData = [
            'name'  => 'Test Name',
            'email' => 'test@test.tst',
        ];
        $modelUser->fill($changeData);
        $changeData['extra'] = 'test';

        $this->be($modelUser);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelUpdate($modelUser, $changeData);

        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Update', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNotNull($model->user_id);
        static::assertNotNull($model->created_at);

        static::assertEquals($changeData['name'], $model->changes['name']);
        static::assertEquals($changeData['email'], $model->changes['email']);
        static::assertEquals($changeData['extra'], $model->changes['extra']);

        static::assertEquals($changeData, $model->changes);
        static::assertCount(count($changeData), $model->changes);
        static::assertCount(3, $changeData);

        static::assertCount(count($modelUser->getRawOriginal()), $model->original);
        static::assertEquals($modelUser->getRawOriginal('name'), $model->original['name']);
        static::assertEquals($modelUser->getOriginal('name'), $model->original['name']);
        static::assertCount(11, $model->toArray());
        static::assertEquals($modelUser::class, $model->actionable_type);
        static::assertEquals($modelUser->id, $model->actionable_id);
    }

    public function testPushModelUpdateWithCallableData(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->create();
        $this->be($modelUser);

        $changeData = [
            'name'  => 'Test Name',
            'email' => 'test@test.tst',
        ];
        $modelUser->fill($changeData);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelUpdate(
            $modelUser,
            static function ($changed, $original) {
                $changed['original'] = $original;

                return $changed;
            }
        );

        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Update', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNotNull($model->user_id);
        static::assertNotNull($model->created_at);

        static::assertEquals($modelUser->email, $model->changes['email']);
        static::assertEquals($modelUser->name, $model->changes['name']);
        static::assertTrue(isset($model->changes['original']));

        static::assertCount(3, $model->changes);
        static::assertCount(11, $model->toArray());
        static::assertCount(count($modelUser->getRawOriginal()), $model->original);
        static::assertEquals($modelUser->getRawOriginal('name'), $model->original['name']);
        static::assertEquals($modelUser->getOriginal('name'), $model->original['name']);
        static::assertEquals($modelUser::class, $model->actionable_type);
        static::assertEquals($modelUser->id, $model->actionable_id);
    }


    public function testPushAndSaveByModelCreate(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->make();

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushAndSaveByModelCreate($modelUser);

        static::assertTrue($modelUser->exists);
        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Create', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNull($model->user_id);
        static::assertNotNull($model->created_at);
        $exp    = $modelUser->getRawOriginal();
        $actual = $model->changes;

        unset($exp['email_verified_at'], $actual['email_verified_at']);

        static::assertEquals($exp, $actual);
        static::assertCount(11, $model->toArray());
        static::assertEquals($modelUser::class, $model->actionable_type);
        static::assertEquals($modelUser->id, $model->actionable_id);
    }

    public function testPushAndSaveByModelUpdate(): void
    {
        /** @var User $modelUser */
        $modelUser = UserFactory::new()->create();
        $this->be($modelUser);

        $changeData = [
            'name'  => 'Test Name',
            'email' => 'test@test.tst',
        ];
        $modelUser->fill($changeData);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushAndSaveByModelUpdate($modelUser);

        static::assertEmpty($modelUser->getDirty());
        static::assertTrue($model->exists);
        static::assertTrue(Uuid::isValid($model->id));
        static::assertTrue(Uuid::isValid($model->thread_id));
        static::assertEquals('Update', $model->name);
        static::assertEquals(ActionEventType::CHANGE, $model->type);
        static::assertEquals(ActionEventStatus::DONE, $model->status);
        static::assertNotNull($model->user_id);
        static::assertNotNull($model->created_at);
        static::assertEquals($changeData, $model->changes);
        static::assertCount(count($changeData), $model->changes);
        static::assertCount(11, $model->toArray());
        static::assertCount(count($modelUser->getRawOriginal()), $model->original);
        static::assertEquals($modelUser->getRawOriginal('name'), $model->original['name']);
        static::assertEquals($modelUser->getOriginal('name'), $model->original['name']);
        static::assertEquals($modelUser::class, $model->actionable_type);
        static::assertEquals($modelUser->id, $model->actionable_id);
    }


    public function testPushModelCreateAndRelation(): void
    {
        /** @var User $dataModel */
        $dataModel = UserFactory::new()->create();

        $this->be($dataModel);

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelCreate($dataModel);

        static::assertCount(1, $dataModel->madeActions);
        static::assertEquals($model->id, $dataModel->madeActions->first()->id);

        $this->pusher->pushAndSaveByModelCreate(UserFactory::new()->make());

        static::assertCount(2, $dataModel->madeActions()->get());
    }

    public function testPushModelCreateAndUpdate(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create();
        $this->be($user);

        /** @var Stuff $staff */
        $staff = StuffFactory::new()->create();

        /** @var ActionEventModel $model */
        $model = $this->pusher->pushByModelCreate($staff);

        $staff->fill(['name' => 'Jack']);
        $this->pusher->pushAndSaveByModelUpdate($staff);

        static::assertCount(2, $staff->actions);
    }


    public function testPushModelCollection(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create();
        $this->be($user);

        /** @var Collection $staffModels */
        $staffModels = StuffFactory::times(10)->create();
        $staffModels->add('time');
        $staffModels->add(new Login());

        /** @var \Illuminate\Support\Collection $model */
        $models = $this->pusher->pushCollectionCreate($staffModels);

        static::assertCount(12, $models);
        static::assertCount(12, $user->madeActions);
    }
}
