<?php
/**
 * PROCESO PARA AGREGAR A LA ORDEN DE DESCUENTO EL PRODUCTOR
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * LANZADOR
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php tmp_productores -app /home/adrian/Desarrollo/www/sigem/app/
 *
 */

class TmpProductoresShell extends Shell{


	function main(){
		
//		return null;
		
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();

		App::import('Model','V1.Solicitud');
		$oSOLICITUD = new Solicitud();
//		
//		App::import('Model','V1.Productor');
//		$oPRODUCTOR = new Productor();		
		
		$oORDEN->unbindModel(array('hasMany' => array('OrdenDescuentoCuota')));
		$registros = $oORDEN->find('all',array('conditions' => array('OrdenDescuento.tipo_orden_dto' => 'EXPTE', 'OrdenDescuento.numero >=' => 500000),'fields' => array('OrdenDescuento.id,OrdenDescuento.numero')));
		
		$solicitud = null;

		$i = 1;
		
		foreach($registros as $registro){
			
			$orden_id = $registro['OrdenDescuento']['id'];
			$numero = $registro['OrdenDescuento']['numero'];
			
			$solicitud = $oSOLICITUD->read("codigo_productor",$numero);
			
			$productor_id = $solicitud['Solicitud']['codigo_productor'];
			$productor_ref = $oSOLICITUD->getNombreCortoProductor($numero);
			
			$registro['OrdenDescuento']['productor_id'] = $productor_id;
			$registro['OrdenDescuento']['productor_ref'] = $productor_ref;
			
			$this->out("$i\tORDEN: $orden_id \tNUMERO: $numero \t\tPRODUCTOR_ID: $productor_id \tREF: $productor_ref");
			$oORDEN->save($registro);
			$i++;
		}
		

		
	}
	
	
}

?>