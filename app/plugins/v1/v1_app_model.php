<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package v1
 * @subpackage model
 */
class V1AppModel extends AppModel{
	
	var $useDbConfig = 'v1';
	
	function save($data = null, $validate = true, $fieldList = array()){
		return parent::save($data,$validate,$fieldList);
	}
	
	
	function getBanco($fields,$codigo_banco){
		App::import('Model', 'V1.BancoV1');
		$this->BancoV1 = new BancoV1(null);
		return $this->BancoV1->read($fields,$codigo_banco);
	}
	
	function getGlobal($fields,$codigo){
		App::import('Model', 'V1.Tglobal');
		$this->Tglobal = new Tglobal(null);	
		return $this->Tglobal->read($fields,$codigo);		
	}
	

	function fechaMySql($fecha,$toMySql=false){
		$fechaNormalizada = "";
		$afecha = array();
		if($fecha!=""){
			if($toMySql){
			    ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $afecha);
			    $fechaNormalizada = $afecha[3]."-".$afecha[2]."-".$afecha[1];
			}else{
				ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha,$afecha);
				$fechaNormalizada = $afecha[3]."/".$afecha[2]."/".$afecha[1];
			}
		}else{
			$fechaNormalizada = '0000-00-00';
		}
		if($fechaNormalizada=='--')$fechaNormalizada=null;
		return $fechaNormalizada;
	}	
	
    function openFile($path){
		$txtFile = "";
		if(file_exists($path)){
			$file = fopen ($path,"r");
			while (!feof ($file)){
				$txtFile .= fgets ($file, 8192);
			}
			fclose($file);
		}
		return $txtFile;    	
    }	
	
}
?>