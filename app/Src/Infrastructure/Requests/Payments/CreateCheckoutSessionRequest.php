<?php

namespace App\Src\Infrastructure\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class CreateCheckoutSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'mediator_id' => ['nullable', 'integer', 'min:1'],
            'method' => ['required', 'string', 'max:50'],   // 'stripe'
            'amount_minor' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'topic' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount_minor.min' => 'Amount must be at least 1 (minor unit).',
        ];
    }

    public function toDto(): array
    {
        return [
            'user_id' => (int) $this->user()->id,
            'mediator_id' => $this->input('mediator_id') ? (int) $this->input('mediator_id') : null,
            'method' => (string) $this->input('method'),
            'amount_minor' => (int) $this->input('amount_minor'),
            'currency' => (string) $this->input('currency'),
            'topic' => $this->input('topic'),
            'metadata' => (array) ($this->input('metadata') ?? []),
        ];
    }
}
