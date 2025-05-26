<?php echo $this->renderElement('head',array('title' => 'APROBAR SOLICITUD DE CREDITO #'.$mutual_producto_solicitud_id,'plugin' => 'config'))?>
<?php echo $this->requestAction('/mutual/mutual_producto_solicitudes/view/'.$mutual_producto_solicitud_id)?>
	<?php 
	echo $ajax->form(array('type' => 'post',
	    'options' => array(
	        'model'=>'MutualProductoSolicitudPago',
	        'update'=>'grilla_forma_pagos',
	        'url' => array('plugin' => 'mutual','controller' => 'mutual_producto_solicitudes', 'action' => 'cargar_forma_pago/'.$mutual_producto_solicitud_id),
			'loading' => "$('spinner').show();$('grilla_forma_pagos').hide();",
			'complete' => "
	    			$('grilla_forma_pagos').show();
	    			$('spinner').hide();
	    			var totalPago = new Number($('MutualProductoSolicitudPagoTotalPagos').getValue());
	    			totalPago = totalPago.toFixed(2);
	    			var totalPercibe = new Number($('MutualProductoSolicitudPagoImportePercibido').getValue());
	    			totalPercibe = totalPercibe.toFixed(2);
	    			//if(totalPago >= totalPercibe) $('btn_cargar_forma_pago').disable();
	    			//else  $('btn_cargar_forma_pago').enable();
					"
	    )
	));
	?>	
	<div class="areaDatoForm">
		<h4>LIQUIDACION DE LA SOLICITUD</h4>
		<table class="tbl_form">
			<tr>
				<td>FORMA DE PAGO</td>
				<td>
				<?php //   echo $frm->input('MutualProductoSolicitudPago.forma_pago',array('type'=>'select','options'=>array('MUTUFPAG0001' => 'EFECTIVO', 'MUTUFPAG0002' => 'CHEQUE'),'empty'=>false,'selected' => '','label'=>""));?>				
                <?php echo $this->renderElement('global_datos/combo_global',array(
                                                                                'plugin'=>'config',
                                                                                'label' => " ",
                                                                                'model' => 'MutualProductoSolicitudPago.forma_pago',
                                                                                'prefijo' => 'MUTUFPAG',
                                                                                'disabled' => false,
                                                                                'empty' => false,
                                                                                'metodo' => "get_fpago_solicitud",
                ))?>                    
				</td>
			</tr>
			<tr>
				<td>BANCO</td>
				<td>
				<?php echo $this->renderElement('banco/combo',array(
																	'plugin'=>'config',
																	'label' => " ",
																	'model' => 'MutualProductoSolicitudPago.banco_id',
																	'disable' => false,
																	'empty' => true,
																	'tipo' => 0
				))?>				
				</td>
			</tr>
			<tr>
				<td>NRO. COMPROBANTE</td><td><?php echo $frm->input('MutualProductoSolicitudPago.nro_comprobante',array('size'=>50,'maxlenght'=>50)); ?></td>
			</tr>
			<tr>
				<td>IMPORTE</td><td><?php echo $frm->money('MutualProductoSolicitudPago.importe',null,$solicitud['MutualProductoSolicitud']['importe_percibido'])?></td>
			</tr>
			<tr>
				<td>OBSERVACIONES</td>
				<td><?php echo $frm->textarea('MutualProductoSolicitudPago.observaciones',array('cols' => 60, 'rows' => 3))?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $frm->submit("CARGAR FORMA DE PAGO",array('id' => 'btn_cargar_forma_pago'))?></td>
			</tr>			
			<tr><td colspan="2"><?php echo $controles->ajaxLoader('spinner','CARGANDO FORMA DE PAGO....')?></td></tr>
							
		</table>
	</div>
	<?php echo $frm->hidden('MutualProductoSolicitudPago.mutual_producto_solicitud_id',array('value' => $mutual_producto_solicitud_id))?>
	<?php echo $frm->hidden('MutualProductoSolicitudPago.importe_percibido',array('value' => $solicitud['MutualProductoSolicitud']['importe_percibido']))?>
	<?php echo $frm->hidden('aprobar',array('value' => 0))?>
	</form>
	<?php 
	/***************************************************************************************************************************
	 * GRILLA AJAX CON DATOS DE O LOS PAGOS
	 ***************************************************************************************************************************/
	?>	
	<div id="grilla_forma_pagos"><?php echo $this->requestAction('/mutual/mutual_producto_solicitudes/cargar_forma_pago/'.$mutual_producto_solicitud_id)?></div>
	<hr/>
	<?php 
	/***************************************************************************************************************************
	 * APRUEBO LA ORDEN
	 ***************************************************************************************************************************/
	?>
	<script type="text/javascript">
	Event.observe(window, 'load', function(){
		$('btn_submit').enable();
		<?php if($solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] == 1):?>
			$('btn_submit').disable();
		<?php endif;?>
                var mesIni = "<?php echo (isset($solicitud['MutualProductoSolicitud']['periodo_ini']) && !empty($solicitud['MutualProductoSolicitud']['periodo_ini']) ? substr($solicitud['MutualProductoSolicitud']['periodo_ini'],-2) : date("m"))?>";    
                var anioIni = "<?php echo (isset($solicitud['MutualProductoSolicitud']['periodo_ini']) && !empty($solicitud['MutualProductoSolicitud']['periodo_ini']) ? substr($solicitud['MutualProductoSolicitud']['periodo_ini'],0,4) : date("Y"))?>";            
                document.getElementById("MutualProductoSolicitudPeriodoIniMonth").value = mesIni;
                document.getElementById("MutualProductoSolicitudPeriodoIniYear").value = anioIni;
                $('MutualProductoSolicitudPeriodoIniMonth').disable();
                $('MutualProductoSolicitudPeriodoIniYear').disable();
                $('modifica_periodo_1').hide();
                $('modifica_periodo_2').hide();

                document.getElementById("MutualProductoSolicitudModificaPeriodo").checked = false;

                $('MutualProductoSolicitudModificaPeriodo').observe('click', function(event) {
                    if($('MutualProductoSolicitudModificaPeriodo').checked){
                        $('MutualProductoSolicitudPeriodoIniMonth').enable();
                        $('MutualProductoSolicitudPeriodoIniYear').enable();
                        $('modifica_periodo_1').show();
                        $('modifica_periodo_2').show();

                    }else{
                        $('MutualProductoSolicitudPeriodoIniMonth').disable();
                        $('MutualProductoSolicitudPeriodoIniYear').disable();
                        $('modifica_periodo_1').hide();
                        $('modifica_periodo_2').hide();

                    }
                });                    
	});
	function validateConfirm(){
            
            var msg = "APROBAR LA SOLICITUD DE CREDITO # <?php echo $mutual_producto_solicitud_id?>";
            msg = msg + "\n";
            msg = msg + "ATENCION!\n";
            
            var periodoIniOri= $('MutualProductoSolicitudPeriodoIniOri').getValue();
            
            if($('MutualProductoSolicitudModificaPeriodo').checked){
                
                mesIni = document.getElementById("MutualProductoSolicitudPeriodoIniMonth").value;
                anioIni = document.getElementById("MutualProductoSolicitudPeriodoIniYear").value;
                var periodoSel = anioIni + mesIni;
                
                msg = msg + "PERIODO INICIO" + (periodoIniOri !== periodoSel ? " MODIFICADO" : "") + ": ** " + mesIni + "/" + anioIni + ' **';
                
            }else{
                
                msg = msg + "PERIODO INICIO: <?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?>";
            }            
//            msg = msg + "PERIODO INICIO: <?php // echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?>";

            <?php if($solicitud['MutualProductoSolicitud']['beneficio_acuerdo_debito'] != 0):?>
            msg = msg + "\n";
            msg = msg + "\n";
            msg = msg + "ATENCION!!!";
            msg = msg + "\n";
            msg = msg + "El Beneficio posee *** ACUERDO DE PAGO ***";
            msg = msg + "\n";
            msg = msg + "\n";
            msg = msg + "Desea aprobar lo mismo esta solicitud?";
            <?php endif;?>

//        orgOrg = $('MutualProductoSolicitudOrganismoReasignado').getValue();
//        alert(orgOrg);        
//        return false;        
//        if(orgOrg !== '<?php // echo $solicitud['MutualProductoSolicitud']['organismo']?>'){
//            msg = msg + "\n";
//            msg = msg + "\n";
//            msg = msg + "ATENCION!! Se modifica el Beneficio";
//            msg = msg + "\n";
//            msg = msg + "Nuevo Organismo: " + getTextoSelect('MutualProductoSolicitudOrganismoReasignado');
//        }    
        
		return confirm(msg);
	}
	</script>
    
   
    
	<div class=notices_error>
		<strong>ATENCION!</strong>
		<br/>
		INICIA EN: <strong><?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?></strong>
		&nbsp;|&nbsp;1er. VTO CLIENTE: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['primer_vto_socio'])?></strong>
	</div>
	<?php if($solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] == 1):?>
		<div class=notices_error>
		<strong>**** NO SE PUEDE APROBAR ESTA SOLICITUD PORQUE EL PERIODO DE INICIO CORRESPONDE A UNA LIQUIDACION CERRADA ( <?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?> )***</strong>
		<br/><br/>
		Deber&aacute; abrir la liquidaci&oacute;n de <?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?> (siempre y cuando no este imputada)
		para aprobar la presente solicitud.
		<br/><br/>
		<strong>RECUERDE: </strong> Una vez aprobada la presente, deber&aacute; efectuar la reliquidaci&oacute;n del cliente a los efectos de que
		esta solicitud (#<?php echo $mutual_producto_solicitud_id?>) sea inclu&iacute;da en la liquidaci&oacute;n del periodo abierto.
		</div>
	<?php endif;?>		
	<?php echo $frm->create(null,array('action' => 'creditos_pendientes_aprobar','onsubmit' => "return validateConfirm();",'id' => 'creditosPendientesAprobar'))?>
    <!--
    <div class="areaDatoForm">
        <h3>ENTE / ORGANISMO DE RECAUDACION</h3>
        <table class="tbl_form">
            <tr>
                <td>ORGANISMO</td>
                <td>
				<?php echo $this->renderElement('global_datos/combo_global',array(
                                        'plugin'=>'config',
                                        'metodo' => "get_organismos_activos",
                                        'model' => 'MutualProductoSolicitud.organismo_reasignado',
                                        'empty' => false,
                                        'label' => null,
                                        'selected' => (isset($solicitud['MutualProductoSolicitud']['organismo']) ? $solicitud['MutualProductoSolicitud']['organismo'] : "")
				))?>                    
                </td>
            </tr>
        </table>
    </div>
-->    

    <table class="tbl_form">
        <tr>
            <td>NRO.REF.PROV.</td>
            <td colspan="2"><?php echo $frm->input('MutualProductoSolicitud.nro_referencia_proveedor',array('size'=>15,'maxlenght'=>15)); ?></td>
        </tr>
        <tr>
            <td>MODIFICAR PERIODO DE INICIO</td><td><input type="checkbox" name="data[MutualProductoSolicitud][modifica_periodo]" id="MutualProductoSolicitudModificaPeriodo"/></td>
            <td id='modifica_periodo_1'>PERIODO DE INICIO:</td>
            <td id='modifica_periodo_2'>
                <?php 
                    $inicia = substr($solicitud['MutualProductoSolicitud']['periodo_ini'], 0,4)."-".substr($solicitud['MutualProductoSolicitud']['periodo_ini'],-2)."-01";
                    echo $frm->periodo('MutualProductoSolicitud.periodo_ini','',$inicia,date('Y') - 1,date('Y') + 1);
                ?>
            </td>
        </tr>
    </table>

		<?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $mutual_producto_solicitud_id))?>
		<?php echo $frm->hidden('MutualProductoSolicitud.aprobar',array('value' => 1))?>
                <?php echo $frm->hidden('MutualProductoSolicitud.periodo_ini_ori',array('value' => $solicitud['MutualProductoSolicitud']['periodo_ini']))?>
        <?php echo $frm->hidden('MutualProductoSolicitud.organismo',array('value' => $solicitud['MutualProductoSolicitud']['organismo']))?>
	
        <?php if($solicitud['MutualProductoSolicitud']['beneficio_acuerdo_debito'] != 0):?>
            <div class="notices_error2" style="width: 100%;">
                <p><strong>ATENCION!!</strong></p>
                <p>El beneficio de la presente solicitud posee un <strong>ACUERDO DE DEBITO POR *** <?php echo $solicitud['MutualProductoSolicitud']['beneficio_acuerdo_debito']?> ***</strong></p>

            </div>
        <?php endif;?>
        <?php if($solicitud['MutualProductoSolicitud']['beneficio_activo'] == 0):?>
            <div class="notices_error2" style="width: 100%;">
                <p><strong>ATENCION!!</strong></p>
                <p>El beneficio de la presente solicitud <strong>NO ESTA VIGENTE</strong></p>
            </div>
                <table class="tbl_form">
			<tr>
				<td>ASIGNAR AL BENEFICIO</td>
				<td><?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','persona_id' => $solicitud['MutualProductoSolicitud']['persona_id'],'soloActivos' => 1))?></td>
			</tr>                     
                </table>    
        <?php endif;?> 


        <?php if(!empty($solicitud['MutualProductoSolicitud']['detalle_calculo_plan'])):?>
            <div class="notices" style="width: 100%;">
            <p><strong>ATENCION!</strong></p>
            <p>Al aprobar se generan junto con la primer cuota y se cobran por caja automaticamente los siguientes conceptos:</p>
            <?php 
                $objetoCalculado = json_decode($solicitud['MutualProductoSolicitud']['detalle_calculo_plan']);
                if(!empty($objetoCalculado->liquidacion->gastoAdminstrativo->importe) 
					&& !empty($objetoCalculado->liquidacion->gastoAdminstrativo->tipoCuota)){
                        echo "<p> * ".$objetoCalculado->liquidacion->gastoAdminstrativo->descripcion . " : " . number_format($objetoCalculado->liquidacion->gastoAdminstrativo->importe,2) . "</p>";
                } 
                if(!empty($objetoCalculado->liquidacion->sellado->importe) 
					&& !empty($objetoCalculado->liquidacion->sellado->tipoCuota)){
                        echo "<p> * ".$objetoCalculado->liquidacion->sellado->descripcion . " : " . number_format($objetoCalculado->liquidacion->sellado->importe,2) . "</p>";
                }                              
            ?>
            </div>
        <?php endif;?>    

        <?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'APROBAR SOLICITUD DE CREDITO #' . $mutual_producto_solicitud_id,'URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/pendientes_aprobar_opago" : $fwrd) ))?>
	
	<?php // debug($solicitud)?>