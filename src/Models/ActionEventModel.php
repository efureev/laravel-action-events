<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Sitesoft\Alice\Casts\MetaCasts;

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
 * @property string $model_type
 * @property string $model_id
 * @property array $original
 * @property array $changes
 * @property Carbon $created_at
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
    ];

    public function getUpdatedAtColumn()
    {
        return null;
    }
}
