<?php
/**
 * PROCESO PARA AGREGAR A LA ORDEN DE DESCUENTO EL PRODUCTOR
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * LANZADOR
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php tmp_solicitudes 7 -app /home/adrian/dev/www/sigem/app/
 *
 *
 */

class TmpSolicitudesShell extends Shell{


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
		
		$conditions = array();
		$conditions['OrdenDescuento.tipo_orden_dto'] = 'EXPTE';
		$conditions['OrdenDescuento.numero >='] = 500000;
		$conditions['OrdenDescuento.proveedor_id'] = $this->args[0];
		
		$registros = $oORDEN->find('all',array('conditions' => $conditions));
		
		$solicitud = null;

		$i = 1;
		
		foreach($registros as $registro){
			
			$orden_id = $registro['OrdenDescuento']['id'];
			$numero = $registro['OrdenDescuento']['numero'];
			
			$solicitud = $oSOLICITUD->read("en_mano,solicitado,cuotas,monto_cuota",$numero);
			
			$enMano = $solicitud['Solicitud']['en_mano'];
			$capital = $solicitud['Solicitud']['solicitado'];
			$cuotas = $solicitud['Solicitud']['cuotas'];
			$montoCuota = $solicitud['Solicitud']['monto_cuota'];
			
			$registro['OrdenDescuento']['importe_solicitado'] = $enMano;
			$registro['OrdenDescuento']['importe_capital'] = $capital;
			
			$this->out("$i\tORDEN: $orden_id \tNUMERO: $numero \t\tSOLICITADO: $capital \tEN_MANO: $enMano");
			
//			debug($registro);
			
			$oORDEN->save($registro);
			$i++;
		}
		

		
	}
	
	
}

?>