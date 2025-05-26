<?php
class OrdenDescuentoCobrosController extends MutualAppController{
	
	var $name = 'OrdenDescuentoCobros';
	
	var $autorizar = array(
							'reversos_by_socio',
							'consulta_pagos',
							'add_recibo',
							'anular',
							'editRecibo',
							'index_anterior',
							'orden_cobro_caja',
                                                        'orden_cobro_caja_mutual',
                                                        'reversar_cobros_masivo_process',
                                                        'anulados_by_socio'
	);
        
	function __construct(){
		parent::__construct();
	}        
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	function index(){
		
		if(!empty($this->data)){
			
			App::import('Model', 'Pfyj.Persona');
			$this->Persona = new Persona(null);			

			
			
			$condiciones = null;		
			$this->Persona->recursive = 2;
			
			$condiciones = array(
								'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
								'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
								'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
								'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",			
							);	
			$this->paginate = array(
									'limit' => 30,
									'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC')
									);
			$this->set('personas', $this->paginate('Persona',$condiciones));				
			

		}
		
		
		
	}
	
	function add($orden_cobro_xcaja_id=null){
		
		App::import('Model','Mutual.OrdenCajaCobro');
		$oORD = new OrdenCajaCobro();		
		
		if(!empty($this->data) && !$oORD->isProcesada($orden_cobro_xcaja_id)){
			if($this->OrdenDescuentoCobro->generarPagoByOrdenCobroByCaja($this->data)){
				$this->Mensaje->ok("EL PAGO FUE REGISTRADO CORRECTAMENTE!");
				#REDIRECCIONO A LA VISTA
				$this->redirect('view/'.$this->OrdenDescuentoCobro->getLastInsertID());					
			}else{
//				$this->Mensaje->errorGuardar();
				$this->Mensaje->errores("ERRORES: ",$this->OrdenDescuentoCobro->notificaciones);
			}
		}
		

		
		$this->set('procesada',($oORD->isProcesada($orden_cobro_xcaja_id) ? 1 : 0));
		$this->set('ocaja',$oORD->read(null,$orden_cobro_xcaja_id));
		$this->set('orden_cobro_xcaja_id',$orden_cobro_xcaja_id);
		
		
	}
	
	function add_recibo($orden_cobro_xcaja_id=null){
		$this->redirect('orden_cobro_caja/' . $orden_cobro_xcaja_id);
		
		$this->Session->del('grilla_cobros');
		
		App::import('Model','Mutual.OrdenCajaCobro');
		$oORD = new OrdenCajaCobro();
        
        App::import('model','Mutual.OrdenDescuentoCobro');
        $oOCOBRO = new OrdenDescuentoCobro();
		
//		if(!empty($this->data) && !$oORD->isProcesada($orden_cobro_xcaja_id)){
//			if($this->OrdenDescuentoCobro->recaudarOrdenCobroByCaja($this->data)){
//				$this->Mensaje->ok("EL PAGO FUE REGISTRADO CORRECTAMENTE!");
//				#REDIRECCIONO A LA VISTA
//				$this->redirect('view/'.$this->OrdenDescuentoCobro->getLastInsertID());					
//			}else{
//				$this->Mensaje->errorGuardar();
//			}
//		}
		
		if(!empty($this->data) && !$oORD->isProcesada($orden_cobro_xcaja_id)){
			$nReciboId = $oOCOBRO->recaudarOrdenCobroByCaja($this->data);
			if($nReciboId > 0){
				$this->Mensaje->ok("EL PAGO FUE REGISTRADO CORRECTAMENTE!");
   				$this->redirect('editRecibo/' . $nReciboId);
			}else{
				$this->Mensaje->errorGuardar();
			}
		}
		
		
		
		$oProveedores = $this->OrdenDescuentoCobro->importarModelo('Proveedor', 'proveedores');
		$cmbProveedores = $oProveedores->comboProveedores('ninguno . . .');
		$orden = $oORD->read(null,$orden_cobro_xcaja_id);

                $this->set('MutualProveedorId', MUTUALPROVEEDORID);
		$this->set('importe_cobrado', $orden['OrdenCajaCobro']['importe_cobrado']);
		
		$this->set('cmbProveedores', $cmbProveedores);
		$this->set('procesada',($oORD->isProcesada($orden_cobro_xcaja_id) ? 1 : 0));
		$this->set('ocaja',$oORD->read(null,$orden_cobro_xcaja_id));
		$this->set('orden_cobro_xcaja_id',$orden_cobro_xcaja_id);
		
		
	}
	
	function view($id,$toPDF=0){
            App::import('model','Mutual.OrdenDescuentoCobro');
            $oCobro = new OrdenDescuentoCobro();
            $pago = $oCobro->getCobro($id);
            $this->set('pago',$pago);
            if($toPDF==1)$this->render('reportes/cobro_pdf','pdf');
	}
	
	
	function by_socio($socio_id = null,$menuPersonas=1){
		if(empty($socio_id)) parent::noDisponible();
		if(empty($socio_id)) $this->redirect('/pfyj/personas');
		$this->OrdenDescuentoCobro->bindModel(array('belongsTo' => array('Socio')));
		$this->OrdenDescuentoCobro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuentoCobro->Socio->read(null,$socio_id);
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
//		$cobros = $this->OrdenDescuentoCobro->getCobrosBySocio($socio_id,false);
		$cobros = $this->OrdenDescuentoCobro->get_by_socio($socio_id, 0);

                
		$this->set('cobros',$cobros);
			
		
	}
	
	
	function reversar($id){

		$cobro = $this->OrdenDescuentoCobro->getCobro($id);
		
		if(empty($cobro)) parent::noDisponible();

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();	
		App::import('Model','Pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();			

		if(!empty($this->data)): 

//			if(!empty($this->data['OrdenDescuentoCobroCuota']['check_id'])){

				// Agregado Gustavo (Movimiento de caja y banco del Reverso)
				$this->BancoCuentaMovimiento = $this->OrdenDescuentoCobro->importarModelo('BancoCuentaMovimiento', 'cajabanco');
				// Array para actualizar el Movimiento de Banco
				$aBancoCuentaMovimiento = array('BancoCuentaMovimiento' => array(
					'banco_cuenta_id' => substr($this->data['OrdenDescuentoCobroCuota']['banco_cuenta_id'],1),
					'orden_descuento_cobro_id' => $id,
					'numero_operacion' => $this->data['OrdenDescuentoCobroCuota']['numero_operacion'],
					'fecha_operacion' => $this->data['OrdenDescuentoCobroCuota']['fecha_operacion'],
					'fecha_vencimiento' => $this->data['OrdenDescuentoCobroCuota']['fecha_operacion'],
					'destinatario' => $cobro['OrdenDescuentoCobro']['destinatario'],
					'descripcion' => 'REVERSO ORDEN COBRO NRO.: ' . $id . ' PERIODO: ' . $cobro['OrdenDescuentoCobro']['periodo_cobro'],
					'importe' => $this->data['OrdenDescuentoCobroCuota']['suma_reverso'] + $this->data['SocioReintegro']['suma_reverso'],
					'debe_haber' => 1,
					'tipo' => 9
					
				));
                
				if(!$this->BancoCuentaMovimiento->save($aBancoCuentaMovimiento)) $this->redirect("/mutual/orden_descuento_cobros/by_socio/".$cobro['OrdenDescuentoCobro']['socio_id']);
				$nBancoCuentaMovimientoId = $this->BancoCuentaMovimiento->id;
				

				
				
				App::import('model','Mutual.OrdenDescuentoCobroCuota');
				$oCCUOTA = new OrdenDescuentoCobroCuota();	
				$error = false;
//				$periodo = $this->data['OrdenDescuentoCobroCuota']['periodo_proveedor_reverso']['year'].$this->data['OrdenDescuentoCobroCuota']['periodo_proveedor_reverso']['month'];
                $periodo = $this->data['Liquidacion']['periodo'];
				if(!empty($this->data['OrdenDescuentoCobroCuota']['check_id'])){
					foreach($this->data['OrdenDescuentoCobroCuota']['check_id'] as $id => $chk):
						$importeReversado = $this->data['OrdenDescuentoCobroCuota']['id1'][$id];
						if(!$oCCUOTA->reversarCobro($id,$periodo,$importeReversado, $nBancoCuentaMovimientoId)):
							$this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR LOS REVERSOS!");
							$error = true;
							break;
						endif;
					endforeach;
				}
//				if(!$error):
//					$this->redirect("/mutual/orden_descuento_cuotas/estado_cuenta/".$cobro['OrdenDescuentoCobro']['socio_id']."/1");
//				else:
//					$this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR LOS REVERSOS!");
//				endif;

				#PROCESO EL REVERSO DEL REINTEGRO
				if(!empty($this->data['SocioReintegro']['check_id'])){
				
					foreach($this->data['SocioReintegro']['check_id'] as $id => $chk):
					
						$importeReversado = $this->data['SocioReintegro']['id1'][$id];
						if(!$oREINTEGRO->reversar($id,$periodo,$importeReversado, $nBancoCuentaMovimientoId)):
							$this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR LOS REVERSOS!");
							$error = true;
							break;
						endif;
					endforeach;
				
				}
				if(!$error){
					$this->redirect("/mutual/orden_descuento_cobros/by_socio/".$cobro['OrdenDescuentoCobro']['socio_id']);
				}
				
//			}else{
//			
//				$this->Mensaje->error("Debe seleccionar al menos un pago!.");
//				
//				
//			}		
		
		endif;
		
		
		$socio = $oSOCIO->read(null,$cobro['OrdenDescuentoCobro']['socio_id']);
		if(empty($socio)) parent::noDisponible();
		
		#CARGO LOS REINTEGROS QUE PUEDA HABER PARA EL PERIODO
		$reintegros = $oREINTEGRO->getReintegrosPendientesBySocio($cobro['OrdenDescuentoCobro']['socio_id'],false,$cobro['OrdenDescuentoCobro']['periodo_cobro']);
		
		$this->set('socio',$socio);
		$this->set('cobro',$cobro);
		$this->set('id',$id);
		$this->set('periodo_corte',$oSOCIO->periodoCorte());
		$this->set('reintegros',$reintegros);
//		exit;
	}
	
	
	function reversos_by_socio($socio_id = null){
		if(empty($socio_id)) parent::noDisponible();

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();	
		$socio = $oSOCIO->read(null,$socio_id);
		if(empty($socio)) parent::noDisponible();
		$this->set('socio',$socio);
		
		App::import('model','Mutual.OrdenDescuentoCobroCuota');
		$oCCUOTA = new OrdenDescuentoCobroCuota();		
		
		$reversos = $oCCUOTA->getCuotasReversadasBySocioByPeriodo($socio_id);
		$this->set('reversos',$reversos);
		
		
	}
	
	
	function consulta_pagos($socio_id){

		if(empty($socio_id)) parent::noDisponible();
		$this->OrdenDescuentoCobro->bindModel(array('belongsTo' => array('Socio')));
		$this->OrdenDescuentoCobro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuentoCobro->Socio->read(null,$socio_id);
		$this->set('menuPersonas',1);
		$this->set('socio',$socio);		
		if(empty($socio)) parent::noDisponible();
		
		App::import('model','Mutual.LiquidacionSocio');
		$oLiqSoc = new LiquidacionSocio();			
		$periodos = $oLiqSoc->periodosBySocio($socio_id,'DESC');
		
		$this->set('periodos',$periodos);
		
		if(!empty($this->data)):

			$cobros = $this->OrdenDescuentoCobro->getCobrosBySocioByPeriodosByTipoCobro($this->data['OrdenDescuentoCobro']['socio_id'],$this->data['OrdenDescuentoCobro']['periodo_desde'],$this->data['OrdenDescuentoCobro']['periodo_hasta'],$this->data['OrdenDescuentoCobro']['tipo_cobro']);
//			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
//			$oCobroCuota = new OrdenDescuentoCobroCuota();
//		
//			$cobros = $oCobroCuota->getCuotasBySocioByTipoPago($this->data['OrdenDescuentoCobro']['socio_id'],$this->data['OrdenDescuentoCobro']['tipo_cobro'],$this->data['OrdenDescuentoCobro']['periodo_desde'],$this->data['OrdenDescuentoCobro']['periodo_hasta'],true,'ASC');
			
			
		endif;
		
		if ($this->RequestHandler->isAjax()):
		
			$this->render('consulta_pagos_ajax','ajax');
		else:
			$this->render('consulta_pagos');
		endif;
		
		
	}
	
	function anular($id, $nSocioId){
		if($this->OrdenDescuentoCobro->anularCobro($id)):
			$this->Mensaje->okGuardar();
		else:
			$this->Mensaje->errorGuardar();
		endif;
   		$this->redirect('by_socio/' . $nSocioId);
	}
	
	
	function editRecibo($nReciboId=null){
		if(empty($nReciboId)) $this->redirect('index');

		if(!empty($this->data)):
			if ($this->OrdenDescuentoCobro->anularCobro(0, $this->data['Recibo']['id'])):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
			
   			$this->redirect('index');
				
		endif;

		$aRecibo = $this->OrdenDescuentoCobro->getRecibo($nReciboId);
//		$aRecibo['Recibo']['liquidacion_intercambio_id'] = $lqdIntercambioId;
//		$aRecibo['Recibo']['liquidacion_id'] = $liquidacionId;
//		$aRecibo['Recibo']['action'] = "editRecibo/" . $nReciboId . '/' . $nSocioId;
		$aRecibo['Recibo']['action'] = "editRecibo/" . $nReciboId;
//		$aRecibo['Recibo']['url'] = '/mutual/OrdenDescuentoCobros/editRecibo/0';
		$aRecibo['Recibo']['url'] = '/mutual/orden_caja_cobros/';	
		$this->set('Recibo', $aRecibo);
//		$this->render('recibos/recibo');
		
	}
		
	
	function orden_cobro_caja($orden_cobro_caja_id){
            
            # DESVIO PARA QUE LA RECAUDACION NO PIDA EL ORIGEN DE LOS FONDOS. 
            # RECAUDA DIRECTAMENTE LA MUTUAL. SI EL CODIGO EXISTE Y EL CAMPO
            # ENTERO_1 ES IGUAL A 1 PIDE EL ORIGEN DE LOS FONDOS. ESTO ES PARA
            # LA MUTUAL AMAN. CASO CONTRARIO NO TIENE EN CUENTA EL ORIGEN DE LOS
            # FONDOS NI EL COMPENSA PAGO.
        
            App::import('model','Mutual.CancelacionOrden');
            $oCANCELACION = new CancelacionOrden();        
        
            $recauda = $oCANCELACION->getGlobalDato('entero_1', 'MUTUMUTURECA');
            $recauda = (empty($recauda['GlobalDato']['entero_1']) ? 0 : $recauda['GlobalDato']['entero_1']);
            if($recauda === 0):
                $this->redirect('orden_cobro_caja_mutual/'.$orden_cobro_caja_id);
            endif;


            $this->Session->del('grilla_cobros');

            App::import('Model','Mutual.OrdenCajaCobro');
            $oORD = new OrdenCajaCobro();		

            $bloquear = 0;
            $origenFondo = MUTUALPROVEEDORID;
            if(!empty($this->data) && !$oORD->isProcesada($orden_cobro_caja_id)):
                
                if(isset($this->data['OrdenCobro']['Cabecera']) && $this->data['OrdenCobro']['Cabecera'] == 1):
                    $bloquear = 1;
                    $origenFondo = $this->data['OrdenCobro']['proveedor_origen_fondo_id'];
                else:
                    $nReciboId = $this->OrdenDescuentoCobro->orden_cobro_caja($this->data);
                    if(!$nReciboId):
                        $this->Mensaje->errores("ERRORES: ",$this->OrdenDescuentoCobro->notificaciones);
                    else:
                        $this->Mensaje->ok("EL PAGO FUE REGISTRADO CORRECTAMENTE!");
                        if($nReciboId > 0):
                            $this->redirect('editRecibo/' . $nReciboId);
                        else:
                            $this->redirect('/mutual/orden_caja_cobros');
                        endif;
                    endif;
                endif;
            endif;


            $oProveedores = $this->OrdenDescuentoCobro->importarModelo('Proveedor', 'proveedores');
            $cmbProveedores = $oProveedores->comboProveedores('ninguno . . .');
            $oORD->recursive = 3;
            $oORD->bindModel(array('hasMany' => array('OrdenCajaCobroCuota')));
            $oORD->OrdenCajaCobroCuota->bindModel(array('belongsTo' => array('OrdenDescuentoCuota')));
            $oORD->OrdenCajaCobroCuota->OrdenDescuentoCuota->bindModel(array('belongsTo' => array('OrdenDescuento','Proveedor')));
            $orden = $oORD->read(null,$orden_cobro_caja_id);

            if($bloquear == 1):
                if($this->data['OrdenCobro']['proveedor_origen_fondo_id'] != MUTUALPROVEEDORID):
                    $importe_cobrado = 0;
                    foreach($orden['OrdenCajaCobroCuota'] as $cuota):
                        if($cuota['OrdenDescuentoCuota']['proveedor_id'] != $this->data['OrdenCobro']['proveedor_origen_fondo_id']):
                            $importe_cobrado += $cuota['importe_abonado'];
                        endif; 
                    endforeach;
                    $this->set('importe_cobrado', $importe_cobrado);
                else:
                    $this->set('importe_cobrado', $orden['OrdenCajaCobro']['importe_cobrado']);
                endif;
            else:
                $this->set('importe_cobrado', $orden['OrdenCajaCobro']['importe_cobrado']);
            endif;

            $this->set('MutualProveedorId', MUTUALPROVEEDORID);

            $this->set('importe_total', $orden['OrdenCajaCobro']['importe_cobrado']);
            $this->set('bloquear', $bloquear);
            $this->set('cmbProveedores', $cmbProveedores);
            $this->set('procesada',($oORD->isProcesada($orden_cobro_caja_id) ? 1 : 0));
            $this->set('ocaja',$oORD->read(null,$orden_cobro_caja_id));
            $this->set('origenFondo', $origenFondo);
            $this->set('orden_cobro_caja_id',$orden_cobro_caja_id);
            $tipoCobro = ($origenFondo == MUTUALPROVEEDORID ? 'MUTUTCOBCAJA' : 'MUTUTCOBCACO');
            $this->set('tipo_cobro', $tipoCobro);
            $this->set('fechaCobro',date('Y-m-d'));
		
	}
		
	
	function orden_cobro_caja_mutual($orden_cobro_caja_id){
            
            $this->Session->del('grilla_cobros');

            App::import('Model','Mutual.OrdenCajaCobro');
            $oORD = new OrdenCajaCobro();		

            $origenFondo = MUTUALPROVEEDORID;
            if(!empty($this->data) && !$oORD->isProcesada($orden_cobro_caja_id)):

                $nReciboId = $this->OrdenDescuentoCobro->orden_cobro_caja($this->data);
                if(!$nReciboId):
                    $this->Mensaje->errores("ERRORES: ",$this->OrdenDescuentoCobro->notificaciones);
                else:
                    $this->Mensaje->ok("EL PAGO FUE REGISTRADO CORRECTAMENTE!");
                    if($nReciboId > 0):
                        $this->redirect('editRecibo/' . $nReciboId);
                    else:
                        $this->redirect('/mutual/orden_caja_cobros');
                    endif;
                endif;
            endif;


            $oProveedores = $this->OrdenDescuentoCobro->importarModelo('Proveedor', 'proveedores');
            $cmbProveedores = $oProveedores->comboProveedores('ninguno . . .');
            $oORD->recursive = 3;
            $oORD->bindModel(array('hasMany' => array('OrdenCajaCobroCuota')));
            $oORD->OrdenCajaCobroCuota->bindModel(array('belongsTo' => array('OrdenDescuentoCuota')));
            $oORD->OrdenCajaCobroCuota->OrdenDescuentoCuota->bindModel(array('belongsTo' => array('OrdenDescuento','Proveedor')));
            $orden = $oORD->read(null,$orden_cobro_caja_id);

            $this->set('MutualProveedorId', MUTUALPROVEEDORID);

            $this->set('importe_cobrado', $orden['OrdenCajaCobro']['importe_cobrado']);
            $this->set('importe_total', $orden['OrdenCajaCobro']['importe_cobrado']);
            $this->set('cmbProveedores', $cmbProveedores);
            $this->set('procesada',($oORD->isProcesada($orden_cobro_caja_id) ? 1 : 0));
            $this->set('ocaja',$oORD->read(null,$orden_cobro_caja_id));
            $this->set('origenFondo', $origenFondo);
            $this->set('orden_cobro_caja_id',$orden_cobro_caja_id);

            $this->set('fechaCobro',date('Y-m-d'));
		
	}

    
    function cobranza_especial(){
        
    }
    
    /*
INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `quick`, `icon`, `activo`, `parent`) 
VALUES ('249', 'Reversar Pagos', '/mutual/orden_descuento_cobros/reversar_cobros_masivo', '249', '1', '0', 'arrow_right2.gif', '1', '200');
insert into grupos_permisos values(1,249);
     *      */
    function reversar_cobros_masivo($UID = null) {
    
        $listadoReverso = array();
        $sinCoincidencia = false;
        $paraReversar = false;
        
        if(!empty($this->data)) {
            
            App::import('Model','Config.Banco');
            $oBANCO = new Banco();  
            App::import('model','Mutual.OrdenDescuentoCobroCuota');
            $oCCUOTA = new OrdenDescuentoCobroCuota();            

            App::import('model','Mutual.OrdenDescuentoCobro');
            $oCOBRO = new OrdenDescuentoCobro();            

            if($this->data['reversos']['archivo_datos']['error'] == 0) {
                
                if ($this->Session->check($UID . "_REVERSOS")) {
                    $this->Session->del($UID . "_REVERSOS");
                }                
                
                $UID = String::uuid();
                
                $registros = $this->leerArchivo($this->data['reversos']['archivo_datos']['tmp_name']);

                $n = 1;
                
                $BANCO = $this->data['reversos']['banco_intercambio'];
                
                // Inicializar array para acumular los datos por socio y liquidación
                $acumulado = [];

                foreach ($registros as $registro) {
                    
                    $socio = $liquidacion = NULL;
                    
                    switch ($BANCO) {

                        // COBRO DIGITAL
                        case '99910':
                            if (strlen($registro) >= 87) { 
                                $socio = (int) substr($registro, 0, 6);
                                $importe = floatval(substr($registro, 75, 12)) / 100;
                                $liquidacion = isset($this->data['reversos']['liquidacion']) ? $this->data['reversos']['liquidacion'] : null;
                                if(empty($liquidacion)) {
                                    $this->Mensaje->errores("ERRORES: ",array('Para COBRO DIGITAL debe indicar el nro de liquidación'));
                                }
                            }
                            break;

                        // COMAFI y 90299 (misma lógica)
                        case '00299':
                        case '90299':
                            if (strlen($registro) >= 71) {
                                $codTrx = substr($registro, 0, 3);
                                if ($codTrx === '070') {
                                    $socio = (int) substr($registro, 16, 5);
                                    $importe = floatval(substr($registro, 61, 10)) / 100;
                                    $liquidacion = (int) substr($registro, 21, 4);
                                }
                            }
                            break;

                        // Banco 00011
                        case '90011':
                        case '91011':                             
                        case '00011':
                            if (strlen($registro) >= 91) {
                                $liquidacion = (int) substr($registro, 72, 4);
                                $socio = (int) substr($registro, 76, 6);
                                $importe = floatval(substr($registro, 18, 15)) / 100;
                            }
                            break;

                        // Default (caso general para otros bancos)
                        default:
                            if (strlen($registro) >= 98) {
                                $data = substr($registro, 74, 24);
                                $importe = floatval(substr($data, 0, 11)) / 100;
                                $socio = (int) substr($data, 11, 7);
                                $liquidacion = (int) substr($data, 18, 6);
                            }
                            break;
                    }
                  
                    
                    // Generar una clave única para la combinación de socio y liquidación
                    $clave = $socio . '-' . $liquidacion;

                    // Si la combinación socio-liquidación ya existe en el array, acumular el importe
                    if(!empty($socio) && !empty($liquidacion)) {
                        if (isset($acumulado[$clave])) {
                            $acumulado[$clave]['importe'] += $importe;
                        } else {
                            // Si no existe, inicializar la entrada con socio, liquidación e importe
                            $acumulado[$clave] = [
                                'socio' => $socio,
                                'liquidacion' => $liquidacion,
                                'importe' => $importe
                            ];
                        }                        
                    }
                }

                foreach ($acumulado as $clave => $info) {

                    $socio_id = $info['socio'];
                    $liquidacion_id = $info['liquidacion'];
                    $importe_acumulado = round(floatval($info['importe']),2);

                    // Consulta SQL para obtener el importe de la base de datos
                    $sql = "
                        SELECT 
                            p.documento,
                            CONCAT(p.apellido, p.nombre) AS apenom,
                            lc.liquidacion_id,
                            lc.socio_id,
                            org.concepto_1 organismo,
                            co.id cobro,
                            co.fecha,
                            tco.concepto_1 tipo_cobro,
                            SUM(IFNULL(odcc.importe, 0)) AS importe,
                            GROUP_CONCAT(lc.orden_descuento_cuota_id) cuota_ids,
                            GROUP_CONCAT(odcc.id) cobro_cuota_ids,
                            (select count(*) from orden_descuento_cobro_cuotas cc where cc.orden_descuento_cobro_id = lc.orden_descuento_cobro_id 
                                    and cc.reversado = 0) cuotas
                        FROM
                            liquidacion_cuotas lc
                        INNER JOIN liquidaciones l ON l.id = lc.liquidacion_id
                        INNER JOIN global_datos org ON org.id = l.codigo_organismo
                        LEFT JOIN orden_descuento_cobro_cuotas odcc ON
                            odcc.orden_descuento_cobro_id = lc.orden_descuento_cobro_id
                            AND odcc.orden_descuento_cuota_id = lc.orden_descuento_cuota_id
                        INNER JOIN socios s ON s.id = lc.socio_id
                        INNER JOIN personas p ON p.id = s.persona_id
                        LEFT JOIN orden_descuento_cobros co ON co.id = lc.orden_descuento_cobro_id
                        LEFT JOIN global_datos tco ON tco.id = co.tipo_cobro
                        WHERE
                            lc.liquidacion_id = $liquidacion_id
                            AND lc.socio_id = $socio_id
                        GROUP BY lc.liquidacion_id, lc.socio_id";

                    $datos = $oCCUOTA->query($sql);
                    $importe_bd = 0;
                    if (!empty($datos)) {
                        $importe_bd = round(floatval($datos[0][0]['importe']),2);
                        if ($importe_acumulado == $importe_bd) {
                            $listadoReverso[$n] = array(
                                'params' => array($registro, $liquidacion, $socio_id, $importe_acumulado),
                                'datos' => (!empty($datos[0]) ? $datos[0] : array())
                            );                            
                        }                        
                    }
                    if(empty($datos[0]) || floatval($importe_acumulado) !== floatval($importe_bd)) {
                        $sql = "select 
                                ls.documento
                                ,ls.apenom
                                ,ls.liquidacion_id
                                ,ls.socio_id
                                ,org.concepto_1 organismo
                                ,$importe_acumulado as importe_debitado
                                 from liquidacion_socios ls
                                inner join liquidaciones l on l.id = ls.liquidacion_id 
                                INNER JOIN global_datos org ON org.id = l.codigo_organismo
                                where ls.liquidacion_id = $liquidacion_id and ls.socio_id = $socio_id group by ls.socio_id;";
                        $datos = $oCCUOTA->query($sql);
                        if(isset($datos[0])) {
                            $sinCoincidencia = true;
                            $listadoReverso[$n]['sin_info'] = $datos[0];
                            $listadoReverso[$n]['sql'] = $sql;
                        }
                    } else {
                        if($datos[0][0]['cuotas'] > 0) {
                            $paraReversar = true;
                        }
                    }                     

                    $n++;

                }
                $this->Session->write($UID."_REVERSOS",$listadoReverso);
                
            } else {
                $this->Mensaje->errores("ERRORES: ",array('Error al leer el archivo'));
            }
            
        } else if(!empty($UID) && $this->Session->check($UID."_REVERSOS")) {
            
            $this->Session->del($UID . "_REVERSOS");
        }
        
        $this->set('UID',$UID);
        $this->set('paraReversar',$paraReversar);
        $this->set('sinCoincidencia',$sinCoincidencia);
        $this->set('listadoReverso',$listadoReverso);
        
    }

    function reversar_cobros_masivo_process() {
        
        if(!empty($this->data)) {
            
            App::import('model','Mutual.OrdenDescuentoCobroCuota');
            $oCCUOTA = new OrdenDescuentoCobroCuota();            

            App::import('model','Mutual.OrdenDescuentoCobro');
            $oCOBRO = new OrdenDescuentoCobro();     
            
            $UID = $this->data['OrdenDescuentoCobro']['uuid'];
            
            $listadoReverso = $this->Session->read($UID."_REVERSOS");

            
            foreach ($listadoReverso as $n => $reverso) {

                if(!empty($reverso['datos'])) {
                    
                    $cobro = $oCOBRO->read(null, $reverso['datos']['co']['cobro']);
                    if(!empty($cobro['OrdenDescuentoCobroCuota']) && $reverso['datos'][0]['cuotas'] !== "0") {
                        foreach ($cobro['OrdenDescuentoCobroCuota'] as $cuota) {
                            if(!$oCCUOTA->reversarCobro($cuota['id'],$this->data['Liquidacion']['periodo'],$cuota['importe'])):
                                    $this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR LOS REVERSOS!");
                                    $error = true;
                                    break;
                            endif;
                        }
                    }                        
                }
            }
        }
        
        $this->Session->del($UID . "_REVERSOS");
        $this->redirect("reversar_cobros_masivo");            
        
    }
    
    function anulados_by_socio($socio_id = null) {
        if(empty($socio_id)) parent::noDisponible();

        App::import('Model','Pfyj.Socio');
        $oSOCIO = new Socio();	
        $socio = $oSOCIO->read(null,$socio_id);
        if(empty($socio)) parent::noDisponible();
        $this->set('socio',$socio);
        $cobros = $this->OrdenDescuentoCobro->get_by_socio($socio_id, 1);
        $this->set('cobros',$cobros);        
    }
}
?>