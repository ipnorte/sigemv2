<?php
class GlobalDato extends ConfigAppModel{
	var $name = 'GlobalDato';

	var $spLiquidaPeriodo = array(
		'SP_LIQUIDA_DEUDA_CBU_PERIODO_GENERAL' => '1 - CBU PERIODO GENERAL',
		'SP_LIQUIDA_DEUDA_CBU_PERIODO_DISCRI_PERM' => '2 - CBU PERIODO GENERAL / DISCRIMINAR PERMANENTES',
		'SP_LIQUIDA_DEUDA_CBU_PERIODO_CONSOLIDA_MORA' => '3 - CBU PERIODO GENERAL / CONSOLIDADO CON MORA',
		'SP_LIQUIDA_DEUDA_CJPC_GENERAL' => '4 - CJPC GENERAL',
		'SP_LIQUIDA_DEUDA_ANSES_GENERAL' => '5 - ANSES GENERAL',
	);
	var $spLiquidaMora = array(
		'SP_LIQUIDA_DEUDA_CBU_MORA_GENERAL' => '1 - CBU MORA GENERAL CON PARAMETROS ',
		'SP_LIQUIDA_DEUDA_CBU_MORA_SPARAM' => '2 - CBU MORA GENERAL SIN PARAMETROS',
		// 'SP_LIQUIDA_DEUDA_CBU_MORA_CJPC' => '3 - CJPC MORA GENERAL',
		// 'SP_LIQUIDA_DEUDA_CBU_MORA_ANSES' => '4 - ANSES MORA GENERAL',
	);
	
	function del($id = null, $cascade = true){
		$ret = false;
		$rws = $this->find('count',array('conditions' => array('GlobalDato.id LIKE' => $id . '%', 'GlobalDato.id <>' => $id)));
		if($rws == 0){
			$ret = parent::del($id);	
		}
		return $ret;
	}
	
	function save($data = null, $validate = true, $fieldList = array()){
            if(isset($data['GlobalDato']['codigo']) && isset($data['GlobalDato']['codigo_prefijo'])){
                $data['GlobalDato']['codigo'] = str_pad($data['GlobalDato']['codigo'],4,0,STR_PAD_LEFT);
                $data['GlobalDato']['id'] = $data['GlobalDato']['codigo_prefijo'].$data['GlobalDato']['codigo'];
            }
            return parent::save($data);
	}        
	
	function getOrganismos(){
		$values = $this->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUCORG%', 'GlobalDato.id <> ' => 'MUTUCORG'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}
	
	
	function getEmpresaList($organismo = null,$incluyeTurno = false){
		if(!$incluyeTurno):
			$conditions = array();
			$conditions['GlobalDato.id LIKE'] = 'MUTUEMPR%';
			$conditions['GlobalDato.id <>'] = 'MUTUEMPR';
			$conditions['GlobalDato.logico_1'] = 1;
//            $organismos = explode(",", $organismo);
//            if(is_array($organismos)){
//                foreach ($organismos as $i => $valores){
//                    $organismos[$i] = substr($valores,8,4);
//                }
//            }
			if(!empty($organismo)) $conditions['GlobalDato.entero_1'] = substr($organismo,8,4);
//            if(!empty($organismo)) $conditions['GlobalDato.concepto_4'] = $organismos;
			$values = $this->find('list',array('conditions' => $conditions,'fields' => array('concepto_1'),'order' => array('GlobalDato.concepto_1')));
		else:
			$sql = "select GlobalDato.id,LiquidacionTurno.turno,concat(GlobalDato.id,'|',LiquidacionTurno.turno) as keylist,
					GlobalDato.concepto_1, LiquidacionTurno.descripcion
					from liquidacion_turnos as LiquidacionTurno, global_datos as GlobalDato
					where 
					GlobalDato.id = LiquidacionTurno.codigo_empresa
					and GlobalDato.id like 'MUTUEMPR%'
					and GlobalDato.id <> 'MUTUEMPR'
					".(!empty($organismo) ? " and GlobalDato.entero_1 = " . substr($organismo,8,4) : "")."
					group by GlobalDato.id,LiquidacionTurno.turno,
					GlobalDato.concepto_1, LiquidacionTurno.descripcion
					order by GlobalDato.concepto_1, LiquidacionTurno.descripcion;";
			$values = array();
			$lista = $this->query($sql);
			if(empty($lista)) return $values;
			foreach($lista as $item){
				$values[$item[0]['keylist']] = $item['GlobalDato']['concepto_1'] . (!empty($item['LiquidacionTurno']['descripcion']) ? " - " . substr($item['LiquidacionTurno']['turno'],-5) . " - " . $item['LiquidacionTurno']['descripcion'] : "");
			}
		endif;
		return $values;
	}
	
    
    function getEmpresas($soloActivas = 1){
		if($soloActivas != 1)$values = $this->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUEMPR%', 'GlobalDato.id <> ' => 'MUTUEMPR'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		else $values = $this->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUEMPR%', 'GlobalDato.id <> ' => 'MUTUEMPR', 'GlobalDato.logico_1' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
    }
    
    function get_productos_siisa() {
        $sql = "select GlobalDato.concepto_4 from global_datos GlobalDato
                where GlobalDato.id like 'MUTUEMPR%'
                and GlobalDato.id <> 'MUTUEMPR' and ifnull(GlobalDato.concepto_4, '') <> '' group by GlobalDato.concepto_4 
                order by GlobalDato.concepto_4;";
        $values = $this->query($sql);
        return $values;
    }
    
	
}
?>