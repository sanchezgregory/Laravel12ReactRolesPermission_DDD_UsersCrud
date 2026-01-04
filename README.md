# Laravel 12 + React + InertiaJS + TailwindCSS + TypeScript con Laravel Sail

Este proyecto implementa una aplicación web moderna usando Laravel 12 como backend con arquitectura hexagonal DDD, y React con TypeScript como frontend, todo ejecutándose en contenedores Docker con Laravel Sail y gestionado con pnpm.

---

## 📚 Documentación Rápida

### 🎯 DOCUMENTO PRINCIPAL

**[📖 COMPLETE_DOCUMENTATION.md](./COMPLETE_DOCUMENTATION.md)** - **¡TODA LA DOCUMENTACIÓN EN UN SOLO ARCHIVO!**
- ✅ **4,955 líneas** de documentación completa
- ✅ **80+ ejemplos** de código
- ✅ **20+ diagramas** visuales
- ✅ **10+ plantillas** listas para usar
- ✅ Navegación por índice maestro
- ✅ Búsqueda rápida por palabras clave

### 📑 Documentos Individuales (Opcional)

Si prefieres consultar por partes:

| Documento | Descripción | Líneas | Ideal Para |
|-----------|-------------|--------|------------|
| [📘 DEVELOPER_GUIDE.md](./DEVELOPER_GUIDE.md) | Guía completa de arquitectura hexagonal | 1,968 | Entender el proyecto |
| [🔄 ARCHITECTURE_FLOWS.md](./ARCHITECTURE_FLOWS.md) | Diagramas y flujos de datos | 853 | Visualizar cómo funciona |
| [⚡ QUICK_REFERENCE.md](./QUICK_REFERENCE.md) | Plantillas y checklist | 1,500+ | Desarrollar rápido |
| [🎨 ARCHITECTURE_VISUAL.md](./ARCHITECTURE_VISUAL.md) | Diagramas visuales ASCII | 634 | Ver la arquitectura |
| [📖 DOCUMENTATION_INDEX.md](./DOCUMENTATION_INDEX.md) | Índice de navegación | 400+ | Navegar la documentación |
| [🚀 QUICK_START.md](./QUICK_START.md) | Configuración inicial | 100+ | Primer setup |

---

## Stack Tecnológico

- **Backend**: Laravel 12 con PHP 8.4
- **Frontend**: React 18 con TypeScript
- **Routing**: InertiaJS 2.0
- **Styling**: TailwindCSS 3.2+
- **Build Tool**: Vite 6.2+
- **Package Manager**: pnpm 9.0+
- **Arquitectura**: Hexagonal DDD (Domain Driven Design)
- **Ambiente Local**: Laravel Sail (Docker)
- **Base de Datos**: PostgreSQL 17


## Estructura del Proyecto

### Backend (Arquitectura Hexagonal DDD)

```
app/Src/
├── Application/          # Capa de aplicación
│   ├── DTOs/            # Data Transfer Objects
│   └── Services/        # Servicios de aplicación
├── Domain/              # Capa de dominio
│   ├── Entities/        # Entidades del dominio
│   ├── Services/        # Servicios del dominio
│   └── ValueObjects/    # Objetos de valor
└── Infrastructure/      # Capa de infraestructura
    ├── Controllers/     # Controladores HTTP
    ├── Middleware/      # Middleware personalizado
    ├── Providers/       # Service Providers
    ├── Repositories/    # Implementaciones de repositorios
    └── Requests/        # Form Requests
    └── Services/        # Servicios de infraestructura
```

### Frontend (React + TypeScript)

```
resources/js/
├── Components/          # Componentes reutilizables
├── Layouts/            # Layouts de la aplicación
├── Pages/              # Páginas de Inertia
├── types/              # Definiciones de tipos TypeScript
├── app.tsx             # Punto de entrada de la aplicación
└── bootstrap.js        # Configuración inicial
```

## Instalación y Configuración

### Prerrequisitos

- Docker Desktop
- Git
- pnpm (se instala automáticamente si no está presente)

### 1. Clonar el repositorio

```bash
git clone <repository-url>
cd <project-name>
```

### 2. Configurar el entorno

```bash
# Copiar archivo de configuración
cp .env.sail .env

# El archivo .env ya está configurado para Sail con PostgreSQL
```

### 3. Crear alias para Sail (Recomendado)

Para macOS/Linux, agregar al archivo `~/.bashrc` o `~/.zshrc`:

