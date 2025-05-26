<?php
/**
 * @deprecated
 * Se utiliza el modelo LiquidacionSocioRendicion
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class LiquidacionIntercambioRegistro extends MutualAppModel{
	
	var $name = 'LiquidacionIntercambioRegistro';
	

	function generarDetalle($liquidacion_intercambio_id,$path){
		$data['LiquidacionIntercambioRegistro']['liquidacion_intercambio_id'] = $liquidacion_intercambio_id;
		$handle = fopen($path, "rb");
		$contents = '';
		while (!feof($handle)) {
		  $contents .= fread($handle, 8192);
		}
		fclose($handle);
		$rows = explode("\n",$contents);
		foreach($rows as $row){
			if(!empty($row)){
				$data['LiquidacionIntercambioRegistro']['registro'] = $row;
				$this->save($data);
				$this->id = 0;
			}
		}
	}
	
	
	function registrosNoEncontradosEnLiquidacionSocio($liquidacion_id){
		
	}
	
}
?>