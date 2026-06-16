<?php

namespace App\Http\Requests;

use App\Enums\ContractStatus;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class StoreContractRequest extends FormRequest
{
    // A autorização é centralizada no controller (via Policy).

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_id' => ['required', $this->activeRule('clients')],
            'package_id' => ['required', $this->activeRule('packages')],
            'start_date' => ['required', 'date'],
            // O término deve ser igual ou posterior ao início.
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'value' => ['required', 'numeric', 'min:0.01'],
            // Opcional: omitido usa o default 'rascunho'; se vier, deve ser válido.
            'status' => ['nullable', Rule::enum(ContractStatus::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início.',
        ];
    }

    /**
     * Garante que o registro (cliente/pacote) existe, não está excluído e está
     * ativo — OU é exatamente o já vinculado (`$currentId`), para o update aceitar
     * um vínculo que ficou inativo após criado (mesmo cuidado das viagens).
     */
    protected function activeRule(string $table, ?int $currentId = null): Exists
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
