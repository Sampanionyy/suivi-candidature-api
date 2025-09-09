<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'position' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'job_url' => ['nullable', 'url', 'max:1000'],
            'applied_date' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(\App\Models\Application::STATUSES))],
            'interview_date' => ['nullable', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:2000']
        ];

        // Pour la mise à jour, on peut permettre des champs optionnels
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['position'][0] = 'sometimes';
            $rules['company'][0] = 'sometimes';
            $rules['applied_date'][0] = 'sometimes';
            $rules['status'][0] = 'sometimes';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'position.required' => 'Le nom du poste est obligatoire.',
            'company.required' => 'Le nom de l\'entreprise est obligatoire.',
            'job_url.url' => 'L\'URL de l\'offre doit être valide.',
            'applied_date.required' => 'La date de candidature est obligatoire.',
            'applied_date.date' => 'La date de candidature doit être valide.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'interview_date.date' => 'La date d\'entretien doit être valide.',
            'interview_date.after' => 'La date d\'entretien doit être dans le futur.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 2000 caractères.'
        ];
    }

    protected function prepareForValidation(): void
    {
        // S'assurer que l'user_id est défini
        if (!$this->has('user_id')) {
            $this->merge(['user_id' => auth()->id()]);
        }
    }
}
