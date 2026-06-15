<?php

namespace App\Models;

use App\Enums\TripStatus;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Searchable;
use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Trip extends Model
{
    /** @use HasFactory<TripFactory> */
    use HasFactory, LogsActivity, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'origin',
        'destination',
        'departure_at',
        'arrival_at',
        'status',
        'vehicle_id',
        'driver_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'departure_at' => 'datetime',
            'arrival_at' => 'datetime',
            'status' => TripStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return array<int, string>
     */
    protected function searchableColumns(): array
    {
        return ['origin', 'destination'];
    }

    public function activityDescription(): string
    {
        return "{$this->origin} → {$this->destination}";
    }

    /**
     * Filtra viagens com partida a partir da data informada (00:00 daquele dia).
     * Data inválida ou vazia é ignorada (não aplica o filtro).
     *
     * @param  Builder<Trip>  $query
     * @return Builder<Trip>
     */
    public function scopeDepartingFrom(Builder $query, ?string $date): Builder
    {
        if (! filled($date)) {
            return $query;
        }

        try {
            $from = Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            return $query;
        }

        return $query->where('departure_at', '>=', $from);
    }

    /**
     * Aplica filtros opcionais e combináveis (origem, destino e data).
     *
     * @param  Builder<Trip>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Trip>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $origin = is_string($filters['origin'] ?? null) ? trim($filters['origin']) : '';
        $destination = is_string($filters['destination'] ?? null) ? trim($filters['destination']) : '';
        $date = is_string($filters['date'] ?? null) ? trim($filters['date']) : '';

        return $query
            ->when($origin !== '', fn (Builder $q) => $q->whereRaw('lower(origin) like ?', ['%'.mb_strtolower($origin).'%']))
            ->when($destination !== '', fn (Builder $q) => $q->whereRaw('lower(destination) like ?', ['%'.mb_strtolower($destination).'%']))
            ->departingFrom($date !== '' ? $date : null);
    }
}
