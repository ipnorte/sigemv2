<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>SOLICITUD #<?php echo $nro_solicitud?></h3>
<style>
    .field{
        width: auto;float: left;margin:5px 10px 5px 0px;
    }
</style>
<div class="areaDatoForm">
    <div class="field">NUMERO: <strong><?php echo $solicitud['MutualProductoSolicitud']['id']?></strong></div>
    <div class="field">ESTADO: <strong><?php echo $solicitud['MutualProductoSolicitud']['estado_desc']?></strong></div>
    <div class="field">FECHA EMISION: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?></strong></div>
    <div class="field">EMITIDA POR: <strong><?php echo $solicitud['MutualProductoSolicitud']['user_created']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">PRODUCTO: <strong><?php echo $solicitud['MutualProductoSolicitud']['producto']?></strong></div>
    <div class="field">PLAN: <strong><?php echo $solicitud['MutualProductoSolicitud']['proveedor_plan']?></strong></div>
    <div class="field">CAPITAL: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_solicitado'])?></strong></div>
    <div class="field">LIQUIDO: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_percibido'])?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">CUOTAS: <strong><?php echo $solicitud['MutualProductoSolicitud']['cuotas']?></strong></div>
    <div class="field">IMPORTE CUOTA: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_cuota'])?></strong></div>
    <div class="field">IMPORTE TOTAL: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_total'])?></strong></div>
    <div style="clear: both;"></div>
    <?php if(!empty($solicitud['MutualProductoSolicitud']['vendedor_id'])):?>
    <div class="areaDatoForm2">
        <h3>DATOS DEL VENDEDOR</h3>
        <hr>
        <div class="field">VENDEDOR: <strong><?php echo $solicitud['MutualProductoSolicitud']['vendedor_nombre']?></strong></div>
        <?php if(!empty($solicitud['MutualProductoSolicitud']['vendedor_remito'])):?>
        <div class="field">CONSTANCIA DE ENTREGA: <strong><?php echo $solicitud['MutualProductoSolicitud']['vendedor_remito']?></strong></div>
        <div class="field"><?php echo $controles->botonGenerico('/ventas/vendedores/imprimir_remito/'.$solicitud['MutualProductoSolicitud']['vendedor_remito_nro'],'controles/pdf.png',null,array('target' => 'blank'))?></div>
        <?php endif;?>
        <div style="clear: both;"></div>
    </div>
    <?php endif;?>
    <?php //   if(!empty($solicitud['MutualProductoSolicitudPago'])):?>
    <!--<h4>FORMA DE LIQUIDACION</h4>-->
    <!--<table>-->
        <?php //   foreach ($solicitud['MutualProductoSolicitud']['MutualProductoSolicitudPago'] as $pago):?>
        <!--<tr>-->
<!--            <td><?php //   echo $pago['forma_pago_desc']?></td>
            <td style="text-align: right"><?php //   echo $util->nf($pago['importe'])?></td>-->
