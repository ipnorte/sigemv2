<?php
/*
INSERT INTO `sigem_db`.`permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`, `parent`) VALUES ('60', 'Web Serv. AFIP', '/config/afip_datos', '60', '1', 'arrow_right2.gif', '50'); 
INSERT INTO `sigem_db`.`grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('1', '60'); 
ALTER TABLE `sigem_db`.`cliente_facturas` ADD COLUMN `Afip_Concepto` INT(2) NULL AFTER `anulado`, ADD COLUMN `Afip_DocTipo` INT(2) NULL AFTER `Afip_Concepto`, ADD COLUMN `Afip_MonId` VARCHAR(3) NULL AFTER `Afip_DocTipo`, ADD COLUMN `Afip_MonCotiz` INT(2) NULL AFTER `Afip_MonId`, ADD COLUMN `Afip_Resultado` VARCHAR(1) NULL AFTER `Afip_MonCotiz`, ADD COLUMN `Afip_CodAutorizacion` VARCHAR(20) NULL AFTER `Afip_Resultado`, ADD COLUMN `Afip_EmisionTipo` VARCHAR(6) NULL AFTER `Afip_CodAutorizacion`, ADD COLUMN `Afip_FchVto` DATE NULL AFTER `Afip_EmisionTipo`, ADD COLUMN `Afip_FchProceso` DATETIME NULL AFTER `Afip_FchVto`, ADD COLUMN `Afip_PtoVta` INT(4) NULL AFTER `Afip_FchProceso`, ADD COLUMN `Afip_CbteTipo` INT(2) NULL AFTER `Afip_PtoVta`; 
 * 
 */

class AfipDatosController extends ConfigAppController{
	
	var $name = 'AfipDatos';
	
	var $autorizar = array('combo');
        
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	

        function combo(){
            
        }
        
        
        function index(){
            $datoAfip = $this->AfipDato->getAfipDato();
            $tipoAbm = 1;
                    
            if(empty($datoAfip)){
                $tipoAbm = 0;
                $datoAfip['cuit'] = '';
                $datoAfip['modo'] = 0;
                $datoAfip['cerificado'] = '';
                $datoAfip['clave'] = '';
                $datoAfip['pem'] = '';
                $datoAfip['factura'] = 0;
                $datoAfip['debito'] = 0;
                $datoAfip['credito'] = 0;
                $datoAfip['punto'] = 0;
            }
            
            $this->set('dAfip', $datoAfip);
            $this->set('tipoAbm', $tipoAbm);
	}


        function edit(){
            $datoAfip = $this->AfipDato->getAfipDato();
            $tipoAbm = (empty($datoAfip)? 0 : 1);
            
            $this->set('dAfip', $datoAfip);
            $this->set('tipoAbm', $tipoAbm);
            
        }

        
}
?>
