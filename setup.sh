#!/bin/bash

# Municipal Document Management System - Initial Setup Script
echo "🏛️  Configuración inicial del Sistema Municipal de Gestión de Documentos"

# Check prerequisites
echo "🔍 Verificando prerequisitos..."

# Check Docker
if ! command -v docker > /dev/null 2>&1; then
    echo "❌ Docker no está instalado. Por favor, instala Docker primero."
    echo "   https://docs.docker.com/get-docker/"
    exit 1
fi

# Check Docker Compose
if ! command -v docker-compose > /dev/null 2>&1; then
    echo "❌ Docker Compose no está instalado. Por favor, instala Docker Compose primero."
    echo "   https://docs.docker.com/compose/install/"
    exit 1
fi

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está ejecutándose. Por favor, inicia Docker primero."
    exit 1
fi

echo "✅ Prerequisitos verificados"

# Create environment file
if [ ! -f .env ]; then
    echo "📝 Creando archivo de configuración..."
    cp .env.example .env
    
    # Generate Laravel application key
    APP_KEY=$(openssl rand -base64 32)
    sed -i "s/APP_KEY=/APP_KEY=base64:$APP_KEY/" .env
    
    echo "⚠️  IMPORTANTE: Configura las siguientes variables en el archivo .env:"
    echo "   - DB_PASSWORD (contraseña de la base de datos)"
    echo "   - MAIL_* (configuración de correo)"
    echo "   - OPENAI_API_KEY (si planeas usar OpenAI)"
fi

# Create necessary directories and set permissions
echo "📁 Creando estructura de directorios..."
mkdir -p {laravel/{storage/{app/public,logs,framework/{cache,sessions,views}},bootstrap/cache},python-ai/{models,temp,logs},nginx/sites,ssl,scripts}

# Set executable permissions
chmod +x start.sh stop.sh python-ai/start.sh

# Create nginx configuration
echo "🌐 Creando configuración de Nginx..."
cat > nginx/sites/default.conf << 'EOF'
server {
    listen 80;
    server_name localhost;
    
    location / {
        proxy_pass http://laravel:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    location /api/ai/ {
        proxy_pass http://python-ai:8000/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
EOF

# Create main nginx.conf
cat > nginx/nginx.conf << 'EOF'
events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    sendfile on;
    keepalive_timeout 65;
    
    client_max_body_size 100M;
    
    include /etc/nginx/conf.d/*.conf;
}
EOF

echo "🔧 Construyendo imágenes Docker..."
docker-compose build

echo "✅ Configuración inicial completada!"
echo ""
echo "Próximos pasos:"
echo "1. Revisa y configura el archivo .env"
echo "2. Ejecuta: ./start.sh para iniciar el sistema"
echo "3. Accede a http://localhost:8080"
echo ""
echo "📚 Documentación adicional en README.md"