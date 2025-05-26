<!-- Modal -->
<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_CBU = (isset($INI_FILE['general']['nosis_validar_cbu']) && $INI_FILE['general']['nosis_validar_cbu'] == 1 ? TRUE : FALSE);        
#MODULO DE TARJETAS DE DEBITO
$MOD_TARJETAS = (isset($INI_FILE['general']['tarjetas_de_debito']) && $INI_FILE['general']['tarjetas_de_debito'] == 1 ? TRUE : FALSE);

?>

<?php 
echo $html->css('creditCard');
echo $javascript->link('aplicacion/creditCard');
?>

<script>

function validateFormAddTarjetaDebito(){
	ret = true;
    var mes = document.getElementById('TarjetaDebitoCardExpirationMonth1').value;
    var anio = document.getElementById('TarjetaDebitoCardExpirationYear1').value;
    ret = controlVigenciaTarjetaDebito(mes,anio,3);
    ret = validateNumberJQUERY(document.getElementById('TarjetaDebitoCardNumber1'));
    if(!ret){return false;}    
    return confirm('Actualizar Tarjeta de Débito');  
}

</script>
<div class="modal fade" id="modificaTarjetaModal" tabindex="-1" role="dialog" aria-labelledby="modificaTarjetaModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modificaTarjetaModalLabel">Carga / Actualización de Tarjeta de Debito</h5>
                <button type="button" id="btnModalClose" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            
            <?php echo $form->create(null,array('action' => 'tarjeta_edit/'.$TOKEN_ID,'name'=>'formAddTarjetaDebito','id'=>'formAddTarjetaDebito','onsubmit' => "return validateFormAddTarjetaDebito()"));?>
            
				<div class="form-row">
                    <div class="form-group col-md-12">
                        <?php $beneficios = $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$solicitud['Persona']['id'].'/1');?>                
                        <label for="MutualProductoSolicitudPersonaBeneficioId">Beneficio / Medio de Pago</label>
                        <select class="form-control" id="MutualProductoSolicitudPersonaBeneficioId" name="data[PersonaBeneficio][id]">
                            <?php if(!empty($beneficios)):?>
                            <?php foreach($beneficios as $beneficio):?>
                            <option value="<?php echo $beneficio['PersonaBeneficio']['id']?>"><?php echo $beneficio['PersonaBeneficio']['string']?></option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>              
                    </div>            
            	</div>

            
            	<div class="card mb-1">
                	<div class="card-header bg-info text-white"><i class="fas fa-credit-card"></i>&nbsp;Tarjeta de Débito</div>
                	<div class="card-body">
                	

				<div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="TarjetaDebitoCardHolderName">Titular</label>
                                <input class="form-control" id="TarjetaDebitoCardHolderName" name="data[TarjetaDebito][card_holder_name]" required="" maxlength="30" value="" type="text" >
                            </div>
                            <div class="form-group col-md-3">
                                <label for="TarjetaDebitoCardNumber">Número</label>
                                <input class="form-control" id="TarjetaDebitoCardNumber1" maxlength="19" name="data[TarjetaDebito][card_number]" onkeypress='return event.charCode >= 48 && event.charCode <= 57'  required=""  value="" type="text"  onblur="validateNumberJQUERY(this)">
                            </div> 
                            <div class="form-group col-md-1">
                            <label for="card_icon">&nbsp;</label>
                                <div id="card_icon"></div>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="TarjetaDebitoCardExpirationMonth1">Mes</label>
                                <select class="form-control" id="TarjetaDebitoCardExpirationMonth1" name="data[TarjetaDebito][card_expiration_month]">
                                <?php 
                                    for($i=1;$i<=12;$i++){
                                        $value = str_pad($i,2,'0',STR_PAD_LEFT);
                                        echo "<option value=\"$value\" ".( $value == date('m') ? " selected " : "").">$value</option>";
                                    }                                    
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="TarjetaDebitoCardExpirationYear1">Año</label>
                                <select class="form-control" id="TarjetaDebitoCardExpirationYear1" name="data[TarjetaDebito][card_expiration_year]">
                                    <?php 
                                    for($i=intval(date('y'));$i<= (intval(date('y')) + 40);$i++){
                                        $value = str_pad($i,2,'0',STR_PAD_LEFT);
                                        echo "<option value=\"$value\" ".( $value == date('y') ? " selected " : "").">$value</option>";
                                    }                                    
                                    ?>
                                </select>
                            </div>                            
                            

                            <div class="form-group col-md-2">
                                <label for="TarjetaDebitoSecurityCode">Cod.Seg.</label>
                                <input class="form-control" id="TarjetaDebitoSecurityCode" maxlength="3" name="data[TarjetaDebito][security_code]" required="" placeholder="###"  value="" type="password" onkeypress='return event.charCode >= 48 && event.charCode <= 57' >
                            </div>                                                          
                    </div>                	
                	
                	</div>
            	</div>
            
            
                	
                            	
            
            
            
            
            
            </div>
            <div class="modal-footer">
            	<?php if($MOD_TARJETAS):?>
                <button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <?php endif;?>
            </div>
            <?php echo $form->end();?>            
		</div>
	</div>
</div>