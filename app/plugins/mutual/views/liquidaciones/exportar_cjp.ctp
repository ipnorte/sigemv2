<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS :: ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'],'concepto_1')))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<?php //   if(!empty($socios)):?>
	<table>
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/0/A','controles/pdf.png','',array('target' => 'blank'))?></td><td>CUOTA SOCIAL ALTAS</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/0/A','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/0/A','controles/ms_excel.png')?></td>
		</tr>
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/0/B','controles/pdf.png','',array('target' => 'blank'))?></td><td>CUOTA SOCIAL BAJAS</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/0/B','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/0/B','controles/ms_excel.png')?></td>
		</tr>
		<!--				
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/1','controles/pdf.png','',array('target' => 'blank'))?></td><td>TODOS LOS CONSUMOS (ALTAS Y VIGENTES)</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/1','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png')?></td>
		</tr>
		-->
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/1/A','controles/pdf.png','',array('target' => 'blank'))?></td><td>CONSUMOS ALTAS Y MODIFICACIONES </td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/1/A','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/1/A','controles/ms_excel.png')?></td>
		</tr>		
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/1/B','controles/pdf.png','',array('target' => 'blank'))?></td><td>CONSUMOS BAJAS</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/1/B','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/1/B','controles/ms_excel.png')?></td>
		</tr>		
	
	</table>
	<h3>DETALLE DE ERRORES DETECTADOS</h3>
	<table>
		<tr>
			<th>#</th>
			<th>SOCIO</th>
			<th>TIPO</th>
			<th>LEY</th>
			<th>BENEFICIO</th>
			<th>SUB-BENEFICIO</th>
			<th>CODIGO DTO</th>
			<th>IMPORTE</th>
			<th>STATUS</th>
			<th>CRITERIO</th>
			<th>ERROR</th>
		</tr>
		<?php $reg = 0?>
		<?php $ACU_IMPORTE = 0?>
		<?php $ACU_ERRORES = 0?>
		<?php foreach($socios as $socio):?>
			<?php $reg++?>
			<?php if($socio['LiquidacionSocio']['error_cbu'] == 1 ) ++$ACU_ERRORES;?>
			<?php $apenom = $socio['LiquidacionSocio']['documento'] ." - <strong>" .$socio['LiquidacionSocio']['apenom']."</strong>";?>
			<?php $ACU_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar']?>
			<tr class="<?php echo ($socio['LiquidacionSocio']['error_cbu'] == 1 ? "grilla_error" : "")?>">
				<td align="center"><?php echo $reg?></td>
				<td nowrap="nowrap"><?php echo $this->renderElement('socios/link_to_estado_cuenta',array('plugin' => 'pfyj', 'texto' =>  $apenom,'socio_id' => $socio['LiquidacionSocio']['socio_id']))?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['tipo']?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['nro_ley']?></td>
				<td align="center"><?php echo substr(str_pad(trim($socio['LiquidacionSocio']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6)?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['sub_beneficio']?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['codigo_dto']?> - <?php echo $socio['LiquidacionSocio']['sub_codigo']?></td>
				<td align="right"><?php echo $util->nf($socio['LiquidacionSocio']['importe_adebitar'])?></td>
				<td align="center"><strong><?php echo $socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] ?></strong></td>
				<td><?php if(!empty($socio['LiquidacionSocio']['formula_criterio_deuda'])) echo $controles->linkModalBox($socio['LiquidacionSocio']['formula_criterio_deuda'],array('title' => 'ORDEN DE DESCUENTO #' . $socio['LiquidacionSocio']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$socio['LiquidacionSocio']['orden_descuento_id'].'/'.$socio['LiquidacionSocio']['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td><?php echo $socio['LiquidacionSocio']['error_intercambio']?></td>
			</tr>
		
		<?php endforeach;?>
		<tr class="totales">
			<th align="right" colspan="7">TOTAL (<?php echo $reg?> REGISTROS) <?php echo ($ACU_ERRORES != 0 ? "<span style='color:red;'>ERRORES: $ACU_ERRORES</span>":"")?></th>
			<th align="right"><?php echo $util->nf($ACU_IMPORTE)?></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</table>	

<?php //   endif;?>