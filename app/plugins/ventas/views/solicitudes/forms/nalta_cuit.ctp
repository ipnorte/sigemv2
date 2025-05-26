<div class="card mb-1">
    <div class="card-header"><i class="fas fa-handshake"></i>&nbsp;Nueva Solicitud</div>
    <div class="card-body">
        <?php echo $form->create(null,array('action' => 'alta_cuit','name'=>'formCUITPersona','id'=>'formCUITPersona','onsubmit' => "return validateForm_P1()" ));?>
        <div class="form-row">
            <div class="form-group col-md-2">
                <input class="form-control solo-numero" id="MutualProductoSolicitudCuitCuil" required="" name="data[MutualProductoSolicitud][cuit_cuil]" value="<?php echo (isset($this->data['MutualProductoSolicitud']['cuit_cuil']) ? $this->data['Persona']['documento'] : "")?>" type="text" maxlength="11" minlength="11" autofocus="" placeholder="CUIT/CUIL" >                
            </div>
            <div class="form-group col-md-2">
                <button type="submit" name="btn_submit" class="btn btn-primary btn-small"><i class="fas fa-arrow-circle-right"></i>&nbsp;Siguiente</button>
            </div>            
        </div>
        <?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
        <?php echo $frm->end()?>        
    </div>
</div>
<script type="text/javascript">
    function validateForm_P1(){
//        if(!controlCUIT($('MutualProductoSolicitudCuitCuil').getValue(),'MutualProductoSolicitudCuitCuil','')) return false;
//        return true;
    };    
//        Event.observe(window, 'load', function() {
//            $('MutualProductoSolicitudCuitCuil').focus();
//	});        

</script>