```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

Para Windows (PowerShell), agregar al perfil:

```powershell
function sail { sh $(if (Test-Path sail) { "sail" } else { "vendor/bin/sail" }) @args }
```

Reiniciar la terminal o ejecutar:

```bash
source ~/.bashrc  # o ~/.zshrc
```

### 4. Instalar pnpm globalmente (si no está instalado)

```bash
npm install -g pnpm
```

### 5. Configuración automática

```bash
# Ejecutar script de configuración (recomendado)
./setup.sh
```

### 6. Configuración manual (alternativa)

```bash
# Instalar dependencias PHP
composer install

# Levantar los contenedores (primera vez puede tomar varios minutos)
sail up -d

# Generar clave de aplicación
sail artisan key:generate

# Ejecutar migraciones
sail artisan migrate

# Instalar dependencias Node.js con pnpm
sail exec laravel.test pnpm install

# Compilar assets para desarrollo
sail exec laravel.test pnpm run dev
```

## Comandos de Desarrollo

### Comandos de Sail (Docker)

```bash
# Levantar contenedores
sail up -d

# Detener contenedores
sail down

# Ver logs
sail logs

# Acceder al contenedor de la aplicación
sail shell

# Ejecutar comandos Artisan
sail artisan <command>

# Ejecutar comandos pnpm
sail exec laravel.test pnpm <command>

# Ejecutar Composer
sail composer <command>
```

### Comandos de Frontend con pnpm

```bash
# Desarrollo con hot reload
sail exec laravel.test pnpm run dev

# Build de producción
sail exec laravel.test pnpm run build

# Verificación de tipos TypeScript
sail exec laravel.test pnpm run type-check

# Verificación de tipos en modo watch
sail exec laravel.test pnpm run type-check:watch

# Instalar nueva dependencia
sail exec laravel.test pnpm add <package-name>

# Instalar dependencia de desarrollo
sail exec laravel.test pnpm add -D <package-name>

# Limpiar node_modules y reinstalar
sail exec laravel.test pnpm run fresh-install
```

### Comandos de Backend

```bash
# Migraciones
sail artisan migrate
sail artisan migrate:fresh --seed

# Limpiar cachés
sail artisan config:clear
sail artisan route:clear
sail artisan cache:clear

# Generar clases
sail artisan make:controller <name>
sail artisan make:model <name>
sail artisan make:migration <name>
```

## URLs del Proyecto

- **Backend (Laravel)**: http://localhost:8001
- **Frontend (Vite Dev Server)**: http://localhost:5174
- **Base de Datos**: 
  - Host: localhost
  - Puerto: 5432
  - Base de datos: laravel
  - Usuario: sail
  - Contraseña: password

## Desarrollo con TypeScript

### Configuración Actual

- ✅ TypeScript configurado con React
- ✅ Tipos para Inertia.js
- ✅ Alias `@/` para imports
- ✅ Configuración de Vite optimizada para Docker
- ✅ pnpm como gestor de paquetes

### Convenciones

- Usar archivos `.tsx` para componentes React
- Definir interfaces para props de componentes
- Usar el alias `@/` para importaciones desde `resources/js/`
- Tipos globales en `resources/js/types/`

## Arquitectura Hexagonal

### Principios Implementados

- **Dominio**: Lógica de negocio pura sin dependencias externas
- **Aplicación**: Orquestación de casos de uso
- **Infraestructura**: Adaptadores para servicios externos

### Estructura de Namespaces

```
App\Src\
├── Application\         # Casos de uso y servicios de aplicación
├── Domain\             # Entidades, value objects y servicios de dominio
└── Infrastructure\     # Controladores, repositorios y adaptadores
```

## Testing

```bash
# Ejecutar tests
sail artisan test

# Tests con coverage
sail artisan test --coverage

# Tests específicos
sail artisan test --filter=<test-name>
```

## Gestión de Paquetes con pnpm

### Ventajas de pnpm

- **Eficiencia de espacio**: Usa enlaces simbólicos para evitar duplicación
- **Velocidad**: Instalaciones más rápidas que npm/yarn
- **Seguridad**: Mejor aislamiento de dependencias
- **Compatibilidad**: Compatible con npm y yarn

### Comandos útiles de pnpm

```bash
# Instalar todas las dependencias
sail exec laravel.test pnpm install

# Agregar dependencia
sail exec laravel.test pnpm add <package>

# Agregar dependencia de desarrollo
sail exec laravel.test pnpm add -D <package>

# Actualizar dependencias
sail exec laravel.test pnpm update

# Remover dependencia
sail exec laravel.test pnpm remove <package>

# Listar dependencias
sail exec laravel.test pnpm list

