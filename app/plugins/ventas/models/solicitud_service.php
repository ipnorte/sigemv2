<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SolicitudService extends VentasAppModel{
    
    var $name = 'SolicitudService';
    var $useTable = false;
    var $oUSER = null;
    var $limiteMenorEdad = NULL;
    var $limiteMayorEdad = NULL;    
    
    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        App::import('model','seguridad.Usuario');
        $this->oUSER = new Usuario();  
        $this->limiteMenorEdad = date("d-m-Y",strtotime(date('Y-m-d')."- 18 year"));
        $this->limiteMayorEdad = date("d-m-Y",strtotime(date('Y-m-d')."- 90 year"));
        
    }
            
    function get_solicitud($id){
        
        App::import('model','mutual.MutualProductoSolicitud');
        $oSOLICITUD = new MutualProductoSolicitud(); 
        $solicitud = $oSOLICITUD->getOrden($id);
        $solicitud = $oSOLICITUD->armaDatos($solicitud, false, true); 
        $vendedorId = $this->oUSER->get_vendedorId_logon(true);
        $vendedorArray = array();
        if(!empty($vendedorId)){
            $vendedorArray = explode(',', $vendedorId);
        }        
        if(!empty($vendedorArray) && !in_array($solicitud['MutualProductoSolicitud']['vendedor_id'], $vendedorArray)){
        //if(!empty($vendedorId) && $vendedorId != $solicitud['MutualProductoSolicitud']['vendedor_id']){
            return null;
        }
        return $solicitud;
    }
    
    function get_solicitudes($params,$filtraVendedor=TRUE){
        App::import('model','mutual.MutualProductoSolicitud');
        $oSOLICITUD = new MutualProductoSolicitud();
        $fecha_desde = $fecha_hasta = $numero = $documento = $estado = $vendedor_id = null;
        $fecha_desde = (isset($params['fecha_desde']) ? $params['fecha_desde'] : null);
        $fecha_hasta = (isset($params['fecha_hasta']) ? $params['fecha_hasta'] : null);
        $numero = (isset($params['numero']) ? $params['numero'] : null);
        $documento = (isset($params['documento']) ? $params['documento'] : null);
        $estado = (isset($params['estado']) ? $params['estado'] : null);
        $persona_id = (isset($params['persona_id']) ? $params['persona_id'] : null);
        $aprobada = (isset($params['aprobada']) ? $params['aprobada'] : null);
        $anulada = (isset($params['anulada']) ? $params['anulada'] : null);
        $estadoNotIn = (isset($params['estadoNotIn']) ? $params['estadoNotIn'] : null);
        
        $vendedor_id = null;
        if($filtraVendedor)$vendedor_id = $this->oUSER->get_vendedorId_logon(true);
        $solicitudes = $oSOLICITUD->getSolicitudesByVendedorGrillaModuloVentas($vendedor_id, $estado, $fecha_desde, $fecha_hasta,$numero,$documento,$persona_id,$aprobada,$anulada,$estadoNotIn);
        return $solicitudes;
    }
    
    function get_persona_by_cuit($cuit){
        App::import('model','pfyj.Persona');
        $oPERSONA = new Persona();
        $persona = $oPERSONA->getByCUIT($cuit);
        if(empty($persona)){
            //busco por el documento
            $id = $oPERSONA->getIdByDocumento(substr($cuit,2,8));
            $persona = $oPERSONA->getPersona($id);
            if(!empty($persona))$persona['Persona']['cuit_cuil'] = $cuit;
        }
        return $persona;
    }
    
    function get_persona_by_dni($ndoc){
        App::import('model','pfyj.Persona');
        $oPERSONA = new Persona();
        $persona = $oPERSONA->getByNdoc($ndoc,FALSE);
        return (!empty($persona) ? $persona[0] : NULL) ;
    }     
    
    function get_persona_by_id($id){
        App::import('model','pfyj.Persona');
        $oPERSONA = new Persona();
        $persona = $oPERSONA->getPersona($id);
        return $persona;
    }    
    
    function set_persona($persona){
        App::import('model','pfyj.Persona');
        $oPERSONA = new Persona();
        if(!$oPERSONA->save($persona)){
            parent::notificar("SE PRODUJO UN ERROR AL GUARDAR / ACTUALIZAR DATOS DE LA PERSONA");
            return false;
        }
        if(empty($persona['Persona']['id'])) $persona['Persona']['id'] = $oPERSONA->getLastInsertID();
        $persona = $oPERSONA->getPersona($persona['Persona']['id']);
        return $persona['Persona'];
    }
    
    function validate_datos_personales($solicitud){
        App::import('model','pfyj.Persona');
        $oPERSONA = new Persona();
        $validate = $oPERSONA->validar_datos_personales($solicitud['Persona']);
        $this->notificaciones = $oPERSONA->notificaciones;
        $this->invalidFields = $oPERSONA->invalidFields;
        return $oPERSONA->validar_datos_personales($solicitud['Persona']);
    }
    
    function get_beneficio_by_id($id){
        App::import('model','pfyj.PersonaBeneficio');
        $oBEN = new PersonaBeneficio();
        $beneficio = $oBEN->getBeneficio($id);
        return $beneficio;
    }
    
    function set_beneficio($beneficio){
        App::import('model','pfyj.PersonaBeneficio');
        $oBEN = new PersonaBeneficio();
        if(!$oBEN->guardar($beneficio, TRUE)){
            $this->notificaciones = $oBEN->notificaciones;
            return false;
        }
        return true;
    }
    
    function set_tarjetaDebito($data){
        App::import('model','pfyj.PersonaBeneficio');
        $oBEN = new PersonaBeneficio();
        if(!$oBEN->actualizarTarjetaDebito($data)){
            $this->notificaciones = $oBEN->notificaciones;
            return false;
        }
        return true;
    }
    
    
    function get_cancelaciones_by_socio($socio_id){
        App::import('model','mutual.CancelacionOrden');
        $oCANC = new CancelacionOrden();
        $cancelaciones = null;
        if(isset($socio_id) && !empty($socio_id)) $cancelaciones = $oCANC->getPendientes($socio_id);
        return $cancelaciones;      
    }
    
    function get_cancelacion($id){
        App::import('model','mutual.CancelacionOrden');
        $oCANC = new CancelacionOrden();
        return $oCANC->get($id);
    }
    
    function get_vendedores(){
        App::import('model','ventas.Vendedor');
        $oVENDEDOR = new Vendedor();
        $vendedores = $oVENDEDOR->getVendedores(true);        
        return $vendedores;
    }
    
    function get_plan($id){
        App::import('Model','proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        $plan = $oPLAN->getPlan($id);
        $plan = $oPLAN->arma_datos($plan);
        return $plan;        
    }
    
    
    function get_cuota($id){
        App::import('Model','proveedores.ProveedorPlanGrillaCuota');
        $oGRILLA = new ProveedorPlanGrillaCuota();
        $cuota = $oGRILLA->read(null,$id); 
        return $cuota;
    }
    
    
    function generar_solicitud($datos){
        
//        debug($datos);
//        exit;
        
        App::import('model','mutual.MutualProductoSolicitud');
        $oSOLICITUD = new MutualProductoSolicitud();

        $oSOLICITUD->begin();
        
        $solicitud = array();
        $solicitud['MutualProductoSolicitud'] = array();

        $solicitud['MutualProductoSolicitud']['id'] = 0;
        $solicitud['MutualProductoSolicitud']['aprobada'] = 0;
        $solicitud['MutualProductoSolicitud']['anulada'] = 0;
        $solicitud['MutualProductoSolicitud']['fecha'] = date('Y-m-d');
        $solicitud['MutualProductoSolicitud']['estado']  = 'MUTUESTA0001';
        $solicitud['MutualProductoSolicitud']['observaciones']  =  $datos['observaciones'];
        $solicitud['MutualProductoSolicitud']['forma_pago']  =  $datos['forma_pago'];
        if(isset($datos['siisa'])) {
            $solicitud['MutualProductoSolicitud']['siisa']  =  $datos['siisa']; 
        }
        
        if(!isset($datos['MutualProductoSolicitud']['vendedor_id']) || empty($datos['MutualProductoSolicitud']['vendedor_id'])) $solicitud['MutualProductoSolicitud']['vendedor_id'] = $this->oUSER->get_vendedorId_logon();
        else $solicitud['MutualProductoSolicitud']['vendedor_id'] = $datos['MutualProductoSolicitud']['vendedor_id'];
        
        if(empty($datos['MutualProductoSolicitud']['vendedor_id'])){
            $codigoEstado = parent::GlobalDato("concepto_2", "MUTUESTA0001");
            $codigoEstado = (empty($codigoEstado) ? "MUTUESTA0001" : $codigoEstado);            
            $solicitud['MutualProductoSolicitud']['estado']  = $codigoEstado;
        }
        
        $solicitud['MutualProductoSolicitud']['tipo_orden_dto'] = parent::GlobalDato('concepto_3',$datos['Plan']['ProveedorPlan']['tipo_producto']);
        $solicitud['MutualProductoSolicitud']['tipo_producto'] = $datos['Plan']['ProveedorPlan']['tipo_producto'];
        $solicitud['MutualProductoSolicitud']['ayuda_economica'] = $datos['Plan']['ProveedorPlan']['ayuda_economica'];

        $solicitud['MutualProductoSolicitud']['proveedor_id'] = $datos['Plan']['ProveedorPlan']['proveedor_id'];

        $solicitud['MutualProductoSolicitud']['persona_id'] = $datos['Persona']['id'];
        $solicitud['MutualProductoSolicitud']['socio_id'] = $datos['Persona']['socio_nro'];
        $solicitud['MutualProductoSolicitud']['persona_beneficio_id'] = $datos['Beneficio']['id'];
        $solicitud['MutualProductoSolicitud']['proveedor_plan_id'] = $datos['Plan']['ProveedorPlan']['id'];
        
        

        $TNA = $datos['Cuota']['ProveedorPlanGrilla']['tna'];
        $TNM = $datos['Cuota']['ProveedorPlanGrilla']['tnm'];
        $GTO = $datos['Cuota']['ProveedorPlanGrilla']['gasto_admin'];
        $SELL = $datos['Cuota']['ProveedorPlanGrilla']['sellado'];
        $IVA = $datos['Cuota']['ProveedorPlanGrilla']['iva'];
        $METODO = $datos['Cuota']['ProveedorPlanGrilla']['metodo_calculo'];

        $solicitud['MutualProductoSolicitud']['importe_cuota'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['importe'];
        $solicitud['MutualProductoSolicitud']['cuotas'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['cuotas'];

        $solicitud['MutualProductoSolicitud']['importe_total'] = $solicitud['MutualProductoSolicitud']['importe_cuota'] * $solicitud['MutualProductoSolicitud']['cuotas'];
        $solicitud['MutualProductoSolicitud']['importe_solicitado'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['capital'];
        $solicitud['MutualProductoSolicitud']['importe_percibido'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['liquido'];

        $solicitud['MutualProductoSolicitud']['tna'] = $TNA;
        $solicitud['MutualProductoSolicitud']['tnm'] = $TNM;
        $solicitud['MutualProductoSolicitud']['cft'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['cft'];

        $solicitud['MutualProductoSolicitud']['capital_puro'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['capital_puro'];
        $solicitud['MutualProductoSolicitud']['interes'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['interes']; 

        $solicitud['MutualProductoSolicitud']['gasto_admin'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['gasto_admin'];
        $solicitud['MutualProductoSolicitud']['gasto_admin_porc'] = $GTO;

        $solicitud['MutualProductoSolicitud']['sellado'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['sellado'];
        $solicitud['MutualProductoSolicitud']['sellado_porc'] = $SELL;

        $solicitud['MutualProductoSolicitud']['iva'] = $datos['Cuota']['ProveedorPlanGrillaCuota']['iva'];
        $solicitud['MutualProductoSolicitud']['iva_porc'] = $IVA;                

        $solicitud['MutualProductoSolicitud']['metodo_calculo'] = $METODO;
        
        $solicitud['MutualProductoSolicitud']['sueldo_neto']  = (isset($datos['Beneficio']['sueldo_neto']) ? $datos['Beneficio']['sueldo_neto'] : 0);
        $solicitud['MutualProductoSolicitud']['debitos_bancarios']  = (isset($datos['Beneficio']['debitos_bancarios']) ? $datos['Beneficio']['debitos_bancarios'] : 0);
        
        
        //calculo vencimientos
        App::import('Model', 'Proveedores.ProveedorVencimiento');
        $this->ProveedorVencimiento = new ProveedorVencimiento(null);
        $vtos = $this->ProveedorVencimiento->calculaVencimiento($solicitud['MutualProductoSolicitud']['proveedor_id'],$solicitud['MutualProductoSolicitud']['persona_beneficio_id'],$solicitud['MutualProductoSolicitud']['fecha']);
        
        App::import('Model', 'Proveedores.Proveedor');
        $oPROVEEDOR = new Proveedor();
        $proveedor = $oPROVEEDOR->read('liquida_prestamo',$solicitud['MutualProductoSolicitud']['proveedor_id']);
        if(isset($proveedor['Proveedor']['liquida_prestamo']) && $proveedor['Proveedor']['liquida_prestamo']){
            $solicitud['MutualProductoSolicitud']['prestamo'] = 1;
        }
        
        $solicitud['MutualProductoSolicitud']['fecha'] = date('Y-m-d');
        $solicitud['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
        $solicitud['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
        $solicitud['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];   
        
        //CARGAR LOS PERIODOS Y VENCIMIENTOS EN EL DETALLE DEL CALCULO
        if(isset($datos['Cuota']['ProveedorPlanGrillaCuota']['calculo']) && !empty($datos['Cuota']['ProveedorPlanGrillaCuota']['calculo'])){
            
            App::import('model', 'proveedores.MetodoCalculoCuota');
            $oCALC = new MetodoCalculoCuota();
            $solicitud['MutualProductoSolicitud']['detalle_calculo_plan'] = $oCALC->setPeriodosAndVencimientos(
                json_decode($datos['Cuota']['ProveedorPlanGrillaCuota']['calculo']),
                $solicitud['MutualProductoSolicitud']['periodo_ini'],
                $solicitud['MutualProductoSolicitud']['primer_vto_socio'],
                $solicitud['MutualProductoSolicitud']['primer_vto_proveedor'],
                $this,
                TRUE
                );
            
        }
        
        
        $totalCancela = 0;
        $formaPagoCancelacion = array();
        if(!empty($datos['Cancelaciones'])){
            $cancelaciones = array();
            App::import('model','mutual.CancelacionOrden');
            $oCANC = new CancelacionOrden();
            foreach($datos['Cancelaciones'] as $cancelacionId => $cancelacion){
                $totalCancela += $cancelacion['CancelacionOrden']['importe_proveedor'];
                array_push($cancelaciones, array('mutual_producto_solicitud_id' => 0,'cancelacion_orden_id' => $cancelacionId));
                $formaPago = array();
                $formaPago['id'] = 0;
                $formaPago['mutual_producto_solicitud_id'] = 0;
                $formaPago['a_la_orden_de'] = 'A LA ORDEN DE ' . $cancelacion['CancelacionOrden']['a_la_orden_de'];
                $formaPago['concepto'] = (!empty($cancelacion['CancelacionOrden']['concepto']) ? $cancelacion['CancelacionOrden']['concepto'] : "CANCELACION #".$cancelacion['CancelacionOrden']['id']);
                $formaPago['importe'] = $cancelacion['CancelacionOrden']['importe_proveedor'];
                array_push($formaPagoCancelacion, $formaPago);
            }
            $solicitud['MutualProductoSolicitudCancelacion'] = $cancelaciones;
        }
        if($solicitud['MutualProductoSolicitud']['importe_solicitado'] < $totalCancela){
            parent::notificar("EL IMPORTE SOLICITADO (".$solicitud['MutualProductoSolicitud']['importe_percibido'].") ES MENOR QUE EL TOTAL SELECCIONADO A CANCELAR (".number_format($totalCancela,2).")");
            $oSOLICITUD->rollback();
            return false;
        }
        #ARMO LA FORMA DE PAGO
        $solicitud['MutualProductoSolicitudInstruccionPago'] = array();
        $formaPago = array();
        $formaPago['id'] = 0;
        $formaPago['mutual_producto_solicitud_id'] = 0;
        $formaPago['a_la_orden_de'] = 'A MI ORDEN PERSONAL';
        $formaPago['concepto'] = 'LIQUIDACION PRESTAMO';
        $formaPago['importe'] = $solicitud['MutualProductoSolicitud']['importe_percibido'] - $totalCancela;
        array_push($solicitud['MutualProductoSolicitudInstruccionPago'], $formaPago);
        if(!empty($formaPagoCancelacion)){
                foreach ($formaPagoCancelacion as $formaCanc){
                        array_push($solicitud['MutualProductoSolicitudInstruccionPago'], $formaCanc);
                }
        }        
        
        
        #BUSCO EL IMPORTE VIGENTE DE LA CUOTA SOCIAL A LA FECHA SEGUN EL BENEFICIO
        App::import('Model','Pfyj.PersonaBeneficio');
        $oBeneficio = new PersonaBeneficio();
        $cuotaSoc = $oBeneficio->getImporteCuotaSocial($solicitud['MutualProductoSolicitud']['persona_beneficio_id']);
        $solicitud['MutualProductoSolicitud']['cuota_social_vigente'] = $cuotaSoc['decimal_1'];
        $solicitud['MutualProductoSolicitud']['actualizar_cuota_social'] = 0;
        
        #SUELDO NETO Y DEBITOS BANCARIOS
        $oBeneficio->setSueldoNetoDebitoBancario($solicitud['MutualProductoSolicitud']['persona_beneficio_id'], $solicitud['MutualProductoSolicitud']['sueldo_neto'], $solicitud['MutualProductoSolicitud']['debitos_bancarios']);
        
        

        #SI NO ES SOCIO, CALCULO LA CUOTA SOCIAL
        if($solicitud['MutualProductoSolicitud']['socio_id'] == 0){
            $cuotaSocialDiferenciada = 0;
            if($cuotaSocialDiferenciada != 0) $solicitud['MutualProductoSolicitud']['cuota_social'] = $cuotaSocialDiferenciada;
            else $solicitud['MutualProductoSolicitud']['cuota_social'] = $cuotaSoc['decimal_1'];
            $solicitud = $oSOLICITUD->__chequearSocio($solicitud);
        }else{
            #SI ES SOCIO, VERIFICO EL IMPORTE DE LA CUOTA SOCIAL QUE ESTA DESCONTANDO
            App::import('Model','Pfyj.Socio');
            $oSocio = new Socio();
            $oSocio->bindModel(array('hasOne' => array('OrdenDescuento')));
            $socio = $oSocio->read(null,$solicitud['MutualProductoSolicitud']['socio_id']);
            $solicitud['MutualProductoSolicitud']['cuota_social'] = $socio['OrdenDescuento']['importe_cuota'];
            if($solicitud['MutualProductoSolicitud']['cuota_social'] < $solicitud['MutualProductoSolicitud']['cuota_social_vigente']) $solicitud['MutualProductoSolicitud']['actualizar_cuota_social'] = 1;
        } 
        

     
        
        if(!isset($solicitud['MutualProductoSolicitud']['proveedor_id'])){
            parent::notificar("No se pudo vincular a un proveedor");
            $oSOLICITUD->rollback();
            return false;
        }
        if(!isset($solicitud['MutualProductoSolicitud']['persona_beneficio_id'])){
            parent::notificar("No se pudo vincular a un beneficio");
            $oSOLICITUD->rollback();
            return false;
        }
        if($solicitud['MutualProductoSolicitud']['actualizar_cuota_social'] == 1 && $solicitud['MutualProductoSolicitud']['socio_id'] != 0 ){
            if(!$oSOLICITUD->__actualizarCuotaSocial($solicitud)){
                parent::notificar("No se pudo actualizar la cuota social");
                $oSOLICITUD->rollback();
                return false;
            }
        }
//        debug($solicitud);
//        exit;
        
        if(!$oSOLICITUD->saveAll($solicitud)){
            parent::notificar("Error al grabar la Solicitud");
            $oSOLICITUD->rollback();
            return false;
        }else{
            
            $solicitud['MutualProductoSolicitud']['id'] = $oSOLICITUD->getLastInsertID();

            #PROCESO LOS ADJUNTOS
            if(!empty($datos['Archivos'])){
                App::import('model','mutual.MutualProductoSolicitudDocumento');
                $oDOCUMENTO = new MutualProductoSolicitudDocumento();
                $DIRDATAUPLOAD = WWW_ROOT . "files" . DS . "solicitudes" . DS . $solicitud['MutualProductoSolicitud']['id'];
                if (!is_dir($DIRDATAUPLOAD)){mkdir($DIRDATAUPLOAD);}
//                 if (is_dir($DIRDATAUPLOAD)){rmdir($DIRDATAUPLOAD);}
//                 mkdir($DIRDATAUPLOAD);
                
                foreach($datos['Archivos'] as $tipoDocumento => $archivo){
                    $documento['MutualProductoSolicitudDocumento'] = array();
                    $documento['MutualProductoSolicitudDocumento']['id'] = 0;
                    $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] = $solicitud['MutualProductoSolicitud']['id'];
                    $documento['MutualProductoSolicitudDocumento']['file_name'] = $archivo['file_name'];
                    $documento['MutualProductoSolicitudDocumento']['file_type'] = $archivo['file_type'];
                    // $documento['MutualProductoSolicitudDocumento']['file_data'] = $archivo['file_data'];
                    $documento['MutualProductoSolicitudDocumento']['codigo_documento'] = $tipoDocumento;


                    // GUARDAR LA IMAGEN EN EL FILESYSTEM
                    $fileName = $DIRDATAUPLOAD . DS . $documento['MutualProductoSolicitudDocumento']['file_name'];
                    if(file_exists($fileName)){unlink ($fileName);}
                    
                    $tmpName = WWW_ROOT . "files" . DS . "solicitudes" . DS . $datos['token'] . DS . $archivo['file_name'];
                    
                    shell_exec("mv $tmpName $fileName");
                    if(!file_exists($fileName)){
                        parent::notificar("Error al subir el archivo ". $archivo['file_name']);
                        $oSOLICITUD->rollback();
                        return false;                        
                    };


                    if(!$oDOCUMENTO->save($documento)){
                        parent::notificar("Error al guardar el archivo ". $archivo['file_name']);
                        $oSOLICITUD->rollback();
                        return false;
                    }
                }

                // borrar el directorio temporal
                $tmpName = WWW_ROOT . "files" . DS . "solicitudes" . DS . $datos['token'];
                
                if(file_exists($tmpName)){
                    
                    if(!rmdir($tmpName)){
                        
                        parent::notificar("Error al eliminar el directorio temporal ". $solicitud['token']);
                        $oSOLICITUD->rollback();
                        return false;
                        
                    }
                }

            }

            
            $oSOLICITUD->guardarHistorial($solicitud);
            
            $oSOLICITUD->commit();

            return $solicitud['MutualProductoSolicitud']['id'];
        }        
    }
    
    
    function search_personas($params = array()){
        
        App::import('model','pfyj.Persona');
        $oPERSONA = new Persona();        
        $sql = "select Persona.*,Socio.* from personas as Persona
                left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.persona_id = Persona.id)
                left join socios Socio on (Socio.persona_id = Persona.id)
                where 
                    Persona.tipo_documento LIKE '".$params['tipo_documento']."%'  
                    AND Persona.documento LIKE '".$params['nro_documento']."%'  
                    AND Persona.apellido LIKE  '".$params['apellido']."%'  
                    AND Persona.nombre LIKE '".$params['nombre']."%'  
                    ".(!empty($params['nro_socio']) ? " AND Socio.id = ".$params['nro_socio']." " : " " )."
                GROUP BY Persona.id         
                limit 3;";
        $oPERSONA->recursive = 3;
        $personas = $oPERSONA->query($sql);
        return $personas;
    }
    
    
    function get_remitos($fechaDesde = NULL,$fechaHasta = NULL){
        
        $fechaDesde = (!empty($fechaDesde) ? $fechaDesde : date('Y-m-d'));
        $fechaHasta = (!empty($fechaHasta) ? $fechaHasta : date('Y-m-d'));
        
        $vendedor_id = $this->oUSER->get_vendedorId_logon();
        
        $datos = NULL;
        
        
        $sql = "select VendedorRemito.* from vendedor_remitos VendedorRemito
                where ".(!empty($vendedor_id) ? "VendedorRemito.vendedor_id = $vendedor_id " : " 1 = 1 ")." and VendedorRemito.anulado = 0
                and VendedorRemito.created between '$fechaDesde' and '$fechaHasta'
                order by VendedorRemito.created desc LIMIT 50;";
        
        $datos = $this->query($sql);
        
//        if(!empty($datos)){
//            
//            App::import('model','mutual.MutualProductoSolicitud');
//            $oSOLICITUD = new MutualProductoSolicitud();
//            
//            foreach($datos as $ix => $remito){
//                
//                $sql = "select id from mutual_producto_solicitudes MutualProductoSolicitud 
//                        where vendedor_remito_id = " . $remito['VendedorRemito']['id'] . " AND vendedor_id = $vendedor_id";
//                
//                $nros = $oSOLICITUD->query($sql);
//                $remito['VendedorRemito']['solicitudes'] = array();
//                if(!empty($nros)){
//                    foreach($nros as $nro){
//                        $solicitud = $oSOLICITUD->getOrden($nro['MutualProductoSolicitud']['id']);
//                        $solicitud = $oSOLICITUD->armaDatos($solicitud, false, true); 
//                        array_push($datos[$ix]['VendedorRemito']['solicitudes'], $solicitud['MutualProductoSolicitud']);
//                    }
//                }
//            }
//        }        
        return $datos;
    }
    
    function getDetalleOrdenDeDescuento($orden_id) {
        App::import('model','Mutual.OrdenDescuentoService');
        $oORDENSERVICE = new OrdenDescuentoService();
        $orden = $oORDENSERVICE->getOrden($orden_id);
        App::import('model','mutual.OrdenDescuentoCuotaService');
        $oODCS = new OrdenDescuentoCuotaService();
        $cuotas = $oODCS->cuotasByOrdenDto($orden_id,true); 
        // $orden['OrdenDescuentoCuota'] =  Set::extract('{n}.OrdenDescuentoCuota', $cuotas);
        $orden['OrdenDescuentoCuota'] = $cuotas;
        return $orden;
        
    }
    
    
    function getDetalleCuota($cuota_id) {
        App::import('model','mutual.OrdenDescuentoCuota');
        $oODC = new OrdenDescuentoCuota();
        $oODC->recursive = 3;
        $oODC->OrdenDescuentoCobroCuota->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
        $oODC->LiquidacionCuota->bindModel(array('belongsTo' => array('Liquidacion')));
        $cuota = $oODC->getCuota($cuota_id);
        return $cuota;
    }
    
}