<?php 
$INI_FILE = (isset($_SESSION['MUTUAL_INI']) ? $_SESSION['MUTUAL_INI'] : NULL);
$MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);

?>

<div class="card mb-1">
    <div class="card-header"><i class="fas fa-business-time"></i>&nbsp;Servicio de Consulta SIISA</div>
    <div class="card-body">
        <?php echo $form->create(null,array('action' => 'consultar_siisa'));?>
        <div class="form-row">
            <div class="form-group col-md-1">
            	<label for="MutualProductoSolicitudDocumento">Documento *</label>
                <input class="form-control solo-numero" id="MutualProductoSolicitudDocumento" required="" name="data[Persona][documento]" type="text" maxlength="8" minlength="8" autofocus="" placeholder="Nro Documento"  required="" value="<?php echo (isset($this->data['Persona']['documento']) ? $this->data['Persona']['documento'] : '')?>">                
            </div>
            <div class="form-group col-md-3">
            <label for="PersonaNombre">Nombre y Apellido *</label>
                <input class="form-control" id="PersonaNombre" name="data[Persona][nombre]" type="text" placeholder="Nombre y Apellido"  required="" value="<?php echo (isset($this->data['Persona']['nombre']) ? $this->data['Persona']['nombre'] : '')?>">                
            </div>
            <div class="form-group col-md-1">
            <label for="PersonaBeneficioSueldoNeto">Sueldo Neto *</label>
                <input class="form-control" id="PersonaBeneficioSueldoNeto" step="0.01" name="data[Persona][sueldo_neto]" required=""  value="<?php echo (isset($this->data['Persona']['sueldo_neto']) ? $this->data['Persona']['sueldo_neto'] : '')?>" type="number" placeholder="0.00" >
            </div>
            <div class="form-group col-md-1">
            	<label for="PersonaBeneficioDebitosBancarios">Débitos Bancarios *</label>
                <input class="form-control" id="PersonaBeneficioDebitosBancarios" step="0.01" name="data[Persona][debitos_bancarios]" value="<?php echo (isset($this->data['Persona']['debitos_bancarios']) ? $this->data['Persona']['debitos_bancarios'] : '')?>" type="number" placeholder="0.00" >
            </div>
            <div class="form-group col-md-1">
            	<label for="PersonaBeneficiCuotaCredito">Cuota Credito *</label>
                <input class="form-control" id="PersonaBeneficioCuotaCredito" step="0.01" name="data[Persona][cuota_credito]" value="<?php echo (isset($this->data['Persona']['cuota_credito']) ? $this->data['Persona']['cuota_credito'] : '')?>" type="number" placeholder="0.00" >
            </div>
            <div class="form-group col-md-2">
                <label for="PersonaSiisaProducto">Producto</label>
                <select class="form-control" name="data[Persona][producto_siisa]" id="PersonaSiisaProducto">
                <?php foreach($productos_siisa as $siisa): ?>
                    <option value="<?php echo $siisa['GlobalDato']['concepto_4']?>" class="input text"><?php echo $siisa['GlobalDato']['concepto_4']?></option>
                <?php endforeach;?>
                </select>
                
            </div>
            <?php if($MOD_SIISA):?>            
            <div class="form-group col-md-1">
            	<label for="btnSubmitConsultaSiisa">&nbsp;</label>
                <button type="submit" name="btn_submit" id="btnSubmitConsultaSiisa" class="form-control btn btn-primary btn-small"><i class="fas fa-business-time"></i>&nbsp;Consultar</button>
            </div>
            <?php endif;?>            
        </div>
        <?php echo $form->end();?>
    </div>
</div>
<?php if($respuestas):?>
    <div class="card mb-1">
    	<div class="card-header bg-success text-white"><h5><?php echo $this->data['Persona']['documento']?> - <?php echo $this->data['Persona']['nombre']?></h5></div>
        <div class="card-body">
        <div class="col-12 mb-3">
        Sueldo Neto: <strong><?php echo $util->nf($this->data['Persona']['sueldo_neto'])?></strong>
        &nbsp;
        Debitos Bancarios: <strong><?php echo $util->nf($this->data['Persona']['debitos_bancarios'])?></strong>
        &nbsp;
        Cuota Credito: <strong><?php echo $util->nf($this->data['Persona']['cuota_credito'])?></strong>        
        </div>
        <table class="table table-hover">
        <thead>
        	<tr>
        		<th>#</th>
        		<th>Producto SIISA</th>
        		<th>Política</th>
        		<th>Monto Máximo</th>
        		<th>Mínimo Dispo</th>
        		<th></th>        		
        		<th>Respuesta</th>
        	</tr>        	
        </thead>
        <tbody>
        <?php foreach ($respuestas as $respuesta):?>
        
		<tr>
			<td><?php echo $respuesta['respuesta']->currentExecId?></td>
			<td><?php echo $respuesta['producto_siisa']?></td>
			<td><?php echo $respuesta['respuesta']->executedPolicy?></td>			
			<td><?php echo $respuesta['respuesta']->monto_max?></td>
			<td><?php echo $respuesta['respuesta']->minimoDisponible?></td>
			<td class="<?php echo ($respuesta['respuesta']->aprueba ? 'text-success': 'text-danger')?>"><?php echo ($respuesta['respuesta']->aprueba ? '<i class="fas fa-thumbs-up" id="siisa_aprueba"></i>': '<i class="fas fa-thumbs-down" id="siisa_rechaza">')?></td>
			<td class="<?php echo ($respuesta['respuesta']->aprueba ? 'text-success': 'text-danger')?>">
			
			
			
			<?php // echo $respuesta['respuesta']->decisionResult?>
			
			<?php 
			if(!$respuesta['respuesta']->onError) {
			    echo $respuesta['respuesta']->decisionResult;
			}else {
			    echo "ERROR SERVICIO COD: <strong>" . $respuesta['respuesta']->oERROR->httpCode . "</strong> | MSG SIISA: " . $respuesta['respuesta']->oERROR->message;
			}
			     
			?>			
			
			</td>
		</tr>        
        <?php endforeach;?>
    	</tbody>
    	</table>
        </div>
    </div>
<?php endif;?>