<!--        </tr>
        <?php //   endforeach;?>
    </table>-->
    <?php //   endif;?>
    <div style="clear: both;margin-bottom: 10px;"></div>
    
    <h3>DATOS DEL SOLICITANTE</h3>
    <hr>
    <!--<div class="field">CUIT/CUIL: <strong><?php //   echo $solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil']?></strong></div>-->
    <div class="field">TIPO Y NRO DE DOCUMENTO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc']?></strong></div>
    <div class="field">NOMBRE: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_apenom']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">DOMICILIO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_domicilio']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">DATOS COMPLEMENTARIOS: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_complementarios']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">MEDIOS DE CONTACTO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_medio_contacto']?></strong></div>
    <div style="clear: both;margin-bottom: 10px;"></div>
    <div class="areaDatoForm2">
        <h3>MEDIO DE PAGO</h3>
        <hr>
        <div class="field">ORGANISMO: <strong><?php echo $solicitud['MutualProductoSolicitud']['organismo_desc']?></strong></div>
        <div class="field">EMPRESA/ENTIDAD: <strong><?php echo $solicitud['MutualProductoSolicitud']['turno_desc']?></strong></div>
        <div style="clear: both;"></div>
        <div class="field">BANCO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_banco']?></strong></div>
        <div class="field">SUCURSAL: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_sucursal']?></strong></div>
        <div class="field">CUENTA: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_cuenta']?></strong></div>
        <div class="field">CBU: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_cbu']?></strong></div>
        <?php if(isset($solicitud['MutualProductoSolicitud']['sueldo_neto']) && !empty($solicitud['MutualProductoSolicitud']['sueldo_neto'])):?>
        <div style="clear: both;"></div>
        <div class="field">SUELDO NETO: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['sueldo_neto'])?></strong></div>
        <div class="field">DEBITOS BANCARIOS: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['debitos_bancarios'])?></strong></div>
        
        <?php endif;?>
        <div style="clear: both;margin-bottom: 10px;"></div>        
    </div>
    
    <h3>INSTRUCCION DE PAGO</h3>
    <hr>
    <table>
        <tr>
            <th>A LA ORDEN DE</th><th>CONCEPTO</th><th>IMPORTE</th>
        </tr>
        <?php $TOTAL_IPAGO = 0?>
        <?php foreach($solicitud['MutualProductoSolicitudInstruccionPago'] as $ipago):?>
            <?php $TOTAL_IPAGO += $ipago['importe']?>
            <tr>
                <td><?php echo $ipago['a_la_orden_de']?></td>
                <td><?php echo $ipago['concepto']?></td>
                <td style="text-align: right;"><?php echo $util->nf($ipago['importe'])?></td>
            </tr>
        <?php endforeach;?>
        <tr class="totales">
            <td colspan="2">TOTAL INSTRUCCION DE PAGO</td>
            <td><?php echo $util->nf($TOTAL_IPAGO)?></td>
        </tr>
    </table>
    <div style="clear: both;margin-bottom: 10px;"></div>
   
    <h3>HISTORIAL DE ESTADOS</h3>
    <hr>
    <table>
        <tr>
            <th>FECHA</th><th>USUARIO</th><th>ESTADO</th><th>OBSERVACIONES</th>
        </tr>
        <?php foreach($solicitud['MutualProductoSolicitud']['MutualProductoSolicitudEstado'] as $estado):?>
        <tr>
            <td><?php echo $estado['created']?></td>
            <td><?php echo $estado['user_created']?></td>
            <td><?php echo $estado['estado_desc']?></td>
            <td><?php echo $estado['observaciones']?></td>
        </tr>
        <?php endforeach;?>
    </table>
    <div style="clear: both;margin-bottom: 10px;"></div>
	<?php if(!empty($solicitud['MutualProductoSolicitudDocumento'])):?>
		<div class=row>
			
			<ul style="list-style-type: square;border: 1px solid #858265;padding: 5px;margin: 5px 0px 5px 0px;">
			<h4>DOCUMENTACION ADJUNTA</h4>
			<div class="actions">
				<?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/download_attach_zipped/'.$solicitud['MutualProductoSolicitud']['id'],'controles/disk_multiple.png','DESCARGAR Y EMPAQUETAR TODOS LOS ADJUNTOS',array('target' => '_blank'))?>
			</div>
				<?php foreach ($solicitud['MutualProductoSolicitudDocumento'] as $documento):?>
					<li style="margin-left: 20px;"><?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/download_attach/'.$documento['MutualProductoSolicitudDocumento']['id'],'controles/attach.png',$documento['GlobalDato']['concepto_1']." (".$documento['MutualProductoSolicitudDocumento']['file_name'].")")?></li>
				<?php endforeach;?>
			</ul>
		</div>		
	<?php endif;?>
        <?php if(count($solicitud['MutualProductoSolicitudDocumento']) < 6):?>
    
<div class="areaDatoForm">
    <script type="text/javascript">
        //mensaje_error_js
        function validate(){
            $("MutualProductoSolicitudArchivo").removeClassName(classFormError);
            $(contenedorMsgError).update('');
            $(contenedorMsgError).hide();            
            if($("MutualProductoSolicitudArchivo").getValue() == ""){
		$("mensaje_error_js").update('*** DEBE INDICAR EL ARCHIVO QUE SE ANEXARA A LA SOLICITUD ***');
		$("mensaje_error_js").show();
		$("MutualProductoSolicitudArchivo").addClassName(classFormError);
		$("MutualProductoSolicitudArchivo").focus();                
                return false;
            }
            return confirm("ANEXAR DOCUMENTO A LA SOLICITUD?");
            
        }
    
    </script>
    
    <?php echo $form->create(null,array('action'=>'adjuntar_documentacion/'.$solicitud['MutualProductoSolicitud']['id'],'type' => 'file','onsubmit' => 'return validate()'));?>
    <table class="tbl_form">
        <tr>
            <td>ADJUNTAR ARCHIVO</td>
            <td><?php echo $frm->file('MutualProductoSolicitud.archivo',array('size' => 50))?></td>
        </tr>
        <tr><td colspan="2" style="color: red;">Se prodr&aacute;n adjuntar hasta 10 archivos (jpg,jpeg,png,pdf) que no superen los 5 Mb cada uno.</td></tr>        
        <tr><td colspan="2"><?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ANEXAR ARCHIVO A LA SOLICITUD','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/by_persona/".$solicitud['MutualProductoSolicitud']['persona_id'] : $fwrd) ))?></td></tr>
    </table>
    <?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $solicitud['MutualProductoSolicitud']['id'])); ?>
    <?php $form->end()?>
</div>     
    
        <?php endif;?>
   
    <hr> 
</div>
<?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/'.$solicitud['MutualProductoSolicitud']['id'],'controles/pdf.png','IMPRIMIR SOLICITUD',array('target' => 'blank'))?>
<?php // debug($solicitud)?>