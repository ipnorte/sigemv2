<?php 

if($menuPersonas == 1)
{

    echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));
    
}
else
{
    
    echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));
}

?>

<h3>LIQUIDACIONES DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3>DETALLE DE INFORMACION A ENVIAR PARA DESCUENTO</h3>
<?php echo $frm->create(null,array('action' => 'modificar_importes_dto/'.$socio['Socio']['id'].'/'.$liquidacion['Liquidacion']['id']))?>
<table>
	<tr>
		<th class="subtabla">REG.</th>
		<th class="subtabla">ORGANISMO</th>
		<th class="subtabla">IDENTIFICACION</th>
		<th class="subtabla">TURNO</th>
		<th class="subtabla">LIQUIDADO</th>
		<th class="subtabla">A DEBITAR</th>
	</tr>
	<?php foreach($liquidaciones as $liquidacion):?>
	
		<tr>
			<td align="center"><?php echo $liquidacion['LiquidacionSocio']['registro']?></td>
			<td align="center" nowrap="nowrap"><?php echo $util->globalDato($liquidacion['LiquidacionSocio']['codigo_organismo'])?></td>
			<td><?php echo $liquidacion['LiquidacionSocio']['beneficio_str']?></td>
			<td align="center"><?php echo $liquidacion['LiquidacionSocio']['turno']?></td>
			<td align="right"><?php echo $util->nf($liquidacion['LiquidacionSocio']['importe_dto'])?></td>
			<td align="right"><input type="text" name="data[LiquidacionSocio][importe_adebitar][<?php echo $liquidacion['LiquidacionSocio']['id']?>]" class="input_number" onkeypress="return soloNumeros(event,true)" value="<?php echo $liquidacion['LiquidacionSocio']['importe_adebitar']?>" /></td>
		
		</tr>
	
	<?php endforeach;?>		

</table>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/mutual/liquidaciones/by_socio/".$socio['Socio']['id']."/1" : $fwrd) ))?>
<?php echo $frm->end()?>
<?php //   debug($liquidaciones)?>