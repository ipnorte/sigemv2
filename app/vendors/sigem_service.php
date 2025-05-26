<?php 

App::import('Model','Proveedores.Proveedor');

class SIGEMService{
	
	var $proveedor = null;
	var $oRESPONSE = NULL;
	
	function SIGEMService(){
            $this->proveedor = null;
            $this->oRESPONSE = new stdClass();
            $this->oRESPONSE->client = '';
            $this->oRESPONSE->client_key = '';
            $this->oRESPONSE->client_pin = '';
            $this->oRESPONSE->error = '';
            $this->oRESPONSE->msg_error = '';
            $this->oRESPONSE->find = '';
            $this->oRESPONSE->result = array();
	}
	
	var $response = array(
		'client' => '',
		'client_key' => 0,
		'client_pin' => "",
                'token' => null,
		'error' => 0,
		'msg_error' => '',
		'find' => 0,                
		'result' => array()
	);
	
	
	/**
	 * Setea un elemento del array que representa la respuesta del servicio
	 * @param $key
	 * @param $value
	 */
	function setResponse($key,$value, $ctrlTOKEN = true){
            if ($ctrlTOKEN) {
               $this->validateToken();
            }              
            $this->response[$key] = $value;
	}
	
	/**
	 * devuelve codificado el atributo response
	 */
	function getResponse(){
            return json_encode($this->response);
	}
        
        function getResponseObjectJSON(){             
            return json_encode($this->oRESPONSE);
        }

	/**
	 * Codifica en JSON un valor pasado por parametro
	 * @param unknown_type $value
	 */
	function encode($value){
		return json_encode($value);
	}	
	
	/**
	 * Valida el PIN de acceso del cliente remoto
	 * setea el proveedor
	 * @param unknown_type $PIN
	 */
	function validatePIN($PIN, $ctrlTOKEN = true){
            
                if ($ctrlTOKEN) {
                    $this->validateToken();
                }               
            
		$proveedor = null;
		$this->proveedor = null;
		$oPROVEEDOR = new Proveedor();
		$proveedor = $oPROVEEDOR->getProveedorByPIN($PIN);
		if(empty($proveedor)):
			$this->proveedor = null;
			$this->response['client'] = "***";
			$this->response['error'] = 1;
			$this->response['msg_error'] = "SOAP-ERROR: PIN Incorrecto";
                        
                        $this->oRESPONSE->cliente = $this->response['client'];
                        $this->oRESPONSE->error = $this->response['error'];
                        $this->oRESPONSE->msg_error = $this->response['msg_error'];
                        
			return false;
		else:
			$this->proveedor = $proveedor['Proveedor'];
			$this->response['client'] = $proveedor['Proveedor']['razon_social'];
			$this->response['client_key'] = $proveedor['Proveedor']['id'];
			$this->response['client_pin'] = $proveedor['Proveedor']['codigo_acceso_ws'];
                        $this->response['token'] = $this->generateToken();
                        
                        $this->oRESPONSE->cliente = $this->response['client'];
                        $this->oRESPONSE->client_key = $this->response['client_key'];
                        $this->oRESPONSE->client_pin = $this->response['client_pin'];                        
                        $this->oRESPONSE->token = $this->response['token']; 
                        
			return true;	
		endif;
	}

	
	/**
	 * Valida el PIN de acceso del cliente remoto
	 * setea el proveedor
	 * @param unknown_type $PIN
	 */
	function validatePIN_KEY($PIN,$key,$ctrlTOKEN = false){
            
                if ($ctrlTOKEN) {
                   $this->validateToken();
                }              
            
		$oPROVEEDOR = new Proveedor();
		$proveedor = $oPROVEEDOR->getProveedorByPIN($PIN);
		$this->proveedor = $proveedor;
		if(empty($this->proveedor)):
			$this->response['client'] = "***";
			$this->response['error'] = 1;
			$this->response['msg_error'] = "SOAP-ERROR: PIN Incorrecto";
                        
                        $this->oRESPONSE->client = $this->response['client'];
                        $this->oRESPONSE->error = $this->response['error'];
                        $this->oRESPONSE->msg_error = $this->response['msg_error'];
                        
			return false;
		elseif($key == $this->proveedor['Proveedor']['id'] && $PIN == $this->proveedor['Proveedor']['codigo_acceso_ws']):
			$this->response['client'] = $this->proveedor['Proveedor']['razon_social'];
			$this->response['client_key'] = $this->proveedor['Proveedor']['id'];
			$this->response['client_pin'] = $this->proveedor['Proveedor']['codigo_acceso_ws'];
                        
                        $this->oRESPONSE->client = $this->response['client'];
                        $this->oRESPONSE->client_key = $this->response['client_key'];
                        $this->oRESPONSE->client_pin = $this->response['client_pin'];
                        
			return true;
		else:
			$this->response['client'] = "***";
			$this->response['error'] = 1;
			$this->response['msg_error'] = "SOAP-ERROR: El PIN no se corresponde con la LLAVE enviada";
                        
                        $this->oRESPONSE->client = $this->response['client'];
                        $this->oRESPONSE->error = $this->response['error'];
                        $this->oRESPONSE->msg_error = $this->response['msg_error'];
                        
			return false;
		endif;
	}	
	
