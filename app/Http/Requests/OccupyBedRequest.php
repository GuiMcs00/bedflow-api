<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OccupyBedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * Strips non-digit characters from the CPF field.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $cpfDigits = preg_replace('/\D+/', '', (string) $this->input('cpf'));
        $this->merge(['cpf' => $cpfDigits]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cpf' => ['required', 'digits:11'],
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
