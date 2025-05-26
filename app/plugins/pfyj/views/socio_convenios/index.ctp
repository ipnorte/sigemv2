<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>CONVENIOS DE PAGO DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div class="actions"><?php echo $controles->botonGenerico('crear_convenio/'.$socio['Socio']['id'],'controles/add.png','Nuevo Convenio de Pago')?></div>

<?php if(!empty($convenios)):?>
	<table>
		<tr>
			<th>#</th>
			<th>ORD.DTO.</th>
			<th>PROVEEDOR</th>
			<th>TIPO CONVENIO</th>
			<th>ORGANISMO</th>
			<th>BENEFICIO</th>
			<th>TOTAL</th>
			<th>CUOTAS</th>
			<th>IMPORTE</th>
		</tr>	
		<?php foreach($convenios as $convenio):?>
			<tr>
			
				<td><strong><?php echo $controles->linkModalBox($convenio['SocioConvenio']['id'],array('title' => 'CONVENIO DE PAGO #' . $convenio['SocioConvenio']['id'],'url' => '/pfyj/socio_convenios/view/'.$convenio['SocioConvenio']['id'],'h' => 450, 'w' => 850))?></strong></td>
				<td align="center"><?php echo $controles->linkModalBox($convenio['SocioConvenio']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $convenio['SocioConvenio']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$convenio['SocioConvenio']['orden_descuento_id'].'/'.$convenio['SocioConvenio']['socio_id'],'h' => 450, 'w' => 850))?></td>
				<td><?php echo $convenio['SocioConvenio']['proveedor_razon_social']?></td>
				<td><?php echo $convenio['SocioConvenio']['tipo_convenio_desc']?></td>
				<td><?php echo $convenio['SocioConvenio']['organismo_desc']?></td>
				<td><?php echo $convenio['SocioConvenio']['beneficio_str']?></td>
				<td align="right"><?php echo $util->nf($convenio['SocioConvenio']['importe_total'])?></td>
				<td align="center"><?php echo $convenio['SocioConvenio']['cuotas']?></td>
				<td align="right"><?php echo $util->nf($convenio['SocioConvenio']['importe_cuota'])?></td>
			</tr>
		
		<?php endforeach;?>
	</table>
<?php //   debug($convenios)?>
<?php else:?>
	<h4>SIN CONVENIOS DE PAGO</h4>
<?php endif;?>