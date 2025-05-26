<?php
class ClienteListadosController extends ClientesAppController{
/*    
 * INSERT INTO `sigem_db`.`permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`, `parent`) VALUES ('720', 'Listados', '/clientes/cliente_listados/iva_venta', '720', '1', 'arrow_right2.gif' , '700'); 
 * INSERT INTO `sigem_db`.`grupos_permisos` (`grupo_id`, permiso_id) VALUES ('1', '720') 
 * ALTER TABLE `sigem_db`.`cliente_facturas` ADD COLUMN `Afip_Concepto` INT(2) NULL AFTER `anulado`, ADD COLUMN `Afip_DocTipo` INT(2) NULL AFTER `Afip_Concepto`, ADD COLUMN `Afip_MonId` VARCHAR(3) NULL AFTER `Afip_DocTipo`, ADD COLUMN `Afip_MonCotiz` INT(2) NULL AFTER `Afip_MonId`, ADD COLUMN `Afip_Resultado` VARCHAR(1) NULL AFTER `Afip_MonCotiz`, ADD COLUMN `Afip_CodAutorizacion` VARCHAR(20) NULL AFTER `Afip_Resultado`, ADD COLUMN `Afip_EmisionTipo` VARCHAR(6) NULL AFTER `Afip_CodAutorizacion`, ADD COLUMN `Afip_FchVto` DATE NULL AFTER `Afip_EmisionTipo`, ADD COLUMN `Afip_FchProceso` DATETIME NULL AFTER `Afip_FchVto`, ADD COLUMN `Afip_PtoVta` INT(4) NULL AFTER `Afip_FchProceso`, ADD COLUMN `Afip_CbteTipo` INT(2) NULL AFTER `Afip_PtoVta`, ADD COLUMN `Afip_NroCbte` INT(10) NULL AFTER `Afip_CbteTipo`; 
 * ALTER TABLE `sigem_db`.`cliente_facturas` ADD COLUMN `Afip_CbteFch` DATE NULL AFTER `Afip_NroCbte`, ADD COLUMN `Afip_FchServDesde` DATE NULL AFTER `Afip_CbteFch`, ADD COLUMN `Afip_FchServHasta` DATE NULL AFTER `Afip_FchServDesde`, ADD COLUMN `Afip_FchVtoPago` DATE NULL AFTER `Afip_FchServHasta`; 
 
 *
 */

    var $name = 'ClienteListados';
    var $uses = array('Clientes.ClienteListado', 'Clientes.Cliente');

    var $autorizar = array('iva_venta_PDF', 'iva_venta_xls', 'saldo_a_fecha', 'cta_cte_fecha', 'saldo_a_fecha_xls', 'saldo_a_fecha_detalle_xls',
                            'saldo_a_fecha_pdf', 'saldo_a_fecha_detalle_pdf', 'cta_cte_fecha_xls', 'cta_cte_fecha_pdf', 'listado_tipo_asiento',
                            'listado_tipo_asiento_pdf', 'listado_tipo_asiento_xls', 'listado_tipo_asiento_detalle', 'listado_tipo_asiento_detalle_pdf',
                            'listado_tipo_asiento_detalle_xls', 'factura_afip', 'ws_factura_afip', 'imprimir_factura_afip'
                            );

    function beforeFilter(){  
        $this->Seguridad->allow($this->autorizar);
        parent::beforeFilter();  
    }


    function index(){
        $this->redirect('iva_venta');	
    }


    function iva_venta(){

        $facturas = array();
        $showTabla = 0;
        $disable_form = 0;
        $fecha_desde = date("Y") . '-01-01';
        $fecha_hasta = date("Ymd");
        $tipo = 0;

        if(!empty($this->data)):

            $showTabla = 1;
            $disable_form = 1;
            $fecha_desde = $this->ClienteListado->armaFecha($this->data['ListadoIvaVenta']['fecha_desde']);
            $fecha_hasta = $this->ClienteListado->armaFecha($this->data['ListadoIvaVenta']['fecha_hasta']);

            $facturas = $this->ClienteListado->facturas_periodo($fecha_desde, $fecha_hasta);
        endif;

        $this->set('facturas', $facturas);
        $this->set('fecha_desde', $fecha_desde);
        $this->set('fecha_hasta', $fecha_hasta);
        $this->set('showTabla', $showTabla);
        $this->set('disable_form', $disable_form);
    }


    function iva_venta_pdf($fecha_desde, $fecha_hasta){

        $facturas = $this->ClienteListado->facturas_periodo($fecha_desde, $fecha_hasta);

        $this->set('facturas', $facturas);
        $this->set('fecha_desde', $fecha_desde);
        $this->set('fecha_hasta', $fecha_hasta);

        $this->render('iva_venta_pdf', 'pdf');
    }


