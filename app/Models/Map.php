<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    /** @use HasFactory<\Database\Factories\MapFactory> */
    use HasFactory;

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
