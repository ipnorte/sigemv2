<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: GENERAR ARCHIVO PARA DEBITO POR CBU'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3>GENERAR ARCHIVO PARA DEBITO POR CBU (CUOTA DEL PERIODO)</h3>

<?php if(!empty($datos)):?>

	<?php //   debug($datos)?>

	<?php echo $frm->create(null,array('action'=>'diskette_cjp_nocob_cbu/'.$liquidacion['Liquidacion']['id'],'id' => 'formGenFile'))?>

	<table>
	
		<tr>
			<th>#</th>
			<th>DOCUMENTO</th>
			<th>SOCIO</th>
			<th>TIPO</th>
			<th>LEY</th>
			<th>BENEFICIO</th>
			<th>SUB</th>
			<th>CBU</th>
			<th>SUCURSAL</th>
			<th>CUENTA</th>
			<th>IMPORTE</th>
			<th>A DEBITAR</th>
			<th>STATUS</th>
			
		</tr>
		<?php $reg = $ACU_ERRORES = $ACU_IMPORTE_DEBITA = $ACU_IMPORTE = $ACU_DEBITA_OK = $ACU_CANT_DEB_OK = 0?>
		<?php foreach($datos as $nro => $dato):?>
		
			<?php $reg++?>
			<?php if($dato['error_cbu'] == 1 ) ++$ACU_ERRORES;?>
			<?php $ACU_IMPORTE += $dato['LiquidacionSocio']['importe_original']?>
			<?php $ACU_IMPORTE_DEBITA += $dato['LiquidacionSocio']['importe_adebitar']?>
			
			<?php 
			
				if($dato['error_cbu'] == 0 ){
					$ACU_DEBITA_OK += $dato['LiquidacionSocio']['importe_adebitar'];
					$ACU_CANT_DEB_OK++;
				}
			
			?>
		
		
			<tr id="TRL_<?php echo $reg?>" class="<?php echo ($dato['LiquidacionSocio']['error_cbu'] == 1 ? "grilla_error" : "")?>">
			
				<td><?php echo $reg?></td>
				<td><?php echo $dato['LiquidacionSocio']['documento']?></td>
				<td nowrap="nowrap"><?php echo $this->renderElement('socios/link_to_liquidacion',array('plugin' => 'pfyj', 'texto' =>  $dato['LiquidacionSocio']['apenom'],'socio_id' => $dato['LiquidacionSocio']['socio_id']))?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['tipo']?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['nro_ley']?></td>
				<td align="center"><?php echo substr(str_pad(trim($dato['LiquidacionSocio']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6)?></td>
				<td><?php echo $dato['LiquidacionSocio']['sub_beneficio']?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['cbu']?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['cbu_sucursal']?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['cbu_nro_cta_bco']?></td>
				<td align="right"><?php echo $util->nf($dato['LiquidacionSocio']['importe_original'])?></td>
				<td align="right"><strong><?php echo $util->nf($dato['LiquidacionSocio']['importe_adebitar'])?></strong></td>
				<td align="left"><?php echo ($dato['LiquidacionSocio']['error_cbu'] == 1 ? $dato['LiquidacionSocio']['ERROR_INTERCAMBIO'] : "")?></td>
			</tr>
		
		
		<?php endforeach;?>
		
		<tr class="totales">
		
			<th colspan="10">TOTAL (<?php echo $reg?> REGISTROS) <?php echo ($ACU_ERRORES != 0 ? "<span style='color:red;'>ERRORES: $ACU_ERRORES</span>":"")?></th>
			<th><?php echo $util->nf($ACU_IMPORTE)?></th>
			<th><?php echo $util->nf($ACU_IMPORTE_DEBITA)?></th>
			<th><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_nocob_cbu/'.$liquidacion['Liquidacion']['id'].'/1','controles/ms_excel.png','',array('target' => '_blank'))?></th>
			
		</tr>
		
		<tr class="totales">
		
			<th colspan="10">TOTAL REGISTROS A DEBITAR (<?php echo $ACU_CANT_DEB_OK?> CON STATUS OK)</th>
			<th></th>
			<th><?php echo $util->nf($ACU_DEBITA_OK)?></th>
			<th></th>
			
		</tr>		
		
		<tr>
			<td align="right" colspan="3">GENERAR ARCHIVO PARA BANCO</td>
			<td colspan="10"><?php echo $this->requestAction('/config/bancos/combo/LiquidacionSocio.banco_intercambio/0/0/5')?></td>
		</tr>
		<tr>
			<td align="right" colspan="3">FECHA</td>
			<td colspan="10"><?php echo $frm->input('LiquidacionSocio.fecha_debito',array('dateFormat' => 'DMY'))?></td>
		</tr>
		<tr><td colspan="13" align="center"><?php echo $frm->submit("GENERAR DISKETTE",array('id' => 'btn_genDiskette'))?></td></tr>
		<?php if(!empty($DISKETTE_UUID)):?>
		<tr><td colspan="13"><?php echo $this->renderElement("banco/info_diskette",array('plugin' => 'config', 'uuid' => $DISKETTE_UUID, 'toPDF' => false ,'toXLS' => true ,'listado' => '/mutual/liquidaciones/diskette_cjp_nocob_cbu/' . $liquidacion['Liquidacion']['id']))?></td></tr>
		<?php endif;?>
	</table>
	
	<?php echo $frm->hidden('LiquidacionSocio.liquidacion_id', array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->end();?>




<?php endif;?>

