# Guía de Desarrollo con Laravel Sail y pnpm

## Configuración Inicial

### 1. Configurar el proyecto por primera vez

```bash
# Ejecutar el script de configuración automática
./setup.sh
```

O manualmente:

```bash
# Instalar dependencias PHP
composer install

# Copiar configuración de ambiente (si no existe)
cp .env.sail .env

# Generar clave de aplicación
php artisan key:generate

# Levantar contenedores
./vendor/bin/sail up -d

# Ejecutar migraciones
./vendor/bin/sail artisan migrate

# Instalar dependencias Node.js con pnpm
./vendor/bin/sail exec laravel.test pnpm install
```

### 2. Crear alias para Sail (Recomendado)

**macOS/Linux:**
```bash
# Agregar al ~/.bashrc o ~/.zshrc
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'

# Recargar configuración
source ~/.bashrc  # o ~/.zshrc
```

**Windows PowerShell:**
```powershell
# Agregar al perfil de PowerShell
function sail { sh $(if (Test-Path sail) { "sail" } else { "vendor/bin/sail" }) @args }
```

### 3. Instalar pnpm (si no está instalado)

```bash
# Instalar pnpm globalmente
npm install -g pnpm

# Verificar instalación
pnpm --version
```

## Comandos de Desarrollo Diario

### Gestión de Contenedores

```bash
# Levantar todos los servicios
sail up -d

# Detener todos los servicios
sail down

# Reconstruir contenedores
sail build --no-cache

# Ver logs en tiempo real
sail logs -f

# Ver logs de un servicio específico
sail logs laravel.test
sail logs pgsql
```

### Desarrollo Frontend con pnpm

```bash
# Desarrollo con hot reload (puerto 5174)
sail exec laravel.test pnpm run dev

# Build para producción
sail exec laravel.test pnpm run build

# Verificar tipos TypeScript
sail exec laravel.test pnpm run type-check

# Verificar tipos en modo watch
sail exec laravel.test pnpm run type-check:watch

# Gestión de dependencias
sail exec laravel.test pnpm add <package-name>
sail exec laravel.test pnpm add -D <package-name>
sail exec laravel.test pnpm remove <package-name>
sail exec laravel.test pnpm update

# Limpiar y reinstalar dependencias
sail exec laravel.test pnpm run fresh-install

# Auditar vulnerabilidades
sail exec laravel.test pnpm audit
```

### Desarrollo Backend

```bash
# Comandos Artisan
sail artisan migrate
sail artisan migrate:fresh --seed
sail artisan make:controller <name>
sail artisan make:model <name>
sail artisan make:migration <name>

# Limpiar cachés
sail artisan config:clear
sail artisan route:clear
sail artisan cache:clear

# Ejecutar tests
sail artisan test
sail artisan test --coverage

# Acceder al contenedor
sail shell

# Ejecutar comandos PHP
sail php -v
sail php artisan tinker
```

### Base de Datos

```bash
# Conectar a PostgreSQL
sail psql

# Ejecutar migraciones
sail artisan migrate

# Rollback de migraciones
sail artisan migrate:rollback

# Refrescar base de datos con seeders
sail artisan migrate:fresh --seed

# Crear seeder
sail artisan make:seeder <name>

# Ejecutar seeder específico
sail artisan db:seed --class=<SeederName>
```

## Estructura de Archivos de Configuración

```
├── .env                    # Configuración principal
├── .env.sail              # Configuración específica para Sail
├── .npmrc                 # Configuración de pnpm
├── docker-compose.yml     # Configuración de Docker
├── package.json           # Dependencias y scripts de pnpm
├── setup.sh              # Script de configuración inicial
└── SAIL_DEVELOPMENT.md   # Esta guía
```

## URLs y Puertos Personalizados

- **Backend (Laravel)**: http://localhost:8001
- **Frontend (Vite Dev Server)**: http://localhost:5174
- **PostgreSQL**: localhost:5432
  - Base de datos: `laravel`
  - Usuario: `sail`
  - Contraseña: `password`

## Configuración de pnpm

### Archivo .npmrc

El proyecto incluye un archivo `.npmrc` con configuraciones optimizadas:

```ini
auto-install-peers=true
strict-peer-dependencies=false
shamefully-hoist=true
prefer-workspace-packages=true
registry=https://registry.npmjs.org/
store-dir=~/.pnpm-store
unsafe-perm=true
```

### Ventajas de pnpm

- **Eficiencia**: Usa enlaces simbólicos para ahorrar espacio
- **Velocidad**: Instalaciones más rápidas
- **Seguridad**: Mejor aislamiento de dependencias
- **Compatibilidad**: Compatible con npm y yarn

## Solución de Problemas

### Problemas Comunes

1. **Puerto 8001 ocupado**
   ```bash
   # Cambiar puerto en .env
   APP_PORT=8002
   sail down && sail up -d
   ```

2. **Puerto 5174 ocupado**
   ```bash
   # Cambiar puerto en .env y vite.config.js
   VITE_PORT=5175
   sail down && sail up -d
   ```

