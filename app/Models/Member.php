<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'status',
        'role',
        'photo',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(MemberFile::class);
    }
}
