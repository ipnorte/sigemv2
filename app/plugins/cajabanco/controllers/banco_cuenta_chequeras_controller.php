<?php
class BancoCuentaChequerasController extends CajabancoAppController {

	var $name = 'BancoCuentaChequeras';
//	var $uses = array('Cajabanco.BancoCuentaChequera','Config.ConfigurarImpresion','Cajabanco.BancoCuenta');
	var $uses = array('Cajabanco.BancoCuentaChequera','Cajabanco.BancoCuenta');
	var $autorizar = array('index','add','del','edit', 'relacionar_banco_formato_cheque');
	
	function index($banco_cuenta_id = null){
		if(empty($banco_cuenta_id)) $this->redirect('/cajabanco/banco_cuentas');
		$oBANCOCUENTA = $this->BancoCuentaChequera->importarModelo("BancoCuenta","cajabanco");
		$cuenta = $oBANCOCUENTA->getCuenta($banco_cuenta_id);
		if(empty($cuenta)) $this->redirect('/cajabanco/banco_cuentas');
		$this->set('banco_cuenta_id',$banco_cuenta_id);
		$this->set('cuenta',$cuenta);
		$this->set('chequeras',$this->BancoCuentaChequera->getChequerasByBancoCuenta($banco_cuenta_id));
	}
	
	
	function add($banco_cuenta_id = null){
		if(empty($banco_cuenta_id)) $this->redirect('/cajabanco/banco_cuentas');
		$oBANCOCUENTA = $this->BancoCuentaChequera->importarModelo("BancoCuenta","cajabanco");
		$cuenta = $oBANCOCUENTA->read(null,$banco_cuenta_id);
		if(empty($cuenta)) $this->redirect('/cajabanco/banco_cuentas');
		$this->set('banco_cuenta_id',$banco_cuenta_id);
		$this->set('cuenta',$cuenta);
		if(!empty($this->data)):
			if($this->BancoCuentaChequera->guardar($this->data)){
				$this->redirect('index/' . $this->data['BancoCuentaChequera']['banco_cuenta_id']);
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->BancoCuentaChequera->notificaciones);
			}
		endif;		
	}

	function edit($id = null){
		if(empty($id)) $this->redirect('/cajabanco/banco_cuentas');
		if(!empty($this->data)):
			if($this->BancoCuentaChequera->guardar($this->data)){
				$this->redirect('index/' . $this->data['BancoCuentaChequera']['banco_cuenta_id']);
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->BancoCuentaChequera->notificaciones);
			}
		endif;
		$chequera = $this->BancoCuentaChequera->read(null,$id);
		if(empty($chequera)) $this->redirect('/cajabanco/banco_cuentas');
		$oBANCOCUENTA = $this->BancoCuentaChequera->importarModelo("BancoCuenta","cajabanco");
		$cuenta = $oBANCOCUENTA->read(null,$chequera['BancoCuentaChequera']['banco_cuenta_id']);
		if(empty($cuenta)) $this->redirect('/cajabanco/banco_cuentas');
		$this->set('banco_cuenta_id',$chequera['BancoCuentaChequera']['banco_cuenta_id']);
		$this->set('cuenta',$cuenta);
		$this->data = $chequera;
	}
	
	function del($id = null){
		if(empty($id)) $this->redirect('/cajabanco/banco_cuentas');
		$chequera = $this->BancoCuentaChequera->read(null,$id);
		if(empty($chequera)) $this->redirect('/cajabanco/banco_cuentas');		
		if (!$this->BancoCuentaChequera->del($id))$this->Mensaje->errorBorrar();
		$this->redirect('index/' . $chequera['BancoCuentaChequera']['banco_cuenta_id']);			
	}	
	
        
	
}
?>
