<?php
class SocioSolicitud extends PfyjAppModel{
	
	var $name = 'SocioSolicitud';
	
        function infoPDF($id = NULL) {
            
            if(empty($id)) {
                return null;
            }
            
            $sql = "select
                ss.id,ss.fecha,
                    -- datos del solicitante
                    concat('DNI ', Persona.documento, ' - ', Persona.apellido, ', ', Persona.nombre) beneficiario
            ,
                    concat(Persona.apellido, ', ', Persona.nombre) beneficiario_apenom
            ,
                    concat('DNI - ', Persona.documento) beneficiario_tdocndoc
            ,
                    'DNI' beneficiario_tdoc
            ,
                    Persona.documento beneficiario_ndoc
            ,
                    Persona.cuit_cuil beneficiario_cuit_cuil
            ,
                    Persona.nombre_conyuge beneficiario_conyuge
            ,
                    case
                            length(Persona.cuit_cuil)
                            when 11 then concat(left(Persona.cuit_cuil, 2), '-', substr(Persona.cuit_cuil, 3, 8), '-', right(Persona.cuit_cuil, 2))
                            else null
                    end beneficiario_cuit_cuil_pick
            ,
                    upper(concat(Persona.calle, ' ', if(Persona.numero_calle <> 0, Persona.numero_calle, ''), ' - ', Persona.localidad, ' (CP ', Persona.codigo_postal, ')' , if(Persona.provincia_id is not null, concat(' - ', Provincia.nombre), ''))) beneficiario_domicilio
            ,
                    concat('CEL.: ', Persona.telefono_movil, ' | TEL: ', Persona.telefono_fijo, ' | OTRO: ', Persona.telefono_referencia, ' (Ref.: ', ifnull(Persona.persona_referencia, ''), ') | EMAIL: ', ifnull(Persona.e_mail, '')) beneficiario_telefonos
            ,
                    concat('EST. CIVIL: ', ifnull(EstadoCivil.concepto_1, ''), ' | SEXO: ', ifnull(Persona.sexo, ''), ' | NACIMIENTO: ', date_format(Persona.fecha_nacimiento, '%d-%m-%Y'), ' | EDAD: ', TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, now()), ' | CUIT/L: ', Persona.cuit_cuil) beneficiario_complementarios
                    -- datos del beneficio
            ,
                    PersonaBeneficio.activo beneficio_activo

