<script type="text/javascript">
<!--
function toggleSubMenu(id){
	var status = document.getElementById(id).style.display;
	if(status==='none')document.getElementById(id).style.display = 'inline';
	else document.getElementById(id).style.display = 'none';
}
//-->
</script>
<?php 
$menus = $this->requestAction('/seguridad/permisos/menu/'.$grupo);
//debug($menus);
//print "<br>";
foreach($menus as $menu){
	print "<div id=\"m_".$menu['Permiso']['id']."\" class='MenuTitle' onclick=\"javascript:toggleSubMenu('sm_".$menu['Permiso']['id']."')\">";
	if(!empty($menu['Permiso']['icon'])) print $html->image('menu/'.$menu['Permiso']['icon'], array('border'=>0));
	print "&nbsp;<strong>".$menu['Permiso']['descripcion']."</strong>";
	print "</div>";
	print "<div id=\"sm_".$menu['Permiso']['id']."\" class='MenuSubTitleContainer' style='display:none;'>";
	$subMenus = $this->requestAction('/seguridad/permisos/menu/'.$grupo.'/'.$menu['Permiso']['id']);
	
	foreach($subMenus as $subMenu){
		print "<div class='MenuSubTitle'>";
		if(!empty($subMenu['Permiso']['icon']))print $html->link($html->image('menu/'.$subMenu['Permiso']['icon'], array('border'=>0)).'&nbsp;'.$subMenu['Permiso']['descripcion'],$subMenu['Permiso']['url'],null,null,false);
		else print $html->link($subMenu['Permiso']['descripcion'],$subMenu['Permiso']['url'],null,null,false);
		print "</div>";
	}
	print "</div>";
//	
//	debug($menu2);
}

?>
