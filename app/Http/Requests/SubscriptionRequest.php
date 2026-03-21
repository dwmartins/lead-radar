<?php

namespace App\Http\Requests;

use App\Rules\NoMaliciousContent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'id'            => ['nullable', 'integer'],
            'user_id'       => ['required', 'integer'],
            'plan_id'       => ['required', 'integer'],
            'plan_price_id' => ['required', 'integer'],
            'status'        => ['required', 'string', 'in:active,canceled,expired,pending'],
            'expires_at'    => ['required', 'date'],
            'notes'         => ['nullable', 'string', new NoMaliciousContent()],
            
            'payment_status'  => ['required', 'in:pending,paid,failed,refunded'],
            'payment_method'  => ['required_if:payment_status,paid', 'string'],
            'payment_paid_at' => ['required_if:payment_status,paid', 'date'],
        ];
    }
}
