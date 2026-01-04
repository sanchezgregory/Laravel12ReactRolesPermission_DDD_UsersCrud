# 📚 DOCUMENTACIÓN COMPLETA - Arquitectura Hexagonal DDD

> **Documento Maestro Consolidado**  
> Toda la documentación del proyecto en un solo archivo  
> Última actualización: 2025-12-30

---

## 📖 ÍNDICE MAESTRO

### PARTE 1: GUÍA DEL DESARROLLADOR
1. [Introducción](#parte-1-guía-del-desarrollador)
2. [Arquitectura del Proyecto](#arquitectura-del-proyecto)
3. [Estructura de Capas](#estructura-de-capas)
   - Domain Layer
   - Application Layer
   - Infrastructure Layer
4. [Patrones y Conceptos Clave](#patrones-y-conceptos-clave)
5. [Cómo Crear un Nuevo Feature](#cómo-crear-un-nuevo-feature)
6. [Ejemplos Prácticos](#ejemplos-prácticos)
7. [Mejores Prácticas](#mejores-prácticas)
8. [Troubleshooting](#troubleshooting)

### PARTE 2: FLUJOS DE ARQUITECTURA
1. [Flujo de Request Completo](#parte-2-flujos-de-arquitectura)
2. [Flujo de Creación de Entidad](#flujo-de-creación-de-entidad)
3. [Flujo de Validación](#flujo-de-validación)
4. [Flujo de Dependency Injection](#flujo-de-dependency-injection)
5. [Flujo de Caché (Decorator Pattern)](#flujo-de-caché-decorator-pattern)
6. [Flujo de Manejo de Errores](#flujo-de-manejo-de-errores)

### PARTE 3: REFERENCIA RÁPIDA
1. [Checklist Completo para Nuevo Feature](#parte-3-referencia-rápida)
2. [Plantillas de Código](#plantillas-de-código)
3. [Comandos Útiles](#comandos-útiles)
4. [Estructura de Archivos](#estructura-de-archivos)
5. [Convenciones de Nomenclatura](#convenciones-de-nomenclatura)

### PARTE 4: ARQUITECTURA VISUAL
1. [Diagrama de Capas Hexagonales](#parte-4-arquitectura-visual)
2. [Flujo de Datos Request → Response](#flujo-de-datos-request--response)
3. [Mapa de Dependencias](#mapa-de-dependencias)
4. [Estructura de Directorios Visualizada](#estructura-de-directorios-visualizada)
5. [Principios SOLID Visualizados](#principios-solid-visualizados)

---

## 🎯 CÓMO USAR ESTE DOCUMENTO

### Por Nivel de Experiencia

**🌱 Principiante (Nuevo en el proyecto)**
1. Lee PARTE 1: Secciones 1-3 (Introducción y Capas)
2. Revisa PARTE 4: Diagramas visuales
3. Practica con PARTE 3: Plantillas de código

**🌿 Intermedio (Vas a crear un feature)**
1. Usa PARTE 3: Checklist completo
2. Consulta PARTE 1: Sección 5 (Crear Feature)
3. Verifica con PARTE 2: Flujos

**🌳 Avanzado (Arquitectura y patrones)**
1. Estudia PARTE 1: Sección 4 (Patrones)
2. Analiza PARTE 2: Todos los flujos
3. Optimiza con PARTE 1: Sección 7 (Mejores Prácticas)

### Por Objetivo

**Quiero entender la arquitectura** → PARTE 1 + PARTE 4  
**Quiero crear un feature** → PARTE 3 + PARTE 1 (Sección 5)  
**Quiero ver cómo fluyen los datos** → PARTE 2  
**Quiero copiar plantillas** → PARTE 3 (Sección 2)  
**Quiero resolver un problema** → PARTE 1 (Sección 8) + PARTE 3 (Comandos)

---

## 📊 ESTADÍSTICAS DEL DOCUMENTO

- **Total de líneas:** ~4,800+
- **Ejemplos de código:** 80+
- **Diagramas ASCII:** 20+
- **Plantillas reutilizables:** 10+
- **Comandos útiles:** 50+
- **Tiempo de lectura completo:** ~3 horas
- **Tiempo de lectura por partes:** 30-45 min cada una

---

## 🚀 INICIO RÁPIDO

### Para Desarrolladores Nuevos
```
1. Lee PARTE 1: Introducción (15 min)
2. Revisa PARTE 4: Diagramas (20 min)
3. Explora el código del proyecto
4. Vuelve a PARTE 1: Estructura de Capas (30 min)
```

### Para Crear Tu Primer Feature
```
1. Abre PARTE 3: Checklist
2. Sigue paso a paso
3. Copia plantillas de PARTE 3: Plantillas de Código
4. Consulta PARTE 1: Sección 5 si tienes dudas
```

---

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


---

# PARTE 2: FLUJOS DE ARQUITECTURA

---


# Flujos de Arquitectura - Diagramas y Ejemplos

## 📋 Contenido

1. [Flujo de Request Completo](#flujo-de-request-completo)
2. [Flujo de Creación de Entidad](#flujo-de-creación-de-entidad)
3. [Flujo de Validación](#flujo-de-validación)
4. [Flujo de Dependency Injection](#flujo-de-dependency-injection)
5. [Flujo de Caché (Decorator Pattern)](#flujo-de-caché-decorator-pattern)
6. [Flujo de Manejo de Errores](#flujo-de-manejo-de-errores)

---

## 🔄 Flujo de Request Completo

### Diagrama de Secuencia: Crear Usuario

```
┌─────────┐     ┌────────────┐     ┌─────────────┐     ┌──────────┐     ┌────────────┐     ┌──────────┐
│ Browser │     │ Controller │     │FormRequest  │     │ Service  │     │ Repository │     │ Eloquent │
└────┬────┘     └─────┬──────┘     └──────┬──────┘     └────┬─────┘     └─────┬──────┘     └────┬─────┘
     │                │                    │                 │                 │                 │
     │ POST /users    │                    │                 │                 │                 │
     ├───────────────>│                    │                 │                 │                 │
     │                │                    │                 │                 │                 │
     │                │ validate()         │                 │                 │                 │
     │                ├───────────────────>│                 │                 │                 │
     │                │                    │                 │                 │                 │
     │                │ toArray()          │                 │                 │                 │
     │                ├───────────────────>│                 │                 │                 │
     │                │<───────────────────┤                 │                 │                 │
     │                │   ['name', ...]    │                 │                 │                 │
     │                │                    │                 │                 │                 │
     │                │ save(array)        │                 │                 │                 │
     │                ├────────────────────┼────────────────>│                 │                 │
     │                │                    │                 │                 │                 │
     │                │                    │                 │ fromArray()     │                 │
     │                │                    │                 ├────────────┐    │                 │
     │                │                    │                 │            │    │                 │
     │                │                    │                 │ Create     │    │                 │
     │                │                    │                 │ Entity     │    │                 │
     │                │                    │                 │<───────────┘    │                 │
     │                │                    │                 │                 │                 │
     │                │                    │                 │ save(Entity)    │                 │
     │                │                    │                 ├────────────────>│                 │
     │                │                    │                 │                 │                 │
     │                │                    │                 │                 │ create()        │
     │                │                    │                 │                 ├────────────────>│
     │                │                    │                 │                 │                 │
     │                │                    │                 │                 │<────────────────┤
     │                │                    │                 │                 │   Model         │
     │                │                    │                 │<────────────────┤                 │
     │                │                    │                 │                 │                 │
     │                │<────────────────────┼─────────────────┤                 │                 │
     │                │                    │                 │                 │                 │
     │ redirect()     │                    │                 │                 │                 │
     │<───────────────┤                    │                 │                 │                 │
     │                │                    │                 │                 │                 │
```

### Código Paso a Paso

#### 1. Browser envía POST request

```javascript
// Frontend (React)
const handleSubmit = async (data) => {
  router.post('/backoffice/users', {
    name: 'John Doe',
    email: 'john@example.com',
    password: 'password123',
    password_confirmation: 'password123',
    roles: ['admin']
  });
};
```

#### 2. Laravel Route

```php
// routes/backoffice_users.php
Route::post('/', Users\StoreController::class)->name('store');
```

#### 3. Controller recibe request

```php
// StoreController.php
public function __invoke(
    StoreUserRequest $request,      // Laravel inyecta y valida
    UserServiceInterface $userService // Laravel inyecta desde container
) {
    // $request ya está validado aquí
    $userService->save($request->toArray());
    
    return redirect()
        ->route('backoffice.users.index')
        ->with('success', 'Usuario creado exitosamente.');
}
```

#### 4. Form Request valida

```php
// StoreUserRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
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
```

#### 5. Service procesa

```php
// UserService.php
public function save(array $user): void
{
    // Convierte array a Entity (con validación de dominio)
    $userEntity = UserEntity::fromArray($user);
    
    // Delega al repositorio
    $this->userRepository->save($userEntity);
}
```

#### 6. Entity se crea con validación

```php
// UserEntity.php
public static function fromArray(array $data): static
{
    // Value Objects validan automáticamente
    $name = PersonName::fromString($data['name']);      // ✓ Valida nombre
    $email = Email::fromString($data['email']);          // ✓ Valida email
    $password = Password::fromString($data['password']); // ✓ Valida password
    
    return new static(
        $data['id'] ?? null,
        $name,
        $email,
        $password,
        $data['roles'] ?? []
    );
}
```

#### 7. Repository persiste

```php
// UserEloquentRepository.php
public function save(UserEntity $userEntity): void
{
    try {
        // Convierte Entity a Model
        $userModel = $this->model->create([
            'name' => $userEntity->name,        // Value Object se convierte a string
            'email' => $userEntity->email,      // Value Object se convierte a string
            'password' => bcrypt($userEntity->password), // Value Object se convierte a string
        ]);

        // Sincroniza roles (Spatie Permission)
        $userModel->syncRoles($userEntity->roles);
    } catch (\Exception $e) {
        throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
    }
}
```

---

## 🏗️ Flujo de Creación de Entidad

### Diagrama: De Array a Entity

```
┌──────────────────────────────────────────────────────────────┐
│                    Array de Datos                             │
│  ['name' => 'John', 'email' => 'john@example.com', ...]      │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         │ UserEntity::fromArray()
                         ▼
┌──────────────────────────────────────────────────────────────┐
│              Creación de Value Objects                        │
│                                                               │
│  PersonName::fromString('John')                              │
│       │                                                       │
│       ├─> validate()  ✓ No vacío                            │
│       ├─> validate()  ✓ Min 2 caracteres                    │
│       └─> new PersonName('John')                            │
│                                                               │
│  Email::fromString('john@example.com')                       │
│       │                                                       │
│       ├─> validate()  ✓ No vacío                            │
│       ├─> validate()  ✓ Formato email válido                │
│       └─> new Email('john@example.com')                     │
│                                                               │
│  Password::fromString('password123')                         │
│       │                                                       │
│       ├─> validate()  ✓ No vacío                            │
│       ├─> validate()  ✓ Min 8 caracteres                    │
│       └─> new Password('password123')                       │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         │ new UserEntity(...)
                         ▼
┌──────────────────────────────────────────────────────────────┐
│                    UserEntity                                 │
│                                                               │
│  id: null                                                     │
│  name: PersonName { value: 'John' }                          │
│  email: Email { value: 'john@example.com' }                  │
│  password: Password { value: 'password123' }                 │
│  roles: ['admin']                                            │
│  createdAt: DateTimeImmutable                                │
│  updatedAt: DateTimeImmutable                                │
└───────────────────────────────────────────────────────────────┘
```

### Código Detallado

```php
// 1. Datos de entrada
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password123',
    'roles' => ['admin']
];

// 2. Crear Entity
$userEntity = UserEntity::fromArray($data);

// 3. Internamente en UserEntity::fromArray()
public static function fromArray(array $data): static
{
    // Cada Value Object valida en su constructor
    $name = PersonName::fromString($data['name']);
    // PersonName::validate() se ejecuta:
    //   - Verifica que no esté vacío
    //   - Verifica longitud mínima
    //   - Trim de espacios
    
    $email = Email::fromString($data['email']);
    // Email::validate() se ejecuta:
    //   - Verifica que no esté vacío
    //   - Verifica formato de email
    //   - Normaliza a minúsculas
    
    $password = Password::fromString($data['password']);
    // Password::validate() se ejecuta:
    //   - Verifica que no esté vacío
    //   - Verifica longitud mínima
    //   - Verifica complejidad (opcional)
    
    return new static(
        $data['id'] ?? null,
        $name,
        $email,
        $password,
        $data['roles'] ?? []
    );
}

// 4. Si alguna validación falla, lanza excepción
try {
    $userEntity = UserEntity::fromArray(['name' => '', 'email' => 'invalid']);
} catch (InvalidArgumentException $e) {
    // "El nombre no puede estar vacío"
    // o "El correo electrónico no es válido"
}
```

---

## ✅ Flujo de Validación

### Diagrama: Capas de Validación

```
┌─────────────────────────────────────────────────────────────────┐
│                    CAPA DE INFRAESTRUCTURA                       │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │           Form Request Validation                       │    │
│  │                                                          │    │
│  │  ✓ Formato de datos (string, integer, email)           │    │
│  │  ✓ Reglas de Laravel (required, unique, min, max)      │    │
│  │  ✓ Validaciones de base de datos (exists, unique)      │    │
│  │  ✓ Confirmación de campos (password_confirmation)      │    │
│  └────────────────────────────────────────────────────────┘    │
│                              │                                   │
│                              │ Si pasa validación                │
│                              ▼                                   │
└─────────────────────────────────────────────────────────────────┘
                               │
                               │ toArray()
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CAPA DE APLICACIÓN                            │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │              Service Layer                              │    │
│  │                                                          │    │
│  │  • Recibe datos validados                              │    │
│  │  • Convierte a Entity                                   │    │
│  │  • Delega a Repository                                  │    │
│  └────────────────────────────────────────────────────────┘    │
│                              │                                   │
│                              │ fromArray()                       │
│                              ▼                                   │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CAPA DE DOMINIO                             │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │           Value Object Validation                       │    │
│  │                                                          │    │
│  │  ✓ Reglas de negocio específicas                       │    │
│  │  ✓ Invariantes del dominio                             │    │
│  │  ✓ Validaciones complejas                              │    │
│  │  ✓ Lógica de negocio                                   │    │
│  └────────────────────────────────────────────────────────┘    │
│                              │                                   │
│                              │ Si pasa validación                │
│                              ▼                                   │
│  ┌────────────────────────────────────────────────────────┐    │
│  │              Entity Creation                            │    │
│  │                                                          │    │
│  │  • Entity creada con Value Objects validados           │    │
│  │  • Timestamps automáticos                              │    │
│  │  • Estado consistente garantizado                      │    │
│  └────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
```

### Ejemplo Completo de Validación

```php
// ============================================
// NIVEL 1: Form Request (Infraestructura)
// ============================================
class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Validaciones de Laravel
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'age' => 'required|integer|min:18|max:100',
            'roles' => 'required|array|exists:roles,name',
        ];
    }
}

// ✓ Pasa: ['name' => 'John', 'email' => 'john@example.com', 'password' => 'pass1234', ...]
// ✗ Falla: ['name' => '', ...] → "El nombre es obligatorio"
// ✗ Falla: ['email' => 'invalid', ...] → "El email debe ser válido"
// ✗ Falla: ['email' => 'existing@example.com', ...] → "El email ya está en uso"

// ============================================
// NIVEL 2: Value Objects (Dominio)
// ============================================
class Email implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        
        // Validación básica
        if (empty($value)) {
            throw new InvalidArgumentException('El email no puede estar vacío.');
        }
        
        // Validación de formato
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El email no es válido.');
        }
        
        // REGLA DE NEGOCIO: Solo emails corporativos
        if (!str_ends_with($value, '@company.com')) {
            throw new DomainException('Solo se permiten emails corporativos (@company.com)');
        }
        
        // Normalización
        $value = strtolower($value);
    }
}

// ✓ Pasa: 'john@company.com' → Email { value: 'john@company.com' }
// ✗ Falla: 'john@gmail.com' → DomainException: "Solo se permiten emails corporativos"

class PersonName implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        
        if (empty($value)) {
            throw new InvalidArgumentException('El nombre no puede estar vacío.');
        }
        
        // REGLA DE NEGOCIO: Nombre completo requerido
        if (str_word_count($value) < 2) {
            throw new DomainException('Se requiere nombre y apellido.');
        }
        
        // REGLA DE NEGOCIO: Solo letras y espacios
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $value)) {
            throw new DomainException('El nombre solo puede contener letras.');
        }
    }
}

// ✓ Pasa: 'John Doe' → PersonName { value: 'John Doe' }
// ✗ Falla: 'John' → DomainException: "Se requiere nombre y apellido"
// ✗ Falla: 'John123' → DomainException: "El nombre solo puede contener letras"

// ============================================
// NIVEL 3: Entity (Dominio)
// ============================================
class UserEntity extends BaseEntity
{
    public static function fromArray(array $data): static
    {
        // Validaciones de Value Objects se ejecutan aquí
        $name = PersonName::fromString($data['name']);
        $email = Email::fromString($data['email']);
        $password = Password::fromString($data['password']);
        
        // REGLA DE NEGOCIO: Validación adicional en Entity
        $age = $data['age'] ?? 0;
        if ($age < 18) {
            throw new DomainException('El usuario debe ser mayor de edad.');
        }
        
        return new static(
            $data['id'] ?? null,
            $name,
            $email,
            $password,
            $data['roles'] ?? [],
            $age
        );
    }
}

// ✓ Pasa: Todos los Value Objects válidos + edad >= 18
// ✗ Falla: Cualquier Value Object inválido o edad < 18
```

---

## 🔌 Flujo de Dependency Injection

### Diagrama: Service Container Resolution

```
┌─────────────────────────────────────────────────────────────────┐
│                   Laravel Service Container                      │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │         AppServiceProvider::register()                  │    │
│  │                                                          │    │
│  │  $this->app->bind(                                      │    │
│  │      UserServiceInterface::class,                       │    │
│  │      UserService::class                                 │    │
│  │  );                                                      │    │
│  │                                                          │    │
│  │  $this->app->bind(                                      │    │
│  │      UserRepositoryInterface::class,                    │    │
│  │      UserEloquentRepository::class                      │    │
│  │  );                                                      │    │
│  └────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
                               │
                               │ Request llega
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                      StoreController                             │
│                                                                  │
│  public function __invoke(                                      │
│      StoreUserRequest $request,                                 │
│      UserServiceInterface $userService  ← Container resuelve    │
│  ) { ... }                                                       │
└─────────────────────────────────────────────────────────────────┘
                               │
                               │ Container busca binding
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│         UserServiceInterface → UserService                       │
│                                                                  │
│  Container necesita crear UserService                           │
│  UserService requiere: UserRepositoryInterface                  │
└─────────────────────────────────────────────────────────────────┘
                               │
                               │ Container busca binding
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│    UserRepositoryInterface → UserEloquentRepository              │
│                                                                  │
│  Container necesita crear UserEloquentRepository                │
│  UserEloquentRepository requiere: User (Model)                  │
└─────────────────────────────────────────────────────────────────┘
                               │
                               │ Container crea instancias
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                   Árbol de Dependencias                          │
│                                                                  │
│  new StoreController(                                           │
│      request: StoreUserRequest,                                 │
│      userService: new UserService(                              │
│          userRepository: new UserEloquentRepository(            │
│              model: new User()                                  │
│          )                                                       │
│      )                                                           │
│  )                                                               │
└─────────────────────────────────────────────────────────────────┘
```

### Código de Registro

```php
// app/Providers/AppServiceProvider.php
class AppServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // ============================================
        // Registrar Servicios
        // ============================================
        
        // Opción 1: Binding simple
        $this->app->bind(
            UserServiceInterface::class,
            UserService::class
        );
        
        // Opción 2: Singleton (una sola instancia)
        $this->app->singleton(
            CacheServiceInterface::class,
            RedisCacheService::class
        );
        
        // Opción 3: Con closure (control total)
        $this->app->bind(
            UserServiceInterface::class,
            function ($app) {
                return new UserService(
                    $app->make(UserRepositoryInterface::class),
                    $app->make(LoggerInterface::class)
                );
            }
        );
        
        // ============================================
        // Registrar Repositorios
        // ============================================
        
        $this->app->bind(
            UserRepositoryInterface::class,
            UserEloquentRepository::class
        );
        
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductEloquentRepository::class
        );
        
        // ============================================
        // Decorator Pattern para Caching
        // ============================================
        
        $this->decorate(
            UserServiceInterface::class,
            UserService::class,
            UserCachingService::class
        );
        
        // Esto crea:
        // 1. UserService (implementación base)
        // 2. UserCachingService que envuelve UserService
        // 3. Cuando se inyecta UserServiceInterface, se recibe UserCachingService
    }
}
```

---

## 🎨 Flujo de Caché (Decorator Pattern)

### Diagrama: Decorator Wrapping

```
                    ┌─────────────────────────────────┐
                    │   UserServiceInterface          │
                    │   (Contrato)                    │
                    └─────────────────────────────────┘
                                  ▲
                                  │ implements
                    ┌─────────────┴─────────────┐
                    │                           │
        ┌───────────┴──────────┐    ┌──────────┴────────────┐
        │   UserService        │    │ UserCachingService    │
        │   (Base)             │    │ (Decorator)           │
        └──────────────────────┘    └───────────────────────┘
                                              │
                                              │ wraps
                                              ▼
                                    ┌──────────────────┐
                                    │   UserService    │
                                    │   (Decorated)    │
                                    └──────────────────┘
```

### Flujo de Ejecución con Caché

```
┌──────────────────────────────────────────────────────────────────┐
│                    Controller                                     │
│                                                                   │
│  $user = $userService->findById(1);                              │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         │ $userService es UserCachingService
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│              UserCachingService::findById(1)                      │
│                                                                   │
│  1. Genera cache key: "user:1"                                   │
│  2. Verifica si existe en caché                                  │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                    ┌────┴────┐
                    │         │
              ¿En caché?      │
                    │         │
         ┌──────────┘         └──────────┐
         │ SÍ                             │ NO
         ▼                                ▼
┌─────────────────────┐      ┌──────────────────────────────────┐
│  Retorna del caché  │      │  Llama al servicio decorado      │
│                     │      │                                   │
│  return $cached;    │      │  $user = $this->decoratedService │
└─────────────────────┘      │           ->findById(1);         │
                             │                                   │
                             │  Guarda en caché                  │
                             │  Cache::put("user:1", $user);    │
                             │                                   │
                             │  return $user;                    │
                             └───────────────────────────────────┘
```

### Código del Decorator

```php
// UserCachingService.php
class UserCachingService implements UserServiceInterface
{
    public function __construct(
        private readonly UserServiceInterface $decoratedService,
        private readonly BaseCacheService $cacheService
    ) {}

    public function findById(int $id): ?UserEntity
    {
        $cacheKey = AppCacheKeys::USER_BY_ID . $id;
        
        // Intenta obtener del caché
        return $this->cacheService->remember(
            $cacheKey,
            fn() => $this->decoratedService->findById($id),
            3600 // TTL: 1 hora
        );
    }

    public function save(array $user): void
    {
        // Ejecuta la operación
        $this->decoratedService->save($user);
        
        // Invalida cachés relacionados
        $this->cacheService->forget(AppCacheKeys::ALL_USERS);
        $this->cacheService->forgetPattern(AppCacheKeys::USER_BY_ID . '*');
    }

    public function update(int $userId, array $userEntity): void
    {
        // Ejecuta la operación
        $this->decoratedService->update($userId, $userEntity);
        
        // Invalida cachés específicos
        $this->cacheService->forget(AppCacheKeys::USER_BY_ID . $userId);
        $this->cacheService->forget(AppCacheKeys::ALL_USERS);
    }

    public function delete(int $userId): void
    {
        // Ejecuta la operación
        $this->decoratedService->delete($userId);
        
        // Invalida cachés
        $this->cacheService->forget(AppCacheKeys::USER_BY_ID . $userId);
        $this->cacheService->forget(AppCacheKeys::ALL_USERS);
    }
}

// BaseCacheService.php
class BaseCacheService
{
    public function remember(string $key, callable $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    public function forgetPattern(string $pattern): void
    {
        // Implementación específica según driver de caché
        $keys = Cache::getRedis()->keys($pattern);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}

// AppCacheKeys.php
class AppCacheKeys
{
    public const USER_BY_ID = 'user:';
    public const USER_BY_EMAIL = 'user:email:';
    public const ALL_USERS = 'users:all';
    public const USER_PROFILE = 'user:profile:';
}
```

---

## ⚠️ Flujo de Manejo de Errores

### Diagrama: Exception Handling

```
┌──────────────────────────────────────────────────────────────────┐
│                    Request Processing                             │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│                  Form Request Validation                          │
│                                                                   │
│  ✗ Validation fails                                              │
│  → ValidationException                                           │
│  → Redirect back with errors                                     │
└────────────────────────┬──────────────────────────────────────────┘
                         │ ✓ Validation passes
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│                    Service Layer                                  │
│                                                                   │
│  try {                                                            │
│      $entity = Entity::fromArray($data);                         │
│  }                                                                │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                    ┌────┴────┐
                    │         │
              ✗ Exception     │ ✓ Success
                    │         │
         ┌──────────┘         └──────────┐
         ▼                                ▼
┌─────────────────────┐      ┌──────────────────────────────────┐
│  Value Object       │      │  Repository Layer                 │
│  Validation Error   │      │                                   │
│                     │      │  try {                            │
│  InvalidArgument    │      │      $model->save();              │
│  Exception          │      │  }                                │
└──────┬──────────────┘      └────────┬──────────────────────────┘
       │                              │
       │                         ┌────┴────┐
       │                         │         │
       │                   ✗ Exception     │ ✓ Success
       │                         │         │
       │              ┌──────────┘         └──────────┐
       │              ▼                                ▼
       │    ┌─────────────────────┐      ┌──────────────────┐
       │    │  Database Error     │      │  Success         │
       │    │                     │      │  Response        │
       │    │  RepositoryException│      └──────────────────┘
       │    └──────┬──────────────┘
       │           │
       └───────────┴─────────────┐
                                 │
                                 ▼
                   ┌──────────────────────────────┐
                   │  Exception Handler           │
                   │                              │
                   │  • Log error                 │
                   │  • Transform exception       │
                   │  • Return response           │
                   └──────────────────────────────┘
```

### Jerarquía de Excepciones

```php
// Domain Layer Exceptions
namespace App\Src\Domain\Exceptions;

abstract class DomainException extends Exception
{
    use LogExceptionTrait;
    
    protected string $errorCode;
    protected bool $isUserFacing = false;

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $this->errorCode = $this->getOrGenerateErrorCode();
        parent::__construct($message, $code, $previous);
        $this->logError();
    }

    public function isUserFacing(): bool
    {
        return $this->isUserFacing;
    }

    protected function logError(): void
    {
        $userId = Auth::id() ?? 'guest';
        
        Log::error(sprintf(
            "[Error code: %s] [UserId: %s] %s in %s on line %d",
            $this->getErrorCode(),
            $userId,
            $this->getMessage(),
            $this->getFile(),
            $this->getLine()
        ));
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}

// User-facing exceptions (mostrar al usuario)
class UserFacingException extends DomainException
{
    protected bool $isUserFacing = true;
}

// System exceptions (solo log, no mostrar)
class SystemException extends DomainException
{
    protected bool $isUserFacing = false;
}

// Excepciones específicas
class UserNotFoundException extends UserFacingException
{
    public function __construct(int $userId)
    {
        parent::__construct(
            "Usuario con ID {$userId} no encontrado",
            404,
            null
        );
    }
}

class InsufficientStockException extends UserFacingException
{
    public function __construct(int $requested, int $available)
    {
        parent::__construct(
            "Stock insuficiente. Solicitado: {$requested}, Disponible: {$available}",
            400,
            null
        );
    }
}

// Infrastructure Layer Exceptions
namespace App\Src\Infrastructure\Exceptions;

class RepositoryException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        Log::error("Repository Error: {$message}", [
            'code' => $code,
            'previous' => $previous?->getMessage()
        ]);
        
        parent::__construct($message, $code, $previous);
    }
}
```

### Custom Exception Handler

```php
// app/Src/Infrastructure/Handlers/CustomExceptionHandler.php
namespace App\Src\Infrastructure\Handlers;

use App\Src\Domain\Exceptions\DomainException;
use App\Src\Infrastructure\Exceptions\RepositoryException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomExceptionHandler
{
    public function __construct(private Handler $handler) {}

    public function render(Request $request, \Throwable $e): Response
    {
        // Domain Exceptions
        if ($e instanceof DomainException) {
            if ($e->isUserFacing()) {
                // Mostrar al usuario
                return back()->with('error', $e->getMessage());
            } else {
                // Error del sistema, mostrar mensaje genérico
                return back()->with('error', 'Ha ocurrido un error. Por favor, intente nuevamente.');
            }
        }

        // Repository Exceptions
        if ($e instanceof RepositoryException) {
            return back()->with('error', 'Error al acceder a los datos. Por favor, intente nuevamente.');
        }

        // Validation Exceptions (Laravel)
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }

        // Default handler
        return $this->handler->render($request, $e);
    }
}
```

### Uso en el Código

```php
// En Repository
public function findById(int $id): ?ProductEntity
{
    try {
        $productModel = $this->model->find($id);
        
        if (!$productModel) {
            throw new ProductNotFoundException($id);
        }
        
        return $this->toEntity($productModel);
    } catch (ProductNotFoundException $e) {
        // Re-lanzar excepciones de dominio
        throw $e;
    } catch (\Exception $e) {
        // Envolver excepciones de infraestructura
        throw new RepositoryException(
            "Error al buscar producto: " . $e->getMessage(),
            $e->getCode(),
            $e
        );
    }
}

// En Entity
public function decreaseStock(int $quantity): void
{
    if ($quantity > $this->stock) {
        throw new InsufficientStockException($quantity, $this->stock);
    }
    
    $this->stock -= $quantity;
}

// En Controller
public function __invoke(int $id, ProductServiceInterface $productService)
{
    try {
        $product = $productService->findById($id);
        
        if (!$product) {
            return back()->with('error', 'Producto no encontrado');
        }
        
        return Inertia::render('Products/Show', [
            'product' => $product->toArray()
        ]);
    } catch (DomainException $e) {
        // El handler se encarga automáticamente
        throw $e;
    }
}
```

---

## 📊 Resumen de Flujos

### Flujo Completo: Create Operation

```
1. Browser → POST /users
2. Laravel Router → StoreController
3. Form Request → Validate (Infrastructure)
4. Controller → UserService->save()
5. Service → UserEntity::fromArray() (Domain)
6. Value Objects → Validate (Domain)
7. Entity → Created with validated data
8. Service → Repository->save(Entity)
9. Repository → Convert Entity to Model
10. Eloquent → Persist to database
11. Repository → Return
12. Service → Return
13. Controller → Redirect with success message
14. Browser → Show success notification
```

### Flujo Completo: Read Operation with Cache

```
1. Browser → GET /users/1
2. Laravel Router → ShowController
3. Controller → UserService->findById(1)
4. CachingService → Check cache
   ├─ Cache HIT → Return cached entity
   └─ Cache MISS → 
       5. UserService->findById(1)
       6. Repository->findById(1)
       7. Eloquent → Query database
       8. Repository → Convert Model to Entity
       9. CachingService → Store in cache
10. Controller → Render view with entity
11. Browser → Display user
```

---

**Última actualización**: 2025-12-30
**Versión**: 1.0.0


---

# PARTE 3: REFERENCIA RÁPIDA

---


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


---

# PARTE 4: ARQUITECTURA VISUAL

---


# 🏛️ Arquitectura Visual del Proyecto

## Diagrama de Capas Hexagonales

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                              │
│                          🌐 EXTERNAL WORLD                                   │
│                                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Browser    │  │   Database   │  │    Cache     │  │  External    │  │
│  │   (React)    │  │ (PostgreSQL) │  │   (Redis)    │  │     APIs     │  │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  │
│         │                  │                  │                  │           │
└─────────┼──────────────────┼──────────────────┼──────────────────┼───────────┘
          │                  │                  │                  │
          │                  │                  │                  │
┌─────────┼──────────────────┼──────────────────┼──────────────────┼───────────┐
│         │                  │                  │                  │           │
│         ▼                  ▼                  ▼                  ▼           │
│  ┌──────────────────────────────────────────────────────────────────────┐  │
│  │                                                                       │  │
│  │                   🔌 INFRASTRUCTURE LAYER                            │  │
│  │                   (Adapters / Ports)                                 │  │
│  │                                                                       │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌───────────┐ │  │
│  │  │ Controllers │  │ Eloquent    │  │   Cache     │  │  HTTP     │ │  │
│  │  │ (Inertia)   │  │ Repositories│  │  Services   │  │  Clients  │ │  │
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └───────────┘ │  │
│  │                                                                       │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐                 │  │
│  │  │   Form      │  │  Eloquent   │  │  Exception  │                 │  │
│  │  │  Requests   │  │   Models    │  │  Handlers   │                 │  │
│  │  └─────────────┘  └─────────────┘  └─────────────┘                 │  │
│  │                                                                       │  │
│  └───────────────────────────────┬───────────────────────────────────────┘  │
│                                  │                                          │
│                                  │ Implements Interfaces                    │
│                                  │                                          │
│  ┌───────────────────────────────▼───────────────────────────────────────┐  │
│  │                                                                         │  │
│  │                   🎯 APPLICATION LAYER                                 │  │
│  │                   (Use Cases / Orchestration)                          │  │
│  │                                                                         │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌───────────┐   │  │
│  │  │ Application │  │   Caching   │  │    DTOs     │  │  Events   │   │  │
│  │  │  Services   │  │  Services   │  │             │  │           │   │  │
│  │  │             │  │ (Decorator) │  │             │  │           │   │  │
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └───────────┘   │  │
│  │                                                                         │  │
│  │  ┌─────────────┐  ┌─────────────┐                                     │  │
│  │  │  Listeners  │  │  Commands   │                                     │  │
│  │  │             │  │             │                                     │  │
│  │  └─────────────┘  └─────────────┘                                     │  │
│  │                                                                         │  │
│  └───────────────────────────────┬─────────────────────────────────────────┘  │
│                                  │                                          │
│                                  │ Uses Interfaces                          │
│                                  │                                          │
│  ┌───────────────────────────────▼─────────────────────────────────────────┐  │
│  │                                                                           │  │
│  │                   💎 DOMAIN LAYER                                        │  │
│  │                   (Business Logic / Core)                                │  │
│  │                                                                           │  │
│  │  ┌─────────────────────────────────────────────────────────────────┐   │  │
│  │  │                        Contracts                                 │   │  │
│  │  │  ┌──────────────────┐  ┌──────────────────┐                    │   │  │
│  │  │  │   Repository     │  │    Service       │                    │   │  │
│  │  │  │   Interfaces     │  │   Interfaces     │                    │   │  │
│  │  │  └──────────────────┘  └──────────────────┘                    │   │  │
│  │  └─────────────────────────────────────────────────────────────────┘   │  │
│  │                                                                           │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌───────────┐     │  │
│  │  │  Entities   │  │   Value     │  │  Domain     │  │  Domain   │     │  │
│  │  │             │  │  Objects    │  │  Services   │  │Exceptions │     │  │
│  │  │ UserEntity  │  │   Email     │  │             │  │           │     │  │
│  │  │ProductEntity│  │PersonName   │  │             │  │           │     │  │
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └───────────┘     │  │
│  │                                                                           │  │
│  │  ┌─────────────────────────────────────────────────────────────────┐   │  │
│  │  │                    Business Rules                                │   │  │
│  │  │  • Validaciones de negocio                                       │   │  │
│  │  │  • Invariantes del dominio                                       │   │  │
│  │  │  • Lógica de negocio pura                                        │   │  │
│  │  └─────────────────────────────────────────────────────────────────┘   │  │
│  │                                                                           │  │
│  └───────────────────────────────────────────────────────────────────────────┘  │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## Flujo de Datos: Request → Response

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                              │
│  1. HTTP Request                                                            │
│     POST /users                                                             │
│     { name: "John", email: "john@example.com", password: "..." }           │
│                                                                              │
└────────────────────────────┬─────────────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  2. INFRASTRUCTURE: Controller                                              │
│     StoreController::__invoke(StoreUserRequest, UserServiceInterface)       │
│                                                                              │
│     ┌────────────────────────────────────────────────────────────────┐     │
│     │  Form Request Validation (Infrastructure Layer)                │     │
│     │  • required, email, unique (Laravel rules)                     │     │
│     │  • Database checks                                             │     │
│     └────────────────────────────────────────────────────────────────┘     │
│                                                                              │
└────────────────────────────┬─────────────────────────────────────────────────┘
                             │
                             │ $request->toArray()
                             ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  3. APPLICATION: Service                                                    │
│     UserService::save(array $data)                                          │
│                                                                              │
│     ┌────────────────────────────────────────────────────────────────┐     │
│     │  Orchestration                                                 │     │
│     │  • Converts array to Entity                                   │     │
│     │  • Delegates to Repository                                    │     │
│     └────────────────────────────────────────────────────────────────┘     │
│                                                                              │
└────────────────────────────┬─────────────────────────────────────────────────┘
                             │
                             │ UserEntity::fromArray($data)
                             ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  4. DOMAIN: Entity Creation                                                 │
│     UserEntity::fromArray()                                                 │
│                                                                              │
│     ┌────────────────────────────────────────────────────────────────┐     │
│     │  Value Objects Creation (Domain Validation)                   │     │
│     │  • PersonName::fromString("John")                             │     │
│     │    ✓ Not empty, min length, valid characters                 │     │
│     │  • Email::fromString("john@example.com")                      │     │
│     │    ✓ Not empty, valid format, business rules                 │     │
│     │  • Password::fromString("...")                                │     │
│     │    ✓ Not empty, min length, complexity                        │     │
│     └────────────────────────────────────────────────────────────────┘     │
│                                                                              │
│     Returns: UserEntity {                                                   │
│       id: null,                                                             │
│       name: PersonName { value: "John" },                                   │
│       email: Email { value: "john@example.com" },                           │
│       password: Password { value: "..." },                                  │
│       roles: ["admin"]                                                      │
│     }                                                                        │
│                                                                              │
└────────────────────────────┬─────────────────────────────────────────────────┘
                             │
                             │ Entity
                             ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  5. APPLICATION: Service → Repository                                       │
│     UserService calls UserRepositoryInterface::save(UserEntity)             │
│                                                                              │
└────────────────────────────┬─────────────────────────────────────────────────┘
                             │
                             │ Entity
                             ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  6. INFRASTRUCTURE: Repository                                              │
│     UserEloquentRepository::save(UserEntity)                                │
│                                                                              │
│     ┌────────────────────────────────────────────────────────────────┐     │
│     │  Entity → Model Conversion                                    │     │
│     │  • Extract values from Value Objects                          │     │
│     │  • Create Eloquent Model                                      │     │
│     │  • Persist to database                                        │     │
│     └────────────────────────────────────────────────────────────────┘     │
│                                                                              │
│     User::create([                                                          │
│       'name' => (string) $entity->name,                                     │
│       'email' => (string) $entity->email,                                   │
│       'password' => bcrypt($entity->password)                               │
│     ]);                                                                      │
│                                                                              │
└────────────────────────────┬─────────────────────────────────────────────────┘
                             │
                             │ Success
                             ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  7. INFRASTRUCTURE: Controller                                              │
│     return redirect()->route('users.index')                                 │
│                       ->with('success', 'User created');                    │
│                                                                              │
└────────────────────────────┬─────────────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  8. HTTP Response                                                           │
│     302 Redirect to /users                                                  │
│     Flash message: "User created successfully"                              │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## Mapa de Dependencias

```
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                           │
│                        Dependency Flow                                    │
│                                                                           │
│  ┌─────────────┐                                                         │
│  │ Controller  │                                                         │
│  └──────┬──────┘                                                         │
│         │                                                                 │
│         │ depends on                                                     │
│         ▼                                                                 │
│  ┌─────────────────────┐                                                 │
│  │ Service Interface   │ ◄────────────────┐                             │
│  └──────┬──────────────┘                   │                             │
│         │                                   │                             │
│         │ implemented by                   │ depends on                 │
│         ▼                                   │                             │
│  ┌─────────────────────┐                   │                             │
│  │ Application Service │───────────────────┘                             │
│  └──────┬──────────────┘                                                 │
│         │                                                                 │
│         │ depends on                                                     │
│         ▼                                                                 │
│  ┌─────────────────────────┐                                             │
│  │ Repository Interface    │ ◄────────────────┐                         │
│  └──────┬──────────────────┘                   │                         │
│         │                                       │                         │
│         │ implemented by                       │ depends on             │
│         ▼                                       │                         │
│  ┌─────────────────────────┐                   │                         │
│  │ Eloquent Repository     │───────────────────┘                         │
│  └──────┬──────────────────┘                                             │
│         │                                                                 │
│         │ uses                                                           │
│         ▼                                                                 │
│  ┌─────────────────────────┐                                             │
│  │ Eloquent Model          │                                             │
│  └─────────────────────────┘                                             │
│                                                                           │
│  ┌────────────────────────────────────────────────────────────────┐     │
│  │  KEY PRINCIPLE:                                                 │     │
│  │  • Infrastructure depends on Domain (via interfaces)           │     │
│  │  • Domain NEVER depends on Infrastructure                      │     │
│  │  • Application orchestrates Domain and Infrastructure          │     │
│  └────────────────────────────────────────────────────────────────┘     │
│                                                                           │
└───────────────────────────────────────────────────────────────────────────┘
```

---

## Estructura de Directorios Visualizada

```
WhapProject/
│
├── 📚 DOCUMENTATION
│   ├── DEVELOPER_GUIDE.md          ← Guía completa de arquitectura
│   ├── ARCHITECTURE_FLOWS.md       ← Diagramas y flujos
│   ├── QUICK_REFERENCE.md          ← Plantillas y checklist
│   ├── DOCUMENTATION_INDEX.md      ← Índice de navegación
│   └── ARCHITECTURE_VISUAL.md      ← Este archivo
│
├── 🏗️ BACKEND (app/Src/)
│   │
│   ├── 💎 Domain/                   ← CORE (Sin dependencias externas)
│   │   ├── Contracts/
│   │   │   ├── RepositoryContracts/
│   │   │   │   ├── BaseEntityRepository.php
│   │   │   │   ├── UserRepositoryInterface.php
│   │   │   │   └── RoleRepositoryInterface.php
│   │   │   └── ServiceContracts/
│   │   │       └── UserServiceInterface.php
│   │   │
│   │   ├── Entities/                ← Objetos con identidad
│   │   │   ├── BaseEntity.php
│   │   │   └── UserEntity.php
│   │   │
│   │   ├── ValueObjects/            ← Objetos inmutables
│   │   │   ├── Concerns/
│   │   │   │   └── StringValueObject.php
│   │   │   ├── Email.php
│   │   │   ├── Password.php
│   │   │   ├── PersonName.php
│   │   │   ├── Username.php
│   │   │   ├── CityName.php
│   │   │   ├── StreetName.php
│   │   │   └── PostalCode.php
│   │   │
│   │   ├── Exceptions/              ← Excepciones del dominio
│   │   │   ├── DomainException.php
│   │   │   ├── UserFacingException.php
│   │   │   └── SystemException.php
│   │   │
│   │   └── Traits/
│   │       └── LogExceptionTrait.php
│   │
│   ├── 🎯 Application/              ← CASOS DE USO
│   │   ├── DTOs/                    ← Data Transfer Objects
│   │   ├── Events/                  ← Eventos de aplicación
│   │   ├── Listeners/               ← Listeners de eventos
│   │   └── Services/
│   │       └── Backoffice/
│   │           ├── CachingServices/ ← Decorator Pattern
│   │           │   ├── AppCacheKeys.php
│   │           │   ├── BaseCacheService.php
│   │           │   └── UserCachingService.php
│   │           └── UserService.php
│   │
│   └── 🔌 Infrastructure/           ← ADAPTADORES
│       ├── Controllers/
│       │   ├── Api/
│       │   │   └── AuthController.php
│       │   └── Backoffice/
│       │       ├── Auth/            ← Autenticación
│       │       ├── Users/           ← CRUD de usuarios
│       │       │   ├── IndexController.php
│       │       │   ├── CreateController.php
│       │       │   ├── StoreController.php
│       │       │   ├── EditController.php
│       │       │   ├── UpdateController.php
│       │       │   └── DestroyController.php
│       │       └── Settings/
│       │
│       ├── Repositories/
│       │   └── Eloquent/
│       │       ├── UserEloquentRepository.php
│       │       └── RoleEloquentRepository.php
│       │
│       ├── Requests/                ← Form Requests
│       │   └── Backoffice/
│       │       └── Users/
│       │           ├── StoreUserRequest.php
│       │           └── UpdateUserRequest.php
│       │
│       ├── Exceptions/
│       │   ├── ExceptionTransformer.php
│       │   └── RepositoryException.php
│       │
│       ├── Handlers/
│       │   └── CustomExceptionHandler.php
│       │
│       └── Middleware/
│
├── 🗄️ DATABASE
│   ├── app/Models/                  ← Eloquent Models
│   │   └── User.php
│   │
│   └── database/migrations/         ← Migraciones
│
├── ⚛️ FRONTEND (resources/js/)
│   ├── Components/                  ← Componentes React
│   ├── Layouts/                     ← Layouts
│   ├── Pages/                       ← Páginas Inertia
│   ├── types/                       ← TypeScript types
│   ├── app.tsx                      ← Entry point
│   └── bootstrap.js
│
├── 🛣️ ROUTES
│   ├── web.php
│   ├── backoffice_routes.php
│   ├── backoffice_auth.php
│   └── api_routes.php
│
└── ⚙️ CONFIG
    ├── app/Providers/
    │   ├── AppServiceProvider.php   ← Dependency Injection
    │   └── BaseServiceProvider.php  ← Decorator helper
    │
    ├── .env                         ← Environment config
    ├── composer.json                ← PHP dependencies
    └── package.json                 ← Node dependencies
```

---

## Patrón de Nombres

```
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                           │
│  DOMAIN LAYER                                                            │
│  ├── Entities:        [Name]Entity.php          (UserEntity)            │
│  ├── Value Objects:   [Name].php                (Email, PersonName)     │
│  ├── Interfaces:      [Name]Interface.php       (UserRepositoryInterface)│
│  └── Exceptions:      [Name]Exception.php       (UserNotFoundException) │
│                                                                           │
│  APPLICATION LAYER                                                       │
│  ├── Services:        [Name]Service.php         (UserService)           │
│  ├── DTOs:            [Name]DTO.php             (CreateUserDTO)         │
│  └── Events:          [Name]Event.php           (UserCreatedEvent)      │
│                                                                           │
│  INFRASTRUCTURE LAYER                                                    │
│  ├── Controllers:     [Action]Controller.php    (StoreController)       │
│  ├── Repositories:    [Name]EloquentRepository  (UserEloquentRepository)│
│  ├── Requests:        [Action][Name]Request     (StoreUserRequest)      │
│  └── Models:          [Name].php                (User)                  │
│                                                                           │
│  DATABASE                                                                │
│  ├── Tables:          [name]s                   (users, products)       │
│  ├── Columns:         [name]_[field]            (user_id, created_at)   │
│  └── Migrations:      create_[table]_table      (create_users_table)    │
│                                                                           │
└───────────────────────────────────────────────────────────────────────────┘
```

---

## Principios SOLID Visualizados

```
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                           │
│  S - Single Responsibility Principle                                     │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  ✓ Controller:  Solo maneja HTTP                                │   │
│  │  ✓ Service:     Solo orquesta casos de uso                      │   │
│  │  ✓ Repository:  Solo accede a datos                             │   │
│  │  ✓ Entity:      Solo lógica de negocio                          │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
│  O - Open/Closed Principle                                               │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  ✓ Decorator Pattern: Agrega caché sin modificar servicio       │   │
│  │  ✓ Interfaces: Permite nuevas implementaciones                  │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
│  L - Liskov Substitution Principle                                       │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  ✓ UserEloquentRepository puede sustituir                       │   │
│  │    UserRepositoryInterface sin romper nada                      │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
│  I - Interface Segregation Principle                                     │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  ✓ Interfaces específicas por dominio                           │   │
│  │  ✓ No interfaces "gordas" con métodos innecesarios              │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
│  D - Dependency Inversion Principle                                      │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  ✓ Controller depende de ServiceInterface (abstracción)         │   │
│  │  ✓ Service depende de RepositoryInterface (abstracción)         │   │
│  │  ✓ NUNCA depende de implementaciones concretas                  │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
└───────────────────────────────────────────────────────────────────────────┘
```

---

## Ciclo de Vida de una Entity

```
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                           │
│  1. CREATION (from array)                                                │
│     ┌─────────────────────────────────────────────────────────────┐    │
│     │  $data = ['name' => 'John', 'email' => 'john@example.com']  │    │
│     │  $entity = UserEntity::fromArray($data)                     │    │
│     │                                                               │    │
│     │  • Creates Value Objects (with validation)                  │    │
│     │  • Validates business rules                                 │    │
│     │  • Sets timestamps                                          │    │
│     └─────────────────────────────────────────────────────────────┘    │
│                                                                           │
│  2. IN MEMORY (Domain operations)                                        │
│     ┌─────────────────────────────────────────────────────────────┐    │
│     │  $entity->changeEmail(Email::fromString('new@example.com')) │    │
│     │  $entity->assignRole('admin')                               │    │
│     │  $entity->activate()                                        │    │
│     │                                                               │    │
│     │  • Business logic methods                                   │    │
│     │  • State changes                                            │    │
│     │  • Invariant validation                                     │    │
│     └─────────────────────────────────────────────────────────────┘    │
│                                                                           │
│  3. PERSISTENCE (to database)                                            │
│     ┌─────────────────────────────────────────────────────────────┐    │
│     │  $repository->save($entity)                                 │    │
│     │                                                               │    │
│     │  • Entity → Model conversion                                │    │
│     │  • Value Objects → primitive types                          │    │
│     │  • Database insert/update                                   │    │
│     └─────────────────────────────────────────────────────────────┘    │
│                                                                           │
│  4. RETRIEVAL (from database)                                            │
│     ┌─────────────────────────────────────────────────────────────┐    │
│     │  $entity = $repository->findById(1)                         │    │
│     │                                                               │    │
│     │  • Database query                                           │    │
│     │  • Model → Entity conversion                                │    │
│     │  • Primitive types → Value Objects                          │    │
│     └─────────────────────────────────────────────────────────────┘    │
│                                                                           │
│  5. SERIALIZATION (to array/JSON)                                        │
│     ┌─────────────────────────────────────────────────────────────┐    │
│     │  $array = $entity->toArray()                                │    │
│     │  $json = json_encode($entity->toArray())                    │    │
│     │                                                               │    │
│     │  • Value Objects → strings                                  │    │
│     │  • Ready for API/Frontend                                   │    │
│     └─────────────────────────────────────────────────────────────┘    │
│                                                                           │
└───────────────────────────────────────────────────────────────────────────┘
```

---

## Comparación: Arquitectura Tradicional vs Hexagonal

```
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                           │
│  ARQUITECTURA TRADICIONAL (Layered)                                      │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  Controller → Model → Database                                   │   │
│  │                                                                   │   │
│  │  ✗ Lógica de negocio mezclada con framework                     │   │
│  │  ✗ Difícil de testear                                           │   │
│  │  ✗ Acoplamiento alto                                            │   │
│  │  ✗ Difícil de cambiar base de datos                             │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
│  ARQUITECTURA HEXAGONAL (Este Proyecto)                                  │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  Controller → Service → Repository Interface → Eloquent Repo    │   │
│  │                  ↓                                                │   │
│  │              Entity (Domain Logic)                               │   │
│  │                                                                   │   │
│  │  ✓ Lógica de negocio aislada en Domain                          │   │
│  │  ✓ Fácil de testear (mock interfaces)                           │   │
│  │  ✓ Bajo acoplamiento                                            │   │
│  │  ✓ Fácil cambiar implementación (Eloquent → MongoDB)            │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
└───────────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 Resumen Visual

```
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                           │
│  ARQUITECTURA HEXAGONAL = CAPAS + INTERFACES + INVERSIÓN DE DEPENDENCIAS│
│                                                                           │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                                                                   │   │
│  │   DOMAIN (Core)                                                  │   │
│  │   • Entities                                                     │   │
│  │   • Value Objects                                                │   │
│  │   • Business Rules                                               │   │
│  │   • Interfaces (Contracts)                                       │   │
│  │                                                                   │   │
│  │   ✓ Sin dependencias externas                                   │   │
│  │   ✓ Lógica de negocio pura                                      │   │
│  │   ✓ Fácil de testear                                            │   │
│  │                                                                   │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                              ▲                                            │
│                              │ Uses                                      │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                                                                   │   │
│  │   APPLICATION (Use Cases)                                        │   │
│  │   • Services                                                     │   │
│  │   • DTOs                                                         │   │
│  │   • Events                                                       │   │
│  │                                                                   │   │
│  │   ✓ Orquesta el dominio                                         │   │
│  │   ✓ Casos de uso de negocio                                     │   │
│  │                                                                   │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                              ▲                                            │
│                              │ Implements                                │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                                                                   │   │
│  │   INFRASTRUCTURE (Adapters)                                      │   │
│  │   • Controllers                                                  │   │
│  │   • Repositories                                                 │   │
│  │   • External Services                                            │   │
│  │                                                                   │   │
│  │   ✓ Implementa interfaces del dominio                           │   │
│  │   ✓ Conecta con servicios externos                              │   │
│  │   ✓ Fácil de reemplazar                                         │   │
│  │                                                                   │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
└───────────────────────────────────────────────────────────────────────────┘
```

---

**Para más detalles, consulta:**
- [📘 DEVELOPER_GUIDE.md](./DEVELOPER_GUIDE.md) - Guía completa
- [🔄 ARCHITECTURE_FLOWS.md](./ARCHITECTURE_FLOWS.md) - Flujos detallados
- [⚡ QUICK_REFERENCE.md](./QUICK_REFERENCE.md) - Referencia rápida
- [📖 DOCUMENTATION_INDEX.md](./DOCUMENTATION_INDEX.md) - Índice de navegación

---

**Última actualización:** 2025-12-30
**Versión:** 1.0.0


---

# 🎉 FIN DEL DOCUMENTO

---

## 📚 RESUMEN DE CONTENIDO

Has completado la lectura de la documentación completa del proyecto. Este documento contiene:

### ✅ PARTE 1: GUÍA DEL DESARROLLADOR (1,968 líneas)
- Arquitectura Hexagonal explicada en detalle
- Estructura de las 3 capas (Domain, Application, Infrastructure)
- Patrones y conceptos clave (DI, Repository, Decorator)
- Guía completa para crear nuevos features
- Ejemplos prácticos y mejores prácticas
- Troubleshooting común

### ✅ PARTE 2: FLUJOS DE ARQUITECTURA (853 líneas)
- Flujo completo de requests HTTP
- Flujo de creación de entidades
- Capas de validación
- Dependency Injection en acción
- Patrón Decorator para caché
- Manejo de errores y excepciones

### ✅ PARTE 3: REFERENCIA RÁPIDA (1,500+ líneas)
- Checklist completo para nuevos features
- 10+ plantillas de código listas para usar
- Comandos útiles (Sail, Artisan, pnpm, Git)
- Estructura de archivos
- Convenciones de nomenclatura

### ✅ PARTE 4: ARQUITECTURA VISUAL (634 líneas)
- Diagramas de capas hexagonales
- Flujo de datos Request → Response
- Mapa de dependencias
- Estructura de directorios visualizada
- Principios SOLID visualizados

---

## 🎯 PRÓXIMOS PASOS

### Si eres nuevo en el proyecto:
1. ✅ Has leído la documentación completa
2. 📝 Explora el código del proyecto
3. 🔨 Modifica un feature existente
4. 🚀 Crea tu primer feature desde cero

### Si vas a desarrollar:
1. 📋 Usa el checklist de PARTE 3
2. �� Copia las plantillas necesarias
3. 🔍 Consulta los flujos en PARTE 2
4. ✅ Valida con las mejores prácticas de PARTE 1

### Si necesitas ayuda:
1. 🔍 Busca en la sección de Troubleshooting (PARTE 1)
2. 📖 Revisa los comandos útiles (PARTE 3)
3. 🎨 Visualiza los flujos (PARTE 2 y PARTE 4)
4. 💬 Pregunta al equipo con contexto específico

---

## 📖 DOCUMENTOS INDIVIDUALES

Si prefieres consultar las partes por separado:

- **DEVELOPER_GUIDE.md** - Guía completa del desarrollador
- **ARCHITECTURE_FLOWS.md** - Diagramas y flujos
- **QUICK_REFERENCE.md** - Referencia rápida
- **ARCHITECTURE_VISUAL.md** - Arquitectura visual
- **DOCUMENTATION_INDEX.md** - Índice de navegación

---

## 🔖 MARCADORES ÚTILES

### Búsqueda Rápida en el Documento

Para encontrar rápidamente lo que necesitas, busca estas palabras clave:

- **"Value Object"** - Para crear objetos de valor
- **"Entity"** - Para crear entidades
- **"Repository"** - Para implementar repositorios
- **"Service"** - Para crear servicios
- **"Controller"** - Para crear controladores
- **"Form Request"** - Para validaciones
- **"Decorator"** - Para implementar caché
- **"Dependency Injection"** - Para inyección de dependencias
- **"Checklist"** - Para el checklist completo
- **"Plantilla"** - Para plantillas de código
- **"Comandos"** - Para comandos útiles
- **"Troubleshooting"** - Para resolver problemas

---

## 💡 CONSEJOS FINALES

### Para Máxima Productividad

1. **Guarda este documento** en tus favoritos
2. **Imprime el checklist** de PARTE 3 para tenerlo a mano
3. **Marca las plantillas** que más uses
4. **Revisa los diagramas** cuando tengas dudas sobre flujos

### Para Aprendizaje Continuo

1. **Lee una sección por día** - No intentes absorber todo de una vez
2. **Practica mientras lees** - Crea ejemplos reales
3. **Compara con el código** - Revisa implementaciones existentes
4. **Enseña a otros** - La mejor forma de aprender es enseñar

### Para Contribuir

1. **Sigue las convenciones** documentadas en PARTE 3
2. **Usa las plantillas** para mantener consistencia
3. **Documenta tu código** siguiendo los ejemplos
4. **Actualiza la documentación** si agregas features importantes

---

## 🏆 DOMINIO DE LA ARQUITECTURA

### Niveles de Competencia

**🌱 Nivel 1: Principiante**
- ✅ Entiendes las 3 capas
- ✅ Puedes modificar features existentes
- ✅ Conoces los Value Objects y Entities

**🌿 Nivel 2: Intermedio**
- ✅ Puedes crear features simples desde cero
- ✅ Entiendes el flujo completo de datos
- ✅ Implementas validaciones correctamente

**🌳 Nivel 3: Avanzado**
- ✅ Creas features complejos con múltiples entidades
- ✅ Implementas patrones avanzados (Decorator, etc.)
- ✅ Optimizas y refactorizas código existente

**🚀 Nivel 4: Experto**
- ✅ Diseñas arquitectura de nuevos módulos
- ✅ Mentorizas a otros desarrolladores
- ✅ Contribuyes a mejorar la arquitectura

---

## 📞 CONTACTO Y SOPORTE

### ¿Tienes preguntas?

1. **Consulta primero** este documento
2. **Busca en** la sección de Troubleshooting
3. **Revisa** los ejemplos y plantillas
4. **Pregunta** al equipo con contexto específico

### ¿Encontraste un error?

1. Reporta el error con detalles
2. Sugiere una corrección
3. Actualiza la documentación si tienes permisos

### ¿Quieres mejorar la documentación?

1. Identifica áreas de mejora
2. Propón cambios específicos
3. Mantén el formato y estilo existente
4. Actualiza el índice si es necesario

---

## 📊 INFORMACIÓN DEL DOCUMENTO

- **Nombre:** COMPLETE_DOCUMENTATION.md
- **Versión:** 1.0.0
- **Fecha de creación:** 2025-12-30
- **Última actualización:** 2025-12-30
- **Total de líneas:** ~4,955
- **Tamaño:** ~184 KB
- **Partes:** 4 secciones principales
- **Idioma:** Español
- **Formato:** Markdown con diagramas ASCII

---

## 🎓 CERTIFICACIÓN DE LECTURA

Si has leído este documento completo, ahora tienes:

✅ Conocimiento profundo de Arquitectura Hexagonal  
✅ Comprensión de Domain-Driven Design  
✅ Habilidad para crear features siguiendo patrones  
✅ Herramientas para ser productivo inmediatamente  
✅ Referencia completa para consultas futuras  

**¡Felicitaciones! Estás listo para desarrollar en este proyecto.**

---

## 🌟 AGRADECIMIENTOS

Gracias por tomarte el tiempo de leer esta documentación completa.

Este documento fue creado para facilitar el desarrollo y mantener la calidad del código en el proyecto.

**¡Feliz desarrollo!** 🚀

---

**FIN DEL DOCUMENTO COMPLETO**

---

