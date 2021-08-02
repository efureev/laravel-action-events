<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class ActionEventModel
 * @package Fureev\ActionEvents\Models
 *
 * @property string $id
 * @property string $thread_id
 * @property string|int $user_id
 * @property string $name
 * @property string $status
 * @property string $type
 * @property array $result
 * @property string $target_type
 * @property string $target_id
 * @property array $original
 * @property array $changes
 * @property array $extra
 * @property Carbon $created_at
 * @property Model|null $target
 */
class ActionEventModel extends Model
{
    protected $guarded = [];

    /** @var string */
    protected $keyType = 'string';

    protected $table = 'action_events';

    protected $casts = [
        'original' => 'array',
        'changes'  => 'array',
        'extra'    => 'array',
    ];

    public function getUpdatedAtColumn()
    {
        return null;
    }

    /**
     * Get the target of the action
     *
     * @return MorphTo
     */
    public function target(): MorphTo
    {
        return $this->morphTo('target', 'target_type', 'target_id')->withTrashed();
    }


    /**
     * Get the collection of the actions with same thread
     *
     * @return Collection
     */
    public function getThread()
    {
        return $this->where('thread_id', $this->thread_id)->get();
    }

    public function isModel(): bool
    {
        return $this->target_type !== null && $this->target_id !== null;
    }
}
