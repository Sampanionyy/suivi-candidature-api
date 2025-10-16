<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobContractTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('job_contract_types', 'name')->ignore($this->route('job_contract_type'))
            ],
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