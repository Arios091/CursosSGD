#!/bin/bash
# ============================================
# Build Script para Render - CursosSGD
# ============================================

set -e

echo "============================================"
echo "Iniciando build..."
echo "============================================"

# Instalar dependencias de Node
echo "Instalando dependencias de Node..."
curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
apt-get install -y nodejs
npm install

# Compilar assets (CRÍTICO)
echo "Compilando assets..."
npm run prod

# Verificar que se creó
if [ -d "public/build" ]; then
    echo "✓ public/build existe"
    ls -la public/build/
elif [ -d "public/css" ]; then
    echo "✓ public/css existe"
    ls -la public/css/
else
    echo "✗ AVISO: Assets no se compilaron"
fi

echo "============================================"
echo "Build completado!"
echo "============================================"