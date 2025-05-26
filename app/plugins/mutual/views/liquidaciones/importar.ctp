<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: IMPORTAR DATOS','plugin' => 'config'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<?php //   debug($archivos) ?>
<?php //   debug($liquidacion) ?>
<script language="Javascript" type="text/javascript">

Event.observe(window, 'load', function() {

	<?php if($liquidacion['Liquidacion']['imputada'] == 1): ?>
		$('formUpLoadFile').disable();
		$("processAsincContainer").hide();
		$("formFileUpContainer").hide();
	<?php endif;?>	

	

;});

function FormUpload(disable){
	if(disable===1)$('formUpLoadFile').disable();
	else $('formUpLoadFile').enable();
}

function validateForm(){
	var proveedorName = getTextoSelect("LiquidacionIntercambioProveedorId");
	var fileName = $('LiquidacionIntercambioArchivo').getValue();
	var bcoName = getTextoSelect("LiquidacionIntercambioBancoId");
	if(fileName === ""){
		alert("DEBE INDICAR EL ARCHIVO A SUBIR!");
		$('LiquidacionIntercambioArchivo').focus();
		return false;
	}
	var msg = "ATENCION!";
	msg = msg + "\n";
	msg = msg + "ASOCIAR EL ARCHIVO " + fileName + " AL BANCO " +  bcoName;
	if(proveedorName !== ""){
		msg = msg + "\n";
		msg = msg + "E IMPUTAR LA COBRANZA A " + proveedorName;
	}
	msg = msg + "?";
	return confirm(msg);
}

</script>

