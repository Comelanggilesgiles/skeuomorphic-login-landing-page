<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Guests are allowed to attempt a sign-in.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the login form.
     *
     * Note: no minimum length is enforced on the password here — accounts
     * created under older policies must still be able to sign in. Length
     * rules belong on the registration / password-reset requests.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Messages phrased to match the login UI's inline errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'That does not look like a valid email address.',
            'password.required' => 'Please enter your password.',
        ];
    }
}
