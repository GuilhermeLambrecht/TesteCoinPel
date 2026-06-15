<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Registra em activity_logs quem criou/editou/excluiu o registro.
 * Só registra quando há um usuário autenticado — ações de sistema
 * (seeders, factories, comandos) não são auditadas.
 *
 * O trait sempre é usado em um Eloquent Model; o @mixin informa isso à
 * análise estática para que resolva os hooks de evento (created/updated/
 * deleted) e métodos como getKey().
 *
 * @mixin Model
 */
trait LogsActivity
{
    /**
     * Texto curto que identifica o registro no log.
     */
    abstract public function activityDescription(): string;

    public static function bootLogsActivity(): void
    {
        static::created(fn ($model) => $model->recordActivity('created'));
        static::updated(fn ($model) => $model->recordActivity('updated'));
        static::deleted(fn ($model) => $model->recordActivity('deleted'));
    }

    public function recordActivity(string $action): void
    {
        $userId = auth()->id();

        if ($userId === null) {
            return;
        }

        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'subject_type' => $this::class,
            'subject_id' => $this->getKey(),
            'description' => $this->activityDescription(),
        ]);
    }
}
