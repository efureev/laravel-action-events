<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Entity;

use Closure;
use Fureev\ActionEvents\Contracts\Actionable;
use Fureev\ActionEvents\Contracts\ActionEventable;
use Fureev\ActionEvents\Helpers\ModelHelper;
use Illuminate\Database\Eloquent\Model;

class ActionEvent extends AbstractActionEvent
{
    protected string $status = ActionEventStatus::DONE;

    protected array $extra = [];

    protected ?Model $model = null;

    public function __construct(
        protected string $name,
        protected string $type = ActionEventType::CHANGE
    ) {
    }

    public static function make(string|object $event, string $type = null): ActionEventable
    {
        if ($event instanceof ActionEventable) {
            return $event;
        }

        if ($event instanceof Actionable || is_string($event)) {
            $type = $event instanceof Actionable ? ActionEventType::EVENT : ($type ?? ActionEventType::READ);
            return static::resolveEvent($event, $type);
        }

        if (is_object($event)) {
            return static::resolveEvent(class_basename($event), ActionEventType::EVENT);
        }

        throw new \RuntimeException('Invalid type of event: ' . gettype($event));
    }

    public static function makeByModelCreate(Model $model, array|Closure $data = null): ActionEventable
    {
        $change = match (true) {
            null === $data => $model->getRawOriginal(),
            is_array($data) => $data,
            $data instanceof Closure => $data($model->getRawOriginal()),
        };

        return (new static('Create'))
            ->setModel($model)
            ->setChangedData($change)
            ->setExtraData(static::resolveExtraClass($model, $change));
    }

    public static function makeByModelUpdate(Model $model, array|Closure $dataFilter = null): ActionEventable
    {
        $change = match (true) {
            null === $dataFilter => $model->getDirty(),
            is_array($dataFilter) => $dataFilter,
            $dataFilter instanceof Closure => $dataFilter($model->getDirty(), $model->getRawOriginal()),
        };

        return (new static('Update'))
            ->setModel($model)
            ->setChangedData(static::resolveMapClass($model, $change))
            ->setOriginalData($model->getRawOriginal())
            ->setExtraData(static::resolveExtraClass($model, $change));
    }

    public static function makeByModelDelete(Model $model, ?string $name = null): ActionEventable
    {
        $name = $name ?? ModelHelper::hasSoftDelete($model) ? 'Soft Delete' : 'Delete';

        return (new static($name))
            ->setModel($model)
            ->setOriginalData($model->getRawOriginal());
    }

    protected static function resolveMapClass(Model|string $model, $change): ?array
    {
        return static::resolveClassInModel($model, 'resolveActionEventMapClass', $change, $change);
    }

    protected static function resolveExtraClass(Model|string $model, $change): ?array
    {
        return static::resolveClassInModel($model, 'resolveActionEventExtraClass', $change);
    }

    protected static function resolveClassInModel(Model|string $model, string $fn, $change, $default = null): ?array
    {
        $cls = method_exists($model, $fn)
            ? $model::$fn()
            : null;

        if (!$cls) {
            return $default;
        }

        if (is_callable($cls)) {
            return $cls($change);
        }

        if (class_exists($cls) && method_exists($cls, 'toArray')) {
            return (new $cls($change))->toArray();
        }

        return $default;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->user) {
            $data['user_id'] = $this->getUserId();
        }

        if ($this->model) {
            $data['target_type'] = $this->model->getMorphClass();
            $data['target_id']   = $this->model->getKey();
        }

        if ($this->extra) {
            $data['extra'] = $this->extra;
        }

        return array_merge($data, parent::toArray());
    }

    protected static function resolveEvent(string|Actionable $event, string $type = null): ActionEventable
    {
        $name = $event instanceof Actionable ? $event->getName() : $event;

        return new ActionEvent($name, ($type ?? ActionEventType::CHANGE));
    }

    public function typeEvent(): self
    {
        $this->type = ActionEventType::EVENT;

        return $this;
    }

    public function typeRead(): self
    {
        $this->type = ActionEventType::READ;

        return $this;
    }

    public function done(): self
    {
        $this->status = ActionEventStatus::DONE;

        return $this;
    }

    public function failed(): self
    {
        $this->status = ActionEventStatus::FAILED;

        return $this;
    }

    public function progress(): self
    {
        $this->status = ActionEventStatus::RUNNING;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function setExtraData(?array $data): static
    {
        $this->extra = $data ?? [];

        return $this;
    }
}
