#!/bin/bash

# Municipal Document Management System - Startup Script
echo "🏛️  Iniciando Sistema Municipal de Gestión de Documentos"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "⚠️  Archivo .env no encontrado. Copiando desde .env.example..."
    cp .env.example .env
    echo "📝 Por favor, configura las variables de entorno en el archivo .env"
fi

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está ejecutándose. Por favor, inicia Docker primero."
    exit 1
fi

# Check if Docker Compose is available
if ! command -v docker-compose > /dev/null 2>&1; then
    echo "❌ Docker Compose no está instalado."
    exit 1
fi

# Create necessary directories
echo "📁 Creando directorios necesarios..."
mkdir -p laravel/storage/app/public
mkdir -p laravel/storage/logs
mkdir -p laravel/bootstrap/cache
mkdir -p python-ai/models
mkdir -p python-ai/temp
mkdir -p python-ai/logs
mkdir -p ssl

# Set permissions
echo "🔐 Configurando permisos..."
chmod -R 755 laravel/storage
chmod -R 755 laravel/bootstrap/cache
chmod +x python-ai/start.sh

# Start the services
echo "🚀 Iniciando servicios Docker..."
docker-compose up -d

# Wait for services to be ready
echo "⏳ Esperando que los servicios estén listos..."
sleep 10

# Check if services are running
echo "🔍 Verificando estado de los servicios..."
docker-compose ps

# Install Laravel if not present
if [ ! -f "laravel/composer.json" ]; then
    echo "📦 Instalando Laravel..."
    docker-compose exec laravel composer create-project laravel/laravel . --prefer-dist
    docker-compose exec laravel php artisan key:generate
fi

# Run database migrations
echo "🗄️  Ejecutando migraciones de base de datos..."
docker-compose exec laravel php artisan migrate --force

# Clear and cache configurations
echo "🧹 Limpiando y cacheando configuraciones..."
docker-compose exec laravel php artisan config:clear
docker-compose exec laravel php artisan config:cache
docker-compose exec laravel php artisan route:cache

echo "✅ Sistema iniciado correctamente!"
echo ""
echo "🌐 Aplicación web: http://localhost:8080"
echo "🤖 Servicio AI: http://localhost:8001"
echo "🗄️  Base de datos: localhost:5432"
echo ""
echo "Para detener el sistema: ./stop.sh"
echo "Para ver logs: docker-compose logs -f"