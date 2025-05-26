<?php


class BcraService {
	
	var $name = 'BcraService';
	var $useTable = NULL;
        var $login = NULL;
        var $url = NULL;
        var $userName = NULL;
        var $userKey = NULL;
        var $token = NULL;
        var $headers = array('Content-Type: application/json');

        /**
         * 
         * @param type $serverName
         * @param type $token
         */
        public function __construct($urlBase,$serverName,$key,$token = NULL) {
            $this->userName = $serverName;
            $this->login = $urlBase . '/login';
            $this->url = $urlBase. '/info';
            $this->userKey = $key;
            if(empty($token)){
                $this->token = $this->generateToken();
            }else{
                $this->token = $token;
            }
            array_push($this->headers,'Authorization: Bearer '. $this->token);
        }
        
        public function generateToken(){
            $loginObj = new stdClass();
            $loginObj->username = $this->userName;
            $loginObj->key = $this->userKey;
            $token = $this->curlPOSTExec($this->login, $this->headers, json_encode($loginObj));
            if(is_object($token)){
                return (isset($token->access_token) ? $token->access_token : NULL);
            }else{
                return NULL;
            }            
        }

	
        public function getFullInfo($cuit){
            $obj = new stdClass();
            $obj->cuitCuil = $cuit;
            $response = $this->curlPOSTExec($this->url, $this->headers, json_encode($obj));
            if(isset($response->statusCode) && $response->statusCode == 401){
                $this->generateToken();
                $response = $this->curlPOSTExec($this->url, $this->headers, json_encode($obj));
            }
            return $response;
        }
        
        public function curlPOSTExec($url,$headers,$post){
            $cliente = curl_init();
            curl_setopt($cliente, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($cliente, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($cliente, CURLOPT_POST,1);
            curl_setopt($cliente, CURLOPT_POSTFIELDS,$post); 
            curl_setopt($cliente, CURLOPT_URL,$url);
            $result = curl_exec($cliente);
            curl_close($cliente);  
            $response = json_decode($result);
            return $response;
        }
        
}
?>