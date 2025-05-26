<?php //   debug($liquidacion)?>
<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: IMPUTACION DE PAGOS EN CUENTA CORRIENTE'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3>PROCESO DE IMPUTACION DE PAGOS EN CUENTA CORRIENTE</h3>
<?php  if(!empty($liquidacion)):?>
	<div class="row">
		<?php if(empty($PID)):?>
			<?php echo $controles->btnRew('Regresar','/mutual/liquidaciones/resumen_cruce_informacion/'.$liquidacion['Liquidacion']['id'])?>
		<?php else:?>
			<?php echo $controles->btnRew('Regresar','/mutual/liquidaciones/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/1/0/1/?pid=' . $PID)?>
		<?php endif;?>
	</div>
	<div class="areaDatoForm">
		<h4>DATOS DEL COBRO A GENERAR</h4>
	<script type="text/javascript">
		function confirmar(){

			var msg = "**** PROCESO DE IMPUTACION ****\n";	
			msg = msg + "#<?php echo $liquidacion['Liquidacion']['id']?> - <?php echo $liquidacion['Liquidacion']['periodo_desc_amp']?> | <?php echo $liquidacion['Liquidacion']['organismo']?>";
			msg = msg + "\n\n";
			msg = msg  + "FECHA: " + getStrFecha('LiquidacionFechaImputacion') + "\n";
			if($('LiquidacionNroRecibo').getValue() !== null){
				msg = msg  + "REFERENCIA: " + $('LiquidacionNroRecibo').getValue() + "\n";
			}
			if($('DesimputacionPrevia').checked){
				msg = msg + "\n";
				msg = msg  + "DESIMPUTAR COBROS YA EMITIDOS [PROCESA TODO]\n";
			}
			msg = msg + "\n\nGENERAR PROCESO?";
			
			return confirm(msg);
		}
	</script>	
	<?php echo $frm->create(null,array('action' => 'imputar_pagos/'.$liquidacion['Liquidacion']['id']. (!empty($PID) ? "/$PID" : ""),'id' => 'frmDatoCobro', 'onsubmit' => "return confirmar()"))?>
		<table class="tbl_form">
			<tr>
				<td>FECHA</td>
				<td><?php echo $frm->input('fecha_imputacion',array('dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1))?></td>
			</tr>
			<tr>
				<td>REFERENCIA</td>
				<td><?php echo $frm->input('nro_recibo',array('label' => '', 'size' => 25, 'value' => "LIQ#".$liquidacion['Liquidacion']['id']))?></td>
			</tr>
			<tr>
				<td>DESIMPUTACION PREVIA</td>
				<td><input type="checkbox" <?php echo ($reprocesar == 1 ? "checked='checked'" : "")?> id="DesimputacionPrevia" name="data[Liquidacion][desimputar]" value="1" />(si se selecciona se borran los cobros ya emitidos en un proceso previo de imputaci&oacute;n si existieran)</td>
			</tr>					
			<tr>
				<td colspan="2"><?php echo $frm->submit("GENERAR PROCESO")?></td>
			</tr>		
		</table>
	<?php echo $frm->end()?>
	</div>
	<?php if($show_asinc == 1):?>
	
		<script type="text/javascript">

		Event.observe(window, 'load', function() {
			$('frmDatoCobro').disable();
		});
		
		</script>
	
		<div class="notices_error"><strong>ATENCION!:</strong> Mientras se encuentra ejecut&aacute;ndose el proceso <strong>NO CERRAR ESTA VENTANA!</strong></div>
		<?php 
		echo $this->renderElement('show',array(
												'plugin' => 'shells',
												'process' => 'imputar_pagos',
												'accion' => '.mutual.liquidaciones.resumen_cruce_informacion.'.$liquidacion['Liquidacion']['id'],
												'target' => '',
												'btn_label' => 'RESUMEN',
												'titulo' => 'PROCESO DE IMPUTACION | GENERAR COBROS',
												'subtitulo' => "FECHA: " . $util->armaFecha($fecha_imputacion) . " | REF:" . $nro_recibo . ($reprocesar == 1 ? " | DESIMPUTACION PREVIA" : ""),
												'p1' => $liquidacion['Liquidacion']['id'],
												'p2' => $fecha_imputacion,
												'p3' => $nro_recibo,
												'p4' => $reprocesar,
												'remote_call_start' => 'FormDatoCobro(1)',
												'remote_call_stop' => 'FormDatoCobro(0)'	
		));
		
		?>
	<?php endif;?>
<?php endif;?>