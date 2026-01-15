<?php

namespace App\Src\Infrastructure\Requests\Backoffice\Mediators;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id'); // Assuming the route parameter is 'id'

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
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
        return array_merge($data, ['id' => (int) $this->route('id')]);
    }
}
