<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'company' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'url' => 'nullable|url|max:255',
            'contract_type_id' => 'sometimes|required|exists:job_contract_types,id',
            'work_mode_id' => 'sometimes|required|exists:work_modes,id',
            'description' => 'nullable|string',
        ];
    }
}
