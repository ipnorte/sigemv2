#!/bin/bash

# Configuración de la base de datos
DB_USER="c1_socore"
DB_PASS="8T3SAQ3NDV76"
DB_NAME="c1_socore"
DB_HOST="localhost"

# Directorios
APP_DIR="/var/www/clients/client1/web1/home/ipn_adrian/web/socore/app"
BACKUP_DIR="$APP_DIR/plugins/seguridad/views/backups/files"
LOG_DIR="$APP_DIR/tmp/logs"
WWW_FILES_DIR="$APP_DIR/webroot/files"
SOLICITUDES_DIR="$WWW_FILES_DIR/solicitudes"

# Definir días de retención
RETENCION_DIAS=3

echo "=== Iniciando proceso de backup ==="

# 1️⃣ Conectar a MySQL y limpiar tablas temporales
echo "🗑 Limpiando tablas temporales..."
mysql -u"$DB_USER" -p"$DB_PASS" -h "$DB_HOST" "$DB_NAME" <<EOF
DELETE FROM asincrono_errores;
DELETE FROM asincrono_temporal_detalles;
DELETE FROM asincrono_temporales;
DELETE FROM asincronos WHERE DATEDIFF(NOW(),created) > 30;
EOF

# 2️⃣ Generar backup de la base de datos (Ejecutado TODOS los días)
echo "💾 Creando backup de la base de datos..."
BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_$(date +%Y%m%d).bz2"
mysqldump --no-create-db --routines --skip-comments --add-drop-table --single-transaction --quick --password="$DB_PASS" --user="$DB_USER" --host="$DB_HOST" "$DB_NAME" \
    | sed -E 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g' | bzip2 > "$BACKUP_FILE"

echo "✅ Backup de base de datos completado."

# 3️⃣ SOLO EJECUTAR LOS SÁBADOS
if [[ $(date +%u) -eq 6 ]]; then
    echo "=== Hoy es sábado, ejecutando limpieza adicional... ==="

    # 3.1️⃣ Borrar archivos temporales
    echo "🗑 Eliminando archivos temporales..."
    find "$WWW_FILES_DIR/reportes" -name "*.xls" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;
    find "$WWW_FILES_DIR/graphics" -name "*.png" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;

    # 3.2️⃣ Borrar backups antiguos
    echo "🗑 Eliminando backups anteriores a $RETENCION_DIAS días..."
    find "$BACKUP_DIR" -name "${DB_NAME}_*.bz2" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;
    find "$BACKUP_DIR" -name "sigem_auditoria_*.bz2" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;
    find "$BACKUP_DIR" -name "documentacion_solicitudes_*.tar.bz2" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;

    # 3.3️⃣ Comprimir logs y auditorías
    echo "🗄 Comprimendo logs de auditoría..."
    AUDIT_FILE="$BACKUP_DIR/sigem_auditoria_$(date +%Y%m%d).tar.bz2"
    tar -cjvf "$AUDIT_FILE" "$LOG_DIR"/*.log

    # Si se generó el archivo, limpiar logs
    if [[ -f "$AUDIT_FILE" ]]; then
        echo "🗑 Eliminando logs antiguos..."
        find "$LOG_DIR" -name "*.log" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;
    fi

    # 3.4️⃣ Borrar logs de SQL y errores
    echo "🗑 Eliminando logs SQL y errores..."
    find "$LOG_DIR" -name "SQL_*.log" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;
    find "$WWW_FILES_DIR/logs" -name "ERRORES_*.log" -mtime +$RETENCION_DIAS -type f -exec rm -f {} \;

    # 3.5️⃣ Backup de imágenes de solicitudes
    if [[ -d "$SOLICITUDES_DIR" ]]; then
        echo "📸 Generando backup de imágenes de solicitudes..."
        SOLICITUDES_BACKUP="$BACKUP_DIR/documentacion_solicitudes_$(date +%Y%m%d).tar.bz2"
        tar -cjvf "$SOLICITUDES_BACKUP" "$SOLICITUDES_DIR"
    fi

    echo "✅ Limpieza y backups adicionales completados."
else
    echo "⏳ No es sábado, se omiten limpieza de logs y archivos temporales."
fi

# 4️⃣ Copiar mutual.ini en la base de datos (Ejecutado TODOS los días)
echo "📄 Guardando mutual.ini en la base de datos..."
MUTUAL_INI=$(cat "$APP_DIR/config/mutual.ini" | sed 's/"/\\"/g')
mysql -u"$DB_USER" -p"$DB_PASS" -h "$DB_HOST" "$DB_NAME" <<EOF
UPDATE global_datos SET texto_1="$MUTUAL_INI" WHERE id='MUTU';
EOF

echo "✅ Proceso de backup finalizado correctamente."

