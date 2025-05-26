<?php

/**
 * ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `siisa` LONGTEXT NULL;
 * 
 * 
 */

class SIISAAuthRequest {
    public $clientId = "";
    public $pinId = "";
    public $password = "";
    public $expires_in = 0;
}


class SIISAAuthResponse {
    public $access_token = "";
    public $token_type = "";
    public $expires_in = 0;
    
}

class SIISAResponse {
    public $nroDoc = 0;
    public $aprueba = 0;
    public $rechaza = 0;
    public $cuota_max = 0;
    public $minimoDisponible = 0;
    public $executedPath = "";
    public $currentExecId = 0;
    public $executionDate = "";
    public $executionTime = 0;
    public $apellidoNombre = "";
    public $decisionResult = "";
    public $executedPolicy = 0;
    public $executedVersion = 0;  
    public $serialize = "";
    public $onError = FALSE;
    public $oERROR = NULL;
}

class SIISARequest {
    public $params = null;
}

class SIISARequestBody {
    public $apellidoNombre = "";
    public $nroDoc = "";
    public $tipo_de_producto = "privado";
    public $debitos_por_cbu = 0;
    public $sueldo_neto = 0;
    public $cuota_credito = 0;
    
}

class SIISAError {
    public $httpCode = 0;
    public $currentExecId = 0;
    public $message = "";
}


App::import('Model','Config.GlobalDato');

class SIISAService {
    
    var $name = 'SIISAService';
    var $useTable = NULL;
    var $oAUTH = NULL;
    var $params = null;
    var $headers = array('Content-Type: application/json');
    var $ERROR = false;
    var $oERROR = NULL;
    
    public function __construct(){
        $INI_FILE = $_SESSION['MUTUAL_INI'];
        $MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);
        if($MOD_SIISA) {
            $this->setConnection();
        }
    }
    
    function executePolicyByParameters($parameters = array()) {
        
        try {
            
            $INI_FILE = $_SESSION['MUTUAL_INI'];
            $MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);
            
            if(!$MOD_SIISA) {
                return NULL;
            }
            
            array_push($this->headers, "accept: text/plain");
            array_push($this->headers, "Authorization: Bearer " . $this->oAUTH->access_token);
            
            $oGlb = new GlobalDato();
            $datos = $oGlb->read(null, 'PERSSISAEXEC');
            
            $url = trim($datos['GlobalDato']['concepto_1']);
            $policy = $datos['GlobalDato']['entero_1'];
            
            $oReq = new SIISARequest();
            $oReqBody = new SIISARequestBody();
            
            $oReqBody->apellidoNombre = $parameters['nombre'];
            $oReqBody->nroDoc = $parameters['nroDoc'];
            $oReqBody->tipo_de_producto = $parameters['tipo_de_producto'];
            $oReqBody->debitos_por_cbu = floatval($parameters['debitos_por_cbu']);
            $oReqBody->sueldo_neto = floatval($parameters['sueldo_neto']);
            $oReqBody->cuota_credito = floatval($parameters['cuota_credito']);
            
            $this->params = $oReqBody;
            
            $oReq->params = $oReqBody;
            
            $response = $this->curlPOSTExec($url."/".$policy, $this->headers, json_encode($oReq));
            
            $oSIISAResponse = new SIISAResponse();
            
            if($this->ERROR) {
                
                $oSIISAResponse->onError = TRUE;
                $oSIISAResponse->oERROR = $this->oERROR;
                
            } else {
                
                $variables = $response->variables;
                
                $oSIISAResponse->nroDoc = $variables->nroDoc;
                $oSIISAResponse->aprueba = $variables->aprueba;
                $oSIISAResponse->rechaza = $variables->rechaza;
                $oSIISAResponse->minimoDisponible = (isset($variables->minimoDisponible) ? $variables->minimoDisponible : 0);
                $oSIISAResponse->monto_max = (isset($variables->monto_max) ? $variables->monto_max : 0);
                $oSIISAResponse->executedPath = $variables->executedPath;
                $oSIISAResponse->currentExecId = $variables->currentExecId;
                $oSIISAResponse->executionDate = $variables->executionDate;
                $oSIISAResponse->executionTime = $variables->executionTime;
                $oSIISAResponse->apellidoNombre = $variables->apellidoNombre;
                $oSIISAResponse->decisionResult = $variables->decisionResult;
                $oSIISAResponse->executedPolicy = $variables->executedPolicy;
                $oSIISAResponse->executedVersion = $variables->executedVersion;
            }
            
            $oSIISAResponse->serialize = base64_encode(serialize($oSIISAResponse));
            
            return $oSIISAResponse;
            
            
        } catch (Exception $e) {
            
            throw new Exception($e);
            
        }
        
    }
    
    
    public function executePolicy($persona, $personaId = NULL, $producto = 'privado', $parameters = array()) {
        
        try {
            
            $persona = $parameters['persona'];
            
            if(empty($persona) && !empty($parameters['personaId'])) {
                App::import('Model', 'Pfyj.Persona');
                $oPERSONA = new Persona();
                $persona = $oPERSONA->read(null, $parameters['personaId']);
            }
            
            if(!$persona) {
                throw new Exception("Persona Inexistente");
            }
            
            $parameters = array(
                'nroDoc' => $persona['Persona']['documento'],
                'nombre' => $persona['Persona']['nombre'] . " " . $persona['Persona']['apellido'],
                'tipo_de_producto' => $parameters['producto']
            );
            
            return $this->executePolicyByParameters($parameters);
            
        } catch (Exception $e) {
            
            throw new Exception($e);
        }
        
    }
    
    private function setConnection() {

        try {
            
            $oGlb = new GlobalDato();
            $datos = $oGlb->read(null, 'PERSSISAAUTH');
            
            $authRequest = new SIISAAuthRequest();
            $authRequest->clientId = trim($datos['GlobalDato']['concepto_2']);
            $authRequest->pinId = trim($datos['GlobalDato']['concepto_3']);
            $authRequest->password = trim($datos['GlobalDato']['concepto_4']);
            $response = $this->curlPOSTExec(trim($datos['GlobalDato']['concepto_1']), $this->headers, json_encode($authRequest));
            $this->oAUTH = new SIISAAuthResponse();
            $this->oAUTH->access_token = $response->access_token;
            $this->oAUTH->token_type = $response->token_type;
            $this->oAUTH->expires_in = $response->expires_in;
            
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
    
    
    private function curlPOSTExec($url,$headers,$post){
        
        try {
            
            $cliente = curl_init();
            curl_setopt($cliente, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($cliente, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($cliente, CURLOPT_POST,1);
            curl_setopt($cliente, CURLOPT_POSTFIELDS,$post);
            curl_setopt($cliente, CURLOPT_URL,$url);
            $result = curl_exec($cliente);
            
            if(!$result || curl_errno($cliente)){ 
                throw new Exception("Error en respuesta del servicio remoto");
            }

            $info = curl_getinfo($cliente);
            curl_close($cliente);
            switch ($info['http_code']) {
                case 200:
                    return json_decode($result);
                    break;
                default:
                    $this->ERROR = TRUE;
                    $response = json_decode($result);
                    $oERROR = new SIISAError();
                    $oERROR->httpCode = $info['http_code'];
                    $oERROR->executionId = $response->executionId;
                    $oERROR->message = $response->message;
                    $this->oERROR = $oERROR;
                    return null;
//                     throw new Exception("ERROR - HTTP CODE: " . $info['http_code']);
            }
            
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        

    }
    
    
}

?>