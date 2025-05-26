<?php
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>ESTADO DE CUENTA DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php if(!empty($periodos)):?>
<div id="FormSearch">
	
	<?php 
	
	echo $ajax->form(array('type' => 'post',
	    'options' => array(
	        'model'=>'OrdenDescuentoCuota',
	        'update'=>'detalle_estado_cuenta',
	        'url' => array('plugin' => 'mutual','controller' => 'orden_descuento_cuotas', 'action' => 'estado_cuenta2/'.$socio['Socio']['id']),
			'loading' => "$('spinner').show();$('detalle_estado_cuenta').hide();",
			'complete' => "$('detalle_estado_cuenta').show();$('spinner').hide();"
	    )
	));
	 
	
	
	?>	
	<script type="text/javascript">
		Event.observe(window, 'load', function() {
			$('periodo_ini').toggle();
//			$('chkDiscriPagos').hide();
			$('OrdenDescuentoCuotaSoloDeuda').observe('click',function(){
				$('periodo_ini').toggle();
//				if(document.getElementById('OrdenDescuentoCuotaSoloDeuda').checked) $('chkDiscriPagos').hide();
//				else $('chkDiscriPagos').show();
			});
			
		});	

	</script>
	<?php //   debug($periodos)?>
	<table>
		<tr>
			<td id="periodo_ini"><?php echo $frm->input('periodo_ini',array('type'=>'select','options'=>$util->cmbPeriodoSocio($periodos),'empty'=>FALSE,'selected' => $periodo_ini,'label'=>'PERIODO DESDE'));?></td>
			<td id="periodo_fin"><?php echo $frm->input('periodo_fin',array('type'=>'select','options'=>$util->cmbPeriodoSocio($periodos),'empty'=>FALSE,'selected' => $periodo_fin,'label'=>'PERIODO HASTA'));?></td>
			<td>SOLO LO ADEUDADO <input type="checkbox" name="data[OrdenDescuentoCuota][solo_deuda]" value="1" id="OrdenDescuentoCuotaSoloDeuda" checked="checked"/></td>
			<!--<td id="chkDiscriPagos">DETALLAR PAGOS<input type="checkbox" name="data[OrdenDescuentoCuota][discrimina_pagos]" value="1" id="OrdenDescuentoCuotaDiscriminaPagos"/></td>-->
			
		</tr>
		<tr>
			<td colspan="4">
				<table>
					<tr>
                                                <td><?php echo $frm->input('codigo_organismo',array('type'=>'select','options'=>$organismos,'empty'=>true,'selected' => '','label'=> 'ORGANISMO'));?></td>
						<td><?php echo $frm->input('proveedor_id',array('type'=>'select','options'=>$proveedores,'empty'=>true,'selected' => '','label'=> 'PROVEEDOR'));?></td>
						<td><?php echo $frm->input('tipo_producto',array('type'=>'select','options'=>$productos,'empty'=>true,'selected' => '','label'=> 'PRODUCTO'));?></td> 
						<td><?php echo $frm->input('situacion',array('type'=>'select','options'=>$situaciones,'empty'=>true,'selected' => '','label'=> 'SITUACION'));?></td>                                                
                                                
						<td colspan="2"><?php echo $frm->submit('CONSULTAR',array('class' => 'btn_consultar'));?></td>
						
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php echo $frm->end();?> 
</div>
<?php echo $controles->ajaxLoader('spinner','PROCESANDO ESTADO DE CUENTA...')?>
<div id="detalle_estado_cuenta"></div>
<?php else:?>
<h4>NO EXISTEN CUOTAS GENERADAS PARA EL SOCIO</h4>	
<?php endif;?>
