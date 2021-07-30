<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Tests\Entity\Models;

use Fureev\ActionEvents\HasActions;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property mixed $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin Model
 */
class Stuff extends Model
{
    use HasActions;

    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
