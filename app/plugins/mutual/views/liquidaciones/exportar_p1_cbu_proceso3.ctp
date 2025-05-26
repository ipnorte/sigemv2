<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if(!empty($envios)):?>
	<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar/'.$liquidacion['Liquidacion']['id'])?>
	<h3>DETALLE DE ARCHIVOS EMITIDOS</h3>
	<table>
		<tr>
			<th></th>
			<th>FECHA</th>
			<th>BANCO</th>
			<th>ARCHIVO</th>
			<th>REGISTROS</th>
			<th>IMPORTE</th>
			<th>EMITIDO EL</th>
			<th>OPERADOR</th>
			
		</tr>
		<?php foreach($envios as $envio):?>
			<tr>
				<td><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cbu_pdf/' . $liquidacion['Liquidacion']['id'] . '/1/' . $envio['LiquidacionSocioEnvio']['id'] .'/PDF','controles/pdf.png',null,array('target' => '_blank'))?></td>
				<td><?php echo $util->armaFecha($envio['LiquidacionSocioEnvio']['fecha_debito'])?></td>
				<td><?php echo $envio['LiquidacionSocioEnvio']['banco_nombre']?></td>
				<td><?php echo $envio['LiquidacionSocioEnvio']['archivo']?></td>
				<td align="center"><?php echo $envio['LiquidacionSocioEnvio']['cantidad_registros']?></td>
				<td align="right"><?php echo $util->nf($envio['LiquidacionSocioEnvio']['importe_debito'])?></td>
				<td><?php echo $envio['LiquidacionSocioEnvio']['created']?></td>
				<td align="center"><?php echo $envio['LiquidacionSocioEnvio']['user_created']?></td>
			</tr>
		
		<?php endforeach;?>
	</table>

<?php endif;?>

<?php //   debug($envios)?>

<div class="areaDatoForm">

	<h3>DETALLE DE TURNOS A PROCESAR</h3>

	
	
	<table>
		<tr>
			<th>COD</th>
			<th>EMPRESA - TURNO</th>
			<th>REG</th>
            <th>IMPORTE A LIQUIDADO</th>
			<th>IMPORTE A DEBITAR</th>
		</tr>
		<?php $ACU_REG = $ACU_IMP = $ACU_LIQ = 0;?>		
		<?php foreach($datos['LiquidacionSocioNoimputada']['turno_pago'] as $codigo => $turno):?>
			<?php 
				$aTrunos = explode("|",$turno);
				$ACU_REG += $aTrunos[0];
				$ACU_IMP += $aTrunos[2] / 100;
                $ACU_LIQ += $aTrunos[1] / 100;
			?>
			<tr>
				<td><?php echo substr(trim($codigo),-5,5)?></td>
				<td><?php echo $aTrunos[3]?></td>
				<td align="right"><?php echo $aTrunos[0]?></td>
                <td align="right"><?php echo $util->nf($aTrunos[1]/100)?></td>
				<td align="right" style="color: green;"><?php echo $util->nf($aTrunos[2]/100)?></td>
			</tr>
		
		<?php endforeach;?>
	
		<tr class="totales">
			<th colspan="2">TOTALES</th>
			<th><?php echo $ACU_REG?></th>
			<th><?php echo $util->nf($ACU_LIQ)?></th>
            <th style="color: green;"><?php echo $util->nf($ACU_IMP)?></th>            
		</tr>
		
		<tr>
			<td colspan="4"><strong><?php echo $util->banco($datos['LiquidacionSocioNoimputada']['banco_intercambio'])?></strong></td>
			<td align="right"><strong><?php echo $util->armaFecha($datos['LiquidacionSocioNoimputada']['fecha_debito'])?></strong></td>
		</tr>
		
	
	</table>

</div>

<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar3/'.$liquidacion['Liquidacion']['id'])?>
<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'genera_archivo3',
                                                                                'noStop' => TRUE,
										'accion' => '.mutual.liquidaciones.exportar3.'.$liquidacion['Liquidacion']['id'].'.1',
										'target' => '',
										'btn_label' => '',
										'titulo' => 'GENERAR DISKETTE II',
										'subtitulo' => $util->banco($datos['LiquidacionSocioNoimputada']['banco_intercambio'])." | FECHA DEBITO: ". $util->armaFecha($datos['LiquidacionSocioNoimputada']['fecha_debito']) . " | TOTAL $ ". $util->nf($ACU_IMP),
										'txt1' => $datos_serialized,
));

?>	