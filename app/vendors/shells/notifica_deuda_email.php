<?php

// /usr/bin/php /home/adrian/trabajo/www/sigemv2/cake/console/cake.php notifica_deuda_email 10140 -app /home/adrian/trabajo/www/sigemv2/app/

/*
 * 

insert into global_datos (id, concepto_1)
values('MUTUNOTI', 'NOTIFICACIONES' );

INSERT  INTO `permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) 
VALUES (229,'Notificacion de Deuda','/mutual/liquidaciones/notifica_deuda',229,1,0
,'arrow_right2.gif',1,200,NULL,NULL,NULL);
-- VER EL ID DEL PERMISO UTILIZADO EN EL INSERT ANTERIOR
INSERT INTO grupos_permisos(grupo_id,permiso_id) VALUES(1,229);  


DROP TABLE IF EXISTS notificacion_socios;
DROP TABLE IF EXISTS notificaciones;

CREATE TABLE notificaciones (
	`id` INT NOT NULL AUTO_INCREMENT,
	`fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`periodo` varchar(6) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`user_created` varchar(45) DEFAULT NULL,
	`user_modified` varchar(45) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `idu_periodo` (`periodo`)
) ENGINE=InnoDB;

CREATE TABLE notificacion_socios (
	`id` INT NOT NULL AUTO_INCREMENT,
	`notificacion_id` int NOT NULL,
	`socio_id` int NOT NULL,
	`email` varchar(50) DEFAULT NULL,
	`error` boolean DEFAULT false,
        `stop_debit` boolean DEFAULT false,
	`saldo` decimal(10,2) NOT NULL,
        `pagado` decimal(10,2) NOT NULL,
	`detalle` LONGTEXT NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `idu_notificacion_id_socio_id` (`notificacion_id`, `socio_id`),
	CONSTRAINT `fk_notificacion_socios_socios1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
	CONSTRAINT `fk_notificacion_socios_notificaciones1` FOREIGN KEY (`notificacion_id`) REFERENCES `notificaciones` (`id`)
) ENGINE=InnoDB;
 
*/

// App::import('Vendor', 'Mailer', array('file' => 'brevo/Mailer.php'));

App::import('Vendor','SMTPMailer',array('file' => 'SMTPMailer.php')); 

class NotificaDeudaEmailShell extends Shell {

function main() {
        $pid = $this->args[0];

        $asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
        $asinc->id = $pid;

        $periodo = $asinc->getParametro('p1');
        $codigoNotificacion = $asinc->getParametro('p2');
        $userCreated = $asinc->getParametro('p3');

        $asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");

        App::import('Model', 'mutual.LiquidacionCuota');
        $OLC = new LiquidacionCuota();

        App::import('Model', 'mutual.Notificacion');
        $oNOTI = new Notificacion();        

        $global = $oNOTI->GlobalDato('texto_1',$codigoNotificacion);

        if(!$global) {
            $asinc->actualizar(99,100,"ERROR DE CONFIGURACION!");
            sleep(4);
            $asinc->fin("**** ERROR DE CONFIGURACION! ****");             
            return;
        }        

        $PARAMS = parse_ini_string(stripslashes(strip_tags(trim($global)))); 

        $imgHost = $PARAMS['LOGO'];
        $proveedor = $PARAMS['PROVEEDOR'];
        $contacto = $PARAMS['TELEFONO'];
        $alias = $PARAMS['ALIAS'];

        $sql = "select * from (
                select p.documento, CONCAT(TRIM(p.apellido), ' ', trim(p.nombre)) apenom, LOWER(p.e_mail) email, od.mutual_producto_solicitud_id AS solicitud,
                CONCAT(LPAD(odc.nro_cuota,2,0), '/', LPAD(od.cuotas,2,0)) cuota, odc.vencimiento, odc.periodo, tc.concepto_1 tipo_cuota
                ,RIGHT(odc.tipo_cuota,4) codigo, odc.importe as importe_cuota, IFNULL(t.importe_pagado,0) importe_pagado
                ,(odc.importe - IFNULL(t.importe_pagado,0)) as saldo_cuota, odc.socio_id
                ,IFNULL(s.calificacion,'') calificacion
                from orden_descuento_cuotas odc 
                INNER JOIN orden_descuentos od ON od.id = odc.orden_descuento_id
                INNER JOIN global_datos tc ON tc.id = odc.tipo_cuota
                INNER JOIN socios s ON s.id = odc.socio_id
                INNER JOIN personas p ON p.id = s.persona_id
                LEFT JOIN (select odcc.orden_descuento_cuota_id, max(odc.fecha) fecha_cobro, sum(odcc.importe) importe_pagado from orden_descuento_cobros odc 
                inner join orden_descuento_cobro_cuotas odcc on odcc.orden_descuento_cobro_id = odc.id 
                WHERE odc.periodo_cobro <= '$periodo'
                GROUP BY odcc.orden_descuento_cuota_id) t on t.orden_descuento_cuota_id = odc.id
                where odc.periodo = '$periodo' AND odc.estado <> 'B' AND IFNULL(p.e_mail, '') <> '' and odc.tipo_cuota not in ('MUTUTCUOGOTO', 'MUTUTCUOSELL')
                UNION 
                select p.documento, CONCAT(TRIM(p.apellido), ' ', trim(p.nombre)) apenom, LOWER(p.e_mail) email, od.mutual_producto_solicitud_id AS solicitud,
                CONCAT(LPAD(odc.nro_cuota,2,0), '/', LPAD(od.cuotas,2,0)) cuota, odc.vencimiento, odc.periodo, tc.concepto_1 tipo_cuota
                ,RIGHT(odc.tipo_cuota,4) codigo, odc.importe as importe_cuota, IFNULL(t.importe_pagado,0) importe_pagado
                ,(odc.importe - IFNULL(t.importe_pagado,0)) as saldo_cuota, odc.socio_id
                ,IFNULL(s.calificacion,'') calificacion
                from orden_descuento_cuotas odc 
                INNER JOIN orden_descuentos od ON od.id = odc.orden_descuento_id
                INNER JOIN global_datos tc ON tc.id = odc.tipo_cuota
                INNER JOIN socios s ON s.id = odc.socio_id
                INNER JOIN personas p ON p.id = s.persona_id
                LEFT JOIN (select odcc.orden_descuento_cuota_id, max(odc.fecha) fecha_cobro, sum(odcc.importe) importe_pagado from orden_descuento_cobros odc 
                inner join orden_descuento_cobro_cuotas odcc on odcc.orden_descuento_cobro_id = odc.id 
                WHERE odc.periodo_cobro <= '$periodo'
                GROUP BY odcc.orden_descuento_cuota_id) t on t.orden_descuento_cuota_id = odc.id
                where odc.periodo < '$periodo' AND odc.estado <> 'B' AND IFNULL(p.e_mail, '') <> '' and odc.tipo_cuota not in ('MUTUTCUOGOTO', 'MUTUTCUOSELL')
                HAVING saldo_cuota > 0) t 
                -- WHERE t.documento = '30941361'
                WHERE t.documento = '29512936'
                -- WHERE t.documento = '24584946'
                ORDER BY t.apenom, t.periodo, t.cuota;";

