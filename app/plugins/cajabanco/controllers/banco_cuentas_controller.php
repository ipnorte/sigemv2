<?php 
class BancoCuentasController extends CajabancoAppController{
	
	var $name = 'BancoCuentas';
	
	var $uses = array('cajabanco.BancoCuenta', 'cajabanco.BancoChequeTercero', 'cajabanco.BancoCuentaMovimiento', 'config.ConfigurarImpresion');
	
	var $autorizar = array('get', 'combo', 'view_planilla_caja', 'relacionar_banco_formato_cheque', 'index_cheque');
	
	
	function index(){
            $this->paginate = array(
                'limit' => 30,
                'order' => array('Banco.nombre' => 'ASC')
            );		
            $this->set('cuentas',$this->paginate());
	}
	
	function get($id = null){
            if(empty($id)) return null;
            $oBANCOCUENTA = $this->BancoCuenta->importarModelo("BancoCuenta","cajabanco");
            return $oBANCOCUENTA->getCuenta($id);

	}
	
	function add(){
            if(!empty($this->data)):
                if($this->BancoCuenta->guardar($this->data)){
                    $this->redirect('index');
                }else{
                    $this->Mensaje->errores("ERRORES: ",$this->BancoCuenta->notificaciones);
                }
            endif;
	}
	
	
	function del($id = null){
            if(empty($id)): $this->redirect('index'); endif;
            if (!$this->BancoCuenta->del($id)): $this->Mensaje->errorBorrar(); endif;
            $this->redirect('index');			
	}
	
	
	function edit($id = null){
            if(empty($id)): $this->redirect('index'); endif;
            if(!empty($this->data)):
                if($this->BancoCuenta->guardar($this->data)){
                    $this->redirect('index');
                }else{
                    $this->Mensaje->errores("ERRORES: ",$this->BancoCuenta->notificaciones);
                }
            endif;
            $this->data = $this->BancoCuenta->read(null,$id);
	}	
	
	function combo($ecepto=0, $caja=false){
		if(!$caja) return $this->BancoCuenta->combo($ecepto);
		return $this->BancoCuenta->comboCuentas();
	}
	
	
	function view_planilla_caja($id){
		$cuenta = $this->getCuenta($id);
debug($cuenta);
exit;		
	}
        
        
        function relacionar_banco_formato_cheque($banco_cuenta_id){
//            $oBANCOCUENTA = $this->ConfigurarImpresion->importarModelo("BancoCuenta","cajabanco");
//  Se agrego este campo en la tabla.
//  ALTER TABLE `mutual22_sigemdb`.`banco_cuentas` ADD COLUMN `formato_cheque` INT(11) DEFAULT 0 NULL AFTER `saldo_extracto`; 
            
            $cuenta = $this->BancoCuenta->getCuenta($banco_cuenta_id);
            if(!empty($this->data)):
//                debug($this->data);
//                exit;
			if($this->BancoCuenta->guardar($this->data)){
				$this->redirect('index');
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->BancoCuenta->notificaciones);
			}
            endif;		

            $chqConfiguracion = $this->ConfigurarImpresion->find('all');

            $aCombo = array(0 => 'Seleccionar . . .');
            foreach($chqConfiguracion as $dato){
		$aCombo[$dato['ConfigurarImpresion']['id']] = $dato['ConfigurarImpresion']['descripcion'];
            }
            $this->set('cuenta', $cuenta);
            $this->set('banco_cuenta_id', $banco_cuenta_id);
            $this->set('combo', $aCombo);
        }

        
        function index_cheque($id){
            if(empty($id)) $this->redirect('index');

            $cuenta = $this->BancoCuenta->getCuenta($id);
            $movimientos = $this->BancoCuentaMovimiento->getTipoMovimiento($cuenta, 1);

            $this->set('cuenta', $cuenta);
            $this->set('movimientos', $movimientos);

        }
}

?>