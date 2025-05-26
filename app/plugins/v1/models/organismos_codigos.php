<?php
class OrganismosCodigos extends V1AppModel{
	
	var $name = 'OrganismosCodigos';
	var $useTable = 'organismos_codigos';

	
	function getCodigo($codigo_item){
		$sql = "select OrganismosCodigos.* from organismos_codigos as OrganismosCodigos 
				where codigo_item = '$codigo_item'";
		$codigo = parent::query($sql);
		return (isset($codigo[0]) ? $codigo[0] : null);		
	}
}
?>