    function iva_venta_xls($fecha_desde, $fecha_hasta){

        $facturas = $this->ClienteListado->facturas_periodo($fecha_desde, $fecha_hasta);

        $this->set('facturas', $facturas);
        $this->set('fecha_desde', $fecha_desde);
        $this->set('fecha_hasta', $fecha_hasta);

        $this->render('iva_venta_xls', 'blank');
    }


    function saldo_a_fecha(){

        $saldos = array();
        $showTabla = 0;
        $disable_form = 0;
        $fecha_desde = date("Y") . '-01-01';
        $fecha_hasta = date("Ymd");

        if(!empty($this->data)):

            $showTabla = 1;
            $disable_form = 1;
            $fecha_desde = $this->ClienteListado->armaFecha($this->data['SaldoFecha']['fecha_desde']);
            $fecha_hasta = $this->ClienteListado->armaFecha($this->data['SaldoFecha']['fecha_hasta']);

            $saldos = $this->ClienteListado->saldo_a_fecha($fecha_desde, $fecha_hasta);

        endif;

        $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($fecha_desde), -1));

        $this->set('saldos', $saldos);
        $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
        $this->set('fecha_desde', $fecha_desde);
        $this->set('fecha_hasta', $fecha_hasta);
        $this->set('showTabla', $showTabla);
        $this->set('disable_form', $disable_form);
    }


    function saldo_a_fecha_pdf($desdeFecha, $hastaFecha){

            $saldos = array();


            $saldos = $this->ClienteListado->saldo_a_fecha($desdeFecha, $hastaFecha);

            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($desdeFecha), -1));

            $this->set('saldos', $saldos);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('saldo_a_fecha_pdf', 'pdf');
    }


    function saldo_a_fecha_xls($desdeFecha, $hastaFecha){

            $saldos = array();


            $saldos = $this->ClienteListado->saldo_a_fecha($desdeFecha, $hastaFecha);

            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($desdeFecha), -1));

            $this->set('saldos', $saldos);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('saldo_a_fecha_xls', 'blank');
            return true;
    }


    function saldo_a_fecha_detalle_pdf($desdeFecha, $hastaFecha){

            $saldos = array();


            $saldos = $this->ClienteListado->saldo_a_fecha($desdeFecha, $hastaFecha);

            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($desdeFecha), -1));

            $this->set('saldos', $saldos);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('saldo_a_fecha_detalle_pdf', 'pdf');
            return true;
    }


    function saldo_a_fecha_detalle_xls($desdeFecha, $hastaFecha){

            $saldos = array();


            $saldos = $this->ClienteListado->saldo_a_fecha($desdeFecha, $hastaFecha);

            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($desdeFecha), -1));

            $this->set('saldos', $saldos);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('saldo_a_fecha_detalle_xls', 'blank');
            return true;
    }


    function cta_cte_fecha($cliente_id, $desdeFecha, $hastaFecha, $saldoAnterior = 0){
            $ctaCte = $this->ClienteListado->ctaCteFecha($cliente_id, $desdeFecha, $hastaFecha);

            $cliente = $this->Cliente->getCliente($cliente_id);
            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($desdeFecha), -1));

            $this->set('ctaCte', $ctaCte);
            $this->set('cliente', $cliente);
            $this->set('saldo_anterior', $saldoAnterior);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);		
            $this->set('fecha_desde', $desdeFecha);
            $this->set('fecha_hasta', $hastaFecha);

    }


    function cta_cte_fecha_pdf($cliente_id, $desdeFecha, $hastaFecha){

            $saldos = array();


            $saldos = $this->ClienteListado->saldo_a_fecha($desdeFecha, $hastaFecha, $cliente_id);

            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($desdeFecha), -1));

            $this->set('saldos', $saldos);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('saldo_a_fecha_detalle_pdf', 'pdf');
            return true;
    }


    function cta_cte_fecha_xls($cliente_id, $desdeFecha, $hastaFecha){

            $saldos = array();


            $saldos = $this->ClienteListado->saldo_a_fecha($desdeFecha, $hastaFecha, $cliente_id);

            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($desdeFecha), -1));

            $this->set('saldos', $saldos);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('saldo_a_fecha_detalle_xls', 'blank');
            return true;
    }


    function listado_tipo_asiento(){

            $aTipoAsiento = array();
            $showTabla = 0;
            $disable_form = 0;
            $fecha_desde = date("Y") . '-01-01';
            $fecha_hasta = date("Ymd");
            $tipo = 0;

            if(!empty($this->data)):
                    $showTabla = 1;
                    $disable_form = 1;
                    $fecha_desde = $this->ClienteListado->armaFecha($this->data['ListadoTipoAsiento']['fecha_desde']);
                    $fecha_hasta = $this->ClienteListado->armaFecha($this->data['ListadoTipoAsiento']['fecha_hasta']);
                    $aTipoAsiento = $this->ClienteListado->factura_tipo_asiento($fecha_desde, $fecha_hasta);
            endif;

            $fecha_saldo_anterior = date("Y-m-d", $this->ClienteListado->addDayToDate(strtotime($fecha_desde), -1));

            $this->set('aTipoAsiento', $aTipoAsiento);
            $this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
            $this->set('fecha_desde', $fecha_desde);
            $this->set('fecha_hasta', $fecha_hasta);
            $this->set('tipo', $tipo);
            $this->set('showTabla', $showTabla);
            $this->set('disable_form', $disable_form);
    }


    function listado_tipo_asiento_pdf($desdeFecha, $hastaFecha){
            $aTipoAsiento = $this->ClienteListado->factura_tipo_asiento($desdeFecha, $hastaFecha);

            $this->set('aTipoAsiento', $aTipoAsiento);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);
