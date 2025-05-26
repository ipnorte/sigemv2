<?php
/**
 * DESFRAGMENTA UN ARCHIVO DE AUDITORIA
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php analisis_archivo_auditoria 4820 -app /home/adrian/dev/www/sigem/app/
 *
 * @author adrian [21/03/2012]
 *
 */
class AnalisisArchivoAuditoriaShell extends Shell{

	function main(){
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$file_name = LOGS . $asinc->getParametro('p1');

		if(file_exists($file_name)):
		
			$registros = array();
			$registros = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		
			foreach($registros as $n => $linea):
			
				list($fechaHora,$usuario,$ip,$accion,$valor) = explode(chr(174).chr(175),$linea);
				
					
					if($accion === 'LINK') $this->out($fechaHora . " *** " . $usuario . " *** " . $accion . " *** " . $valor);
					if($accion == 'SAVE'){
						$model = unserialize(stripslashes($valor));
						$model['data_model_serialized'] = unserialize($model['data_model_serialized']);
						
						debug($model);
					}
			
			endforeach;
		
		endif;
		
		
		
	}
	
}

?>