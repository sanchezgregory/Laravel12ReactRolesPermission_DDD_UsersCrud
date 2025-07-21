<?php

namespace App\Src\Application\Services\Backoffice\CachingServices;

enum AppCacheKeys: string
{
    // Define los prefijos o claves base como casos del enum
    case USER_DATA = 'user_data';
    case USERS_LIST = 'users_list';

    /**
     * Genera la clave final, añadiendo un identificador si es necesario.
     *
     * @param int|string|null $id
     * @return string
     */
    public function key(int|string|null $id): string
    {
        // Si se proporciona un ID, lo concatena.
        if ($id) {
            return $this->value . '_' . $id;
        }

        // Si no, devuelve el valor base (para listas generales).
        return $this->value;
    }
}
