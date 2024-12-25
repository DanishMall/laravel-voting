<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'student_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'faculty' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:15'],
        ];
    }
    public function messages(): array
    {
        return [
            'student_id.required' => 'Student ID is required for voter registration.',
            'student_id.unique' => 'This Student ID is already registered.',
            'faculty.required' => 'Please select your faculty.',
            'phone.max' => 'Phone number cannot exceed 15 characters.',
        ];
    }
}
