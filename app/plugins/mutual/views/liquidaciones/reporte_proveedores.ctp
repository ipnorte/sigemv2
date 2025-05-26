<?php echo $this->renderElement('head',array('title' => 'PROCESO DE LIQUIDACION DE PROVEEDORES'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if(empty($proveedores)):?>
	<?php if($procesarSobrePreImputacion == 1):?>
		<div class="notices_error"><strong>ANALISIS EFECTUADO EN BASE A LA PRE-IMPUTACION</strong></div>
	<?php endif;?>
<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'reporte_control_liquidacion',
										'accion' => '.mutual.liquidaciones.reporte_proveedores.'.$liquidacion['Liquidacion']['id'].'.1.0.'. $procesarSobrePreImputacion,
										'target' => '',
										'btn_label' => 'CONSULTAR REPORTE LIQUIDACION PROVEEDORES',
										'titulo' => 'REPORTE LIQUIDACION DE PROVEEDORES',
										'subtitulo' => ($procesarSobrePreImputacion == 0 ? "ANALISIS SOBRE CUOTAS IMPUTADAS" : "ANALISIS SOBRE CUOTAS PRE-IMPUTADAS"),
										'p1' => $liquidacion['Liquidacion']['id'],
										'p2' => 'CALCULA_IMPUTADO',
										'p3' => $procesarSobrePreImputacion
));

?>	

<?php else:?>

	<?php //   debug($proveedores)?>
	<?php //   debug($procesarSobrePreImputacion)?>
	
	<?php 
	
	$tabs = array();
	$tabs[0] = array('url' => '/mutual/liquidaciones/resumen_cruce_informacion/'.$liquidacion['Liquidacion']['id'],'label' => 'CRUCE DE INFORMACION', 'icon' => 'controles/arrow_switch.png','atributos' => array('target' => 'blank'), 'confirm' => null);
	if($procesarSobrePreImputacion == 1){
		$tabs[1] = array('url' => '/mutual/liquidaciones/imputar_pagos/'.$liquidacion['Liquidacion']['id'].'/' . $PID,'label' => 'IMPUTAR PAGOS EN CUENTA CORRIENTE DEL SOCIO', 'icon' => 'controles/money_add.png','atributos' => array(), 'confirm' => "IMPUTAR PAGOS EN CUENTA CORRIENTE DEL SOCIO?");
	}
