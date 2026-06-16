<?php

namespace App\Http\Requests;

use App\Models\Contract;

class UpdateContractRequest extends StoreContractRequest
{
    /**
     * No update, o pacote já vinculado é aceito mesmo inativo (não força a troca);
     * ainda assim não se pode escolher um pacote inativo diferente.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        /** @var Contract $contract */
        $contract = $this->route('contract');

        $rules['client_id'] = ['required', $this->activeRule('clients', $contract->client_id)];
        $rules['package_id'] = ['required', $this->activeRule('packages', $contract->package_id)];

        return $rules;
    }
}
