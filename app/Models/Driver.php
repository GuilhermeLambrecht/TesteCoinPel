<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Searchable;
use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory, LogsActivity, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'cpf',
        'cnh',
        'cnh_category',
        'cnh_expiration',
        'phone',
        'active',
        'photo_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cnh_expiration' => 'date',
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
     * URL pública da foto (ou null quando o motorista não tem foto).
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $path = $this->photo_path;

            // Arquivos do disco 'public' são servidos via symlink em /storage.
            return is_string($path) && $path !== ''
                ? asset('storage/'.ltrim($path, '/'))
                : null;
        });
    }

    /**
     * @return array<int, string>
     */
    protected function searchableColumns(): array
    {
        return ['name', 'cpf'];
    }

    public function activityDescription(): string
    {
        return $this->name;
    }
}
