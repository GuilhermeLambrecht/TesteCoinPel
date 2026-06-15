<?php

namespace App\Http\Requests;

use App\Enums\TripStatus;
use App\Models\Trip;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Validator;

class StoreTripRequest extends FormRequest
{
    // A autorização é centralizada no controller (via Policy).

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'departure_at' => ['required', 'date'],
            // A chegada deve ser posterior à partida (regra de negócio do CLAUDE.md).
            'arrival_at' => ['required', 'date', 'after:departure_at'],
            // Opcional: omitido usa o default 'agendada'; se vier, deve ser válido.
            'status' => ['nullable', Rule::enum(TripStatus::class)],
            'vehicle_id' => ['required', $this->activeExistsRule('vehicles')],
            'driver_id' => ['required', $this->activeExistsRule('drivers')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'arrival_at.after' => 'A data/hora de chegada deve ser posterior à de partida.',
        ];
    }

    /**
     * Após validar os campos, checa conflito de agendamento (veículo/motorista
     * já alocado em outra viagem com horário sobreposto).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function () use ($validator): void {
            // Só checa conflito se os campos necessários já passaram nas regras.
            foreach (['departure_at', 'arrival_at', 'vehicle_id', 'driver_id'] as $field) {
                if ($validator->errors()->has($field)) {
                    return;
                }
            }

            $this->validateScheduleConflicts($validator);
        });
    }

    /**
     * Acusa conflito quando o veículo ou o motorista já está em viagem sobreposta.
     */
    protected function validateScheduleConflicts(Validator $validator): void
    {
        $departure = $this->date('departure_at');
        $arrival = $this->date('arrival_at');

        if ($departure === null || $arrival === null) {
            return;
        }

        if ($trip = $this->overlappingTrip('vehicle_id', $this->integer('vehicle_id'), $departure, $arrival)) {
            $validator->errors()->add('vehicle_id', $this->conflictMessage('O veículo', $trip));
        }

        if ($trip = $this->overlappingTrip('driver_id', $this->integer('driver_id'), $departure, $arrival)) {
            $validator->errors()->add('driver_id', $this->conflictMessage('O motorista', $trip));
        }
    }

    /**
     * Primeira viagem (não excluída) que sobrepõe o intervalo informado para a
     * coluna de relacionamento dada, ignorando a própria viagem em edição.
     *
     * Sobreposição: A.departure < B.arrival E A.arrival > B.departure
     * (viagens que apenas se encostam NÃO conflitam).
     */
    protected function overlappingTrip(string $column, int $relatedId, Carbon $departure, Carbon $arrival): ?Trip
    {
        return Trip::query()
            ->where($column, $relatedId)
            ->when($this->ignoredTripId(), fn ($query, $id) => $query->whereKeyNot($id))
            ->where('departure_at', '<', $arrival)
            ->where('arrival_at', '>', $departure)
            ->first();
    }

    /**
     * Id da viagem a ignorar na checagem (null na criação; a própria no update).
     */
    protected function ignoredTripId(): ?int
    {
        return null;
    }

    protected function conflictMessage(string $subject, Trip $trip): string
    {
        return sprintf(
            '%s já está alocado na viagem %s → %s (%s a %s).',
            $subject,
            $trip->origin,
            $trip->destination,
            Carbon::parse($trip->departure_at)->format('d/m/Y H:i'),
            Carbon::parse($trip->arrival_at)->format('d/m/Y H:i'),
        );
    }

    /**
     * Garante que o relacionamento aponta para um registro existente e não excluído,
     * que esteja ativo — OU seja exatamente o id já vinculado (`$currentId`), para o
     * update aceitar um veículo/motorista que ficou inativo após o vínculo.
     */
    protected function activeExistsRule(string $table, ?int $currentId = null): Exists
    {
        return Rule::exists($table, 'id')->where(function (Builder $query) use ($currentId) {
            $query->whereNull('deleted_at')
                ->where(function (Builder $inner) use ($currentId) {
                    $inner->where('active', true);
                    if ($currentId !== null) {
                        $inner->orWhere('id', $currentId);
                    }
                });
        });
    }
}
