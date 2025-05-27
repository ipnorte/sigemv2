#!/bin/bash

# Archivo ZIP de salida
LOCAL_ZIP_PATH="sigemv2-deploy.zip"

# Directorio base donde están los archivos dentro de CakePHP
BASE_DIR="app"

# Lista de servidores (alias SSH definidos en ~/.ssh/config)
SERVERS=(
    # "ipnorte.ar"
    # "soluciones"
    # "platilandia"
    # "ryvsa"
    "mutualaman"
)

# Rutas remotas
REMOTE_PATH="release"
TARGET_DIR="$REMOTE_PATH/sigemv2"
UPDATE_SCRIPT="$REMOTE_PATH/sigemv2.sh"

# Archivos y directorios a incluir (sin prefijo 'app/')
INCLUDES=(
    "plugins"
    "vendors"
    "views"
    "controllers"
    "models"
    "app_controller.php"
    "app_model.php"
    "webroot/img/"
    "webroot/css/"
    "webroot/js/"
    "webroot/asincrono.php"
    "webroot/process.php"
)

# Función para verificar si los archivos/directorios existen
check_files() {
    for item in "${INCLUDES[@]}"; do
        if [[ ! -e "$BASE_DIR/$item" ]]; then
            echo "⚠️ Advertencia: '$BASE_DIR/$item' no existe y no se incluirá en el ZIP."
        fi
    done
}

# Verificar archivos antes de comprimir
check_files

# Crear el archivo ZIP sin incluir la carpeta 'app/' en la estructura
echo "📦 Creando archivo ZIP: $LOCAL_ZIP_PATH..."
(cd "$BASE_DIR" && zip -r "../$LOCAL_ZIP_PATH" "${INCLUDES[@]}") > /dev/null 2>&1

if [[ $? -ne 0 ]]; then
    echo "❌ Error: No se pudo crear el archivo ZIP."
    exit 1
fi
echo "✅ Archivo ZIP creado exitosamente: $LOCAL_ZIP_PATH"

# Iterar sobre cada servidor en la lista
for SERVER in "${SERVERS[@]}"; do
    echo "🚀 Desplegando en el servidor: $SERVER..."

    echo "🔍 Verificando conexión SSH a $SERVER..."
    ssh -q "$SERVER" exit
    if [[ $? -ne 0 ]]; then
        echo "❌ Error: No se puede conectar al servidor $SERVER."
        exit 1
    fi
    echo "✅ Conexión SSH verificada en $SERVER."

    echo "📤 Copiando $LOCAL_ZIP_PATH al servidor $SERVER..."
    scp "$LOCAL_ZIP_PATH" "$SERVER:$REMOTE_PATH/"

    if [[ $? -ne 0 ]]; then
        echo "❌ Error: No se pudo copiar el archivo ZIP al servidor $SERVER."
        exit 1
    fi
    echo "✅ Archivo ZIP copiado exitosamente en $SERVER."

    echo "🔄 Conectando a $SERVER para descomprimir y actualizar..."
    ssh "$SERVER" << EOF
set -e

echo "🗑️ Eliminando el directorio existente: $TARGET_DIR"
rm -rf "$TARGET_DIR" && sync

echo "📂 Creando el directorio limpio: $TARGET_DIR"
mkdir -p "$TARGET_DIR"

if [[ ! -f "$REMOTE_PATH/$LOCAL_ZIP_PATH" ]]; then
    echo "❌ Error: El archivo ZIP no se encuentra en el servidor."
    exit 1
fi

echo "📦 Descomprimiendo en $TARGET_DIR..."
unzip -o "$REMOTE_PATH/$LOCAL_ZIP_PATH" -d "$TARGET_DIR"

if [[ \$? -ne 0 ]]; then
    echo "❌ Error: No se pudo descomprimir el archivo ZIP en el servidor."
    exit 1
fi
echo "✅ Descompresión completada."

if [[ ! -f "$UPDATE_SCRIPT" ]]; then
    echo "❌ Error: No se encontró el script de actualización $UPDATE_SCRIPT"
    exit 1
fi

echo "🛠️ Ejecutando script de actualización: $UPDATE_SCRIPT..."
chmod +x "$UPDATE_SCRIPT"
"$UPDATE_SCRIPT"

if [[ \$? -ne 0 ]]; then
    echo "❌ Error: El script de actualización falló."
    exit 1
fi
echo "✅ Script de actualización ejecutado correctamente."

echo "🗑️ Eliminando el archivo ZIP del servidor..."
rm -f "$REMOTE_PATH/$LOCAL_ZIP_PATH"
echo "✅ Archivo ZIP eliminado."
EOF

    if [[ $? -eq 0 ]]; then
        echo "🎉 Despliegue completado con éxito en $SERVER."
    else
        echo "❌ Hubo un problema en el proceso de despliegue en $SERVER."
    fi

    echo "-----------------------------------------"
done

echo "✅ Despliegue completado en todos los servidores."
