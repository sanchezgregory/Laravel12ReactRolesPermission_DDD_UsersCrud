<?php

namespace App\Src\Infrastructure\Requests\Backoffice\Mediators;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'session_price_minor' => 'required|integer|min:0',
            'currency' => 'required|string|size:3',
            'calendly_url' => 'nullable|url',
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ];
    }

    public function toArray(): array
    {
        $data = $this->validated();
        // Normalize any data if needed
        return $data;
    }
}
