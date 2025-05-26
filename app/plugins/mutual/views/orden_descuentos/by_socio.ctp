<?php
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>HISTORIAL DE CONSUMOS DEL SOCIO</h3>

<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('persona_id' => $socio['Socio']['persona_id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php //   debug($periodos)?>
<?php //   echo $this->renderElement('orden_descuento/grilla_ordenes_vigentes',array('socio_id'=>$socio['Socio']['id'],'plugin' => 'mutual'))?>

<?php if(!empty($periodos)):?>
	<div id="FormSearch">
	
	
		<?php 
		
		echo $ajax->form(array('type' => 'post',
		    'options' => array(
		        'model'=>'OrdenDescuento',
		        'update'=>'detalle_consumos',
		        'url' => array('plugin' => 'mutual','controller' => 'orden_descuentos', 'action' => 'by_socio/'.$socio['Socio']['id'].'/'.$menuPersonas),
				'loading' => "$('spinner').show();$('detalle_liquidacion').hide();",
				'complete' => "$('detalle_consumos').show();$('spinner').hide();"
		    )
		));
		 
		
		
		?>	
		
		<table>
			<tr>
				<td><?php echo $frm->input('estado_actual',array('type'=>'select','options'=>array(0 => '1 - VIGENTES (CON SALDO)',2 => '2 - FINALIZADOS (PAGADOS TOTALMENTE)',3 => '3 - BAJA',4 => '4 - PERMANENTES VIGENTES'),'empty'=>FALSE,'label'=>'ESTADO ACTUAL'));?></td>
				<td><?php echo $frm->submit('CONSULTAR',array('class' => 'btn_consultar'));?></td>
			</tr>
		</table>
		<?php echo $frm->end();?> 
	</div>
	<?php echo $controles->ajaxLoader('spinner','CONSULTANDO CONSUMOS....')?>
	<div id="detalle_consumos" style="clear: both;"></div>
<?php else:?>
	<h4>NO EXISTEN CONSUMOS PARA EL SOCIO</h4>
<?php endif;?>	


