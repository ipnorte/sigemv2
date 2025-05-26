<?php
class ContabilidadAppModel extends AppModel{
	
	function traeEjercicio($ejercicio_id){
		App::import('Model','Contabilidad.Ejercicio');
		$oEjercicio = new Ejercicio();

		$ejercicio = $oEjercicio->find('all',array(
							'conditions' => array('Ejercicio.id' => $ejercicio_id)		
		));
		if(empty($ejercicio)) return null;
		else return $ejercicio[0]['Ejercicio'];
	}
	
	function getCuenta($id){
		App::import('Model','Contabilidad.PlanCuenta');
		$oPlanCuenta = new PlanCuenta();		
		return $oPlanCuenta->getCuenta($id);
	}
	
	function traeNroAsiento($ejercicio_id){
		$ejercicio = $this->traeEjercicio($ejercicio_id);
		$nroAsiento = $ejercicio['nro_asiento'] + 1;
		App::import('Model','Contabilidad.Ejercicio');
		$oEjercicio = new Ejercicio();
		$oEjercicio->id = $ejercicio_id;
		if(!$oEjercicio->saveField('nro_asiento', $nroAsiento)) return 0;
		else return $nroAsiento;	
		
	}
	
	
	function getEjercicioVigente(){

		$glb = $this->getGlobalDato('entero_1', 'CONTEVIG');
		
		return $glb['GlobalDato']['entero_1'];
		
	}
	
	
	function lookRegistro($id=0){
            App::import('Model','Contabilidad.Ejercicio');
            $oEjercicio = new Ejercicio();

            if($id == 0){
                $aEjercicio = $this->traeEjercVigente();
                $id = $aEjercicio['id'];
            }
//		$id = $this->getEjercicioVigente();
            $i = 0;
            while ($i <= 50):
                    $look = $oEjercicio->read(null, $id);
                    if($look['Ejercicio']['look'] == 0):
                            $look['Ejercicio']['look'] = 1;
                            if($oEjercicio->guardar($look['Ejercicio'])):
                                    return true;
                            endif;
                    endif;
                    $i++;
            endwhile;

            return false;
	}
	
	
	function unLookRegistro($id=0){
            App::import('Model','Contabilidad.Ejercicio');
            $oEjercicio = new Ejercicio();
		
            if($id == 0){
                $aEjercicio = $this->traeEjercVigente();
                $id = $aEjercicio['id'];
            }
//		$id = $this->getEjercicioVigente();
            $i = 0;

            while ($i <= 50):
                $unLook = $oEjercicio->read(null, $id);
                if($unLook['Ejercicio']['look'] == 1):
                    $unLook['Ejercicio']['look'] = 0;
                    if($oEjercicio->save($unLook)):
                        return true;
                    endif;
                endif;
                $i++;
            endwhile;


            return false;
	}
	
	
	function getNumeroAsiento($id=0){
            App::import('Model','Contabilidad.Ejercicio');
            $oEjercicio = new Ejercicio();

            if($id == 0){
                $aEjercicio = $this->traeEjercVigente();
                $id = $aEjercicio['id'];
            }
//		$id = $this->getEjercicioVigente();
            $i = 0;

            while ($i <= 50):
                if($this->lookRegistro($id)):
                    $look = $oEjercicio->read(null, $id);
                    return $look['Ejercicio']['nro_asiento'] + 1;
                endif;
                $i++;
            endwhile;

            return 0;
	}
	
	function putNumeroAsiento($id=0, $nCantidad=1){
            App::import('Model','Contabilidad.Ejercicio');
            $oEjercicio = new Ejercicio();

            if($id == 0){
                $aEjercicio = $this->traeEjercVigente();
                $id = $aEjercicio['id'];
            }
//            $id = $this->getEjercicioVigente();
            $i = 0;

            while ($i <= 50):
                $look = $oEjercicio->read(null, $id);
                if($look['Ejercicio']['look'] == 1):
                    $look['Ejercicio']['look'] = 0;
                    $look['Ejercicio']['nro_asiento'] += $nCantidad;
                    if($oEjercicio->save($look['Ejercicio'])):
                        return true;
                    endif;
                endif;
                $i++;
            endwhile;

            return false;
		
	}
	
	
	function getEjercicio($id){
		
		return $this->read(null, $id);
		
	}
	
	
	function traeEjercicioAnt($ejercicio_id){
            App::import('Model','Contabilidad.Ejercicio');
            $oEjercicio = new Ejercicio();

            $ejercicioVig = $this->traeEjercicio($ejercicio_id);
            
            $ejercicio = $oEjercicio->find('all',array(
                                                    'conditions' => array('Ejercicio.fecha_hasta <' => $ejercicioVig['fecha_hasta']),
                                                    'order' => array('Ejercicio.fecha_hasta DESC')
            ));
            if(empty($ejercicio)) return null;
            else return $ejercicio[0]['Ejercicio'];
	}
	
	
	function traeEjercicioPos($ejercicio_id){
            App::import('Model','Contabilidad.Ejercicio');
            $oEjercicio = new Ejercicio();

            $ejercicioVig = $this->traeEjercicio($ejercicio_id);
            
            $ejercicio = $oEjercicio->find('all',array(
                                                    'conditions' => array('Ejercicio.fecha_hasta >' => $ejercicioVig['fecha_hasta']),
                                                    'order' => array('Ejercicio.fecha_hasta')
            ));
            if(empty($ejercicio)) return null;
            else return $ejercicio[0]['Ejercicio'];
	}
	
	function traeEjercVigente(){
		App::import('Model','Contabilidad.Ejercicio');
		$oEjercicio = new Ejercicio();

		$ejercicio = $oEjercicio->find('all',array(
							'conditions' => array('Ejercicio.activo' => 1)		
		));

		if(empty($ejercicio)) return null;
		else return $ejercicio[0]['Ejercicio'];
	}
}
?>