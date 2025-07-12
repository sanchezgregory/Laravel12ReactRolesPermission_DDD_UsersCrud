# Configuración para Laravel Sail
# Este archivo contiene variables específicas para el ambiente de desarrollo con Docker

# Configuración de la aplicación
APP_NAME="Laravel DDD Project"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001

# Configuración de Sail
SAIL_XDEBUG_MODE=develop,debug
SAIL_XDEBUG_CONFIG="client_host=host.docker.internal"

# Puertos para desarrollo
APP_PORT=8001
VITE_PORT=5174
FORWARD_DB_PORT=5432

# Configuración de base de datos PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# Configuración de sesiones y caché
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Configuración de correo para desarrollo
MAIL_MAILER=log

# Variables para Docker
WWWGROUP=1000
WWWUSER=1000