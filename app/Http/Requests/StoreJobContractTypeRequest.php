<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobContractTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:job_contract_types,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du type de contrat est obligatoire.',
            'name.unique' => 'Ce type de contrat existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        ];
    }
}