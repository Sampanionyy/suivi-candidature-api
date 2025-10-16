<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:skills,name'],
            'skill_category_id' => ['nullable', 'integer', 'exists:skill_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la compétence est obligatoire.',
            'name.unique' => 'Cette compétence existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'skill_category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
        ];
    }
}