            ,
                    concat(case 
                    when left(right(PersonaBeneficio.codigo_beneficio, 4), 2) = 22 
                then 
                    (select concat(Organismo.concepto_1, ' - ', substr(ifnull(Empresa.concepto_1, ''), 1, 40), '|'
                    , if(PersonaBeneficio.codigo_empresa <> PersonaBeneficio.turno_pago, ifnull(PersonaBeneficio.turno_pago, ''), '')
                , '|', ifnull(PersonaBeneficio.codigo_reparticion, ''), '|CBU:', PersonaBeneficio.cbu))

                    when left(right(PersonaBeneficio.codigo_beneficio, 4), 2) <> 22 
                then 
                    (select concat(Organismo.concepto_1, ' - BENEFICIO: ', concat(PersonaBeneficio.tipo, PersonaBeneficio.nro_ley, PersonaBeneficio.nro_beneficio, PersonaBeneficio.sub_beneficio)))     
            end, if(PersonaBeneficio.activo = 0, ' ** NO VIGENTE **', '')) beneficio_str
            ,
                    ifnull(Banco.nombre, '') beneficio_banco
            ,
                    PersonaBeneficio.acuerdo_debito beneficio_acuerdo_debito
            ,
                    PersonaBeneficio.banco_id beneficio_banco_id
            ,
                    PersonaBeneficio.nro_sucursal beneficio_sucursal
            ,
                    PersonaBeneficio.nro_cta_bco beneficio_cuenta
            ,
                    PersonaBeneficio.cbu beneficio_cbu
            ,
                    PersonaBeneficio.fecha_ingreso beneficio_ingreso
            ,
                    TIMESTAMPDIFF(YEAR,
                    PersonaBeneficio.fecha_ingreso,
                    now()) beneficio_antiguedad
            ,
                    PersonaBeneficio.nro_legajo beneficio_legajo
            ,
                    PersonaBeneficio.tipo beneficio_tipo_beneficio
            ,
                    PersonaBeneficio.nro_beneficio beneficio_nro_beneficio
            ,
                    PersonaBeneficio.nro_ley beneficio_nro_ley
            ,
                    PersonaBeneficio.sub_beneficio beneficio_sub_beneficio
            ,
                    case
                            left(right(PersonaBeneficio.codigo_beneficio,
                            4),
                            2)
                            when 77 then concat(PersonaBeneficio.tipo, PersonaBeneficio.nro_ley, PersonaBeneficio.nro_beneficio, PersonaBeneficio.sub_beneficio)
                            else ''
                    end beneficio_cjpc_nro
            ,
                    PersonaBeneficio.codigo_reparticion beneficio_codigo_reparticion
            ,
                    PersonaBeneficio.tarjeta_titular beneficio_tarjeta_titular
            ,
                    PersonaBeneficio.tarjeta_numero beneficio_tarjeta_numero
            ,
                    PersonaBeneficio.codigo_beneficio organismo
            ,
                    Organismo.concepto_1 organismo_desc
            ,
                    if(PersonaBeneficio.codigo_empresa <> PersonaBeneficio.turno_pago,
                    PersonaBeneficio.turno_pago,
                    PersonaBeneficio.codigo_empresa) turno
            ,
                    concat(Empresa.concepto_1, ' - ', right(PersonaBeneficio.turno_pago, 5)) turno_desc
            from
                    socio_solicitudes ss
            inner join persona_beneficios PersonaBeneficio on
                    PersonaBeneficio.id = ss.persona_beneficio_id
            inner join global_datos Organismo on
                    Organismo.id = PersonaBeneficio.codigo_beneficio
            left join bancos Banco on
                    Banco.id = PersonaBeneficio.banco_id
            left join global_datos Empresa on
                    Empresa.id = PersonaBeneficio.codigo_empresa
            inner join personas Persona on
                    Persona.id = ss.persona_id
            left join provincias Provincia on
                    Provincia.id = Persona.provincia_id
            left join global_datos EstadoCivil on
                    EstadoCivil.id = Persona.estado_civil
            where
                    ss.id = $id;";
            