3. **pnpm no encontrado en el contenedor**
   ```bash
   # Instalar pnpm en el contenedor
   sail exec laravel.test npm install -g pnpm
   ```

4. **Problemas de permisos**
   ```bash
   # Verificar variables de usuario
   echo $WWWUSER
   echo $WWWGROUP
   
   # Reconstruir contenedores
   sail build --no-cache
   ```

5. **Base de datos no conecta**
   ```bash
   # Verificar que PostgreSQL esté ejecutándose
   sail ps
   
   # Ver logs de PostgreSQL
   sail logs pgsql
   
   # Recrear volumen de base de datos
   sail down -v
   sail up -d
   ```

6. **Dependencias Node.js desactualizadas**
   ```bash
   # Limpiar y reinstalar con pnpm
   sail exec laravel.test pnpm run clean
   sail exec laravel.test pnpm install
   ```

### Comandos de Limpieza

```bash
# Limpiar todo y empezar de nuevo
sail down -v
docker system prune -f
sail build --no-cache
sail up -d
sail artisan migrate:fresh --seed
sail exec laravel.test pnpm install
```

## Desarrollo con TypeScript

### Verificar Tipos

```bash
# Verificación de tipos en tiempo real
sail exec laravel.test pnpm run type-check

# Verificación con watch mode
sail exec laravel.test pnpm run type-check:watch
```

### Estructura de Tipos

```
resources/js/
├── types/
│   ├── index.ts          # Tipos principales
│   └── global.d.ts       # Declaraciones globales
├── Components/           # Componentes React (.tsx)
├── Pages/               # Páginas Inertia (.tsx)
└── Layouts/             # Layouts (.tsx)
```

## Arquitectura Hexagonal

### Estructura de Directorios

```
app/Src/
├── Application/
│   ├── DTOs/            # Data Transfer Objects
│   └── Services/        # Servicios de aplicación
├── Domain/
│   ├── Entities/        # Entidades del dominio
│   ├── Services/        # Servicios del dominio
│   └── ValueObjects/    # Objetos de valor
└── Infrastructure/
    ├── Controllers/     # Controladores HTTP
    ├── Repositories/    # Implementaciones de repositorios
    └── Requests/        # Form Requests
```

### Comandos para Crear Clases DDD

```bash
# Crear entidad de dominio
sail artisan make:class App/Src/Domain/Entities/UserEntity

# Crear servicio de aplicación
sail artisan make:class App/Src/Application/Services/UserService

# Crear repositorio
sail artisan make:class App/Src/Infrastructure/Repositories/UserRepository

# Crear controlador en la capa de infraestructura
sail artisan make:controller Src/Infrastructure/Controllers/UserController
```

## Scripts de pnpm Personalizados

### Scripts Disponibles

```json
{
  "scripts": {
    "dev": "vite --host 0.0.0.0 --port 5174",
    "dev:docker": "vite --host 0.0.0.0 --port 5174",
    "build": "vite build",
    "type-check": "tsc --noEmit",
    "type-check:watch": "tsc --noEmit --watch",
    "clean": "rm -rf node_modules pnpm-lock.yaml",
    "fresh-install": "pnpm clean && pnpm install"
  }
}
```

### Uso de Scripts

```bash
# Desarrollo
sail exec laravel.test pnpm run dev

# Verificación de tipos
sail exec laravel.test pnpm run type-check

# Limpiar e instalar
sail exec laravel.test pnpm run fresh-install
```

## Tips de Productividad

1. **Usar alias para comandos frecuentes**
   ```bash
   alias sa='sail artisan'
   alias sp='sail exec laravel.test pnpm'
   alias st='sail artisan test'
   alias sps='sail ps'
   ```

2. **Configurar IDE para TypeScript**
   - Instalar extensiones de TypeScript
   - Configurar path mapping para `@/`
   - Habilitar verificación de tipos en tiempo real

3. **Usar scripts pnpm personalizados**
   ```bash
   # Desarrollo con hot reload
   sail exec laravel.test pnpm run dev
   
   # Verificación continua de tipos
   sail exec laravel.test pnpm run type-check:watch
   ```

4. **Configurar Git hooks para calidad de código**
   ```bash
   # Pre-commit hook para verificar tipos
   sail exec laravel.test pnpm run type-check
   sail artisan test
   ```

## Monitoreo y Logs

### Ver logs en tiempo real

```bash
# Todos los servicios
sail logs -f

# Solo Laravel
sail logs -f laravel.test

# Solo PostgreSQL
sail logs -f pgsql
```

### Monitorear recursos

```bash
# Ver estado de contenedores
sail ps

# Ver uso de recursos
docker stats

# Ver volúmenes
docker volume ls
```

## Configuración de Vite para Docker

El archivo `vite.config.js` está configurado para funcionar correctamente en Docker:

```javascript
export default defineConfig({
    // ... otras configuraciones
    server: {
        host: '0.0.0.0',
        port: 5174,
        hmr: {
            host: 'localhost',
            port: 5174,
        },
    },
});
```

Esta configuración permite:
- Acceso desde fuera del contenedor
- Hot Module Replacement (HMR) funcionando
- Puerto personalizado 5174