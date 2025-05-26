<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class LiquidacionTurno extends MutualAppModel{
	
	var $name = 'LiquidacionTurno';
	
	function getDescripcionByTruno($turno){
		$turno = $this->find('all',array('conditions' => array('LiquidacionTurno.turno' => $turno),'limit' => 1));
		
		if(!empty($turno[0]['LiquidacionTurno'])){
			$descripcion = $turno[0]['LiquidacionTurno']['descripcion'];
			$empresa = parent::GlobalDato('concepto_1', $turno[0]['LiquidacionTurno']['codigo_empresa']);
			
			$glb = parent::getGlobalDato(null,$turno[0]['LiquidacionTurno']['codigo_empresa']);
			$empresa = (!empty($glb['GlobalDato']['concepto_2']) ? $glb['GlobalDato']['concepto_2'] : $glb['GlobalDato']['concepto_1']);
			
			$descripcion = $empresa . (!empty($descripcion) ? " - ".$descripcion : "");
		}else{
			$descripcion = NULL;
		}
		return $descripcion;
	}
	
	
	function getTurnoByCodRepa($codEmpre,$codigoRepa){
		//tomo los primeros 5 caracteres
		$codigoRepa = substr($codigoRepa,0,5);
		$turno = $this->find('all',array('conditions' => array('LiquidacionTurno.codigo_empresa' => $codEmpre,'substring(LiquidacionTurno.codigo_reparticion,1,5)' => $codigoRepa),'limit' => 1));
		if(empty($turno)) return null;
		else return trim($turno[0]['LiquidacionTurno']['turno']);
	}
	
	
    function getEmpresaTurnos(){
        App::import('model','config.GlobalDato');
        $oGLB = new GlobalDato();
        $datos = array();
        $empresas = $oGLB->getEmpresaList();
        if(empty($empresas)) return $datos;
        
        foreach($empresas as $codigo => $empresa){
            $datos[$codigo]['empresa'] = $empresa;
            
            $turnos = $this->find('all',array('conditions' => array('LiquidacionTurno.codigo_empresa' => $codigo)));
            
            if(!empty($turnos)){
                
                foreach($turnos as $turno){
                
                    $datos[$codigo]['turnos'][$turno['LiquidacionTurno']['id']]['turno'] = $turno['LiquidacionTurno']['turno'];
                    $datos[$codigo]['turnos'][$turno['LiquidacionTurno']['id']]['codigo_reparticion'] = $turno['LiquidacionTurno']['codigo_reparticion'];
                    $datos[$codigo]['turnos'][$turno['LiquidacionTurno']['id']]['descripcion'] = (!empty($turno['LiquidacionTurno']['descripcion']) ? $turno['LiquidacionTurno']['descripcion'] : $empresa);
                    
                }
            }
            
            
        }
        
        return $datos;
        
    }
    
    
    function importar_empresas(){
        $sql = "insert into liquidacion_turnos(turno,codigo_empresa)
                select id,id from global_datos where id like 'MUTUEMPR%' and id <> 'MUTUEMPR'
                and id not in (select codigo_empresa from liquidacion_turnos);";
        $this->query($sql);
    }
    
}
?>