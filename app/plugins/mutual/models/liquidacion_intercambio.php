<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class LiquidacionIntercambio extends MutualAppModel{
	
	var $name = 'LiquidacionIntercambio';
	var $dir_up;
	var $dir_url;
	var $extensiones = array('TXT','HAB');
	
//	var $validate = array(
//		"archivo_nombre" => array("rule" => "validarFile", "required" => true, "allowEmpty" => false, "message" => "message")
//	);
	
	

	function __construct(){
		$this->dir_up = 'files' . DS . 'intercambio' . DS;
		$this->dir_url = 'files/intercambio/';
		parent::__construct();			
	}
	
	
	function get($id){
		$archivo = $this->read(null,$id);
		$archivo = $this->infoAdicional($archivo);
		return $archivo;
	}
	
	function getByLiquidacionId($liquidacion_id,$toReciboLnk = false){
		
		$archivos = $this->find('all',array('conditions' => array('LiquidacionIntercambio.liquidacion_id' => $liquidacion_id),'order' => array('LiquidacionIntercambio.created')));
		foreach($archivos as $clave => $archivo){
			$archivo = $this->infoAdicional($archivo);
			$archivo['LiquidacionIntercambio']['recibo_link'] = null;
			if($archivo['LiquidacionIntercambio']['recibo_id'] > 0)	$archivo['LiquidacionIntercambio']['recibo_link'] = $this->getReciboLink($archivo['LiquidacionIntercambio']['recibo_id']);
			$archivos[$clave] = $archivo;
		}
		
		return $archivos;
		
	}
	
	
	function infoAdicional($archivo){
		$archivo['LiquidacionIntercambio']['organismo'] = parent::GlobalDato('concepto_1', $archivo['LiquidacionIntercambio']['codigo_organismo']);
		$archivo['LiquidacionIntercambio']['periodo_desc'] = parent::periodo($archivo['LiquidacionIntercambio']['periodo']);
		$archivo['LiquidacionIntercambio']['periodo_desc_amp'] = parent::periodo($archivo['LiquidacionIntercambio']['periodo'],true);
		$archivo['LiquidacionIntercambio']['banco_intercambio'] = parent::getNombreBanco($archivo['LiquidacionIntercambio']['banco_id']);
		$archivo['LiquidacionIntercambio']['proveedor_razon_social'] = null;
		$archivo['LiquidacionIntercambio']['proveedor_razon_social_resumida'] = null;
                if($archivo['LiquidacionIntercambio']['recibo_id'] > 0)	$archivo['LiquidacionIntercambio']['recibo_link'] = $this->getReciboLink($archivo['LiquidacionIntercambio']['recibo_id']);
		if($archivo['LiquidacionIntercambio']['proveedor_id'] != 0){
			App::import('Model','proveedores.Proveedor');
			$oPROV = new Proveedor();
			$ret = $oPROV->getRazonSocialAndRazonSocialResumida($archivo['LiquidacionIntercambio']['proveedor_id']);
			$archivo['LiquidacionIntercambio']['proveedor_razon_social'] = $ret['razon_social'];
			$archivo['LiquidacionIntercambio']['proveedor_razon_social_resumida'] = $ret['razon_social_resumida'];
		}
		//saco los datos de los registros
		App::import('Model','mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();
		$archivo['LiquidacionIntercambio']['total_registros'] = $oLSR->getTotalRegistros($archivo['LiquidacionIntercambio']['id']);
		$archivo['LiquidacionIntercambio']['registros_cobrados'] = $oLSR->getRegistrosCobrados($archivo['LiquidacionIntercambio']['id']);
		$archivo['LiquidacionIntercambio']['importe_cobrado'] = $oLSR->getImporteCobrado($archivo['LiquidacionIntercambio']['id']);
		return $archivo;
	}
	
	
	function getValor($id,$field){
		$registro = $this->read($field,$id);
		return $registro['LiquidacionIntercambio'][$field];		
	}
	
	
	
	function subir($data){
        
		if(!$this->validarFile($data)) return false;
		
		//si no viene el codigo del banco tomar el banco de la mutual
		$data['LiquidacionIntercambio']['banco_id'] = (isset($data['LiquidacionIntercambio']['banco_id']) ? $data['LiquidacionIntercambio']['banco_id'] : 99999);
		
		//armo el nombre del archivo
		$nombre = $data['LiquidacionIntercambio']['periodo'].'_'.$data['LiquidacionIntercambio']['codigo_organismo'];
		$nombre .= '_'.$data['LiquidacionIntercambio']['banco_id'];
		$nombre .= '_'.$data['LiquidacionIntercambio']['archivo']['name'];
		
		$target_path = WWW_ROOT . $this->dir_up . $nombre;
		
		$data['LiquidacionIntercambio']['archivo_file'] = $this->dir_url . $nombre;
		$data['LiquidacionIntercambio']['archivo_nombre'] = $data['LiquidacionIntercambio']['archivo']['name'];
		$data['LiquidacionIntercambio']['target_path'] = $target_path;
		
		if(!move_uploaded_file($data['LiquidacionIntercambio']['archivo']['tmp_name'], $target_path)) return false;

		$data['LiquidacionIntercambio']['lote'] = file_get_contents($target_path);
                $data['LiquidacionIntercambio']['total_registros'] = count(file($target_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
		
		if(!$this->save($data))return false;
		
		return $this->getLastInsertID();
		
	}
    
    
    function generarLote($liquidacionId,$periodo,$codigoOrganismo,$bancoId,$fileName,$rows){
        
        $data = array();
        
        $data['LiquidacionIntercambio']['id'] = 0;
        $data['LiquidacionIntercambio']['banco_id'] = $bancoId;
        $data['LiquidacionIntercambio']['liquidacion_id'] = $liquidacionId;
        $data['LiquidacionIntercambio']['periodo'] = $periodo;
        $data['LiquidacionIntercambio']['codigo_organismo'] = $codigoOrganismo;
        
		//armo el nombre del archivo
		$nombre = $data['LiquidacionIntercambio']['periodo'].'_'.$data['LiquidacionIntercambio']['codigo_organismo'];
		$nombre .= '_'.$data['LiquidacionIntercambio']['banco_id'];
		$nombre .= '_'.$fileName;
		
		$target_path = WWW_ROOT . $this->dir_up . $nombre;
		
		$data['LiquidacionIntercambio']['archivo_file'] = $this->dir_url . $nombre;
		$data['LiquidacionIntercambio']['archivo_nombre'] = $fileName;
		$data['LiquidacionIntercambio']['target_path'] = $target_path; 
        $data['LiquidacionIntercambio']['lote'] = implode("\r\n", $rows);
        
        file_put_contents($target_path, $data['LiquidacionIntercambio']['lote'], LOCK_EX);
        
		if(!$this->save($data))return false;
		return true;        
    }
    
    
	
	function subirAndProcesar($data){
		
		if(!$this->validarFile($data)) return false;
		
//		if($data['LiquidacionIntercambio']['archivo']['error'] != 0 || $data['LiquidacionIntercambio']['archivo']['type'] != 'text/plain'){
//			return false;
//		}
		
		//armo el nombre del archivo
		$nombre = $data['LiquidacionIntercambio']['periodo'].'_'.$data['LiquidacionIntercambio']['codigo_organismo'];
		$nombre .= '_'.(isset($data['LiquidacionIntercambio']['banco_id']) ? $data['LiquidacionIntercambio']['banco_id'] : 'XXXXX');
		$nombre .= '_'.$data['LiquidacionIntercambio']['archivo']['name'];
		
		$target_path = WWW_ROOT . $this->dir_up . $nombre;
		
		$data['LiquidacionIntercambio']['archivo_file'] = $this->dir_url . $nombre;
		$data['LiquidacionIntercambio']['archivo_nombre'] = $data['LiquidacionIntercambio']['archivo']['name'];
		$data['LiquidacionIntercambio']['target_path'] = $target_path;
		
		if(!move_uploaded_file($data['LiquidacionIntercambio']['archivo']['tmp_name'], $target_path)) return false;

		if(!$this->save($data))return false;
		
		App::import('Model','Mutual.LiquidacionIntercambioRegistro');
		$oFile = new LiquidacionIntercambioRegistro();	

		$oFile->generarDetalle($this->getLastInsertID(),$target_path);
		
		return true;
		
		
	}
	
	/**
	 * 
	 * @param unknown_type $liquidacion_id
	 * @deprecated
	 */
	function borrarIntercambioByLiquidacion($liquidacion_id){
		App::import('Model','Mutual.LiquidacionIntercambioRegistro');
		$oREGISTROS = new LiquidacionIntercambioRegistro();
		$archivos = $this->find('all',array('conditions' => array('LiquidacionIntercambio.liquidacion_id' => $liquidacion_id)));
		foreach($archivos as $archivo){
			if(file_exists($archivo['LiquidacionIntercambio']['target_path']))unlink($archivo['LiquidacionIntercambio']['target_path']);
			parent::del($archivo['LiquidacionIntercambio']['id'],false);
			$oREGISTROS->deleteAll("LiquidacionIntercambioRegistro.liquidacion_intercambio_id = " . $archivo['LiquidacionIntercambio']['id'],false);
		}
	}
	
	function getReciboLink($recibo_id=null){
		if(empty($recibo_id)) return '';
		$oRecibo = parent::importarModelo("Recibo","clientes");
		$recibo = $oRecibo->read(null, $recibo_id);
		return $recibo['Recibo']['tipo_documento'] . ' - ' . $recibo['Recibo']['sucursal'] . ' - ' . $recibo['Recibo']['nro_recibo'];
	}
	
	
	function validarFile($data){
		$validado = true;
		$archivo = $this->find('count',array('conditions' => array('LiquidacionIntercambio.liquidacion_id' => $data['LiquidacionIntercambio']['liquidacion_id'],'LiquidacionIntercambio.archivo_nombre' => $data['LiquidacionIntercambio']['archivo']['name'])));
		if($archivo != 0){
			$validado = false;
			parent::notificar("EL ARCHIVO <strong>".$data['LiquidacionIntercambio']['archivo']['name'] . "</strong> YA EXISTE!");
		}
		if($data['LiquidacionIntercambio']['archivo']['type'] != 'text/plain' && $data['LiquidacionIntercambio']['archivo']['type'] != 'application/octet-stream'){
			$validado = false;
			parent::notificar("EL ARCHIVO <strong>". $data['LiquidacionIntercambio']['archivo']['name'] . "</strong> NO TIENE UN FORMATO VALIDO [".$data['LiquidacionIntercambio']['archivo']['type']."]");
		}
		if($data['LiquidacionIntercambio']['archivo']['error'] != 0){
			$validado = false;
			parent::notificar("EL ARCHIVO <strong>".$data['LiquidacionIntercambio']['archivo']['name'] . "</strong> NO PUDO SUBIRSE AL SERVIDOR [COD ".$data['LiquidacionIntercambio']['archivo']['error'] ."]");
		}
		if(!is_dir(WWW_ROOT . $this->dir_up)){
			$validado = false;
			parent::notificar("NO EXISTE CARPETA DESTINO EN SERVIDOR");
		}
		return $validado;
	}
	
	
	function borrarArchivo($id){
		$ret = false;
		$archivo = $this->read(null,$id);
		if(empty($archivo)){
			parent::notificar("ARCHIVO INEXISTENTE");
			return false;
		}
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		if(file_exists($archivo['LiquidacionIntercambio']['target_path'])){
			if($oLSR->deleteAllByArchivoIntercambioId($archivo['LiquidacionIntercambio']['id'])){
				if(!parent::del($archivo['LiquidacionIntercambio']['id'],false) && unlink($archivo['LiquidacionIntercambio']['target_path'])){
					parent::notificar("INCONSISTENCIA: ERROR AL BORRAR EL ARCHIVO DE INTERCAMBIO [".$archivo['LiquidacionIntercambio']['archivo_nombre']."]");
					$ret = false;
				}else{
					$ret = true;
				}
			}else{
				parent::notificar("INCONSISTENCIA: ERROR AL BORRAR EL DETALLE DEL ARCHIVO DE INTERCAMBIO [".$archivo['LiquidacionIntercambio']['archivo_nombre']."]");
				$ret = false;
			}
		}else{
			parent::notificar("INCONSISTENCIA: EL ARCHIVO " . $archivo['LiquidacionIntercambio']['archivo_nombre'] . " NO EXISTE");
			$ret = false;
		}
		return $ret;
	}
	
	function borrarArchivosByLiquidacion($liquidacionId){
		$ret = false;
		$archivos = $this->find('all',array('conditions' => array('LiquidacionIntercambio.liquidacion_id' => $liquidacionId), 'fields' => array('LiquidacionIntercambio.id')));
		
		if(empty($archivos)) return $ret;
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		if($oLiq->isBloqueada($liquidacionId)){
			parent::notificar("LA LIQUIDACION #$liquidacionId ESTA BLOQUEADA!");
			return $ret;
		}

		foreach($archivos as $archivo){
			$ret = $this->borrarArchivo($archivo['LiquidacionIntercambio']['id']);
			if(!$ret) break;
		}
		return $ret;
	}
    
    
    function setNoProcesado($liquidacionId){
		return $this->updateAll(
							array(
                                    'LiquidacionIntercambio.procesado' => 0,
                                ),
							array(
								'LiquidacionIntercambio.liquidacion_id' => $liquidacionId,
							)
		);        
    }
    
    function subdividirLotePorLiquidacion($bancoID, $fileName, $fileTmpName, $session) {
        
        if (empty($bancoID) || empty($fileName) || empty($fileTmpName)) {
            return false; // Evitar errores si faltan datos
        }
        
        
        App::import('Model','Config.Banco');
        $oBANCO = new Banco();

        $diskette = array();
        $diskette['banco_intercambio'] = $bancoID;
        $diskette['archivo'] = $fileName;
        $diskette['info_cabecera'] = array();
        $diskette['info_pie'] = array();
        
        if (!is_uploaded_file($fileTmpName)) {
            return false; // Archivo no válido
        }
        
        $registros = $this->leerArchivo($fileTmpName); 
        
        $files = array();
        
        #CREDICOOP
        if($bancoID == '00191'){
            foreach($registros as $cadena){
                $campos = $oBANCO->decodeStringDebitoBancoCredicoop($cadena);
                $liquiID = intval(substr($campos['identificador_debito'],0,3));
                $files[$liquiID] = array();
            }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['info_cabecera'] = array();
                    $diskette_1['info_pie'] = array();
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decodeStringDebitoBancoCredicoop($cadena_2);
                        $idl2 = intval(substr($campos['identificador_debito'],0,3));
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE"))$session->del($UID."_DISKETTE");
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }

        }

        #BANCO GALICIA
        if($bancoID == '00007'){
            $diskette['cabecera'] =  array_shift($registros);
            $diskette['pie'] = array_pop($registros);
            $pie = intval(substr(preg_replace('([^A-Za-z0-9])', '', $diskette['pie']),0,4));
            if($pie != 9999){
                $diskette['pie'] = array_pop($registros);
            }
            $exclude = array(' ','0000','9999');
            foreach($registros as $cadena){
                $ini = substr($cadena,0,4);
                if(!in_array($ini, $exclude)){
                    $campos = $oBANCO->decodeStringDebitoBancoGalicia($cadena);
                    $files[intval($campos['liquidacion_id'])] = array();
                }
            }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();
                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = $diskette['cabecera']. "\r\n";
                    $diskette_1['pie'] = $diskette['pie']. "\r\n";
                    $diskette_1['registros'] = array();
                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decodeStringDebitoBancoGalicia($cadena_2);
//                                $idl2 = intval(substr($campos['ref_univ'],7,5));
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);
                    if($session->check($UID."_DISKETTE"))$session->del($UID."_DISKETTE");
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        }
        ################################################################################
        #BANCO NACION
        ################################################################################
        $BCRA_CODIGOS = array('00011','90011','99960', '91011');
        if(in_array($bancoID, $BCRA_CODIGOS)){

            $diskette['cabecera'] =  array_shift($registros);
            $diskette['pie'] = array_pop($registros);

            foreach($registros as $cadena){
                $campos = $oBANCO->decodeStringDebitoBcoNacion($cadena);
                $files[intval($campos['liquidacion_id'])] = array();
            }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = $diskette['cabecera']. "\r\n";
//                             $diskette_1['pie'] = $diskette['pie']. "\r\n";
                    $diskette_1['pie'] = str_pad($diskette['pie'], strlen($diskette['cabecera']),' ',STR_PAD_RIGHT). "\r\n";

                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decodeStringDebitoBcoNacion($cadena_2);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }

                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE")){$session->del($UID."_DISKETTE");}
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        }

        #BANCO CORDOBA
        if($bancoID == '00020'){
            foreach($registros as $cadena) {
                $idl = $oBANCO->get_cba_liquidacion($cadena);
                $files[intval($idl)] = array();
            }

            if(!empty($files)) {

                ksort($files);
                foreach ($files as $idl => $cadena_1) {

                    $linea = 1;
                    $files[$idl] = array();                            

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;

                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $idl2 = $oBANCO->get_cba_liquidacion($cadena_2);
                        $idl2 = intval($idl2);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    } 
                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE")){$session->del($UID."_DISKETTE");}
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;                            

                }
            }
        }

        #################################################################################
        #BANCO RIO
        #################################################################################
        $BCRA_CODIGOS = array('00072','90072','99072');
        if(in_array($bancoID, $BCRA_CODIGOS)){

        // if($this->data['LiquidacionIntercambio']['banco_id'] == '00072'){
            $diskette['cabecera'] =  array_shift($registros);
            foreach($registros as $cadena){
                $campos = $oBANCO->decodeStringDebitoBancoSantanderRio($cadena);
                $files[intval(substr($campos['id_univ'],0,5))] = array();
            }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = $diskette['cabecera'] . "\r\n";
                    $diskette_1['pie'] = array();
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decodeStringDebitoBancoSantanderRio($cadena_2);
                        $idl2 = intval(substr($campos['id_univ'],0,5));
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE"))$session->del($UID."_DISKETTE");
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        }
        #STANDARBANK
        if($this->data['LiquidacionIntercambio']['banco_id'] == '00430'){

        }
        #####################################################################
        # BANCO COMAFI
        #####################################################################
        if($bancoID == '00299'){
            $diskette['cabecera'] =  array();

            foreach($registros as $cadena){

                $campos = $oBANCO->decode_str_debito_banco_comafi($cadena);
                $codigosTransaccion = array(52, 70);
                if(in_array($campos['codigo_transaccion'], $codigosTransaccion)) {$files[intval($campos['liquidacion_id'])] = array();}
            }

            if(!empty($files)){

                ksort($files);

                foreach ($files as $idl => $cadena_1){

                    $linea = 1;

                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = array();
                    $diskette_1['pie'] = array();
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decode_str_debito_banco_comafi($cadena_2);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            $codigosTransaccion = array(52, 65, 70);
                            if( in_array($campos['codigo_transaccion'], $codigosTransaccion)) {array_push($diskette_1['registros'], $cadena_2 . "\r\n");}
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE"))$session->del($UID."_DISKETTE");
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }

            }
        }

        ##################################################################################
        # BANCO DE COMERCIO
        ##################################################################################
        if($bancoID == '00300'){
            $diskette['cabecera'] =  array();
            foreach($registros as $cadena){
                $campos = $oBANCO->decode_str_debito_banco_comercial($cadena);
                $files[intval($campos['liquidacion_id'])] = array();
            }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = array();
                    $diskette_1['pie'] = array();
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decode_str_debito_banco_comercial($cadena_2);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE"))$session->del($UID."_DISKETTE");
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        }

        ##################################################################################
        # ZENRISE
        ##################################################################################
        if($bancoID == '99998'){
            $diskette['cabecera'] =  array();
            foreach($registros as $cadena){
                $campos = $oBANCO->decodeStringDebitoZenrise($cadena);
                $files[intval($campos['liquidacion_id'])] = array();
                                }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = array();
                    $diskette_1['pie'] = array();
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decodeStringDebitoZenrise($cadena_2);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE"))$session->del($UID."_DISKETTE");
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        }

        ##################################################################################
        # BANCO ITAU 00259
        ##################################################################################
        if($bancoID == '00259'){

            $diskette['cabecera'] =  array_shift($registros);
            $diskette['pie'] = array_pop($registros);

            foreach($registros as $cadena){
                $campos = $oBANCO->decode_str_debito_banco_itau($cadena);
                $files[intval($campos['liquidacion_id'])] = array();
            }
//                    exit;
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = $diskette['cabecera']. "\r\n";
                    $diskette_1['pie'] = $diskette['pie']. "\r\n";
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decode_str_debito_banco_itau($cadena_2);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }

                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE")){$session->del($UID."_DISKETTE");}
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        }
        ##################################################################################
        # BANCO FRANCES
        ##################################################################################
        $BCRA_CODIGOS = array('00017','90017','91217','92217','91117','92117','91017','92017');
        if(in_array($bancoID, $BCRA_CODIGOS)){

            $diskette['cabecera'] = array_shift($registros);
            $diskette['pie'] = array_pop($registros);

            $files = array();
            $registroInfo = array(4210);
            foreach($registros as $cadena){
                $campos = $oBANCO->decodeStringDebitoBancoFrances($cadena);
                $codReg = intval($campos['codigo_registro']);
                if(in_array($codReg, $registroInfo)){
                    $files[intval($campos['liquidacion_id'])] = array();
                }
            }

            if(!empty($files)){

                ksort($files);

                $registrosAdicionales = array(4220,4230,4240);

                foreach ($files as $idl => $cadena_1){

                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = $diskette['cabecera']. "\r\n";
                    $diskette_1['pie'] = $diskette['pie']. "\r\n";
                    $diskette_1['registros'] = array();

                    $socioActual = 0;

                    foreach($registros as $i => $cadena_2){

                        $campos = $oBANCO->decodeStringDebitoBancoFrances($cadena_2);

                        $codReg = intval($campos['codigo_registro']);
                        $socioId = intval($campos['socio_id']);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            $socioActual = $socioId;
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }else if(in_array($codReg, $registrosAdicionales) && $socioActual === $socioId){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }else{
                            $socioActual = 0;
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);
                    if($session->check($UID."_DISKETTE")){$session->del($UID."_DISKETTE");}
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        }
        ##################################################################################
        # BANCO COINAG
        ##################################################################################
        if($bancoID == '00431'){
            $diskette['cabecera'] =  array_shift($registros);
            $diskette['pie'] = array_pop($registros);

            foreach($registros as $cadena){
                $campos = $oBANCO->decode_str_debito_coinag($cadena);
                $files[intval($campos['liquidacion_id'])] = array();
                                }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = $diskette['cabecera']. "\r\n";
                    $diskette_1['pie'] = $diskette['pie']. "\r\n";
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decode_str_debito_coinag($cadena_2);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }

                    $diskette_1['lineas'] = count($diskette_1['registros']);

                    if($session->check($UID."_DISKETTE")){$session->del($UID."_DISKETTE");}
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }


        }
        ############################################################################
        #BANCO CRONOCRED
        ############################################################################
        if($bancoID == '99950'){

            $diskette['cabecera'] =  array_shift($registros);
            $diskette['pie'] = array_pop($registros);

            foreach($registros as $cadena){
                $campos = $oBANCO->decodeStringDebitoBcoNacion($cadena);
                $files[intval($campos['liquidacion_id'])] = array();
                                }
            if(!empty($files)){
                ksort($files);
                foreach ($files as $idl => $cadena_1){
                    $linea = 1;
                    $files[$idl] = array();

                    $diskette_1 = array();
                    $diskette_1['uuid'] = $UID = String::uuid();
                    $diskette_1['banco_intercambio'] = $bancoID;
                    $diskette_1['archivo'] = $idl."_".$fileName;
                    $diskette_1['cabecera'] = $diskette['cabecera']. "\r\n";
                    $diskette_1['pie'] = $diskette['pie']. "\r\n";
                    $diskette_1['registros'] = array();

                    foreach($registros as $i => $cadena_2){
                        $campos = $oBANCO->decodeStringDebitoBcoNacion($cadena_2);
                        $idl2 = intval($campos['liquidacion_id']);
                        if($idl2 === $idl){
                            array_push($diskette_1['registros'], $cadena_2 . "\r\n");
                        }
                        $linea++;
                    }
                    $diskette_1['lineas'] = count($diskette_1['registros']);
                    if($session->check($UID."_DISKETTE")){$session->del($UID."_DISKETTE");}
                    $session->write($UID."_DISKETTE",$diskette_1);
                    $files[$idl] = $diskette_1;
                }
            }
        } 
        return $files;
    }    
	
}
?>