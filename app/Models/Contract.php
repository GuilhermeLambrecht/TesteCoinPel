<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Searchable;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    /** @use HasFactory<ContractFactory> */
    use HasFactory, LogsActivity, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'client_id',
        'package_id',
        'start_date',
        'end_date',
        'value',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'value' => 'decimal:2',
            'status' => ContractStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Package, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return array<int, string>
     */
    protected function searchableColumns(): array
    {
        return ['title'];
    }

    public function activityDescription(): string
    {
        return $this->title;
    }
}
