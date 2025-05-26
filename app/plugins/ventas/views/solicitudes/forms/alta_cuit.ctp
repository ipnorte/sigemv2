<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>NUEVA SOLICITUD</h3>
<hr/>
<script type="text/javascript">
    function validateForm_P1(){
        if(!controlCUIT($('MutualProductoSolicitudCuitCuil').getValue(),'MutualProductoSolicitudCuitCuil','')) return false;
        return true;
    };    
        Event.observe(window, 'load', function() {
            $('MutualProductoSolicitudCuitCuil').focus();
	});        

</script>
<div class="areaDatoForm">    
    <?php echo $form->create(null,array('action' => 'alta_cuit','name'=>'formCUITPersona','id'=>'formCUITPersona','onsubmit' => "return validateForm_P1()" ));?>

    <h3>CUIT/CUIL</h3>
    <table class="tbl_form">
        <tr>
                <td>Ingrese el Nro. de CUIT del Solicitante</td>	
                <td><?php echo $frm->number('MutualProductoSolicitud.cuit_cuil',array('label'=>'','size'=>12,'maxlength'=>11,'value' => (isset($solicitud['Persona']['cuit_cuil']) ? $solicitud['Persona']['cuit_cuil'] : ""))); ?></td>
                <td><input type="submit" value="SIGUIENTE" id="btnProcessCUIT"/></td>
        </tr>	        
    </table>

<?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
<?php echo $frm->end()?>
</div>