<?php if(!empty($archivos)):?>
	<h3>ARCHIVOS RECIBIDOS VINCULADOS A ESTA LIQUIDACION</h3>

	<?php if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) == 77): ?>
		<?php if($liquidacion['Liquidacion']['recibo_id'] > 0):?><td><?php echo $html->link($liquidacion['Liquidacion']['recibo_link'],'/mutual/liquidaciones/editRecibojp/'.$liquidacion['Liquidacion']['id'])?></td>  
		<?php elseif($liquidacion['Liquidacion']['importe_cobrado'] > 0):?><td><?php echo $controles->botonGenerico('/mutual/liquidaciones/addRecibojp/'.$liquidacion['Liquidacion']['id'],'controles/book_open.png','GENERAR RECIBO DE INGRESO') ?></td>  
		<?php else:?>  <td></td>
		<?php endif; ?>
	<?php endif;?>
        <div class="actions">
            <?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_archivo/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png','REPORTE GENERAL',array('target' => 'blank'))?>
            <?php if($liquidacion['Liquidacion']['imputada'] == 0) echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'].'/?action=dropAll','controles/user-trash-full.png','ANULAR LA PRE-IMPUTACION',null,"ANULAR LA PRE-IMPUTACION? \\nTodos los archivos seran marcados como NO PROCESADOS.")?>
        </div>

        <div id="grilla_archivos_importados">
       
		<table>
			<tr>
				<td colspan="<?php echo (substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) != '77' ? 11 : 10)?>">
                    <?php if($liquidacion['Liquidacion']['imputada'] == 0): ?>
                        <?php echo $controles->btnAjax('controles/arrow_refresh.png','/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'],'grilla_archivos_importados')?>
                    <?php endif;?>                 
                </td>
				<td></td>
                <td></td>
			</tr>
			<tr>
				<th>#</th>
				<th>UPLOAD</th>
				<th>BANCO</th>
				<th>ARCHIVO</th>
				<th>TOTAL REG.</th>
				<?php if($liquidacion['Liquidacion']['codigo_organismo'] != 'MUTUCORG7701'): ?>
					
					<th>REG.COB.</th>
					<th>IMP.COB.</th>
					<th>RECIBO</th>
				<?php endif;?>
				<th>OBS</th>
				<th>FRAG.</th>
				<th>PRE-IMP</th>
				<th>IMPUTAR A</th>
				<th>
					<?php // if($liquidacion['Liquidacion']['imputada'] == 0) echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'].'/?action=dropAll','controles/user-trash-full.png',null,null,"ELIMINAR TODOS LOS ARCHIVOS SUBIDOS AL SERVIDOR?")?>
				</th>
				<th></th>
				<th></th>
				<th></th>
                                <th></th>
			</tr>
		<?php $ACU_REGISTROS = $ACU_REGISTROS_COBRADOS = $ACU_IMPORTE_COBRADO = 0;?>	
		<?php foreach($archivos as $archivo):?>
		
			<?php 
			// debug($archivo);
			$ACU_REGISTROS += $archivo['LiquidacionIntercambio']['total_registros'];
			$ACU_REGISTROS_COBRADOS += $archivo['LiquidacionIntercambio']['registros_cobrados'];
			$ACU_IMPORTE_COBRADO += $archivo['LiquidacionIntercambio']['importe_cobrado'];
			?>
		
			<tr class="<?php echo ($archivo['LiquidacionIntercambio']['proveedor_id'] != 0 ? "amarillo" : "")?>">
				<td><?php echo $archivo['LiquidacionIntercambio']['id']?></td>
				<td><?php echo $util->armaFecha($archivo['LiquidacionIntercambio']['created'])?></td>
				<td><?php echo $archivo['LiquidacionIntercambio']['banco_intercambio']?></td>
				<td><a href="<?php echo $this->base?>/<?php echo $archivo['LiquidacionIntercambio']['archivo_file']?>" target="_blank"><?php echo $archivo['LiquidacionIntercambio']['archivo_nombre']?></a></td>
				<td align='right'><?php echo $archivo['LiquidacionIntercambio']['total_registros'] ?></td>
				<?php if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) != '77'): ?>
					<td align='right'><?php echo $archivo['LiquidacionIntercambio']['registros_cobrados'] ?></td>
					<td align='right'><?php echo $util->nf($archivo['LiquidacionIntercambio']['importe_cobrado']) ?></td>
					<?php if($archivo['LiquidacionIntercambio']['recibo_id'] > 0):?><td><?php echo $html->link($archivo['LiquidacionIntercambio']['recibo_link'],'/mutual/liquidaciones/editRecibo/'.$archivo['LiquidacionIntercambio']['id'].'/'.$archivo['LiquidacionIntercambio']['liquidacion_id'])?></td>  
					<?php elseif($archivo['LiquidacionIntercambio']['procesado'] == '1'):?><td><?php echo $controles->botonGenerico('/mutual/liquidaciones/addRecibo/'.$archivo['LiquidacionIntercambio']['id'].'/'.$archivo['LiquidacionIntercambio']['liquidacion_id'],'controles/book_open.png','') ?></td>  
					<?php else:?>  
					<td></td>
					<?php endif; ?>
				<?php endif;?>
				<td><?php echo $archivo['LiquidacionIntercambio']['observaciones']?></td>
				<td align="center"><?php echo $controles->onOff($archivo['LiquidacionIntercambio']['fragmentado'])?></td>
				<td align="center"><?php echo $controles->onOff($archivo['LiquidacionIntercambio']['procesado'])?></td>
				
				<td align="center" style="font-weight: bold;"><?php echo $archivo['LiquidacionIntercambio']['proveedor_razon_social_resumida']?></td>
				
				<td><?php if($archivo['LiquidacionIntercambio']['procesado'] == 0 && $liquidacion['Liquidacion']['imputada'] == 0) echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'].'/?action=dropOne&file='.$archivo['LiquidacionIntercambio']['id'],'controles/user-trash-full.png',null,null,"ELIMINAR EL ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']."?")?></td>
				<td><?php if($archivo['LiquidacionIntercambio']['fragmentado'] == 1) echo $controles->botonGenerico('/mutual/liquidaciones/detalle_archivo/'.$liquidacion['Liquidacion']['id'] . '/' . $archivo['LiquidacionIntercambio']['id'],'controles/ms_excel.png','',array('target' => 'blank')) ?></td>
				<td><?php if($liquidacion['Liquidacion']['imputada'] == 0 && $archivo['LiquidacionIntercambio']['fragmentado'] == 1) echo $controles->botonGenerico('/mutual/liquidacion_socios/reprocesar_archivo/'.$archivo['LiquidacionIntercambio']['id'],'controles/disk.png','',array('target' => 'blank')) ?></td>
                                <td><?php if($liquidacion['Liquidacion']['imputada'] == 2 && $archivo['LiquidacionIntercambio']['fragmentado'] == 1) echo $controles->botonGenerico('/mutual/liquidacion_socio_rendiciones/imputar_archivo/'.$archivo['LiquidacionIntercambio']['id'],'controles/money_add.png') ?></td>
			</tr>
		<?php endforeach;?>
		<?php if($liquidacion['Liquidacion']['codigo_organismo'] != 'MUTUCORG7701'): ?>
			<tr class="totales">
				<th colspan="4">TOTAL</th>
				<th><?php echo $ACU_REGISTROS?></th>
				<th><?php echo $ACU_REGISTROS_COBRADOS?></th>
				<th><?php echo $util->nf($ACU_IMPORTE_COBRADO)?></th>
				<th colspan="10"></th>
			</tr>
		
		<?php endif;?>
		
		
	
		</table>
	</div>
	<div id="processAsincContainer" class="areaDatoForm3">
		<?php if($liquidacion['Liquidacion']['imputada'] == 0): ?>
			<h4>PROCESO DE PREIMPUTACION DE LOS ARCHIVOS</h4>
			<?php 
			echo $this->renderElement('show',array(
													'plugin' => 'shells',
//													'pUID' => $liquidacion['Liquidacion']['asincrono_id'],
													'process' => 'procesa_archivo2',
													'accion' => '.mutual.liquidaciones.resumen_cruce_informacion.'.$archivo['LiquidacionIntercambio']['liquidacion_id'],
													'target' => '',
													'btn_label' => 'RESUMEN IMPORTACION',
													'titulo' => 'PROCESA LOS ARCHIVOS DE RENDICION [PRE-IMPUTACION]',
													'subtitulo' => '#' . $liquidacion['Liquidacion']['id'] . ' - ' . $liquidacion['Liquidacion']['organismo']." | ".$liquidacion['Liquidacion']['periodo_desc'],
													'p1' => $archivo['LiquidacionIntercambio']['liquidacion_id'],
													'remote_call_start' => 'FormUpload(1)',
													'remote_call_stop' => 'FormUpload(0)'
			));
			
			?>
