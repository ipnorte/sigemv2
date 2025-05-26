#!/bin/bash

# Ruta base donde están ubicados los distintos homes
BASE_PATH="/var/www/clients"

# Lista de clientes con su respectivo directorio web
CLIENTES_WEB=("client1/web1")

# Ruta fuente de la actualización
SOURCE_DIR="$HOME/release/sigemv2"

echo ""
echo "-----------------------------------"
echo "Iniciando Actualización SIGEM"
echo "-----------------------------------"
echo ""

# Iterar sobre cada cliente/webX
for CLIENT_WEB in "${CLIENTES_WEB[@]}"; do
    DEST_DIR="${BASE_PATH}/${CLIENT_WEB}/web"

    echo "Actualizando $DEST_DIR"

    # Validar existencia del destino
    if [ ! -d "$DEST_DIR" ]; then
        echo "❌ ERROR: No existe el directorio $DEST_DIR. Saltando..."
        echo "-----------------------------------"
        continue
    fi

    # Sincronizar archivos y carpetas con rsync sin eliminar archivos en el destino
    rsync -a "$SOURCE_DIR/plugins/" "$DEST_DIR/app/plugins/"
    rsync -a "$SOURCE_DIR/vendors/" "$DEST_DIR/app/vendors/"
    rsync -a "$SOURCE_DIR/views/" "$DEST_DIR/app/views/"
    rsync -a "$SOURCE_DIR/controllers/" "$DEST_DIR/app/controllers/"
    rsync -a "$SOURCE_DIR/models/" "$DEST_DIR/app/models/"
    rsync -a "$SOURCE_DIR/app_controller.php" "$DEST_DIR/app/"
    rsync -a "$SOURCE_DIR/app_model.php" "$DEST_DIR/app/"

    # Sincronizar archivos de webroot
    rsync -a "$SOURCE_DIR/webroot/img/" "$DEST_DIR/app/webroot/img/"
    rsync -a "$SOURCE_DIR/webroot/css/" "$DEST_DIR/app/webroot/css/"
    rsync -a "$SOURCE_DIR/webroot/js/" "$DEST_DIR/app/webroot/js/"
    rsync -a "$SOURCE_DIR/webroot/asincrono.php" "$DEST_DIR/app/webroot/"
    rsync -a "$SOURCE_DIR/webroot/process.php" "$DEST_DIR/app/webroot/"

    echo "✅ Actualización completada en $DEST_DIR"
    echo "-----------------------------------"
done

echo ""
echo "✔ Actualización finalizada en todos los destinos."
echo "-----------------------------------"
