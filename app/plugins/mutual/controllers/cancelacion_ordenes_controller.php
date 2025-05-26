<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage controller
 */

class CancelacionOrdenesController extends MutualAppController{
	
	var $name = 'CancelacionOrdenes';
	var $autorizar = array(
							'view','borrar_desde_padron','generar_orden_cobro_caja_recibo', 'editRecibo','generar_orden_cobro_caja','generar_recibo_caja','vista_detalle'
	);	
	
	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
	    parent::beforeFilter();		
	}	
	
	
	function by_socio($socio_id = null,$menuPersonas=1){
		if(empty($socio_id)) parent::noAutorizado();
		$this->CancelacionOrden->bindModel(array('belongsTo' => array('Socio')));
		$this->CancelacionOrden->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->CancelacionOrden->Socio->read(null,$socio_id);
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
                
		$cancelaciones = $this->CancelacionOrden->get_socio_by_estado_2($socio_id,'P');
                $cancelacionesEmi = $this->CancelacionOrden->get_socio_by_estado_2($socio_id,'E');                
//		$cancelaciones = $this->CancelacionOrden->getBySocioByEstado($socio_id,'P',false);
//		$cancelacionesEmi = $this->CancelacionOrden->getBySocioByEstado($socio_id,'E',false);
                

//                exit;

                
		$this->set('cancelaciones',$cancelaciones);
		$this->set('cancelacionesEmi',$cancelacionesEmi);
                $this->render('by_socio2');
		
	}
	
	
	function view($id=null,$toPDF=0){
		if(empty($id)) parent::noAutorizado();
		$cancelacion = $this->CancelacionOrden->get($id,true);
		$this->set('cancelacion',$cancelacion);
		if($toPDF==1)$this->render('reportes/orden_cancelacion_pdf','pdf');
	}
	
	function list_socios(){
//		$condiciones = null;		
//		$this->Persona->recursive = 2;
//		$search = null;
//		if(!empty($this->data)){
//			$this->Session->del($this->name.'.search');
//			$search = $this->data;
//		}else if($this->Session->check($this->name.'.search')){
//			$search = $this->Session->read($this->name.'.search');
//			$this->data = $search; 
//		}
//		
//		$condiciones = array(
//							'Persona.tipo_documento  LIKE ' => $search['Persona']['tipo_documento'] ."%",
//							'Persona.documento LIKE ' => $search['Persona']['documento']."%",
//							'Persona.apellido LIKE ' => $search['Persona']['apellido']."%",
//							'Persona.nombre LIKE ' => $search['Persona']['nombre']."%",
//							'Socio.id <>' => 0			
//						);	
//		$this->Session->write($this->name.'.search', $search);
//		$this->paginate = array(
//								'limit' => 30,
//								'order' => array('Persona.apellido,Persona.nombre ASC')
//								);		
////		$condiciones = array(
////							'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
////							'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
////							'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
////							'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",			
////						);	
////		$this->paginate = array(
////								'limit' => 30,
////								'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC')
////								);
//		App::import('model','Pfyj.Persona');
//		$this->Persona = new Persona();						
//		$this->set('personas', $this->paginate('Persona',$condiciones));		
	}
	
	function generar($persona_id){
		App::import('model','Pfyj.Persona');
		$oPersona = new Persona();	
		$persona = $oPersona->read(null,$persona_id);
		if(empty($persona['Socio'])) $this->redirect('list_socios');
		$this->set('persona', $persona);

		#traigo las ordenes de cancelacion para el socio
//		$this->set('cancelaciones', $this->CancelacionOrden->getBySocioByEstado($persona['Socio']['id'],'E'));
                $cancelacionesEmi = $this->CancelacionOrden->get_socio_by_estado_2($persona['Socio']['id'],'E'); 
                $this->set('cancelacionesEmi',$cancelacionesEmi);

	}
	
	function sel_cuota($persona_id,$orden_dto_id){

            
		if(!empty($this->data)){
			if($this->CancelacionOrden->generar($this->data)){
//				$this->Auditoria->log();
				$this->redirect('generar/'.$persona_id);
			}else{
				if(!empty($this->CancelacionOrden->notificaciones))$this->Mensaje->errores("ERRORES: ",$this->CancelacionOrden->notificaciones);
				else $this->Mensaje->error("Se produjo un error al generar la Orden de Cancelacion");
			}
		}

		App::import('model','Pfyj.Persona');
		$oPersona = new Persona();	
		$persona = $oPersona->read(null,$persona_id);
		if(empty($persona['Socio'])) $this->redirect('list_socios');
		$this->set('persona', $persona);	
		
      
		
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
		$cuotas = $oCuota->cuotasAdeudadasByOrdenDto($orden_dto_id,'MUTUSICUMUTU',FALSE,NULL,TRUE,TRUE);
		$this->set('cuotas', $cuotas);
		
		App::import('model','Mutual.OrdenDescuento');
		$oOrd = new OrdenDescuento();
		$orden = $oOrd->getOrden($orden_dto_id,null,true);
		$this->set('orden', $orden);		
		
//		$TimeHoy = mktime(0,0,0,date('m'),date('d'),date('Y')) + (24 * 60 * 60);
//		$this->set('hoy', date('d/m/Y',$TimeHoy));
		//kolo problema al cargar una cancelacion de grupo tanto EXPTE #530962 30/11/2012  
 		
		$this->set('hoy', date("d/m/Y"));
		
				
	}
	
	function del($id,$socio_id){
		
		if (!$id) $this->redirect(array('action'=>'list_socios'));
		
		$this->CancelacionOrden->bindModel(array('hasMany' => array('CancelacionOrdenCuota' => array('dependent'=> true))));
		
		$this->CancelacionOrden->begin();
		
		if($this->CancelacionOrden->CancelacionOrdenCuota->deleteAll('CancelacionOrdenCuota.cancelacion_orden_id = '. $id)){
			if ($this->CancelacionOrden->del($id,true)) {
				$this->Auditoria->log();
				$this->CancelacionOrden->commit();
			}else{
				$this->CancelacionOrden->rollback();
				$this->Mensaje->errorBorrar();
			}
		}else{
			$this->CancelacionOrden->rollback();
			$this->Mensaje->errorBorrar();
		}
		App::import('model','Pfyj.Socio');
		$oSocio = new Socio();
		$socio = $oSocio->read('persona_id',$socio_id);
		$this->redirect(array('action'=>'generar/'.$socio['Socio']['persona_id']));		
	}
	
	function borrar_desde_padron($id,$socio_id){
		
		if (!$id) parent::noDisponible();
		
		$this->CancelacionOrden->bindModel(array('hasMany' => array('CancelacionOrdenCuota' => array('dependent'=> true))));
		
                if(!$this->CancelacionOrden->is_deleteable($id)){
                    $this->Mensaje->error("LA CANCELACION #$id NO SE PUEDE ELIMINAR PORQUE TIENE SOLICITUDES VINCULADAS. Deberá anular las solicitudes primero.");
                    $this->redirect(array('action'=>'by_socio/'.$socio_id));
                }
		$this->CancelacionOrden->begin();
		
		if($this->CancelacionOrden->CancelacionOrdenCuota->deleteAll('CancelacionOrdenCuota.cancelacion_orden_id = '. $id)){
			if ($this->CancelacionOrden->del($id,true)) {
				$this->CancelacionOrden->commit();
			}else{
				$this->CancelacionOrden->rollback();
				$this->Mensaje->errorBorrar();
			}
		}else{
			$this->CancelacionOrden->rollback();
			$this->Mensaje->errorBorrar();
		}

		$this->redirect(array('action'=>'by_socio/'.$socio_id));		
	}	

	function cargar_info_pago($id){
		
		if(!empty($this->data)){
			if($this->CancelacionOrden->datosPago($this->data)){
				$this->Mensaje->okGuardar();
			}else{
				$this->Mensaje->errorGuardar();
			}
		}
		
		$orden = $this->CancelacionOrden->get($id);
		$this->data = $orden;
		$this->set('orden', $orden);
//		$this->render(null,'blank');
		
	}
	
	function vista_detalle($id){
		$orden = $this->CancelacionOrden->get($id);
		if(empty($orden)) parent::noDisponible();
		$this->set('orden', $orden);
		$this->render();
	}
	
	
	function generar_orden_cobro_caja($persona_id){
		$this->Session->del('grilla_cobros');
		$oProveedores = $this->CancelacionOrden->importarModelo('Proveedor', 'proveedores');
		App::import('model','Pfyj.Persona');
		$oPersona = new Persona();	
		$persona = $oPersona->read(null,$persona_id);
		if(empty($persona['Socio'])) $this->redirect('list_socios');
		$this->set('persona', $persona);

		if(!empty($this->data)){
			if(isset($this->data['CancelacionOrden']['id_check'])){
				
				if(!empty($this->data['CancelacionOrden']['fecha_imputacion'])){
					if($this->CancelacionOrden->recaudarByCaja($this->data)){
						$this->Mensaje->ok("La Orden de Cancelacion fue Recaudada Correctamente!.");
//						$this->redirect('generar_orden_cobro_caja/'.$persona_id);
						$this->redirect('by_socio/'.$persona['Socio']['id']);
					}else{
						$this->Mensaje->error("Se produjo un error al recaudar la Orden de Cancelacion.");
					}
				}else{
					$this->Mensaje->error("Falta indicar la Fecha de Imputacion!.");
				}
				
			}else{
				$this->Mensaje->error("Seleccionar al menos UNA Orden de Cancelacion!");
			}
			
		}
		
		$cmbProveedores = $oProveedores->comboProveedores('ninguno . . .');
		$this->set('cmbProveedores', $cmbProveedores);
		
		#traigo las ordenes de cancelacion para el socio
//		$this->set('cancelaciones', $this->CancelacionOrden->getBySocioByEstado($persona['Socio']['id'],'E'));

                $this->set('cancelaciones', $this->CancelacionOrden->get_socio_by_estado_cobrables($persona['Socio']['id']));
		
	}
	
	
	function anular($id=null){
		
		if (empty($id)) parent::noDisponible();

		if(!empty($this->data)):
			if($this->CancelacionOrden->anular($this->data['CancelacionOrden']['id'])){
				$this->Mensaje->ok("LA ORDEN DE CANCELACION #".$this->data['CancelacionOrden']['id']." FUE ANULADA CORRECTAMENTE!");
				$this->redirect("by_socio/".$this->data['CancelacionOrden']['socio_id']);
			}else{
				$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR ANULAR LA ORDEN DE CANCELACION #" . $this->data['CancelacionOrden']['id']);
			}
		endif;		
		
		$cancelacion = $this->CancelacionOrden->get($id,true);
		if(empty($cancelacion)) parent::noDisponible();
		$this->set('cancelacion',$cancelacion);
		
		$this->CancelacionOrden->bindModel(array('belongsTo' => array('Socio')));
		$this->CancelacionOrden->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->CancelacionOrden->Socio->read(null,$cancelacion['CancelacionOrden']['socio_id']);
		$this->set('menuPersonas',1);
		$this->set('socio',$socio);	

		//cargo el cobro emitido por la cancelacion
		App::import('model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();		
		$cobro = $oCOBRO->getCobroByCancelacion($cancelacion['CancelacionOrden']['id']);
		$this->set('cobro',$cobro);	
	}
	
	
	
	function generar_orden_cobro_caja_recibo($persona_id){
                
            # DESVIO PARA QUE LA RECAUDACION NO PIDA EL ORIGEN DE LOS FONDOS. 
            # RECAUDA DIRECTAMENTE LA MUTUAL. SI EL CODIGO EXISTE Y EL CAMPO
            # ENTERO_1 ES IGUAL A 1 PIDE EL ORIGEN DE LOS FONDOS. ESTO ES PARA
            # LA MUTUAL AMAN. CASO CONTRARIO NO TIENE EN CUENTA EL ORIGEN DE LOS
            # FONDOS NI EL COMPENSA PAGO.
            $recauda = $this->CancelacionOrden->getGlobalDato('entero_1', 'MUTUMUTURECA');
//            debug($recauda);
            $recauda = (empty($recauda['GlobalDato']['entero_1'])? 0: $recauda['GlobalDato']['entero_1']);
//            debug($recauda);
//            exit;
            if($recauda === '1'):
                $this->redirect('generar_recibo_caja/'.$persona_id);
            endif;

            $this->Session->del('grilla_cobros');
            $oProveedores = $this->CancelacionOrden->importarModelo('Proveedor', 'proveedores');
            $this->Liquidaciones = $this->CancelacionOrden->importarModelo('Liquidacion', 'mutual');

            App::import('model','Pfyj.Persona');
            $oPersona = new Persona();	
            $persona = $oPersona->read(null,$persona_id);
            if(empty($persona['Socio'])) $this->redirect('list_socios');
            $this->set('persona', $persona);

            $bloquear = 0;
            $origenFondo = 0;
            $retieneComercio = 0;
            $cancelaciones = array();

            if(!empty($this->data)){

                if(isset($this->data['CancelacionOrden']['id_check'])){

                    if(!empty($this->data['CancelacionOrden']['fecha_comprobante'])){

                        $nReciboId = $this->CancelacionOrden->cancelacionByCaja($this->data);

                        if(!$nReciboId):
                                $this->Mensaje->errores("ERRORES:",$this->CancelacionOrden->notificaciones);
                                $this->redirect('by_socio/'.$this->data['CancelacionOrden']['cabecera_socio_id']);
                        else:
                                $this->Mensaje->ok("La Orden de Cancelacion fue Recaudada Correctamente!.");
                                if($nReciboId == 'A'):
                                        $this->redirect('by_socio/'.$this->data['CancelacionOrden']['cabecera_socio_id']);
                                else:
                                        $this->redirect('editRecibo/' . $nReciboId . '/' . $persona['Socio']['id']);
                                endif;
                        endif;
                    }else{
                        $this->Mensaje->error("Falta indicar la Fecha del Comprobante!.");
                    }

                }else{
                    $this->Mensaje->error("Seleccionar al menos UNA Orden de Cancelacion!");
                }

            }

            $retieneComercio = (isset($this->data['CancelacionOrden']['retiene_comercio']) ? 1 : $retieneComercio);

            #traigo las ordenes de cancelacion para el socio
//		$this->set('cancelaciones', $this->CancelacionOrden->getBySocioByEstado($persona['Socio']['id'],'E'));
//            $cancelaciones = $this->CancelacionOrden->getBySocioByEstado($persona['Socio']['id'],'E');
            $cancelaciones = $this->CancelacionOrden->get_socio_by_estado_cobrables($persona['Socio']['id']);
            
            
            $origenFondo = $this->data['CancelacionOrden']['proveedor_origen_fondo_id'];

            $cmbProveedores = $oProveedores->comboProveedores('ninguno . . .');
            $this->set('cmbProveedores', $cmbProveedores);
            $this->set('origenFondo', MUTUALPROVEEDORID);
            $this->set('retieneComercio', 0);		

            $liquidaciones = $this->Liquidaciones->datosCombo(array('Liquidacion.imputada' => 0));
            $this->set('liquidaciones',$liquidaciones);

            $this->set('cancelaciones',$cancelaciones);
		
	}
	
	
	function generar_orden_cobro_caja_recibo_old($persona_id){
		$this->Session->del('grilla_cobros');
		$oProveedores = $this->CancelacionOrden->importarModelo('Proveedor', 'proveedores');
		$this->Liquidaciones = $this->CancelacionOrden->importarModelo('Liquidacion', 'mutual');

                App::import('model','Pfyj.Persona');
		$oPersona = new Persona();	
		$persona = $oPersona->read(null,$persona_id);
		if(empty($persona['Socio'])) $this->redirect('list_socios');
                
                $this->set('MutualProveedorId', MUTUALPROVEEDORID);
		$this->set('persona', $persona);
                
                $this->set('recibo', 1);
//		$this->redirect('generar_recibo_caja/'.$persona_id);

		if(!empty($this->data)){
			if(isset($this->data['CancelacionOrden']['id_check'])){
				
				if(!empty($this->data['CancelacionOrden']['fecha_comprobante'])){
//					$nReciboId = $this->CancelacionOrden->cobroByCaja($this->data);
						$nReciboId = $this->CancelacionOrden->cancelacionByCaja($this->data);
			
					if(!$nReciboId):
						$this->Mensaje->error("Se produjo un error al recaudar la Orden de Cancelacion.");
					else:
						$this->Mensaje->ok("La Orden de Cancelacion fue Recaudada Correctamente!.");
   						$this->redirect('editRecibo/' . $nReciboId . '/' . $persona['Socio']['id']);
					endif;
				}else{
					$this->Mensaje->error("Falta indicar la Fecha de Imputacion!.");
				}
				
			}else{
				$this->Mensaje->error("Seleccionar al menos UNA Orden de Cancelacion!");
			}
			
		}
		
		$cmbProveedores = $oProveedores->comboProveedores('ninguno . . .');
		$this->set('cmbProveedores', $cmbProveedores);
		$this->set('origenFondo', MUTUALPROVEEDORID);
		$this->set('retieneComercio', 0);		
		
		$liquidaciones = $this->Liquidaciones->datosCombo(array('Liquidacion.imputada' => 0));
		$this->set('liquidaciones',$liquidaciones);
		
		#traigo las ordenes de cancelacion para el socio
		$this->set('cancelaciones', $this->CancelacionOrden->getBySocioByEstado($persona['Socio']['id'],'E'));
	}
	
	
	function editRecibo($nReciboId=null, $nSocioId=null){
		if(empty($nReciboId)) $this->redirect('by_socio/'.$nSocioId);

		if(!empty($this->data)):
			if ($this->CancelacionOrden->anularRecibo($nReciboId)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
			
   			$this->redirect('by_socio/'.$nSocioId);
				
		endif;

		$aRecibo = $this->CancelacionOrden->getRecibo($nReciboId);
//		$aRecibo['Recibo']['liquidacion_intercambio_id'] = $lqdIntercambioId;
//		$aRecibo['Recibo']['liquidacion_id'] = $liquidacionId;
		$aRecibo['Recibo']['action'] = "editRecibo/" . $nReciboId . '/' . $nSocioId;
		$aRecibo['Recibo']['url'] = '/mutual/CancelacionOrdenes/editRecibo/0/' . $nSocioId;
		
		$this->set('Recibo', $aRecibo);
//		$this->render('recibos/recibo');
		
	}
	
	
	function imprimir_recibo($id){
		// traer Recibo
		$this->Cliente = $this->CancelacionOrden->importarModelo('Cliente', 'clientes');
		$aRecibo = $this->Cliente->getRecibo($id);
		$this->set('aRecibo', $aRecibo);
		$this->render('recibos/imprimir_recibo_pdf', 'pdf');	
		
	}
	
	
	
	function generar_recibo_caja($persona_id){
		$this->Session->del('grilla_cobros');
		$oProveedores = $this->CancelacionOrden->importarModelo('Proveedor', 'proveedores');
		$this->Liquidaciones = $this->CancelacionOrden->importarModelo('Liquidacion', 'mutual');
		
		App::import('model','Pfyj.Persona');
		$oPersona = new Persona();	
		$persona = $oPersona->read(null,$persona_id);
		if(empty($persona['Socio'])) $this->redirect('list_socios');
		$this->set('persona', $persona);
		
		$bloquear = 0;
		$origenFondo = 0;
		$retieneComercio = 0;
		$cancelaciones = array();
		
		if(!empty($this->data)){
//                    debug($this->data);
//                    exit;
			if(isset($this->data['CancelacionOrden']['Cabecera']) && $this->data['CancelacionOrden']['Cabecera'] == 1):
				if($this->data['CancelacionOrden']['proveedor_origen_fondo_id'] != 0):
					$bloquear = 1;
					$retieneComercio = (isset($this->data['CancelacionOrden']['retiene_comercio']) ? 1 : $retieneComercio);
					
					#traigo las ordenes de cancelacion para el socio
//					$this->set('cancelaciones', $this->CancelacionOrden->getBySocioByEstado($persona['Socio']['id'],'E'));
					$cancelaciones = $this->CancelacionOrden->getBySocioByEstado($persona['Socio']['id'],'E');
					$origenFondo = $this->data['CancelacionOrden']['proveedor_origen_fondo_id'];
				else:
					$this->Mensaje->error("Debe seleccionar el origen de los fondos");
				endif;
			else:
				if(isset($this->data['CancelacionOrden']['id_check'])){
					
					if(!empty($this->data['CancelacionOrden']['fecha_comprobante'])){

						$nReciboId = $this->CancelacionOrden->cancelacionByCaja($this->data);
			
						if(!$nReciboId):
							$this->Mensaje->errores("ERRORES:",$this->CancelacionOrden->notificaciones);
//							$this->Mensaje->error("Se produjo un error al recaudar la Orden de Cancelaci�n.");
	   						$this->redirect('by_socio/'.$this->data['CancelacionOrden']['cabecera_socio_id']);
						else:
							$this->Mensaje->ok("La Orden de Cancelacion fue Recaudada Correctamente!.");
							if($nReciboId == 'A'):
	   							$this->redirect('by_socio/'.$this->data['CancelacionOrden']['cabecera_socio_id']);
	   						else:
								$this->redirect('editRecibo/' . $nReciboId . '/' . $persona['Socio']['id']);
	   						endif;
						endif;
					}else{
						$this->Mensaje->error("Falta indicar la Fecha del Comprobante!.");
					}
					
				}else{
					$this->Mensaje->error("Seleccionar al menos UNA Orden de Cancelacion!");
				}
			endif;
			
		}
		
		$cmbProveedores = $oProveedores->comboProveedores('ninguno . . .');
		$this->set('cmbProveedores', $cmbProveedores);
		$this->set('bloquear', $bloquear);
		$this->set('origenFondo', $origenFondo);
		$this->set('retieneComercio', $retieneComercio);		
		
		$liquidaciones = $this->Liquidaciones->datosCombo(array('Liquidacion.imputada' => 0));
		$this->set('liquidaciones',$liquidaciones);
		
		$this->set('cancelaciones',$cancelaciones);
                

		
	}
	
	
	function terceros_generar($persona_id){
		App::import('model','Pfyj.Persona');
		$oPersona = new Persona();
		$persona = $oPersona->read(null,$persona_id);
		if(empty($persona['Socio'])) $this->redirect('list_socios');
		$this->set('persona', $persona);
		if(!empty($this->data)){
			if($this->CancelacionOrden->generarOrdenDeTerceros($this->data)){
				$this->redirect('by_socio/' . $this->data['CancelacionOrden']['socio_id']);
			}else{
				$this->Mensaje->error("Se produjo un error al generar la orden de cancelacion.");
			}
		}
		$TimeHoy = mktime(0,0,0,date('m'),date('d'),date('Y')) + (24 * 60 * 60);
		$this->set('hoy', date('d/m/Y',$TimeHoy));
	}	
	
	
}
?>