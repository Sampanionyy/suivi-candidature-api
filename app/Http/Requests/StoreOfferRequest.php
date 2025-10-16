<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'contract_type_id' => 'required|exists:job_contract_types,id',
            'work_mode_id' => 'required|exists:work_modes,id',
            'description' => 'nullable|string',
        ];
    }
}
