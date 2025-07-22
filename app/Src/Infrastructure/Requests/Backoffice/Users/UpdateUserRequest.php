<?php

namespace App\Src\Infrastructure\Requests\Backoffice\Users;

use Illuminate\Foundation\Http\FormRequest;


class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->route('id'),
            'password' => 'nullable|min:8|confirmed',
            'password_confirmation' => 'nullable|min:8|same:password',
            'roles' => 'required|array',
        ];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser un correo electrónico válido.',
            'email.unique' => 'El email ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password_confirmation.required' => 'La confirmación de la contraseña es obligatoria.',
            'password_confirmation.min' => 'La confirmación de la contraseña debe tener al menos 8 caracteres.',
            'password_confirmation.same' => 'La confirmación de la contraseña no coincide con la contraseña.',
            'roles.required' => 'Al menos debe seleccionar un rol.',
        ];
    }
}
