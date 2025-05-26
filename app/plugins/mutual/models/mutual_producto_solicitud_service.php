<?php

define('SQL_SOLICITUD',"
select
                MutualProductoSolicitud.*
                ,EstadoSolicitud.concepto_1 estado_desc
                ,FormaPago.concepto_1 forma_pago_desc
                ,lpad(MutualProductoSolicitud.id,8,0) nro_print
                ,concat(MutualProductoSolicitud.tipo_orden_dto,' #',MutualProductoSolicitud.id) tipo_nro
                ,lpad(MutualProductoSolicitud.cuotas,2,0) cuotas_print
                ,0 barcode
                ,null MutualProductoSolicitudPago
                ,ifnull(OrdenDescuento.activo,0) estado_orden_dto
                ,ifnull(OrdenDescuento.permanente,0) permanente
                ,ifnull(OrdenDescuento.id,0) orden_descuento_id
                ,null OrdenDescuento
                ,null OrdenDescuentoSeguro
                ,concat(OrdenPago.tipo_documento,' - ',OrdenPago.sucursal,'-',lpad(OrdenPago.nro_orden_pago,8,0)) orden_pago_link
                ,0 bloqueo_liquidacion
                ,'' inicia_en
                ,ifnull((
    
                	select sum(co.importe_proveedor) from cancelacion_ordenes co
                    inner join mutual_producto_solicitud_cancelaciones mpsc on mpsc.cancelacion_orden_id = co.id
                    where mpsc.mutual_producto_solicitud_id = MutualProductoSolicitud.id group by MutualProductoSolicitud.id
    
                ),0) total_cancela
    
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
                ,FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE(Proveedor.id) proveedor_saldo_operativo
                -- datos del solicitante
                ,concat('DNI ',Persona.documento, ' - ',Persona.apellido,', ',Persona.nombre) beneficiario
                ,concat(Persona.apellido,', ',Persona.nombre) beneficiario_apenom
                ,concat('DNI - ',Persona.documento) beneficiario_tdocndoc
                ,'DNI' beneficiario_tdoc
                ,Persona.documento beneficiario_ndoc
                ,concat('#',Socio.id,'| ALTA: ',date_format(Socio.fecha_alta,'%d-%m-%Y')
                	,' | CATEGORIA: ',SocioCategoria.concepto_1,' | CALIF: ',ifnull(SocioCalificacion.concepto_1,'***'),' (',Socio.fecha_calificacion,')') beneficiario_socio
    
                ,Persona.cuit_cuil beneficiario_cuit_cuil
                ,Persona.nombre_conyuge beneficiario_conyuge
                ,case length(Persona.cuit_cuil) when 11 then concat(left(Persona.cuit_cuil,2),'-',substr(Persona.cuit_cuil,3,8),'-',right(Persona.cuit_cuil,2)) else null end beneficiario_cuit_cuil_pick
                ,upper(concat(Persona.calle,' ',if(Persona.numero_calle <> 0,Persona.numero_calle,''),' - ',Persona.localidad,' (CP ',Persona.codigo_postal,')'	,if(Persona.provincia_id is not null,concat(' - ',Provincia.nombre),''))) beneficiario_domicilio
                ,concat('CEL.: ',Persona.telefono_movil,' | TEL: ',Persona.telefono_fijo,' | OTRO: ',Persona.telefono_referencia,' (Ref.: ',ifnull(Persona.persona_referencia,''),') | EMAIL: ',ifnull(Persona.e_mail,'')) beneficiario_telefonos
                ,concat('EST. CIVIL: ',ifnull(EstadoCivil.concepto_1,''),' | SEXO: ',ifnull(Persona.sexo,''),' | NACIMIENTO: ',date_format(Persona.fecha_nacimiento,'%d-%m-%Y'),' | EDAD: ',TIMESTAMPDIFF(YEAR,Persona.fecha_nacimiento,now()),' | CUIT/L: ',Persona.cuit_cuil) beneficiario_complementarios
    
                -- datos del beneficio
                ,PersonaBeneficio.activo beneficio_activo
    
                ,concat(case 
                	when left(right(PersonaBeneficio.codigo_beneficio,4),2) = 22 
                    then 
                	(select concat(Organismo.concepto_1,' - ',substr(ifnull(Empresa.concepto_1,''),1,40),'|'
                	,if(PersonaBeneficio.codigo_empresa <> PersonaBeneficio.turno_pago, ifnull(PersonaBeneficio.turno_pago,''),'')
                    ,'|',ifnull(PersonaBeneficio.codigo_reparticion,''),'|CBU:',PersonaBeneficio.cbu))
                    
                	when left(right(PersonaBeneficio.codigo_beneficio,4),2) <> 22 
                    then 
                	(select concat(Organismo.concepto_1,' - BENEFICIO: ',concat(PersonaBeneficio.tipo,PersonaBeneficio.nro_ley,PersonaBeneficio.nro_beneficio,PersonaBeneficio.sub_beneficio)))   
                    
                end, if(PersonaBeneficio.activo = 0,' ** NO VIGENTE **','')) beneficio_str
    
    
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
    
                -- vendedor
                ,upper(concat(MutualProductoSolicitud.vendedor_id,' - ', 'DNI ' ,VendedorPersona.documento,' - ',VendedorPersona.apellido,', ',VendedorPersona.nombre)) vendedor_nombre
                ,upper(concat(MutualProductoSolicitud.vendedor_id,' - ',VendedorPersona.apellido,', ',VendedorPersona.nombre)) vendedor_nombre_min
                ,VendedorPersona.cuit_cuil vendedor_cuit
                ,upper(concat(VendedorPersona.apellido,', ',VendedorPersona.nombre)) vendedor_apenom
                ,concat('#',VendedorRemito.id, ' - ',VendedorRemito.user_created , ' (',VendedorRemito.created,')') vendedor_remito
                ,VendedorRemito.id vendedor_remito_nro
                ,upper(VendedorRemito.user_created) vendedor_remito_user_created
                ,VendedorRemito.created vendedor_remito_created
                -- plan
                ,concat('#',ProveedorPlan.id,' ',ProveedorPlanProveedor.razon_social_resumida, ' - ', ProveedorPlan.descripcion) proveedor_plan
                ,ProveedorPlan.descripcion proveedor_plan_descripcion
                ,ProveedorPlan.activo proveedor_plan_activo
                ,ProveedorPlan.tipo_producto proveedor_plan_producto
                ,concat('#',ProveedorPlan.id,' | ',ProveedorPlanProveedor.razon_social_resumida,' - ',ProveedorPlanProducto.concepto_1, ' ** ', ProveedorPlan.descripcion, ' **') proveedor_plan_string
                ,ProveedorPlan.interes_moratorio proveedor_interes_moratorio
                ,ProveedorPlan.costo_cancelacion_anticipada proveedor_costo_cancela
                ,ProveedorPlan.modelo_solicitud proveedor_plan_modelo_solicitud
                ,ProveedorPlanProducto.concepto_1 proveedor_plan_producto_descripcion
                ,ifnull(ProveedorPlanTemplate.concepto_2,'imprimir_credito_mutual_pdf') proveedor_plan_modelo_solicitud_2
    
                ,(select group_concat(concat(GlobalDato.id,'-',GlobalDato.concepto_2) order by GlobalDato.id separator '|') anexo
                                    from proveedor_plan_anexos ProveedorPlanAnexo
                                    inner join global_datos as GlobalDato on (GlobalDato.id = ProveedorPlanAnexo.codigo_anexo)
                                    where ProveedorPlanAnexo.proveedor_plan_id = ProveedorPlan.id
                ) proveedor_plan_anexos
    
                ,(
                select group_concat(distinct concat(Proveedor2.id,'-',Proveedor2.razon_social) order by Proveedor2.razon_social separator '|') reasignacion from global_datos Reasignacion
                inner join proveedores Proveedor2 on Proveedor2.cuit = ltrim(rtrim(Reasignacion.concepto_2))
                where Reasignacion.id like 'PROVREAS%'
                and ltrim(rtrim(Reasignacion.concepto_3)) = Proveedor.cuit
                order by Proveedor2.razon_social
                ) proveedor_reasignable_a
    
                ,ifnull(ProveedorReasigna.razon_social,'') as proveedor_reasignada_a

                from mutual_producto_solicitudes MutualProductoSolicitud
                --
                inner join global_datos EstadoSolicitud on EstadoSolicitud.id = MutualProductoSolicitud.estado
                left join global_datos FormaPago on FormaPago.id = MutualProductoSolicitud.forma_pago
                inner join global_datos Producto on Producto.id = MutualProductoSolicitud.tipo_producto
                --
                inner join proveedores Proveedor on Proveedor.id = MutualProductoSolicitud.proveedor_id
                --
                inner join personas Persona on Persona.id = MutualProductoSolicitud.persona_id
                left join provincias Provincia on Provincia.id = Persona.provincia_id
                left join global_datos EstadoCivil on EstadoCivil.id = Persona.estado_civil
                left join socios Socio on Socio.id = MutualProductoSolicitud.socio_id
                left join global_datos SocioCategoria on SocioCategoria.id = Socio.categoria
                left join global_datos SocioCalificacion on SocioCalificacion.id = Socio.calificacion
                --
                inner join persona_beneficios PersonaBeneficio on PersonaBeneficio.id = MutualProductoSolicitud.persona_beneficio_id
                inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                left join bancos Banco on Banco.id = PersonaBeneficio.banco_id
                left join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa
                --
                left join orden_descuentos OrdenDescuento 
                    on OrdenDescuento.mutual_producto_solicitud_id = MutualProductoSolicitud.id 
                    and ifnull(OrdenDescuento.nueva_orden_descuento_id,0) = 0 
                    and (OrdenDescuento.proveedor_id = MutualProductoSolicitud.proveedor_id
                    or OrdenDescuento.proveedor_id = MutualProductoSolicitud.reasignar_proveedor_id)
                    and OrdenDescuento.tipo_producto = MutualProductoSolicitud.tipo_producto
                --
                left join vendedores Vendedor on Vendedor.id = MutualProductoSolicitud.vendedor_id
                left join personas VendedorPersona on VendedorPersona.id = Vendedor.persona_id
                left join vendedor_remitos VendedorRemito on VendedorRemito.id = MutualProductoSolicitud.vendedor_remito_id
                --
                left join proveedor_planes ProveedorPlan on ProveedorPlan.id = MutualProductoSolicitud.proveedor_plan_id
                left join proveedores ProveedorPlanProveedor on ProveedorPlanProveedor.id = ProveedorPlan.proveedor_id
                left join global_datos ProveedorPlanProducto on ProveedorPlanProducto.id = ProveedorPlan.tipo_producto
                left join global_datos ProveedorPlanTemplate on ProveedorPlanTemplate.id = ProveedorPlan.modelo_solicitud_codigo
                --
                left join orden_pagos OrdenPago on OrdenPago.id = MutualProductoSolicitud.orden_pago_id
                --
                left join proveedores ProveedorReasigna on ProveedorReasigna.id = MutualProductoSolicitud.reasignar_proveedor_id
    
");

class MutualProductoSolicitudService extends MutualAppModel
{
    
    public $useTable = false;
    public $name = "MutualProductoSolicitudService";
    
    public function getSolicitud($id,$cargarOrdenes=true) {
        
        $sql = SQL_SOLICITUD . "where MutualProductoSolicitud.id = $id;";
        $result = $this->query($sql);
        if(empty($result)) {return null;}
        
        $solicitud = array();
        
        $mutualProductoSolicitud = Set::extract("{n}.MutualProductoSolicitud",$result);
        $camposCalculados = Set::extract("{n}.0",$result);
        $estadoSolicitud = Set::extract("{n}.EstadoSolicitud",$result);
        $formaPago = Set::extract("{n}.FormaPago",$result);
        $proveedor = Set::extract("{n}.Proveedor",$result);
        $producto = Set::extract("{n}.Producto",$result);
        $persona = Set::extract("{n}.Persona",$result);
        $beneficio = Set::extract("{n}.PersonaBeneficio",$result);
        $organismo = Set::extract("{n}.Organismo",$result);
        $vendedorPersona = Set::extract("{n}.VendedorPersona",$result);
        $vendedorRemito = Set::extract("{n}.VendedorRemito",$result);
        $proveedorPlan = Set::extract("{n}.ProveedorPlan",$result);
        $proveedorPlanProducto = Set::extract("{n}.ProveedorPlanProducto",$result);
        
        foreach ($mutualProductoSolicitud[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($proveedor[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($estadoSolicitud[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($formaPago[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($camposCalculados[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($producto[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($persona[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($beneficio[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($organismo[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($vendedorPersona[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($vendedorRemito[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($proveedorPlan[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        foreach ($proveedorPlanProducto[0] as $key => $value) {
            $solicitud['MutualProductoSolicitud'][$key] = $value;
        }
        
        return $this->_fillerInfoAdicional($solicitud);
    }
    
    
    public function getSolicitudes($conditions) {
        $sql = SQL_SOLICITUD . $conditions;
        $result = $this->query($sql);
        if(empty($result)) {return null;}
        $solicitudes = $this->_procesarLLaves($result);
        return $solicitudes;        
    }
    

    public function getByPersona($personaId,$anuladas=false,$limit=null,$page=0){
        $sql = SQL_SOLICITUD . " 
                where MutualProductoSolicitud.persona_id = $personaId 
                AND MutualProductoSolicitud.anulada = ".($anuladas ? 1 : 0)."
                ORDER BY MutualProductoSolicitud.id DESC 
                ".(!empty($limit) ? "LIMIT $limit OFFSET $page" : "").";";
        $result = $this->query($sql);
        if(empty($result)) {return null;}
        $solicitudes = $this->_procesarLLaves($result);
        return $solicitudes;
    }
    
    private function _procesarLLaves($result){
        if(empty($result)) {return null;}
        $solicitud = $solicitudes = array();
        foreach ($result as $dataSet){
//             debug($dataSet);
            foreach ($dataSet['MutualProductoSolicitud'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet[0] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['EstadoSolicitud'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['FormaPago'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['Proveedor'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['Producto'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['Persona'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['PersonaBeneficio'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['Organismo'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['VendedorPersona'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['VendedorRemito'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['ProveedorPlan'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            foreach ($dataSet['ProveedorPlanProducto'] as $key => $value) {
                $solicitud['MutualProductoSolicitud'][$key] = $value;
            }
            //$solicitud = $this->_fillerInfoAdicional($solicitud);
            array_push($solicitudes,$solicitud);
        }
        return $solicitudes;
    }
    
    private function _fillerInfoAdicional($solicitud){
        App::import('Helper', 'Util');
        $oUT = new UtilHelper();
        $solicitud['MutualProductoSolicitud']['fecha_emision_str'] = array(
            
            'dia' => array('numero' => date('d',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),'string' => trim(parent::num2letras(date('d',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),false))),
            'mes' => array('numero' => date('m',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),'string' => $oUT->mesToStr(date('m',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),true)),
            'anio' => array('numero' => date('Y',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),'string' => trim(parent::num2letras(date('Y',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),false))),
            
        );
        $mkTFC = mktime(0,0,0,date('m',strtotime($solicitud['MutualProductoSolicitud']['primer_vto_socio'])),date('d',strtotime($solicitud['MutualProductoSolicitud']['primer_vto_socio'])),date('Y',strtotime($solicitud['MutualProductoSolicitud']['primer_vto_socio'])));
        $mktVto = parent::addMonthToDate($mkTFC,$solicitud['MutualProductoSolicitud']['cuotas']);
        $vencimientoPagare = date('Y-m-d',$mktVto);
        $solicitud['MutualProductoSolicitud']['vencimiento_pagare'] = $vencimientoPagare;
        $solicitud['MutualProductoSolicitud']['vencimiento_pagare_str'] = array(
            
            'dia' => array('numero' => date('d',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),'string' => trim(parent::num2letras(date('d',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),false))),
            'mes' => array('numero' => date('m',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),'string' => $oUT->mesToStr(date('m',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),true)),
            'anio' => array('numero' => date('Y',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),'string' => trim(parent::num2letras(date('Y',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),false))),
            
        );
        $solicitud = $this->setBarcodeSolicitud($solicitud);
        
        
        App::import('Model','Mutual.MutualProductoSolicitudPago');
        $oMPSP = new MutualProductoSolicitudPago();
        $pagos = $oMPSP->getPagosBySolicitud($solicitud['MutualProductoSolicitud']['id']);
        $pagos = Set::extract('{n}.MutualProductoSolicitudPago',$pagos);
        $solicitud['MutualProductoSolicitud']['MutualProductoSolicitudPago'] = $pagos;
        
        $solicitud['MutualProductoSolicitud']['total_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_total']);
        $solicitud['MutualProductoSolicitud']['total_importe_solicitado_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_solicitado']);
        $solicitud['MutualProductoSolicitud']['total_importe_percibido_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_percibido']);
        $solicitud['MutualProductoSolicitud']['total_cuota_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_cuota']);
        $solicitud['MutualProductoSolicitud']['cantidad_cuota_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['cuotas'],false);
        
        App::import('Model','Mutual.OrdenDescuento');
        $oOdto = new OrdenDescuento();
        
        
        #BUSCO LA ORDEN DE DESCUENTO ASOCIADA
        if(!empty($solicitud['MutualProductoSolicitud']['orden_descuento_id'])){
            $solicitud['MutualProductoSolicitud']['OrdenDescuento'] = $oOdto->getOrdenFullInfo($solicitud['MutualProductoSolicitud']['orden_descuento_id']);
        }
        if(!empty($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'])){
            $solicitud['MutualProductoSolicitud']['OrdenDescuentoSeguro'] =$oOdto->getOrdenFullInfo($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id']);
        }
        
        // VENCIMIENTOS
        if($solicitud['MutualProductoSolicitud']['aprobada'] == 0){
            App::import('Model', 'Proveedores.ProveedorVencimiento');
            $oVTOS = new ProveedorVencimiento(null);
            $fechaPago = date('Y-m-d');
            $vtos = $oVTOS->calculaVencimiento($solicitud['MutualProductoSolicitud']['proveedor_id'],$solicitud['MutualProductoSolicitud']['persona_beneficio_id'],$fechaPago);
            $solicitud['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
            $solicitud['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
            $solicitud['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
            App::import('Model','mutual.Liquidacion');
            $oLQ = new Liquidacion();
            $solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] = $oLQ->isCerrada($solicitud['MutualProductoSolicitud']['organismo'], $solicitud['MutualProductoSolicitud']['periodo_ini']);
        }else{
            App::import('Model','mutual.Liquidacion');
            $oLQ = new Liquidacion();
            $solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] = $oLQ->isCerrada($solicitud['MutualProductoSolicitud']['organismo'], $solicitud['MutualProductoSolicitud']['periodo_ini']);
        }
        
        $solicitud['MutualProductoSolicitud']['inicia_en'] = parent::periodo($solicitud['MutualProductoSolicitud']['periodo_ini']);
        
        //ANEXOS
        if(!empty($solicitud['MutualProductoSolicitud']['proveedor_plan_anexos'])){
            $anexos = explode("|", $solicitud['MutualProductoSolicitud']['proveedor_plan_anexos']);
            $solicitud['MutualProductoSolicitud']['proveedor_plan_anexos'] = array();
            if(!empty($anexos)){
                foreach ($anexos as $value) {
                    list($id,$concepto) = explode("-", $value);
                    $solicitud['MutualProductoSolicitud']['proveedor_plan_anexos'][$id]=$concepto;
                }
            }
        }
        //REASIGNABLE
        if(!empty($solicitud['MutualProductoSolicitud']['proveedor_reasignable_a'])){
            $reasignables = explode("|", $solicitud['MutualProductoSolicitud']['proveedor_reasignable_a']);
            $solicitud['MutualProductoSolicitud']['proveedor_reasignable_a'] = array();
            if(!empty($reasignables)){
                foreach ($reasignables as $value) {
                    list($id,$concepto) = explode("-", $value);
                    $solicitud['MutualProductoSolicitud']['proveedor_reasignable_a'][$id]=$concepto;
                }
            }
            
        }
        
        
        if(!empty($solicitud['MutualProductoSolicitud']['mutual_producto_id'])){
            App::import('model','mutual.MutualProducto');
            $oMPROD = new MutualProducto();
            $solicitud['MutualProductoSolicitud']['proveedor_plan_modelo_solicitud'] = $oMPROD->get_modelo_print($solicitud['MutualProductoSolicitud']['mutual_producto_id']);
            $solicitud['MutualProductoSolicitud']['proveedor_plan_anexos'] = $oMPROD->get_anexos_print($solicitud['MutualProductoSolicitud']['mutual_producto_id']);
        }
        
        
        $solicitud['MutualProductoSolicitud']['fdoas'] = 0;
        $solicitud['MutualProductoSolicitud']['fdoas_total'] = null;
        $solicitud['MutualProductoSolicitud']['fdoas_total_cuota'] = null;
        $solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad'] = null;
        $solicitud['MutualProductoSolicitud']['fdoas_total_letras'] = null;
        $solicitud['MutualProductoSolicitud']['fdoas_total_cuota_letras'] = null;
        $solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad_letras'] = null;
        $TIPO_PRODUCTO_SIGEM = parent::GlobalDato('concepto_4',$solicitud['MutualProductoSolicitud']['tipo_producto']);
        $importeSeguro = 0;
        if(!empty($TIPO_PRODUCTO_SIGEM)) $importeSeguro = parent::GlobalDato('decimal_1',$TIPO_PRODUCTO_SIGEM);
        
        if(!empty($importeSeguro)){
            $solicitud['MutualProductoSolicitud']['fdoas'] = 1;
            $importeSeguroTotal = $importeSeguro * $solicitud['MutualProductoSolicitud']['cuotas'];
            
            $solicitud['MutualProductoSolicitud']['fdoas_total'] = $importeSeguroTotal;
            $solicitud['MutualProductoSolicitud']['fdoas_total_cuota'] = $importeSeguro;
            $solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad'] = $solicitud['MutualProductoSolicitud']['cuotas'];
            
            $solicitud['MutualProductoSolicitud']['fdoas_total_letras'] = parent::num2letras($importeSeguroTotal);
            $solicitud['MutualProductoSolicitud']['fdoas_total_cuota_letras'] = parent::num2letras($importeSeguro);
            $solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['cuotas'],false);
            
        }
        
        //CARGO EL HISTORIAL
        App::import('Model','Mutual.MutualProductoSolicitudEstado');
        $oMPSE = new MutualProductoSolicitudEstado();
        $estados = $oMPSE->getEstadosBySolicitud($solicitud['MutualProductoSolicitud']['id']);
        $estados = Set::extract('{n}.MutualProductoSolicitudEstado',$estados);
        $solicitud['MutualProductoSolicitudEstado'] = $estados;
        
        
        #######################################################################################
        # ARMAR EL DETALLE DE LOS VENCIMIENTOS
        #######################################################################################
        $planVencimientos = array();
        $mkIni = mktime(0,0,0,substr($solicitud['MutualProductoSolicitud']['periodo_ini'],4,2),1,substr($solicitud['MutualProductoSolicitud']['periodo_ini'],0,4));
        $periodoInicio = $solicitud['MutualProductoSolicitud']['periodo_ini'];
        for( $i=1 ; $i <= $solicitud['MutualProductoSolicitud']['cuotas']; $i++ ){
            $planVencimientos[$i] = array(
                'periodo' => $periodoInicio,
                'importe_cuota' => $solicitud['MutualProductoSolicitud']['importe_cuota'],
                'importe_foas' => $solicitud['MutualProductoSolicitud']['fdoas_total_cuota']
            );
            $solicitud['MutualProductoSolicitud']['finaliza_en'] = parent::periodo($periodoInicio);
            $periodoInicio = date('Ym',$this->addMonthToDate($mkIni,$i));
        }

        #######################################################################################
        # CARGAR LOS DOCUMENTOS
        #######################################################################################        
        $sql = "select MutualProductoSolicitudDocumento.*,GlobalDato.concepto_1 from mutual_producto_solicitud_documentos MutualProductoSolicitudDocumento
                        left join global_datos as GlobalDato on GlobalDato.id = MutualProductoSolicitudDocumento.codigo_documento
                        where mutual_producto_solicitud_id = " . $solicitud['MutualProductoSolicitud']['id'];
        $documentos = $this->query($sql);
        if(!empty($documentos)){
            $solicitud['MutualProductoSolicitudDocumento'] = $documentos;
        }
        
        #######################################################################################
        # CARGAR INSTRUCCION DE PAGO
        #######################################################################################
        $sql = "select * from mutual_producto_solicitud_instruccion_pagos MutualProductoSolicitudInstruccionPago
                where mutual_producto_solicitud_id = " . $solicitud['MutualProductoSolicitud']['id'];
        $instrucciones = $this->query($sql);
        if(!empty($instrucciones)){
            $solicitud['MutualProductoSolicitudInstruccionPago'] = Set::extract("{n}.MutualProductoSolicitudInstruccionPago",$instrucciones);
        }
        
        $solicitud['MutualProductoSolicitud']['cronograma_de_vencimientos'] = $planVencimientos;
        return $solicitud;
    }
    
}

