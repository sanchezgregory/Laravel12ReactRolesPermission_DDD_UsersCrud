<?php

namespace App\Src\Infrastructure\Resources;

use App\Src\Domain\Entities\UserEntity;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @mixin UserEntity
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->value,
            'email' => $this->email->value,
            'roles' => $this->roles
        ];
    }
}
