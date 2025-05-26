<?php
/**
* 	adrian
*	28/09/2010
 * http://localhost/sigem/service/wsdl/creditos
*
*/
class ServiceController extends AppController{
	
	public $name = 'Service';
	public $uses = array('TestService','CreditosService','CuotasService','VentasService','IntranetService','OrdenesService');
	public $helpers = array();
	public $components = array('Soap');
	
	
	
	var $autorizar = array(
							'call',
							'wsdl',
							'xmlrpc'
	);
	
	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	

	/**
	 * Handle SOAP calls
	 */
	function call($model){
		$this->autoRender = FALSE;
		$this->Soap->handle($model, 'wsdl');
	}

	/**
	 * Provide WSDL for a model
	 */
	function wsdl($model){
		$this->autoRender = FALSE;
		header('Content-type: text/xml; charset=UTF-8');
		echo $this->Soap->getWSDL($model, 'call');
	}
	
	
}



?>