<?php

declare(strict_types=1);

namespace Fureev\ActionEvents;

use Fureev\ActionEvents\Contracts\ActionEventable;
use Fureev\ActionEvents\Entity\ActionEvent;
use Fureev\ActionEvents\Models\ActionEventModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ActionLogger
{
    public static string $model = ActionEventModel::class;


    public static function setModel(string $model): self
    {
        self::$model = $model;

        return new self();
    }

    public static function model(): string
    {
        return self::$model;
    }

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
        return $this->push(ActionEvent::makeByModelCreate($model, $data)->setUser(auth()->user()));
    }

    public function pushByModelUpdate(Model $model, array|\Closure $data = null): ?Model
    {
        return $this->push(ActionEvent::makeByModelUpdate($model, $data)->setUser(auth()->user()));
    }

    public function pushAndSaveByModelCreate(Model $model, array|\Closure $dataFilter = null): ?Model
    {
        return DB::transaction(
            function () use ($model, $dataFilter) {
                if ($model->save()) {
                    return $this->push(ActionEvent::makeByModelCreate($model, $dataFilter)->setUser(auth()->user()));
                }

                return null;
            }
        );
    }

    public function pushAndSaveByModelUpdate(Model $model, array|\Closure $dataFilter = null): ?Model
    {
        return DB::transaction(
            function () use ($model, $dataFilter) {
                $event = ActionEvent::makeByModelUpdate($model, $dataFilter);
                if ($model->save()) {
                    return $this->push($event->setUser(auth()->user()));
                }

                return null;
            }
        );
    }

    /**
     * @param Collection $collection
     * @param \Closure|null $dataFilter
     *
     * @return EloquentCollection of ActionEvent models
     *
     * @wip
     */
    public function pushCollectionCreate(Collection $collection, \Closure $dataFilter = null): EloquentCollection
    {
        $threadId = ActionEvent::buildTread();
        $events   = new EloquentCollection();

        $collection->each(
            fn($model) => $events->add(
                $this->push(
                    ($model instanceof Model
                        ? ActionEvent::makeByModelCreate($model, $dataFilter)
                        : ActionEvent::make($dataFilter ? $dataFilter($model) : $model)

                    )
                        ->setThread($threadId)
                        ->setUser(auth()->user())
                )
            )
        );

        return $events;
    }

    /**
     * @param Collection $collection of the models for change and saving
     * @param \Closure|null $dataFilter
     *
     * @return EloquentCollection of ActionEvent models
     */
    public function pushCollectionUpdate(Collection $collection, \Closure $dataFilter = null): EloquentCollection
    {
        return DB::transaction(
            function () use ($collection, $dataFilter) {
                $threadId = ActionEvent::buildTread();
                $events   = [];

                $collection->each(
                    function ($model) use ($dataFilter, $threadId, &$events) {
                        if ($model instanceof Model) {
                            $event = ActionEvent::makeByModelUpdate($model, $dataFilter);
                            if (!$model->save()) {
                                return;
                            }
                        } else {
                            $event = ActionEvent::make($dataFilter ? $dataFilter($model) : $model);
                        }

                        $events[] = $this->push($event->setThread($threadId)->setUser(auth()->user()));
                    }
                );

                return new EloquentCollection($events);
            }
        );
    }
}
