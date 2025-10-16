<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

//tout les utilisateurs qui ont accès au module(relation many to many)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_modules')
                    ->withPivot('active')
                    ->withTimestamps();
    }
}
