<?php

namespace App\Http\Requests\Admin;

use App\Models\Reporter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ReporterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', $this->emailRule()],
            'password' => [
                Rule::when($this->route('reporter') === null, ['required'], ['nullable']),
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter the reporter name.',
            'email.required' => 'Enter an email address.',
            'email.unique' => 'A reporter with that email already exists.',
            'password.required' => 'Enter a password.',
            'password.min' => 'Use at least 8 characters for the password.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    private function emailRule(): Unique
    {
        $rule = Rule::unique('reporters', 'email');
        $reporter = $this->route('reporter');

        if ($reporter instanceof Reporter) {
            $rule->ignore($reporter);
        }

        return $rule;
    }
}
