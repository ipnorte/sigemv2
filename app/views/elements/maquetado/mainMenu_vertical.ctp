<?php 

//echo $cssMenu->menu(array(
//								'Menu_1'=> array('Menu_1.1'=> $this->base.'/activities/add','Menu_1.2'=> $this->base.'/activities/index'),
//								'Menu_2'=> array('Menu_2.1'=> $this->base.'/activities/add','Menu_2.2'=> $this->base.'/activities/index'),
//								'Menu_3'=> array('Menu_3.1'=> $this->base.'/activities/add','Menu_3.2'=> $this->base.'/activities/index'),
//
//						),'down');
						
						
echo $cssMenu->menuPrincipal(array(
								'Seguridad'=> array('Menu_1.1'=> $this->base.'/activities/add','Menu_1.2'=> $this->base.'/activities/index'),
								'Configuraciones'=> array('Menu_2.1'=> $this->base.'/activities/add','Menu_2.2'=> $this->base.'/activities/index'),
								'Menu_3'=> array('Menu_3.1Menu_3.1'=> $this->base.'/activities/add','Menu_3.2'=> $this->base.'/activities/index'),
								
						));


?>