//            $this->set('tipo', $tipo);

            $this->render('listado_tipo_asiento_pdf', 'pdf');
            return true;
    }


    function listado_tipo_asiento_xls($desdeFecha, $hastaFecha){
            $aTipoAsiento = $this->ClienteListado->factura_tipo_asiento($desdeFecha, $hastaFecha);

            $this->set('aTipoAsiento', $aTipoAsiento);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);
//            $this->set('tipo', $tipo);

            $this->render('listado_tipo_asiento_xls', 'blank');
            return true;
    }


    function listado_tipo_asiento_detalle($tipo_asiento, $desdeFecha, $hastaFecha){
            $aTipoAsiento = $this->ClienteListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo_asiento);

            $this->set('aTipoAsiento', $aTipoAsiento);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

    }


    function listado_tipo_asiento_detalle_pdf($tipo_asiento, $desdeFecha, $hastaFecha){
            $aTipoAsiento = $this->ClienteListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo_asiento);

            $this->set('aTipoAsiento', $aTipoAsiento);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('listado_tipo_asiento_pdf', 'pdf');
            return true;

    }


    function listado_tipo_asiento_detalle_xls($tipo_asiento, $desdeFecha, $hastaFecha){
            $aTipoAsiento = $this->ClienteListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo_asiento);

            $this->set('aTipoAsiento', $aTipoAsiento);
            $this->set('desdeFecha', $desdeFecha);
            $this->set('hastaFecha', $hastaFecha);

            $this->render('listado_tipo_asiento_xls', 'blank');
            return true;

    }


    function factura_afip(){

        $facturas = array();
        $showTabla = 0;
        $disable_form = 0;
        $fecha_desde = date("Y") . '-01-01';
        $fecha_hasta = date("Ymd");
        $tipo = 0;

        if(!empty($this->data)):

            $showTabla = 1;
            $disable_form = 1;
            $fecha_desde = $this->ClienteListado->armaFecha($this->data['ListadoIvaVenta']['fecha_desde']);
            $fecha_hasta = $this->ClienteListado->armaFecha($this->data['ListadoIvaVenta']['fecha_hasta']);

            $facturas = $this->ClienteListado->facturas_periodo($fecha_desde, $fecha_hasta);
        endif;

        $this->set('facturas', $facturas);
        $this->set('fecha_desde', $fecha_desde);
        $this->set('fecha_hasta', $fecha_hasta);
        $this->set('showTabla', $showTabla);
        $this->set('disable_form', $disable_form);
    }


    function ws_factura_afip($cliente_factura_id){
        
        $aClntFct = $this->ClienteListado->ws_factura_afip($cliente_factura_id);
        
//        $oClnFactura = $this->ClienteListado->importarModelo('ClienteFactura', 'clientes');

        $oCliente = $this->ClienteListado->importarModelo('Cliente', 'clientes');

//        $aClntFct = $oClnFactura->getFactura($cliente_factura_id);
        $aCliente = $oCliente->getCliente($aClntFct['cliente_id']);

        $this->set('aClntFct', $aClntFct);
        $this->set('aCliente', $aCliente);
    }
	
    
    function imprimir_factura_afip($id){
        $aFacturaAfip = $this->ClienteListado->factura_afip($id);
        
        $this->set('aFacturaAfip', $aFacturaAfip);
        
        
        $this->render('imprimir_factura_afip','pdf');
    }
	
}	
?>