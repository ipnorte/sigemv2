<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>



<?php if(!empty($socios)):?>

	<?php //   debug($socios)?>
	<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar/'.$liquidacion['Liquidacion']['id'])?>
	<br/>	
	<!-- 
	<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td align="right">BANCO INTERCAMBIO</td>
			<td><strong><?php //   echo $util->banco($banco_intercambio)?></strong></td>
		</tr>
		<tr>
			<td align="right">FECHA DEBITO</td>
			<td><strong><?php //   echo $util->armaFecha($fechaDebito)?></strong></td>
		</tr>
	</table>
	</div>

	<table>			
		<tr>
			<td>LISTADO DE SOPORTE REGISTROS DISKETTE</td><td><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cbu_pdf/'.$liquidacion['Liquidacion']['id'],'controles/printer.png','',array('target' => 'blank'))?></td>
			<td>DESCARGAR ARCHIVO PARA DISKETTE</td><td><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cbu_txt/'.$liquidacion['Liquidacion']['id'],'controles/disk.png','',array('target' => 'blank'))?></td>
		</tr>
	</table>	
	
	-->
	<h3>DETALLE DE REGISTROS PROCESADOS</h3>
	<table>
		<tr>
			<th>#</th>
			<th>SOCIO</th>
			<th>CALIFICACION</th>
			<th>REG</th>
			<th>TURNO</th>
			<th>SUCURSAL - CUENTA</th>
			<th>CBU</th>
			<th>A DEBITAR</th>
			<th></th>
			<th>STATUS</th>
		</tr>
		<?php $reg = 0?>
		<?php $ACU_IMPORTE = 0?>
		<?php $ACU_LIQUI = 0?>
		<?php $ACU_CANTIDAD = 0?>
		<?php $EMPRESA = NULL;?>
		<?php $PRIMERO = TRUE;?>
		<?php $ACU_IMPO_PARCIAL=0;?>
		<?php $ACU_LIQUI_PARCIAL=0;?>
		<?php $ACU_CANTIDAD_PARCIAL=0;?>
		<?php $ACU_ERRORES = 0?>
		<?php $ACU_ERRORESP = 0?>
		<?php $i=0;?>
		
		<?php foreach($socios as $socio):?>
			<?php //   debug($socio)?>
			
			<?php $reg++?>
			<?php $i++?>
			<?php if($socio['LiquidacionSocio']['error_cbu'] == 1 ) ++$ACU_ERRORES;?>
			
			<?php if($EMPRESA != $socio['LiquidacionSocio']['codigo_empresa']):?>
			
				
				<?php if($PRIMERO):?>
					<?php $PRIMERO = FALSE;?>
				<?php else:?>
							
					<tr class="totales">
						<th colspan="7">SUBTOTAL <?php echo $util->globalDato($EMPRESA,'concepto_1')?> (<?php echo $ACU_CANTIDAD_PARCIAL?> REGISTROS A ENVIAR) <?php echo ($ACU_ERRORESP != 0 ? "<span style='color:red;'>ERRORES: $ACU_ERRORESP</span>":"")?></th>
						<th><?php echo $util->nf($ACU_IMPO_PARCIAL)?></th>
						<th></th>
						<th></th>
					</tr>
					<tr><td colspan="10"></td></tr>
					
					<tr>
						<th>#</th>
						<th>SOCIO</th>
						<th>CALIFICACION</th>
						<th>REG</th>
						<th>TURNO</th>
						<th>SUCURSAL - CUENTA</th>
						<th>CBU</th>
						<th>A DEBITAR</th>
						<th></th>
						<th>STATUS</th>
					</tr>					
					
					<?php $ACU_IMPO_PARCIAL = 0;?>
					<?php $ACU_LIQUI_PARCIAL = 0;?>
					<?php $ACU_CANTIDAD_PARCIAL = 0;?>	
					<?php $ACU_ERRORESP = 0?>			
				
				<?php endif;?>
				
				<?php $EMPRESA = $socio['LiquidacionSocio']['codigo_empresa'];?>
			
			<?php endif;?>
			<?php if($socio['LiquidacionSocio']['error_cbu'] == 1 ) ++$ACU_ERRORESP;?>
			<?php $ACU_LIQUI_PARCIAL += $socio['LiquidacionSocio']['importe_dto'];?>
			<?php if($socio['LiquidacionSocio']['error_cbu'] == 0 ){
				$ACU_IMPO_PARCIAL += $socio['LiquidacionSocio']['importe_adebitar'];
			}?>
			<?php if($socio['LiquidacionSocio']['error_cbu'] == 0 ) ++$ACU_CANTIDAD_PARCIAL;?>
			
			
			<?php $apenom = $socio['LiquidacionSocio']['documento'] ." - <strong>" .$socio['LiquidacionSocio']['apenom']."</strong>";?>
			<?php $ACU_LIQUI += $socio['LiquidacionSocio']['importe_dto'];?>			
			<?php if($socio['LiquidacionSocio']['error_cbu'] == 0 ){
				$ACU_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];
			}?>
			<?php if($socio['LiquidacionSocio']['error_cbu'] == 0 ) ++$ACU_CANTIDAD;?>
			
			<tr class="<?php echo ($socio['LiquidacionSocio']['error_cbu'] == 1 ? "grilla_error" : "")?>" id="TRL_<?php echo $i?>">
				<td align="center"><?php echo $reg?></td>
				<td nowrap="nowrap"><?php echo $this->renderElement('socios/link_to_liquidacion',array('plugin' => 'pfyj', 'texto' =>  $apenom,'socio_id' => $socio['LiquidacionSocio']['socio_id']))?></td>
				<td align="center"><?php echo $util->globalDato($socio['LiquidacionSocio']['ultima_calificacion'])?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['registro']?></td>
				<td><?php echo $socio['LiquidacionSocio']['turno']?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['sucursal']?> - <?php echo $socio['LiquidacionSocio']['nro_cta_bco']?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['cbu']?></td>
				<td align="right"><?php echo $util->nf(($socio['LiquidacionSocio']['error_cbu'] == 0 ? $socio['LiquidacionSocio']['importe_adebitar'] : 0))?></td>
				<td><?php echo $controles->btnModalBox(array('title' => 'LIQUIDACION '.$util->periodo($liquidacion['Liquidacion']['periodo'],true),'url' => '/mutual/liquidaciones/by_socio_periodo/'.$socio['LiquidacionSocio']['socio_id'].'/'.$liquidacion['Liquidacion']['periodo'],'h' => 450, 'w' => 950))?></td>
				<td align="center"><strong><?php echo $socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] ?></strong></td>
			</tr>
		
		<?php endforeach;?>
		
			<tr class="totales">
				<th colspan="7">SUBTOTAL <?php echo $util->globalDato($EMPRESA,'concepto_1')?> (<?php echo $ACU_CANTIDAD_PARCIAL?> REGISTROS A ENVIAR) <?php echo ($ACU_ERRORESP != 0 ? "<span style='color:red;'>ERRORES: $ACU_ERRORESP</span>":"")?></th>
				<th><?php echo $util->nf($ACU_IMPO_PARCIAL)?></th>
				<th></th>
				<th></th>
			</tr>		
		<tr><td colspan="10"></td></tr>
		<tr class="totales">
			<th align="right" colspan="7"><h3>TOTAL GENERAL (<?php echo $ACU_CANTIDAD?> REGISTROS A ENVIAR) <?php echo ($ACU_ERRORES != 0 ? "<span style='color:red;'>ERRORES: $ACU_ERRORES</span>":"")?> </h3></th>
			<th align="right"><h3><?php echo $util->nf($ACU_IMPORTE)?></h3></th>
			<th></th>
			<th></th>
		</tr>
		
	</table>
	<?php echo $this->renderElement("banco/info_diskette",array('plugin' => 'config', 'uuid' => $DISKETTE_UUID, 'listado' => '/mutual/liquidaciones/diskette_cbu_pdf/' . $liquidacion['Liquidacion']['id']."/0"))?>
	<BR/>
	<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar/'.$liquidacion['Liquidacion']['id'])?>
<?php endif;?>