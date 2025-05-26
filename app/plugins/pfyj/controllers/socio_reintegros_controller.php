<?php
class SocioReintegrosController extends PfyjAppController{
	var $name = 'SocioReintegros';
	
	var $autorizar = array('genera_imputacion_en_ctacte','ver_detalle_pago','reporte_socio_reintegros','reintegro_anticipado', 'imprimir_orden_pago', 'editOrdenPago');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index(){
		$this->redirect('by_socio');
	}
	
	function by_socio($socio_id = null,$menuPersonas=1){
		if(empty($socio_id)) parent::noDisponible();
//		if(empty($socio_id)) $this->redirect('/pfyj/personas');
		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		$reintegros = $this->SocioReintegro->getReintegrosBySocio2($socio_id,false);		
		
		$this->set('reintegros',$reintegros);			
	}
	
	function imputar_en_ctacte($socio_id=null,$id = null,$menuPersonas=1){
		if(empty($id)) parent::noDisponible();
		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		if(empty($socio)) $this->redirect('/pfyj/personas');
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		$imputacion = null;
		//apruebo la imputacion
		if(!empty($this->data)){
			if($this->data['SocioReintegro']['accion'] == 'PREVIEW'):
				
				$imputacion = $this->SocioReintegro->armarImputacionEnCtaCte($this->data['SocioReintegro']['socio_id'],$this->data['SocioReintegro']['saldo']);
				
				if(empty($imputacion['cuotas'])):
					$this->redirect('by_socio/'.$socio_id.'/1');
					$this->Mensaje->error("NO EXISTEN CUOTAS ADEUDADAS DISPONIBLES PARA APLICAR EL REINTEGRO");
				endif;
				
			elseif($this->data['SocioReintegro']['accion'] == 'APROBAR'):
				if($this->SocioReintegro->aprobarImputacionEnCtaCte($this->data['SocioReintegro']['id'],$this->data['SocioReintegro']['socio_id'],$this->data['SocioReintegro']['saldo'])):
					$this->Mensaje->ok("REINTEGRO PROCESADO CORRECTAMENTE");
					$this->data = null;
					$this->redirect('by_socio/'.$socio_id.'/1');
				else:
					$this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR EL REINTEGRO!");	
				endif;
			endif;
		}
		$this->set('imputacion',$imputacion);
		$this->set('reintegro',$this->SocioReintegro->getReintegro($id));
		
	}
	
	function genera_imputacion_en_ctacte(){
		$imputacion = null;
		if(!empty($this->data)){
			
			debug($this->data);
			
//			$imputacion = $this->SocioReintegro->armarImputacionEnCtaCte($this->data['SocioReintegro']['socio_id'],$this->data['SocioReintegro']['importe_imputa'],$this->data);
		}
		$this->set('imputacion',$imputacion);
	}
	
	
	function generar_opago($socio_id = null,$menuPersonas=1){
		if(empty($socio_id)) parent::noDisponible();
		
		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		//genero la orden de pago
		if(!empty($this->data)){
			App::import('Model','Pfyj.SocioReintegroPago');
			$oPAGO = new SocioReintegroPago();
			if($oPAGO->generarPago($this->data)){
				$this->Mensaje->ok("PAGO PROCESADO CORRECTAMENTE!");
				$this->redirect('by_socio/'.$socio_id);
			}else{
				$this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR EL PAGO");
			}
		}
		
		$reintegros = $this->SocioReintegro->getReintegrosPendientesBySocio($socio_id);		
		
		$this->set('reintegros',$reintegros);		
		
	}
	

	function ver_detalle_pago($pago_id = null,$id=null){
		if(empty($pago_id)) parent::noDisponible();
		if(empty($id)) parent::noDisponible();
		App::import('Model','Pfyj.SocioReintegroPago');
		$oPAGO = new SocioReintegroPago();		
		$this->set('pago',$oPAGO->getPago($pago_id));
		$this->set('reintegro',$this->SocioReintegro->getReintegro($id));
	}
	
