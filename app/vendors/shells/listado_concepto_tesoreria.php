<?php

/**
 * 
 * @author GUSTAVO LUJAN
 * @package shells
 * @subpackage background-execute
 * 
 * 
 * LANZADOR
 * 	/usr/bin/php5 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php listado_inaes 523 -app /home/adrian/Desarrollo/www/sigem/app/
 *  D:\Desarrollo\xampp\php\php.exe D:\Desarrollo\xampp\htdocs\sigem\cake\console\cake.php listado_concepto_tesoreria 4460 -app D:\Desarrollo\xampp\htdocs\sigem\app\
 */

class ListadoConceptoTesoreriaShell extends Shell {

	var $tasks = array('Temporal');
	
	var $uses = array('Proveedores.Movimiento', 'Clientes.Recibo', 'Proveedores.OrdenPago', 'Cajabanco.BancoCuentaMovimiento');
	
	function main() {
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$fecha_desde = $asinc->getParametro('p1');
		$fecha_hasta = $asinc->getParametro('p2');
		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, PROCESANDO INFORMACION...");

		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		$movimiento = $this->getMovimientos($fecha_desde,$fecha_hasta);
		
		$total = count($movimiento);
		$asinc->setTotal($total);
		$j = 0;	

		$i=0;
		while($i < count($movimiento)):
		  	$concepto = $movimiento[$i][0]['banco_concepto_id'];
		  	$temp = array(
		  		'asincrono_id' => $asinc->id,
		  		'clave_1' => $concepto,
		  		'texto_1' => $movimiento[$i][0]['concepto'],
		  		'texto_11' => $movimiento[$i][0]['tipo']
		  	);
			if(!$this->Temporal->grabar($temp)){
				$STOP = 1;
				break;
			}				
				
		  	
		   	while($concepto == $movimiento[$i][0]['banco_concepto_id'] && $i < count($movimiento)):
		   		$banco = $movimiento[$i][0]['banco_cuenta_id'];
		   		$temp = array();
		  		$temp['asincrono_id'] = $asinc->id;
		  		$temp['clave_1'] = $concepto;
		  		$temp['texto_1'] = $movimiento[$i][0]['concepto'];
		  		$temp['texto_11'] = $movimiento[$i][0]['tipo'];
		   		$temp['clave_2'] = $banco;
		   		$temp['texto_2'] = $movimiento[$i][0]['nombre'] . '-' . $movimiento[$i][0]['denominacion'] . '-' . $movimiento[$i][0]['numero'];
				if(!$this->Temporal->grabar($temp)){
					$STOP = 1;
					break;
				}				
				
				
		   		while($concepto == $movimiento[$i][0]['banco_concepto_id'] && $banco == $movimiento[$i][0]['banco_cuenta_id'] && $i < count($movimiento)):
		   			$movi = $this->BancoCuentaMovimiento->getMovimientoIdEdit($movimiento[$i][0]['id']);
		   			
		   			// Recibo
		   			if($movimiento[$i][0]['recibo_id'] > 0):
		   				$aRecibo = $this->Recibo->getRecibo($movimiento[$i][0]['recibo_id']);
		   				$temp['texto_3'] = 'R';
		   				$temp['texto_4'] = $aRecibo['Recibo']['razon_social'];
		   				$temp['texto_5'] = $aRecibo['Recibo']['numero_string2'];
		   				$temp['texto_6'] = '/clientes/clientes/view_recibo/' . $movimiento[$i][0]['recibo_id'];
		   				$temp['texto_7'] = $aRecibo['Recibo']['comentario'];
		   			
		   			// Orden de Pago
		   			elseif($movimiento[$i][0]['orden_pago_id'] > 0):
		   				$aOrdenPago = $this->OrdenPago->getOrdenPagoAmpliado($movimiento[$i][0]['orden_pago_id']);
		   				$temp['texto_3'] = 'P';
		   				$temp['texto_4'] = $aOrdenPago['Proveedor']['razon_social'];
		   				$temp['texto_5'] = $aOrdenPago['OrdenPago']['tipo_comprobante_desc'];
		   				$temp['texto_6'] = '/proveedores/orden_pagos/view_orden_pago/' . $movimiento[$i][0]['orden_pago_id'];
		   				$temp['texto_7'] = $aOrdenPago['OrdenPago']['comentario'];
		   				
		   			
		   			
		   			// Cancelacion Ordenes
		   			elseif($movimiento[$i][0]['cancelacion_orden_id'] > 0):
		   				$temp['texto_3'] = 'C';
		   				$temp['texto_4'] = '';
		   				$temp['texto_5'] = 'CANC. # ' . $movimiento[$i][0]['cancelacion_orden_id'];
		   				$temp['texto_6'] = '/mutual/cancelacion_ordenes/vista_detalle/' . $movimiento[$i][0]['cancelacion_orden_id'];
		   				$temp['texto_7'] = $movimiento[$i][0]['descripcion'];
		   			
		   			
		   			// Orden de caja cobro
		   			elseif($movimiento[$i][0]['orden_caja_cobro_id'] > 0):
		   				$temp['texto_3'] = 'O';
		   				$temp['texto_4'] = '';
		   				$temp['texto_5'] = 'COBRO # ' . $movimiento[$i][0]['orden_descuento_cobro_id'];
		   				$temp['texto_6'] = '/mutual/orden_descuento_cobros/view/' . $movimiento[$i][0]['orden_descuento_cobro_id'];
		   				$temp['texto_7'] = $movimiento[$i][0]['descripcion'];
		   			
		   			
		   			else:
		   				$temp['texto_3'] = 'M';
		   				$temp['texto_4'] = $movimiento[$i][0]['destinatario'];
		   				$temp['texto_5'] = '';
		   				$temp['texto_6'] = '';
		   				if($movimiento[$i][0]['banco_cuenta_movimiento_id'] > 0):
			   				$temp['texto_5'] = 'MOV. # ' . $movimiento[$i][0]['banco_cuenta_movimiento_id'];
			   				$temp['texto_6'] = '/cajabanco/banco_cuenta_movimientos/edit_comprobante/' . $movimiento[$i][0]['banco_cuenta_movimiento_id'];
			   			endif;
		   				$temp['texto_7'] = $movimiento[$i][0]['descripcion'];
		   			endif;
		   			
		   			$temp['texto_8'] = $movimiento[$i][0]['fecha_operacion'];
		   			$temp['texto_9'] = $movimiento[$i][0]['fecha_vencimiento'];
		   			$temp['texto_10'] = $movimiento[$i][0]['numero_operacion'];
		   			$temp['texto_11'] = $movimiento[$i][0]['tipo'];
		   			$temp['texto_3'] = $movimiento[$i][0]['anulado'];
		   			$temp['texto_15'] = '/cajabanco/banco_cuenta_movimientos/edit_comprobante/' . $movimiento[$i][0]['id'];
		   			$temp['decimal_1'] = ($movimiento[$i][0]['debe_haber'] == 0 ? $movimiento[$i][0]['importe'] : 0.00);
		   			$temp['decimal_2'] = ($movimiento[$i][0]['debe_haber'] == 1 ? $movimiento[$i][0]['importe'] : 0.00);
		   			$temp['entero_1'] = $movimiento[$i][0]['id'];
			   		$temp['texto_12'] = '';
			   		$temp['texto_13'] = '';
			   		$temp['texto_14'] = '';
		   			
		   			if($movimiento[$i][0]['reemplazar'] == 1 && $movimiento[$i][0]['anulado'] == 1):
			   			$temp['texto_12'] = 'R';
			   			$temp['texto_13'] = ' (ANULADO)-REEM.MOV. # ' . $movimiento[$i][0]['banco_cuenta_movimiento_id'];
			   			$temp['texto_14'] = '/cajabanco/banco_cuenta_movimientos/edit_comprobante/' . $movimiento[$i][0]['banco_cuenta_movimiento_id'];
			   		endif;
		   			
		   			if(!$this->Temporal->grabar($temp)){
						$STOP = 1;
						break;
					}				
				
		   			$i++;
					$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $movimiento[$i][0]['concepto']);
				endwhile;
			endwhile;
			
