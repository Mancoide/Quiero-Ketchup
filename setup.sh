#!/bin/bash

echo "========================================="
echo "  Contabot - Setup Inicial"
echo "========================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Verificar si .env existe
if [ ! -f .env ]; then
    echo "📋 Creando archivo .env..."
    cp .env.example .env
    echo -e "${GREEN}✓ Archivo .env creado${NC}"
else
    echo -e "${YELLOW}⚠ El archivo .env ya existe${NC}"
fi

echo ""
echo "🐳 Levantando contenedores Docker..."
docker compose up -d

echo ""
echo "⏳ Esperando a que los servicios estén listos..."
sleep 15

echo ""
echo "📦 Instalando dependencias de PHP..."
docker compose exec -T app composer install

echo ""
echo "🔑 Generando APP_KEY..."
docker compose exec -T app php artisan key:generate

echo ""
echo "🗄️ Ejecutando migraciones..."
docker compose exec -T app php artisan migrate --force

echo ""
echo "✨ Instalando dependencias de Node..."
docker compose exec -T app npm install

echo ""
echo "🏗️ Compilando assets..."
docker compose exec -T app npm run build

echo ""
echo "========================================="
echo -e "${GREEN}✓ ¡Contabot está listo!${NC}"
echo "========================================="
echo ""
echo "📍 Accesible en: http://localhost:8000"
echo "🔍 API Health: http://localhost:8000/api/health"
echo ""
echo "💡 Comandos útiles:"
echo "  docker compose logs -f app      # Ver logs"
echo "  docker compose exec app bash    # Entrar al contenedor"
echo "  docker compose down -v          # Detener y limpiar"
echo ""
