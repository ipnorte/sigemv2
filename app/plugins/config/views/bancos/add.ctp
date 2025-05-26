<?php echo $this->renderElement('head',array('title' => 'BANCOS :: NUEVO BANCO'))?>
<?php echo $frm->create('Banco');?>
<div class="areaDatoForm">
 	<div class='row'>
 		<?php echo $frm->input('codigo',array('label'=>'CODIGO BCRA','size'=>5,'maxlength'=>5)) ?>
 		<?php echo $frm->input('nombre',array('label'=>'DENOMINACION','size'=>60,'maxlenght'=>100)) ?>
 	</div> 		
	<div class="row">
		<?php echo $frm->input('Banco.activo',array('label' => 'ACTIVO','checked' => 'checked'))?>
		<?php echo $frm->input('Banco.beneficio',array('label' => 'BENEFICIO'))?>
		<?php echo $frm->input('Banco.fpago',array('label' => 'F.PAGO'))?>
	</div> 	
	<div class="row">
		<?php echo $frm->input('Banco.intercambio',array('label' => 'INTERCAMBIO INFORMACION'))?>
	</div> 	 		
	<div class="row">
		<?php echo $frm->input('tipo_registro',array('type' => 'select','options' => array(1 => 'ARCHIVO UNICO',3 => 'ARCHIVO CABECERA - DETALLE Y PIE'),'label'=>'TIPO DE REGISTRO')) ?>
		<?php echo $frm->number('longitud',array('label' => 'LONGITUD ENVIO','size'=>6,'maxlength'=>5))?>
		<?php echo $frm->number('longitud_salida',array('label' => 'LONGITUD RECEPCION','size'=>6,'maxlength'=>5))?>
	</div>
	<div class="row" id="seteo_id_registros">
		<?php echo $frm->input('indicador_cabecera',array('label' => 'ID CABECERA','size'=>40,'maxlength'=>50))?>
		<?php echo $frm->input('indicador_detalle',array('label' => 'ID DETALLE','size'=>40,'maxlength'=>50))?>
		<?php echo $frm->input('indicador_pie',array('label' => 'ID PIE','size'=>40,'maxlength'=>50))?>
	</div> 		
	<div class="row">
		<?php echo $frm->input('Banco.tipo_cta_sueldo',array('label' => 'CODIGO CUENTA SUELDO','size'=>3,'maxlength'=>2))?>
		<?php echo $frm->input('nro_cta_acredita_debito',array('label' => 'NRO CUENTA ACREDITACION DEBITOS','size'=>30,'maxlength'=>50))?>
	</div> 	
    <div class="row">
	<?php echo $frm->input('metodo_str_encode',array('type' => 'select','label' => 'METODO CODIFICACION','empty' => TRUE, 'selected' => $this->data['Banco']['metodo_str_encode'],'options' => $metodos))?>
    </div>
    <div class="row">
        <?php echo $frm->input('metodo_str_decode',array('label' => 'METODO DECODIFICACION','size'=>70))?>
    </div>      
    <div class="row">
        <label for="BancoParametrosIntercambio">PARAMETROS</label>
    </div>
    <div class="row">
        
        <textarea name="data[Banco][parametros_intercambio]" cols="65" rows="15" id="BancoParametrosIntercambio" style="background-color: #e4e0d8;"><?php echo $this->data['Banco']['parametros_intercambio']?></textarea>
    </div>    
	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/bancos'))?>
