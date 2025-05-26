<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class Liquidacion extends MutualAppModel{

	var $name = 'Liquidacion';
	var $tiposLiquidacion = array(0 => '1 - LIQUIDAR MORA MAS PERIODO', 1 => '2 - LIQUIDAR SOLAMENTE EL PERIODO', 2 => '3 - LIQUIDAR SOLAMENTE LA MORA');
	
	
	/**
	 * carga una liquidacion
	 * @param $id
	 * @return array
	 */
	function cargar($id){
		$liquidacion = $this->read(null,$id);
		$glb = parent::getGlobalDato('concepto_1,logico_2',$liquidacion['Liquidacion']['codigo_organismo']);
		$liquidacion['Liquidacion']['mostar_bancos'] = $glb['GlobalDato']['logico_2'];
		return $this->armaDatos($liquidacion);
	}
	
	
	function liquidacionesByPeriodoDesdeHasta($periodo_desde,$periodo_hasta,$cerrada=true,$imputada=true,$organismo=null){
		$conditions = array();
		$periodos = array();
//		if($cerrada) $conditions['Liquidacion.cerrada'] = 1;
////		else $conditions['Liquidacion.cerrada'] = 0;
//		if($imputada) $conditions['Liquidacion.imputada'] = 1;
////		else $conditions['Liquidacion.imputada'] = 0;
//		if(!empty($organismo)) $conditions['Liquidacion.codigo_organismo IN '] = "(".$organismo.")";
//		$conditions['Liquidacion.periodo >='] = $periodo_desde;
//		$conditions['Liquidacion.periodo <='] = $periodo_hasta;
//		$liquidaciones = $this->find('all',array('conditions' => $conditions));
//		$dbo = $this->getDataSource();
//		$querys = $dbo->_queriesLog;
//		debug($querys);
                
                $sql = "SELECT 
                        Liquidacion.*
                        FROM liquidaciones AS Liquidacion 
                        WHERE
                        1 = 1
                        ".($cerrada ? " AND Liquidacion.cerrada = 1 " : "")."
                        ".($imputada ? " AND Liquidacion.imputada = 1 " : "")."
                        ".(!empty($organismo) ? " AND Liquidacion.codigo_organismo IN ($organismo) " : "")."    
                        AND Liquidacion.periodo BETWEEN '$periodo_desde' AND '$periodo_hasta' ";
//                debug($sql);
                $liquidaciones = $this->query($sql);
		if(empty($liquidaciones)) return null;
		foreach($liquidaciones as $i => $liquidacion):
			$liquidaciones[$i] = $this->armaDatos($liquidacion);
		endforeach;
		return $liquidaciones;
		
	}
	

	/**
	 * Arma Datos Adicionales
	 * @param $liquidacion
	 * @return unknown_type
	 */
	function armaDatos($liquidacion){
//            debug($liquidacion);
		$liquidacion['Liquidacion']['organismo'] = parent::GlobalDato('concepto_1',$liquidacion['Liquidacion']['codigo_organismo']);
		$liquidacion['Liquidacion']['periodo_desc'] = parent::periodo($liquidacion['Liquidacion']['periodo']);
		$liquidacion['Liquidacion']['periodo_desc_amp'] = parent::periodo($liquidacion['Liquidacion']['periodo'],true);
		$liquidacion['Liquidacion']['total_importe_dto'] = 0;
                App::import('model','mutual.LiquidacionSocio');
                $oLS = new LiquidacionSocio();
                $liquidacion['Liquidacion']['total_importe_dto'] = $oLS->getTotalEnviadoDto($liquidacion['Liquidacion']['id']);
		//SACO EL IMPORTE COBRADO DE LA LIQUIDACION SOCIO RENDICIONES
		App::import('Model','mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();
		$liquidacion['Liquidacion']['importe_cobrado'] = $oLSR->getTotalByLiquidacion($liquidacion['Liquidacion']['id']);
		return $liquidacion;
	}
	

	/**
	 * Carga las liquidaciones para un periodo 
	 * @author adrian [23/01/2012]
	 * @param $periodo
	 * @param $cerrada
	 * @param $imputada
	 * @param $organismo
	 * @param $excludeBloquedas
	 * @param $excludeEnProceso
	 * @return array
	 */
	function getLiquidacionesByPeriodo($periodo,$cerrada=true,$imputada=true,$organismo=null,$excludeBloquedas = true, $excludeEnProceso = true){
		$conditions = array();
		$conditions['Liquidacion.periodo'] = $periodo;
		if($excludeBloquedas)$conditions['Liquidacion.bloqueada'] = 0;
		if($excludeEnProceso)$conditions['Liquidacion.en_proceso'] = 0;
//		if($cerrada)$conditions['Liquidacion.cerrada'] = 1;
//		if($imputada)$conditions['Liquidacion.imputada'] = $imputada;
                $conditions['Liquidacion.cerrada'] = (empty($cerrada) || !$cerrada ? 0 : 1);
                $conditions['Liquidacion.imputada'] = (empty($imputada) || !$imputada ? 0 : 1);
		$conditions['Liquidacion.codigo_organismo LIKE'] = "%$organismo%";
		$datos = $this->find('all',array('conditions' => $conditions));
		return $datos;
	}
	
	/**
	 * verifica si una liquidacion para un periodo y organismo se encuentra cerrada
	 * @param $organismo
	 * @param $periodo
	 * @return unknown_type
	 */
	function isCerrada($organismo,$periodo){
		$liquidacion = $this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo),'fields'=>array('Liquidacion.cerrada')));
		if(isset($liquidacion[0]['Liquidacion']['cerrada']) && $liquidacion[0]['Liquidacion']['cerrada'] == 1) return true;
		else return false;
	}
	
	/**
	 * arma los datos para presentar en el combo de importacion
	 * @return unknown_type
	 */
	function datosComboImportar(){
		$combo = array();
		$liquidacion = $this->find('all',array('conditions' => array('Liquidacion.cerrada' => 1),'fields'=>array('Liquidacion.id,Liquidacion.codigo_organismo,Liquidacion.periodo')));
		foreach($liquidacion as $dato){
			$glb = parent::getGlobalDato('concepto_1,logico_2',$dato['Liquidacion']['codigo_organismo']);
			$periodo = parent::periodo($dato['Liquidacion']['periodo'],true);
			$key = $dato['Liquidacion']['id'].'|'.$glb['GlobalDato']['logico_2'].'|'.$dato['Liquidacion']['periodo'].'|'.$dato['Liquidacion']['codigo_organismo'];
			$combo[$key] = $periodo .' - ' . $glb['GlobalDato']['concepto_1'];
		}
		return $combo;
	}
	
	/**
	 * devuelve una cadena con los datos de la liquidacion
	 * @param unknown_type $liquidacion_id
	 * @return unknown_type
	 */
	function getDatoLiquidacion($liquidacion_id,$indicaImputada=false){
		$liquidacion = $this->read(null,$liquidacion_id);
		$periodo = parent::periodo($liquidacion['Liquidacion']['periodo']);
		$glb = parent::getGlobalDato('concepto_1',$liquidacion['Liquidacion']['codigo_organismo']);
		$str = $periodo . ' - ' . $glb['GlobalDato']['concepto_1'];
		if($indicaImputada && $liquidacion['Liquidacion']['imputada'] == 1) $str .= " ** IMPUTADA **";
		return $str;
	}
	
	function getPeriodosLiquidados($abiertos=true,$imputados=false,$organismo=null,$orderPeriodo='ASC',$toStr=false){
		$conditions = array();
		$periodos = array();
		if($abiertos) $conditions['Liquidacion.cerrada'] = 0;
//		else $conditions['Liquidacion.cerrada'] = 1;
		if($imputados) $conditions['Liquidacion.imputada'] = 1;
//		else $conditions['Liquidacion.imputada'] = 0;
		if(!empty($organismo)) $conditions['Liquidacion.codigo_organismo'] = $organismo;
                
		$liquidaciones = $this->find('all',array('conditions' => $conditions,'fields' => array('Liquidacion.periodo'),'group' => array('Liquidacion.periodo'),'order' => array("Liquidacion.periodo $orderPeriodo")));
		$liquidaciones = Set::extract('/Liquidacion/periodo',$liquidaciones);
		if(!empty($liquidaciones)):
			foreach($liquidaciones as $periodo):
				if($toStr)$periodos[$periodo] = parent::periodo($periodo,true);
				else $periodos[$periodo] = $periodo;
			endforeach;
		endif;
		return $periodos;
	}
	
	/**
	 * Devuelve array asociativo para un render de un combo
	 * @param array $conditions
	 * @return array
	 */
	function datosCombo($conditions=array()){
		$combo = array();
		$liquidacion = $this->find('all',array('conditions' => $conditions,'fields'=>array('Liquidacion.id,Liquidacion.codigo_organismo,Liquidacion.periodo'),'order' => array('Liquidacion.periodo DESC')));
		foreach($liquidacion as $dato){
			$glb = parent::getGlobalDato('concepto_1,logico_2',$dato['Liquidacion']['codigo_organismo']);
			$periodo = parent::periodo($dato['Liquidacion']['periodo'],true);
			$combo[$dato['Liquidacion']['id']] = '#'.$dato['Liquidacion']['id'].' - '.$periodo .' - ' . $glb['GlobalDato']['concepto_1'];
		}
		return $combo;
	}	
	
	function guardarRecibo($datos){
		// Llamo a los modelos a utilizar
		// Recibo Cabecera
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		
		$nReciboId = $oRecibo->guardarRecibo($datos);
		if(!$nReciboId):
			return false;
		endif;
		
		return $nReciboId;
		
	}
	
	function getRecibo($id=null){
		if(empty($id)) return array();
		
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		
		return $oRecibo->getRecibo($id);
	}
	
	function anularRecibo($datos){
		
		// Llamo al modelo a utilizar
		// Recibo Cabecera
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		
		if(!$oRecibo->anularRecibo($datos)) return false;
		
		return true;
		
	}

	function getReciboLink($recibo_id=null){
		if(empty($recibo_id)) return '';
		$oRecibo = parent::importarModelo("Recibo","clientes");
		$recibo = $oRecibo->read(null, $recibo_id);
		return $recibo['Recibo']['tipo_documento'] . ' - ' . $recibo['Recibo']['sucursal'] . ' - ' . $recibo['Recibo']['nro_recibo'];
	}
	
	
	function grabarFacturas($datos){
		$ok = true;
		$oLiq = $this->importarModelo('Liquidacion','mutual');
		$liquidacion = $oLiq->cargar($datos['Liquidacion']['id']);

		$oListado = $this->importarModelo('ListadoService','mutual');
		$proveedores = $oListado->getTemporalFacturas($datos['Liquidacion']['pid'],$datos['Liquidacion']['id']);

		$oProveedorFactura = $this->importarModelo('ProveedorFactura','proveedores');

		$oClienteFactura = $this->importarModelo('ClienteFactura','clientes');
		
		$proveedores = $oListado->getTemporalFacturas($datos['Liquidacion']['pid'],$datos['Liquidacion']['id']);

		$liquidacion['Liquidacion']['facturada'] = 1;
		if(!$this->save($liquidacion)):
			return false;
		endif;
		
		foreach($proveedores as $factura):
			if($factura['tipo'] == 'E'):
				if(!$oProveedorFactura->grabarFacturaLiquidacion($factura, $liquidacion)):
					$ok = false;
				endif;
			else:
				if(!$oClienteFactura->grabarFacturaLiquidacion($factura, $liquidacion)):
					$ok = false;
				endif;
			endif;
		endforeach;
		
		
		return $ok;
	}
	
	/**
	 * Devuelve el ultimo periodo cerrado. Si le paso un offset le suma n meses al periodo devuelto
	 * 
	 * @author adrian [14/03/2012]
	 * @param unknown_type $organismo
	 * @param unknown_type $offSet
	 */
	function getUltimoPeriodoCerrado($organismo,$mesesOffSet=0){
		$periodo = null;
		$liquidacion = $this->find('all',array('conditions' => array('Liquidacion.codigo_organismo' => $organismo,'Liquidacion.cerrada' => 1),'fields'=>array('Liquidacion.periodo'),'order' => array('Liquidacion.periodo DESC'), 'limit' => 1));
                if(empty($liquidacion) || $organismo == 'MUTUCORGMUTU'){
			//SI NO TIENE ULTIMO PERIODO (PARA EL CASO DE LOS CAJA AMAN) TOMO DE LOS ULTIMOS 3 IMPUTADOS EL MENOR 
			$liquidacion = $this->find('all',array('conditions' => array('Liquidacion.imputada' => 1),'fields'=>array('Liquidacion.periodo'),'order' => array('Liquidacion.periodo DESC'), 'limit' => 3));
			$periodo = (!empty($liquidacion) ? $liquidacion[0]['Liquidacion']['periodo'] : date('Ym'));
		}else{
			$periodo = $liquidacion[0]['Liquidacion']['periodo'];
		}
		
		if($mesesOffSet == 0) return $periodo;
		//$periodo = date('Ym',$this->addMonthToDate(mktime(0,0,0,substr($periodo,4,2),1,substr($periodo,0,4)),$mesesOffSet));
		return $periodo;
	}	
	
	
	/**
	 * Verifica que una liquidacion no tenga liquidaciones posteriores imputadas
	 * para generar el recupero de cartera
	 * @param unknown_type $id
	 */
	function isCarteraRecuperable($id){
		
		$liquidacion = $this->read(null,$id);
		
		$conditions = array();
		$conditions['Liquidacion.codigo_organismo'] = $liquidacion['Liquidacion']['codigo_organismo'];
		$conditions['Liquidacion.periodo >'] = $liquidacion['Liquidacion']['periodo'];
		$conditions['Liquidacion.imputada'] = 1;
		
		$posteriores = $this->find('count',array('conditions' => $conditions));
		if($posteriores === 0) return true;
		else return false;
		
	}
	
	/**
	 * verifica si la liquidacion esta bloqueada por algun usuario
	 * @param int $id
	 * @return boolean
	 */
	function isBloqueada($id){
		$liquidacion = $this->read('bloqueada',$id);
		if($liquidacion['Liquidacion']['bloqueada'] == 1) return true;
		else return false;
	}
	
	/**
	 * Verifica si una liquidacion esta cerrada
	 * @param unknown_type $id
	 */
	function isCerradaById($id){
		$liquidacion = $this->read('cerrada',$id);
		if($liquidacion['Liquidacion']['cerrada'] == 1) return true;
		else return false;
	}	
	
	/**
	 * Verifica si una liquidacion para un periodo / organismo esta imputada 
	 * 
	 * @author adrian [01/02/2012]
	 * @param string $organismo
	 * @param string $periodo
	 * @return boolean
	 */
	function isImputada($organismo,$periodo){
            $ultimoPeriodoImputado = $this->getUltimoPeriodoImputado($organismo);
            if ($periodo <= $ultimoPeriodoImputado) {
                return true;
            } else {
                return false;
            }
        }

	/**
	 * Devuelve el ultimo periodo imputado para un organismo
	 * 
	 * @author adrian [01/02/2012]
	 * @param string $organismo
	 * @return string
	 */
	function getUltimoPeriodoImputado($organismo = NULL){
            if(empty($organismo)){
                $sql = "select periodo from liquidaciones as Liquidacion where imputada = 1 group by periodo order by periodo desc LIMIT 1;";
                $liquidacion = $this->query($sql);
                if(!empty($liquidacion[0]['Liquidacion']['periodo'])){
                    return $liquidacion[0]['Liquidacion']['periodo'];
                }else{
                    return NULL;
                }
            }else{
                $liquidacion = $this->find('all',array('conditions' => array('Liquidacion.codigo_organismo' => $organismo,'Liquidacion.imputada' => 1),'fields'=>array('Liquidacion.periodo'),'order' => array('Liquidacion.periodo DESC'), 'limit' => 1));
                if (!empty($liquidacion[0]['Liquidacion']['periodo'])) {
                    return $liquidacion[0]['Liquidacion']['periodo'];
                } else {
                    return null;
                }
            }
	}	
    
    
	function getUltimoPeriodoLiquidado($organismo){
            $liquidacion = $this->find('all',array('conditions' => array('Liquidacion.codigo_organismo' => $organismo),'fields'=>array('Liquidacion.periodo'),'order' => array('Liquidacion.periodo DESC'), 'limit' => 1));
            if(!empty($liquidacion[0]['Liquidacion']['periodo']))return $liquidacion[0]['Liquidacion']['periodo'];
            else return null;
	}    
	

	/**
	 * Bloquea una liquidacion.
	 * 
	 * @author adrian [03/02/2012]
	 * @param int $id
	 * @param int $asincrono_id
	 * @return boolean
	 */
	function bloquear($id,$asincrono_id = null){
		$this->id = $id;
		if(!parent::saveField("bloqueada", 1)) return false;
		return $this->updateAll(array('Liquidacion.bloqueada' => 1, 'Liquidacion.asincrono_id' => (!empty($asincrono_id) ? $asincrono_id : 0)),array('Liquidacion.id' => $id));
	}
	
	/**
	 * Desbloquea una liquidacion
	 * 
	 * @author adrian [03/02/2012]
	 * @param int $id
	 * @return boolean
	 */
	function desbloquear($id){
		$this->id = $id;
		return $this->updateAll(array('Liquidacion.bloqueada' => 0, 'Liquidacion.asincrono_id' => 0),array('Liquidacion.id' => $id));
	}
	
	/**
	 * Devuelve el id del asincrono que la tiene bloqueada
	 * 
	 * @author adrian [28/03/2012]
	 * @param unknown_type $id
	 */
	function getBloqueoPID($id){
		$liq = $this->read('asincrono_id',$id);
		return $liq['Liquidacion']['asincrono_id'];
	}
	

	/**
	 * Cierra una liquidacion (siempre que no este bloqueada)
	 * 
	 * @author adrian [03/02/2012]
	 * @param int $id
	 * @return boolean
	 */
	function cerrar($id){
		$this->id = $id;
		if($this->isBloqueada($id)) return false;
		return $this->updateAll(array('Liquidacion.cerrada' => 1),array('Liquidacion.id' => $id));
	}
	
	/**
	 * Abre una liquidacion (mientras no este bloqueada)
	 * 
	 * @author adrian [03/02/2012]
	 * @param int $id
	 * @return boolean
	 */
	function abrir($id){
		$this->id = $id;
		if($this->isBloqueada($id)) return false;
		return $this->updateAll(array('Liquidacion.cerrada' => 0),array('Liquidacion.id' => $id));
	}
	
	/**
	 * MARCA COMO IMPUTADA UNA LIQUIDACION
	 * 
	 * @author adrian [28/03/2012]
	 * @param integer $id
	 */
	function setImputada($id, $fechaImputacion = null, $nro_recibo = null){
            $this->id = $id;
            $fechaImputacion = (empty($fechaImputacion) ? date('Y-m-d') : date('Y-m-d', strtotime($fechaImputacion)));
            return $this->updateAll(array('Liquidacion.sobre_pre_imputacion' => 0,'Liquidacion.cerrada' => 1, 'Liquidacion.imputada' => 1,'Liquidacion.scoring' => 1, 'Liquidacion.nro_recibo' => "'".$nro_recibo."'" ,'Liquidacion.fecha_imputacion' => "'".$fechaImputacion."'"),array('Liquidacion.id' => $id));
	}
	
	
	function grabarFacturaLiquidacion($datos){
            $ok = true;
            $oLiq = $this->importarModelo('Liquidacion','mutual');
            $liquidacion = $oLiq->cargar($datos['Liquidacion']['id']);

            $oListado = $this->importarModelo('ListadoService','mutual');
//		$proveedores = $oListado->getTemporalFacturaLiquidacion($datos['Liquidacion']['pid'],$datos['Liquidacion']['id']);

            $oProveedor = $this->importarModelo('Proveedor', 'proveedores');

            $oProveedorFactura = $this->importarModelo('ProveedorFactura','proveedores');

            $oClienteFactura = $this->importarModelo('ClienteFactura','clientes');

            $this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');

            $proveedores = $oListado->getTemporalFacturaLiquidacion($datos['Liquidacion']['pid'],$datos['Liquidacion']['id']);

            $liquidacion['Liquidacion']['facturada'] = 1;
            if(!$this->save($liquidacion)):
                return false;
            endif;

            foreach($proveedores as $factura):
                $aProveedor = $oProveedor->getProveedor($factura['proveedor_id']);

                $factura['proveedor_factura_id'] = $oProveedorFactura->grabarFacturaLiquidacion($factura, $liquidacion);
                if(!$factura['proveedor_factura_id']):
                    $ok = false;
                endif;

                if($factura['importe_cliente'] > 0 && $aProveedor['Proveedor']['cliente_id'] > 0){
                    $factura['cliente_factura_id'] = $oClienteFactura->grabarFacturaLiquidacion($factura, $liquidacion);
                    if(!$factura['cliente_factura_id']){
                        $ok = false;
                    }
                }

/*
 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
 * esta funcion queda obsoleta.
                if(!$this->ProveedorLiquidacion->grabarLiquidacion($factura, $liquidacion)):
                    $ok = false;
                endif;
 * 
 */
            endforeach;

            return $ok;
	}
	
	/**
	 * 
	 * 
	 * @author adrian [29/02/2012]
	 * @param unknown_type $id
	 */
	function getCodigoOrganismo($id){
		$liq = $this->read('codigo_organismo',$id);
		return $liq['Liquidacion']['codigo_organismo'];
	}
	
	/**
	 * 
	 * 
	 * @author adrian [29/02/2012]
	 * @param unknown_type $id
	 */
	function getPeriodo($id){
		$liq = $this->read('periodo',$id);
		return $liq['Liquidacion']['periodo'];
	}	
	
	/**
	 * 
	 * 
	 * @author adrian [29/02/2012]
	 * @param unknown_type $id
	 */
	function getFechaImputacion($id){
		$liq = $this->read('fecha_imputacion',$id);
		return $liq['Liquidacion']['fecha_imputacion'];
	}	
	
	/**
	 * Devuelve el ultimo ID de liquidacion imputada
	 * 
	 * @author adrian [01/03/2012]
	 * @param unknown_type $organismo
	 */
	function getUltimaLiquidacionImputada($organismo){
		$liquidacion = $this->find('all',array('conditions' => array('Liquidacion.codigo_organismo' => $organismo,'Liquidacion.imputada' => 1),'fields'=>array('Liquidacion.id'),'order' => array('Liquidacion.periodo DESC'), 'limit' => 1));
		if(!empty($liquidacion[0]['Liquidacion']['id']))return $liquidacion[0]['Liquidacion']['id'];
		else return null;
	}	
	
	
	function generarPeriodosDesdeUltimoCerrado($organismo,$cantidadPeriodos = 18,$periodoMinimo = NULL){
		$periodos = array();
		if(!empty($periodoMinimo)){
			$ultimoPeriodo = $periodoMinimo;
		}else{
			$ultimoPeriodo = $this->getUltimoPeriodoCerrado($organismo,1);
		}		
		$periodo = $periodoNew = $ultimoPeriodo;
                // debug($periodo);
                // debug($periodoNew);
                // debug($cantidadPeriodos);
                
		
		for ($i = 0; $i <= $cantidadPeriodos; $i++) {

			$periodos[$periodoNew] = parent::periodo($periodoNew,true);
			$periodoNew = date('Ym',$this->addMonthToDate(mktime(0,0,0,substr($periodo,4,2),1,substr($periodo,0,4)),$i));
                        // debug($i . " - " . $periodoNew . " - " . $periodo);
			
		}
                // exit;
		return $periodos;
	}
	
	/**
	 * Setea los totales de la liquidacion en la cabecera
	 * 
	 * @author adrian [26/03/2012]
	 * @param unknown_type $id
	 */
	function setTotales($id){
		
		$liquidacion = $this->read(null,$id);
		
		App::import('Model','mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();	
		
		App::import('Model','mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();	

		
		$liquidacion['Liquidacion']['cuota_social_vencida'] = $oLC->getTotalCuotaSocial($id,1);
		$liquidacion['Liquidacion']['cuota_social_periodo'] = $oLC->getTotalCuotaSocial($id,0);

		$liquidacion['Liquidacion']['deuda_vencida'] = $oLC->getTotalDeuda($id,1);
		$liquidacion['Liquidacion']['deuda_periodo'] = $oLC->getTotalDeuda($id,0);	

		$liquidacion['Liquidacion']['total_vencido'] = $oLC->getTotalVencido($id,1);
		$liquidacion['Liquidacion']['total_periodo'] = $oLC->getTotalVencido($id,0);
		$liquidacion['Liquidacion']['total'] = $oLC->getTotal($id);
		$liquidacion['Liquidacion']['en_proceso'] = 0;	
		
		$liquidacion['Liquidacion']['registros_enviados'] = $oLS->getTotalRegistros($id);
		$liquidacion['Liquidacion']['altas'] = $oLS->getTotalAltas($id,$liquidacion['Liquidacion']['codigo_organismo']);

		return $this->save($liquidacion);
		
	}
	
	function isLiquidadaSobrePreImputacionAnterior($periodo){
		$liquidacion = $this->find('count',array('conditions' => array('Liquidacion.periodo' => $periodo,'Liquidacion.sobre_pre_imputacion' => 1)));
		if($liquidacion != 0)return true;
		else return false;
	}

	function isArchivosGenerados($organismo,$periodo){
		$sql = "select count(*) as cant from liquidacion_socio_envios, liquidaciones
				where liquidaciones.id = liquidacion_socio_envios.liquidacion_id
				and liquidaciones.codigo_organismo = '$organismo'
				and liquidaciones.periodo = '$periodo';";
		$cant = $this->query($sql);
		if(!empty($cant) && $cant[0][0]['cant'] != 0) return true;
		else return false;
	}
	
	
	function getPeriodosFacturados($facturados=true,$organismo=null,$orderPeriodo='ASC',$toStr=false){
		$conditions = array();
		$periodos = array();
		if($facturados) $conditions['Liquidacion.facturada'] = 1;
		else $conditions['Liquidacion.facturada'] = 0;
		if(!empty($organismo)) $conditions['Liquidacion.codigo_organismo'] = $organismo;
                
		$liquidaciones = $this->find('all',array('conditions' => $conditions,'fields' => array('Liquidacion.periodo'),'group' => array('Liquidacion.periodo'),'order' => array("Liquidacion.periodo $orderPeriodo")));
		$liquidaciones = Set::extract('/Liquidacion/periodo',$liquidaciones);
		if(!empty($liquidaciones)):
			foreach($liquidaciones as $periodo):
				if($toStr)$periodos[$periodo] = parent::periodo($periodo,true);
				else $periodos[$periodo] = $periodo;
			endforeach;
		endif;
		return $periodos;
	}
    
    function cargarTotalesScoring($liquidacion_id){
        
        $sql = "select count(socio_id) as cantidad_socios, sum(`13`) as `13`,round((sum(`13`)/sum(saldo_actual)) * 100,2) as porc_13
                ,sum(`12`) as `12`,round((sum(`12`)/sum(saldo_actual)) * 100,2) as porc_12,
                sum(`09`) as `09`,round((sum(`09`)/sum(saldo_actual)) * 100,2) as porc_09,
                sum(`06`) as `06`,round((sum(`06`)/sum(saldo_actual)) * 100,2) as porc_06,
                sum(`03`) as `03`,round((sum(`03`)/sum(saldo_actual)) * 100,2) as porc_03,
                sum(`00`) as `00`,round((sum(`00`)/sum(saldo_actual)) * 100,2) as porc_00,
                sum(cargos_adicionales) as cargos_adicionales,
                sum(`13`+ `12`+ `09`+ `06`+ `03`+ `00`+cargos_adicionales) as total,
                sum(saldo_actual) as saldo_actual
                from liquidacion_socio_scores  
                where liquidacion_id = $liquidacion_id";
        $datos = $this->query($sql);
        return (!empty($datos) ? $datos[0][0] : null);

    }
    
    function cargarHistorico($liquidacion_id){
        // $sql = "select l.periodo,sum(lc.importe) as importe,sum(lc.saldo_actual) as saldo_actual,sum(importe_debitado) as importe_debitado from liquidacion_cuotas lc
        //         inner join liquidaciones l on (l.id = lc.liquidacion_id)
        //         where l.codigo_organismo = (select codigo_organismo from
        //         liquidaciones where id = $liquidacion_id)
        //         and l.id <> $liquidacion_id and l.imputada = 1   
        //         group by l.periodo order by l.periodo desc limit 12";

		$sql = "select l.periodo,ifnull(l.total,0) as saldo_actual,ifnull(l.importe_imputado,0) as importe_debitado 
		from liquidaciones l
		where l.codigo_organismo = (select codigo_organismo from
		liquidaciones where id = $liquidacion_id) and
		l.periodo between date_sub(date_format(concat((select periodo from
		liquidaciones where id = $liquidacion_id),'01'),'%Y-%m-%d'),interval 12 month) and l.periodo order by l.periodo desc limit 12";
				
        $datos = $this->query($sql);
        return (!empty($datos) ? $datos : null);        
    }


	function cargarScoresHistorico($liquidacion_id){
        $sql = "select trim(Liquidacion.periodo) periodo, CEILING(avg(ifnull(LiquidacionSocioScore.score,0))) score 
		from liquidacion_socio_scores LiquidacionSocioScore 
		inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionSocioScore.liquidacion_id)
		where Liquidacion.codigo_organismo = (select codigo_organismo from
		liquidaciones where id = $liquidacion_id) and liquidacion_id <> $liquidacion_id group by Liquidacion.periodo order by Liquidacion.periodo ASC";
        $datos = $this->query($sql);
        return (!empty($datos) ? $datos : null);  
	}
    
    
    function cargarScoringByRango($liquidacion_id){
        $sql = "select Persona.documento,Persona.apellido,Persona.nombre,LiquidacionSocioScore.* from liquidacion_socio_scores as LiquidacionSocioScore
                inner join socios Socio on (Socio.id = LiquidacionSocioScore.socio_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                where liquidacion_id = $liquidacion_id
                order by Persona.apellido,Persona.nombre; ";
        $datos = $this->query($sql);
        return (!empty($datos) ? $datos : null);
    }
    
    function reset_valores($id){
        $liquidacion = $this->read(null,$id);
        $liquidacion['Liquidacion']['registros_recibidos'] = 0;
        $liquidacion['Liquidacion']['importe_cobrado'] = 0;
        $liquidacion['Liquidacion']['importe_no_cobrado'] = 0;
        $liquidacion['Liquidacion']['importe_imputado'] = 0;
        $liquidacion['Liquidacion']['importe_reintegro'] = 0;
        $liquidacion['Liquidacion']['archivos_procesados'] = 0;
        $liquidacion['Liquidacion']['fecha_imputacion'] = null;
        $liquidacion['Liquidacion']['nro_recibo'] = null;
        return $this->save($liquidacion);
    }

  
    function getLiquidaciones($iniFilecCtrl = true){
        
        
        $sql = "";
        
				
		/**
		 * Para las liquidaciones NO IMPUTADAS recalulo los totales en base al detalle de la liquiacion_cuotas
		 * Si la liquidación esta IMPUTADA tomo la info de la cabecera
		 */
		$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
		if(isset($INI_FILE['general']['consulta_liquidaciones_totalizada']) && $INI_FILE['general']['consulta_liquidaciones_totalizada'] == 1 && $iniFilecCtrl){

			// $sql = "select Liquidacion.*
			//         ,CodigoOrganismo.concepto_1 
			//         ,ifnull((select sum(importe_adebitar) from liquidacion_socios where liquidacion_id = Liquidacion.id and diskette = 1),0) as importe_dto
			//         ,ifnull((select sum(importe) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as importe_original
			//         ,ifnull((select sum(saldo_actual) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as saldo_actual
			//         ,ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as imputado
			//         ,ifnull((select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_id = Liquidacion.id and indica_pago = 1),0) as importe_debitado
			//         ,ifnull((select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_id = Liquidacion.id and indica_pago = 0),0) as importe_nodebitado
			//         ,ifnull((select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_id = Liquidacion.id and indica_pago = 1),0) 
			//         - ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as reintegros
			//         from liquidaciones as Liquidacion
			//         inner join global_datos CodigoOrganismo on CodigoOrganismo.id = Liquidacion.codigo_organismo
			//         where Liquidacion.en_proceso = 0
			// 		order by Liquidacion.periodo DESC, CodigoOrganismo.concepto_1;";


			$sql = " select * from (select Liquidacion.*
					,CodigoOrganismo.concepto_1 
					,ifnull((select sum(importe_adebitar) from liquidacion_socios where liquidacion_id = Liquidacion.id and diskette = 1),0) as importe_dto
					,ifnull((select sum(importe) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as importe_original
					,ifnull((select sum(saldo_actual) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as saldo_actual
					,ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as imputado
					,ifnull((select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_id = Liquidacion.id and indica_pago = 1),0) as importe_debitado
					,ifnull((select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_id = Liquidacion.id and indica_pago = 0),0) as importe_nodebitado
					,ifnull((select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_id = Liquidacion.id and indica_pago = 1),0) 
					- ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_id = Liquidacion.id),0) as reintegros
					from liquidaciones as Liquidacion
					inner join global_datos CodigoOrganismo on CodigoOrganismo.id = Liquidacion.codigo_organismo
					where Liquidacion.en_proceso = 0 and Liquidacion.imputada = 0
					union
					select Liquidacion.*
					,CodigoOrganismo.concepto_1 
					,Liquidacion.total as importe_dto
					,Liquidacion.total as importe_original
					,Liquidacion.total as saldo_actual
					,Liquidacion.importe_imputado as importe_debitado
					,Liquidacion.importe_cobrado as importe_debitado
					,Liquidacion.importe_no_cobrado as importe_nodebitado
					,Liquidacion.importe_cobrado - Liquidacion.importe_imputado as reintegros
					from liquidaciones as Liquidacion
					inner join global_datos CodigoOrganismo on CodigoOrganismo.id = Liquidacion.codigo_organismo
					where Liquidacion.en_proceso = 0 and Liquidacion.imputada = 1) Liquidacion
					order by Liquidacion.periodo DESC, Liquidacion.concepto_1;";
					return $this->query($sql);

		}else{

			return $this->find('all',array('conditions' => array('Liquidacion.en_proceso' => 0),'order'=>array('Liquidacion.periodo DESC','Liquidacion.codigo_organismo ASC')));

		}

    }
    
    
    function unSetImputada($id, $fechaImputacion = null, $nro_recibo = null){
            $this->id = $id;
            $fechaImputacion = (empty($fechaImputacion) ? date('Y-m-d') : date('Y-m-d', strtotime($fechaImputacion)));
            return $this->updateAll(array('Liquidacion.cerrada' => 0, 'Liquidacion.imputada' => 0, 'Liquidacion.nro_recibo' => NULL ,'Liquidacion.fecha_imputacion' => NULL),array('Liquidacion.id' => $id));
    }     
    
    

    function cargarFacturado($proveedores, $id, $facturada){

        $oProveedor = $this->importarModelo('Proveedor','proveedores');
        $oPrvFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
        $oOPFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
        $oCliente = $this->importarModelo('Cliente', 'clientes');
        $oCltFactura = $this->importarModelo('ClienteFactura', 'clientes');
        $oRcbFactura = $this->importarModelo('ReciboFactura', 'clientes');
        
        $aPrvReturn = array();
        foreach($proveedores as $dato){
            $aProveedor = $oProveedor->getProveedor($dato['proveedor_id']);
            $dato['cuit'] = $aProveedor['Proveedor']['cuit'];
            $dato['cliente_id'] = (empty($aProveedor['Proveedor']['cliente_id']) ? 0 : $aProveedor['Proveedor']['cliente_id']);
            if($dato['cliente_id'] === 0){
                $aCliente = $oCliente->traerClienteXCuit($aProveedor['Proveedor']['cuit']);
                $dato['cliente_id'] = $aCliente[0]['Cliente']['id'];
            }
            $dato['tdoc'] = '';
            $dato['fecha'] = '';
            $dato['comprobante'] = '';
            $dato['facturado'] = 0.00;
            $dato['proveedor_factura_id'] = 0;
            $dato['cliente_factura_id'] = 0;

            if($dato['tipo'] === 'E'){
                $aFctProv = $oPrvFactura->find('all',array('conditions' => array('ProveedorFactura.proveedor_id' => $dato['proveedor_id'], 'ProveedorFactura.liquidacion_id' => $id)));
                $aProvFct = $oPrvFactura->getFactura($aFctProv[0]['ProveedorFactura']['id']);
                $dato['tdoc'] = $aProvFct['tipo'];
                $dato['fecha'] = $aProvFct['fecha_comprobante'];
                $dato['comprobante'] = $aProvFct['tipo_comprobante_desc2'];
                $dato['facturado'] = $aProvFct['total_comprobante'];
                $dato['pagos'] = $aProvFct['pagos'];
                $dato['proveedor_factura_id'] = $aProvFct['id'];
            }
            else{
                $aFctClie = $oCltFactura->find('all',array('conditions' => array('ClienteFactura.cliente_id' => $dato['cliente_id'], 'ClienteFactura.liquidacion_id' => $id)));
                $aClieFct = $oCltFactura->getFactura($aFctClie[0]['ClienteFactura']['id']);
                $dato['tdoc'] = $aClieFct['tipo'];
                $dato['fecha'] = $aClieFct['fecha_comprobante'];
                $dato['comprobante'] = $aClieFct['tipo'] . ' ' . $aClieFct['letra_comprobante'] . ' ' . $aClieFct['punto_venta_comprobante'] . '-' . $aClieFct['numero_comprobante'];
                $dato['facturado'] = $aClieFct['total_comprobante'];
                $dato['pagos'] = $aClieFct['pagos'];
                $dato['cliente_factura_id'] = $aClieFct['id'];
            
            }
            
            array_push($aPrvReturn, $dato);
            
        }
        
        return $aPrvReturn;
    }
    
	function getByPeriodoOrganismo($periodo,$organismo){
		$liquidacion = $this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo)));		
		return (!empty($liquidacion) ? $liquidacion[0] : NULL);
	}    
	
	
	function sp_liquida_sp(){

		$state = array(
			// 'next' => 0,
			'liquidacion_id' => 0,
			'error' => false,
			'error_msg' => ''
		);	
		// $SPCALL = '';
		// // if($proceso == 1){
		// // 	$SPCALL = "call SP_LIQUIDA_CUOTA_SOCIAL('202010','MUTUCORG7701','MUTUPROD0003','MUTUTCUOCSOC','CMUTU','MUTUSICUMUTU',FALSE);";
		// // 	$state['next'] = 2;
		// // }
		// // if($proceso == 2){
		// // 	$SPCALL = "call SP_LIQUIDA_CUOTA_SOCIAL('202010','MUTUCORG7701','MUTUPROD0003','MUTUTCUOCSOC','CMUTU','MUTUSICUMUTU',FALSE);";
		// // 	$state['next'] = 3;
		// // }		
		// if($proceso == 1){
		// 	$SPCALL = "CALL SP_LIQUIDA_DEUDA('202010','MUTUCORG2201',1,FALSE,'MUTUPROD0003','MUTUTCUOCSOC','CMUTU',FALSE);";
		// 	$state['next'] = 0;
		// }
		$SPCALL = "CALL SP_LIQUIDA_DEUDA('202010','MUTUCORG2201',1,FALSE,'MUTUPROD0003','MUTUTCUOCSOC','CMUTU',FALSE);";
		$this->query($SPCALL);
		if(!empty($this->getDataSource()->error)){
			$state['error'] = true;
			$state['error_msg'] = $this->getDataSource()->error;
			return $state;
		}	
		$liquidacion = $this->getByPeriodoOrganismo('202010','MUTUCORG2201');
		$state['liquidacion_id'] = $liquidacion['Liquidacion']['id'];
		return $state;

	}
       
}
?>