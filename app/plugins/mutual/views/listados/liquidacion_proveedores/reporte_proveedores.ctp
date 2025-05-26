<?php echo $this->renderElement('head',array('title' => 'PROCESO DE LIQUIDACION DE PROVEEDORES'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if($tipo_producto == '0'):?>

<h3>REPORTE GENERAL  :: <?php echo $proveedor['Proveedor']['razon_social']?></h3>

<?php else:?>
	
	<h3>REPORTE DETALLADO  :: <?php echo $proveedor['Proveedor']['razon_social']?></h3>
	<div class="areaDatoForm2">
	
	PRODUCTO: <strong><?php echo $util->globalDato($tipo_producto)?></strong>
	<br/>
	CONCEPTO: <strong><?php echo $util->globalDato($tipo_cuota)?></strong>
	
	</div>	
	
<?php endif;?>
<!-- <div class="notices_error"><strong>ATENCION!:</strong> Mientras se encuentra ejecut&aacute;ndose el proceso <strong>NO CERRAR ESTA VENTANA!</strong></div> -->
<?php if($procesarSobrePreImputacion == 1):?>
	<div class="notices_error"><strong>ANALISIS EFECTUADO EN BASE A LA PRE-IMPUTACION</strong></div>
<?php endif;?>
<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'reporte_liquidacion_proveedor_detalle_xls',
//										'accion' => '.mutual.listados.reporte_proveedores.'.$liquidacion['Liquidacion']['id'].'.'.$proveedor_id.'.'.$tipo_producto.'.'.$tipo_cuota.'.'.$tipo_salida,
										'accion' => $accion,
										'target' => '',
										'btn_label' => 'CONSULTAR REPORTE LIQUIDACION PROVEEDORES',
										'titulo' => 'PROCESA REPORTE :: FORMATO DE SALIDA ' . $tipo_salida,
										'subtitulo' => ($procesarSobrePreImputacion == 0 ? "ANALISIS SOBRE CUOTAS IMPUTADAS" : "ANALISIS SOBRE CUOTAS PRE-IMPUTADAS"),		
										'p1' => $liquidacion['Liquidacion']['id'],
										'p2' => $proveedor_id,
										'p3' => $tipo_cuota,
										'p4' => $tipo_producto,
										'p5' => $procesarSobrePreImputacion,
));

?>	