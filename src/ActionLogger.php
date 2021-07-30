<?php

declare(strict_types=1);

namespace Fureev\ActionEvents;

use Fureev\ActionEvents\Contracts\ActionEventable;
use Fureev\ActionEvents\Entity\ActionEvent;
use Fureev\ActionEvents\Models\ActionEventModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ActionLogger
{
    public static string $model = ActionEventModel::class;

    public function build(ActionEvent|string $event): ActionEventable
    {
        if (is_string($event)) {
            $event = ActionEvent::make($event);
        }

        return $event;
    }

    public function push(ActionEvent|string $event): ?Model
    {
        /** @var Model $model */
        $model = ActionEventModel::make($this->build($event)->toArray());

        return $model->save() ? $model : null;
    }

    public function pushByModelCreate(Model $model, array|\Closure $data = null): ?Model
    {
        $event = ActionEvent::makeByModelCreate($model, $data)->setUser(auth()->user());

        return $this->push($event);
    }

    public function pushByModelUpdate(Model $model, array|\Closure $data = null): ?Model
    {
        $event = ActionEvent::makeByModelUpdate($model, $data)->setUser(auth()->user());

        return $this->push($event);
    }

    public function pushAndSaveByModelCreate(Model $model, array|\Closure $data = null): ?Model
    {
        return DB::transaction(
            function () use ($model, $data) {
                if ($model->save()) {
                    $event = ActionEvent::makeByModelCreate($model, $data)->setUser(auth()->user());
                    return $this->push($event);
                }

                return null;
            }
        );
    }

    public function pushAndSaveByModelUpdate(Model $model): ?Model
    {
        return DB::transaction(
            function () use ($model) {
                $changes = $model->getDirty();
                if ($model->save()) {
                    $event = ActionEvent::makeByModelUpdate($model, $changes)->setUser(auth()->user());
                    return $this->push($event);
                }

                return null;
            }
        );
    }

    /**
     * @param Collection $collection
     * @param \Closure|null $dataFilter
     *
     * @return Collection
     *
     * @wip
     */
    public function pushByCollectionCreate(Collection $collection, \Closure $dataFilter = null): Collection
    {
        $threadId = ActionEvent::buildTread();

        return $collection->each(
            fn($model) => $this->push(
                ($model instanceof Model
                    ? ActionEvent::makeByModelCreate($model, $dataFilter)
                    : ActionEvent::make($dataFilter ? $dataFilter($model) : $model)

                )
                    ->setThread($threadId)
                    ->setUser(auth()->user())
            )
        );
    }

    public static function setModel(string $model): self
    {
        self::$model = $model;

        return new self();
    }

    public static function model(): string
    {
        return self::$model;
    }
}
