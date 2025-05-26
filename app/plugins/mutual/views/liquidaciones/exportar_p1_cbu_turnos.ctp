<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS :: ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'],'concepto_1')))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if(!empty($turnos)):?>

	<?php //   debug($turnos)?>

	<script language="Javascript" type="text/javascript">
	
		var rows = <?php echo count($turnos)?>;

		Event.observe(window, 'load', function() {
			$('total_registros').update("0");
			$('total_liquidado').update("0");
			$('total_enviado').update("0");
			$('btn_genDiskette').disable();
			<?php if($liquidacion['Liquidacion']['imputada'] == 1): ?>
			$('formGenFile').disable();
			<?php endif;?>
			SelSum();
		});

		function unSellAll(){
			for (i=1;i<=rows;i++){
				oChkCheck = document.getElementById('chk_' + i);
				oChkCheck.checked = true;
			}
		}

		function SelSum(){
			var totalRegistros = 0;
			var totalLiquidado = 0;
			var totalEnviado = 0;

			for (i=1;i<=rows;i++){
				oChkCheck = document.getElementById('chk_' + i);
				valCheck = oChkCheck.value;
				aValCheck = valCheck.split("|");
				if (oChkCheck.checked){
					totalRegistros = totalRegistros + parseInt(aValCheck[0]);
					totalLiquidado = totalLiquidado + parseInt(aValCheck[1]);
					totalEnviado = totalEnviado + parseInt(aValCheck[2]);
				}
			}

			if(totalRegistros != 0)$('btn_genDiskette').enable();				
			else $('btn_genDiskette').disable();				

			totalLiquidado = FormatCurrency(totalLiquidado / 100);
			totalEnviado = FormatCurrency(totalEnviado / 100);

			$('total_registros').update(totalRegistros);
			$('total_liquidado').update(totalLiquidado);
			$('total_enviado').update(totalEnviado);			
			
		}
		
		function chkOnclick(idr,oCHK){
			toggleCell(idr,oCHK);
			 SelSum();
		}
			
	</script>
<!--	<div style="width:90%; ;height: 300px; overflow: scroll;">-->
	<?php echo $frm->create(null,array('action'=>'exportar/'.$liquidacion['Liquidacion']['id'],'id' => 'formGenFile'))?>
	<table>
		<tr>
			<th>CODIGO</th>
			<th>EMPRESA - TURNO</th>
			<th>REGISTROS</th>
			<th>IMPORTE LIQUIDADO</th>
			<th>IMPORTE A DEBITAR</th>
<!--			<th>FECHA</th>-->
<!--			<th></th>			-->
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<?php $i=0;?>
		<?php foreach($turnos as $turno):?>
			<?php //   debug($turno)?>
			<?php 
				$aTrunos = explode("-",$turno['descripcion']);
			?>
			<?php $i++;?>
			<?php $valCheck = $turno['cantidad']."|".($turno['importe_dto']*100)."|".($turno['importe_adebitar']*100)?>
			<tr id="TRL_<?php echo $i?>" class="<?php echo ($turno['error_turno'] == 1 ? "grilla_error" : "")?>">
				<td><?php echo substr(trim($turno['turno_pago']),-5,5)?></td>
				<td><strong><?php echo $aTrunos[0]?></strong><?php echo (isset($aTrunos[1]) ? " - " . $aTrunos[1] : "")?></td>
				<td align="center"><?php echo $turno['cantidad']?></td>
				<td align="right"><?php echo $util->nf($turno['importe_dto'])?></td>
				<td align="right"><?php echo $util->nf($turno['importe_adebitar'])?></td>
<!--				<td align="center"><?php //   echo $util->armaFecha($turno['fecha_debito'])?></td>-->
<!--				<td align="center"><?php //   echo (!empty($turno['fecha_debito']) ? $controles->onOff(1):"")?></td>-->
				<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_turno_diskette/'.$liquidacion['Liquidacion']['id'].'/'.$turno['turno_pago'],'controles/disk.png')?></td>
				<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_turno_pdf/'.$liquidacion['Liquidacion']['id'].'/'.$turno['turno_pago'],'controles/pdf.png','',array('target' => 'blank'))?></td>
<td><input type="checkbox" name="data[LiquidacionSocio][turno_pago][<?php echo $turno['turno_pago']?>]" value="<?php echo $valCheck?>" id="chk_<?php echo $i?>" onclick="chkOnclick('TRL_<?php echo $i?>',this)"></td>				
			</tr>		
		<?php endforeach;?>
	</table>
<!--	</div>-->
	<table>
		<tr>
			<th colspan="3">DATOS PARA GENERAR DISKETTE</th>
		</tr>
		<tr>
			<th>TOTAL REGISTROS</th>
			<th>TOTAL LIQUIDADO</th>
			<th>TOTAL A DEBITAR</th>
		</tr>
		<tr>
			<td align="center"><h3><span id="total_registros"></span></h3></td>
			<td align="right"><h3><span id="total_liquidado"></span></h3></td>
			<td align="right"><h3><span id="total_enviado"></span></h3></td>
		</tr>
		<tr>
			<td colspan="3"><hr/></td>
		</tr>		
		<?php if($liquidacion['Liquidacion']['mostar_bancos'] == 1):?>
		<tr>
			<td align="right">GENERAR ARCHIVO PARA BANCO</td><td colspan="2"><?php echo $this->requestAction('/config/bancos/combo/LiquidacionSocio.banco_intercambio/0/0/5')?></td>
		</tr>
		<tr>
			<td align="right">FECHA</td><td colspan="2"><?php echo $frm->input('LiquidacionSocio.fecha_debito',array('dateFormat' => 'DMY'))?></td>
		</tr>
		<tr id="bcoNacionNroArchivo">
			<td align="right">NUMERO DE ARCHIVO</td>
			<td><?php echo $frm->number('LiquidacionSocio.nro_archivo',array('maxlength' => 4,'size' => 4))?></td>
		</tr>
		

		<?php endif;?>
		<tr><td colspan="3" align="center"><?php echo $frm->submit("PROCESAR DATOS PARA GENERAR DISKETTE",array('id' => 'btn_genDiskette'))?></td></tr>	
	</table>
	<?php echo $frm->hidden('LiquidacionSocio.liquidacion_id', array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->hidden('LiquidacionSocio.periodo', array('value' => $liquidacion['Liquidacion']['periodo']))?>
	<?php echo $frm->hidden('LiquidacionSocio.codigo_organismo', array('value' => $liquidacion['Liquidacion']['codigo_organismo']))?>
	
	<?php echo $frm->end();?>
<?php //   debug($turnos)?>
<?php endif;?>