	/**
	 * Devuelve una provincia en base a la letra
	 * @param unknown_type $letra
	 * @param unknown_type $field
	 */
	function getProvinciaByLetra($letra,$field=null){
		App::import('Model', 'Config.Provincia');
		$oPROVINCIA = new Provincia(null);
		$provincia = $oPROVINCIA->find('all',array('conditions' => array('Provincia.letra' => $letra)));
		if(!empty($provincia)) return (empty($field) ? $provincia[0] : $provincia[0]['Provincia'][$field]);
		else return null;
	}	

    /**
     * normaliza un periodo
     * @param $periodo
     * @param $ampliado
     * @return string
     */
	function periodo($periodo,$ampliado=false){
		if(empty($periodo)) return "";
		$meses = array('01' => 'ENE', '02' => 'FEB', '03' => 'MAR', '04' => 'ABR', '05' => 'MAY', '06' => 'JUN', '07' => 'JUL', '08' => 'AGO', '09' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DIC');	
		$mesesA = array('01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');
		if(!$ampliado)return $meses[substr($periodo,4,2)] . '-'.substr($periodo,0,4);
		else return $mesesA[substr($periodo,4,2)] . ' / '.substr($periodo,0,4);
	}

	
	function GlobalDato($field,$codigo){
		App::import('Model', 'Config.GlobalDato');
		$this->GlobalDato = new GlobalDato(null);
		$dato = $this->GlobalDato->read($field,$codigo);	
		return $dato['GlobalDato'][$field];
	}
        
        
	function writeLog($message){
            $filename = LOGS . 'WEBSERVICE_'.date('Ymd') . '.log';
            $log = new File($filename, true);
            $message .= "\r\n"; 
            if ($log->writable()) $log->append($message);
	}
	
        function generateToken() {
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $expires = time() + 600; // Token expira en 10 minutos
            $this->storeToken($token, $expires);
            return $token;
        }
        
        private function storeToken($token, $expires) {
            $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $token;
            $data = json_encode(['token' => $token, 'expires' => $expires]);
            file_put_contents($filePath, $data);
        }
        
        function isTokenValid($token) {
            $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $token;
            if (file_exists($filePath)) {
                $data = json_decode(file_get_contents($filePath), true);
                if ($data && $data['expires'] > time()) {
                    return true;
                } else {
                    unlink($filePath); // Eliminar token expirado
                }
            }
            return false;
        }    

        function validateToken() {
            // Obtener encabezados de forma compatible con ambos entornos
            $headers = $this->getRequestHeaders();

            if (isset($headers['Authorization'])) {
                $token = str_replace('Bearer ', '', $headers['Authorization']);

                if ($this->isTokenValid($token)) {
                    return true;
                } else {
                    return $this->returnSoapError('Invalid or expired token');
                }
            } else {
                return $this->returnSoapError('Missing Authorization header');
            }
        }

        /**
         * Método para obtener los encabezados de la solicitud de manera compatible con mod_php y FastCGI
         */
        private function getRequestHeaders() {
            if (function_exists('apache_request_headers')) {
                return apache_request_headers();
            } elseif (function_exists('getallheaders')) {
                return getallheaders();
            } else {
                return $this->getHeadersFromServer();
            }
        }

        /**
         * Método alternativo para obtener encabezados si apache_request_headers() y getallheaders() no están disponibles
         */
        private function getHeadersFromServer() {
            $headers = [];
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $header = str_replace('_', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    $headers[$header] = $value;
                }
            }
            return $headers;
        }

        /**
         * Método para generar una respuesta de error SOAP
         */
        protected function returnSoapError($message) {
            $error = new stdClass();
            $error->faultcode = 'SOAP-ENV:Server';
            $error->faultstring = $message;
            $this->outputSoapError($error);
            return false;
        }
 
        

        function outputSoapError($error) {
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">';
            echo '<SOAP-ENV:Body>';
            echo '<SOAP-ENV:Fault>';
            echo '<faultcode>' . $error->faultcode . '</faultcode>';
            echo '<faultstring>' . $error->faultstring . '</faultstring>';
            echo '</SOAP-ENV:Fault>';
            echo '</SOAP-ENV:Body>';
            echo '</SOAP-ENV:Envelope>';
            exit;
        }
       
        
}

?>