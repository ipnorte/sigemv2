<?php 
if($menuPersonas == 1){ 
    echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));
}else{ 
    echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));
}
?>
<h3>LIQUIDACIONES DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php if(!empty($periodos)):?>
	<div id="FormSearch">
	
	
		<?php 
		
		echo $ajax->form(array('type' => 'post',
		    'options' => array(
		        'model'=>'LiquidacionSocio',
		        'update'=>'detalle_liquidacion',
		        'url' => array('plugin' => 'mutual','controller' => 'liquidaciones', 'action' => 'by_socio/'.$socio['Socio']['id'].'/'.$menuPersonas),
				'loading' => "$('spinner').show();$('detalle_liquidacion').hide();",
				'complete' => "$('detalle_liquidacion').show();$('spinner').hide();"
		    )
		));
		 
		
		
		?>	
		
		<table>
			<tr>
				<td>
					<?php echo $frm->input('periodo',array('type'=>'select','options'=>$util->cmbPeriodoSocio($periodos),'empty'=>FALSE,'label'=>'PERIODO DESDE'));?>
					<?php echo $frm->input('periodo_hasta',array('type'=>'select','options'=>$util->cmbPeriodoSocio($periodos),'empty'=>FALSE,'label'=>'HASTA'));?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $this->renderElement('global_datos/combo_global',array(
																					'label' => 'ORGANISMO',
																					'plugin'=>'config',
																					'model' => 'LiquidacionSocio.codigo_organismo',
																					'prefijo' => 'MUTUCORG',
																					'disabled' => false,
																					'empty' => true,
																					'metodo' => "get_organismos",
																					'selected' =>  (isset($this->data['LiquidacionSocio']['codigo_organismo']) ? $this->data['LiquidacionSocio']['codigo_organismo'] : "")	
					))?>
					<?php echo $this->renderElement('proveedor/combo_general',array(
																					'label' => 'PROVEEDOR',
																					'plugin'=>'proveedores',
																					'metodo' => "proveedores_list",
																					'model' => 'LiquidacionSocio.proveedor_id',
																					'empty' => true,
																					'selected' => (isset($this->data['LiquidacionSocio']['proveedor_id']) ? $this->data['LiquidacionSocio']['proveedor_id'] : "")
					))?>
				</td>
			</tr>
			<tr>	
				<td>
				<?php echo $frm->submit('CONSULTAR',array('class' => 'btn_consultar'));?>
				</td>
			</tr>
		</table>
            
                <?php echo $controles->btnModalBox(array('title' => 'GENERAR LIQUIDACION','url' => '/mutual/liquidacion_socios/generar_liquidacion/'.$socio['Socio']['id'],'h' => 150, 'w' => 650, 'img' => 'calculator.png', 'texto' => 'GENERAR LIQUIDACION NO EXISTENTE'))?>
            
            
		
		<?php echo $frm->end();?> 
	</div>
	<?php echo $controles->ajaxLoader('spinner','CONSULTANDO LIQUIDACION....')?>
	<div id="detalle_liquidacion" style="clear: both;"></div>
<?php else:?>
	<h4>NO EXISTEN LIQUIDACIONES GENERADAS</h4>
	<div class="areaDatoForm">
		<?php if(!empty($periodos_liquidados)):?>
			<?php echo $frm->create(null,array('action'=>'generar_liquidacion_puntual/'.$socio['Socio']['id'],'id' => 'formGeneraLiquidacion'))?>
			<table class="tbl_form">
				<tr>
					<td>LIQUIDACIONES ABIERTAS</td>
					<td><?php echo $frm->input('periodo',array('type'=>'select','options'=>$util->cmbPeriodoSocio($periodos_liquidados),'empty'=>FALSE));?></td>
					<td><?php echo $frm->submit('GENERAR LIQUIDACION',array('class' => 'btn_liquidar'));?></td>
				</tr>
			</table>
			<?php echo $frm->hidden('Liquidacion.socio_id', array('value' => $socio['Socio']['id']))?>
			<?php echo $frm->end();?>
		<?php else:?>
			<input type="button" value="GENERAR LIQUIDACION" onclick="javascript:window.location='<?php echo $this->base?>/mutual/liquidacion_socios/generar_liquidacion/<?php echo $socio['Socio']['id']?>'" />
		<?php endif;?>	
	<?php //   debug($periodos_liquidados)?>
	</div>
<?php endif;?>	