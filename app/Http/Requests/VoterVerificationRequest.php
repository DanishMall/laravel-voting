<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoterVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'voter' && !$this->user()->hasVerifiedEmail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'verification_code' => ['required', 'string', 'size:6'],
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
            'verification_code.required' => 'Please enter the verification code sent to your email.',
            'verification_code.size' => 'The verification code must be 6 characters long.',
        ];
    }
}
