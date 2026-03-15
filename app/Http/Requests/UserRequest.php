<?php

namespace App\Http\Requests;

use App\Rules\NoMaliciousContent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user_id = $this->input('id');

        $base_rules = [
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user_id)],
            'account_status' => ['required', 'boolean'],
            'plan_id' => ['nullable', 'integer']
        ];

        foreach ($base_rules as $field => $rules) {
            if (is_array($rules)) {
                $rules[] = new NoMaliciousContent();
            }
        }

        return $base_rules;
    }
}
