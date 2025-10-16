<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkModeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:work_modes,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du mode de travail est obligatoire.',
            'name.unique' => 'Ce mode de travail existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        ];
    }
}