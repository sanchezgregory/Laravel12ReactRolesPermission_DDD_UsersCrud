# Guía del Desarrollador - Arquitectura Hexagonal DDD

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Arquitectura del Proyecto](#arquitectura-del-proyecto)
3. [Estructura de Capas](#estructura-de-capas)
4. [Patrones y Conceptos Clave](#patrones-y-conceptos-clave)
5. [Cómo Crear un Nuevo Feature](#cómo-crear-un-nuevo-feature)
6. [Ejemplos Prácticos](#ejemplos-prácticos)
7. [Mejores Prácticas](#mejores-prácticas)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Introducción

Este proyecto implementa **Arquitectura Hexagonal** (también conocida como Ports & Adapters) combinada con **Domain-Driven Design (DDD)**. Esta arquitectura permite:

- ✅ **Independencia del framework**: La lógica de negocio no depende de Laravel
- ✅ **Testabilidad**: Fácil de testear cada capa por separado
- ✅ **Mantenibilidad**: Código organizado y fácil de mantener
- ✅ **Escalabilidad**: Fácil de extender con nuevas funcionalidades

### Stack Tecnológico

- **Backend**: Laravel 12 + PHP 8.4
- **Frontend**: React 18 + TypeScript + InertiaJS
- **Base de Datos**: PostgreSQL 17
- **ORM**: Eloquent (mapeado a Entities)
- **Autenticación**: Laravel Sanctum + Spatie Permissions

---

## 🏗️ Arquitectura del Proyecto

### Diagrama de Capas

```
┌─────────────────────────────────────────────────────┐
│           INFRASTRUCTURE LAYER                       │
│  (Controllers, Requests, Eloquent Repositories)     │
│                                                      │
│  ┌────────────────────────────────────────────┐    │
│  │      APPLICATION LAYER                      │    │
│  │  (Services, DTOs, Use Cases)               │    │
│  │                                             │    │
│  │  ┌──────────────────────────────────────┐ │    │
│  │  │      DOMAIN LAYER                     │ │    │
│  │  │  (Entities, Value Objects,           │ │    │
│  │  │   Interfaces, Business Logic)        │ │    │
│  │  └──────────────────────────────────────┘ │    │
│  └────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
```

### Principios SOLID Aplicados

1. **Single Responsibility**: Cada clase tiene una única responsabilidad
2. **Open/Closed**: Abierto para extensión, cerrado para modificación
3. **Liskov Substitution**: Las interfaces pueden ser sustituidas por sus implementaciones
4. **Interface Segregation**: Interfaces específicas en lugar de generales
5. **Dependency Inversion**: Dependemos de abstracciones, no de implementaciones concretas

---

## 📁 Estructura de Capas

### 1. Domain Layer (`app/Src/Domain/`)

**Responsabilidad**: Contiene la lógica de negocio pura, sin dependencias externas.

```
Domain/
├── Contracts/
│   ├── RepositoryContracts/    # Interfaces de repositorios
│   │   ├── BaseEntityRepository.php
│   │   ├── UserRepositoryInterface.php
│   │   └── RoleRepositoryInterface.php
│   └── ServiceContracts/       # Interfaces de servicios
│       └── UserServiceInterface.php
├── Entities/                   # Entidades del dominio
│   ├── BaseEntity.php
│   └── UserEntity.php
├── ValueObjects/               # Objetos de valor inmutables
│   ├── Concerns/
│   │   └── StringValueObject.php
│   ├── Email.php
│   ├── Password.php
│   ├── PersonName.php
│   ├── Username.php
│   ├── CityName.php
│   ├── StreetName.php
│   └── PostalCode.php
├── Exceptions/                 # Excepciones del dominio
│   ├── DomainException.php
│   ├── UserFacingException.php
│   └── SystemException.php
└── Traits/
    └── LogExceptionTrait.php
```

#### 1.1 Entities (Entidades)

Las entidades representan conceptos del dominio con identidad única.

**Características**:
- Tienen un ID único
- Contienen lógica de negocio
- Son independientes del framework
- Usan Value Objects para validación

**Ejemplo**: `UserEntity.php`

```php
<?php

namespace App\Src\Domain\Entities;

use App\Src\Domain\ValueObjects\Email;
use App\Src\Domain\ValueObjects\Password;
use App\Src\Domain\ValueObjects\PersonName;

class UserEntity extends BaseEntity
{
    public function __construct(
        public ?int $id,
        public PersonName $name,
        public ?Email $email,
        public ?Password $password,
        public array $roles = []
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): static
    {
        $name = PersonName::fromString($data['name']);
        $email = Email::fromString($data['email']);
        $password = isset($data['password']) 
            ? Password::fromString($data['password']) 
            : null;
        $roles = $data['roles'] ?? [];

        return new static(
            $data['id'] ?? null,
            $name,
            $email,
            $password,
            $roles
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
```

#### 1.2 Value Objects (Objetos de Valor)

Los Value Objects son objetos inmutables que representan valores sin identidad.

**Características**:
- Son inmutables (`readonly`)
- Contienen validación de negocio
- Se comparan por valor, no por identidad
- Usan el trait `StringValueObject`

**Ejemplo**: `Email.php`

```php
<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;
use JsonSerializable;

final readonly class Email implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        if (empty($value)) {
            throw new InvalidArgumentException('El correo electrónico no puede estar vacío.');
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El correo electrónico no es válido.');
        }
    }
}
```

**Trait StringValueObject**:

```php
<?php

namespace App\Src\Domain\ValueObjects\Concerns;

trait StringValueObject
{
    public readonly string $value;

    private function __construct(string $value)
    {
        static::validate($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    abstract public static function validate(string &$value): void;
}
```

#### 1.3 Contracts (Interfaces)

Definen los contratos que deben implementar las capas externas.

**Repository Interface**:

```php
<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\UserEntity;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity;
    public function findByEmail(string $email): ?UserEntity;
    public function save(UserEntity $user): void;
    public function update(int $userId, UserEntity $userEntity): void;
    public function delete(int $userId): void;
    public function getAll(): array;
    public function getUserProfileData(int $userId): array;
}
```

**Service Interface**:

```php
<?php

namespace App\Src\Domain\Contracts\ServiceContracts;

use App\Src\Domain\Entities\UserEntity;

interface UserServiceInterface
{
    public function findById(int $id): ?UserEntity;
    public function findByEmail(string $email): ?UserEntity;
    public function save(array $user): void;
    public function update(int $userId, array $userEntity): void;
    public function delete(int $userId): void;
    public function getAll(): array;
    public function getUserProfileData(int $userId): array;
}
```

---

### 2. Application Layer (`app/Src/Application/`)

**Responsabilidad**: Orquesta los casos de uso y coordina las operaciones del dominio.

```
Application/
├── DTOs/                       # Data Transfer Objects
├── Events/                     # Eventos de aplicación
├── Listeners/                  # Listeners de eventos
└── Services/
    └── Backoffice/
        ├── CachingServices/    # Servicios de caché (Decorator Pattern)
        │   ├── AppCacheKeys.php
        │   ├── BaseCacheService.php
        │   └── UserCachingService.php
        └── UserService.php     # Servicio de aplicación
```

#### 2.1 Application Services

Los servicios de aplicación coordinan las operaciones del dominio.

**Características**:
- Implementan interfaces del dominio
- Coordinan repositorios y entidades
- No contienen lógica de negocio (eso va en el dominio)
- Transforman datos entre capas

**Ejemplo**: `UserService.php`

```php
<?php

namespace App\Src\Application\Services\Backoffice;

use App\Src\Domain\Contracts\RepositoryContracts\UserRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Domain\Entities\UserEntity;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function getUserProfileData(int $userId): array
    {
        return $this->userRepository->getUserProfileData($userId);
    }

    public function update(int $userId, array $userEntity): void
    {
        $userEntity = UserEntity::fromArray($userEntity);
        $this->userRepository->update($userId, $userEntity);
    }

    public function findById(int $id): ?UserEntity
    {
        return $this->userRepository->findById($id);
    }

    public function findByEmail(string $email): ?UserEntity
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    public function save(array $user): void
    {
        $userEntity = UserEntity::fromArray($user);
        $this->userRepository->save($userEntity);
    }

    public function delete(int $userId): void
    {
        $this->userRepository->delete($userId);
    }
}
```

#### 2.2 Caching Services (Decorator Pattern)

Implementan el patrón Decorator para agregar caché a los servicios.

**Ejemplo**: `UserCachingService.php`

```php
<?php

namespace App\Src\Application\Services\Backoffice\CachingServices;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Domain\Entities\UserEntity;

class UserCachingService implements UserServiceInterface
{
    public function __construct(
        private readonly UserServiceInterface $decoratedService,
        private readonly BaseCacheService $cacheService
    ) {}

    public function findById(int $id): ?UserEntity
    {
        $cacheKey = AppCacheKeys::USER_BY_ID . $id;
        
        return $this->cacheService->remember(
            $cacheKey,
            fn() => $this->decoratedService->findById($id)
        );
    }

    public function save(array $user): void
    {
        $this->decoratedService->save($user);
        $this->cacheService->forget(AppCacheKeys::ALL_USERS);
    }

    // ... otros métodos
}
```

---

### 3. Infrastructure Layer (`app/Src/Infrastructure/`)

**Responsabilidad**: Implementa los adaptadores para servicios externos (BD, HTTP, etc.)

```
Infrastructure/
├── Controllers/
│   ├── Api/
│   │   └── AuthController.php
│   ├── Backoffice/
│   │   ├── Auth/               # Controladores de autenticación
│   │   ├── Users/              # Controladores de usuarios
│   │   │   ├── IndexController.php
│   │   │   ├── CreateController.php
│   │   │   ├── StoreController.php
│   │   │   ├── EditController.php
│   │   │   ├── UpdateController.php
│   │   │   └── DestroyController.php
│   │   └── Settings/           # Controladores de configuración
│   └── Controller.php
├── Exceptions/
│   ├── ExceptionTransformer.php
│   └── RepositoryException.php
├── Handlers/
│   └── CustomExceptionHandler.php
├── Middleware/                 # Middleware personalizado
├── Repositories/
│   └── Eloquent/
│       ├── UserEloquentRepository.php
│       └── RoleEloquentRepository.php
├── Requests/                   # Form Requests
│   └── Backoffice/
│       └── Users/
│           ├── StoreUserRequest.php
│           └── UpdateUserRequest.php
└── Services/                   # Servicios de infraestructura
```

#### 3.1 Controllers (Invocables)

Los controladores son **invocables** (single action controllers).

**Características**:
- Un controlador = una acción
- Reciben Request y Service por inyección de dependencias
- Delegan la lógica al servicio de aplicación
- Retornan respuestas HTTP o Inertia

**Ejemplo**: `StoreController.php`

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Users\StoreUserRequest;

class StoreController extends Controller
{
    public function __invoke(
        StoreUserRequest $request, 
        UserServiceInterface $userService
    ) {
        $userService->save($request->toArray());
        
        return redirect()
            ->route('backoffice.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }
}
```

#### 3.2 Form Requests

Validan y transforman los datos de entrada.

**Características**:
- Contienen reglas de validación
- Transforman datos con `toArray()`
- Mensajes de error personalizados

**Ejemplo**: `StoreUserRequest.php`

```php
<?php

namespace App\Src\Infrastructure\Requests\Backoffice\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'password_confirmation' => 'required|min:8|same:password',
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
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'El correo electrónico ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'roles.required' => 'El rol es obligatorio.',
        ];
    }
}
```

#### 3.3 Eloquent Repositories

Implementan las interfaces del dominio usando Eloquent.

**Características**:
- Mapean modelos Eloquent a Entidades del dominio
- Implementan interfaces del dominio
- Manejan excepciones de infraestructura

**Ejemplo**: `UserEloquentRepository.php`

```php
<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Models\User as UserModel;
use App\Src\Domain\Contracts\RepositoryContracts\UserRepositoryInterface;
use App\Src\Domain\Entities\UserEntity;
use App\Src\Infrastructure\Exceptions\RepositoryException;

class UserEloquentRepository implements UserRepositoryInterface
{
    public function __construct(private UserModel $model) {}

    /**
     * Mapea un Modelo Eloquent a una Entidad de Dominio
     */
    private function toEntity(UserModel $userModel): UserEntity
    {
        return UserEntity::fromArray($userModel->toArray());
    }

    public function findById(int $id): ?UserEntity
    {
        try {
            $userModel = $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $userModel ? $this->toEntity($userModel) : null;
    }

    public function findByEmail(string $email): ?UserEntity
    {
        try {
            $userModel = $this->model->where('email', $email)->first();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $userModel ? $this->toEntity($userModel) : null;
    }

    public function getAll(): array
    {
        return $this->model
            ->all()
            ->map(fn($userModel) => $this->toEntity($userModel))
            ->toArray();
    }

    public function save(UserEntity $userEntity): void
    {
        try {
            $userModel = $this->model->create([
                'name' => $userEntity->name,
                'email' => $userEntity->email,
                'password' => bcrypt($userEntity->password),
            ]);

            $userModel->syncRoles($userEntity->roles);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function update(int $userId, UserEntity $userEntity): void
    {
        try {
            $userModel = $this->model->find($userId);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        if (!empty($userEntity->name)) {
            $userModel->name = $userEntity->name;
        }
        if (!empty($userEntity->email)) {
            $userModel->email = $userEntity->email;
        }
        if (!empty($userEntity->password)) {
            $userModel->password = bcrypt($userEntity->password);
        }
        if (!empty($userEntity->roles)) {
            $userModel->syncRoles($userEntity->roles);
        }

        $userModel->save();
    }

    public function delete(int $id): void
    {
        try {
            $userModel = $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        $userModel->delete();
    }

    public function getUserProfileData(int $userId): array
    {
        return $this->model->find($userId)->toArray();
    }
}
```

---

## 🔧 Patrones y Conceptos Clave

### 1. Dependency Injection (Inyección de Dependencias)

Todas las dependencias se inyectan a través del constructor.

**Registro en `AppServiceProvider.php`**:

```php
<?php

namespace App\Providers;

use App\Src\Application\Services\Backoffice\UserService;
use App\Src\Domain\Contracts\RepositoryContracts\UserRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Repositories\Eloquent\UserEloquentRepository;

class AppServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // Decorator Pattern para Caching
        $this->decorate(
            UserServiceInterface::class, 
            UserService::class, 
            UserCachingService::class
        );

        // Repository Binding
        $this->app->bind(
            UserRepositoryInterface::class, 
            UserEloquentRepository::class
        );
    }
}
```

### 2. Decorator Pattern (Patrón Decorador)

Usado para agregar funcionalidad (como caché) sin modificar el código existente.

**BaseServiceProvider**:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    protected function decorate(
        string $interface, 
        string $implementation, 
        string $decorator
    ): void {
        // 1. Registra la implementación base
        $this->app->bind($interface, $implementation);

        // 2. Envuelve con el decorador
        $this->app->extend($interface, function ($originalService, $app) use ($decorator) {
            return $app->make($decorator, ['decoratedService' => $originalService]);
        });
    }
}
```

### 3. Repository Pattern

Abstrae el acceso a datos, permitiendo cambiar la implementación sin afectar el dominio.

**Flujo**:
```
Controller → Service → Repository Interface → Eloquent Repository → Eloquent Model
                                    ↓
                              Domain Entity
```

### 4. Value Objects Pattern

Encapsulan validación y comportamiento de valores primitivos.

**Beneficios**:
- Validación centralizada
- Inmutabilidad
- Type safety
- Reutilización

---

## 🚀 Cómo Crear un Nuevo Feature

### Paso 1: Definir el Dominio

#### 1.1 Crear Value Objects (si es necesario)

```bash
# Crear archivo: app/Src/Domain/ValueObjects/ProductName.php
```

```php
<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;
use JsonSerializable;

final readonly class ProductName implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        
        if (empty($value)) {
            throw new InvalidArgumentException('El nombre del producto no puede estar vacío.');
        }
        
        if (strlen($value) < 3) {
            throw new InvalidArgumentException('El nombre debe tener al menos 3 caracteres.');
        }
        
        if (strlen($value) > 100) {
            throw new InvalidArgumentException('El nombre no puede exceder 100 caracteres.');
        }
    }
}
```

#### 1.2 Crear Entity

```bash
# Crear archivo: app/Src/Domain/Entities/ProductEntity.php
```

```php
<?php

namespace App\Src\Domain\Entities;

use App\Src\Domain\ValueObjects\ProductName;

class ProductEntity extends BaseEntity
{
    public function __construct(
        public ?int $id,
        public ProductName $name,
        public string $description,
        public float $price,
        public int $stock
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): static
    {
        return new static(
            $data['id'] ?? null,
            ProductName::fromString($data['name']),
            $data['description'] ?? '',
            $data['price'] ?? 0.0,
            $data['stock'] ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => (string) $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    // Métodos de negocio
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function decreaseStock(int $quantity): void
    {
        if ($quantity > $this->stock) {
            throw new \DomainException('Stock insuficiente');
        }
        
        $this->stock -= $quantity;
    }
}
```

#### 1.3 Crear Repository Interface

```bash
# Crear archivo: app/Src/Domain/Contracts/RepositoryContracts/ProductRepositoryInterface.php
```

```php
<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\ProductEntity;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?ProductEntity;
    public function findByName(string $name): ?ProductEntity;
    public function getAll(): array;
    public function getAvailable(): array;
    public function save(ProductEntity $product): void;
    public function update(int $productId, ProductEntity $product): void;
    public function delete(int $productId): void;
}
```

#### 1.4 Crear Service Interface

```bash
# Crear archivo: app/Src/Domain/Contracts/ServiceContracts/ProductServiceInterface.php
```

```php
<?php

namespace App\Src\Domain\Contracts\ServiceContracts;

use App\Src\Domain\Entities\ProductEntity;

interface ProductServiceInterface
{
    public function findById(int $id): ?ProductEntity;
    public function getAll(): array;
    public function getAvailable(): array;
    public function save(array $product): void;
    public function update(int $productId, array $product): void;
    public function delete(int $productId): void;
}
```

---

### Paso 2: Implementar la Capa de Aplicación

#### 2.1 Crear Application Service

```bash
# Crear archivo: app/Src/Application/Services/Backoffice/ProductService.php
```

```php
<?php

namespace App\Src\Application\Services\Backoffice;

use App\Src\Domain\Contracts\RepositoryContracts\ProductRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\ProductServiceInterface;
use App\Src\Domain\Entities\ProductEntity;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function findById(int $id): ?ProductEntity
    {
        return $this->productRepository->findById($id);
    }

    public function getAll(): array
    {
        return $this->productRepository->getAll();
    }

    public function getAvailable(): array
    {
        return $this->productRepository->getAvailable();
    }

    public function save(array $product): void
    {
        $productEntity = ProductEntity::fromArray($product);
        $this->productRepository->save($productEntity);
    }

    public function update(int $productId, array $product): void
    {
        $productEntity = ProductEntity::fromArray($product);
        $this->productRepository->update($productId, $productEntity);
    }

    public function delete(int $productId): void
    {
        $this->productRepository->delete($productId);
    }
}
```

---

### Paso 3: Implementar la Capa de Infraestructura

#### 3.1 Crear Eloquent Model

```bash
sail artisan make:model Product -m
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];
}
```

#### 3.2 Crear Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
            
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

#### 3.3 Crear Eloquent Repository

```bash
# Crear archivo: app/Src/Infrastructure/Repositories/Eloquent/ProductEloquentRepository.php
```

```php
<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Models\Product as ProductModel;
use App\Src\Domain\Contracts\RepositoryContracts\ProductRepositoryInterface;
use App\Src\Domain\Entities\ProductEntity;
use App\Src\Infrastructure\Exceptions\RepositoryException;

class ProductEloquentRepository implements ProductRepositoryInterface
{
    public function __construct(private ProductModel $model) {}

    private function toEntity(ProductModel $productModel): ProductEntity
    {
        return ProductEntity::fromArray($productModel->toArray());
    }

    public function findById(int $id): ?ProductEntity
    {
        try {
            $productModel = $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $productModel ? $this->toEntity($productModel) : null;
    }

    public function findByName(string $name): ?ProductEntity
    {
        try {
            $productModel = $this->model->where('name', $name)->first();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $productModel ? $this->toEntity($productModel) : null;
    }

    public function getAll(): array
    {
        return $this->model
            ->all()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    public function getAvailable(): array
    {
        return $this->model
            ->where('stock', '>', 0)
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    public function save(ProductEntity $product): void
    {
        try {
            $this->model->create([
                'name' => (string) $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
            ]);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function update(int $productId, ProductEntity $product): void
    {
        try {
            $productModel = $this->model->findOrFail($productId);
            
            $productModel->update([
                'name' => (string) $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
            ]);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function delete(int $productId): void
    {
        try {
            $productModel = $this->model->findOrFail($productId);
            $productModel->delete();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
```

#### 3.4 Crear Form Requests

**StoreProductRequest.php**:

```php
<?php

namespace App\Src\Infrastructure\Requests\Backoffice\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100|unique:products,name',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.unique' => 'Ya existe un producto con ese nombre.',
            'price.required' => 'El precio es obligatorio.',
            'price.min' => 'El precio no puede ser negativo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.min' => 'El stock no puede ser negativo.',
        ];
    }
}
```

**UpdateProductRequest.php**:

```php
<?php

namespace App\Src\Infrastructure\Requests\Backoffice\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                Rule::unique('products', 'name')->ignore($productId)
            ],
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }
}
```

#### 3.5 Crear Controllers (Invocables)

**IndexController.php**:

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Products;

use App\Src\Domain\Contracts\ServiceContracts\ProductServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function __invoke(ProductServiceInterface $productService)
    {
        $products = $productService->getAll();
        
        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }
}
```

**CreateController.php**:

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Products;

use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class CreateController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Products/Create');
    }
}
```

**StoreController.php**:

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Products;

use App\Src\Domain\Contracts\ServiceContracts\ProductServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Products\StoreProductRequest;

class StoreController extends Controller
{
    public function __invoke(
        StoreProductRequest $request,
        ProductServiceInterface $productService
    ) {
        $productService->save($request->toArray());
        
        return redirect()
            ->route('backoffice.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }
}
```

**EditController.php**:

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Products;

use App\Src\Domain\Contracts\ServiceContracts\ProductServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class EditController extends Controller
{
    public function __invoke(int $id, ProductServiceInterface $productService)
    {
        $product = $productService->findById($id);
        
        if (!$product) {
            abort(404, 'Producto no encontrado');
        }
        
        return Inertia::render('Products/Edit', [
            'product' => $product->toArray(),
        ]);
    }
}
```

**UpdateController.php**:

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Products;

use App\Src\Domain\Contracts\ServiceContracts\ProductServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Products\UpdateProductRequest;

class UpdateController extends Controller
{
    public function __invoke(
        int $id,
        UpdateProductRequest $request,
        ProductServiceInterface $productService
    ) {
        $productService->update($id, $request->toArray());
        
        return redirect()
            ->route('backoffice.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }
}
```

**DestroyController.php**:

```php
<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Products;

use App\Src\Domain\Contracts\ServiceContracts\ProductServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;

class DestroyController extends Controller
{
    public function __invoke(int $id, ProductServiceInterface $productService)
    {
        $productService->delete($id);
        
        return redirect()
            ->route('backoffice.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
```

---

### Paso 4: Registrar Dependencias

Editar `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Src\Application\Services\Backoffice\ProductService;
use App\Src\Domain\Contracts\RepositoryContracts\ProductRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\ProductServiceInterface;
use App\Src\Infrastructure\Repositories\Eloquent\ProductEloquentRepository;

class AppServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // ... registros existentes ...

        // Product Service
        $this->app->bind(
            ProductServiceInterface::class, 
            ProductService::class
        );

        // Product Repository
        $this->app->bind(
            ProductRepositoryInterface::class, 
            ProductEloquentRepository::class
        );
    }
}
```

---

### Paso 5: Definir Rutas

Crear archivo `routes/backoffice_products.php`:

```php
<?php

use App\Src\Infrastructure\Controllers\Backoffice\Products;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('products')->name('products.')->group(function () {
    Route::get('/', Products\IndexController::class)->name('index');
    Route::get('/create', Products\CreateController::class)->name('create');
    Route::post('/', Products\StoreController::class)->name('store');
    Route::get('/{product}/edit', Products\EditController::class)->name('edit');
    Route::put('/{product}', Products\UpdateController::class)->name('update');
    Route::delete('/{product}', Products\DestroyController::class)->name('destroy');
});
```

Incluir en `routes/web.php`:

```php
Route::middleware(['auth', 'verified'])->prefix('backoffice')->name('backoffice.')->group(function () {
    // ... otras rutas ...
    
    require __DIR__ . '/backoffice_products.php';
});
```

---

### Paso 6: Ejecutar Migraciones

```bash
sail artisan migrate
```

---

## 📚 Ejemplos Prácticos

### Ejemplo 1: Agregar Validación de Negocio

**Escenario**: Validar que el precio no sea mayor a $10,000

**Solución**: Agregar validación en el Value Object o Entity

```php
// En ProductEntity.php

public static function fromArray(array $data): static
{
    $price = $data['price'] ?? 0.0;
    
    if ($price > 10000) {
        throw new \DomainException('El precio no puede exceder $10,000');
    }
    
    return new static(
        $data['id'] ?? null,
        ProductName::fromString($data['name']),
        $data['description'] ?? '',
        $price,
        $data['stock'] ?? 0
    );
}
```

### Ejemplo 2: Agregar Método de Negocio

**Escenario**: Aplicar descuento a un producto

```php
// En ProductEntity.php

public function applyDiscount(float $percentage): void
{
    if ($percentage < 0 || $percentage > 100) {
        throw new \InvalidArgumentException('El descuento debe estar entre 0 y 100');
    }
    
    $this->price = $this->price * (1 - ($percentage / 100));
}
```

### Ejemplo 3: Agregar Filtros en Repository

**Escenario**: Buscar productos por rango de precio

```php
// En ProductRepositoryInterface.php
public function findByPriceRange(float $min, float $max): array;

// En ProductEloquentRepository.php
public function findByPriceRange(float $min, float $max): array
{
    return $this->model
        ->whereBetween('price', [$min, $max])
        ->get()
        ->map(fn($model) => $this->toEntity($model))
        ->toArray();
}

// En ProductServiceInterface.php
public function findByPriceRange(float $min, float $max): array;

// En ProductService.php
public function findByPriceRange(float $min, float $max): array
{
    return $this->productRepository->findByPriceRange($min, $max);
}
```

---

## ✅ Mejores Prácticas

### 1. Naming Conventions

#### Controllers
- **Patrón**: `{Action}Controller.php`
- **Ejemplos**: `IndexController`, `StoreController`, `UpdateController`

#### Entities
- **Patrón**: `{Name}Entity.php`
- **Ejemplos**: `UserEntity`, `ProductEntity`, `OrderEntity`

#### Value Objects
- **Patrón**: `{Name}.php` (sin sufijo)
- **Ejemplos**: `Email`, `PersonName`, `ProductName`

#### Repositories
- **Interface**: `{Name}RepositoryInterface.php`
- **Implementation**: `{Name}EloquentRepository.php`

#### Services
- **Interface**: `{Name}ServiceInterface.php`
- **Implementation**: `{Name}Service.php`

### 2. Validación

#### Validación de Infraestructura (Form Requests)
- Reglas de Laravel (required, email, unique, etc.)
- Validaciones de formato
- Validaciones de base de datos

#### Validación de Dominio (Value Objects y Entities)
- Reglas de negocio
- Invariantes del dominio
- Validaciones complejas

**Ejemplo**:

```php
// Form Request (Infraestructura)
public function rules(): array
{
    return [
        'email' => 'required|email|unique:users,email',
        'age' => 'required|integer|min:0|max:150',
    ];
}

// Value Object (Dominio)
public static function validate(string &$value): void
{
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Email inválido');
    }
    
    // Regla de negocio: solo emails corporativos
    if (!str_ends_with($value, '@company.com')) {
        throw new DomainException('Solo se permiten emails corporativos');
    }
}
```

### 3. Manejo de Errores

#### Excepciones del Dominio
```php
// app/Src/Domain/Exceptions/ProductNotFoundException.php
namespace App\Src\Domain\Exceptions;

class ProductNotFoundException extends DomainException
{
    protected bool $isUserFacing = true;
    
    public function __construct(int $productId)
    {
        parent::__construct(
            "Producto con ID {$productId} no encontrado",
            404,
            null
        );
    }
}
```

#### Uso en Repository
```php
public function findById(int $id): ?ProductEntity
{
    try {
        $productModel = $this->model->find($id);
        
        if (!$productModel) {
            throw new ProductNotFoundException($id);
        }
        
        return $this->toEntity($productModel);
    } catch (\Exception $e) {
        throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
    }
}
```

### 4. Testing

#### Test de Value Objects
```php
<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_creates_valid_email()
    {
        $email = Email::fromString('test@example.com');
        
        $this->assertEquals('test@example.com', (string) $email);
    }
    
    public function test_throws_exception_for_invalid_email()
    {
        $this->expectException(InvalidArgumentException::class);
        
        Email::fromString('invalid-email');
    }
    
    public function test_throws_exception_for_empty_email()
    {
        $this->expectException(InvalidArgumentException::class);
        
        Email::fromString('');
    }
}
```

#### Test de Entities
```php
<?php

namespace Tests\Unit\Domain\Entities;

use App\Src\Domain\Entities\ProductEntity;
use App\Src\Domain\ValueObjects\ProductName;
use PHPUnit\Framework\TestCase;

class ProductEntityTest extends TestCase
{
    public function test_creates_product_from_array()
    {
        $data = [
            'id' => 1,
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
        ];
        
        $product = ProductEntity::fromArray($data);
        
        $this->assertEquals(1, $product->id);
        $this->assertEquals('Test Product', (string) $product->name);
        $this->assertEquals(99.99, $product->price);
    }
    
    public function test_product_is_available_when_stock_is_positive()
    {
        $product = ProductEntity::fromArray([
            'name' => 'Test',
            'price' => 10,
            'stock' => 5,
        ]);
        
        $this->assertTrue($product->isAvailable());
    }
    
    public function test_decrease_stock_throws_exception_when_insufficient()
    {
        $product = ProductEntity::fromArray([
            'name' => 'Test',
            'price' => 10,
            'stock' => 5,
        ]);
        
        $this->expectException(\DomainException::class);
        
        $product->decreaseStock(10);
    }
}
```

#### Test de Services
```php
<?php

namespace Tests\Unit\Application\Services;

use App\Src\Application\Services\Backoffice\ProductService;
use App\Src\Domain\Contracts\RepositoryContracts\ProductRepositoryInterface;
use App\Src\Domain\Entities\ProductEntity;
use PHPUnit\Framework\TestCase;
use Mockery;

class ProductServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }
    
    public function test_save_calls_repository()
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $service = new ProductService($repository);
        
        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ];
        
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(ProductEntity::class));
        
        $service->save($data);
    }
}
```

### 5. Documentación de Código

```php
<?php

namespace App\Src\Domain\Entities;

/**
 * Entidad que representa un producto en el sistema.
 * 
 * Esta entidad encapsula toda la lógica de negocio relacionada
 * con los productos, incluyendo validaciones y operaciones.
 * 
 * @package App\Src\Domain\Entities
 */
class ProductEntity extends BaseEntity
{
    /**
     * Constructor de la entidad Product.
     * 
     * @param int|null $id ID del producto (null para nuevos productos)
     * @param ProductName $name Nombre del producto (Value Object)
     * @param string $description Descripción del producto
     * @param float $price Precio del producto
     * @param int $stock Cantidad en stock
     */
    public function __construct(
        public ?int $id,
        public ProductName $name,
        public string $description,
        public float $price,
        public int $stock
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }
    
    /**
     * Verifica si el producto está disponible para la venta.
     * 
     * Un producto está disponible si tiene stock mayor a 0.
     * 
     * @return bool True si está disponible, false en caso contrario
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }
    
    /**
     * Disminuye el stock del producto.
     * 
     * @param int $quantity Cantidad a disminuir
     * @throws \DomainException Si la cantidad excede el stock disponible
     * @return void
     */
    public function decreaseStock(int $quantity): void
    {
        if ($quantity > $this->stock) {
            throw new \DomainException('Stock insuficiente');
        }
        
        $this->stock -= $quantity;
    }
}
```

---

## 🐛 Troubleshooting

### Error: "Class not found"

**Causa**: Autoload no actualizado

**Solución**:
```bash
sail composer dump-autoload
```

### Error: "Target interface is not instantiable"

**Causa**: Interfaz no registrada en el Service Provider

**Solución**: Verificar `AppServiceProvider.php`
```php
$this->app->bind(YourInterface::class, YourImplementation::class);
```

### Error: "Too few arguments to function"

**Causa**: Dependencias no inyectadas correctamente

**Solución**: Verificar constructor y Service Provider

### Error: Value Object validation fails

**Causa**: Datos no cumplen reglas de negocio

**Solución**: Revisar método `validate()` del Value Object

### Error: "Call to a member function on null"

**Causa**: Entity no encontrada en repositorio

**Solución**: Agregar validación en controller
```php
$product = $productService->findById($id);

if (!$product) {
    abort(404, 'Producto no encontrado');
}
```

---

## 📖 Recursos Adicionales

### Lecturas Recomendadas

1. **Domain-Driven Design** - Eric Evans
2. **Implementing Domain-Driven Design** - Vaughn Vernon
3. **Clean Architecture** - Robert C. Martin
4. **Patterns of Enterprise Application Architecture** - Martin Fowler

### Artículos

- [Hexagonal Architecture](https://alistair.cockburn.us/hexagonal-architecture/)
- [DDD in PHP](https://www.php-fig.org/psr/)
- [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html)

---

## 📝 Checklist para Nuevos Features

- [ ] Crear Value Objects necesarios
- [ ] Crear Entity con lógica de negocio
- [ ] Definir Repository Interface
- [ ] Definir Service Interface
- [ ] Implementar Application Service
- [ ] Crear Eloquent Model y Migration
- [ ] Implementar Eloquent Repository
- [ ] Crear Form Requests (Store y Update)
- [ ] Crear Controllers invocables (Index, Create, Store, Edit, Update, Destroy)
- [ ] Registrar bindings en AppServiceProvider
- [ ] Definir rutas
- [ ] Ejecutar migraciones
- [ ] Crear tests unitarios
- [ ] Crear componentes React (si aplica)
- [ ] Documentar el feature

---

## 🎓 Conclusión

Esta arquitectura hexagonal con DDD proporciona:

✅ **Separación de responsabilidades** clara entre capas
✅ **Testabilidad** mejorada con interfaces y DI
✅ **Mantenibilidad** a largo plazo
✅ **Escalabilidad** para crecer el proyecto
✅ **Independencia** del framework y tecnologías externas

Siguiendo esta guía, podrás crear features consistentes y mantenibles que se alinean con los principios de arquitectura limpia y DDD.

---

**Última actualización**: 2025-12-30
**Versión**: 1.0.0
