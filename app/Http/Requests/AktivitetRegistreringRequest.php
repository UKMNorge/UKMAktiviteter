<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AktivitetRegistreringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mobileNumber' => 'required|string|min:8|max:12',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'mobileNumber.required' => 'Mobilnummer er påkrevd.',
            'mobileNumber.min' => 'Mobilnummer må være minst 8 tegn.',
            'mobileNumber.max' => 'Mobilnummer kan ikke være lenger enn 12 tegn.',
        ];
    }
}