	function reporte_socio_reintegros($socio_id=null){
		if(empty($socio_id)) parent::noDisponible();
		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		if(empty($socio)) $this->redirect('/pfyj/personas');
		$this->set('socio',$socio);
		$reintegros = $this->SocioReintegro->getReintegrosBySocio($socio_id);		
		$this->set('reintegros',$reintegros);	

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();		
		$this->set('apenom',$oSOCIO->getApenom($socio_id,true));		
		
		$this->render('reportes/reporte_socio_reintegros_pdf','pdf');
	}
	
	
	function reintegro_anticipado($socio_id = null){
		if(empty($socio_id)) parent::noDisponible();
		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		$this->set('menuPersonas',1);
		$this->set('socio',$socio);	
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();		

		if(!empty($this->data)):
		
			$liquidacion = $oLQ->read('periodo',$this->data['SocioReintegro']['liquidacion_id']);
			$this->data['SocioReintegro']['periodo'] = $liquidacion['Liquidacion']['periodo'];

			
			if($this->SocioReintegro->save($this->data)){
				$this->redirect('generar_orden_pago/'.$socio_id);
			}else{
				$this->Mensaje->errorGuardar();
			}
			
//			App::import('Model','Pfyj.SocioReintegroPago');
//			$oPAGO = new SocioReintegroPago();
//			
////			debug($this->data);
////			exit;
////			
//			if($oPAGO->save($this->data)):
//				$this->data['SocioReintegro']['socio_reintegro_pago_id'] = $oPAGO->getLastInsertID();
//				if($this->SocioReintegro->save($this->data)):
//					$this->redirect('by_socio/'.$socio_id);
//				else:
//					$this->Mensaje->errorGuardar();
//				endif;
//			else:
//				$this->Mensaje->errorGuardar();
//			endif;
			
			
		endif;
		
		$liquidaciones = $oLQ->datosCombo(array('Liquidacion.imputada' => 0));
		$this->set('liquidaciones',$liquidaciones);

		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
	}
	
//	function del($id=null){
//		if(empty($id)) parent::noDisponible();
//		$reintegro = $this->SocioReintegro->getReintegro($id);
//		if(!$this->SocioReintegro->del($id)) $this->Mensaje->errorBorrar();
//		$this->redirect('by_socio/'.$reintegro['SocioReintegro']['socio_id']);
//	}
	

	function generar_orden_pago($socio_id = null,$menuPersonas=1){
		$this->Session->del('grilla_pagos');
		if(empty($socio_id)) parent::noDisponible();
		
			
    			# 1) ######################################################
    			$this->set('uuid', $this->SocioReintegro->generarPIN(20));
    			//este UUID se guarda como hidden en el formulario del detalle
    			#######################################################
			
		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		//genero la orden de pago
		if(!empty($this->data)){
			App::import('Model','Pfyj.SocioReintegroPago');
			$oPAGO = new SocioReintegroPago();
//			$nOrdenPago = $oPAGO->generarOrdenPago($this->data);
    			
				# 5) ######################################################
    			# reconstruyo el campo renglonesSerialize con los datos de la sessi�n 
    			# con el uuid para que el modelo ni se entere del cambio
    			if(!isset($this->data['Movimiento']['renglonesSerialize'])){
    				$renglones = $this->Session->read('grilla_pagos_' . $this->data['Movimiento']['uuid']);	
    				$this->data['Movimiento']['renglonesSerialize'] = base64_encode(serialize($renglones));
    			}
    			######################################################
				
			$nOrdenPago = $oPAGO->generarOrdenPagoParcial($this->data);
			if(!$nOrdenPago){
				$this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR EL PAGO");
			}else{
				$this->Mensaje->ok("PAGO PROCESADO CORRECTAMENTE!");
//				$this->redirect('by_socio/'.$socio_id);
				$this->redirect('editOrdenPago/'.$nOrdenPago . '/' . $socio_id);
			}
		}
		
		$this->chqCartera = $this->SocioReintegro->importarModelo('BancoChequeTercero', 'cajabanco');
		$chqCarteras = $this->chqCartera->getChequeCartera();
		 
		$reintegros = $this->SocioReintegro->getReintegrosPendientesBySocio($socio_id);		
		
		$this->set('reintegros',$reintegros);
		$this->set('chqCarteras', $chqCarteras);		
		
	}
	