<!--						<div class="notices"><strong>ATENCION!:</strong> Mientras se encuentra ejecut&aacute;ndose el proceso <strong>NO CERRAR ESTA VENTANA!</strong></div>	-->
		<?php endif;?>
	</div>
<?php endif;?>

<div class="areaDatoForm" id="formFileUpContainer">

		<h3>SUBIR ARCHIVO AL SERVIDOR</h3>
		<?php echo $frm->create(null,array('action'=>'importar/'.$liquidacion['Liquidacion']['id'],'type' => 'file','id' => 'formUpLoadFile', "onsubmit" => "return validateForm();"))?>
		<table class="tbl_form">
			
				<?php if($liquidacion['Liquidacion']['mostar_bancos'] == 1):?>
					<tr>
					<td align="right">BANCO EMISOR</td>
					<td>
						<?php echo $this->renderElement('banco/combo_global', array(
						
							'plugin' => 'config',
							'model' => 'LiquidacionIntercambio.banco_id',
							'tipo' => 5,
							'empty' => false,
							'selected' => (isset($this->data['LiquidacionIntercambio']['banco_id']) ? $this->data['LiquidacionIntercambio']['banco_id'] : "")
						
						))?>				
					</td>
					</tr>
				<?php endif;?>
				
			
			<tr><td>ARCHIVO</td><td><div class="input select"><label for="LiquidacionIntercambioArchivo"></label><?php echo $frm->file('LiquidacionIntercambio.archivo',array('size' => 25))?></div></td></tr>		
			<tr><td colspan="2">OBSERVACIONES</td></tr>
			<tr>
				<td colspan="2"><?php echo $frm->textarea('LiquidacionIntercambio.observaciones',array('cols' => 60, 'rows' => 10))?></td>
			</tr>
			<tr>
			<tr>
				<td align="center"><?php echo $frm->submit("SUBIR ARCHIVO",array('id' => 'btn_fileUp'))?></td>
				<td>
                    <div class="submit"><input type="submit" name="data[LiquidacionIntercambio][subdividir]" value="*** SUBDIVIDIR ARCHIVO POR LIQUIDACIONES ***"/></div>                    
					<?php echo $frm->btnForm(array('URL' => '/mutual/liquidaciones/importar_generar_lote/' . $liquidacion['Liquidacion']['id'].'/00011','LABEL' => 'BANCO NACION ** GENERAR LOTE DE RENDICION **'))?>
					<?php echo $frm->btnForm(array('URL' => '/mutual/liquidaciones/importar_generar_lote/' . $liquidacion['Liquidacion']['id'].'/99999','LABEL' => 'MUTUAL ** GENERAR LOTE DE RENDICION **'))?>
				</td>
			</tr>
		</table>
		<?php echo $frm->hidden('LiquidacionIntercambio.liquidacion_id', array('value' => $liquidacion['Liquidacion']['id']))?>
		<?php echo $frm->hidden('LiquidacionIntercambio.periodo', array('value' => $liquidacion['Liquidacion']['periodo']))?>
		<?php echo $frm->hidden('LiquidacionIntercambio.codigo_organismo', array('value' => $liquidacion['Liquidacion']['codigo_organismo']))?>
	
		<?php echo $frm->end();?>
	<div style="clear: both;"></div>
    <?php if(!empty($files)):?>
    <div class="areaDatoForm3">
        <h3>SUBDIVISION DEL LOTE :: DETALLE DE ARCHIVOS GENERADOS</h3>
        
        <table>
            <tr>
                <th></th>
                <th>LIQ</th>
                <th>ARCHIVO</th>
                <th>REGISTROS</th>
            </tr>
            <?php $registros = 0;?>
            <?php foreach($files as $lid => $file):?>
            <?php $registros += $file['lineas'];?>
            <tr>    
                <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'].'/'.$file['uuid'],'controles/disk.png','',array('target' => '_blank'))?></td>
                <td style="font-weight: bold;"><?php echo $lid?></td>
                <td><?php echo $file['archivo']?></td>
                <td style="text-align: center;"><?php echo $file['lineas']?></td>
            </tr>
            <?php endforeach;?>
            <tr class="subtotales">
                <th colspan="3">Total Registros Le&iacute;dos</th>
                <th><?php echo $registros?></th>
            </tr>    
        </table>    
        
            <?php //   debug($files)?>
        
        
    </div>
    <?php endif;?>
</div>	
</div>	