/*
//	if($liquidacion['Liquidacion']['facturada'] == 1 && $procesarSobrePreImputacion == 0):
//		$tabs[2] = array('url' => '/mutual/liquidaciones/anular_facturas/'.$liquidacion['Liquidacion']['id'].'/'.$PID,'label' => 'ANULAR FACTURACION', 'icon' => 'controles/money_add.png','atributos' => array(), 'confirm' => "ANULAR LA FACTURACION EMITIDA PARA ESTA LIQUIDACION?");
//	elseif($procesarSobrePreImputacion == 0):
//		$tabs[3] = array('url' => '/mutual/liquidaciones/imputar_comercios/'.$liquidacion['Liquidacion']['id'].'/'.$PID,'label' => 'GENERAR FACTURA COMERCIO EN CUENTA CORRIENTE', 'icon' => 'controles/money_add.png','atributos' => array(), 'confirm' => "GENERAR LA FACTURACION DE COMERCIO EN CUENTA CORRIENTE?");
//	endif;
*/
        
        if($procesarSobrePreImputacion == 0){
		$tabs[2] = array('url' => '/mutual/liquidaciones/imputar_comercios/'.$liquidacion['Liquidacion']['id'].'/'.$PID.'/'.$liquidacion['Liquidacion']['facturada'],'label' => 'GENERAR FACTURA COMERCIO EN CUENTA CORRIENTE', 'icon' => 'controles/money_add.png','atributos' => array(), 'confirm' => "GENERAR LA FACTURACION DE COMERCIO EN CUENTA CORRIENTE?");
        }
	$tabs[4] = array('url' => '/mutual/listados/reporte_imputacion_deuda/'.$liquidacion['Liquidacion']['id'].'/'.$PID.'/PDF/' . $procesarSobrePreImputacion,'label' => 'IMPRIMIR PLANILLA', 'icon' => 'controles/pdf.png','atributos' => array('target' => 'blank'), 'confirm' => null);
	$tabs[5] = array('url' => '/mutual/listados/reporte_imputacion_deuda/'.$liquidacion['Liquidacion']['id'].'/'.$PID.'/XLS/' . $procesarSobrePreImputacion,'label' => 'EXPORTAR PLANILLA', 'icon' => 'controles/ms_excel.png','atributos' => array('target' => 'blank'), 'confirm' => null);
	
	echo $cssMenu->menuTabs($tabs,false);			
	
	?>		
	
	<h3>PLANILLA GENERAL DE <?php echo ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTACION" : "IMPUTACION")?></h3>
	
	<table class="tbl_grilla">
	
		<tr>
			<th colspan="19" style="text-align: left;">PROVEEDOR</th>
		</tr>
	
		<tr>
			<th colspan="2" rowspan="2">PRODUCTO - CONCEPTO</th>
			<th colspan="3">LIQUIDACION</th>
			<th colspan="4"><?php echo ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO")?></th>
			<th rowspan="2">REVERSADO</th>
			<th colspan="2">COMISION COBRANZA</th>
			<th rowspan="2">NETO PROVEEDOR</th>
			<th rowspan="2">LIQUIDADO NO COBRADO</th>
			<th colspan="4" rowspan="2"></th>
		</tr>
		<tr>
			<th>PERIODO</th>
			<th>DEUDA</th>
			<th>TOTAL</th>
			<th>PERIODO</th>
			<th>DEUDA</th>
			<th>TOTAL</th>
                        <th>SOCIOS</th>
			<th>ALICUOTA %</th>
			<th>IMPORTE</th>			
		</tr>
		
		<?php $PROVEEDOR = 0;?>
		<?php $PRIMERO = true;?>
		<?php $LIQUIDADO = 0;?>
		<?php $LIQUIDADO_PERIODO = 0;?>
		<?php $LIQUIDADO_DEUDA = 0;?>
		<?php $IMPUTADO = 0;?>
                <?php $NSOCIOS = 0;?>
		<?php $IMPUTADO_PERIODO = 0;?>
		<?php $IMPUTADO_DEUDA = 0;?>
		<?php $SALDO = 0;?>	
		<?php $COMISION = 0;?>
		<?php $NETO_PROVEEDOR = 0;?>
		<?php $REVERSO = 0;?>
		
		<?php $TLIQUIDADO = 0;?>
		<?php $TLIQUIDADO_PERIODO = 0;?>
		<?php $TLIQUIDADO_DEUDA = 0;?>
		<?php $TIMPUTADO = 0;?>
                <?php $TNSOCIOS = 0;?>
		<?php $TIMPUTADO_PERIODO = 0;?>
		<?php $TIMPUTADO_DEUDA = 0;?>
		<?php $TSALDO = 0;?>
		<?php $TCOMISION = 0;?>	
		<?php $TNETO_PROVEEDOR = 0;?>		
		<?php $TREVERSO = 0;?>
		
		<?php foreach($proveedores as $dato):?>
		
			<?php //   DEBUG($dato)?>
			
			<?php if($PROVEEDOR != trim($dato['entero_1'])):?>
			
				<?php if($PRIMERO):?>
					<?php $PRIMERO = false;?>
				<?php else:?>
				
					<tr class="totales">
						<th colspan="2">TOTAL</th>
						<th><?php echo $util->nf($LIQUIDADO_PERIODO)?></th>
						<th><?php echo $util->nf($LIQUIDADO_DEUDA)?></th>
						<th><?php echo $util->nf($LIQUIDADO)?></th>
						<th><?php echo $util->nf($IMPUTADO_PERIODO)?></th>
						<th><?php echo $util->nf($IMPUTADO_DEUDA)?></th>
						<th><?php echo $util->nf($IMPUTADO)?></th>
                                                <th style="text-align: center"><?php echo $NSOCIOS?></th>
						<th><?php echo $util->nf($REVERSO)?></th>
						<th></th>
						<th><?php echo $util->nf($COMISION)?></th>
						<th><?php echo $util->nf($NETO_PROVEEDOR)?></th>		
						<th><?php echo $util->nf($SALDO)?></th>
						<th colspan="4"></th>
					</tr>
					
					<tr>
						<th colspan="2" rowspan="2">PRODUCTO - CONCEPTO</th>
						<th colspan="3">LIQUIDACION</th>
						<th colspan="4"><?php echo ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO")?></th>
						<th rowspan="2">REVERSADO</th>
						<th colspan="2">COMISION COBRANZA</th>
						<th rowspan="2">NETO PROVEEDOR</th>
						<th rowspan="2">LIQUIDADO NO COBRADO</th>
						<th colspan="4" rowspan="2"></th>
					</tr>
					<tr>
						<th>PERIODO</th>
						<th>DEUDA</th>
						<th>TOTAL</th>
						<th>PERIODO</th>
						<th>DEUDA</th>
						<th>TOTAL</th>
                                                <th>SOCIOS</th>
						<th>ALICUOTA %</th>
						<th>IMPORTE</th>									
					</tr>				
				
				<?php endif;?>
			
			
				<?php $PROVEEDOR = trim($dato['entero_1']);?>
				<tr>
					<td colspan="14" style="font-size:13px;background-color: #e2e6ea"><strong><?php echo $dato['texto_1']?></strong></td>
					<td style="font-size:13px;background-color: #e2e6ea"><?php echo $controles->botonGenerico('/mutual/listados/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/'.$dato['entero_1'].'/0/0/PDF/' . $procesarSobrePreImputacion,'controles/pdf.png','',array('target' => '_blank'))?></td>
					<td style="font-size:13px;background-color: #e2e6ea"><?php echo $controles->botonGenerico('/mutual/listados/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/'.$dato['entero_1'].'/0/0/XLS/' . $procesarSobrePreImputacion,'controles/ms_excel.png','',array('target' => '_blank'))?></td>
					<td style="font-size:13px;background-color: #e2e6ea"><?php if($procesarSobrePreImputacion == 0) echo $controles->botonGenerico('/mutual/liquidaciones/intercambio_proveedores/'.$liquidacion['Liquidacion']['id'].'/'.$dato['entero_1'].'/?pid='.$PID,'controles/disk.png','',array('target' => '_blank'))?></td>
					<td style="font-size:13px;background-color: #e2e6ea"><?php // if($liquidacion['Liquidacion']['imputada'] == 1) echo $controles->botonGenerico('/mutual/listados/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/'.$dato['entero_1'].'/0/0/SMTP/','controles/email.png','',array('target' => '_blank'))?></td>					
				</tr>
				<?php $LIQUIDADO = 0;?>
				<?php $LIQUIDADO_PERIODO = 0;?>
				<?php $LIQUIDADO_DEUDA = 0;?>
				<?php $IMPUTADO = 0;?>
                                <?php $NSOCIOS = 0;?>
				<?php $IMPUTADO_PERIODO = 0;?>
				<?php $IMPUTADO_DEUDA = 0;?>
				<?php $SALDO = 0;?>	
				<?php $COMISION = 0;?>
				<?php $NETO_PROVEEDOR = 0;?>
				<?php $REVERSO = 0;?>									
			
			
			<?php endif;?>
			
			<?php $LIQUIDADO += $dato['decimal_9'];?>
			<?php $LIQUIDADO_PERIODO += $dato['decimal_7'];?>
			<?php $LIQUIDADO_DEUDA += $dato['decimal_8'];?>
			
			<?php $IMPUTADO += $dato['decimal_6'];?>
                        <?php $NSOCIOS += $dato['entero_3'];?>    
			<?php $IMPUTADO_PERIODO += $dato['decimal_4'];?>
			<?php $IMPUTADO_DEUDA += $dato['decimal_5'];?>
			<?php $SALDO += $dato['decimal_13'];?>
			<?php $COMISION +=  ($dato['texto_2'] != 'REVERSADO' ? $dato['decimal_11'] : $dato['decimal_11'] * -1);?>
			<?php $NETO_PROVEEDOR += $dato['decimal_12'];?>
			<?php $REVERSO += $dato['decimal_14'];?>
			
			<?php $TLIQUIDADO += $dato['decimal_9'];?>
			<?php $TLIQUIDADO_PERIODO += $dato['decimal_7'];?>
			<?php $TLIQUIDADO_DEUDA += $dato['decimal_8'];?>
			<?php $TIMPUTADO += $dato['decimal_6'];?>
                        <?php $TNSOCIOS += $dato['entero_3'];?>      
			<?php $TIMPUTADO_PERIODO += $dato['decimal_4'];?>
			<?php $TIMPUTADO_DEUDA += $dato['decimal_5'];?>
			<?php $TSALDO += $dato['decimal_13'];?>							
			<?php $TCOMISION +=  ($dato['texto_2'] != 'REVERSADO' ? $dato['decimal_11'] : $dato['decimal_11'] * -1);?>
			<?php $TNETO_PROVEEDOR += $dato['decimal_12'];?>
			<?php $TREVERSO += $dato['decimal_14'];?>
			
			<tr>
			
				<td></td>
				<td><strong><?php echo $dato['texto_4']?></strong> - <?php echo $dato['texto_5']?></td>
				<td align="right"><?php echo $util->nf($dato['decimal_7'])?></td>
				<td align="right"><?php echo $util->nf($dato['decimal_8'])?></td>
				<td align="right" class="activo_1"><strong><?php echo $util->nf($dato['decimal_9'])?></strong></td>
				<td align="right"><?php echo $util->nf($dato['decimal_4'])?></td>
				<td align="right"><?php echo $util->nf($dato['decimal_5'])?></td>
				<td align="right" class="<?php echo ($dato['decimal_6'] == 0 ? "celda_texto_error" : "activo_1")?>"><strong><?php echo $util->nf($dato['decimal_6'])?></strong></td>
				<td align="center"><?php echo $dato['entero_3']?></td>
                                <td align="right"><?php echo $util->nf($dato['decimal_14'])?></td>
				<td align="right"><?php echo $util->nf($dato['decimal_10'])?></td>
				<td align="right"><?php echo $util->nf(($dato['texto_2'] != 'REVERSADO' ? $dato['decimal_11'] : $dato['decimal_11'] * -1))?></td>
				<td align="right" class="activo_1"><strong><?php echo $util->nf($dato['decimal_12'])?></strong></td>
				<td align="right" class="activo_0"><?php echo $util->nf($dato['decimal_13'])?></td>				
				<td><?php echo $controles->botonGenerico('/mutual/listados/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/'.$dato['entero_1'].'/'.$dato['texto_2'].'/'.$dato['texto_3'].'/PDF/' . $procesarSobrePreImputacion,'controles/pdf.png','',array('target' => 'blank'))?></td>
				<td><?php echo $controles->botonGenerico('/mutual/listados/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/'.$dato['entero_1'].'/'.$dato['texto_2'].'/'.$dato['texto_3'].'/XLS/' . $procesarSobrePreImputacion,'controles/ms_excel.png','',array('target' => 'blank'))?></td>
				<td><?php // echo $controles->botonGenerico('/mutual/listados/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/'.$dato['entero_1'].'/'.$dato['texto_2'].'/'.$dato['texto_3'].'/PDF','controles/printer.png','',array('target' => 'blank'))?></td>
				<td></td>
			</tr>			
			
			
		<?php endforeach;?>	
		
		<tr class="totales">
			<th colspan="2">TOTAL</th>
			<th><?php echo $util->nf($LIQUIDADO_PERIODO)?></th>
			<th><?php echo $util->nf($LIQUIDADO_DEUDA)?></th>
			<th><?php echo $util->nf($LIQUIDADO)?></th>
			<th><?php echo $util->nf($IMPUTADO_PERIODO)?></th>
			<th><?php echo $util->nf($IMPUTADO_DEUDA)?></th>
			<th><?php echo $util->nf($IMPUTADO)?></th>
                        <th style="text-align: center"><?php echo $NSOCIOS?></th>
			<th><?php echo $util->nf($REVERSO)?></th>
			<th></th>
			<th><?php echo $util->nf($COMISION)?></th>	
			<th><?php echo $util->nf($NETO_PROVEEDOR)?></th>
			<th><?php echo $util->nf($SALDO)?></th>
			<th colspan="4"></th>
		</tr>
		
		<tr class="totales">
			<th colspan="2">TOTAL GENERAL</th>
			<th><?php echo $util->nf($TLIQUIDADO_PERIODO)?></th>
			<th><?php echo $util->nf($TLIQUIDADO_DEUDA)?></th>
			<th><?php echo $util->nf($TLIQUIDADO)?></th>
			<th><?php echo $util->nf($TIMPUTADO_PERIODO)?></th>
			<th><?php echo $util->nf($TIMPUTADO_DEUDA)?></th>
			<th><?php echo $util->nf($TIMPUTADO)?></th>
                        <th style="text-align: center"><?php echo $TNSOCIOS?></th>
			<th><?php echo $util->nf($TREVERSO)?></th>
			<th></th>
			<th><?php echo $util->nf($TCOMISION)?></th>	
			<th><?php echo $util->nf($TNETO_PROVEEDOR)?></th>			
			<th><?php echo $util->nf($TSALDO)?></th>
			<th colspan="4"></th>
		</tr>
		
	</table>	


	<?php //   debug($proveedores)?>

<?php endif;?>

