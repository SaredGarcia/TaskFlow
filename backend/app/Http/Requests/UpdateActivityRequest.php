<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', Rule::in(['pending', 'in_progress', 'completed'])],
            'priority' => ['sometimes', 'required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede superar 150 caracteres.',
            'status.in' => 'El estado seleccionado no es válido.',
            'priority.in' => 'La prioridad seleccionada no es válida.',
            'due_date.date_format' => 'La fecha límite debe tener formato YYYY-MM-DD.',
        ];
    }
}
