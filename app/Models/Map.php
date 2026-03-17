<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    protected $fillable = [
        'alias',
        'name',
        'timeline',
        'location',
        'description',
    ];

    public function centralPoints(): HasMany
    {
        return $this->hasMany(CentralPoint::class)
            ->orderBy('order');
    }
}