# Auditar vulnerabilidades
sail exec laravel.test pnpm audit
```

## Características Implementadas

- ✅ Laravel 12 con PHP 8.4
- ✅ React 18 con TypeScript
- ✅ InertiaJS para SPA sin API
- ✅ TailwindCSS para estilos
- ✅ Arquitectura hexagonal DDD
- ✅ Autenticación con Laravel Breeze
- ✅ Laravel Sail para desarrollo local
- ✅ PostgreSQL como base de datos
- ✅ Configuración de Vite optimizada para Docker
- ✅ pnpm como gestor de paquetes
- ✅ Puertos personalizados (8001 backend, 5174 frontend)
- ✅ Tipos TypeScript para Inertia

## Solución de Problemas

### Problemas Comunes

1. **Puertos ocupados**: Los puertos están configurados como 8001 y 5174
2. **Permisos de Docker**: Asegurar que Docker Desktop esté ejecutándose
3. **Dependencias Node**: Ejecutar `sail exec laravel.test pnpm install`
4. **pnpm no encontrado**: Instalar globalmente con `npm install -g pnpm`

### Comandos de Limpieza

```bash
# Reconstruir contenedores
sail down
sail build --no-cache
sail up -d

# Limpiar volúmenes de Docker
sail down -v

# Limpiar dependencias Node.js
sail exec laravel.test pnpm run clean
sail exec laravel.test pnpm install
```

## Configuración de Puertos

El proyecto está configurado con puertos específicos para evitar conflictos:

- **Backend Laravel**: Puerto 8001
- **Frontend Vite**: Puerto 5174
- **PostgreSQL**: Puerto 5432 (estándar)

Estos puertos se pueden cambiar en los archivos `.env` y `docker-compose.yml`.

## Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📚 Documentación del Proyecto

Este proyecto cuenta con documentación completa para facilitar el desarrollo:

### Guías Principales

1. **[DEVELOPER_GUIDE.md](./DEVELOPER_GUIDE.md)** - Guía completa del desarrollador
   - Arquitectura Hexagonal explicada en detalle
   - Estructura de capas (Domain, Application, Infrastructure)
   - Patrones y conceptos clave (DI, Repository, Decorator)
   - Guía paso a paso para crear nuevos features
   - Ejemplos prácticos y mejores prácticas
   - Troubleshooting común

2. **[ARCHITECTURE_FLOWS.md](./ARCHITECTURE_FLOWS.md)** - Diagramas y flujos
   - Flujo completo de requests
   - Flujo de creación de entidades
   - Capas de validación
   - Dependency Injection en acción
   - Patrón Decorator para caché
   - Manejo de errores y excepciones

3. **[QUICK_REFERENCE.md](./QUICK_REFERENCE.md)** - Referencia rápida
   - Checklist completo para nuevos features
   - Plantillas de código listas para usar
   - Comandos útiles (Sail, Artisan, pnpm)
   - Estructura de archivos
   - Convenciones de nomenclatura

### Documentación Adicional

- **[QUICK_START.md](./QUICK_START.md)** - Inicio rápido del proyecto
- **[SAIL_DEVELOPMENT.md](./SAIL_DEVELOPMENT.md)** - Desarrollo con Laravel Sail

### 🎯 Por Dónde Empezar

**¿Eres nuevo en el proyecto?**
1. Lee [QUICK_START.md](./QUICK_START.md) para configurar el entorno
2. Revisa [DEVELOPER_GUIDE.md](./DEVELOPER_GUIDE.md) para entender la arquitectura
3. Consulta [ARCHITECTURE_FLOWS.md](./ARCHITECTURE_FLOWS.md) para ver los flujos en acción

**¿Vas a crear un nuevo feature?**
1. Usa el checklist en [QUICK_REFERENCE.md](./QUICK_REFERENCE.md)
2. Copia las plantillas de código necesarias
3. Sigue el ejemplo paso a paso en [DEVELOPER_GUIDE.md](./DEVELOPER_GUIDE.md)

**¿Necesitas resolver un problema?**
1. Consulta la sección Troubleshooting en [DEVELOPER_GUIDE.md](./DEVELOPER_GUIDE.md)
2. Revisa los comandos útiles en [QUICK_REFERENCE.md](./QUICK_REFERENCE.md)

---

## Próximos Pasos

1. Convertir componentes restantes a TypeScript
2. Implementar casos de uso específicos en la capa de aplicación
3. Crear entidades y value objects del dominio
4. Implementar repositorios en la capa de infraestructura
5. Agregar tests unitarios y de integración
6. Configurar CI/CD con GitHub Actions