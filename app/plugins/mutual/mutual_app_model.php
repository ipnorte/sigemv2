<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 *
 */

if (!defined('MODULO_V1')){
    $file = parse_ini_file(CONFIGS.'mutual.ini', true);
    define('MODULO_V1',(isset($file['general']['habilitar_modulo_v1']) && !empty($file['general']['habilitar_modulo_v1']) ? TRUE : FALSE));
}

class MutualAppModel extends AppModel{
	
	
	/**
	 * tipos de Ordenes de Descuentos
	 * @var array
	 */
	var $tiposOrdenDto = array(
		'CMUTU' => 'CMUTU - CARGOS MUTUAL',
		'EXPTE' => 'EXPTE - EXPEDIENTE',
		'OCOMP' => 'OCOMP - ORDEN DE COMPRA',
		'OSERV' => 'OSERV - ORDEN DE SERVICIO',
		'RECAR' => 'RECAR - RECUPERO DE CARTERA',
	);	
	
	/**
	 * estados de una cuota
	 * @var array
	 */
	var $codigos_estado_cuota = array(
										'A' => array('codigo_db' => 'A','label_vista' => 'Adeudada'),
										'P' => array('codigo_db' => 'P','label_vista' => 'Pagada'),
										'B' => array('codigo_db' => 'P','label_vista' => 'Baja'),
										'C' => array('codigo_db' => 'C','label_vista' => 'Convenio'),
										'D' => array('codigo_db' => 'D','label_vista' => 'Cob.Directo'),
	);
	
	/**
	 * Id del proveedor Lomas de Villa Allende
	 * @var integer
	 */
	var $lomas_villa_allende = 0;
	
	function __construct(){
            
            $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
            parent::__construct($id = false, $table = null, $ds = null);
            
            $this->CJP_COD_CSOC = (isset($INI_FILE['intercambio']['CJP_COD_CSOC']) && !empty($INI_FILE['intercambio']['CJP_COD_CSOC']) ? $INI_FILE['intercambio']['CJP_COD_CSOC'] : $this->CJP_COD_CSOC);
            $this->CJP_SCOD_CSOC = (isset($INI_FILE['intercambio']['CJP_SCOD_CSOC']) && !empty($INI_FILE['intercambio']['CJP_SCOD_CSOC']) ? $INI_FILE['intercambio']['CJP_SCOD_CSOC'] : $this->CJP_SCOD_CSOC);

            $this->CJP_COD_CONS = (isset($INI_FILE['intercambio']['CJP_COD_CONS']) && !empty($INI_FILE['intercambio']['CJP_COD_CONS']) ? $INI_FILE['intercambio']['CJP_COD_CONS'] : $this->CJP_COD_CONS);
            $this->CJP_SCOD_CONS = (isset($INI_FILE['intercambio']['CJP_SCOD_CONS']) && !empty($INI_FILE['intercambio']['CJP_SCOD_CONS']) ? $INI_FILE['intercambio']['CJP_SCOD_CONS'] : $this->CJP_SCOD_CONS);

     }
     
     function setBarcodeSolicitud($solicitud){
         $barCode = parent::fill($solicitud['MutualProductoSolicitud']['id'],7,'0','L');
         $barCode .= parent::fill($solicitud['MutualProductoSolicitud']['socio_id'],7,'0','L');
         $barCode .= substr($solicitud['MutualProductoSolicitud']['tipo_producto'],8,4);
         $barCode .= parent::fill($solicitud['MutualProductoSolicitud']['importe_total']*100,7,'0','L');
         $barCode .= parent::fill($solicitud['MutualProductoSolicitud']['cuotas'],2,'0','L');
         $barCode .= date('Ymd',strtotime($solicitud['MutualProductoSolicitud']['fecha_pago']));
         $barCode .= parent::digitoVerificador($barCode);
         $solicitud['MutualProductoSolicitud']['barcode'] = $barCode;
         return $solicitud;
     }
     
     function setBarcodeOrdenDto($orden){
         $barCode = parent::fill($orden['OrdenDescuento']['id'],7,'0','L');
         $barCode .= parent::fill($orden['OrdenDescuento']['socio_id'],7,'0','L');
         $barCode .= substr($orden['OrdenDescuento']['tipo_producto'],8,4);
         $barCode .= parent::fill($orden['OrdenDescuento']['importe_total']*100,7,'0','L');
         $barCode .= parent::fill($orden['OrdenDescuento']['cuotas'],2,'0','L');
         $barCode .= date('Ymd',strtotime($orden['OrdenDescuento']['fecha']));
         $barCode .= parent::digitoVerificador($barCode);
         $orden['OrdenDescuento']['barcode'] = $barCode;
         return $orden;
     }
     
	function leerArchivo($path){
            if(!file_exists($path)) return false;
            $registros = array();
            $registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if(!is_array($registros)) return null;
            foreach ($registros as $i => $registro) {
                // $registros[$i] = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $registro);
                $registros[$i] = preg_replace("[^A-Za-z0-9]", "",$registro);
            }
            return $registros;		
	}      
	
}

?>