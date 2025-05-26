<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<?php // echo $this->requestAction('/pfyj/socios/view/'.$socio['Socio']['id'])?>
<h3>ALTA INFORME DE DEUDA</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
    
	<?php if($persona['Persona']['fallecida'] == 1):?>
		$('formAltaInfoSocio').disable();
		return;
	<?php endif;?>    
    
});    
    
</script>
<?php if(empty($deuda)) echo $form->create(null,array('name'=>'formAltaInfoSocio','id'=>'formAltaInfoSocio','action' => 'alta_informe/'. $socio['Socio']['id']));?>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>
					<?php echo $this->renderElement('global_datos/combo_global',array(
                                                        'plugin'=>'config',
                                                        'metodo' => 'get_empresas_info_deuda',
                                                        'label' => 'EMPRESA / CANAL',
                                                        'model' => 'SocioInforme.empresa',
                                                        'prefijo' => 'MUTUSOIN',
                                                        'disable' => false,
                                                        'empty' => false,
                                                        'logico' => true,
					))?>			
			</td>
                        <td><?php echo $frm->periodo('SocioInforme.periodo_corte','INFORMAR DEUDA A',(isset($periodo_corte) ? $periodo_corte :  null),date('Y')-2,date('Y'),false)?></td>
                </tr>
	</table>

</div>
<?php if(empty($deuda)) echo $frm->hidden('SocioInforme.socio_id',array('value' => $socio['Socio']['id'])); ?>
<?php if(empty($deuda)) echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'PREVISUALIZAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$socio['Socio']['persona_id'] : $fwrd) ))?>

<?php if(!empty($deuda)):?>

<script type="text/javascript">
//Event.observe(window, 'load', function(){
//    
//	<?php // if($persona['Persona']['fallecida'] == 1):?>
//		$('formAltaInfoSocio').disable();
//		return;
//	<?php // endif;?>    
//    
//});
function validateForm(){
    
    var msgConfirm = "ATENCION!\n\n";
        msgConfirm = msgConfirm + "INFORMAR AL SOCIO #<?php echo $socio['Socio']['id']?>\n";
        msgConfirm = msgConfirm + "<?php echo $util->globalDato($socio['Persona']['tipo_documento'])." ".$socio['Persona']['documento']." - ".$socio['Persona']['apellido'].", ".$socio['Persona']['nombre']?>";
        msgConfirm = msgConfirm + "\n\n";
        msgConfirm = msgConfirm + "EMPRESA: " + "<?php echo $util->globalDato($empresa)?>" + "\n";
        
//        impoDeuda = new Number(<?php // echo $deuda?>);
//        impoDeuda = impoDeuda.toFixed(2);
        msgConfirm = msgConfirm + "PERIODO DE CORTE: " + "<?php echo $util->periodo($periodo_corte)?>" + "\n";
        msgConfirm = msgConfirm + "DEUDA: " + "<?php echo $util->nf($deuda['total'])?>" + "\n";
        return confirm(msgConfirm);
    

}

</script>


<h3>Detalle de la deuda a Informar</h3>

<?php echo $form->create(null,array('name'=>'formAltaInfoSocio','id'=>'formAltaInfoSocio','onsubmit' => 'return validateForm()','action' => 'alta_informe/'. $socio['Socio']['id']));?>

<div class="areaDatoForm">
    <div class="row">EMPRESA/CANAL: <strong><?php echo $util->globalDato($empresa)?></strong> | DEUDA CALCULADA A: <strong><?php echo $util->periodo($periodo_corte)?></strong></div>
<br/>
<table>
    <tr>
        <th>Orden</th>
        <th>Tipo Numero</th>
        <th>Proveedor-Producto</th>
        <th>Concepto</th>
        <th>Nro Cuota</th>
        <th>Saldo Conciliado</th>
    </tr>
    <?php // $fechaMin = NULL;?>
    <?php foreach($deuda['cuotas'] as $cuota):?>
    <tr>
        <td><?php echo $cuota['orden_descuento_id']?></td>
        <td><?php echo $cuota['tipo_numero']?></td>
        <td><?php echo $cuota['proveedor_producto']?></td>
        <td><?php echo $cuota['tipo_cuota']?></td>
        <td style="text-align: center;"><?php echo $cuota['cuota']?></td>
        <td style="text-align: right;"><?php echo $util->nf($cuota['saldo_conciliado'])?></td>
    </tr>
    
    <?php 
    
//        if(!empty($cuota['vencimiento'])){
//            $fechaNow = strtotime($cuota['vencimiento']);
//        }else{
//            $periodoActual = $periodo_corte;
//        }
        
        
        
    ?>
    
    <?php endforeach;?>
    <tr class="totales">
        <th colspan="5">TOTAL DEUDA A INFORMAR</th>
        <th class="totales"><?php echo $util->nf($deuda['total'])?></th>
    </tr>
        
</table>
</div>
<?php // debug($deuda['cuota_id_saldo'])?>
<?php echo $frm->hidden('SocioInforme.socio_id',array('value' => $socio['Socio']['id'])); ?>
<?php echo $frm->hidden('SocioInforme.periodo_corte',array('value' => $periodo_corte)); ?>
<?php echo $frm->hidden('SocioInforme.empresa',array('value' => $empresa)); ?>
<?php echo $frm->hidden('SocioInforme.fecha_calculo_deuda',array('value' => $deuda['fecha_calculo_deuda'])); ?>
<?php echo $frm->hidden('SocioInforme.saldo_conciliado',array('value' => $deuda['total'])); ?>
<?php echo $frm->hidden('SocioInforme.cuotas',array('value' => base64_encode(serialize($deuda['cuota_id_saldo'])))); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'CONFIRMAR','URL' => ( empty($fwrd) ? "/pfyj/socios/alta_informe/".$socio['Socio']['id'] : $fwrd) ))?>

<?php // echo base64_encode(serialize($deuda['cuota_id_saldo']))?>

<?php endif; ?>


<?php // debug($deuda);?>