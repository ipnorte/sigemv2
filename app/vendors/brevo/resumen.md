
# Notificacion Deuda v1 – Resumen Funcional

## 1. Extracción de datos

- Consulta a la base vía SQL con el modelo `LiquidacionCuota`.
- Se agrupan cuotas en:
  - **Pagadas** (`saldo = 0`)
  - **Parcialmente pagadas** (`saldo > 0 && importe_debitado > 0`)
  - **Vencidas** (`saldo > 0 && importe_debitado = 0`)
- Se calculan:
  - `total_pagado`: suma de pagos reales (incluye pagos parciales)
  - `total_vencido`: suma de saldos pendientes

---

## 2. Parámetros del email

Función `generarParamsPorSocio()` genera:

- `nombre`, `fecha`, `periodo`
- `cuotas_pagadas` y `cuotas_vencidas` (en formato HTML `<tr>`)
- `total_pagado`, `total_vencido`
- `subject`:  
  `[NO REPLY] Estado de cuenta Rolicred c/ NOMBRE - Actualización dd/mm/yyyy`
- `proveedor`, `contacto`

---

## 3. Envío del email

- Clase `Mailer` en `vendors/brevo/Mailer.php`
- Envío vía API Brevo v3
- Usa plantilla con HTML responsivo
- El campo `subject` puede sobreescribir el de la plantilla

---

## 4. Plantilla Brevo

- HTML responsivo, sin Bootstrap
- Muestra:
  - Tabla de cuotas pagadas (verde)
  - Tabla de cuotas vencidas (rojo y naranja para pagos parciales)
- Totales destacados
- Firma personalizada (`Equipo {{ params.proveedor }}`)
- Contacto para WhatsApp (`{{ params.contacto }}`)

---

## 5. Registro de envíos

- Archivo: `app/tmp/logs/mailer.log`
- Formato de línea:
  `[YYYY-MM-DD HH:MM:SS] [ENVIADO] [201] email@example.com :: [NO REPLY] Estado de cuenta Rolicred...`
