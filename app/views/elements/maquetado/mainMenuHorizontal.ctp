<?php 
if(isset($Seguridad)):

	$menus = null;

	if(isset($Seguridad['Usuario']['grupo_id']) && $Seguridad['Usuario']['activo'] == 1):
		$menus = $this->requestAction('/seguridad/permisos/opcionesMenu/'.$Seguridad['Usuario']['grupo_id']);
		if(!empty($menus)):
			//echo "<div class='menuBar'>";
			echo "<ul id='nav'>";
			foreach($menus as $menu):
				//echo "<div class=\"n1\">".$menu['Permiso']['descripcion']."</div>";
				echo "<li>";
                //print $html->image('menu/'.$menu['Permiso']['icon'], array('border'=>0));
				echo "<span style='padding: 5px;'>".$menu['Permiso']['descripcion']."</span>";
				$subMenus = $this->requestAction('/seguridad/permisos/opcionesMenu/'.$Seguridad['Usuario']['grupo_id'].'/'.$menu['Permiso']['id']);
				if(!empty($subMenus)):
					echo "<ul>";
					foreach($subMenus as $subMenu):
						echo "<li>";
						//echo "<a href=''>".$subMenu['Permiso']['descripcion']."</a>";
                        if(!empty($subMenu['Permiso']['icon'])){
                            echo $html->link($html->image('menu/'.$subMenu['Permiso']['icon'], array('border'=>0))."&nbsp;".$subMenu['Permiso']['descripcion'],$subMenu['Permiso']['url'],null,null,false);
                        }else{
                            echo $html->link($subMenu['Permiso']['descripcion'],$subMenu['Permiso']['url'],null,null,false);
                        }    
						
						echo "</li>";
					endforeach;
					
					echo "</ul>";
				endif;
				echo "</li>";
			endforeach;
			//echo "</div>";
			echo "</ul>";
		endif;
		//debug($menus);
	endif;
endif;
?>

