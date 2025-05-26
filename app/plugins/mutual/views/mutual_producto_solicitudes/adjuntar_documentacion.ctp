<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>
<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>
<h3>ADJUNTAR DOCUMENTACION :: SOLICITUD DE CREDITO</h3>
<?php // echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $persona,'plugin'=>'mutual'))?>
<?php echo $this->renderElement('mutual_producto_solicitudes/ficha_solicitud_minima',array('solicitud'=>$solicitud,'plugin' => 'mutual'));?>
<?php if(!empty($solicitud['MutualProductoSolicitudDocumento'])):?>
<div class="areaDatoForm2">
    <table>
        <tr>
            <th></th>
            <th></th><th>DOCUMENTACION ADJUNTA</th><th>TYPE</th><th></th>
        </tr>
        
        <?php $array_tmp = array();?>
        <?php foreach ($solicitud['MutualProductoSolicitudDocumento'] as $documento):?>
        <?php // debug($documento);?>
        <tr>
            <td><?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/borrar_documentacion_adjunta/'.$documento['MutualProductoSolicitudDocumento']['id'],'controles/user-trash-full.png',NULL,NULL,'Eliminar el Documento?')?></td>
            <td><?php 
                    echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/download_attach/'.$documento['MutualProductoSolicitudDocumento']['id'],'controles/disk.png');
                    array_push($array_tmp, $documento['MutualProductoSolicitudDocumento']['codigo_documento']);
                ?>
            </td>
            <td><?php echo $documento['GlobalDato']['concepto_1'] . " (" . $documento['MutualProductoSolicitudDocumento']['file_name'] . ")"?></td>
            <td><?php echo $documento['MutualProductoSolicitudDocumento']['file_type']?></td>
            <td></td>
        </tr>
                <!--<li style="margin-left: 20px;"><?php // echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/download_attach/'.$documento['MutualProductoSolicitudDocumento']['id'],'controles/attach.png',$documento['MutualProductoSolicitudDocumento']['file_name'])?></li>-->
        <?php endforeach;?>
        <tr>
            <th colspan="4" style="font-weight: bold;"><?php echo $controles->botonGenerico('download_attach_zipped/'.$solicitud['MutualProductoSolicitud']['id'],'controles/disk_multiple.png','DESCARGAR TODOS LOS ADJUNTOS',array('target' => '_blank'))?></th>
        </tr>
    </table>		
</div>
<?php endif;?>
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
    
    <?php echo $form->create(null,array('action'=>'adjuntar_documentacion/'.$solicitud['MutualProductoSolicitud']['id'].'/1','type' => 'file','onsubmit' => 'return validate()'));?>
    <table class="tbl_form">
        <tr>
            <td>ADJUNTAR DOCUMENTACIÓN</td>
            <!--td><?php //echo $frm->file('MutualProductoSolicitud.archivo',array('size' => 50))?></td-->
        </tr>
        <tr>
            <td>
                <div class="row mb-1 ">
                        <?php if ($solicitud['MutualProductoSolicitud']['anulada'] == 0): ?>
                        <div class="col-6">
                            <?php echo $form->create(null, array('action' => 'adjuntar_documentacion/' . $solicitud['MutualProductoSolicitud']['id'] . '/1', 'type' => 'file')); ?>
                                <span>
                                    (Tiene disponible para adjuntar)
                                    <span class="badge badge-success badge-pill">
                                        <?php
                                        echo (count($datos) - count($solicitud['MutualProductoSolicitudDocumento']))
                                        ?>
                                    </span> documentos.
                                </span>
                                <div class="form-group row border">
                                    <table class="tbl_form"> 
                                        <?php

                                        foreach ($datos as $valor) {
                                            if (!in_array($valor["GlobalDato"]["id"], $array_tmp)) {
                                                echo '<tr>';
                                                echo '<td style="vertical-align:middle;">';
                                                echo $valor["GlobalDato"]["concepto_1"];
                                                echo '</td>';
                                                echo '<td><input type="file" name="data[ProveedorPlanDocumento][' . $valor["GlobalDato"]["id"] . '|' . $valor["GlobalDato"]["concepto_1"] . ']" id=ProveedorPlanDocumento' . $valor["GlobalDato"]["concepto_1"] . '"/></td>';
                                                echo '</tr>';
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                            <?php echo $frm->hidden('MutualProductoSolicitud.id', array('value' => $solicitud['MutualProductoSolicitud']['id'])); ?>
                            <?php $form->end() ?>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr><!--td colspan="2" style="color: red;">Se prodr&aacute;n adjuntar hasta 10 archivos (jpg,jpeg,png,pdf) que no superen los 5 Mb cada uno.</td--></tr>        
        <tr><td colspan="2">
            <?php if( (count($datos) - count($solicitud['MutualProductoSolicitudDocumento'])) > 0):?>
                <?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ANEXAR ARCHIVO A LA SOLICITUD - (2Mb Max)','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/by_persona/".$solicitud['MutualProductoSolicitud']['persona_id'] : $fwrd) ))?></td></tr>
            <?php endif; ?>
    </table>
                
 
    <?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $solicitud['MutualProductoSolicitud']['id'])); ?>
    <?php $form->end()?>
</div>


