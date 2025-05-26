<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>LISTADO DE PAGOS DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div class="areaDatoForm">
	<?php //   echo $frm->create(null,array('action' => 'imprimir/'. $socio['Socio']['id'],'id' => 'form_cobros_por_fecha'))?>
	
		<?php 
		
		echo $ajax->form(array('type' => 'post',
		    'options' => array(
		        'model'=>'OrdenDescuentoCobro',
		        'update'=>'detalle_cobros',
		        'url' => array('plugin' => 'mutual','controller' => 'orden_descuento_cobros', 'action' => 'consulta_pagos/'.$socio['Socio']['id']),
				'loading' => "$('spinner').show();$('detalle_cobros').hide();",
				'complete' => "$('detalle_cobros').show();$('spinner').hide();"
		    )
		));
		 
		
		
		?>		
	
	<table class="tbl_form">
		
		<tr>
			<td>DESDE</td>
			<td><?php echo $frm->input('periodo_desde',array('type'=>'select','options'=>$util->cmbPeriodoSocio($periodos),'empty'=>FALSE,'label'=>''));?></td>
			<td>HASTA</td>
			<td><?php echo $frm->input('periodo_hasta',array('type'=>'select','options'=>$util->cmbPeriodoSocio($periodos),'empty'=>FALSE,'label'=>''));?></td>
			
		</tr>
		<tr>
			<td>TIPO COBRO</td>
			<td colspan="2">
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'OrdenDescuentoCobro.tipo_cobro',
																			'prefijo' => 'MUTUTCOB',
																			'disabled' => false,
																			'empty' => true,
																			'metodo' => "get_todos_tipos_cobro_caja",
																			'selected' => ""	
			))?>							
			</td>
			<td><?php echo $frm->submit("CONSULTAR")?></td>		
		</tr>
	</table>
	<?php echo $frm->hidden('socio_id',array('value' => $socio['Socio']['id']))?>
	<?php echo $frm->end()?>
</div>
<?php echo $controles->ajaxLoader('spinner','CONSULTANDO PAGOS DEL SOCIO....')?>
<div id="detalle_cobros" style="clear: both;"></div>