            $datos = $this->query($sql);
            if(!empty($datos)) {
                $originalArray = $datos[0];
                // Extraer los valores que necesitamos
                $beneficiarioFull = $originalArray[0]['beneficiario_apenom'];
                list($beneficiarioApellido, $beneficiarioNombre) = explode(', ', $beneficiarioFull);

                // Extraer datos adicionales
                $direccionCompleta = $originalArray[0]['beneficiario_domicilio'];
                list($calle, $numeroYLocalidad) = explode(' ', $direccionCompleta, 2);
                list($numeroCalle, $restoDireccion) = explode(' ', $numeroYLocalidad, 2);
                list($localidad, $provinciaYcp) = explode('(', $restoDireccion, 2);
                $provinciaYcp = rtrim($provinciaYcp, ')');
                list($cp, $provincia) = explode(' - ', $provinciaYcp);

                // Extraer teléfonos
                $telefonos = $originalArray[0]['beneficiario_telefonos'];
                preg_match('/TEL: ([0-9\s]+)/', $telefonos, $telefonoFijo);
                preg_match('/CEL.: ([0-9\s]+)/', $telefonos, $telefonoMovil);
                preg_match('/OTRO: ([0-9\s]+) \(Ref.: ([^)]+)\)/', $telefonos, $telefonoReferencia);

                App::import('Helper', 'Util');
                $oUT = new UtilHelper();                  
                
                // Crear el nuevo arreglo
                $newArray = [
                    'SocioSolicitud' => [
                        'id' => $originalArray['ss']['id'],
                        'fecha' => $originalArray['ss']['fecha'],
                        'beneficiario' => $originalArray[0]['beneficiario'],
                        'beneficiario_apenom' => $beneficiarioFull,
                        'beneficiario_apellido' => $beneficiarioApellido,
                        'beneficiario_nombre' => $beneficiarioNombre,
                        'beneficiario_tdocndoc' => str_replace(' ', '', $originalArray[0]['beneficiario_tdocndoc']),
                        'beneficiario_tdoc' => $originalArray[0]['beneficiario_tdoc'],
                        'beneficiario_ndoc' => $originalArray['Persona']['beneficiario_ndoc'],
                        'beneficiario_socio' => '#144 | ALTA: 19-06-2013 | CATEGORIA:  | CALIF: MOROSO (28-12-2020)', // Esta es una suposición. Ajusta según necesites.
                        'beneficiario_cuit_cuil' => $originalArray['Persona']['beneficiario_cuit_cuil'],
                        'beneficiario_conyuge' => $originalArray['Persona']['beneficiario_conyuge'],
                        'beneficiario_cuit_cuil_pick' => $originalArray[0]['beneficiario_cuit_cuil_pick'],
                        'beneficiario_domicilio' => $direccionCompleta,
                        'beneficiario_telefonos' => '', // Este campo parece estar vacío en el resultado esperado
                        'beneficiario_complementarios' => $originalArray[0]['beneficiario_complementarios'],
                        'beneficiario_medio_contacto' => $telefonos,
                        'beneficiario_estado_civil' => 'Casado/a', // Tomado de beneficiario_complementarios
                        'beneficiario_sexo' => 'M', // Tomado de beneficiario_complementarios
                        'beneficiario_fecha_nacimiento' => '29-10-1963', // Tomado de beneficiario_complementarios
                        'beneficiario_edad' => '60', // Tomado de beneficiario_complementarios
                        'beneficiario_calle' => $calle,
                        'beneficiario_numero_calle' => $numeroCalle,
                        'beneficiario_piso' => '',
                        'beneficiario_dpto' => '',
                        'beneficiario_barrio' => '',
                        'beneficiario_localidad' => $localidad,
                        'beneficiario_cp' => $cp,
                        'beneficiario_provincia' => $provincia,
                        'beneficiario_telefono_fijo' => isset($telefonoFijo[1]) ? $telefonoFijo[1] : '',
                        'beneficiario_telefono_movil' => isset($telefonoMovil[1]) ? $telefonoMovil[1] : '',
                        'beneficiario_telefono_referencia' => isset($telefonoReferencia[1]) ? $telefonoReferencia[1] : '',
                        'beneficiario_persona_referencia' => isset($telefonoReferencia[2]) ? $telefonoReferencia[2] : '',
                        'beneficiario_e_mail' => '', // Este campo parece estar vacío en el resultado esperado
                        'beneficio_activo' => $originalArray['PersonaBeneficio']['beneficio_activo'],
                        'beneficio_str' => $originalArray[0]['beneficio_str'],
                        'beneficio_banco' => $originalArray[0]['beneficio_banco'],
                        'beneficio_acuerdo_debito' => $originalArray['PersonaBeneficio']['beneficio_acuerdo_debito'],
                        'beneficio_banco_id' => $originalArray['PersonaBeneficio']['beneficio_banco_id'],
                        'beneficio_sucursal' => $originalArray['PersonaBeneficio']['beneficio_sucursal'],
                        'beneficio_cuenta' => $originalArray['PersonaBeneficio']['beneficio_cuenta'],
                        'beneficio_cbu' => $originalArray['PersonaBeneficio']['beneficio_cbu'],
                        'beneficio_ingreso' => $originalArray['PersonaBeneficio']['beneficio_ingreso'],
                        'beneficio_antiguedad' => $originalArray[0]['beneficio_antiguedad'],
                        'beneficio_legajo' => $originalArray['PersonaBeneficio']['beneficio_legajo'],
                        'beneficio_tipo_beneficio' => $originalArray['PersonaBeneficio']['beneficio_tipo_beneficio'],
                        'beneficio_nro_beneficio' => $originalArray['PersonaBeneficio']['beneficio_nro_beneficio'],
                        'beneficio_nro_ley' => $originalArray['PersonaBeneficio']['beneficio_nro_ley'],
                        'beneficio_sub_beneficio' => $originalArray['PersonaBeneficio']['beneficio_sub_beneficio'],
                        'beneficio_cjpc_nro' => $originalArray[0]['beneficio_cjpc_nro'],
                        'beneficio_codigo_reparticion' => $originalArray['PersonaBeneficio']['beneficio_codigo_reparticion'],
                        'beneficio_tarjeta_titular' => $originalArray['PersonaBeneficio']['beneficio_tarjeta_titular'],
                        'beneficio_tarjeta_numero' => $originalArray['PersonaBeneficio']['beneficio_tarjeta_numero'],
                        'beneficio_tarjeta_debito' => '',
                        'fecha_emision_str' => array(
                            'dia' => array('numero' => date('d',strtotime($originalArray['ss']['fecha'])),'string' => trim(parent::num2letras(date('d',strtotime($originalArray['ss']['fecha'])),false))),
                            'mes' => array('numero' => date('m',strtotime($originalArray['ss']['fecha'])),'string' => $oUT->mesToStr(date('m',strtotime($originalArray['ss']['fecha'])),true)),
                            'anio' => array('numero' => date('Y',strtotime($originalArray['ss']['fecha'])),'string' => trim(parent::num2letras(date('Y',strtotime($originalArray['ss']['fecha'])),false))),
                        )
                    ]
                ];
                return $newArray;
            };
        }
        
	
	function aprobar($sol,$vtos=null,$impoCuotaSocial=null,$liquiPrimeraCuota=false){
        
        if(empty($sol['SocioSolicitud']['periodo_ini'])){
    		App::import('Model', 'Proveedores.ProveedorVencimiento');
    		$oVTOS = new ProveedorVencimiento(null);
            $vtos = $oVTOS->calculaVencimiento(18,$sol['SocioSolicitud']['persona_beneficio_id'],$sol['SocioSolicitud']['fecha']);
    		$sol['SocioSolicitud']['periodo_ini'] = $vtos['inicia_en'];
    		$sol['SocioSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
    		$sol['SocioSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
        }
		
		App::import('Model', 'Pfyj.Socio');
		$oSocio = new Socio(null);	
		
		// GUARDO EL SOCIO
		$ret = $oSocio->guardar(array('Socio' => array(
					'fecha_alta' => $sol['SocioSolicitud']['fecha'],
					'socio_solicitud_id' => $sol['SocioSolicitud']['id'],
					'persona_id' => $sol['SocioSolicitud']['persona_id'],
					'persona_beneficio_id' => $sol['SocioSolicitud']['persona_beneficio_id'],
					'periodo_ini' => $sol['SocioSolicitud']['periodo_ini'],
					'activo' => 1,
				)));
		$socio_id = $oSocio->getLastInsertID();
		if(!$ret) return false;
				
		// MARCO COMO APROBADA LA SOLICITUD
		$sol['SocioSolicitud']['aprobada'] = 1;
		if(!parent::save($sol)) return false;
		
		// GENERO LA ORDEN DE DESCUENTO
		// BUSCAR EL IMPORTE DE LA CUOTA SOCIAL
	
    	App::import('Model', 'Mutual.OrdenDescuento');
    	$this->OrdenDescuento = new OrdenDescuento(null);
    	
    	$glb = parent::getGlobalDato('entero_1',"MUTUPROD0003");
    	//seteo a cero el importe de la cuota social porque el liquidador busca la que corresponde al momento de liquidar
    	//no se usa el valor de la cabecera
//    	$impoCuotaSocial = 0;
    	$ordenDto = array();
    	$ordenDto['OrdenDescuento'] = array(
									'fecha' => $sol['SocioSolicitud']['fecha'],
									'tipo_orden_dto' => parent::GlobalDato('concepto_3',"MUTUPROD0003"),
									'numero' => $socio_id,
									'tipo_producto' => 'MUTUPROD0003',
									'socio_id' => $socio_id,
									'persona_beneficio_id' => $sol['SocioSolicitud']['persona_beneficio_id'],
									'proveedor_id' => parent::GlobalDato('entero_1',"MUTUPROD0003"),
									'mutual_producto_id' => 0,
									'periodo_ini' => $sol['SocioSolicitud']['periodo_ini'],
									'primer_vto_socio' => $sol['SocioSolicitud']['primer_vto_socio'],
									'primer_vto_proveedor' => $sol['SocioSolicitud']['primer_vto_proveedor'],
									'importe_cuota' => (empty($impoCuotaSocial) ? $oSocio->getImpoCuotaSocial($socio_id) : $impoCuotaSocial),
									'cuotas' => 1,
									'permanente' => 1,
					);
    	
//		App::import('Model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();
//		$ordenDto['OrdenDescuentoCuota'] = $oCuota->armaCuotas($ordenDto);			
//		$ret = $this->OrdenDescuento->saveAll($ordenDto);		
		
		$ret = $this->OrdenDescuento->save($ordenDto);		
		
		
		$ret = $this->actualizarOrdenDto($this->id,$this->OrdenDescuento->getLastInsertID());	
		$ret = $oSocio->actualizarOrdenDto($socio_id,$this->OrdenDescuento->getLastInsertID());	

		
		if($liquiPrimeraCuota):
	    	App::import('Model', 'Mutual.LiquidacionSocio');
	    	$oLS = new LiquidacionSocio(null);
	    	$oLS->reliquidar($socio_id,$sol['SocioSolicitud']['periodo_ini']);
		endif;
//		exit;
		return $socio_id;
		
	}
	
	
    function actualizarOrdenDto($id,$idexp){
    	$data = $this->read(null,$id);
    	$data['SocioSolicitud']['orden_descuento_id'] = $idexp;
    	return parent::save($data);
    }	
	
	function replaceCampos($str){
		$this->recursive = 3;
		$this->bindModel(array('belongsTo' => array('Persona','PersonaBeneficio')));
		$this->Persona->bindModel(array('hasOne' => array('Socio')));
		$this->Persona->Socio->bindModel(array('hasOne' => array('OrdenDescuento')));
		$sol = $this->read(null,$this->id);
		$txt = "";
		
//		debug($sol);
//		exit;
	
		$txt = str_replace("#NRO_SOLICITUD#",$this->id,$str);
		$txt = str_replace("#FECHA_SOLICITUD#",date('d/m/Y',strtotime($sol['SocioSolicitud']['fecha'])),$txt);
		$txt = str_replace("#APELLIDO#",$sol['Persona']['apellido'],$txt);
		$txt = str_replace("#NOMBRE#",$sol['Persona']['nombre'],$txt);
		
		$glb = $this->getGlobalDato('concepto_1',$sol['Persona']['tipo_documento']);
		
		$txt = str_replace("#TDOCNDOC#",$glb['GlobalDato']['concepto_1'].' - ' .$sol['Persona']['documento'],$txt);
		
		$glb = $this->getGlobalDato('concepto_1',$sol['Persona']['estado_civil']);
		$txt = str_replace("#ESTADOCIVIL#",$glb['GlobalDato']['concepto_1'],$txt);
		
		$txt = str_replace("#FECHANAC#",date('d/m/Y',strtotime($sol['Persona']['fecha_nacimiento'])),$txt);
		
		$txt = str_replace("#CALLENRO#",$sol['Persona']['calle'].' '.$sol['Persona']['numero_calle'],$txt);
		
		$localidad = $this->getLocalidad($sol['Persona']['localidad_id']);
		
		$txt = str_replace("#LOCALIDAD#",$localidad['Localidad']['nombre'],$txt);
		$txt = str_replace("#BARRIO#",$sol['Persona']['barrio'],$txt);
		$txt = str_replace("#PCIA#",$localidad['Provincia']['nombre'],$txt);
		$txt = str_replace("#CP#",$localidad['Localidad']['cp'],$txt);
		
		$txt = str_replace("#TELFIJO#",$sol['Persona']['telefono_fijo'],$txt);
		$txt = str_replace("#TELMOVIL#",$sol['Persona']['telefono_movil'],$txt);
		
		$txt = str_replace("#PEREFE#",$sol['Persona']['telefono_referencia'] .''. $sol['Persona']['persona_referencia'],$txt);
		

		$glb = $this->getGlobalDato('concepto_1,concepto_2',$sol['PersonaBeneficio']['codigo_beneficio']);
		$txt = str_replace("#BENEFICIO#",$glb['GlobalDato']['concepto_1'],$txt);
		$txt = str_replace("#NROBENEF#",$sol['PersonaBeneficio']['nro_beneficio'],$txt);
		$txt = str_replace("#NROLEY#",$sol['PersonaBeneficio']['nro_ley'],$txt);

		if($glb['GlobalDato']['concepto_2'] == 'AC'){
			$txt = str_replace("#NROLEGAJO#",$sol['PersonaBeneficio']['nro_legajo'],$txt);
			$txt = str_replace("#CODREPA#",$sol['PersonaBeneficio']['codigo_reparticion'],$txt);
			$txt = str_replace("#FECINGRE#",date('d/m/Y',strtotime($sol['PersonaBeneficio']['fecha_ingreso'])),$txt);
			
			$bco = $this->getBanco($sol['PersonaBeneficio']['banco_id']);
			$txt = str_replace("#BANCO#",$bco['Banco']['nombre'],$txt);
			$txt = str_replace("#SUCURSAL#",$sol['PersonaBeneficio']['nro_sucursal'],$txt);
			$txt = str_replace("#NROCTA#",$sol['PersonaBeneficio']['nro_cta_bco'],$txt);
			$txt = str_replace("#CBU#",$sol['PersonaBeneficio']['cbu'],$txt);
		}else{
			$txt = str_replace("#NROLEGAJO#","",$txt);
			$txt = str_replace("#CODREPA#","",$txt);
			$txt = str_replace("#FECINGRE#","",$txt);
			
			$txt = str_replace("#BANCO#","",$txt);
			$txt = str_replace("#SUCURSAL#","",$txt);
			$txt = str_replace("#NROCTA#","",$txt);
			$txt = str_replace("#CBU#","",$txt);
			
		}
		
		if(isset($sol['Persona']['Socio']['id'])){
			$txt = str_replace("#DATOS_SOCIO#",$sol['Persona']['Socio']['id'],$txt);
			$txt = str_replace("#FECHA_APRO_SOCIO#",date('d/m/Y',strtotime($sol['Persona']['Socio']['created'])),$txt);
//			$txt = str_replace("#ORDEN_DESCUENTO#",$sol['Persona']['Socio']['OrdenDescuento']['id'],$txt);
			$txt = str_replace("#ORDEN_DESCUENTO#",$sol['Persona']['Socio']['orden_descuento_id'],$txt);
		}else{
			$txt = str_replace("#DATOS_SOCIO#","",$txt);
			$txt = str_replace("#FECHA_APRO_SOCIO#","",$txt);
			$txt = str_replace("#ORDEN_DESCUENTO#","",$txt);
		}
		$txt = str_replace("#MUTUAL#",Configure::read('APLICACION.nombre_fantasia'),$txt);
        $txt = str_replace("#MUTUAL_DOMICILIO#",Configure::read('APLICACION.domi_fiscal'),$txt);
        $txt = str_replace("#MUTUAL_TELEFONO#",Configure::read('APLICACION.telefonos'),$txt);
		return $txt;
	}
	
	
}
?>