#!/bin/bash

# Script de configuración inicial para Laravel Sail con pnpm
# Este script automatiza la configuración inicial del proyecto

echo "🚀 Configurando proyecto Laravel con Sail y pnpm..."

# Verificar si Docker está ejecutándose
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker no está ejecutándose. Por favor, inicia Docker Desktop."
    exit 1
fi

# Verificar si composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer no está instalado. Por favor, instala Composer primero."
    exit 1
fi

# Verificar si pnpm está instalado
if ! command -v pnpm &> /dev/null; then
    echo "⚠️  pnpm no está instalado. Instalando pnpm..."
    npm install -g pnpm
fi

echo "📦 Instalando dependencias PHP..."
composer install

echo "🔑 Generando clave de aplicación..."
php artisan key:generate

echo "🐳 Levantando contenedores Docker..."
./vendor/bin/sail up -d

echo "⏳ Esperando que los servicios estén listos..."
sleep 15

echo "🗄️ Ejecutando migraciones de base de datos..."
./vendor/bin/sail artisan migrate

echo "📦 Instalando dependencias Node.js con pnpm..."
./vendor/bin/sail exec laravel.test pnpm install

echo "🎨 Compilando assets para desarrollo..."
./vendor/bin/sail exec laravel.test pnpm run dev &

echo "✅ ¡Configuración completada!"
echo ""
echo "🌐 URLs de la aplicación:"
echo "  - Backend (Laravel): http://localhost:8001"
echo "  - Frontend (Vite): http://localhost:5174"
echo "  - Base de datos: localhost:5432"
echo ""
echo "📋 Comandos útiles:"
echo "  - Levantar contenedores: ./vendor/bin/sail up -d"
echo "  - Detener contenedores: ./vendor/bin/sail down"
echo "  - Ver logs: ./vendor/bin/sail logs"
echo "  - Acceder al contenedor: ./vendor/bin/sail shell"
echo "  - Ejecutar Artisan: ./vendor/bin/sail artisan <command>"
echo "  - Ejecutar pnpm: ./vendor/bin/sail exec laravel.test pnpm <command>"
echo ""
echo "💡 Tip: Crea un alias para sail:"
echo "  alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'"
echo ""
echo "🔧 Para desarrollo frontend:"
echo "  ./vendor/bin/sail exec laravel.test pnpm run dev"