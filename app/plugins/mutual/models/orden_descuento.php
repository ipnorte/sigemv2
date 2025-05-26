<?php
/**
 *
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class OrdenDescuento extends MutualAppModel{

	var $name = 'OrdenDescuento';

	var $belongsTo = array('Socio','Proveedor');
	var $hasMany = array('OrdenDescuentoCuota' => array('className' => 'OrdenDescuentoCuota','order' => 'OrdenDescuentoCuota.nro_cuota'));

	function save($data = null, $validate = true, $fieldList = array()){
		$ret = parent::save($data);
		return $ret;
	}

	/**
	 * genera una orden de descuento
	 * @param unknown_type $data
	 * @param unknown_type $situacionCuota
	 * @param unknown_type $estadoCuota
	 * @return unknown_type
	 */
	function generarOrden($data,$situacionCuota="MUTUSICUMUTU",$estadoCuota="A",$transaction=true){

		$ret = true;
		if($transaction)parent::begin();

    	App::import('Model', 'Mutual.OrdenDescuentoCuota');
    	$oCUOTA = new OrdenDescuentoCuota();

    	$situacionCuota = (empty($situacionCuota) ? "MUTUSICUMUTU" : $situacionCuota);
    	$estadoCuota = (empty($estadoCuota) ? "A" : $estadoCuota);


		if($data['OrdenDescuento']['permanente'] == 0){
			$data['OrdenDescuentoCuota'] = $oCUOTA->armaCuotas($data,$situacionCuota,$estadoCuota);
			if(empty($data['OrdenDescuentoCuota'])){
				if($transaction)parent::rollback();
				return false;
			}
			if(!$this->saveAll($data)){
				if($transaction)parent::rollback();
				return false;
			}else{
				if($transaction)parent::commit();
				return true;
			}
		}else{
			if(!$this->save($data)){
				if($transaction)parent::rollback();
				return false;
			}else{
				if($transaction)parent::commit();
				return true;
			}
		}

	}

	/**
	 * @deprecated
	 * VER METODO OrdenesBySocioByEstadoActual
	 * @param unknown_type $socio_id
	 * @param unknown_type $soloVigentes
	 * @param unknown_type $periodoIni
	 * @param unknown_type $activo
	 * @return unknown_type
	 */
	function OrdenesBySocio($socio_id,$soloVigentes=true,$periodoIni=null,$activo=1){
		return $this->OrdenesBySocioByEstadoActual($socio_id,($soloVigentes ? 1 : ($activo == 1 ? 3 : 2)),null,$periodoIni);
	}

	/**
	 * @deprecated
	 * VER METODO OrdenesBySocioByEstadoActual
	 * @param $socio_id
	 * @param $persona_beneficio_id
	 * @param $soloVigentes
	 * @return unknown_type
	 */
	function OrdenesBySocioByBeneficio($socio_id,$persona_beneficio_id,$soloVigentes=true){
		return $this->OrdenesBySocioByEstadoActual($socio_id,($soloVigentes ? 1 : 2),$persona_beneficio_id);
	}

	/**
	 * devuelve las ordenes de un socio segun la situacion de la cuota
	 * @param $socio_id
	 * @param $situacion
	 * @param $estado
	 * @return unknown_type
	 */
	function OrdenesBySocioBySituacion($socio_id, $situacion='', $estado='P'){
		$ordenesFiltradas = array();
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
		$conditions = array();
		$conditions['OrdenDescuento.socio_id'] = $socio_id;
//		$conditions['OrdenDescuento.tipo_orden_dto <>'] = 'CMUTU';
		$ordenes = $this->find('all',array('conditions' => $conditions,'order'=>'OrdenDescuento.periodo_ini DESC,OrdenDescuento.created DESC'));
		$ordenes = $this->armaDatos($ordenes);

		$cuotasFiltradas = array();

		foreach($ordenes as $idx => $orden){

			App::import('model','Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota();
			$oCuota->unbindModel(array('belongsTo' => array('Socio')));
			$cuotas = $oCuota->find('all',array('conditions' => array('OrdenDescuentoCuota.orden_descuento_id' => $orden['OrdenDescuento']['id'],'OrdenDescuentoCuota.situacion LIKE' => $situacion.'%','OrdenDescuentoCuota.estado <>' => $estado)));

			$cuotas = $oCuota->armaInfoAdicional($cuotas);
			$cuotas = Set::extract('/OrdenDescuentoCuota',$cuotas);

			if(count($cuotas) != 0){
				$orden['OrdenDescuentoCuota'] = $cuotas;
				array_push($ordenesFiltradas,$orden);
			}

		}
		return $ordenesFiltradas;
	}


	function getOrden($id,$periodo=null,$calculaSaldos=false,$unbindCuotas=true,$codigoOrganismo = null){
		$ordenes = array();
		if($unbindCuotas)$this->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
		$orden = $this->read(null,$id);
		if(empty($orden)) return null;
		array_push($ordenes,$orden);
//		$calculaSaldos = true;
		$ordenes = $this->armaDatos($ordenes,$calculaSaldos,$periodo,$codigoOrganismo);
		return (isset($ordenes[0]) ? $ordenes[0] : null);
	}
	
	

	

	/**
	 * Tipo y Numero de una orden de descuento. Si el segundo parametro es TRUE devuelve un string con el tipo
	 * y el numero de orden PE: OCOM #512360, EXPTE #512536.
	 * Si es FALSE devuelve un array de 2 elementos [0] => TIPO y [1] => NUMERO
	 * @param integer $id
	 * @param boolean $toString
	 */
	function getTipoAndNro($id,$toString=true){
		$this->unbindModel(array('belongsTo' => array('Socio'),'hasMany' => array('OrdenDescuentoCuota')));
		$orden = $this->read(null,$id);
		if($toString){
			return $orden['OrdenDescuento']['tipo_orden_dto'].' #'.$orden['OrdenDescuento']['numero'];
		}else{
			$orden[0] = $orden['OrdenDescuento']['tipo_orden_dto'];
			$orden[1] = $orden['OrdenDescuento']['numero'];
			return $orden;
		}
	}


	function getCuotas($id){
		$this->unbindModel(array('belongsTo' => array('Socio'),'hasMany' => array('OrdenDescuentoCuota')));
		$orden = $this->read(null,$id);
		return $orden['OrdenDescuento']['cuotas'];
	}

	/**
	 * devuelve una orden de descuento en base a un tipo y numero
	 * @param $numero
	 * @param $tipo
	 * @param $tipoProducto
	 * @param $bindModel
	 * @param $armaDatos
	 * @param $calculaSaldos
	 * @return unknown_type
	 */
	function getOrdenByNumero($numero,$tipo,$tipoProducto="",$bindModel=false,$armaDatos=true,$calculaSaldos=true,$soloActivo = false){
		if(!$bindModel)$this->unbindModel(array('belongsTo' => array('Socio'),'hasMany' => array('OrdenDescuentoCuota')));
                $conditions = array();
                $conditions['OrdenDescuento.tipo_orden_dto'] = $tipo;
                $conditions['OrdenDescuento.numero'] = $numero;
                $conditions['OrdenDescuento.tipo_producto LIKE '] = $tipoProducto . "%";
                if($soloActivo) $conditions['OrdenDescuento.activo'] = 1;
                $conditions['OrdenDescuento.nueva_orden_descuento_id'] = NULL;

		$ordenes = $this->find('all',array('conditions' => $conditions));
		if($armaDatos)$ordenes = $this->armaDatos($ordenes,$calculaSaldos);
		return (isset($ordenes[0]) ? $ordenes[0] : null);
	}

	/**
	 * devuelve todas las ordenes de descuento para un mismo numero
	 * @param $numero
	 * @param $bindModel
	 * @return unknown_type
	 */
	function getOrdenesByNumero($numero,$bindModel=false){
		if(!$bindModel)$this->unbindModel(array('belongsTo' => array('Socio'),'hasMany' => array('OrdenDescuentoCuota')));
		$ordenes = $this->find('all',array('conditions' => array('OrdenDescuento.numero' => $numero)));
		$ordenes = $this->armaDatos($ordenes);
		return $ordenes;
	}

	/**
	 * Devuelve todas las ordenes de descuentos para un socio y un proveedor
	 * @param unknown_type $socio_id
	 * @param unknown_type $proveedor_id
	 * @param unknown_type $bindModel
	 */
	function getOrdenesBySocioByProveedor($socio_id,$proveedor_id,$bindModel=false){
		if(!$bindModel)$this->unbindModel(array('belongsTo' => array('Socio'),'hasMany' => array('OrdenDescuentoCuota')));
		$ordenes = $this->find('all',array('conditions' => array('OrdenDescuento.socio_id' => $socio_id,'OrdenDescuento.proveedor_id' => $proveedor_id)));
		$ordenes = $this->armaDatos($ordenes);
		return $ordenes;

	}

	/**
	 * arma datos adicionales de la orden, si el segundo parametro es true calcula los saldos
	 * @param array $ordenes
	 * @param boolean $conSaldos
	 * @param string $codigoOrganismo
	 * @return array
	 */
	function armaDatos($ordenes,$conSaldos=true,$periodo=null,$codigoOrganismo = null){
                /**
                 * 26/11/2014 Gustavo
                 * Agrego una columna de la descripcion de la calificacion del socio por pedido de Nora.
                 * Expresamente para ECPYS. Autorizado por Guillermo. Urgente.
                 */
                App::import('model','Pfyj.Socio');
                $oSocio = new Socio();

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		foreach($ordenes as $idx => $orden){

                        $ultCalificacion = $oSocio->getUltimaCalificacion($orden['OrdenDescuento']['socio_id'], '', true);
			$orden = ($conSaldos ? $this->armaSaldos($orden,$periodo,$codigoOrganismo) : $this->armaDatosByOrdenSinSaldos($orden));
			$orden = $this->setTotalesLetras($orden);

                        $orden['OrdenDescuento']['ultima_calificacion'] = $ultCalificacion;
                        $orden['OrdenDescuento']['resumen'] = NULL;
//                        $orden['OrdenDescuento']['resumen'] = $oCUOTA->getMoraByOrdenDtoHastaPeriodo($orden['OrdenDescuento']['id']);

                        $ordenes[$idx] = $orden;

		}
		return $ordenes;
	}


	function setTotalesLetras($orden){
		$orden['OrdenDescuento']['total_letras'] = parent::num2letras($orden['OrdenDescuento']['importe_total']);
		$orden['OrdenDescuento']['total_cuota_letras'] = parent::num2letras($orden['OrdenDescuento']['importe_cuota']);
		$orden['OrdenDescuento']['total_cuota_cantidad_letras'] = parent::num2letras($orden['OrdenDescuento']['cuotas'],false);
		return $orden;
	}


	/**
	 * arma datos adicionales de la orden sin el calculo de saldos
	 * @param $orden
	 * @return unknown_type
	 */
	function armaDatosByOrdenSinSaldos($orden){
		$orden = $this->setProducto($orden);
		$orden = $this->__setPersona($orden);
		$orden = $this->__setBeneficio($orden);
		$orden = $this->__setOrganismo($orden);
		$orden['OrdenDescuento']['tipo_nro'] = $orden['OrdenDescuento']['tipo_orden_dto'].' #'.$orden['OrdenDescuento']['numero'];
		$orden['OrdenDescuento']['inicia_en'] = parent::periodo($orden['OrdenDescuento']['periodo_ini']);
		$orden['OrdenDescuento']['recibo_detalle'] = $orden['OrdenDescuento']['tipo_orden_dto'].' #'.$orden['OrdenDescuento']['numero'] . " - " . $orden['OrdenDescuento']['producto_descripcion'];
		if($orden['OrdenDescuento']['tipo_orden_dto'] == 'CMUTU'):
			$orden['OrdenDescuento']['recibo_detalle'] = $orden['OrdenDescuento']['producto_descripcion'];
		endif;

		return $orden;
	}

	/**
	 * calcula los saldos actuales de una orden
	 * @param array $orden
	 * @param string $periodo
	 * @param string $codigoOrganismo
	 * @return array
	 */
	function armaSaldos($orden,$periodo=null,$codigoOrganismo = null){

		$saldos = array();
		$hoy = date('Y-m-d');

		$vencidas = 0;
		$avencer = 0;
		$pagadas = 0;

		$impoVencido = 0;
		$impoAvencer = 0;
		$impoPagado = 0;
		$impoDevengado = 0;

		$orden['OrdenDescuento']['vencidas'] = $vencidas;
		$orden['OrdenDescuento']['avencer'] = $avencer;
		$orden['OrdenDescuento']['pagadas'] = $pagadas;
		$orden['OrdenDescuento']['importe_vencido'] = $impoVencido;
		$orden['OrdenDescuento']['importe_avencer'] = $impoAvencer;
		$orden['OrdenDescuento']['importe_pagado'] = $impoPagado;
		$orden['OrdenDescuento']['importe_devengado'] = $impoDevengado;
		$orden['OrdenDescuento']['saldo'] = round($impoDevengado - $impoPagado,2);

//		debug($orden);

		$orden = $this->setProducto($orden);
		$orden = $this->__setPersona($orden);
		$orden = $this->__setBeneficio($orden);
		$orden = $this->__setOrganismo($orden);

		$orden['OrdenDescuento']['tipo_nro'] = $orden['OrdenDescuento']['tipo_orden_dto'].' #'.$orden['OrdenDescuento']['numero'];
		$orden['OrdenDescuento']['inicia_en'] = parent::periodo($orden['OrdenDescuento']['periodo_ini']);

		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
		$saldos = $oCuota->getSaldosByOrdenDto($orden['OrdenDescuento']['id'],$periodo,$codigoOrganismo);

		$orden['OrdenDescuento']['vencidas'] = $saldos['vencidas'];
		$orden['OrdenDescuento']['avencer'] = $saldos['avencer'];
		$orden['OrdenDescuento']['pagadas'] = $saldos['pagadas'];
		$orden['OrdenDescuento']['importe_vencido'] = $saldos['importe_vencido'];
		$orden['OrdenDescuento']['importe_avencer'] = $saldos['importe_avencer'];
		$orden['OrdenDescuento']['importe_pagado'] = $saldos['importe_pagado'];
		$orden['OrdenDescuento']['importe_devengado'] = $saldos['importe_devengado'];
		$orden['OrdenDescuento']['saldo'] = $saldos['saldo'];
		$orden['OrdenDescuento']['bloqueo_liquidacion'] = $saldos['bloqueo'];
		$orden['OrdenDescuento']['baja_cuotas'] = $saldos['baja_cuotas'];
		$orden['OrdenDescuento']['baja_importe'] = $saldos['baja_importe'];

		//saco los datos del ultimo cobro
		$orden['OrdenDescuento']['ultimo_cobro'] = $this->getUltimoCobroByOrdenDto($orden['OrdenDescuento']['id']);

		return $orden;
	}

	/**
	 * setea el string que identifica al proveedor / producto
	 * @param $orden
	 * @return unknown_type
	 */
	function setProducto($orden){
		App::import('model','Proveedores.Proveedor');
		$oProveedor = new Proveedor();
		$oProveedor->unbindModel(array('hasMany' => array('MutualProducto')));
		$proveedor = $oProveedor->read(null,$orden['OrdenDescuento']['proveedor_id']);
		$glb = $this->getGlobalDato('concepto_1',$orden['OrdenDescuento']['tipo_producto']);
//		if($orden['OrdenDescuento']['tipo_orden_dto'] == 'CMUTU'):
//			$glb = $this->getGlobalDato('concepto_1',$glb['GlobalDato']['concepto_2']);
//		endif;
		$orden['OrdenDescuento']['proveedor_producto'] = $proveedor['Proveedor']['razon_social_resumida'] . ' / ' . $glb['GlobalDato']['concepto_1'] . ( !empty($orden['OrdenDescuento']['nro_referencia_proveedor']) ? ' (REF:'.$orden['OrdenDescuento']['nro_referencia_proveedor'].')' : '');
		$orden['OrdenDescuento']['proveedor'] = $proveedor['Proveedor']['razon_social'];
		$orden['OrdenDescuento']['proveedor_resumido'] = $proveedor['Proveedor']['razon_social_resumida'];
		$orden['OrdenDescuento']['producto_descripcion'] = $glb['GlobalDato']['concepto_1'];
		return $orden;
	}

	/**
	 * setea el string con el apellido y nombre
	 * @param $orden
	 * @return unknown_type
	 */
	function __setPersona($orden){
		App::import('model','Pfyj.Socio');
		$oSocio = new Socio();
		$orden['OrdenDescuento']['persona']	= $oSocio->getApenom($orden['OrdenDescuento']['socio_id'],true);
		$orden['OrdenDescuento']['persona_tdocndoc']	= $oSocio->getTdocNdoc($orden['OrdenDescuento']['socio_id']);
		$orden['OrdenDescuento']['persona_apenom']	= $oSocio->getApenom($orden['OrdenDescuento']['socio_id'],false);
		return $orden;
	}

	/**
	 * setea el string del beneficio
	 * @param $orden
	 * @return unknown_type
	 */
	function __setBeneficio($orden){
		App::import('model','Pfyj.PersonaBeneficio');
		$oBeneficio = new PersonaBeneficio();
		$orden['OrdenDescuento']['beneficio_str']	= $oBeneficio->getStrBeneficio($orden['OrdenDescuento']['persona_beneficio_id']);
                $aBeneficio = $oBeneficio->getBeneficio($orden['OrdenDescuento']['persona_beneficio_id']);
                $orden['OrdenDescuento']['cbu'] = $aBeneficio['PersonaBeneficio']['cbu'];
		return $orden;
	}

	/**
	 * setea el string del organismo
	 * @param $orden
	 * @return unknown_type
	 */
	function __setOrganismo($orden){
		App::import('model','Pfyj.PersonaBeneficio');
		$oBeneficio = new PersonaBeneficio();
		$orden['OrdenDescuento']['codigo_organismo']	= $oBeneficio->getCodigoOrganismo($orden['OrdenDescuento']['persona_beneficio_id']);
		$orden['OrdenDescuento']['organismo']	= $oBeneficio->getOrganismo($orden['OrdenDescuento']['persona_beneficio_id']);
		$orden['OrdenDescuento']['empresa'] = $oBeneficio->getEmpresaDescripcion($orden['OrdenDescuento']['persona_beneficio_id']);
		return $orden;
	}

	/**
	 * elimina una orden siempre y cuando no tenga pagos. Si tiene pagos la marca como anulada
	 * @param $id
	 * @return unknown_type
	 */
	function eliminarOrden($id){

		//verifico que no tenga pagos
		$orden = $this->getOrden($id);
		if($orden['OrdenDescuento']['importe_pagado'] != 0) return false;

		//no tiene pago la elimino y elimino las cuotas
		parent::begin();
		//borro las cuotas
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();

		if($oCuota->deleteAll("OrdenDescuentoCuota.orden_descuento_id = $id")){
			if($this->del($id)){
				return parent::commit();
			}else{
				parent::rollback();
				return false;

			}
		}else{
			parent::rollback();
			return false;
		}
	}

	/**
	 * @deprecated
	 * @param $id
	 * @return unknown_type
	 */
	function baja($id){
//		parent::begin();
//		//borro las cuotas
//		App::import('model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();
//
//		if($oCuota->deleteAll("OrdenDescuentoCuota.orden_descuento_id = $id")){
//			if($this->del($id)){
//				parent::commit();
//				return true;
//			}else{
//				parent::rollback();
//				return false;
//
//			}
//		}else{
//			parent::rollback();
//			return false;
//		}
	}

	/**
	 * devuelve una orden de descuento con sus cuotas reprogramadas a la fecha pasada por parametro
	 * SOLAMENTE AFECTA A LAS CUOTAS QUE ESTAN EN ESTADO A
	 * @param $id
	 * @param $fecha
	 * @return array ordenReprogramada
	 */
	function calculaReprogramacion($id,$fecha){

		$orden = $this->getOrden($id,null,true,false);
		//proceso las cuotas
		if(count($orden['OrdenDescuentoCuota']) != 0){

			App::import('Model', 'Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota(null);

			$inicio = date('Ym',strtotime($fecha));
			$mkIni = mktime(0,0,0,substr($inicio,4,2),1,substr($inicio,0,4));
			$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($orden['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($orden['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($orden['OrdenDescuento']['primer_vto_socio'])));
			$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($orden['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($orden['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($orden['OrdenDescuento']['primer_vto_proveedor'])));

			$cuotasReprogramadas = array();
			$n=0;
			foreach($orden['OrdenDescuentoCuota'] as $idx => $cuota){
                            //calcular el saldo de la cuota
                            $saldo = $oCuota->getSaldo($cuota['id'], '999912');
                            if($saldo != 0):
                                $cuota = $oCuota->infoCuota($cuota);
                                if(empty($cuota['bloqueo_liquidacion']) || $cuota['bloqueo_liquidacion']['id'] == 0):
                                        if($orden['OrdenDescuento']['reprogramada']==0)$cuota['periodo_origen'] = $cuota['periodo'];
                                        $cuota['periodo'] = date('Ym',$this->addMonthToDate($mkIni,$n));

                                        $cuota['vencimiento'] = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$n));
                                        $cuota['vencimiento_proveedor'] = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$n));
                                endif;
                                array_push($cuotasReprogramadas,$cuota);
                                $n++;
                            endif;
			}
			$orden['OrdenDescuentoCuota'] = $cuotasReprogramadas;
		}
		return $orden;
	}


	function reprogramarOrdenByPeriodoInicio($id,$periodoInicio){

		$orden = $this->getOrden($id,null,true,false);
		$iniDay = 1;

//		$fecha = $orden['OrdenDescuento']['fecha'];

		$periodoDiff = parent::periodoDiff($orden['OrdenDescuento']['periodo_ini'], $periodoInicio);

		if(count($orden['OrdenDescuentoCuota']) != 0):
			App::import('Model', 'Mutual.OrdenDescuentoCuota');
//			$oCuota = new OrdenDescuentoCuota(null);
			$mkIni = mktime(0,0,0,substr($periodoInicio,4,2),$iniDay,substr($periodoInicio,0,4));

			$n=0;

			foreach($orden['OrdenDescuentoCuota'] as $idx => $cuota):

				$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($cuota['vencimiento'])),date('d',strtotime($cuota['vencimiento'])),date('Y',strtotime($cuota['vencimiento'])));
				$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($cuota['vencimiento_proveedor'])),date('d',strtotime($cuota['vencimiento_proveedor'])),date('Y',strtotime($cuota['vencimiento_proveedor'])));

				if($orden['OrdenDescuento']['reprogramada'] ==0 ) $cuota['periodo_origen'] = $cuota['periodo'];
				$cuota['periodo'] = date('Ym',$this->addMonthToDate($mkIni,$n));
				$cuota['vencimiento'] = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$periodoDiff));
				$cuota['vencimiento_proveedor'] = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$periodoDiff));

				if($cuota['estado'] === "A") $orden['OrdenDescuentoCuota'][$idx] = $cuota;
				$n++;

			endforeach;


		endif;

		return $orden;


	}

	/**
	 * guarda en una transaccion una orden reprogramada
	 * @param array $datos (orden con la reprogramacion calculada)
	 */
	function reprogramarOrden($datos){
//		return false;
		$datos['OrdenDescuento']['reprogramada'] = 1;

		if(!empty($datos['OrdenDescuentoCuota'])){
			if(!$this->saveAll($datos)) return false;
		}else{
			if(!$this->save($datos)) return false;
		}
//		if($datos['OrdenDescuento']['tipo_orden_dto'] == 'OCOMP'){
//			//si es una orden de compra / servicio modifico la mutual_producto_solicitudes
//			App::import('Model', 'Mutual.MutualProductoSolicitud');
//			$oCompra = new MutualProductoSolicitud(null);
//			$ordenCompra = $oCompra->read(null,$datos['OrdenDescuento']['numero']);
//			$ordenCompra['MutualProductoSolicitud']['fecha_pago'] = $datos['OrdenDescuento']['fecha'];
//			$ordenCompra['MutualProductoSolicitud']['periodo_ini'] = $datos['OrdenDescuento']['periodo_ini'];
//			$ordenCompra['MutualProductoSolicitud']['primer_vto_socio'] = $datos['OrdenDescuento']['primer_vto_socio'];
//			$ordenCompra['MutualProductoSolicitud']['primer_vto_proveedor'] = $datos['OrdenDescuento']['primer_vto_proveedor'];
//			if(!$oCompra->save($ordenCompra)) return false;
//		}
//		if(!$this->save($datos)) return false;
		return true;
	}

	/**
	 * Reasigna el beneficio de una orden de descuento VIGENTE y de sus cuotas ADEUDADAS
	 * @param $orden_descuento_id
	 * @param $persona_beneficio_id
	 * @return unknown_type
	 */
	function reasignarBeneficio($orden_descuento_id,$persona_beneficio_id,$periodoDesde = NULL){
		$status = true;
		$orden = $this->read(null,$orden_descuento_id);

		//SI EL BENEFICIO ES DISTINTO AL ACTUAL NOVAR LA ORDEN
		if($persona_beneficio_id == $orden['OrdenDescuento']['persona_beneficio_id']) return $status;

		$motivo = "NOVACION POR REASIGNACION DE BENEFICIO [".(isset($_SESSION['NAME_USER_LOGON_SIGEM']) ? $_SESSION['NAME_USER_LOGON_SIGEM'] : 'APLICACION_SERVER')." | ".date("d-m-Y H:i:s")."]";

		$orden = $this->novarOrden($orden_descuento_id,$persona_beneficio_id,$motivo,$periodoDesde);

		if(!$orden) return false;



//		$this->id = $orden['OrdenDescuento']['id'];
//		if(!$this->saveField('persona_beneficio_id',$persona_beneficio_id)){
//			$status = false;
//		}
//		$this->saveField('reasignada',1);
//		if(count($orden['OrdenDescuentoCuota']) != 0){
//			foreach($orden['OrdenDescuentoCuota'] as $cuota){
//				$this->OrdenDescuentoCuota->id = $cuota['id'];
//				if($cuota['estado'] == $this->codigos_estado_cuota['A']['codigo_db']){
//					if(!$this->OrdenDescuentoCuota->saveField('persona_beneficio_id',$persona_beneficio_id)){
//						$status = false;
//						break;
//					}
//				}
//
//			}
//		}
		#VERIFICAR QUE EL IMPORTE DE LA CUOTA SOCIAL SEA LA CORRESPONDIENTE AL NUEVO BENEFICIO
		if($orden['OrdenDescuento']['tipo_orden_dto'] == 'CMUTU' && $orden['OrdenDescuento']['numero'] == $orden['OrdenDescuento']['socio_id'] && $orden['OrdenDescuento']['tipo_producto'] == 'MUTUPROD0003'){
			App::import('Model', 'Pfyj.Socio');
			$oSocio = new Socio(null);
			if(!$oSocio->actualizarCuotaSocial($orden['OrdenDescuento']['socio_id'],$persona_beneficio_id,null,true)) $status = false;
		}
		return true;
	}

	/**
	 * reasigna beneficio masivamente todas las ordenes de un beneficio dado con el nuevo beneficio pasado por parametro
	 * @param unknown_type $persona_beneficio_id
	 * @param unknown_type $nuevo_beneficio_id
	 * @return unknown_type
	 */
	function reasignarBeneficioByPersonaBeneficioId($persona_beneficio_id,$nuevo_beneficio_id,$periodoDesde = NULL){
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor')));
		$ordenes = $this->find('all',array('conditions' => array('OrdenDescuento.persona_beneficio_id' => $persona_beneficio_id,'OrdenDescuento.activo' => 1),'fields' => array('OrdenDescuento.id')));
		$socio_id = null;
		foreach($ordenes as $orden){
			if(!$this->reasignarBeneficio($orden['OrdenDescuento']['id'],$nuevo_beneficio_id,$periodoDesde)){
				return false;
			}
		}
		return true;
	}

	/**
	 * marca masivamente como no activas las ordenes para un beneficio dado
	 * @param $persona_beneficio_id
	 * @return unknown_type
	 */
	function desactivarByPersonaBeneficioId($persona_beneficio_id){
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor')));
		$ordenes = $this->find('all',array('conditions' => array('OrdenDescuento.persona_beneficio_id' => $persona_beneficio_id),'fields' => array('OrdenDescuento.id')));
		foreach($ordenes as $orden){
			$this->actualizarValor($orden['OrdenDescuento']['id'],'activo',0);
		}
	}

	/**
	 * marca masivamente todas las ordenes para un socio
	 * @param unknown_type $socio_id
	 * @param string $periodoHasta
	 * @return unknown_type
	 */
	function desactivarBySocio($socio_id,$periodoHasta = null){
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor')));
		$ordenes = $this->find('all',array('conditions' => array('OrdenDescuento.socio_id' => $socio_id,'OrdenDescuento.activo' => 1),'fields' => array('OrdenDescuento.id')));
		foreach($ordenes as $orden){
			$this->actualizarValor($orden['OrdenDescuento']['id'],'activo',0);
			$this->actualizarValor($orden['OrdenDescuento']['id'],'periodo_hasta',$periodoHasta);
		}
	}

	/**
	 * metodo generico para actualizar un valor de un campo
	 * @param $id
	 * @param $field
	 * @param $valor
	 * @return unknown_type
	 */
	function actualizarValor($id,$field,$valor){
		if($id == 0) return true;
        $this->unbindModel(array('belongsTo' => array('Socio','Proveedor'), 'hasMany' => array('OrdenDescuentoCuota')));
		$orden = $this->read(null,$id);
		$orden['OrdenDescuento'][$field] = $valor;
		return $this->save($orden);
	}

	function actualizarValorImporteTotalAndImporteCuota($id,$valor){
		if($id == 0) return true;
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor'), 'hasMany' => array('OrdenDescuentoCuota')));
		$orden = $this->read(null,$id);
		$orden['OrdenDescuento']['importe_cuota'] = $orden['OrdenDescuento']['importe_total'] = $valor;
		//debug($orden['OrdenDescuento']['importe_cuota']." ** ".$orden['OrdenDescuento']['importe_total']);
		return $this->save($orden);
	}

	/**
	 * devuelve la cuota social diferenciada de un socio
	 * @param unknown_type $socio_id
	 * @return unknown_type
	 */
	function getCuotaSocialDiferenciada($socio_id,$periodo=null){

		//verificar si tiene solamente una orden cuyo producto tiene una cuota social especial
		$sql = "SELECT MutualProducto.cuota_social_diferenciada FROM orden_descuentos AS OrdenDescuento
				INNER JOIN mutual_productos AS MutualProducto ON
				(
					MutualProducto.tipo_orden_dto = OrdenDescuento.tipo_orden_dto
					AND MutualProducto.tipo_producto = OrdenDescuento.tipo_producto
					AND MutualProducto.proveedor_id = OrdenDescuento.proveedor_id
				)
				WHERE OrdenDescuento.socio_id = $socio_id
				AND OrdenDescuento.activo = 1
				AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
				AND MutualProducto.cuota_social_diferenciada <> 0
				AND OrdenDescuento.socio_id NOT IN
				(SELECT socio_id FROM orden_descuentos WHERE tipo_orden_dto <> 'CMUTU'
				AND proveedor_id <> OrdenDescuento.proveedor_id)
				ORDER BY MutualProducto.cuota_social_diferenciada DESC;";
		$datos = $this->query($sql);
		if(!empty($datos) && isset($datos[0]['MutualProducto']['cuota_social_diferenciada'])){
			$cuota = $datos[0]['MutualProducto']['cuota_social_diferenciada'];
			if($cuota != 0 && count($datos) == 1) return $cuota;
		}

		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
		//si tiene alguna orden de dto distinta de cmutu y no permanente devolver cero
		$NoPerm = $this->find('count',array('conditions' => array('OrdenDescuento.socio_id' => $socio_id,'OrdenDescuento.tipo_orden_dto <>' => 'CMUTU','OrdenDescuento.permanente' => 0)));

		if($NoPerm != 0) return 0;
		//traigo las ordenes permanentes
		//ojo tener en cuenta el periodo hasta
		if(empty($periodo)):
			$conditions = array();
			$conditions['OrdenDescuento.socio_id'] = $socio_id;
			$conditions['OrdenDescuento.tipo_orden_dto <> '] = 'CMUTU';
			$conditions['OrdenDescuento.permanente'] = 1;
			$conditions['OrdenDescuento.activo'] = 1;
		else:
			$conditions = array();
			$conditions['OrdenDescuento.socio_id'] = $socio_id;
			$conditions['OrdenDescuento.tipo_orden_dto <> '] = 'CMUTU';
			$conditions['OrdenDescuento.permanente'] = 1;
			$conditions['IFNULL(OrdenDescuento.periodo_hasta,99999) >='] = $periodo;
		endif;

		$ordenes = $this->find('all',array('conditions' => $conditions));

		if(count($ordenes) == 0) return 0;

		App::import('Model', 'Mutual.MutualProducto');
		$oMP = new MutualProducto(null);

		$mayorCuotaSocialDiferenciada = 0;
		foreach($ordenes as $orden){
			//busco si para el producto y proveedor tiene una cuota social diferenciada
			$cuotaSocialDiferenciada = $oMP->getMayorCuotaSocialDiferenciada($orden['OrdenDescuento']['tipo_producto'],$orden['OrdenDescuento']['proveedor_id']);
			if($cuotaSocialDiferenciada > $mayorCuotaSocialDiferenciada){
				$mayorCuotaSocialDiferenciada = $cuotaSocialDiferenciada;
			}

		}
		return $mayorCuotaSocialDiferenciada;
	}

	/**
	 * devuelve el id del proveedor asociado a la orden
	 * @param unknown_type $id
	 */
	function getProveedorID($id){
		$orden = $this->read('proveedor_id',$id);
		return $orden['OrdenDescuento']['proveedor_id'];
	}

	/**
	 * devuelve el periodo de inicio para un socio para una orden especifica o para todas. Si el parametro $toArray
	 * devuelve un arreglo con los periodos
	 * @param $socio_id
	 * @param $ordenDtoId
	 * @param $ordenamiento
	 * @param $toArray
	 * @return unknown_type
	 */
	function getPeriodoIni($socio_id,$ordenDtoId = null,$ordenamiento="ASC",$toArray=true){
		$conditions = array();
		$conditions['OrdenDescuento.socio_id'] = $socio_id;
		if(!empty($ordenDtoId)) $conditions['OrdenDescuento.id'] = $ordenDtoId;
		$periodos = $this->find('all',array('conditions' => $conditions,'fields' => array('OrdenDescuento.periodo_ini,OrdenDescuento.socio_id'),'group' => array('OrdenDescuento.periodo_ini','OrdenDescuento.socio_id'),'order' => array("OrdenDescuento.periodo_ini $ordenamiento")));
		if($toArray) $periodos = Set::extract("{n}.OrdenDescuento.periodo_ini",$periodos);
		return $periodos;
	}

	/**
	 * devuelve las ordenes de un socio segun el parametro pasado como estado. Se puede filtrar por beneficio o por periodo
	 * estado = 0 --> TODAS LAS ORDENES CON SALDO
	 * estado = 1 --> SOLO LAS VIGENTES (con saldo > 0)
	 * estado = 2 --> SOLO LAS FINALIZADAS (saldo=0, no permanentes y activo = 1)
	 * estado = 3 --> SOLO LAS ANULADAS (activo = 0)
	 *
	 * @param unknown_type $socio_id
	 * @param unknown_type $estado
	 * @param $persona_beneficio_id
	 * @param $periodoIni
	 * @return unknown_type
	 */
	function OrdenesBySocioByEstadoActual($socio_id,$estado=1,$persona_beneficio_id=null,$periodoIni=null){

		$consumos = array();
		$conditions = array();
		$conditions['OrdenDescuento.socio_id'] = $socio_id;
//		$conditions['OrdenDescuento.activo'] = 1;

		if(!empty($persona_beneficio_id)) $conditions['OrdenDescuento.persona_beneficio_id'] = $persona_beneficio_id;
		if(!empty($periodoIni)) $conditions['OrdenDescuento.periodo_ini'] = $periodoIni;

		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
		$ordenes = $this->find('all',array('conditions' => $conditions,'order'=>'OrdenDescuento.periodo_ini DESC,OrdenDescuento.created DESC'));
		$ordenes = $this->armaDatos($ordenes,false);


		App::import('Model', 'Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota(null);
		foreach($ordenes as $idx => $orden):

			$saldos = $oCUOTA->getSaldosByOrdenDto($orden['OrdenDescuento']['id']);

			$orden['OrdenDescuento']['vencidas'] = $saldos['vencidas'];
			$orden['OrdenDescuento']['avencer'] = $saldos['avencer'];
			$orden['OrdenDescuento']['pagadas'] = $saldos['pagadas'];
			$orden['OrdenDescuento']['importe_vencido'] = $saldos['importe_vencido'];
			$orden['OrdenDescuento']['importe_avencer'] = $saldos['importe_avencer'];
			$orden['OrdenDescuento']['importe_pagado'] = $saldos['importe_pagado'];
			$orden['OrdenDescuento']['importe_devengado'] = $saldos['importe_devengado'];
			$orden['OrdenDescuento']['saldo'] = $saldos['saldo'];
			$orden['OrdenDescuento']['bloqueo_liquidacion'] = $saldos['bloqueo'];

			$ordenes[$idx] = $orden;

		endforeach;

//		debug($ordenes);

//		$ADRIAN = Set::extract("/OrdenDescuento[activo=1 or saldo>0]",$ordenes);
//		debug($ADRIAN);

//		if($estado == 1) return Set::extract("/OrdenDescuento[saldo>0]",$ordenes);
		if($estado == 1) return Set::extract("/OrdenDescuento[activo=1]",$ordenes);
		if($estado == 2) return Set::extract("/OrdenDescuento[saldo=0][activo=1][permanente=0]",$ordenes);
		if($estado == 3) return Set::extract("/OrdenDescuento[activo=0]",$ordenes);
		if($estado == 0) return Set::extract("/OrdenDescuento[saldo>0]",$ordenes);

	}

        /**
         *
         * @param type $socio_id
         * @param type $estado
         * @param type $persona_beneficio_id
         * @param type $periodoIni
         * @return type
         */
        function get_by_socio_by_estado($socio_id,$estado=1,$persona_beneficio_id=null,$periodoIni=null,$periodoCorte=null,$fechaEmiDesde=null,$fechaEmiHasta=null,$soloAdeudadas = FALSE){

            $periodoCorte = (empty($periodoCorte) ? date('Ym') : $periodoCorte);

            $sql = "select
                    OrdenDescuento.id
                    ,OrdenDescuento.socio_id
                    ,OrdenDescuento.tipo_orden_dto
                    ,OrdenDescuento.numero
                    ,concat(OrdenDescuento.tipo_orden_dto,' #'
                    ,OrdenDescuento.numero) as tipo_numero
                    ,OrdenDescuento.periodo_ini
                    ,OrdenDescuento.periodo_hasta
                    ,OrdenDescuento.primer_vto_socio
                    ,OrdenDescuento.primer_vto_proveedor
                    ,OrdenDescuento.importe_total
                    ,OrdenDescuento.cuotas
                    ,OrdenDescuento.importe_cuota
                    ,OrdenDescuento.activo
                    ,OrdenDescuento.permanente
                    ,OrdenDescuento.reprogramada
                    ,OrdenDescuento.nro_referencia_proveedor
                    ,CodigoOrganismo.concepto_1
                    ,CodigoEmpresa.concepto_1
                    ,Proveedor.razon_social_resumida
                    ,TipoProducto.concepto_1
                    ,temp_1.total_cuotas
                    ,temp_1.cantidad_cuotas
                    ,temp_2.total_pagos
                    ,temp_2.cantidad_pagos
                    ,temp_1.total_cuotas - temp_2.total_pagos as saldo
                    ,if((temp_1.total_cuotas - temp_2.total_pagos) > 0, temp_1.cantidad_cuotas - temp_2.cantidad_pagos, 0) as cuotas_adeudadas
                    from orden_descuentos as OrdenDescuento
                    inner join proveedores Proveedor on Proveedor.id = OrdenDescuento.proveedor_id
                    inner join global_datos TipoProducto on TipoProducto.id = OrdenDescuento.tipo_producto
                    inner join persona_beneficios PersonaBeneficio on PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id
                    inner join global_datos CodigoOrganismo on CodigoOrganismo.id = PersonaBeneficio.codigo_beneficio
                    left join global_datos CodigoEmpresa on CodigoEmpresa.id = PersonaBeneficio.codigo_empresa
                    left join (select o.id , ifnull(sum(c.importe),0) as total_cuotas,count(c.id) as cantidad_cuotas
                    from orden_descuentos o
                    inner join orden_descuento_cuotas c on c.orden_descuento_id = o.id
                    where o.socio_id = $socio_id  ".($estado != 3 ? " and c.estado <> 'B' " : " ")." and c.periodo <= '$periodoCorte'
                    group by o.id) as temp_1 on (temp_1.id = OrdenDescuento.id)
                    left join (select o.id , ifnull(sum(cc.importe),0) as total_pagos, count(cc.orden_descuento_cuota_id) as cantidad_pagos
                    from orden_descuentos o
                    inner join orden_descuento_cuotas c on c.orden_descuento_id = o.id
                    left join orden_descuento_cobro_cuotas cc on cc.orden_descuento_cuota_id = c.id
                    where o.socio_id = $socio_id ".($estado != 3 ? " and c.estado <> 'B' " : " ")." group by o.id) as temp_2 on (temp_2.id = OrdenDescuento.id)
                    where
                    OrdenDescuento.socio_id = $socio_id
                    ".(!empty($persona_beneficio_id) ? " and OrdenDescuento.persona_beneficio_id = $persona_beneficio_id " : " ")."
                    ".($estado == 0 ? " and OrdenDescuento.activo = 1 and (if(ifnull(temp_1.total_cuotas,0) = 0 AND OrdenDescuento.permanente = 1,OrdenDescuento.importe_cuota,ifnull(temp_1.total_cuotas,0)) - if(OrdenDescuento.permanente = 1,0,ifnull(temp_2.total_pagos,0))) > 0 " : " ")."
                    ".($estado == 1 ? " and OrdenDescuento.activo = 1 AND OrdenDescuento.permanente = 0" : " ")."
                    ".($estado == 2 ? " and OrdenDescuento.activo = 1 and OrdenDescuento.permanente = 0 and (temp_1.total_cuotas - temp_2.total_pagos) <= 0 " : " ")."
                    ".($estado == 3 ? " and OrdenDescuento.activo = 0 " : " ")."
                    ".($estado == 4 ? " and OrdenDescuento.activo = 1 and OrdenDescuento.permanente = 1 " : " ")."    
                    ".(!empty($fechaEmiDesde) ? " and OrdenDescuento.fecha >= '$fechaEmiDesde' " : "")."
                    ".(!empty($fechaEmiHasta) ? " and OrdenDescuento.fecha <= '$fechaEmiHasta' " : "")."
                    -- and temp_1.total_cuotas > temp_2.total_pagos
                    ".($soloAdeudadas ? " having saldo > 0 " : " " )."
                    order by OrdenDescuento.periodo_ini desc,OrdenDescuento.created DESC;";
//            debug($sql);
            $datos = $this->query($sql);
            $ordenes = array();
            if(!empty($datos)){
                foreach($datos as $i => $dato){
                    $ordenes[$i]['OrdenDescuento'] = $dato['OrdenDescuento'];
                    $ordenes[$i]['OrdenDescuento']['tipo_numero'] = $dato[0]['tipo_numero'];
                    $ordenes[$i]['OrdenDescuento']['proveedor_producto'] = $dato['Proveedor']['razon_social_resumida']."/".$dato['TipoProducto']['concepto_1'];
                    $ordenes[$i]['OrdenDescuento']['saldo'] = $dato[0]['saldo'];
                    $ordenes[$i]['OrdenDescuento']['cuotas_adeudadas'] = $dato[0]['cuotas_adeudadas'];
                    $ordenes[$i]['OrdenDescuento']['beneficio_str'] = $dato['CodigoOrganismo']['concepto_1'].(!empty($dato['CodigoEmpresa']['concepto_1']) ? " - ". $dato['CodigoEmpresa']['concepto_1'] : "");
                    $ordenes[$i]['OrdenDescuento']['total_cuotas'] = $dato['temp_1']['total_cuotas'];
                    $ordenes[$i]['OrdenDescuento']['cantidad_cuotas'] = $dato['temp_1']['cantidad_cuotas'];
                    $ordenes[$i]['OrdenDescuento']['total_pagos'] = $dato['temp_2']['total_pagos'];
                    $ordenes[$i]['OrdenDescuento']['cantidad_pagos'] = $dato['temp_2']['cantidad_pagos'];
                }
            }
            return $ordenes;
        }


	/**
	 * Suspende una orden de descuento permanente y da de baja las cuotas adeudadas pasadas por parametro como array
	 * @param integer $id
	 * @param string $periodoHasta periodo hasta el cual se tienen que liquidar las cuotas
	 * @param array $bajarCuotas array con los id de las cuotas a dar de baja
	 * @return boolean
	 */
	function suspenderOrdenPermanente($id,$periodoHasta,$bajarCuotas){

		if(empty($periodoHasta)) return false;

		$orden = $this->read(null,$id);

//		$periodoHasta = (!empty($periodoHasta) ? $periodoHasta : date('Ym'));

		if($orden['OrdenDescuento']['permanente'] != 1) return false;
		$orden['OrdenDescuento']['periodo_hasta'] = $periodoHasta;

		if(!empty($bajarCuotas)):

			App::import('Model', 'Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota(null);

			foreach($bajarCuotas as $cuota_id):
				if(!$oCUOTA->cambiarEstado($cuota_id,'B'))return false;
			endforeach;
		endif;

		$orden['OrdenDescuento']['activo'] = 0;

		return $this->save($orden);

	}

	/**
	 * devuelve true or false si la orden esta activa o no
	 * @param integer $id
	 * @return integer (1 / 0)
	 */
	function isActiva($id){
		$orden = $this->read(null,$id);
		return $orden['OrdenDescuento']['activo'];
	}

	/**
	 * NOVAR ORDEN
	 * Para el caso del cambio de importe de una orden permanente o cambio de beneficio
	 * recordar condicion para la CJP
	 * @author adrian [10/05/2012]
	 * @param integer $id
	 * @param integer $persona_beneficio_id
	 * @param string $motivo
	 *
	 * @return array OrdenNovada
	 */
	function novarOrden($id,$persona_beneficio_id = null,$motivo = null,$periodoDesde = NULL){

		$orden = array();
		$ordenNovada = array();

		$this->unbindModel(array('hasMany' => array('OrdenDescuentoCuota'),'belongsTo' => array('Socio','Proveedor')));
		$orden = $ordenNovada = $this->read(null,$id);


		$orden['OrdenDescuento']['activo'] = 0;
		$orden['OrdenDescuento']['motivo_novacion'] = $motivo;

		if ($orden['OrdenDescuento']['permanente'] == 1) {
                    $orden['OrdenDescuento']['periodo_hasta'] = (!empty($periodoDesde) ? $periodoDesde : date('Ym'));
                }

                $ordenNovada['OrdenDescuento']['id'] = 0;
		$ordenNovada['OrdenDescuento']['anterior_orden_descuento_id'] = $id;
                $ordenNovada['OrdenDescuento']['periodo_hasta'] = null;

		if (!empty($persona_beneficio_id)) {
                    $ordenNovada['OrdenDescuento']['persona_beneficio_id'] = $persona_beneficio_id;
                }

                parent::begin();

		//creo la nueva orden
		if(!$this->save($ordenNovada)){
			parent::rollback();
			return false;
		}

		//tomo el ID nuevo
		$ordenNovada['OrdenDescuento']['id'] = $this->getLastInsertID();

		//grabo la orden vieja referenciando a la orden nueva
		$orden['OrdenDescuento']['nueva_orden_descuento_id'] = $ordenNovada['OrdenDescuento']['id'];
		if(!$this->save($orden)){
			parent::rollback();
			return false;
		}

		//actualizo las cuotas con el id nuevo
		App::import('Model','mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		//CAMBIO EL ID DE LA ORDEN DE DESCUENTO NUEVA A LAS CUOTAS
		$update = array();
		$update['OrdenDescuentoCuota.orden_descuento_id'] = $ordenNovada['OrdenDescuento']['id'];
// 		if(!empty($persona_beneficio_id)) $update['OrdenDescuentoCuota.persona_beneficio_id'] = $persona_beneficio_id;
		if(!$oCUOTA->updateAll($update,array('OrdenDescuentoCuota.orden_descuento_id' => $id))){
			parent::rollback();
			return false;
		}



		//SI HAY CAMBIO DE BENEFICIO SOLAMENTE AFECTAR A LAS CUOTAS ADEUDADAS, LAS PAGAS/BAJA NO TOCAR
		$cuotas = $oCUOTA->cuotasAdeudadasTotalmenteByOrdenDto($ordenNovada['OrdenDescuento']['id'],null);
// 		debug($cuotas);
// 		parent::rollback();
		if(!empty($persona_beneficio_id) && !empty($cuotas)){
			foreach($cuotas as $idx => $cuota){
				$cuotas[$idx]['OrdenDescuentoCuota']['persona_beneficio_id'] = $persona_beneficio_id;
			}
			if(!$oCUOTA->saveAll($cuotas)){
				parent::rollback();
				return false;
			}
		}
		


// 		$update = array();
// 		$update['OrdenDescuentoCuota.orden_descuento_id'] = $ordenNovada['OrdenDescuento']['id'];
// 		if(!empty($persona_beneficio_id)) $update['OrdenDescuentoCuota.persona_beneficio_id'] = $persona_beneficio_id;

// 		if(!$oCUOTA->updateAll($update,array('OrdenDescuentoCuota.orden_descuento_id' => $id))){
// 			parent::rollback();
// 			return false;
// 		}

		parent::commit();

		return $ordenNovada;

	}


	function anularNovacion($id){

		$ordenAnterior = array();
		$ordenNovada = array();

		$this->unbindModel(array('hasMany' => array('OrdenDescuentoCuota'),'belongsTo' => array('Socio','Proveedor')));
		$ordenNovada = $this->read(null,$id);

		$idAnterior = $ordenNovada['OrdenDescuento']['anterior_orden_descuento_id'];
		$this->unbindModel(array('hasMany' => array('OrdenDescuentoCuota'),'belongsTo' => array('Socio','Proveedor')));
		$ordenAnterior = $this->read(null,$idAnterior);

		$ordenAnterior['OrdenDescuento']['activo'] = 1;
		$ordenAnterior['OrdenDescuento']['nueva_orden_descuento_id'] = 0;
		$ordenAnterior['OrdenDescuento']['motivo_novacion'] = null;
                $ordenAnterior['OrdenDescuento']['periodo_hasta'] = null;

		$ordenNovada['OrdenDescuento']['activo'] = 0;
		$ordenNovada['OrdenDescuento']['observaciones'] = "POR ANULACION DE LA NOVACION DE LA ORDEN #$idAnterior";

		App::import('Model','mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		parent::begin();

		if(!$this->save($ordenNovada)){
			parent::rollback();
			return false;
		}

		if(!$this->save($ordenAnterior)){
			parent::rollback();
			return false;
		}

		if(!$oCUOTA->updateAll(array('OrdenDescuentoCuota.orden_descuento_id' => $idAnterior),array('OrdenDescuentoCuota.orden_descuento_id' => $id))){
			parent::rollback();
			return false;
		}

		parent::commit();

		return $ordenAnterior;

	}

	/**
	 * Cargo la informacion del ultimo cobro de una orden de descuento
	 *
	 * @author adrian [15/05/2012]
	 * @param unknown_type $orden_descuento_id
	 * @return boolean|unknown
	 */
	function getUltimoCobroByOrdenDto($orden_descuento_id){
		$ultimoCobro = array();
		$sql = "SELECT OrdenDescuentoCobro.* FROM orden_descuento_cuotas AS OrdenDescuentoCuota
				INNER JOIN orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota ON (OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
				INNER JOIN orden_descuento_cobros AS OrdenDescuentoCobro ON (OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id)
				WHERE orden_descuento_id = $orden_descuento_id
				ORDER BY OrdenDescuentoCobro.fecha DESC
				LIMIT 1";
		$datos = $this->query($sql);
		if(empty($datos)) return $ultimoCobro;

		$ultimoCobro['id'] = $datos['0']['OrdenDescuentoCobro']['id'];
		$ultimoCobro['fecha'] = $datos['0']['OrdenDescuentoCobro']['fecha'];
		$ultimoCobro['periodo_cobro'] = $datos['0']['OrdenDescuentoCobro']['periodo_cobro'];
		$ultimoCobro['periodo_cobro_desc'] = parent::periodo($datos['0']['OrdenDescuentoCobro']['periodo_cobro']);
		$ultimoCobro['tipo_cobro'] = $datos['0']['OrdenDescuentoCobro']['tipo_cobro'];
		$ultimoCobro['tipo_cobro_desc'] = parent::GlobalDato("concepto_1",$datos['0']['OrdenDescuentoCobro']['tipo_cobro']);
		$ultimoCobro['importe'] = $datos['0']['OrdenDescuentoCobro']['importe'];
		return $ultimoCobro;

	}


	function getOrdenesByProveedor($proveedor_id,$codigo_organismo = null,$periodo = null,$valorActual = 0, $valorNuevo = 0){
		$ordenes = array();
		$sql = "SELECT
				Persona.id,
				Persona.documento,
				Persona.apellido,
				Persona.nombre,
				OrdenDescuento.id,
				OrdenDescuento.tipo_orden_dto,
				OrdenDescuento.numero,
				OrdenDescuento.socio_id,
				OrdenDescuento.importe_cuota
				".($valorActual != 0 && $valorNuevo != 0 ? ",ROUND(OrdenDescuento.importe_cuota / $valorActual,0) AS unidades,
				ROUND(OrdenDescuento.importe_cuota / $valorActual,0) * $valorNuevo AS valor_nuevo" : ",0 AS unidades,OrdenDescuento.importe_cuota as valor_nuevo")."
				FROM orden_descuentos AS OrdenDescuento
				INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
				INNER JOIN socios AS Socio ON (Socio.id = OrdenDescuento.socio_id)
				INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
				WHERE
				OrdenDescuento.proveedor_id = $proveedor_id
				AND OrdenDescuento.activo = 1 AND IFNULL(OrdenDescuento.periodo_hasta,'$periodo') = '$periodo'
				".(!empty($codigo_organismo) ? "AND PersonaBeneficio.codigo_beneficio = '$codigo_organismo'" : "")."
				ORDER BY Persona.apellido,
				Persona.nombre;";
		$datos = $this->query($sql);
		debug($sql);
		if(empty($datos)) return $ordenes;

		foreach($datos as $idx => $dato){

			$ordenes[$idx]['OrdenDescuento']['id'] = $dato['OrdenDescuento']['id'];
			$ordenes[$idx]['OrdenDescuento']['tipo_numero'] = $dato['OrdenDescuento']['tipo_orden_dto']." #".$dato['OrdenDescuento']['numero'];
			$ordenes[$idx]['OrdenDescuento']['socio_id'] = $dato['OrdenDescuento']['socio_id'];
			$ordenes[$idx]['OrdenDescuento']['persona_id'] = $dato['Persona']['id'];
			$ordenes[$idx]['OrdenDescuento']['persona_documento'] = $dato['Persona']['documento'];
			$ordenes[$idx]['OrdenDescuento']['persona_apenom'] = $dato['Persona']['apellido'].", ".$dato['Persona']['nombre'];
			$ordenes[$idx]['OrdenDescuento']['importe_actual'] = $dato['OrdenDescuento']['importe_cuota'];
			$ordenes[$idx]['OrdenDescuento']['valor_unidad_actual'] = $valorActual;
			$ordenes[$idx]['OrdenDescuento']['unidades'] = $dato[0]['unidades'];
			$ordenes[$idx]['OrdenDescuento']['valor_unidad_nuevo'] = $valorNuevo;
			$ordenes[$idx]['OrdenDescuento']['importe_nuevo'] = (isset($dato[0]['valor_nuevo']) ? $dato[0]['valor_nuevo'] : $dato['OrdenDescuento']['importe_cuota']);
		}

		return $ordenes;

	}


	function anularOrden($id,$motivo=null,$situacion='MUTUSICUANUL',$periodoHasta = NULL){

		$periodoHasta = (empty($periodoHasta) ? date('Ym') : $periodoHasta);

		$ordenDto = $this->read(null,$id);
		parent::begin();
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
		if(!empty($ordenDto['OrdenDescuentoCuota'])){
			foreach($ordenDto['OrdenDescuentoCuota'] as $cuota){
				$saldo = $oCuota->getSaldo($cuota['id']);
// 				if(round($saldo,2) == round($cuota['importe'],2)){
				if(round($saldo,2) != 0){
					$oCuota->id = $cuota['id'];
					if(!$oCuota->saveField('estado','B')){
						parent::rollback();
						return false;
					}
					if(!$oCuota->saveField('situacion',$situacion)){
						parent::rollback();
						return false;
					}
					if(!$oCuota->saveField('user_modified',(isset($_SESSION["NAME_USER_LOGON_SIGEM"]) ? $_SESSION["NAME_USER_LOGON_SIGEM"] : 'APLICACION_SERVER'))){
						parent::rollback();
						return false;
					}
					if(!$oCuota->saveField('modified',date('Y-m-d H:i:s'))){
						parent::rollback();
						return false;
					}
				}
			}
		}
		$ordenDto['OrdenDescuento']['periodo_hasta'] = ($ordenDto['OrdenDescuento']['permanente'] == 1 ? $periodoHasta : NULL);
		$ordenDto['OrdenDescuento']['activo'] = 0;
    $ordenDto['OrdenDescuento']['user_modified'] = (isset($_SESSION["NAME_USER_LOGON_SIGEM"]) ? $_SESSION["NAME_USER_LOGON_SIGEM"] : 'APLICACION_SERVER');
    $ordenDto['OrdenDescuento']['modified'] = date('Y-m-d H:i:s');

		$ordenDto['OrdenDescuento']['observaciones'] .= "<br/>".$motivo;
		if(!$this->save($ordenDto)){
			parent::rollback();
			return false;
		}
		parent::commit();
		return true;

	}

	function activarOrden($id,$cuotasIds=array()){
		$ordenDto = $this->read(null,$id);
		// debug($id);
		// exit;
		parent::begin();

		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
		
		if(!empty($ordenDto['OrdenDescuentoCuota'])){

			foreach($ordenDto['OrdenDescuentoCuota'] as $cuota){

				if(empty($cuotasIds) || in_array($cuota['id'],$cuotasIds)){

					$saldo = $oCuota->getSaldo($cuota['id']);

					if($cuota['estado'] == 'B'){
						$oCuota->id = $cuota['id'];
						if(!$oCuota->saveField('estado','A')){
							parent::rollback();
							return false;
						}
						if(!$oCuota->saveField('situacion','MUTUSICUMUTU')){
							parent::rollback();
							return false;
						}
						if(!$oCuota->saveField('user_modified',(isset($_SESSION["NAME_USER_LOGON_SIGEM"]) ? $_SESSION["NAME_USER_LOGON_SIGEM"] : 'APLICACION_SERVER'))){
							parent::rollback();
							return false;
						}
						if(!$oCuota->saveField('modified',date('Y-m-d H:i:s'))){
							parent::rollback();
							return false;
						}
					}
				}
			}
		}
		if(!$ordenDto['OrdenDescuento']['activo']){
			$ordenDto['OrdenDescuento']['activo'] = 1;
			$ordenDto['OrdenDescuento']['periodo_hasta'] = null;
			$ordenDto['OrdenDescuento']['user_modified'] = (isset($_SESSION["NAME_USER_LOGON_SIGEM"]) ? $_SESSION["NAME_USER_LOGON_SIGEM"] : 'APLICACION_SERVER');
			$ordenDto['OrdenDescuento']['modified'] = date('Y-m-d H:i:s');
			$ordenDto['OrdenDescuento']['observaciones'] .= "*** ACTIVADA  EL " .date('d/m/Y H:i:s') . " POR " . $_SESSION['NAME_USER_LOGON_SIGEM']." ***";
			if(!$this->save($ordenDto)){
				parent::rollback();
				return false;
			}
		}

		parent::commit();
		return true;

	}


    function getPersonaByOrdenDescuento($id){
        $orden = $this->read('socio_id',$id);
        if(empty($orden)) return null;
        App::import('Model','pfyj.Socio');
        $oSOCIO = new Socio();
        $persona = $oSOCIO->getPersonaBySocioID($orden['OrdenDescuento']['socio_id']);
        return $persona;
    }


    function getOrdenDtoCuotaSocialBySocioID($socio_id){
		App::import('model','pfyj.Socio');
		$oSOCIO = new Socio();
        $orden = $oSOCIO->getOrdenDtoCuotaSocial($socio_id);
        return $orden;
    }

    function getOrdenDtoCuotaSocialByPersonaID($persona_id){
		App::import('model','pfyj.Socio');
		$oSOCIO = new Socio();
        $orden = $oSOCIO->getOrdenDtoCuotaSocialByPersonaID($persona_id);
        return $orden;
    }

    function reprograma_orden2($id,$periodo_inicio){
        $sql = "select id,periodo,nro_cuota,@rownum := @rownum + 1 as numrow,
                date_format(DATE_ADD(STR_TO_DATE(concat('$periodo_inicio','01'),'%Y%m%d'),
                interval @rownum - 1 month),'%Y%m') as periodo_nuevo
                from orden_descuento_cuotas OrdenDescuentoCuota,
                (SELECT @rownum := 0) r
                where OrdenDescuentoCuota.orden_descuento_id = $id
                and OrdenDescuentoCuota.importe > ifnull((
                select sum(importe) from orden_descuento_cobro_cuotas cc
                where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
                ,0);";
        $datos = $this->query($sql);
        if(!empty($datos)){
            App::import('Model', 'Mutual.OrdenDescuentoCuota');
            $oCUOTA = new OrdenDescuentoCuota();
            foreach($datos as $dato){
                $oCUOTA->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
                $cuota = $oCUOTA->read(null,$dato['OrdenDescuentoCuota']['id']);
                $cuota['OrdenDescuentoCuota']['periodo_origen'] = $cuota['OrdenDescuentoCuota']['periodo'];
                $cuota['OrdenDescuentoCuota']['periodo'] = $dato[0]['periodo_nuevo'];
                if(!$oCUOTA->save($cuota)) return false;
            }
            $ordenDto = $this->read(null,$id);
            $ordenDto['OrdenDescuento']['reprogramada'] = 1;
            return $this->save($ordenDto);
        }
    }


    function getCuotasAVencer($id,$periodoControl){
        $sql = "select OrdenDescuentoCuota.*,
                OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= '$periodoControl') as saldo
                 from orden_descuento_cuotas OrdenDescuentoCuota
                where OrdenDescuentoCuota.orden_descuento_id = $id
                and OrdenDescuentoCuota.estado NOT IN ('B','D')
                and OrdenDescuentoCuota.periodo > '$periodoControl'
                and OrdenDescuentoCuota.importe > (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= '$periodoControl');";
        $datos = $this->query($sql);
        return $datos;
    }
    
    
    function getOrdenFullInfo($id,$periodo=null) {
        
        
        
        $sql = "select 
                    OrdenDescuento.* 
                    ,lpad(OrdenDescuento.id,7,0) nro_print
                    ,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) tipo_nro
                    ,lpad(OrdenDescuento.cuotas,2,0) cuotas_print
                    ,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero,' - ',Producto.concepto_1) recibo_detalle
                    
                    -- datos del solicitante
                    ,concat('DNI ',Persona.documento, ' - ',Persona.apellido,', ',Persona.nombre) beneficiario
                    ,concat(Persona.apellido,', ',Persona.nombre) beneficiario_apenom
                    ,concat('DNI - ',Persona.documento) beneficiario_tdocndoc
                    ,'DNI' beneficiario_tdoc
                    ,Persona.documento beneficiario_ndoc
                    ,concat('#',Socio.id,'| ALTA: ',date_format(Socio.fecha_alta,'%d-%m-%Y'),' | CATEGORIA: ',SocioCategoria.concepto_1,' | CALIF: ',ifnull(SocioCalificacion.concepto_1,'***'),' (',Socio.fecha_calificacion,')') beneficiario_socio
                    ,ifnull(SocioCalificacion.concepto_1,'***') ultima_calificacion
                    ,Persona.cuit_cuil beneficiario_cuit_cuil
                    ,Persona.nombre_conyuge beneficiario_conyuge
                    ,case length(Persona.cuit_cuil) when 11 then concat(left(Persona.cuit_cuil,2),'-',substr(Persona.cuit_cuil,3,8),'-',right(Persona.cuit_cuil,2)) else null end beneficiario_cuit_cuil_pick
                    ,upper(concat(Persona.calle,' ',if(Persona.numero_calle <> 0,Persona.numero_calle,''),' - ',Persona.localidad,' (CP ',Persona.codigo_postal,')'	,if(Persona.provincia_id is not null,concat(' - ',Provincia.nombre),''))) beneficiario_domicilio
                    ,concat('CEL.: ',Persona.telefono_movil,' | TEL: ',Persona.telefono_fijo,' | OTRO: ',Persona.telefono_referencia,' (Ref.: ',ifnull(Persona.persona_referencia,''),') | EMAIL: ',ifnull(Persona.e_mail,'')) beneficiario_telefonos
                    ,concat('EST. CIVIL: ',ifnull(EstadoCivil.concepto_1,''),' | SEXO: ',ifnull(Persona.sexo,''),' | NACIMIENTO: ',date_format(Persona.fecha_nacimiento,'%d-%m-%Y'),' | EDAD: ',TIMESTAMPDIFF(YEAR,Persona.fecha_nacimiento,now()),' | CUIT/L: ',Persona.cuit_cuil) beneficiario_complementarios
                    
                    -- datos del beneficio
                    ,PersonaBeneficio.activo beneficio_activo
                    
                    ,concat(Organismo.concepto_1,' - ',Empresa.concepto_1,'|'
                    	,if(PersonaBeneficio.codigo_empresa <> PersonaBeneficio.turno_pago, PersonaBeneficio.turno_pago,'')
                        ,'|',PersonaBeneficio.codigo_reparticion
                        ,'|CBU:',PersonaBeneficio.cbu, if(PersonaBeneficio.activo = 0,' ** NO VIGENTE **','')) beneficio_str
                    
                    
                    ,ifnull(Banco.nombre,'') beneficio_banco
                    ,PersonaBeneficio.acuerdo_debito beneficio_acuerdo_debito
                    ,PersonaBeneficio.banco_id beneficio_banco_id
                    ,PersonaBeneficio.nro_sucursal beneficio_sucursal
                    ,PersonaBeneficio.nro_cta_bco beneficio_cuenta
                    ,PersonaBeneficio.cbu beneficio_cbu
                    ,PersonaBeneficio.fecha_ingreso beneficio_ingreso
                    ,TIMESTAMPDIFF(YEAR,PersonaBeneficio.fecha_ingreso,now()) beneficio_antiguedad
                    ,PersonaBeneficio.nro_legajo beneficio_legajo
                    ,PersonaBeneficio.tipo beneficio_tipo_beneficio
                    ,PersonaBeneficio.nro_beneficio beneficio_nro_beneficio
                    ,PersonaBeneficio.nro_ley beneficio_nro_ley
                    ,PersonaBeneficio.sub_beneficio beneficio_sub_beneficio
                    ,case left(right(PersonaBeneficio.codigo_beneficio,4),2) when 77 then concat(PersonaBeneficio.tipo,PersonaBeneficio.nro_ley,PersonaBeneficio.nro_beneficio,PersonaBeneficio.sub_beneficio) else '' end beneficio_cjpc_nro
                    ,PersonaBeneficio.codigo_reparticion beneficio_codigo_reparticion
                    ,PersonaBeneficio.tarjeta_titular beneficio_tarjeta_titular
                    ,PersonaBeneficio.tarjeta_numero beneficio_tarjeta_numero
                    ,PersonaBeneficio.codigo_beneficio organismo
                    ,Organismo.concepto_1 organismo_desc
                    ,if(PersonaBeneficio.codigo_empresa <> PersonaBeneficio.turno_pago, PersonaBeneficio.turno_pago, PersonaBeneficio.codigo_empresa) turno
                    ,concat(Empresa.concepto_1,' - ',right(PersonaBeneficio.turno_pago,5)) turno_desc
                    
                    -- datos del proveedor
                    ,concat(Proveedor.razon_social_resumida,' - ',Producto.concepto_1) proveedor_producto
                    ,Proveedor.razon_social_resumida proveedor
                    ,Proveedor.pagare_blank proveedor_pagare_blank
                    ,Proveedor.direccion_pagare proveedor_pagare_direccion
                    ,Proveedor.razon_social proveedor_full_name
                    ,Proveedor.cuit proveedor_cuit
                    ,concat(Proveedor.calle,' ',Proveedor.numero_calle,' - ',Proveedor.piso,' - Of. ',Proveedor.dpto) proveedor_domicilio
                    ,concat(Proveedor.codigo_postal,' - ',Proveedor.localidad) proveedor_localidad
                    ,Proveedor.telefono_fijo proveedor_telefono
                    ,Proveedor.reasignable proveedor_reasignable
                    ,Producto.concepto_1 producto
                    
                    from orden_descuentos OrdenDescuento 
                    inner join proveedores Proveedor on Proveedor.id = OrdenDescuento.proveedor_id
                    inner join global_datos Producto on Producto.id = OrdenDescuento.tipo_producto
                    -- 
                    inner join socios Socio on Socio.id = OrdenDescuento.socio_id
                    inner join personas Persona on Persona.id = Socio.persona_id
                    left join provincias Provincia on Provincia.id = Persona.provincia_id
                    left join global_datos EstadoCivil on EstadoCivil.id = Persona.estado_civil
                    
                    left join global_datos SocioCategoria on SocioCategoria.id = Socio.categoria
                    left join global_datos SocioCalificacion on SocioCalificacion.id = Socio.calificacion
                    
                    inner join persona_beneficios PersonaBeneficio on PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id
                    inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                    left join bancos Banco on Banco.id = PersonaBeneficio.banco_id
                    inner join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa 
                    --
                    
                    where OrdenDescuento.id = $id";

        $result = $this->query($sql);
        if(empty($result)) {return null;}
        
        $orden = array();
        
        $ordenDescuento = Set::extract("{n}.OrdenDescuento",$result);
        foreach ($ordenDescuento[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $camposCalculados = Set::extract("{n}.0",$result);
        foreach ($camposCalculados[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $persona = Set::extract("{n}.Persona",$result);
        foreach ($persona[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $beneficio = Set::extract("{n}.PersonaBeneficio",$result);
        foreach ($beneficio[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $proveedor = Set::extract("{n}.Proveedor",$result);
        foreach ($proveedor[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $producto = Set::extract("{n}.Producto",$result);
        foreach ($producto[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $organismo = Set::extract("{n}.Organismo",$result);
        foreach ($organismo[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }

        $orden['OrdenDescuento']['total_letras'] = parent::num2letras($orden['OrdenDescuento']['importe_total']);
        $orden['OrdenDescuento']['total_cuota_letras'] = parent::num2letras($orden['OrdenDescuento']['importe_cuota']);
        $orden['OrdenDescuento']['cantidad_cuota_letras'] = parent::num2letras($orden['OrdenDescuento']['cuotas'],false);
        
        return $orden;
        
    }

}
?>
