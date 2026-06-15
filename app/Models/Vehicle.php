<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Searchable;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory, LogsActivity, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'plate',
        'model',
        'brand',
        'capacity',
        'year',
        'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'year' => 'integer',
            'active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Trip, $this>
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * @return array<int, string>
     */
    protected function searchableColumns(): array
    {
        return ['plate', 'model'];
    }

    public function activityDescription(): string
    {
        return $this->plate;
    }
}
