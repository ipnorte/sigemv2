<div class="card mb-1">
    <div class="card-header"><i class="fas fa-network-wired"></i>&nbsp;Servicio de Consulta Intranet SIGEM</div>
    <div class="card-body">
        <?php echo $form->create(null,array('action' => 'consultar_intranet'));?>
        <div class="form-row">
            <div class="form-group col-md-2">
                <input class="form-control solo-numero" id="MutualProductoSolicitudDocumento" required="" name="data[Persona][documento]" value="<?php echo (isset($this->data['Persona']['documento']) ? $this->data['Persona']['documento'] : "")?>" type="text" maxlength="8" minlength="8" autofocus="" placeholder="Nro Documento" >                
            </div>
            <div class="form-group col-md-2">
                <button type="submit" name="btn_submit" class="btn btn-secondary btn-small"><i class="fas fa-network-wired"></i>&nbsp;Consultar</button>
            </div>            
        </div>
        <?php echo $form->end();?>
    </div>
</div>
<style>
    h1{
        font-size: 12px;
        font-weight: bold;
    }
    h3{
        font-size: 10px;
        font-weight: bold;
        margin-bottom: 1px;
    }
    .notices_ok{
        margin: 1px;
        color: green;
    }
</style>
<div class="card mb-1">
    <div class="card-body">
        <small>
            <?php echo $this->renderElement('personas/consulta_intranet_info',array('plugin' => 'pfyj','informe' => $informe,'cuit' => $cuitCuil))?>
        </small>
    </div>
</div>

