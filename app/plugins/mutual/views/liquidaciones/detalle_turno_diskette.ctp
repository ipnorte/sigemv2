<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: DETALLE DEL TURNO '))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3><?php echo $descripcion_turno?></h3>

<?php if(!empty($socios)):?>

	<script type="text/javascript">
	
		var rows = <?php echo count($socios)?>;

		Event.observe(window, 'load', function(){
			
		<?php 
		$i = 0;
		foreach($socios as $socio):
			$i++;
			if($socio['LiquidacionSocio']['diskette'] == 1) echo "document.getElementById('chk_".$i."').checked = false;";
			else echo "document.getElementById('chk_".$i."').checked = true;";
			echo "chkOnclick('TRL_".$i."',document.getElementById('chk_".$i."'));";
		endforeach;
		?>

		});
		
		function chkOnclick(idr,oCHK){
			var check = oCHK.checked;
			var celdas = $(idr).immediateDescendants();
			if(check)celdas.each(function(td){td.addClassName("activo_0");});
			else celdas.each(function(td){td.removeClassName("activo_0");});
		}

		function checkUncheck(accion){
			var form = $('formGenDiskette');
			var chks = form.getInputs('checkbox');
			var i = 1;
			chks.each(function(oCHK){
				oCHK.checked = accion;
				chkOnclick('TRL_' + i,oCHK);
				i++;
			});
		}

	</script>

	<?php echo $frm->create(null,array('action'=>'detalle_turno_diskette/'.$liquidacion['Liquidacion']['id'].'/'.$turno,'id' => 'formGenDiskette'))?>
	<div class="areaDatoForm">Seleccione los registros que <strong>NO se enviar&aacute;n</strong> en el diskette</div>
	<table>
		<tr>
			<th>#</th>
			<th>SOCIO</th>
			<th><?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_turno_diskette/'.$liquidacion['Liquidacion']['id'].'/'.$turno.'/SOCIO','controles/arrow_down.png')?></th>
			<th>CALIFICACION</th>
			<th>REG</th>
			<th>CBU</th>
			<th>SUCURSAL - CUENTA</th>
			<th>IMPORTE</th>
			<th><?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_turno_diskette/'.$liquidacion['Liquidacion']['id'].'/'.$turno.'/IMPORTE','controles/arrow_down.png')?></th>
			<th></th>
			<th></th>
			<th>REINT.<?php echo $util->periodo($ultimoPeriodoImputado)?></th>
			<th></th>
		</tr>
		
		<?php $i=0;?>
		<?php $reg = 0?>
		<?php $ACU_IMPORTE = 0?>
		
		<?php foreach($socios as $socio):?>
			<?php $reg++?>
			<?php $i++?>
			<?php $apenom = $socio['LiquidacionSocio']['documento'] ." - <strong>" .$socio['LiquidacionSocio']['apenom']."</strong>";?>		
			<?php $ACU_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar']?>
			<tr id="TRL_<?php echo $i?>">
				<td align="center"><?php echo $reg?></td>
				<td nowrap="nowrap"><?php echo $this->renderElement('socios/link_to_estado_cuenta',array('plugin' => 'pfyj', 'texto' =>  $apenom,'socio_id' => $socio['LiquidacionSocio']['socio_id']))?></td>
				<td></td>
				<td align="center"><?php echo $util->globalDato($socio['LiquidacionSocio']['ultima_calificacion'])?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['registro']?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['cbu']?></td>
				<td align="center"><?php echo $socio['LiquidacionSocio']['sucursal']?> - <?php echo $socio['LiquidacionSocio']['nro_cta_bco']?></td>
				<td align="right"><?php echo $util->nf($socio['LiquidacionSocio']['importe_adebitar'])?></td>
				<td></td>
				<td align="right"><?php echo $controles->onOff($socio['LiquidacionSocio']['diskette'])?></td>
				<td align="center"><input type="checkbox" name="data[LiquidacionSocio][noenvia_diskette][<?php echo $socio['LiquidacionSocio']['id']?>]" value="<?php echo $socio['LiquidacionSocio']['id']?>" id="chk_<?php echo $i?>" onclick="chkOnclick('TRL_<?php echo $i?>',this)"/></td>
				<td align="right" style="<?php echo ( $socio['LiquidacionSocio']['importe_reintegro_liquidacion_anterior'] != 0 ? "font-weight: bold; color: white; background-color: red;" : "")?>"><?php if($socio['LiquidacionSocio']['importe_reintegro_liquidacion_anterior'] != 0) echo $util->nf($socio['LiquidacionSocio']['importe_reintegro_liquidacion_anterior'])?></td>
				<td><?php if($socio['LiquidacionSocio']['importe_reintegro_liquidacion_anterior'] != 0) echo $controles->botonGenerico('/pfyj/socio_reintegros/by_socio/'.$socio['LiquidacionSocio']['socio_id'],'controles/search.png',null,array('target' => 'blank'))?></td>
			</tr>
		
		
		<?php endforeach;?>
		<tr class="totales">
			<th colspan="2">
				<input type="button" onclick="checkUncheck(true)" value="QUITAR TODOS"/>
				<input type="button" onclick="checkUncheck(false)" value="ENVIAR TODOS"/>
			</th>
			<th align="right" colspan="5">TOTAL GENERAL (<?php echo $reg?> REGISTROS)</th>
			<th align="right"><?php echo $util->nf($ACU_IMPORTE)?></th>
			<th></th>
			<th colspan="4"></th>
		</tr>		
		
	</table>		
	<?php echo $frm->hidden('LiquidacionSocio.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->hidden('LiquidacionSocio.turno_pago',array('value' => $turno))?>
	
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/mutual/liquidaciones/exportar/".$liquidacion['Liquidacion']['id'] : $fwrd) ))?>
	
	
	<?php echo $frm->end()?>


<?php endif;?>