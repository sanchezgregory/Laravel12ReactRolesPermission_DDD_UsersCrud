#!/bin/bash

# Script de gestión de pnpm para Laravel Sail
# Facilita el uso de pnpm dentro del contenedor Docker

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar ayuda
show_help() {
    echo -e "${BLUE}🔧 Script de gestión de pnpm para Laravel Sail${NC}"
    echo ""
    echo "Uso: ./pnpm.sh [comando]"
    echo ""
    echo "Comandos disponibles:"
    echo "  install, i          - Instalar dependencias"
    echo "  add <package>       - Agregar dependencia"
    echo "  add-dev <package>   - Agregar dependencia de desarrollo"
    echo "  remove <package>    - Remover dependencia"
    echo "  update              - Actualizar dependencias"
    echo "  dev                 - Ejecutar servidor de desarrollo"
    echo "  build               - Build para producción"
    echo "  type-check          - Verificar tipos TypeScript"
    echo "  type-check:watch    - Verificar tipos en modo watch"
    echo "  clean               - Limpiar node_modules"
    echo "  fresh               - Limpiar e instalar desde cero"
    echo "  audit               - Auditar vulnerabilidades"
    echo "  list                - Listar dependencias"
    echo "  outdated            - Mostrar dependencias desactualizadas"
    echo "  help, -h, --help    - Mostrar esta ayuda"
    echo ""
    echo "Ejemplos:"
    echo "  ./pnpm.sh install"
    echo "  ./pnpm.sh add react-router-dom"
    echo "  ./pnpm.sh add-dev @types/node"
    echo "  ./pnpm.sh dev"
}

# Verificar si Sail está ejecutándose
check_sail() {
    if ! ./vendor/bin/sail ps | grep -q "laravel.test"; then
        echo -e "${RED}❌ Error: Los contenedores de Sail no están ejecutándose.${NC}"
        echo -e "${YELLOW}💡 Ejecuta: ./vendor/bin/sail up -d${NC}"
        exit 1
    fi
}

# Ejecutar comando pnpm en el contenedor
run_pnpm() {
    ./vendor/bin/sail exec laravel.test pnpm "$@"
}

# Función principal
main() {
    case "$1" in
        "install"|"i")
            echo -e "${BLUE}📦 Instalando dependencias con pnpm...${NC}"
            check_sail
            run_pnpm install
            ;;
        "add")
            if [ -z "$2" ]; then
                echo -e "${RED}❌ Error: Especifica el nombre del paquete${NC}"
                echo "Uso: ./pnpm.sh add <package>"
                exit 1
            fi
            echo -e "${BLUE}➕ Agregando dependencia: $2${NC}"
            check_sail
            run_pnpm add "$2"
            ;;
        "add-dev")
            if [ -z "$2" ]; then
                echo -e "${RED}❌ Error: Especifica el nombre del paquete${NC}"
                echo "Uso: ./pnpm.sh add-dev <package>"
                exit 1
            fi
            echo -e "${BLUE}➕ Agregando dependencia de desarrollo: $2${NC}"
            check_sail
            run_pnpm add -D "$2"
            ;;
        "remove")
            if [ -z "$2" ]; then
                echo -e "${RED}❌ Error: Especifica el nombre del paquete${NC}"
                echo "Uso: ./pnpm.sh remove <package>"
                exit 1
            fi
            echo -e "${YELLOW}➖ Removiendo dependencia: $2${NC}"
            check_sail
            run_pnpm remove "$2"
            ;;
        "update")
            echo -e "${BLUE}🔄 Actualizando dependencias...${NC}"
            check_sail
            run_pnpm update
            ;;
        "dev")
            echo -e "${GREEN}🚀 Iniciando servidor de desarrollo en puerto 5174...${NC}"
            echo -e "${BLUE}🌐 Frontend disponible en: http://localhost:5174${NC}"
            check_sail
            run_pnpm run dev
            ;;
        "build")
            echo -e "${BLUE}🏗️  Construyendo para producción...${NC}"
            check_sail
            run_pnpm run build
            ;;
        "type-check")
            echo -e "${BLUE}🔍 Verificando tipos TypeScript...${NC}"
            check_sail
            run_pnpm run type-check
            ;;
        "type-check:watch")
            echo -e "${BLUE}👀 Verificando tipos TypeScript en modo watch...${NC}"
            check_sail
            run_pnpm run type-check:watch
            ;;
        "clean")
            echo -e "${YELLOW}🧹 Limpiando node_modules...${NC}"
            check_sail
            run_pnpm run clean
            ;;
        "fresh")
            echo -e "${YELLOW}🔄 Limpiando e instalando desde cero...${NC}"
            check_sail
            run_pnpm run fresh-install
            ;;
        "audit")
            echo -e "${BLUE}🔒 Auditando vulnerabilidades...${NC}"
            check_sail
            run_pnpm audit
            ;;
        "list")
            echo -e "${BLUE}📋 Listando dependencias...${NC}"
            check_sail
            run_pnpm list
            ;;
        "outdated")
            echo -e "${BLUE}📊 Verificando dependencias desactualizadas...${NC}"
            check_sail
            run_pnpm outdated
            ;;
        "help"|"-h"|"--help"|"")
            show_help
            ;;
        *)
            echo -e "${RED}❌ Comando no reconocido: $1${NC}"
            echo ""
            show_help
            exit 1
            ;;
    esac
}

# Ejecutar función principal con todos los argumentos
main "$@"