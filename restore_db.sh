#!/bin/bash

# Función para mostrar el uso del script
usage() {
  echo "Uso: $0 --file <nombre_del_archivo> [--database <nombre_de_la_base_de_datos>] [--dry-run]"
  exit 1
}

# Variables por defecto
DATABASE="sigem_db"
DRY_RUN=0

# Verificar y procesar los parámetros de entrada
while [[ "$#" -gt 0 ]]; do
  case $1 in
    --file)
      FILE="$2.bz2"
      shift
      ;;
    --database)
      DATABASE="$2"
      shift
      ;;
    --dry-run)
      DRY_RUN=1
      ;;
    *)
      usage
      ;;
  esac
  shift
done

# Verificar que el parámetro del archivo esté presente
if [[ -z "$FILE" ]]; then
  usage
fi

# Convertir la ruta del archivo a una ruta absoluta
FILE=$(realpath "$FILE")

# Variables
now=$(date +"%Y-%m-%d %H:%M:%S")

# Logging de inicio
echo "COMIENZO RESTORE: $now | Base de datos: $DATABASE"

# Verificar si el archivo existe
if [[ ! -f $FILE ]]; then
  echo "ERROR: El archivo $FILE no existe."
  exit 1
fi

# Descomprimir el archivo manualmente
bunzip2 -k "$FILE"
SQL_FILE="${FILE%.bz2}"

# Eliminar encabezados y líneas conflictivas, incluyendo la línea con '\-'
sed -i '/^--/d;/^\\-/d;/^\\./d;/^\\[a-zA-Z]/d;/^\/\*!.*\\-.*sandbox mode/d;/^\/*!/d' "$SQL_FILE"

# Eliminar los DEFINER del archivo SQL
sed -i 's/DEFINER=`[^`]*`@`[^`]*` //g' "$SQL_FILE"

# Verificar que el archivo SQL resultante exista
if [[ ! -f $SQL_FILE ]]; then
  echo "ERROR: La descompresión del archivo $FILE falló."
  exit 1
fi

# Si es dry-run, salir después de las verificaciones
if [[ $DRY_RUN -eq 1 ]]; then
  echo "DRY-RUN: Verificaciones completadas exitosamente. No se ejecutó el restore."
  rm "$SQL_FILE"
  exit 0
fi

# Activar log_bin_trust_function_creators globalmente
mysql --login-path=local -e "SET GLOBAL log_bin_trust_function_creators = 1;"

# Ejecutar la restauración
if mysql --login-path=local "$DATABASE" < "$SQL_FILE"; then
  now=$(date +"%Y-%m-%d %H:%M:%S")
  echo "FINAL RESTORE: $now | Base de datos: $DATABASE"
  rm "$SQL_FILE"
  mysql --login-path=local -e "SET GLOBAL log_bin_trust_function_creators = 0;"
else
  now=$(date +"%Y-%m-%d %H:%M:%S")
  echo "ERROR durante el restore: $now | Base de datos: $DATABASE"
  mysql --login-path=local -e "SET GLOBAL log_bin_trust_function_creators = 0;"
  exit 1
fi

exit 0