	function reintegro_anticipado_opago($socio_id = null){
		$this->Session->del('grilla_pagos');
		if(empty($socio_id)) parent::noDisponible();
		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		$this->set('menuPersonas',1);
		$this->set('socio',$socio);	
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();		

		if(!empty($this->data)):
		
			App::import('Model','pfyj.SocioReintegroPago');
			$oSRP = new SocioReintegroPago();
			$ordenPagoId = $oSRP->generarOrdenPagoAnticipado($this->data);			
			if(!$ordenPagoId){
				$this->Mensaje->errorGuardar();
			}else{
				$this->redirect('editOrdenPago/' . $ordenPagoId . '/' . $this->data['SocioReintegro']['socio_id']);
			}
		
//			$liquidacion = $oLQ->read('periodo',$this->data['SocioReintegro']['liquidacion_id']);
//			$this->data['SocioReintegro']['periodo'] = $liquidacion['Liquidacion']['periodo'];
//			$this->data['SocioReintegro']['importe_aplicado'] = $this->data['Movimiento']['importe_pago'];
//			
//			if(empty($this->data['Movimiento']['observacion'])) $this->data['Movimiento']['observacion'] = 'REINTEGRO ANTICIPADO A SOCIO';
//			
//			$this->data['SocioReintegro']['procesado'] = 1;
//			$this->data['SocioReintegro']['reintegrado'] = 1;
//			
//			$this->data['SocioReintegro']['socio_id'] = $this->data['SocioReintegroPago']['socio_id'];
//			
//			$this->oSocioReintegro = $this->SocioReintegro->importarModelo('SocioReintegro', 'pfyj');
//			$oSocioReintegroPago = $this->SocioReintegro->importarModelo('SocioReintegroPago', 'pfyj');
//
//			$this->SocioReintegro->begin();
//			if($oSocioReintegroPago->save($this->data)):
//				$this->data['SocioReintegro']['socio_reintegro_pago_id'] = $oSocioReintegroPago->getLastInsertID();
//				if($this->oSocioReintegro->save($this->data)):
//				
//					// GRABO LA ORDEN DE PAGO DEL REINTEGRO.
//					$idSocioReintegro = $this->oSocioReintegro->getLastInsertID();
//					$this->data['Movimiento']['detalle_reintegro'] = array();
//					$aDetalleReintegro = array('socio_reintegro_id' => $idSocioReintegro, 'importe' => $this->data['Movimiento']['importe_pago']);
//					array_push($this->data['Movimiento']['detalle_reintegro'], $aDetalleReintegro);
//
//					$this->oMovimiento = $this->SocioReintegro->importarModelo('Movimiento', 'proveedores');
//					$this->oOPagoDetalle = $this->SocioReintegro->importarModelo('OrdenPagoDetalle', 'proveedores');
//			
//					if($this->oMovimiento->guardarOpago($this->data, false)):
//					
//						//CARGO EL REINTEGRO
//						$reintegro = $this->SocioReintegro->read(null,$idSocioReintegro);
//						
//						if(empty($reintegro)):
//							$this->SocioReintegro->rollback();
//							$this->Mensaje->errorGuardar();
//							return;
//						endif;
//						
//		    			$reintegro['SocioReintegro']['orden_pago_id'] = $this->oOPagoDetalle->getOPagoByReintegro($reintegro['SocioReintegro']['id']); 
//						
////						$this->data['SocioReintegro']['id'] = $idSocioReintegro;
////		    			$this->data['SocioReintegro']['orden_pago_id'] = $this->oOPagoDetalle->getOPagoByReintegro($idSocioReintegro); 
//						if($this->SocioReintegro->save($reintegro)):
////		    			if($this->SocioReintegro->save($this->data)):
//							$this->SocioReintegro->commit();
//							$this->redirect('editOrdenPago/' . $reintegro['SocioReintegro']['orden_pago_id'] . '/' . $this->data['SocioReintegro']['socio_id']);
////							$this->redirect('by_socio/'.$socio_id);
//						else:
//							$this->SocioReintegro->rollback();
//							$this->Mensaje->errorGuardar();
//						endif;
//					else:
//						$this->SocioReintegro->rollback();
//						$this->Mensaje->errorGuardar();
//						return false;			
//					endif;
//		
//				else:
//					$this->SocioReintegro->rollback();
//					$this->Mensaje->errorGuardar();
//				endif;
//			else:
//				$this->SocioReintegro->rollback();
//				$this->Mensaje->errorGuardar();
//			endif;
			
			
		endif;
		
		$liquidaciones = $oLQ->datosCombo(array('Liquidacion.imputada' => 0));
		$this->set('liquidaciones',$liquidaciones);

		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
	}
	

	function editOrdenPago($nOrdenPago=0, $socio_id=0){
		
		if(empty($nOrdenPago)) $this->redirect('by_socio/'.$socio_id);

		$this->SocioReintegro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioReintegro->Socio->read(null,$socio_id);
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		if(!empty($this->data)):
			if ($this->SocioReintegro->anularOrdenPago($nOrdenPago)):
				$this->Mensaje->ok('LA ORDEN DE PAGO SE ANULO CORRECTAMENTE');
				$this->redirect('by_socio/'.$socio_id);
			else:
				$this->Mensaje->errorBorrar();
			endif;
			
				
		endif;

		
	
		
		$this->Socio = $this->SocioReintegro->importarModelo('Socio', 'pfyj');
		$this->persona = $this->SocioReintegro->importarModelo('Persona', 'pfyj');
		$this->oOPago = $this->SocioReintegro->importarModelo('OrdenPago', 'proveedores');
		$aOPago = $this->oOPago->getOrdenDePago($nOrdenPago);

		$aOPago['OrdenPago']['action'] = "editOrdenPago/" . $nOrdenPago . '/' . $socio_id;
		$aOPago['OrdenPago']['url'] = '/pfyj/socio_reintegros/editOrdenPago/0/' . $socio_id;
		
		$aSocio = $this->Socio->getPersonaBySocioID($aOPago['OrdenPago']['socio_id']);

		$aPersona = $this->persona->getPersona($aSocio['Persona']['id']); 
		$aOPago['Proveedor'] = array(
			'id' => $aPersona['Persona']['id'],
			'razon_social' => $aPersona['Persona']['apenom'],
			'domicilio' => $aPersona['Persona']['domicilio'],
			'iva_concepto' => 'CONSUMIDOR FINAL',
			'formato_cuit' => $aPersona['Persona']['documento'],
			'nro_ingresos_brutos' => ''
		);
		$this->set('menuPersonas',1);
		$this->set('aOrdenPago', $aOPago);
		
	}
	
	
	function borrar($id=null){
		if(empty($id)) parent::noDisponible();
		$reintegro = $this->SocioReintegro->getReintegro($id);
		if(!$this->SocioReintegro->borrar($id)) $this->Mensaje->errorBorrar();
		$this->redirect('by_socio/'.$reintegro['SocioReintegro']['socio_id']);
	}	
	
	
}
?>