# Quick Reference - Plantillas y Checklists

## 📋 Contenido

1. [Checklist Completo para Nuevo Feature](#checklist-completo-para-nuevo-feature)
2. [Plantillas de Código](#plantillas-de-código)
3. [Comandos Útiles](#comandos-útiles)
4. [Estructura de Archivos](#estructura-de-archivos)
5. [Convenciones de Nomenclatura](#convenciones-de-nomenclatura)

---

## ✅ Checklist Completo para Nuevo Feature

### Fase 1: Planificación
- [ ] Definir requisitos del feature
- [ ] Identificar entidades del dominio
- [ ] Identificar value objects necesarios
- [ ] Diseñar interfaces de repositorio
- [ ] Diseñar interfaces de servicio

### Fase 2: Domain Layer
- [ ] Crear Value Objects
  - [ ] Implementar validación
  - [ ] Usar trait `StringValueObject`
  - [ ] Implementar `Stringable` y `JsonSerializable`
- [ ] Crear Entity
  - [ ] Extender `BaseEntity`
  - [ ] Implementar `fromArray()`
  - [ ] Implementar `toArray()`
  - [ ] Agregar métodos de negocio
- [ ] Crear Repository Interface
  - [ ] Definir métodos CRUD
  - [ ] Definir métodos de búsqueda
- [ ] Crear Service Interface
  - [ ] Definir métodos de aplicación
- [ ] Crear Excepciones (si es necesario)
  - [ ] Extender `DomainException`
  - [ ] Definir `isUserFacing`

### Fase 3: Application Layer
- [ ] Crear Application Service
  - [ ] Implementar Service Interface
  - [ ] Inyectar Repository Interface
  - [ ] Implementar lógica de orquestación
- [ ] Crear DTOs (si es necesario)
- [ ] Crear Events (si es necesario)
- [ ] Crear Listeners (si es necesario)

### Fase 4: Infrastructure Layer
- [ ] Crear Eloquent Model
  - [ ] Definir `$fillable`
  - [ ] Definir `$casts`
  - [ ] Definir relaciones
- [ ] Crear Migration
  - [ ] Definir tabla
  - [ ] Definir columnas
  - [ ] Definir índices
  - [ ] Definir foreign keys
- [ ] Crear Eloquent Repository
  - [ ] Implementar Repository Interface
  - [ ] Implementar `toEntity()`
  - [ ] Implementar métodos CRUD
  - [ ] Manejar excepciones
- [ ] Crear Form Requests
  - [ ] `StoreRequest` con validaciones
  - [ ] `UpdateRequest` con validaciones
  - [ ] Implementar `toArray()`
  - [ ] Implementar `messages()`
- [ ] Crear Controllers Invocables
  - [ ] `IndexController`
  - [ ] `CreateController`
  - [ ] `StoreController`
  - [ ] `EditController`
  - [ ] `UpdateController`
  - [ ] `DestroyController`

### Fase 5: Configuración
- [ ] Registrar bindings en `AppServiceProvider`
  - [ ] Service Interface → Service Implementation
  - [ ] Repository Interface → Repository Implementation
  - [ ] Decorator (si aplica)
- [ ] Definir rutas en archivo de rutas
- [ ] Incluir archivo de rutas en `web.php`

### Fase 6: Frontend (si aplica)
- [ ] Crear componentes React
- [ ] Crear páginas Inertia
- [ ] Definir tipos TypeScript
- [ ] Implementar formularios

### Fase 7: Testing
- [ ] Tests de Value Objects
- [ ] Tests de Entities
- [ ] Tests de Services
- [ ] Tests de Repositories
- [ ] Tests de Controllers
- [ ] Tests de integración

### Fase 8: Documentación
- [ ] Documentar Value Objects
- [ ] Documentar Entity
- [ ] Documentar Service
- [ ] Documentar Repository
- [ ] Actualizar README si es necesario

---

## 📝 Plantillas de Código

### 1. Value Object Template

```php
<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Value Object para [DESCRIPCIÓN]
 * 
 * Reglas de negocio:
 * - [REGLA 1]
 * - [REGLA 2]
 */
final readonly class [NombreValueObject] implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        
        // Validación: No vacío
        if (empty($value)) {
            throw new InvalidArgumentException('[MENSAJE DE ERROR]');
        }
        
        // Validación: Longitud mínima
        if (strlen($value) < [MIN_LENGTH]) {
            throw new InvalidArgumentException('[MENSAJE DE ERROR]');
        }
        
        // Validación: Longitud máxima
        if (strlen($value) > [MAX_LENGTH]) {
            throw new InvalidArgumentException('[MENSAJE DE ERROR]');
        }
        
        // Validación: Formato (ejemplo: regex)
        if (!preg_match('/[PATTERN]/', $value)) {
            throw new InvalidArgumentException('[MENSAJE DE ERROR]');
        }
        
        // Normalización (opcional)
        $value = strtolower($value);
    }
}
```

### 2. Entity Template

```php
<?php

namespace App\Src\Domain\Entities;

use App\Src\Domain\ValueObjects\[ValueObject1];
use App\Src\Domain\ValueObjects\[ValueObject2];

/**
 * Entidad [NOMBRE]
 * 
 * Representa [DESCRIPCIÓN]
 * 
 * Reglas de negocio:
 * - [REGLA 1]
 * - [REGLA 2]
 */
class [NombreEntity] extends BaseEntity
{
    public function __construct(
        public ?int $id,
        public [ValueObject1] $[campo1],
        public [ValueObject2] $[campo2],
        public [tipo] $[campo3],
        // ... más campos
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Crea una entidad desde un array de datos
     * 
     * @param array $data Datos de entrada
     * @return static Nueva instancia de la entidad
     * @throws \InvalidArgumentException Si los datos no son válidos
     */
    public static function fromArray(array $data): static
    {
        // Crear Value Objects (con validación automática)
        $[campo1] = [ValueObject1]::fromString($data['[campo1]']);
        $[campo2] = [ValueObject2]::fromString($data['[campo2]']);
        
        // Validaciones adicionales de negocio
        if ([CONDICIÓN]) {
            throw new \DomainException('[MENSAJE DE ERROR]');
        }
        
        return new static(
            $data['id'] ?? null,
            $[campo1],
            $[campo2],
            $data['[campo3]'] ?? [DEFAULT_VALUE],
            // ... más campos
        );
    }

    /**
     * Convierte la entidad a un array
     * 
     * @return array Representación en array de la entidad
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            '[campo1]' => (string) $this->[campo1],
            '[campo2]' => (string) $this->[campo2],
            '[campo3]' => $this->[campo3],
            // ... más campos
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    // ============================================
    // Métodos de Negocio
    // ============================================

    /**
     * [DESCRIPCIÓN DEL MÉTODO]
     * 
     * @param [tipo] $[param] [descripción]
     * @return [tipo] [descripción]
     * @throws \DomainException Si [condición]
     */
    public function [metodoDeNegocio]([tipo] $[param]): [tipo]
    {
        // Validación
        if ([CONDICIÓN]) {
            throw new \DomainException('[MENSAJE DE ERROR]');
        }
        
        // Lógica de negocio
        // ...
        
        return [RESULTADO];
    }
}
```

### 3. Repository Interface Template

```php
<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\[NombreEntity];

/**
 * Interfaz del repositorio de [NOMBRE]
 * 
 * Define los métodos para persistir y recuperar [NOMBRE]
 */
interface [NombreRepository]Interface
{
    /**
     * Busca una entidad por su ID
     * 
     * @param int $id ID de la entidad
     * @return [NombreEntity]|null Entidad encontrada o null
     */
    public function findById(int $id): ?[NombreEntity];

    /**
     * Busca una entidad por [CAMPO]
     * 
     * @param string $[campo] [Descripción]
     * @return [NombreEntity]|null Entidad encontrada o null
     */
    public function findBy[Campo](string $[campo]): ?[NombreEntity];

    /**
     * Obtiene todas las entidades
     * 
     * @return array Array de entidades
     */
    public function getAll(): array;

    /**
     * Persiste una nueva entidad
     * 
     * @param [NombreEntity] $[entidad] Entidad a persistir
     * @return void
     */
    public function save([NombreEntity] $[entidad]): void;

    /**
     * Actualiza una entidad existente
     * 
     * @param int $id ID de la entidad
     * @param [NombreEntity] $[entidad] Datos actualizados
     * @return void
     */
    public function update(int $id, [NombreEntity] $[entidad]): void;

    /**
     * Elimina una entidad
     * 
     * @param int $id ID de la entidad
     * @return void
     */
    public function delete(int $id): void;
}
```

### 4. Service Interface Template

```php
<?php

namespace App\Src\Domain\Contracts\ServiceContracts;

use App\Src\Domain\Entities\[NombreEntity];

/**
 * Interfaz del servicio de [NOMBRE]
 * 
 * Define los casos de uso de [NOMBRE]
 */
interface [NombreService]Interface
{
    /**
     * Busca una entidad por su ID
     * 
     * @param int $id ID de la entidad
     * @return [NombreEntity]|null Entidad encontrada o null
     */
    public function findById(int $id): ?[NombreEntity];

    /**
     * Obtiene todas las entidades
     * 
     * @return array Array de entidades
     */
    public function getAll(): array;

    /**
     * Crea una nueva entidad
     * 
     * @param array $data Datos de la entidad
     * @return void
     */
    public function save(array $data): void;

    /**
     * Actualiza una entidad existente
     * 
     * @param int $id ID de la entidad
     * @param array $data Datos actualizados
     * @return void
     */
    public function update(int $id, array $data): void;

    /**
     * Elimina una entidad
     * 
     * @param int $id ID de la entidad
     * @return void
     */
    public function delete(int $id): void;
}
```

### 5. Application Service Template

```php
<?php

namespace App\Src\Application\Services\Backoffice;

use App\Src\Domain\Contracts\RepositoryContracts\[NombreRepository]Interface;
use App\Src\Domain\Contracts\ServiceContracts\[NombreService]Interface;
use App\Src\Domain\Entities\[NombreEntity];

/**
 * Servicio de aplicación para [NOMBRE]
 * 
 * Orquesta los casos de uso de [NOMBRE]
 */
class [NombreService] implements [NombreService]Interface
{
    public function __construct(
        private readonly [NombreRepository]Interface $[nombre]Repository
    ) {}

    public function findById(int $id): ?[NombreEntity]
    {
        return $this->[nombre]Repository->findById($id);
    }

    public function getAll(): array
    {
        return $this->[nombre]Repository->getAll();
    }

    public function save(array $data): void
    {
        $entity = [NombreEntity]::fromArray($data);
        $this->[nombre]Repository->save($entity);
    }

    public function update(int $id, array $data): void
    {
        $entity = [NombreEntity]::fromArray($data);
        $this->[nombre]Repository->update($id, $entity);
    }

    public function delete(int $id): void
    {
        $this->[nombre]Repository->delete($id);
    }
}
```

### 6. Eloquent Repository Template

```php
<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Models\[NombreModel] as [NombreModel]Model;
use App\Src\Domain\Contracts\RepositoryContracts\[NombreRepository]Interface;
use App\Src\Domain\Entities\[NombreEntity];
use App\Src\Infrastructure\Exceptions\RepositoryException;

/**
 * Implementación Eloquent del repositorio de [NOMBRE]
 */
class [NombreEloquent]Repository implements [NombreRepository]Interface
{
    public function __construct(private [NombreModel]Model $model) {}

    /**
     * Convierte un modelo Eloquent a una entidad de dominio
     * 
     * @param [NombreModel]Model $model Modelo Eloquent
     * @return [NombreEntity] Entidad de dominio
     */
    private function toEntity([NombreModel]Model $model): [NombreEntity]
    {
        return [NombreEntity]::fromArray($model->toArray());
    }

    public function findById(int $id): ?[NombreEntity]
    {
        try {
            $model = $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $model ? $this->toEntity($model) : null;
    }

    public function findBy[Campo](string $[campo]): ?[NombreEntity]
    {
        try {
            $model = $this->model->where('[campo]', $[campo])->first();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $model ? $this->toEntity($model) : null;
    }

    public function getAll(): array
    {
        return $this->model
            ->all()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    public function save([NombreEntity] $entity): void
    {
        try {
            $this->model->create([
                '[campo1]' => (string) $entity->[campo1],
                '[campo2]' => (string) $entity->[campo2],
                '[campo3]' => $entity->[campo3],
                // ... más campos
            ]);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function update(int $id, [NombreEntity] $entity): void
    {
        try {
            $model = $this->model->findOrFail($id);
            
            $model->update([
                '[campo1]' => (string) $entity->[campo1],
                '[campo2]' => (string) $entity->[campo2],
                '[campo3]' => $entity->[campo3],
                // ... más campos
            ]);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function delete(int $id): void
    {
        try {
            $model = $this->model->findOrFail($id);
            $model->delete();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
```

### 7. Form Request Template

```php
<?php

namespace App\Src\Infrastructure\Requests\Backoffice\[Modulo];

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para crear [NOMBRE]
 */
class Store[Nombre]Request extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta request
     */
    public function authorize(): bool
    {
        return true; // O implementar lógica de autorización
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            '[campo1]' => 'required|string|max:255',
            '[campo2]' => 'required|email|unique:[tabla],[campo2]',
            '[campo3]' => 'required|integer|min:0',
            // ... más reglas
        ];
    }

    /**
     * Convierte la request a un array para el servicio
     */
    public function toArray(): array
    {
        return [
            '[campo1]' => $this->[campo1],
            '[campo2]' => $this->[campo2],
            '[campo3]' => $this->[campo3],
            // ... más campos
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            '[campo1].required' => 'El [campo1] es obligatorio.',
            '[campo1].max' => 'El [campo1] no puede exceder 255 caracteres.',
            '[campo2].required' => 'El [campo2] es obligatorio.',
            '[campo2].email' => 'El [campo2] debe ser un email válido.',
            '[campo2].unique' => 'El [campo2] ya está en uso.',
            '[campo3].required' => 'El [campo3] es obligatorio.',
            '[campo3].min' => 'El [campo3] debe ser mayor o igual a 0.',
            // ... más mensajes
        ];
    }
}
```

### 8. Controller Templates

#### IndexController

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\[Modulo];

use App\Src\Domain\Contracts\ServiceContracts\[NombreService]Interface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

/**
 * Controlador para listar [NOMBRE]
 */
class IndexController extends Controller
{
    public function __invoke([NombreService]Interface $[nombre]Service)
    {
        $[nombre]s = $[nombre]Service->getAll();
        
        return Inertia::render('[Modulo]/Index', [
            '[nombre]s' => $[nombre]s,
        ]);
    }
}
```

#### CreateController

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\[Modulo];

use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

/**
 * Controlador para mostrar formulario de creación
 */
class CreateController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('[Modulo]/Create');
    }
}
```

#### StoreController

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\[Modulo];

use App\Src\Domain\Contracts\ServiceContracts\[NombreService]Interface;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\[Modulo]\Store[Nombre]Request;

/**
 * Controlador para crear [NOMBRE]
 */
class StoreController extends Controller
{
    public function __invoke(
        Store[Nombre]Request $request,
        [NombreService]Interface $[nombre]Service
    ) {
        $[nombre]Service->save($request->toArray());
        
        return redirect()
            ->route('backoffice.[nombre]s.index')
            ->with('success', '[NOMBRE] creado exitosamente.');
    }
}
```

#### EditController

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\[Modulo];

use App\Src\Domain\Contracts\ServiceContracts\[NombreService]Interface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

/**
 * Controlador para mostrar formulario de edición
 */
class EditController extends Controller
{
    public function __invoke(int $id, [NombreService]Interface $[nombre]Service)
    {
        $[nombre] = $[nombre]Service->findById($id);
        
        if (!$[nombre]) {
            abort(404, '[NOMBRE] no encontrado');
        }
        
        return Inertia::render('[Modulo]/Edit', [
            '[nombre]' => $[nombre]->toArray(),
        ]);
    }
}
```

#### UpdateController

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\[Modulo];

use App\Src\Domain\Contracts\ServiceContracts\[NombreService]Interface;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\[Modulo]\Update[Nombre]Request;

/**
 * Controlador para actualizar [NOMBRE]
 */
class UpdateController extends Controller
{
    public function __invoke(
        int $id,
        Update[Nombre]Request $request,
        [NombreService]Interface $[nombre]Service
    ) {
        $[nombre]Service->update($id, $request->toArray());
        
        return redirect()
            ->route('backoffice.[nombre]s.index')
            ->with('success', '[NOMBRE] actualizado exitosamente.');
    }
}
```

#### DestroyController

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\[Modulo];

use App\Src\Domain\Contracts\ServiceContracts\[NombreService]Interface;
use App\Src\Infrastructure\Controllers\Controller;

/**
 * Controlador para eliminar [NOMBRE]
 */
class DestroyController extends Controller
{
    public function __invoke(int $id, [NombreService]Interface $[nombre]Service)
    {
        $[nombre]Service->delete($id);
        
        return redirect()
            ->route('backoffice.[nombre]s.index')
            ->with('success', '[NOMBRE] eliminado exitosamente.');
    }
}
```

### 9. Routes Template

```php
<?php

use App\Src\Infrastructure\Controllers\Backoffice\[Modulo];
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('[nombre]s')
    ->name('[nombre]s.')
    ->group(function () {
        Route::get('/', [Modulo]\IndexController::class)->name('index');
        Route::get('/create', [Modulo]\CreateController::class)->name('create');
        Route::post('/', [Modulo]\StoreController::class)->name('store');
        Route::get('/{[nombre]}/edit', [Modulo]\EditController::class)->name('edit');
        Route::put('/{[nombre]}', [Modulo]\UpdateController::class)->name('update');
        Route::delete('/{[nombre]}', [Modulo]\DestroyController::class)->name('destroy');
    });
```

### 10. Migration Template

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('[tabla]', function (Blueprint $table) {
            $table->id();
            $table->string('[campo1]', 255);
            $table->string('[campo2]', 255)->unique();
            $table->integer('[campo3]')->default(0);
            $table->text('[campo4]')->nullable();
            $table->decimal('[campo5]', 10, 2)->nullable();
            $table->boolean('[campo6]')->default(false);
            $table->timestamp('[campo7]')->nullable();
            
            // Foreign keys
            $table->foreignId('[relacion]_id')
                ->nullable()
                ->constrained('[tabla_relacionada]')
                ->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); // Si se necesita soft delete
            
            // Índices
            $table->index('[campo1]');
            $table->index(['[campo1]', '[campo2]']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('[tabla]');
    }
};
```

---

## 🔧 Comandos Útiles

### Laravel Sail

```bash
# Levantar contenedores
sail up -d

# Detener contenedores
sail down

# Ver logs
sail logs

# Acceder al contenedor
sail shell

# Ejecutar comandos Artisan
sail artisan [comando]

# Ejecutar Composer
sail composer [comando]

# Ejecutar pnpm
sail exec laravel.test pnpm [comando]
```

### Artisan

```bash
# Crear migración
sail artisan make:migration create_[tabla]_table

# Ejecutar migraciones
sail artisan migrate

# Rollback migraciones
sail artisan migrate:rollback

# Refresh migraciones (¡CUIDADO! Borra datos)
sail artisan migrate:fresh --seed

# Crear modelo
sail artisan make:model [Nombre]

# Crear seeder
sail artisan make:seeder [Nombre]Seeder

# Ejecutar seeders
sail artisan db:seed

# Limpiar cachés
sail artisan config:clear
sail artisan route:clear
sail artisan cache:clear
sail artisan view:clear

# Optimizar aplicación
sail artisan optimize

# Generar autoload
sail composer dump-autoload
```

### pnpm

```bash
# Instalar dependencias
sail exec laravel.test pnpm install

# Desarrollo con hot reload
sail exec laravel.test pnpm run dev

# Build de producción
sail exec laravel.test pnpm run build

# Verificación de tipos TypeScript
sail exec laravel.test pnpm run type-check

# Linter
sail exec laravel.test pnpm run lint

# Agregar dependencia
sail exec laravel.test pnpm add [paquete]

# Agregar dependencia de desarrollo
sail exec laravel.test pnpm add -D [paquete]
```

### Git

```bash
# Crear rama para feature
git checkout -b feature/[nombre-feature]

# Commit con mensaje descriptivo
git commit -m "feat: [descripción del feature]"

# Push de rama
git push origin feature/[nombre-feature]

# Merge de rama
git checkout main
git merge feature/[nombre-feature]
```

---

## 📁 Estructura de Archivos

### Estructura Completa de un Feature

```
app/Src/
├── Domain/
│   ├── Contracts/
│   │   ├── RepositoryContracts/
│   │   │   └── [Nombre]RepositoryInterface.php
│   │   └── ServiceContracts/
│   │       └── [Nombre]ServiceInterface.php
│   ├── Entities/
│   │   └── [Nombre]Entity.php
│   ├── ValueObjects/
│   │   ├── [Campo1].php
│   │   └── [Campo2].php
│   └── Exceptions/
│       └── [Nombre]Exception.php
├── Application/
│   └── Services/
│       └── Backoffice/
│           └── [Nombre]Service.php
└── Infrastructure/
    ├── Controllers/
    │   └── Backoffice/
    │       └── [Modulo]/
    │           ├── IndexController.php
    │           ├── CreateController.php
    │           ├── StoreController.php
    │           ├── EditController.php
    │           ├── UpdateController.php
    │           └── DestroyController.php
    ├── Repositories/
    │   └── Eloquent/
    │       └── [Nombre]EloquentRepository.php
    └── Requests/
        └── Backoffice/
            └── [Modulo]/
                ├── Store[Nombre]Request.php
                └── Update[Nombre]Request.php

app/Models/
└── [Nombre].php

database/migrations/
└── [timestamp]_create_[tabla]_table.php

routes/
└── backoffice_[modulo].php

resources/js/
├── Pages/
│   └── [Modulo]/
│       ├── Index.tsx
│       ├── Create.tsx
│       └── Edit.tsx
└── types/
    └── [modulo].d.ts
```

---

## 📝 Convenciones de Nomenclatura

### PHP

#### Clases
- **PascalCase**: `UserEntity`, `ProductService`
- **Sufijos**:
  - Entities: `[Nombre]Entity`
  - Services: `[Nombre]Service`
  - Repositories: `[Nombre]EloquentRepository`
  - Interfaces: `[Nombre]Interface`
  - Controllers: `[Action]Controller`
  - Requests: `[Action][Nombre]Request`

#### Métodos
- **camelCase**: `findById`, `getUserData`
- **Prefijos comunes**:
  - `get`: Obtener datos
  - `find`: Buscar entidad
  - `save`: Persistir nueva entidad
  - `update`: Actualizar entidad existente
  - `delete`: Eliminar entidad
  - `is`: Verificación booleana
  - `has`: Verificación de existencia

#### Variables
- **camelCase**: `$userId`, `$productName`
- **Descriptivas**: Evitar abreviaciones

#### Constantes
- **UPPER_SNAKE_CASE**: `MAX_LENGTH`, `DEFAULT_VALUE`

### Base de Datos

#### Tablas
- **snake_case**: `users`, `product_categories`
- **Plural**: `products`, `orders`

#### Columnas
- **snake_case**: `user_id`, `created_at`
- **Foreign keys**: `[tabla_singular]_id`

#### Índices
- **Formato**: `[tabla]_[columna(s)]_index`
- **Ejemplo**: `users_email_index`

### TypeScript/React

#### Componentes
- **PascalCase**: `UserForm`, `ProductList`

#### Archivos
- **PascalCase** para componentes: `UserForm.tsx`
- **camelCase** para utilidades: `formatDate.ts`

#### Tipos/Interfaces
- **PascalCase**: `User`, `ProductData`
- **Prefijo `I`** (opcional): `IUserProps`

---

## 🎯 Ejemplo Rápido: Crear Feature "Product"

### 1. Crear Value Object

```bash
# Crear: app/Src/Domain/ValueObjects/ProductName.php
```

### 2. Crear Entity

```bash
# Crear: app/Src/Domain/Entities/ProductEntity.php
```

### 3. Crear Interfaces

```bash
# Crear: app/Src/Domain/Contracts/RepositoryContracts/ProductRepositoryInterface.php
# Crear: app/Src/Domain/Contracts/ServiceContracts/ProductServiceInterface.php
```

### 4. Crear Service

```bash
# Crear: app/Src/Application/Services/Backoffice/ProductService.php
```

### 5. Crear Model y Migration

```bash
sail artisan make:model Product -m
```

### 6. Crear Repository

```bash
# Crear: app/Src/Infrastructure/Repositories/Eloquent/ProductEloquentRepository.php
```

### 7. Crear Requests

```bash
# Crear: app/Src/Infrastructure/Requests/Backoffice/Products/StoreProductRequest.php
# Crear: app/Src/Infrastructure/Requests/Backoffice/Products/UpdateProductRequest.php
```

### 8. Crear Controllers

```bash
# Crear directorio: app/Src/Infrastructure/Controllers/Backoffice/Products/
# Crear: IndexController.php, CreateController.php, StoreController.php
# Crear: EditController.php, UpdateController.php, DestroyController.php
```

### 9. Registrar en AppServiceProvider

```php
// app/Providers/AppServiceProvider.php
$this->app->bind(ProductServiceInterface::class, ProductService::class);
$this->app->bind(ProductRepositoryInterface::class, ProductEloquentRepository::class);
```

### 10. Crear Rutas

```bash
# Crear: routes/backoffice_products.php
# Incluir en routes/web.php
```

### 11. Ejecutar Migration

```bash
sail artisan migrate
```

---

**Última actualización**: 2025-12-30
**Versión**: 1.0.0
