<?php
class Solicitud extends V1AppModel{

	var $name = 'Solicitud';
	var $primaryKey = 'nro_solicitud';
	var $useTable = 'solicitudes';

//	var $actsAs   = array('Mutual.transaction');

	var $orden_cancelacion_id = 0;
	var $orden_dto_credito_id = 0;
	var $orden_dto_seguro_id = 0;
	var $orden_dto_csocial_id = 0;
	var $nro_socio = 0;


	function guardarFormaPago($datos){
		// actualizo en la orden de descuento y orden de descuento cuotas el nro de credito del proveedor
		App::import('Model', 'Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento(null);
		$nro_credito_proveedor = $datos['Solicitud']['nro_credito_proveedor'];

		$oOrden->bindModel(array('hasMany' => array('OrdenDescuentoCuota')));

		$orden = $oOrden->getOrdenByNumero($datos['Solicitud']['nro_solicitud'],"EXPTE",($datos['Solicitud']['carga_directa'] == 1 ? "MUTUPROD0011" :"MUTUPROD0001"),true);

		if(empty($orden)) return false;

		$orden['OrdenDescuento']['nro_referencia_proveedor'] = $nro_credito_proveedor;

		if(!empty($orden['OrdenDescuentoCuota'])){
			foreach($orden['OrdenDescuentoCuota'] as $idx => $cuota){
				$cuota['nro_referencia_proveedor'] = $nro_credito_proveedor;
				$orden['OrdenDescuentoCuota'][$idx] = $cuota;
			}
		}
		if(!$oOrden->saveAll($orden)) return false;
		return $this->save($datos);
	}


	function bySocio($socio_id,$tDoc=null,$nDoc=null){
		$conditions = array();
		if(!empty($socio_id) && $socio_id != 0) $conditions['Solicitud.id_persona'] = $socio_id;
		if(!empty($tDoc) && !empty($nDoc) && MODULO_V1):
			//saco el id de la persona en base al tipo y numero de documento
			App::import('Model', 'V1.PersonaV1');
			$oPV1 = new PersonaV1(null);
			$personaV1 = $oPV1->getByTdocNdoc($tDoc,$nDoc);
			$conditions['Solicitud.id_persona'] = $personaV1['PersonaV1']['id_persona'];
		endif;
		$conditions['Solicitud.estado <>'] = 15;
		$sol = $this->find('all',array('conditions' => $conditions,'order' => array('Solicitud.fecha_solicitud')));
		return $this->armaDatos($sol);
	}

	function getSolicitud($nro_solicitud){
		$solicitud = $this->read(null,$nro_solicitud);
		$solicitud = $this->armaDatoSolicitud($solicitud);
		return $solicitud;
//		$solicitud = $this->armaDatos(array($solicitud));
//		return $solicitud[0];
	}


	function armaDatoSolicitud($solicitud){
		App::import('Model', 'V1.Beneficio');
		$this->Beneficio = new Beneficio(null);

		App::import('Model', 'V1.Productor');
		$this->Productor = new Productor(null);

		App::import('Model', 'V1.Producto');
		$this->Producto = new Producto(null);

		App::import('Model', 'V1.SolicitudEstado');
		$this->SolicitudEstado = new SolicitudEstado(null);

		App::import('Model', 'V1.SolicitudCancelaciones');
		$this->SolicitudCancelaciones = new SolicitudCancelaciones(null);

		App::import('model','V1.BancoSucursalV1');
		$this->BancoSucursalV1 = new BancoSucursalV1(null);


		App::import('model','V1.SolicitudCancelacionOrden');
		$this->SolicitudCancelacionOrden = new SolicitudCancelacionOrden(null);


		//cargo el id proveedor y el id beneficio de la v2

		App::import('model','Proveedores.Proveedor');
		$oProV2 = new Proveedor(null);

		App::import('model','Pfyj.PersonaBeneficio');
		$oBenV2 = new PersonaBeneficio(null);


		// agrego el modelo cancelacion ordenes
		$this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');

		if(isset($solicitud['personas_beneficios']['codigo_beneficio'])){
			$glb = parent::getGlobal('concepto','XXTO'.$solicitud['personas_beneficios']['codigo_beneficio']);
			$solicitud['personas_beneficios']['codigo_beneficio_desc'] = $glb['Tglobal']['concepto'];
		}


		if(isset($solicitud['Solicitud']['codigo_banco'])){
			$banco = parent::getBanco('banco',$solicitud['Solicitud']['codigo_banco']);
			$solicitud['Solicitud']['banco'] = $banco['BancoV1']['banco'];
		}

		$solicitud['Solicitud']['dato_giro'] = '';
		if(isset($solicitud['Solicitud']['codigo_fpago'])){
			$glb = parent::getGlobal('concepto','LEPA'.$solicitud['Solicitud']['codigo_fpago']);
			$solicitud['Solicitud']['forma_pago'] = $glb['Tglobal']['concepto'];
			if($solicitud['Solicitud']['codigo_fpago']=='0003'){
				$suc = $this->BancoSucursalV1->read(null,$solicitud['Solicitud']['id_sucursal']);
				if(!empty($suc)){
					$solicitud['Solicitud']['nro_sucursal'] = $suc['BancoSucursalV1']['nro_sucursal'];
					$solicitud['Solicitud']['dato_giro'] = $solicitud['Solicitud']['banco'] . " SUC (".$suc['BancoSucursalV1']['nro_sucursal'].' - ' . $suc['BancoSucursalV1']['sucursal'] .")";
				}else if(!empty($solicitud['Solicitud']['id_sucursal'])){
					$solicitud['Solicitud']['dato_giro'] = $solicitud['Solicitud']['banco'] . " SUC (".$solicitud['Solicitud']['id_sucursal'].")";
				}
			}
		}


		if(isset($solicitud['Solicitud']['estado'])){
			$estado = $this->SolicitudEstado->read('descripcion',$solicitud['Solicitud']['estado']);
			$solicitud['Solicitud']['estado_descripcion'] = $estado['SolicitudEstado']['descripcion'];
		}

		if(isset($solicitud['Solicitud']['id_beneficio'])){
			$ben = $this->Beneficio->getBeneficio($solicitud['Solicitud']['id_beneficio']);
			$solicitud['Beneficio'] = $ben['Beneficio'];
		}

		if(isset($solicitud['Solicitud']['codigo_productor'])){
			$productor = $this->Productor->read(null,$solicitud['Solicitud']['codigo_productor']);
			$solicitud['Productor'] = $productor['Productor'];
		}

		if(isset($solicitud['Solicitud']['codigo_producto'])){
			$producto = $this->Producto->read(null,$solicitud['Solicitud']['codigo_producto']);
			$solicitud['Producto'] = $producto['Producto'];

			$solicitud['Solicitud']['proveedor_producto'] = "#" . $producto['Producto']['codigo_producto'] ." - " . $producto['Producto']['Proveedor']['razon_social'] . " - " . $producto['Producto']['descripcion'];
			$solicitud['Solicitud']['proveedor'] = $producto['Producto']['Proveedor']['razon_social'];
			$solicitud['Solicitud']['cuit_proveedor'] = $producto['Producto']['Proveedor']['cuit'];
		}

		//cargo si tiene cancelaciones
		$solicitud['Solicitud']['total_cancelado'] = $this->SolicitudCancelaciones->totalCanceladoBySolicitud($solicitud['Solicitud']['nro_solicitud']);
		$solicitud['Cancelaciones'] = $this->SolicitudCancelaciones->bySolicitud($solicitud['Solicitud']['nro_solicitud']);

		//saco el concepto del seguro
		$glb = parent::getGlobal('concepto','CUTCCS');
		$solicitud['Solicitud']['cuota_social_concepto'] = $glb['Tglobal']['concepto'];

		$glb = parent::getGlobal('concepto','CUTCRC');
		$solicitud['Solicitud']['cuota_seguro_concepto'] = $glb['Tglobal']['concepto'];


		if(isset( $solicitud['Solicitud']['monto_cuota']) && $solicitud['Solicitud']['monto_seguro'] && $solicitud['Solicitud']['monto_cuota_social']) $solicitud['Solicitud']['cuota_total'] = $solicitud['Solicitud']['monto_cuota'] + $solicitud['Solicitud']['monto_seguro'] + $solicitud['Solicitud']['monto_cuota_social'];

		//traigo los datos del proveedor y benecio de la v2
		$solicitud['Solicitud']['proveedor_id_v2'] = 0;

		if(isset($producto['Producto']['Proveedor'])){

//			$proveedorV2 = $oProV2->findAllByCuit($producto['Producto']['Proveedor']['codigo_proveedor']);
			$proveedorV2 = $oProV2->read('situacion_cuota,estado_cuota',$producto['Producto']['Proveedor']['idr']);

//			if(isset($proveedorV2[0]['Proveedor']['id']))$solicitud['Solicitud']['proveedor_id_v2'] = $proveedorV2[0]['Proveedor']['id'];
			$solicitud['Solicitud']['proveedor_id_v2'] = $producto['Producto']['Proveedor']['idr'];

			if(empty($proveedorV2)):
				$solicitud['Solicitud']['proveedor_id_v2_situacion_cuota'] = "MUTUSICUMUTU";
				$solicitud['Solicitud']['proveedor_id_v2_estado_cuota'] = "A";
			else:
				$solicitud['Solicitud']['proveedor_id_v2_situacion_cuota'] = $proveedorV2['Proveedor']['situacion_cuota'];
				$solicitud['Solicitud']['proveedor_id_v2_estado_cuota'] = $proveedorV2['Proveedor']['estado_cuota'];
			endif;

			$solicitud['Solicitud']['proveedor_id_v2_situacion_cuota'] = (empty($solicitud['Solicitud']['proveedor_id_v2_situacion_cuota']) ? 'MUTUSICUMUTU' : $solicitud['Solicitud']['proveedor_id_v2_situacion_cuota']);
			$solicitud['Solicitud']['proveedor_id_v2_estado_cuota'] = (empty($solicitud['Solicitud']['proveedor_id_v2_estado_cuota']) ? 'A' : $solicitud['Solicitud']['proveedor_id_v2_estado_cuota']);

//			if(isset($proveedorV2['Proveedor']['situacion_cuota']))$solicitud['Solicitud']['proveedor_id_v2_situacion_cuota'] = $proveedorV2['Proveedor']['situacion_cuota'];
//			else $solicitud['Solicitud']['proveedor_id_v2_situacion_cuota'] = "MUTUSICUMUTU";
//			if(isset($proveedorV2['Proveedor']['estado_cuota']))$solicitud['Solicitud']['proveedor_id_v2_estado_cuota'] = $proveedorV2['Proveedor']['estado_cuota'];
//			else $solicitud['Solicitud']['proveedor_id_v2_estado_cuota'] = "A";

		}
		if(isset($solicitud['Solicitud']['id_beneficio'])){
			$beneficioV2 = $oBenV2->getByIDR($solicitud['Solicitud']['id_beneficio']);
			if(!empty($beneficioV2))$solicitud['Solicitud']['persona_beneficio_id_v2'] = $beneficioV2['PersonaBeneficio']['id'];
		}


		# Esta funcion no exite, define otra funcion dentro del Modelo Cancelacion Ordenes.
		$ordenesCancelacion = $this->SolicitudCancelacionOrden->findAllByNroSolicitud($solicitud['Solicitud']['nro_solicitud']);
//		$ordenesCancelacion = $this->CancelacionOrden->getCancelacionByNroSolicitud($solicitud['Solicitud']['nro_solicitud']);
		$solicitud['SolicitudCancelacionOrden'] = $ordenesCancelacion;

		//armo los montos
		if(isset($solicitud['Solicitud']['en_mano']))$solicitud['Solicitud']['monto_a_percibir'] = $solicitud['Solicitud']['en_mano'] - $solicitud['Solicitud']['total_cancelado'];
		if(isset($solicitud['Solicitud']['monto_cuota']))$solicitud['Solicitud']['total_cuota_pura'] = $solicitud['Solicitud']['cuotas'] * $solicitud['Solicitud']['monto_cuota'];
		if(isset($solicitud['Solicitud']['cuota_total']))$solicitud['Solicitud']['total_credito'] = $solicitud['Solicitud']['cuotas'] * $solicitud['Solicitud']['cuota_total'];


		App::import('model','V1.PersonaV1');
		$oPERSONA_V2 = new PersonaV1(null);

		$personaV1 = $oPERSONA_V2->getPersona($solicitud['Solicitud']['id_persona']);
		$solicitud['PersonaV1'] = Set::extract("PersonaV1",$personaV1);

		App::import('model','V1.Beneficio');
		$oBENEFICIO_V2 = new Beneficio(null);

		$oBENEFICIO_V2->actualizarV2($solicitud['PersonaV1']['idr_fv2'],$solicitud['Beneficio']);

		//PRODUCTOR
		$solicitud['Solicitud']['productor_nombre_corto'] = $solicitud['Productor']['nombre_corto'];
		$solicitud['Solicitud']['productor_documento'] = $solicitud['Productor']['documento'];
		$solicitud['Solicitud']['productor_nombre'] = $solicitud['Productor']['apellido'].", " . $solicitud['Productor']['nombre'];
		$glb = parent::getGlobal("concepto", "FILI" . $solicitud['Productor']['zona']);
		$solicitud['Solicitud']['productor_filial'] = $glb['Tglobal']['concepto'];
		$glb = parent::getGlobal("concepto", "TIVE" . $solicitud['Productor']['tipo']);
		$solicitud['Solicitud']['productor_tipo'] = $glb['Tglobal']['concepto'];
		$glb = parent::getGlobal("concepto", "VEVE" . $solicitud['Productor']['vendedor']);
		$solicitud['Solicitud']['productor_vendedor'] = $glb['Tglobal']['concepto'];

		//armo el string del solicitante
		//PERSTPDC0001
		$glb = parent::getGlobal('concepto',"TPDC".substr($solicitud['PersonaV1']['tipo_documento'],8,4));
		$solicitud['Solicitud']['solicitante'] = $glb['Tglobal']['concepto'] . " " . $solicitud['PersonaV1']['documento'] . " - " . $solicitud['PersonaV1']['apellido'] . ", ". $solicitud['PersonaV1']['nombre'];

		//si viene el id del proveedor que se reasigna armo los datos
		$solicitud['Solicitud']['reasignar_proveedor_razon_social'] = "";
		if($solicitud['Solicitud']['reasignar_proveedor_id']!=0) $solicitud['Solicitud']['reasignar_proveedor_razon_social'] = $oProV2->getRazonSocial($solicitud['Solicitud']['reasignar_proveedor_id']);

		//$oProV2

		if(isset($solicitud['Solicitud']['recibo_id'])):
			$solicitud['Solicitud']['recibo_link'] = '';
			if($solicitud['Solicitud']['recibo_id'] > 0):
				$solicitud['Solicitud']['recibo_link'] = $this->getReciboLink($solicitud['Solicitud']['recibo_id']);
			endif;
		endif;

		if(isset($solicitud['Solicitud']['orden_pago_id'])):
			$solicitud['Solicitud']['orden_pago_link'] = '';
			if($solicitud['Solicitud']['orden_pago_id'] > 0):
				$solicitud['Solicitud']['orden_pago_link'] = $this->getOrdenPagoLink($solicitud['Solicitud']['orden_pago_id']);
			endif;
		endif;

		return $solicitud;
	}

	function armaDatos($resultados){

		#modelos relacionados

		App::import('Model', 'V1.Beneficio');
		$this->Beneficio = new Beneficio(null);

		App::import('Model', 'V1.Productor');
		$this->Productor = new Productor(null);

		App::import('Model', 'V1.Producto');
		$this->Producto = new Producto(null);

		App::import('Model', 'V1.SolicitudEstado');
		$this->SolicitudEstado = new SolicitudEstado(null);

		App::import('Model', 'V1.SolicitudCancelaciones');
		$this->SolicitudCancelaciones = new SolicitudCancelaciones(null);

		App::import('model','V1.BancoSucursalV1');
		$this->BancoSucursalV1 = new BancoSucursalV1(null);


		App::import('model','V1.SolicitudCancelacionOrden');
		$this->SolicitudCancelacionOrden = new SolicitudCancelacionOrden(null);


		//cargo el id proveedor y el id beneficio de la v2

		App::import('model','Proveedores.Proveedor');
		$oProV2 = new Proveedor(null);

		App::import('model','Pfyj.PersonaBeneficio');
		$oBenV2 = new PersonaBeneficio(null);

		foreach($resultados as $clave => $valor){

			if(isset($valor['personas_beneficios']['codigo_beneficio'])){
				$glb = parent::getGlobal('concepto','XXTO'.$valor['personas_beneficios']['codigo_beneficio']);
				$resultados[$clave]['personas_beneficios']['codigo_beneficio_desc'] = $glb['Tglobal']['concepto'];
			}

			if(isset($valor['Solicitud']['codigo_banco'])){
				$banco = parent::getBanco('banco',$valor['Solicitud']['codigo_banco']);
				$resultados[$clave]['Solicitud']['banco'] = $banco['BancoV1']['banco'];
			}

			$resultados[$clave]['Solicitud']['dato_giro'] = '';
			if(isset($valor['Solicitud']['codigo_fpago'])){
				$glb = parent::getGlobal('concepto','LEPA'.$valor['Solicitud']['codigo_fpago']);
				$resultados[$clave]['Solicitud']['forma_pago'] = $glb['Tglobal']['concepto'];
				if($valor['Solicitud']['codigo_fpago']=='0003'){
					$suc = $this->BancoSucursalV1->read(null,$valor['Solicitud']['id_sucursal']);
					if(!empty($suc)){
						$resultados[$clave]['Solicitud']['dato_giro'] = $resultados[$clave]['Solicitud']['banco'] . " SUC (".$suc['BancoSucursalV1']['nro_sucursal'].' - ' . $suc['BancoSucursalV1']['sucursal'] .")";
					}
				}
			}


			if(isset($valor['Solicitud']['estado'])){
				$estado = $this->SolicitudEstado->read('descripcion',$valor['Solicitud']['estado']);
				$resultados[$clave]['Solicitud']['estado_descripcion'] = $estado['SolicitudEstado']['descripcion'];
			}

			if(isset($valor['Solicitud']['id_beneficio'])){
				$ben = $this->Beneficio->getBeneficio($valor['Solicitud']['id_beneficio']);
				$resultados[$clave]['Beneficio'] = $ben['Beneficio'];
			}

			if(isset($valor['Solicitud']['codigo_productor'])){
				$productor = $this->Productor->read(null,$valor['Solicitud']['codigo_productor']);
				$resultados[$clave]['Productor'] = $productor['Productor'];
			}

			if(isset($valor['Solicitud']['codigo_producto'])){
				$producto = $this->Producto->read(null,$valor['Solicitud']['codigo_producto']);
				$resultados[$clave]['Producto'] = $producto['Producto'];

				$resultados[$clave]['Solicitud']['proveedor_producto'] = "#" . $producto['Producto']['codigo_producto'] ." - " . $producto['Producto']['Proveedor']['razon_social'] . " - " . $producto['Producto']['descripcion'];
				$resultados[$clave]['Solicitud']['proveedor'] = $producto['Producto']['Proveedor']['razon_social'];
			}

			//cargo si tiene cancelaciones
			$resultados[$clave]['Solicitud']['total_cancelado'] = $this->SolicitudCancelaciones->totalCanceladoBySolicitud($valor['Solicitud']['nro_solicitud']);
			$resultados[$clave]['Cancelaciones'] = $this->SolicitudCancelaciones->bySolicitud($valor['Solicitud']['nro_solicitud']);

			//saco el concepto del seguro
			$glb = parent::getGlobal('concepto','CUTCCS');
			$resultados[$clave]['Solicitud']['cuota_social_concepto'] = $glb['Tglobal']['concepto'];

			$glb = parent::getGlobal('concepto','CUTCRC');
			$resultados[$clave]['Solicitud']['cuota_seguro_concepto'] = $glb['Tglobal']['concepto'];


			if(isset( $valor['Solicitud']['monto_cuota']) && $valor['Solicitud']['monto_seguro'] && $valor['Solicitud']['monto_cuota_social']) $resultados[$clave]['Solicitud']['cuota_total'] = $valor['Solicitud']['monto_cuota'] + $valor['Solicitud']['monto_seguro'] + $valor['Solicitud']['monto_cuota_social'];

			//traigo los datos del proveedor y benecio de la v2
			$resultados[$clave]['Solicitud']['proveedor_id_v2'] = 0;

			if(isset($producto['Producto']['Proveedor'])){
//				$proveedorV2 = $oProV2->findAllByCuit($producto['Producto']['Proveedor']['codigo_proveedor']);
				$proveedorV2 = $oProV2->read(null,$producto['Producto']['Proveedor']['idr']);
//				if(isset($proveedorV2[0]['Proveedor']['id']))$resultados[$clave]['Solicitud']['proveedor_id_v2'] = $proveedorV2[0]['Proveedor']['id'];
				$resultados[$clave]['Solicitud']['proveedor_id_v2'] = $producto['Producto']['Proveedor']['idr'];

				if(empty($proveedorV2)):
					$resultados[$clave]['Solicitud']['proveedor_id_v2_situacion_cuota'] = "MUTUSICUMUTU";
					$resultados[$clave]['Solicitud']['proveedor_id_v2_estado_cuota'] = "A";
				else:
					$resultados[$clave]['Solicitud']['proveedor_id_v2_situacion_cuota'] = $proveedorV2['Proveedor']['situacion_cuota'];
					$resultados[$clave]['Solicitud']['proveedor_id_v2_estado_cuota'] = $proveedorV2['Proveedor']['estado_cuota'];
				endif;

				$resultados[$clave]['Solicitud']['proveedor_id_v2_situacion_cuota'] = (empty($resultados[$clave]['Solicitud']['proveedor_id_v2_situacion_cuota']) ? 'MUTUSICUMUTU' : $resultados[$clave]['Solicitud']['proveedor_id_v2_situacion_cuota']);
				$resultados[$clave]['Solicitud']['proveedor_id_v2_estado_cuota'] = (empty($resultados[$clave]['Solicitud']['proveedor_id_v2_estado_cuota']) ? 'A' : $resultados[$clave]['Solicitud']['proveedor_id_v2_estado_cuota']);


//				if(isset($proveedorV2[0]['Proveedor']['situacion_cuota']))$resultados[$clave]['Solicitud']['proveedor_id_v2_situacion_cuota'] = $proveedorV2[0]['Proveedor']['situacion_cuota'];
//				else $resultados[$clave]['Solicitud']['proveedor_id_v2_situacion_cuota'] = "MUTUSICUMUTU";
//				if(isset($proveedorV2[0]['Proveedor']['estado_cuota']))$resultados[$clave]['Solicitud']['proveedor_id_v2_estado_cuota'] = $proveedorV2[0]['Proveedor']['estado_cuota'];
//				else $resultados[$clave]['Solicitud']['proveedor_id_v2_estado_cuota'] = "A";

			}
			if(isset($valor['Solicitud']['id_beneficio'])){
				$beneficioV2 = $oBenV2->getByIDR($valor['Solicitud']['id_beneficio']);
				if(!empty($beneficioV2))$resultados[$clave]['Solicitud']['persona_beneficio_id_v2'] = $beneficioV2['PersonaBeneficio']['id'];
			}

			$ordenesCancelacion = $this->SolicitudCancelacionOrden->findAllByNroSolicitud($valor['Solicitud']['nro_solicitud']);
			$resultados[$clave]['SolicitudCancelacionOrden'] = $ordenesCancelacion;

			//armo los montos
			if(isset($resultados[$clave]['Solicitud']['en_mano']))$resultados[$clave]['Solicitud']['monto_a_percibir'] = $resultados[$clave]['Solicitud']['en_mano'] - $resultados[$clave]['Solicitud']['total_cancelado'];
			if(isset($resultados[$clave]['Solicitud']['monto_cuota']))$resultados[$clave]['Solicitud']['total_cuota_pura'] = $resultados[$clave]['Solicitud']['cuotas'] * $resultados[$clave]['Solicitud']['monto_cuota'];
			if(isset($resultados[$clave]['Solicitud']['cuota_total']))$resultados[$clave]['Solicitud']['total_credito'] = $resultados[$clave]['Solicitud']['cuotas'] * $resultados[$clave]['Solicitud']['cuota_total'];



			if(isset($valor['Solicitud']['recibo_id'])):
				$resultados[$clave]['Solicitud']['recibo_link'] = '';
				if($valor['Solicitud']['recibo_id'] > 0):
					$resultados[$clave]['Solicitud']['recibo_link'] = $this->getReciboLink($valor['Solicitud']['recibo_id']);
				endif;
			endif;

			if(isset($valor['Solicitud']['orden_pago_id'])):
				$resultados[$clave]['Solicitud']['orden_pago_link'] = '';
				if($valor['Solicitud']['orden_pago_id'] > 0):
					$resultados[$clave]['Solicitud']['orden_pago_link'] = $this->getOrdenPagoLink($valor['Solicitud']['orden_pago_id']);
				endif;
			endif;


			#CARGO LOS DATOS DE REASIGNACION
			$valor['Solicitud']['reasignar_proveedor_razon_social'] = "";
			if(isset($valor['Solicitud']['reasignar_proveedor_id']) && !empty($valor['Solicitud']['reasignar_proveedor_id'])) $valor['Solicitud']['reasignar_proveedor_razon_social'] = $oProV2->getRazonSocial($valor['Solicitud']['reasignar_proveedor_id']);
			$resultados[$clave]['Solicitud']['reasignar_proveedor_razon_social'] = $valor['Solicitud']['reasignar_proveedor_razon_social'];

		}

		return $resultados;
	}

	function aGenerarExpediente($data){

		if(empty($data['Solicitud']['fecha_d'])) $data['Solicitud']['fecha_d'] = date('d/m/Y');
		if(empty($data['Solicitud']['fecha_h'])) $data['Solicitud']['fecha_h'] = date('d/m/Y');

		$fecha_d = $this->fechaMySql($data['Solicitud']['fecha_d'],true);
		$fecha_h = $this->fechaMySql($data['Solicitud']['fecha_h'],true);

		$codigo_beneficio = substr($data['Solicitud']['codigo_beneficio'],4,4);

		$db = & ConnectionManager::getDataSource($this->useDbConfig);
		$useDB = $db->config['database'];

		//QUERY MODIFICADA BRUNO PARA TRAER DATOS DE LA TARJETA
		$sql = "SELECT Solicitud.nro_solicitud,Solicitud.fecha_solicitud,Solicitud.en_mano,
				Solicitud.cuotas,Solicitud.codigo_productor,Solicitud.estado,Solicitud.fecha_estado,
				Solicitud.recibo_id, Solicitud.orden_pago_id, personas.id_persona,
				personas.apellido,personas.nombre,personas_beneficios.codigo_beneficio,personas_beneficios.nro_beneficio,
				personas_beneficios.nro_ley,personas_beneficios.cbu,personas_beneficios.codigo_reparticion,
				personas_beneficios.codigo_empresa,personas_beneficios.tarjeta_numero,personas_beneficios.tarjeta_titular,personas_beneficios.tarjeta_debito,proveedores.razon_social,
				proveedores_productos.descripcion as producto,proveedores_productos.codigo_producto,productores.nombre_corto as productor,
				productores_vendedores.nombre_corto as vendedor,solicitud_codigo_estados.descripcion,bancos.banco
				FROM $useDB.solicitudes as Solicitud
				LEFT JOIN $useDB.personas ON Solicitud.id_persona = personas.id_persona
				LEFT JOIN $useDB.personas_beneficios ON Solicitud.id_beneficio = personas_beneficios.id_beneficio
				LEFT JOIN $useDB.proveedores_productos ON Solicitud.codigo_producto = proveedores_productos.codigo_producto
				LEFT JOIN $useDB.proveedores ON proveedores_productos.codigo_proveedor = proveedores.codigo_proveedor
				LEFT JOIN $useDB.productores ON Solicitud.codigo_productor = productores.codigo_vendedor
				LEFT JOIN $useDB.productores_vendedores ON Solicitud.codigo_subvendedor = productores_vendedores.codigo_subvendedor
				LEFT JOIN $useDB.solicitud_codigo_estados ON Solicitud.estado = solicitud_codigo_estados.codigo
				LEFT JOIN $useDB.bancos ON personas_beneficios.codigo_banco = bancos.codigo_banco
				WHERE personas.apellido like '%".$data['Persona']['apellido']."%' AND personas.nombre like '%".$data['Persona']['nombre']."%'
				AND Solicitud.nro_solicitud like '%".$data['Solicitud']['nro_solicitud_aprox']."%' AND Solicitud.estado = 12 AND
				Solicitud.fecha_estado between '$fecha_d' and '$fecha_h' AND
				personas_beneficios.codigo_beneficio like '%".$codigo_beneficio."%'
				ORDER BY personas.apellido, personas.nombre LIMIT 0, 100";
		$solicitudes = $this->query($sql);
		$solicitudes = $this->armaDatos($solicitudes);
		return $solicitudes;
	}


	function generarExpediente($data){

		#INICIO UNA TRANSACCION
		parent::begin();

		$data = $this->__calculaTotales($data);
		$data = $this->__setComisiones($data);

		//leo la solicitud para ver si no le cargaron una reasignacion de proveedor
		$solCtrl = $this->read("reasignar_proveedor_id",$data['Solicitud']['nro_solicitud']);

		if(!empty($solCtrl['Solicitud']['reasignar_proveedor_id'])) $data['Solicitud']['reasignar_proveedor_id'] = $solCtrl['Solicitud']['reasignar_proveedor_id'];


		#controlo si es con cancelacion y si vienen chequeadas
		if($data['Solicitud']['con_cancelacion'] == 1 && !isset($data['Solicitud']['cancelacion_orden_id'])){
//			parent::rollback();
//			return array(1,"Debe indicar que Orden o cuales Ordenes de Cancelación Emitidas se vinculan a la presente.");
		}

		#####################################################################################################################
		#ACTUALIZO DATOS TARJETA DEBITO EN SIGEM (BRUNO)
		#####################################################################################################################

		if(!$this->__actualizarTarjetaDebitoV2($data)){
			parent::rollback();
			return array(1,"Se produjo un error al actualizar datos tarjeta debito en sigem :: PROCESO DE GENERACION SUSPENDIDO");
		}

		#####################################################################################################################
		#VERIFICO SI ES SOCIO NUEVO
		#####################################################################################################################
		if($data['Solicitud']['socio_id'] == 0 || empty($data['Solicitud']['socio_id'])){
			$data['Solicitud']['socio_id'] = $this->__generaSocio($data);
			if($data['Solicitud']['socio_id'] == 0 || empty($data['Solicitud']['socio_id'])){
				parent::rollback();
				return array(1,"Se produjo un error al generar los datos del Socio :: PROCESO DE GENERACION SUSPENDIDO");
			}
		}else{
			#VERIFICAR SI NO ESTA ACTIVO
			if(!$this->__isSocioActivo($data['Solicitud']['socio_id'])){
				if(!$this->__reactivarSocio($data)){
					parent::rollback();
					return array(1,"Se produjo un error al intentar reactivar el Socio :: PROCESO DE GENERACION SUSPENDIDO");
				}
			}
		}

		#####################################################################################################################
		#GENERO LA ORDEN DE DESCUENTO PARA EL CREDITO
		#####################################################################################################################
		if(!$this->__generarOrdenDtoCredito($data)){
			parent::rollback();
			return array(1,"Se produjo un error al generar la Orden de Descuento para el Cr�dito :: PROCESO DE GENERACION SUSPENDIDO");
		}

		#####################################################################################################################
		#GENERO LA ORDEN DE DESCUENTO PARA EL SEGURO
		#####################################################################################################################
		if(!$this->__generarOrdenDtoSeguro($data)){
			parent::rollback();
			return array(1,"Se produjo un error al generar la Orden de Descuento para el Seguro :: PROCESO DE GENERACION SUSPENDIDO");
		}

		#####################################################################################################################
		# SI TIENE CANCELACIONES PROCESO LAS CANCELACIONES
		#####################################################################################################################
		if(!$this->__procesoCancelaciones($data)){
			parent::rollback();
			return array(1,"Se produjo un error al procesar las Ordenes de Cancelacion.:: PROCESO DE GENERACION SUSPENDIDO");
		}

		#####################################################################################################################
		# GRABO LOS DATOS DE LA LIQUIDACION DE LA SOLICITUD (MEDIO, FECHA PAGO Y NRO CREDITO PROVEEDOR)
		#####################################################################################################################
		if(!$this->__datosLiquidacion($data)){
			parent::rollback();
			return array(1,"Se produjo un error al Cargar los datos de la Liquidación de la Solicitud :: PROCESO DE GENERACION SUSPENDIDO");
		}

		#####################################################################################################################
		# GRABO EL ID DEL BENEFICIO DE LA SOLICITUD COMO IDR EN PERSONABENEFICIOS (V2)
		#####################################################################################################################
		if(!$this->__actualizarIDRBeneficio($data)){
			parent::rollback();
			return array(1,"Se produjo un error al actualizar la referencia del Beneficio. :: PROCESO DE GENERACION SUSPENDIDO");
		}

		#####################################################################################################################
		# CAMBIO EL ESTADO DE LA SOLICITUD Y GENERO EL EVENTO EN EL HISTORIAL DE LA SOLICITUD
		#####################################################################################################################
		if(!$this->__marcarAprobada($data)){
			parent::rollback();
			return array(1,"Se produjo un error al actualizar el estado de la Solicitud. :: PROCESO DE GENERACION SUSPENDIDO");
		}


// 		//VERIFICAR DATOS
// 		DEBUG($data);
// 		parent::rollback();
// 		exit;


		#TODO OK CONFIRMO LA TRANSACCION
		return parent::commit();
//		parent::rollback();
//		return array(1,"ERROR ADRIAN");

	}

	/**
	 * funcion que calcula los totales del credito
	 * @param $data
	 * @return unknown_type
	 */
	function __calculaTotales($data){
		$solicitud = $this->read(null,$data['Solicitud']['nro_solicitud']);
		$data['Solicitud']['importe_cuota_credito'] = $solicitud['Solicitud']['monto_cuota'];
		$data['Solicitud']['importe_total_credito'] = $solicitud['Solicitud']['monto_cuota'] * $solicitud['Solicitud']['cuotas'];
		$data['Solicitud']['importe_cuota_seguro'] = $solicitud['Solicitud']['monto_seguro'];
		$data['Solicitud']['importe_total_seguro'] = $solicitud['Solicitud']['monto_seguro'] * $solicitud['Solicitud']['cuotas'];
		$data['Solicitud']['cuotas'] = $solicitud['Solicitud']['cuotas'];
		return $data;
	}

	/**
	 * verifica si el socio esta activo
	 * @param unknown_type $socio_id
	 */
	function __isSocioActivo($socio_id){
		App::import('Model', 'Pfyj.Socio');
		$oSocio = new Socio();
		return $oSocio->isActivo($socio_id);
	}

	/**
	 * REACTIVA EL SOCIO
	 * Genera una nueva solicitud de afiliacion, graba el historico y actualiza el importe de la cuota social
	 *
	 * @param $data
	 */
	function __reactivarSocio($datos){
		App::import('Model', 'Pfyj.Socio');
		$oSocio = new Socio();
		$vtos = $this->__armaVtos($datos);
		return $oSocio->reactivar($datos['Solicitud']['socio_id'],$vtos['inicia_en'],$vtos['vto_primer_cuota_socio']);
	}

	/**
	 * Actualiza el IDR en la persona_beneficios
	 * @param $data
	 */
	function __actualizarIDRBeneficio($data){
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBeneficio = new PersonaBeneficio();
		$beneficio = $oBeneficio->read(null,$data['Solicitud']['persona_beneficio_id_v2']);
		$beneficio['PersonaBeneficio']['idr'] = $data['Solicitud']['id_beneficio'];
		$beneficio['PersonaBeneficio']['idr_persona'] = $data['Solicitud']['persona_id'];
		if(!$oBeneficio->guardar($beneficio,false))return false;
		return true;
	}

	/**
	 * Actualiza Tarjeta Debito en persona_beneficios (bruno)
	 * @param $data
	 */

	function __actualizarTarjetaDebitoV2($data){
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBeneficio = new PersonaBeneficio();
		$beneficio = $oBeneficio->read(null,$data['Solicitud']['persona_beneficio_id_v2']);
		$beneficio['PersonaBeneficio']['tarjeta_numero'] = $data['Solicitud']['tarjeta_numero'];
		$beneficio['PersonaBeneficio']['tarjeta_titular'] = $data['Solicitud']['tarjeta_titular'];
		$beneficio['PersonaBeneficio']['tarjeta_debito'] = $data['Solicitud']['tarjeta_debito'];
		if(!$oBeneficio->actualizarTarjetaV2($beneficio,false))return false;
		return true;
	}

	/**
	 * genera el socio y le carga la orden de descuento para la cuota social
	 * @param $data
	 * @return unknown_type
	 */
	function __generaSocio($datos){

		$vtos = $this->__armaVtos($datos);

		App::import('Model', 'Pfyj.SocioSolicitud');
		$oSolSoc = new SocioSolicitud();

		#GENERO UNA SOLICITUD DE AFILIACION
		$afiliacion = array('SocioSolicitud' => array(
			'tipo_solicitud' => 'A',
			'aprobada' => 0,
			'persona_id' => $datos['Solicitud']['persona_id'],
			'persona_beneficio_id' => $datos['Solicitud']['persona_beneficio_id_v2'],
			'fecha' => date('Y-m-d'),
			'periodo_ini' => $vtos['inicia_en'],
			'observaciones' => "GENERADA POR APROBACION DE EXPEDIENTE " . $datos['Solicitud']['nro_solicitud'],
			'primer_vto_proveedor' => $vtos['vto_primer_cuota_proveedor'],
			'primer_vto_socio' => $vtos['vto_primer_cuota_socio'],
		));

		if(!$oSolSoc->save($afiliacion)) return false;

		#APRUEBO LA SOLICITUD DE AFILIACION
		$id = $oSolSoc->getLastInsertID();
		$afiliacion = $oSolSoc->read(null,$id);
		return $oSolSoc->aprobar($afiliacion,$vtos);
	}


	/**
	 * genera la orden de descuento (cabecera y detalle) para el credito
	 * @param $data
	 * @return unknown_type
	 */
	function __generarOrdenDtoCredito($datos){
		$data = $datos;
		App::import('Model', 'Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento(null);

		$TIPO_PRODUCTO_SIGEM = ($data['Solicitud']['carga_directa'] == 1 ? "MUTUPROD0011" : ( isset($data['Solicitud']['codigo_producto_sigem']) && !empty($data['Solicitud']['codigo_producto_sigem']) ? $data['Solicitud']['codigo_producto_sigem'] : "MUTUPROD0001"));

		//verificar que no exista
		$orden = $oOrden->getOrdenByNumero($data['Solicitud']['nro_solicitud'],"EXPTE",$TIPO_PRODUCTO_SIGEM,false,true,true,true);

		if(!empty($orden)){

			$this->orden_dto_credito_id = $orden['OrdenDescuento']['id'];
			//ACTUALIZO EL NRO DE CREDITO DEL PROVEEDOR
			if(isset($orden['OrdenDescuento']['id']) && $orden['OrdenDescuento']['id'] != 0){

//				$oOrden->id = $orden['OrdenDescuento']['id'];
				$orden['OrdenDescuento']['nro_referencia_proveedor'] = $data['Solicitud']['nro_credito_proveedor'];

				$datosOrden = $data;
				if(!empty($orden['OrdenDescuento']['fecha'])){
					$datosOrden['Solicitud']['fecha_operacion_pago'] = $orden['OrdenDescuento']['fecha'];
				}else{
					$orden['OrdenDescuento']['fecha'] = (!empty($data['Solicitud']['fecha_operacion_pago_fix']) ? $data['Solicitud']['fecha_operacion_pago_fix'] : parent::armaFecha($data['Solicitud']['fecha_operacion_pago']));
				}
				$vtos1 = $this->__armaVtos($datosOrden);
				if(empty($orden['OrdenDescuento']['primer_vto_socio'])){
					$orden['OrdenDescuento']['primer_vto_socio'] = $vtos1['vto_primer_cuota_socio'];
				}
				if(empty($orden['OrdenDescuento']['primer_vto_proveedor'])){
					$orden['OrdenDescuento']['primer_vto_proveedor'] = $vtos1['vto_primer_cuota_proveedor'];
				}
				return $oOrden->save($orden);

			}

		}
		$vtos = $this->__armaVtos($data);


		$TIPO_ORDEN_SIGEM = $oOrden->GlobalDato('concepto_3', $TIPO_PRODUCTO_SIGEM);
		$TIPO_ORDEN_SIGEM = (empty($TIPO_ORDEN_SIGEM) ? 'EXPTE' : $TIPO_ORDEN_SIGEM);

		$OrdenDto = array('OrdenDescuento' => array(
						'fecha' => (!empty($data['Solicitud']['fecha_operacion_pago_fix']) ? $data['Solicitud']['fecha_operacion_pago_fix'] : parent::armaFecha($data['Solicitud']['fecha_operacion_pago'])),
						'tipo_orden_dto' => $TIPO_ORDEN_SIGEM,
						'numero' => $data['Solicitud']['nro_solicitud'],
						'tipo_producto' => $TIPO_PRODUCTO_SIGEM,
						'socio_id' => $data['Solicitud']['socio_id'],
						'persona_beneficio_id' => $data['Solicitud']['persona_beneficio_id_v2'],
						'proveedor_id' => ($data['Solicitud']['reasignar_proveedor_id'] != 0 ? $data['Solicitud']['reasignar_proveedor_id'] : $data['Solicitud']['proveedor_id_v2']),
						'mutual_producto_id' => 0,
						'periodo_ini' => $vtos['inicia_en'],
						'importe_cuota' => $data['Solicitud']['importe_cuota_credito'],
						'importe_total' => $data['Solicitud']['importe_total_credito'],
						'primer_vto_socio' => $vtos['vto_primer_cuota_socio'],
						'primer_vto_proveedor' => $vtos['vto_primer_cuota_proveedor'],
						'cuotas' => $data['Solicitud']['cuotas'],
						'permanente' => 0,
						'nro_referencia_proveedor' => (!empty($data['Solicitud']['nro_credito_proveedor_fix']) ? $data['Solicitud']['nro_credito_proveedor_fix'] : $data['Solicitud']['nro_credito_proveedor']),
						'observaciones' => $data['Solicitud']['observaciones'],
						'comision_cobranza' => $data['Solicitud']['comision_cobranza'],
						'comision_colocacion' => $data['Solicitud']['comision_colocacion'],
						'productor_id' => $data['Solicitud']['codigo_productor'],

						'productor_ref' => $this->getNombreCortoProductor($data['Solicitud']['nro_solicitud']),
						'importe_solicitado' => $data['Solicitud']['en_mano'],
						'importe_capital' => $data['Solicitud']['solicitado'],
					));
		$oOrden->id = 0;
		$situacionCuota = $data['Solicitud']['proveedor_id_v2_situacion_cuota'];
		$estadoCuota = $data['Solicitud']['proveedor_id_v2_estado_cuota'];
		$grabado = $oOrden->generarOrden($OrdenDto,$situacionCuota,$estadoCuota,false);
		$this->orden_dto_credito_id = $oOrden->getLastInsertID();

		//VALIDAR ID CON NUMERO DE ORDEN DE DESCUENTO
		$validar = $oOrden->getTipoAndNro($this->orden_dto_credito_id,false);
		if($validar[0] == 'EXPTE' && $validar[1] == $data['Solicitud']['nro_solicitud'] && $grabado) return true;
		else return false;
//
//		return $grabado;

	}

	/**
	 * genera la orden de descuento (cabecera y detalle) para el seguro
	 * busca de la global para el codigo MUTUPROD0002 (entero_1) cual es el ID del proveedor que tiene el seguro
	 * @param $datos
	 * @return unknown_type
	 */
	function __generarOrdenDtoSeguro($datos){

		App::import('Model', 'Mutual.OrdenDescuento');
		$oOrdenSeg = new OrdenDescuento(null);

		$orden = $oOrdenSeg->getOrdenByNumero($datos['Solicitud']['nro_solicitud'],"EXPTE","MUTUPROD0002",false,true,true,true);
		if(!empty($orden)){
			$this->orden_dto_seguro_id = $orden['OrdenDescuento']['id'];
			return true;
		}

		$vtos = $this->__armaVtos($datos);

		$TIPO_PRODUCTO_SIGEM = "MUTUPROD0002";
		$TIPO_ORDEN_SIGEM = $oOrdenSeg->GlobalDato('concepto_3', $TIPO_PRODUCTO_SIGEM);
		$TIPO_ORDEN_SIGEM = (empty($TIPO_ORDEN_SIGEM) ? 'EXPTE' : $TIPO_ORDEN_SIGEM);

		$glb = parent::getGlobalDato('entero_1',"MUTUPROD0002");
		$OrdenDto = array('OrdenDescuento' => array(
						'fecha' => (!empty($datos['Solicitud']['fecha_operacion_pago_fix']) ? $datos['Solicitud']['fecha_operacion_pago_fix'] : parent::armaFecha($datos['Solicitud']['fecha_operacion_pago'])),
						'tipo_orden_dto' => $TIPO_ORDEN_SIGEM,
						'numero' => $datos['Solicitud']['nro_solicitud'],
						'tipo_producto' => $TIPO_PRODUCTO_SIGEM,
						'socio_id' => $datos['Solicitud']['socio_id'],
						'persona_beneficio_id' => $datos['Solicitud']['persona_beneficio_id_v2'],
						'proveedor_id' => $glb['GlobalDato']['entero_1'],
						'mutual_producto_id' => 0,
						'periodo_ini' => $vtos['inicia_en'],
						'importe_cuota' => $datos['Solicitud']['importe_cuota_seguro'],
						'importe_total' => $datos['Solicitud']['importe_total_seguro'],
						'primer_vto_socio' => $vtos['vto_primer_cuota_socio'],
						'primer_vto_proveedor' => $vtos['vto_primer_cuota_proveedor'],
						'cuotas' => $datos['Solicitud']['cuotas'],
						'permanente' => 0,
						'nro_referencia_proveedor' => "",
						'observaciones' => $datos['Solicitud']['observaciones'],
						'comision_cobranza' => $datos['Solicitud']['comision_cobranza'],
						'comision_colocacion' => $datos['Solicitud']['comision_colocacion'],
						'productor_id' => $datos['Solicitud']['codigo_productor'],
						'productor_ref' => $this->getNombreCortoProductor($datos['Solicitud']['nro_solicitud']),
		));
		$oOrdenSeg->id = 0;
		if($datos['Solicitud']['importe_cuota_seguro'] != 0 && $datos['Solicitud']['importe_total_seguro'] != 0){
			$situacionCuota = $datos['Solicitud']['proveedor_id_v2_situacion_cuota'];
			$estadoCuota = $datos['Solicitud']['proveedor_id_v2_estado_cuota'];
			$grabado = $oOrdenSeg->generarOrden($OrdenDto,$situacionCuota,$estadoCuota,false);
			$this->orden_dto_seguro_id = $oOrdenSeg->getLastInsertID();
			//VALIDAR ID CON NUMERO DE ORDEN DE DESCUENTO
			$validar = $oOrdenSeg->getTipoAndNro($this->orden_dto_seguro_id,false);
			if($validar[0] == 'EXPTE' && $validar[1] == $datos['Solicitud']['nro_solicitud'] && $grabado) return true;
			else return false;
//			return $grabado;
		}else{
			return true;
		}
	}


	/**
	 * arma los vencimientos y el periodo de inicio del credito y el seguro
	 * @param $datos
	 * @return unknown_type
	 */
	function __armaVtos($datos){
		App::import('Model', 'Proveedores.ProveedorVencimiento');
		$oVto = new ProveedorVencimiento(null);
		$proveedorId = $datos['Solicitud']['proveedor_id_v2'];
		$beneficioId = $datos['Solicitud']['persona_beneficio_id_v2'];
		$fechaPago = (!empty($datos['Solicitud']['fecha_operacion_pago_fix']) ? $datos['Solicitud']['fecha_operacion_pago_fix'] : parent::armaFecha($datos['Solicitud']['fecha_operacion_pago']));
		//si el beneficio corresponde a ANSES calcular los vtos en base a la fecha del cupon
		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBEN = new PersonaBeneficio(null);
		$organismo = substr($oBEN->getCodigoOrganismo($beneficioId),8,2);
		if($organismo == 66) $fechaPago = parent::armaFecha($datos['Solicitud']['fecha_cupon_anses']);

		return $oVto->calculaVencimiento($proveedorId,$beneficioId,$fechaPago);
	}


	/**
	 * procesa las cancelaciones
	 * @param $data
	 * @return unknown_type
	 */
	function __procesoCancelaciones($data){

		$process = true;

		if($data['Solicitud']['con_cancelacion'] == 1 && isset($data['Solicitud']['cancelacion_orden_id'])){

			App::import('Model', 'Mutual.CancelacionOrden');
			$oCan = new CancelacionOrden(null);

			$fechaPago = (!empty($data['Solicitud']['fecha_operacion_pago_fix']) ? $data['Solicitud']['fecha_operacion_pago_fix'] : parent::armaFecha($data['Solicitud']['fecha_operacion_pago']));

			foreach($data['Solicitud']['cancelacion_orden_id'] as $orden_cancelacion_id => $check){
				#GRABAR EN LA V1 (CANCELACIONES) EL ID DE LA ORDEN DE CANCELACION DE LA V2
				#analizar la generacion de ND / NC en la cta cte del proveedor y del socio (opciones en la carga de
				#los datos del pago de la cancelacion)
				if(!$oCan->_procesar($orden_cancelacion_id,$this->orden_dto_credito_id,$fechaPago)){
					$process = false;
					break;
				}
			}
		}

		return $process;
	}

	/**
	 * marcar la solicitud aprobada
	 * @param $datos
	 * @return unknown_type
	 */
	function __marcarAprobada($datos){

		$nro_solicitud = $datos['Solicitud']['nro_solicitud'];
		App::import('Model', 'V1.SolicitudEstadoHist');
		$oEstado = new SolicitudEstadoHist(null);

		//armo el mensaje
		$obs = "**** APROBADA **** <br>";
		$obs .= "ORDEN DTO. CREDITO:$this->orden_dto_credito_id - EXPTE #$nro_solicitud <br>";
		$obs .= "ORDEN DTO. FONDO ASISTENCIA:$this->orden_dto_seguro_id - EXPTE #$nro_solicitud ";
		if($this->orden_dto_csocial_id != 0){
			$obs .= "<br>ORDEN DTO. CUOTA SOCIAL:$this->orden_dto_csocial_id - CMUTU #$this->nro_socio <br>";
		}

		$oEstado->grabarHistorial($nro_solicitud,14,$obs);
		$estado_id = $oEstado->getLastInsertID();
		$this->id = $nro_solicitud;

		$solicitud = $this->read(null,$nro_solicitud);

		$solicitud['Solicitud']['fecha_solicitud'] = $solicitud['Solicitud']['fecha_solicitud'];
		$solicitud['Solicitud']['estado'] = 14;
		$solicitud['Solicitud']['fecha_estado'] = date('Y-m-d');
		$solicitud['Solicitud']['id_ultimo_hist'] = $estado_id;
		$solicitud['Solicitud']['nro_credito_proveedor'] = (!empty($datos['Solicitud']['nro_credito_proveedor_fix']) ? $datos['Solicitud']['nro_credito_proveedor_fix'] : $datos['Solicitud']['nro_credito_proveedor']);
		$solicitud['Solicitud']['fecha_operacion_pago'] = (!empty($datos['Solicitud']['fecha_operacion_pago_fix']) ? $datos['Solicitud']['fecha_operacion_pago_fix'] : parent::armaFecha($datos['Solicitud']['fecha_operacion_pago']));
		$solicitud['Solicitud']['fecha_cupon_anses'] = (isset($datos['Solicitud']['fecha_cupon_anses']) ? parent::armaFecha($datos['Solicitud']['fecha_cupon_anses']) : null);
		$solicitud['Solicitud']['nro_operacion_pago'] = (isset($datos['Solicitud']['nro_operacion']) ? $datos['Solicitud']['nro_operacion'] : '');
		$solicitud['Solicitud']['orden_descuento_id'] = $this->orden_dto_credito_id;
		$solicitud['Solicitud']['seguro_orden_descuento_id'] = $this->orden_dto_seguro_id;

		return parent::save($solicitud);

	}

	/**
	 * graba los datos de la liquidacion de una solicitud
	 * @param $data
	 * @return unknown_type
	 */
	function __datosLiquidacion($data){

		$this->id = $data['Solicitud']['nro_solicitud'];

		$data['Solicitud']['fecha_operacion_pago'] = (!empty($data['Solicitud']['fecha_operacion_pago_fix']) ? $data['Solicitud']['fecha_operacion_pago_fix'] : parent::armaFecha($data['Solicitud']['fecha_operacion_pago']));

		if(isset($data['Solicitud']['banco_id'])) $data['Solicitud']['codigo_banco'] = $data['Solicitud']['banco_id'];
		else if(isset($data['Solicitud']['codigo_fpago']) && $data['Solicitud']['codigo_fpago'] == '0001') $data['Solicitud']['codigo_banco'] = '';

		$data['Solicitud']['nro_operacion_pago'] = (isset($data['Solicitud']['nro_operacion']) ? $data['Solicitud']['nro_operacion'] : null);
//		$data['Solicitud']['nro_credito_proveedor'] = (empty($data['Solicitud']['nro_credito_proveedor']) ? $data['Solicitud']['nro_solicitud'] : $data['Solicitud']['nro_credito_proveedor']);
		$data['Solicitud']['nro_credito_proveedor'] = (empty($data['Solicitud']['nro_credito_proveedor']) ? (!empty($data['Solicitud']['nro_credito_proveedor_fix']) ? $data['Solicitud']['nro_credito_proveedor_fix'] : $data['Solicitud']['nro_solicitud'])  : $data['Solicitud']['nro_credito_proveedor']);
		//(!empty($datos['Solicitud']['nro_credito_proveedor_fix']) ? $datos['Solicitud']['nro_credito_proveedor_fix'] : $datos['Solicitud']['nro_credito_proveedor'])

		return parent::save($data);
	}

	/**
	 * busca y setea en la solicitud las comisiones del producto (para
	 * grabarlas en la orden de descuento)
	 * @param $data
	 * @return unknown_type
	 */
	function __setComisiones($data){
		App::import('Model', 'V1.Producto');
		$oProducto = new Producto(null);
		$producto = $oProducto->read(null,$data['Solicitud']['codigo_producto']);
		$data['Solicitud']['comision_cobranza'] = $producto['Producto']['comision_cobranza'];
		$data['Solicitud']['comision_colocacion'] = $producto['Producto']['comision_colocacion'];
		return $data;
	}

	/**
	 * devuelve una persona en base al idr
	 * @param $idr
	 * @return unknown_type
	 */
	function __personaByIdr($idr){
		App::import('Model', 'Pfyj.Persona');
		$oPersona = new Persona(null);
		$persona = $oPersona->getByIdr($idr);
		return 	$persona;
	}

	/**
	 * devuelve el beneficio
	 * @param $id
	 * @return unknown_type
	 */
	function __beneficio($id){
		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio(null);
		$beneficio = $oBen->getBeneficio($id);
		return 	$beneficio;
	}

	function __beneficioByOrdenDto($ordenDto){
		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio(null);
		$beneficio = $oBen->getByOrdenDto($ordenDto);
		return 	$beneficio;

	}


	/**
	 * devuelve orden de descuento para un tipo, numero y producto especificado
	 * @param $numero
	 * @param $tipo
	 * @param $tipoProducto
	 * @return unknown_type
	 */
	function __ordenDto($numero,$tipo,$tipoProducto=""){
		App::import('Model', 'Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento();
		$ord = $oOrden->getOrdenByNumero($numero,$tipo,$tipoProducto);
		return $ord;
	}

	function __ordenesDescuentoByNumeroSolicitud($numero){
		App::import('Model', 'Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento();
		$ord = $oOrden->getOrdenesByNumero($numero);
		return $ord;
	}

	/**
	 * devuelve la solicitud con la persona, beneficio y ordenes de descuento asociadas
	 * para pasarlas a la impresion de la caratula
	 * @param $nro_expediente
	 * @return unknown_type
	 */
	function getSolicitudPDF($nro_expediente){
		$solicitud = $this->getSolicitud($nro_expediente);
//		$solicitud['PersonaV2'] = $this->__personaByIdr($solicitud['Solicitud']['id_persona']);
		App::import('Model', 'Pfyj.Persona');
		$oPersona = new Persona(null);
		$oPersona->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPersona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);
		if(empty($persona)) return null;
		$solicitud['PersonaV2'] = $persona;
		if(isset($solicitud['Solicitud']['persona_beneficio_id_v2'])){
//			$solicitud['BeneficioV2'] = $this->__beneficio($solicitud['Solicitud']['persona_beneficio_id_v2']);
			$solicitud['BeneficioV2'] = $this->__beneficioByOrdenDto($solicitud['Solicitud']['orden_descuento_id']);
			if(empty($solicitud['BeneficioV2'])) return null;
		}
		$solicitud['OrdenDescuento'] = $this->__ordenesDescuentoByNumeroSolicitud($solicitud['Solicitud']['nro_solicitud']);
		if(empty($solicitud['OrdenDescuento'])) return null;
		return $solicitud;
	}


	function getNombreCortoProductor($nro_solicitud){
		App::import('Model', 'V1.Productor');
		$oPRODUCTOR = new Productor(null);
		$solicitud = $this->read('codigo_productor',$nro_solicitud);
		$productor = $oPRODUCTOR->read("nombre_corto",$solicitud['Solicitud']['codigo_productor']);
		return $productor['Productor']['nombre_corto'];
	}


	function getSolicitudesByCuitProveedor($proveedor,$estado=9,$offset = 0, $limit = 50,$fechaDesde = null,$fechaHasta=null){
		// $cuit = trim($cuit);
		$estado = trim($estado);

		$fechaDesde = (empty($fechaDesde) ? "2000-01-01" : $fechaDesde);
		$fechaHasta = (empty($fechaHasta) ? date('Y-m-d') : $fechaHasta);

		$db = & ConnectionManager::getDataSource($this->useDbConfig);
		$useDB = $db->config['database'];
		$sql = "SELECT Solicitud.nro_solicitud
				FROM $useDB.solicitudes as Solicitud
				LEFT JOIN $useDB.personas ON personas.id_persona = Solicitud.id_persona
				LEFT JOIN $useDB.proveedores_productos ON proveedores_productos.codigo_producto = Solicitud.codigo_producto
				LEFT JOIN $useDB.proveedores ON proveedores_productos.codigo_producto = proveedores_productos.codigo_proveedor
				WHERE
					proveedores.codigo_proveedor = '".$proveedor['cuit']."' or
					ifnull(Solicitud.reasignar_proveedor_id,0) = " . $proveedor['id'] . "
					".(!empty($estado) ? " and Solicitud.estado = $estado " : "")."
					and Solicitud.fecha_solicitud between '$fechaDesde' and '$fechaHasta'
				ORDER BY personas.apellido, personas.nombre LIMIT $limit OFFSET $offset";
//		error_log($sql,3,"/home/adrian/Escritorio/ws.txt");
		$solicitudes = $this->query($sql);
		$datos = array();
		if(!empty($solicitudes)):
			foreach($solicitudes as $i => $solicitud):
				array_push($datos,$this->getSolicitud($solicitud['Solicitud']['nro_solicitud']));
			endforeach;
		endif;
		return $datos;
	}



	function getPeriodosProductoresLiquidados(){
		$periodos = array();
		$sql = "SELECT DISTINCT fecha,CONCAT(anio,periodo)AS periodo FROM productores_liquidacion ORDER BY CONCAT(anio,periodo) desc";
		$datos = $this->query($sql);
		if(empty($datos)) return $periodos;
		foreach($datos as $dato):
			$fechaCorte = $dato['productores_liquidacion']['fecha'];
			$periodo = $dato[0]['periodo'];
			$periodoDesc = parent::periodo($periodo,true);
			$periodos[$periodo] = $periodoDesc . " ($fechaCorte)";
		endforeach;
		return $periodos;
	}


	function getSolicitudesByProducto($codigoProducto,$estadoDesde=9,$estadoHasta=12,$soloNoReasignadas=true){
		$codigoProducto = trim($codigoProducto);
		$db = & ConnectionManager::getDataSource($this->useDbConfig);
		$useDB = $db->config['database'];
		$sql = "SELECT Solicitud.nro_solicitud
				FROM $useDB.solicitudes as Solicitud
				LEFT JOIN $useDB.personas ON Solicitud.id_persona = personas.id_persona
				LEFT JOIN $useDB.proveedores_productos ON Solicitud.codigo_producto = proveedores_productos.codigo_producto
				LEFT JOIN $useDB.proveedores ON proveedores_productos.codigo_proveedor = proveedores.codigo_proveedor
				WHERE
					Solicitud.codigo_producto = '$codigoProducto'
					".($soloNoReasignadas ? "and Solicitud.reasignar_proveedor_id = 0" : "")."
					and Solicitud.estado between $estadoDesde and $estadoHasta
				ORDER BY personas.apellido, personas.nombre";
		$solicitudes = $this->query($sql);
		$datos = array();
		if(!empty($solicitudes)):
			foreach($solicitudes as $i => $solicitud):
				array_push($datos,$this->getSolicitud($solicitud['Solicitud']['nro_solicitud']));
			endforeach;
		endif;
		return $datos;
	}


	function reasignarProveedorSolicitud($nroSolicitud,$proveedorId){
		$solicitud = $this->read(null,$nroSolicitud);
		//cargo el proveedor
		App::import('model','Proveedores.Proveedor');
		$oProV2 = new Proveedor(null);
		$solicitud['Solicitud']['reasignar_proveedor_id'] = $proveedorId;
		$solicitud['Solicitud']['reasignar_proveedor_razonsocial'] = $oProV2->getRazonSocialResumida($proveedorId);
		$solicitud['Solicitud']['reasigna_proveedor_fecha'] = date("Y-m-d");
		$solicitud['Solicitud']['reasigna_proveedor_user'] = (isset($_SESSION['NAME_USER_LOGON_SIGEM']) ? $_SESSION['NAME_USER_LOGON_SIGEM'] : 'APLICACION_SERVER');
		return $this->save($solicitud);
	}


	function anularReasignarProveedorSolicitud($nroSolicitud){
		$solicitud = $this->read(null,$nroSolicitud);
		$solicitud['Solicitud']['reasignar_proveedor_id'] = 0;
		$solicitud['Solicitud']['reasignar_proveedor_razonsocial'] = null;
		$solicitud['Solicitud']['reasigna_proveedor_fecha'] = null;
		$solicitud['Solicitud']['reasigna_proveedor_user'] = (isset($_SESSION['NAME_USER_LOGON_SIGEM']) ? $_SESSION['NAME_USER_LOGON_SIGEM'] : 'APLICACION_SERVER');
		return $this->save($solicitud);
	}


	function grabarOrdenPago($datos){
		$solicitud = $this->getSolicitud($datos['Solicitud']['id']);
		$this->Persona = $this->importarModelo('Persona', 'pfyj');
		$this->Persona->bindModel(array('hasOne' => array('Socio')));
//		$persona = $this->Persona->findAllByIdr($solicitud['Solicitud']['id_persona']);

		$persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);

		$this->OrdenPago = $this->importarModelo('Movimiento', 'proveedores');
		$this->OrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
		$datos['Movimiento']['fecha_pago'] = $this->armaFecha($datos['Movimiento']['fecha_pago']);
		$datos['Movimiento']['fecha_operacion'] = $datos['Movimiento']['fecha_pago'];
//		$datos['Movimiento']['fpago'] = $this->armaFecha($datos['Movimiento']['fpago']);
		$datos['Movimiento']['fpago'] = $datos['Movimiento']['fecha_pago'];
		$datos['Movimiento']['fvenc'] = $this->armaFecha($datos['Movimiento']['fvenc']);
		$datos['Movimiento']['nro_solicitud'] = $datos['Solicitud']['id'];

		$aDetalleSolicitud = array('id_persona' => $datos['Movimiento']['id_persona'], 'importe_pago' => $datos['Movimiento']['importe_pago']);
		$datos['Movimiento']['detalle_solicitud'] = array();
		array_push($datos['Movimiento']['detalle_solicitud'], $aDetalleSolicitud);

		$this->begin();
		if(!$this->OrdenPago->guardarOpago($datos, false)):
			$this->rollback();
			return false;
		endif;

		$datos['Solicitud']['orden_pago_id'] = $this->OrdenPagoDetalle->getOPagoBySolicitud($datos['Solicitud']['id']);
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;


		$this->commit();
		return true;
	}


	function getReciboLink($recibo_id=null){
		if(empty($recibo_id)) return '';
		$oRecibo = parent::importarModelo("Recibo","clientes");
		$recibo = $oRecibo->read(null, $recibo_id);
		return $recibo['Recibo']['tipo_documento'] . ' - ' . $recibo['Recibo']['sucursal'] . ' - ' . $recibo['Recibo']['nro_recibo'];
	}


	function getOrdenPagoLink($orden_pago_id=null){
		if(empty($orden_pago_id)) return '';
		$oOrdenPago = parent::importarModelo("OrdenPago","proveedores");
		$ordenPago = $oOrdenPago->read(null, $orden_pago_id);
		return $ordenPago['OrdenPago']['tipo_documento'] . ' - ' . $ordenPago['OrdenPago']['sucursal'] . ' - ' . str_pad($ordenPago['OrdenPago']['nro_orden_pago'],8,0,STR_PAD_LEFT);
	}


	function anularOrdenPago($nOrdenPago){
		$this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');

		if(!$this->OrdenPago->anular($nOrdenPago)):
			return false;
		endif;

		return true;
	}


	function grabarRecibo($datos){
		$solicitud = $this->getSolicitud($datos['Solicitud']['id']);
		$this->Persona = $this->importarModelo('Persona', 'pfyj');
		$this->Persona->bindModel(array('hasOne' => array('Socio')));
		$this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');
		$this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
		$this->Recibo = $this->importarModelo('Recibo', 'clientes');
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');


		# Preparo la cancelacion para ser recaudada.
        $datos['CancelacionOrden']['proveedor_origen_id'] = $solicitud['Producto']['Proveedor']['idr'];
        $datos['CancelacionOrden']['forma_pago'] = '';
        $datos['CancelacionOrden']['pendiente_rendicion_proveedor'] = 0;
        $datos['CancelacionOrden']['nro_cta_bco'] = '';
        $datos['CancelacionOrden']['nro_operacion'] = '';
        $datos['CancelacionOrden']['nro_recibo'] = '';
        $datos['CancelacionOrden']['fecha_imputacion'] = $datos['Recibo']['fecha_comprobante'];
        $datos['CancelacionOrden']['origen_proveedor_id'] = $solicitud['Producto']['Proveedor']['idr'];

        # Recaudo la cancelacion
		if(!$this->CancelacionOrden->recaudarByCaja($datos)):
			parent::notificar("NO SE RECAUDO LAS CANCELACIONES");
			return false;
		endif;

        $persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);

		if(empty($datos['Recibo']['observacion'])) $datos['Recibo']['observacion'] = $solicitud['Solicitud']['solicitante'] . ' ** Solic. ' . $solicitud['Solicitud']['nro_solicitud'];

		$datos['Recibo']['detalle_solicitud'] = array();

		$importe_cancela = 0;
		foreach($datos['CancelacionOrden']['id_check'] as $id => $valor):
			$orden = $this->CancelacionOrden->get($id);
			$importe_cancela += $orden['CancelacionOrden']['importe_proveedor'];
		endforeach;


		# Preparo el detalle del Recibo.
		$aDetalle = array('recibo_id' => 0, 'concepto' => $datos['Recibo']['observacion'], 'importe' => $datos['Recibo']['importe_cobro'] - $datos['Recibo']['aporte_socio'] + $importe_cancela);
		array_push($datos['Recibo']['detalle_solicitud'], $aDetalle);
		if(isset($datos['Recibo']['aporte_socio']) && $datos['Recibo']['aporte_socio'] > 0):
			$aDetalle = array('recibo_id' => 0, 'concepto' => 'APORTE SOCIO', 'importe' => $datos['Recibo']['aporte_socio']);
			array_push($datos['Recibo']['detalle_solicitud'], $aDetalle);
		endif;

		foreach($datos['CancelacionOrden']['id_check'] as $id => $valor):
			$orden = $this->CancelacionOrden->get($id);
			$aDetalle = array('recibo_id' => 0, 'concepto' => 'CANC. ' . $orden['CancelacionOrden']['recibo_detalle'], 'importe' => round($orden['CancelacionOrden']['importe_proveedor'] * -1,2));
			array_push($datos['Recibo']['detalle_solicitud'], $aDetalle);
		endforeach;


		$datos['Recibo']['importe_cancela'] = $importe_cancela;

		if(!$this->Recibo->guardarRecibo($datos)):
			parent::notificar("PROBLEMA AL GENERAR EL RECIBO");
			$this->rollback();
			return false;
		endif;

		$datos['Solicitud']['recibo_id'] = $this->Recibo->getReciboBySolicitud($datos['Solicitud']['id']);
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;

		// Proceso la cancelacion para informar en la Liquidacion de Proveedores.
		foreach($datos['CancelacionOrden']['id_check'] as $id => $valor):
			$orden = $this->CancelacionOrden->get($id);
			$orden['CancelacionOrden']['estado'] = 'P';
			$orden['CancelacionOrden']['recibo_id'] = $datos['Solicitud']['recibo_id'];

			$this->CancelacionOrden->id = $id;
			if(!$this->CancelacionOrden->save($orden)):
				parent::notificar("NO SE RECAUDO LAS CANCELACIONES");
				$this->rollback();
				return false;
			endif;

			// Actualizo la Orden Descuento Cobro con el id del Recibo
			$this->OrdenDescuentoCobro->id = $orden['CancelacionOrden']['orden_descuento_cobro_id'];
			if(!$this->OrdenDescuentoCobro->saveField('recibo_id',$datos['Solicitud']['recibo_id'])):
				parent::notificar('EN LA ORDEN DESCUENTO COBRO NO SE ACTUALIZO EL ID DEL RECIBO');
				$recaudado = false;
				break;
			endif;


			// grabo la liquidacion a Proveedores
/*
 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
 * esta funcion queda obsoleta. 26/06/2017
			$orden['CancelacionOrden']['proveedor_liquidacion_id'] = $this->ProveedorLiquidacion->grabarLiquidacionByCancelacion($orden['CancelacionOrden']['orden_descuento_cobro_id']);
			if(!$orden['CancelacionOrden']['proveedor_liquidacion_id']):
				parent::notificar("NO SE GRABO LA LIQUIDACION A PROVEEDORES");
				$this->rollback();
				return false;
			endif;
 *
 */


		endforeach;


		$this->commit();
		return true;
	}


	function getRecibo($id=null){
		if(empty($id)) return array();

		$oRecibo = $this->importarModelo('Recibo', 'clientes');

		return $oRecibo->getRecibo($id);
	}


	function anularRecibo($id=null){
		if(empty($id)) return array();

		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');

		return $this->OrdenDescuentoCobro->anularCobro(0, $id);
	}

//	function anularRecibo($id=null){
//		if(empty($id)) return array();
//
//		$datos['Recibo']['id'] = $id;
//
//		$oRecibo = $this->importarModelo('Recibo', 'clientes');
//
//		return $oRecibo->anularRecibo($datos);
//	}


	function getPeriodosLiquidados(){
		App::import('model','mutual.Liquidacion');
		$oLIQ = new Liquidacion(null);
		return $oLIQ->getPeriodosLiquidados(false,true,null,"DESC",true);
	}


	function getProveedoresLiquidados($toList = false){
		$sql = "SELECT
				Proveedor.codigo_proveedor,
				Proveedor.razon_social
				FROM productores_liquidacion_solicitudes AS LiquiSolicitudes
				INNER JOIN solicitudes AS Solicitudes ON (Solicitudes.nro_solicitud = LiquiSolicitudes.nro_solicitud)
				INNER JOIN proveedores_productos AS ProveedorProducto ON (ProveedorProducto.codigo_producto = Solicitudes.codigo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.codigo_proveedor = ProveedorProducto.codigo_proveedor)
				GROUP BY Proveedor.codigo_proveedor
				ORDER BY Proveedor.razon_social;";
		$proveedores = $this->query($sql);
		if($toList && !empty($proveedores)){
			$list = array();
			foreach ($proveedores as $proveedor){
				$list[$proveedor['Proveedor']['codigo_proveedor']] = strtoupper(utf8_encode($proveedor['Proveedor']['razon_social']));
			}
			return $list;
		}else{
			return $proveedores;
		}

	}


}

?>
