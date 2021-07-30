<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Tests\Entity\Models;

use Fureev\ActionEvents\HasActions;
use Fureev\ActionEvents\MadeActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 *
 * @property mixed $id
 * @property string $name
 * @property string $last_name
 * @property string $first_name
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin Model
 */
class User extends Authenticatable
{
    use MadeActions;
    use HasActions;

    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function fullName(): string
    {
        return \implode(' ', \array_filter([$this->first_name, $this->last_name]));
    }
}
