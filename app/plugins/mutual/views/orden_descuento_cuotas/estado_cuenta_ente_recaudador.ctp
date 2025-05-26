<?php
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>INFORME ENTES RECAUDADORES</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div id="FormSearch">
	<?php 
	
	echo $ajax->form(array('type' => 'post',
	    'options' => array(
	        'model'=>'OrdenDescuentoCuota',
	        'update'=>'informe_ente_recaudador',
	        'url' => array('plugin' => 'mutual','controller' => 'orden_descuento_cuotas', 'action' => 'ente_recaudador/'.$socio['Socio']['id']),
			'loading' => "$('spinner').show();$('detalle_estado_cuenta').hide();",
			'complete' => "$('informe_ente_recaudador').show();$('spinner').hide();"
	    )
	));
	?>
        <table>
            <tr>
                <td>
                    <?php echo $this->renderElement('global_datos/combo_global',array(
                                                                                    'plugin'=>'config',
                                                                                    'label' => "ENTE RECAUDADOR",
                                                                                    'model' => 'OrdenDescuentoCuota.ente_recaudador',
                                                                                    'prefijo' => 'MUTUCORG',
                                                                                    'disabled' => false,
                                                                                    'empty' => false,
                                                                                    'metodo' => "get_ente_recaudadores",
                                                                                    'selected' => ""	
                    ))?>				
                </td>
                <td>
<div class="input select"><label for="OrdenDescuentoCuotaTipoReporte">TIPO INFORME</label><select name="data[OrdenDescuentoCuota][tipo_reporte]"  id="OrdenDescuentoCuotaTipoReporte">
<option value="1">ESTADO DE CUENTA</option>
<option value="2">LIQUIDACION DE DEUDA</option>
</select></div>				                    
                </td>
                <td colspan="2"><?php echo $frm->submit('CONSULTAR',array('class' => 'btn_consultar'));?></td>

            </tr>
        </table>    
    <?php echo $frm->end();?> 
</div>
<?php echo $controles->ajaxLoader('spinner','CONSULTANDO DATOS EN EL ENTE RECAUDADOR...')?>
<div id="informe_ente_recaudador"></div>