# Configuración Rápida - Laravel Sail + pnpm

## 🚀 Inicio Rápido

```bash
# 1. Configuración automática
./setup.sh

# 2. Crear alias (opcional pero recomendado)
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'

# 3. URLs del proyecto
# Backend:  http://localhost:8001
# Frontend: http://localhost:5174
```

## 📋 Comandos Esenciales

### Gestión de Contenedores
```bash
sail up -d          # Levantar servicios
sail down           # Detener servicios
sail logs -f        # Ver logs en tiempo real
```

### Frontend (pnpm)
```bash
./pnpm.sh dev       # Servidor de desarrollo
./pnpm.sh build     # Build producción
./pnpm.sh install   # Instalar dependencias
./pnpm.sh add <pkg> # Agregar paquete
```

### Backend (Laravel)
```bash
sail artisan migrate        # Ejecutar migraciones
sail artisan test          # Ejecutar tests
sail artisan make:model    # Crear modelo
```

## 🔧 Scripts Útiles

- `./setup.sh` - Configuración inicial completa
- `./pnpm.sh` - Gestión de paquetes Node.js
- `sail` - Comandos de Docker/Laravel

## 🌐 Puertos Configurados

- **8001**: Backend Laravel
- **5174**: Frontend Vite
- **5432**: PostgreSQL

## 📁 Estructura Clave

```
├── app/Src/                 # Arquitectura hexagonal
│   ├── Application/         # Casos de uso
│   ├── Domain/             # Lógica de negocio
│   └── Infrastructure/     # Adaptadores
├── resources/js/           # Frontend React + TypeScript
├── docker-compose.yml      # Configuración Docker
└── .env                    # Variables de ambiente
```