        $resultados = $OLC->query($sql);

        if(empty($resultados)) {
            $asinc->actualizar(99,100,"SIN DATOS PARA PROCESAR!");
            sleep(4);
            $asinc->fin("**** SIN DATOS PARA PROCESAR ****");             
            return;
        }

        $total = count($resultados);
        $asinc->setTotal($total);
        $i = 0;
        $socios = array();
        foreach ($resultados as $row) {
            $t = $row['t'];
            $email = $t['email'];
            $nombre = $t['apenom'] . ' - DNI ' . $t['documento'];
            $socios[$email]['nombre'] = $nombre;
            $socios[$email]['ndoc'] = $t['documento'];
            $socios[$email]['apenom'] = $t['apenom'];
            $socios[$email]['calificacion'] = $t['calificacion'];
            $socios[$email]['socio_id'] = $t['socio_id'];
            $socios[$email]['datos'][] = $t;
            $asinc->actualizar($i,$total,"$i / $total - Analizando Cuotas");
            $i++;
        }

        $mailer = new SMTPMailer();
        $mailer->isHTML(true);

        $asinc->actualizar(1,100,"INICIANDO ENVIO DE EMAILS...");
        $total = count($socios);
        $asinc->setTotal($total);
        $i = 0;

        $notificacion = [
            'periodo' => $periodo,
            'user_created' => $userCreated,
            'NotificacionSocio' => []
        ];
        $notificacionSocio = [];

        foreach ($socios as $email => $info) {
            $nombre = $info['nombre'];
            $isStop = ($info['calificacion'] == 'MUTUCALISDEB');

            $params = $this->generarParamsDesdeDatosUnificados($info['datos'], $nombre, $periodo, $proveedor, $contacto, $alias, $isStop, $imgHost);

            $template = file_get_contents(APP . 'vendors' . DS . 'shells' . DS . 'templates' . DS . 'deuda_unificada.html');
            foreach ($params as $key => $value) {
                $search = '{{ params.' . $key . ' }}';
                $replace = (is_array($value) ? '' : $value);
                $template = str_replace($search, $replace, $template);
            }
            $template = str_replace('{{ params.cuotas_completas }}', $params['cuotas_completas'], $template);
            // Limpiar placeholders no reemplazados
            $template = preg_replace('/{{\s*params\.[^}]+\s*}}/', '', $template);            

            try {
                
                $email = 'm.adrian.torres@gmail.com';
                $email = 'lu1hiw@hotmail.com';
                $email = 'm_atorres@hotmail.com';
                
                $mailer->clearAddresses();
                $mailer->Subject = $params['subject'];
                $mailer->Body = $template;
                $mailer->addAddress($email, $nombre);
                $status = !$mailer->send(); // false si OK

                $asinc->actualizar($i,$total,"$i / $total - $nombre -> SEND " . ($status ? '[ERROR]' : '[OK]'));
                $notificacionSocio[] = [
                    'socio_id' => $info['socio_id'],
                    'email' => $email,
                    'saldo' => floatval(str_replace(',', '.', str_replace('.', '', $params['total_vencido']))),
                    'pagado' => floatval(str_replace(',', '.', str_replace('.', '', $params['total_pagado']))),
                    'error' => $status,
                    'detalle' => json_encode($params['detalle']),
                    'stop_debit' => $isStop,
                ];
            } catch (Exception $e) {
                $asinc->actualizar($i,$total,"$i / $total - Error con $email: " . $e->getMessage());
                return;
            }
            sleep(3);
            $i++;
        }

