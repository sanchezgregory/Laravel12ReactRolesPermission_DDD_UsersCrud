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