		endwhile;

	
		if($STOP == 0){
			$asinc->actualizar($j,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}
		
		
		
	}
	//FIN PROCESO ASINCRONO

	function getMovimientos($fecha_desde, $fecha_hasta){
		$sql = "(SELECT	
					BancoConcepto.concepto, 
					Banco.nombre, 
					BancoCuenta.denominacion, 
					BancoCuenta.numero,
					BancoCuentaMovimiento.*
				FROM	
					banco_cuenta_movimientos BancoCuentaMovimiento
				INNER JOIN 
					banco_conceptos BancoConcepto
				ON	
					BancoConcepto.id = BancoCuentaMovimiento.banco_concepto_id
				INNER JOIN 
					banco_cuentas BancoCuenta
				ON	
					BancoCuenta.id = BancoCuentaMovimiento.banco_cuenta_id
				INNER JOIN 
					bancos Banco
				ON	
					Banco.id = BancoCuenta.banco_id
				WHERE	
					BancoCuentaMovimiento.fecha_operacion >= '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta')

				UNION
				
				(SELECT	IF(tipo=9,'REVERSO','EFECTIVO') AS concepto, 
					Banco.nombre, 
					BancoCuenta.denominacion, 
					BancoCuenta.numero,
					BancoCuentaMovimiento.*
				FROM	
					banco_cuenta_movimientos BancoCuentaMovimiento
				INNER JOIN 
					banco_cuentas BancoCuenta
				ON	
					BancoCuenta.id = BancoCuentaMovimiento.banco_cuenta_id
				INNER JOIN 
					bancos Banco
				ON	
					Banco.id = BancoCuenta.banco_id
				WHERE	
					BancoCuentaMovimiento.banco_concepto_id = 0 AND
					BancoCuentaMovimiento.fecha_operacion >= '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta')
				ORDER BY 
					tipo,
					banco_concepto_id, 
					banco_cuenta_id, 
					banco_cheque_tercero_id,
					fecha_operacion,
					id";
		

					
		return $this->Movimiento->query($sql);
		
	}
}
?>