<?php echo $this->renderElement('head',array('title' => 'MODULO DE VENTAS TELEFONICAS','plugin' => 'config'));?>
<?php if(empty($persona_id)):?>
	<?php 
		//FORMULARIO DE BUSQUEDA Y GRILLA DE RESULTADOS
		echo $this->renderElement('personas/search',array('accion' => 'index','plugin' => 'pfyj','nro_socio' => true));
		echo $this->renderElement(
									'personas/grilla_personas_paginada',
									array(
											'plugin' => 'pfyj',
											'accion'=>'/mutual/ventastelefonicas/index/',
											'icon' => 'controles/editpaste.png',
											'busquedaAvanzada' => false,
											'datos_post' => $this->data
									));
		
	?>
<?php else:?>
	<!-- PAGINA PRINCIPAL PARA CARGA -->
	<?php	
	//MENU
	$tabs = array(
					0 => array('url' => '/mutual/ventastelefonicas/index/'.$persona_id.'/0','label' => 'Resumen', 'icon' => 'controles/viewmag.png','atributos' => array(), 'confirm' => null),
					1 => array('url' => '/mutual/ventastelefonicas/index/'.$persona_id.'/1','label' => 'Actualizar Datos Personales', 'icon' => 'controles/user.png','atributos' => array(), 'confirm' => null),
					2 => array('url' => '/mutual/ventastelefonicas/index/'.$persona_id.'/2','label' => 'Nuevo Adicional', 'icon' => 'controles/user_add.png','atributos' => array(), 'confirm' => null),
					3 => array('url' => '/mutual/ventastelefonicas/index/'.$persona_id.'/3','label' => 'Nuevo Servicio', 'icon' => 'controles/vcard.png','atributos' => array(), 'confirm' => null),
					4 => array('url' => '/mutual/ventastelefonicas','label' => 'Consultar Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
					
				);
	echo $cssMenu->menuTabs($tabs);			
	?>	
	<?php echo $this->renderElement('personas/datos_personales',array('persona_id' => $persona_id,'link_padron' => false,'plugin' => 'pfyj'));?>
	<?php if($opcionMenu == 0):?>
		<h3>SERVICIOS CONTRATADOS</h3>
			<?php echo $this->renderElement('mutual_servicio_solicitudes/detalle_servicios_by_persona',array('plugin' => 'mutual','persona_id' => $persona_id))?>
	<?php endif;?>
	
	<?php if($opcionMenu == 1)echo $this->renderElement('ventas_telefonicas/form_datos_personales_titular',array('plugin' => 'mutual'))?>
	
	<?php if($opcionMenu == 2)echo $this->renderElement('ventas_telefonicas/form_nuevo_adicional',array('plugin' => 'mutual'))?>
	
	<?php if($opcionMenu == 3)echo $this->renderElement('ventas_telefonicas/form_nuevo_servicio',array('plugin' => 'mutual'))?>
	
	
	
<?php endif;?>