        try {
            $notificacion['NotificacionSocio'] = $notificacionSocio;
            $oNOTI->borrarPorPeriodo($periodo);
            if(!$oNOTI->saveAll($notificacion)) {
                throw new Exception("ERROR AL GUARDAR LA NOTIFICACION!");
            }
            $asinc->setValue('p3',$oNOTI->id);
            $asinc->actualizar(99,100,"FINALIZANDO...");
            $asinc->fin("**** PROCESO FINALIZADO ****");
            return;
        } catch (Exception $exc) {
            $msg = "Error: " . $exc->getMessage();
            $asinc->actualizar(99,100,$msg);
            return;
        }
    }    

    
    function generarParamsDesdeDatosUnificados($datos, $nombre, $periodo, $proveedor, $contacto, $alias, $isStop = false, $imgHost) {
        $total_pagado = 0;
        $total_vencido = 0;
        $filas = $detalle = [];

        App::import('Helper', 'Util');
        $oUT = new UtilHelper();        

        foreach ($datos as $t) {
            $importe = (float)$t['importe_cuota'];
            $pagado = (float)$t['importe_pagado'];
            $saldo  = (float)$t['saldo_cuota'];

            $total_pagado += $pagado;
            $total_vencido += $saldo;

            if ($saldo == 0) {
                $style = 'background-color: #eafaf1;'; // Pagada
            } elseif ($saldo > 0 && $pagado > 0) {
                $style = 'background-color: #fff8e1;'; // Parcial
            } else {
                $style = 'background-color: #fdecea;'; // Impaga
            }

            $filas[] = sprintf(
                "<tr style=\"%s\"><td>%s</td><td align=\"center\">%s</td><td align=\"right\">%s</td><td align=\"right\">%s</td><td>%s</td><td>%s</td></tr>",
                $style,
                $t['solicitud'],
                $t['cuota'],
                number_format($importe, 2, ',', '.'),
                number_format($saldo, 2, ',', '.'),
                $oUT->periodo($t['periodo']),
                $t['tipo_cuota']    
            );
            
            $detalle[] = [
                'solicitud' => $t['solicitud'],
                'cuota' => $t['cuota'],
                'periodo' => $t['periodo'],
                'tipo_cuota' => $t['tipo_cuota'],
                'importe' => $importe,
                'saldo' => $saldo,
            ];
        }

        $periodoActual = $oUT->periodo($periodo);
        $nombreMayus = strtoupper($nombre);
        $subject = '' . $proveedor . ' c/ ' . $nombreMayus . ' - Actualización ' . $periodoActual;
        
        $mensaje_whatsapp = 'Hola, soy *' . $nombreMayus . '*, me contacto por mi estado de cuenta en ' . $proveedor . ' a ' . $periodoActual . '.';
        $mensaje_whatsapp_utf8 = mb_convert_encoding($mensaje_whatsapp, 'UTF-8', 'auto');
        $mensaje_whatsapp_encoded = urlencode($mensaje_whatsapp_utf8);

        
        $mensajeStop = "";
        if ($isStop) {
            $mensajeStop = "*** Usted aplicó STOP DEBIT. Revierta para evitar gestión judicial. ***";
        }

        return [
            'nombre' => $nombreMayus,
            'periodo' => $periodoActual,
            'proveedor' => $proveedor,
            'alias' => $alias,
            'mensaje_whatsapp' => $mensaje_whatsapp_encoded,
            'contacto' => preg_replace('/[^0-9]/', '', $contacto),
            'cuotas_completas' => implode("\n", $filas),
            'total_pagado' => number_format($total_pagado, 2, ',', '.'),
            'total_vencido' => number_format($total_vencido, 2, ',', '.'),
            'subject' => $subject,
            'detalle' => $detalle,
            'mensaje_stop' => $mensajeStop,
            'logo_url' => $imgHost,
        ];

    }
    
}
?>
