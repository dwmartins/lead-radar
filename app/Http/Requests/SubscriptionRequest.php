<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
        $id = $this->input('id');

        return [
            'id'            => ['nullable', 'integer'],
            'plan_id'       => ['required', 'integer'],
            'status'        => ['required', 'string', 'in:active,canceled,expired,pending,trial'],
            'billing_cycle' => ['required', 'string', 'in:monthly,semiannual,yearly'],
            'user_id'       => ['required', 'integer']
        ];
    }
}
