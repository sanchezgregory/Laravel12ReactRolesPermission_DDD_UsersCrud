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
