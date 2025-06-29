<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->cv->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'template' => ['sometimes', 'required', 'string', 'in:classic,modern,professional'],
            'language' => ['sometimes', 'required', 'string', 'in:sq,en'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Titulli i CV-së është i detyrueshëm.',
            'title.max' => 'Titulli i CV-së nuk mund të jetë më i gjatë se 255 karaktere.',
            'template.required' => 'Ju lutem zgjidhni një template.',
            'template.in' => 'Template-i i zgjedhur nuk është i vlefshëm.',
            'language.required' => 'Ju lutem zgjidhni një gjuhë.',
            'language.in' => 'Gjuha e zgjedhur nuk është e vlefshme.',
        ];
    }
} 