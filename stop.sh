#!/bin/bash

# Municipal Document Management System - Stop Script
echo "🛑 Deteniendo Sistema Municipal de Gestión de Documentos"

# Stop and remove containers
echo "📦 Deteniendo contenedores Docker..."
docker-compose down

# Optionally remove volumes (uncomment if you want to reset data)
# echo "🗑️  Eliminando volúmenes de datos..."
# docker-compose down -v

echo "✅ Sistema detenido correctamente!"
echo ""
echo "Para reiniciar el sistema: ./start.sh"
echo "Para eliminar todos los datos: docker-compose down -v"