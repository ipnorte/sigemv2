<?php echo $this->renderElement('personas/padron_header',array('persona' => $socio))?>
<?php echo $this->requestAction('/pfyj/socios/view/'.$socio['Socio']['id'])?>
<h3>BAJA SOCIO</h3>
<script type="text/javascript">
    
    Event.observe(window, 'load', function(){
        fallecimiento_socio();
        $('SocioCodigoBaja').observe('change',function(){
            fallecimiento_socio();
        });
        
    });
    
    function fallecimiento_socio(){
        
        var codigo = $('SocioCodigoBaja').getValue();
//        alert(codigo);
        if(codigo === 'MUTUBASOBFAL'){
            $('SocioPeriodoHastaLiquidaMonth').disable();
            $('SocioPeriodoHastaLiquidaYear').disable();
            document.getElementById('SocioBajaDeuda').checked = true;
            $('SocioBajaDeuda').disable();
            $(contenedorMsgError).show();
            var msg = "<p>*** BAJA POR FALLECIMIENTO ***</p>";
            msg = msg + "<p>Se procede a la baja de TODO lo relacionado a esta persona. Se toma como fecha de fallecimiento a la fecha indicada. </p>";
            $(contenedorMsgError).update(msg);
        }else{
            $('SocioPeriodoHastaLiquidaMonth').enable();
            $('SocioPeriodoHastaLiquidaYear').enable();  
            $('SocioBajaDeuda').enable();
            if(document.getElementById('SocioBajaDeuda').checked) document.getElementById('SocioBajaDeuda').checked = false;
            $('mensaje_error_js').hide();
            $('mensaje_error_js').update("");
        }    
    
    }
	function confirmarForm(){

		var msgConfirm = "ATENCION!\n\n";

		msgConfirm = msgConfirm + "DAR DE BAJA AL SOCIO #<?php echo $socio['Socio']['id']?>\n";
		msgConfirm = msgConfirm + "<?php echo $util->globalDato($socio['Persona']['tipo_documento'])." ".$socio['Persona']['documento']." - ".$socio['Persona']['apellido'].", ".$socio['Persona']['nombre']?>";
		msgConfirm = msgConfirm + "\n\n";

		msgConfirm = msgConfirm + "MOTIVO: " + getTextoSelect('SocioCodigoBaja') + "\n";
		msgConfirm = msgConfirm + "FECHA: " + getStrFecha('SocioFechaBaja') + "\n";
                msgConfirm = msgConfirm + "\n\n";
		msgConfirm = msgConfirm + "BAJA A PARTIR DE: *** " + getStrPeriodo('SocioPeriodoHastaLiquida') + " ***\n";
                msgConfirm = msgConfirm + "\n\n";
		if(document.getElementById("SocioBajaDeuda").checked){
			msgConfirm = msgConfirm + "ATENCION!: *** BAJA DE CUOTAS ADEUDADAS ***" + "\n";
		}
//		msgConfirm = msgConfirm + "\n\n";
		var oOdto = document.getElementById("ordenDescuentoAVencer");
		if(oOdto != null){
			impoAvencerTXT = new Number(oOdto.value);
			impoAvencerTXT = impoAvencerTXT.toFixed(2);
			
			if(impoAvencerTXT != 0) msgConfirm = msgConfirm + "\nDEUDA A VENCER: " + impoAvencerTXT;
		}
		return confirm(msgConfirm);
		
	}
	
</script>
<?php echo $this->renderElement('orden_descuento/grilla_ordenes_by_socio',array('plugin' => 'mutual','socio_id' => $socio['Socio']['id'],'estado_actual' => 0))?>

<?php echo $form->create(null,array('name'=>'formBajaSocio','id'=>'formBajaSocio','onsubmit' => "return confirmarForm()",'action' => 'baja/'. $socio['Socio']['id']));?>
<div class="areaDatoForm">
	<table class="tbl_form">
	
			<tr>
				<td>CAUSA DE LA BAJA</td>
				<td>
					<?php echo $this->renderElement('global_datos/combo',array(
																						'plugin'=>'config',
																						'label' => '.',
																						'model' => 'Socio.codigo_baja',
																						'prefijo' => 'MUTUBASO',
																						'disable' => false,
																						'empty' => false,
																						'selected' => '0',
																						'logico' => true,
					))?>				
				</td>
			</tr>
			<tr>
				<td>FECHA</td>
				<td><?php echo $frm->input('Socio.fecha_baja',array('dateFormat' => 'DMY'))?></td>
			</tr>
			<tr>
				<td>BAJA A PARTIR DE</td>
				<td><?php echo $frm->periodo('Socio.periodo_hasta_liquida',NULL,NULL,date('Y')-1,date('Y')+1,false)?></td>
			</tr>                       
			<tr>
				<td>BAJA DEUDA ANTERIOR</td>
				<td><input type="checkbox" name="data[Socio][baja_deuda]" id="SocioBajaDeuda" value="1"/></td>
			</tr>									
			<tr>
				<td valign="top">OBSERVACIONES</td>
				<td><?php echo $frm->textarea('Socio.observaciones',array('cols' => 60, 'rows' => 10))?></td>
			</tr>
	</table>
</div>
<?php echo $frm->hidden('Socio.activo',array('value' => 0)); ?>
<?php echo $frm->hidden('Socio.persona_id',array('value' => $socio['Persona']['id']));?>
<?php echo $frm->hidden('Socio.id',array('value' => $socio['Socio']['id'])); ?>
<?php echo $frm->hidden('Socio.idr',array('value' => $socio['Socio']['idr'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'PROCESAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$socio['Persona']['id'] : $fwrd) ))?>
<?php // debug($socio)?>