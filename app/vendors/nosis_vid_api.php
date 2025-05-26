<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of nosis_api
 *
 * @author adrian
 */
class NosisVidApi {
    
    //put your code here
    private $urlApi;
    private $userId;
    private $tokenId;
    private $NroGrupoVID;
    private $nDoc;
    private $consulta = array();
    public $idConsulta = NULL;
    public $pedido = NULL;
    public $resultado = NULL;
    public $personaNosis = NULL;
    public $smsNosis = NULL;
    public $cbuNosis = NULL;

    const VALIDATION_METHOD = 'Validacion';
    const EVALUATION_METHOD = 'Evaluacion';


    public function __construct($urlApi,$userId,$tokenId,$NroGrupoVID,$nDoc,$idConsulta = NULL) {
        $this->urlApi = $urlApi;
        $this->userId = $userId;
        $this->tokenId = $tokenId;
        $this->NroGrupoVID = $NroGrupoVID;
        $this->nDoc = $nDoc;
        $this->idConsulta = $idConsulta;
        
        $this->consulta = array(
            'Usuario' => $this->userId,
            'Token' => $this->tokenId,
            'Documento' => $this->nDoc,
            'NroGrupoVID' => $this->NroGrupoVID,            
        );
    }
    
    public function evaluarTokenSMS($IdConsulta,$TokenSms){
        $json = $this->getJSON(NULL,NULL,$IdConsulta,$TokenSms);
        $this->serviceCall(self::EVALUATION_METHOD, $json);
        $respuesta = array(
            'Pedido' => $this->pedido,
            'Resultado' => $this->resultado,
            'Datos' => array(
                'IdConsulta' => $this->idConsulta,
                'Persona' => array(
                    'Documento' => $this->personaNosis->Documento,
                    'RazonSocial' => $this->personaNosis->RazonSocial,
                    'Sexo' => $this->personaNosis->Sexo,
                    'FechaNacimiento' => $this->personaNosis->FechaNacimiento
                ),
                'Sms' => array(
                    'Estado' => $this->smsNosis->Estado,
                    'Validado' => ($this->smsNosis->Estado !== 'APROBADO' ? FALSE : TRUE),
                )                
            ),
        );
        return $respuesta;        
    }

    public function validarSMS($nroCelular){
        $json = $this->getJSON(NULL,$nroCelular);
        $this->serviceCall(self::VALIDATION_METHOD, $json);
        $respuesta = array(
            'Pedido' => $this->pedido,
            'Resultado' => $this->resultado,
            'Datos' => array(
                'IdConsulta' => $this->idConsulta,
                'Persona' => array(
                    'Documento' => $this->personaNosis->Documento,
                    'RazonSocial' => $this->personaNosis->RazonSocial,
                    'Sexo' => $this->personaNosis->Sexo,
                    'FechaNacimiento' => $this->personaNosis->FechaNacimiento
                ),
                'Sms' => array(
                    'TokenEnviado' => (empty($this->smsNosis->TokenEnviado) ? 0 : 1),
                    'Novedad' => $this->smsNosis->Novedad,
                    'Prefijo' => $this->smsNosis->Prefijo,
                    'Estado' => $this->smsNosis->Estado,
                    'Validado' => ($this->smsNosis->Estado !== 'APROBADO' || empty($this->smsNosis->TokenEnviado)  ? FALSE : TRUE),
                )                
            ),
        );
        return $respuesta;        
    }
    
    public function validarCBU($cbu){
        $json = $this->getJSON($cbu);
        $this->serviceCall(self::VALIDATION_METHOD, $json);
        $respuesta = array(
            'Pedido' => $this->pedido,
            'Resultado' => $this->resultado,
            'Datos' => array(
                'IdConsulta' => $this->idConsulta,
                'Persona' => array(
                    'Documento' => $this->personaNosis->Documento,
                    'RazonSocial' => $this->personaNosis->RazonSocial,
                    'Sexo' => $this->personaNosis->Sexo,
                    'FechaNacimiento' => $this->personaNosis->FechaNacimiento
                ),
                'Cbu' => array(
                    'Novedad' => $this->cbuNosis->Novedad,
                    'Estado' => $this->cbuNosis->Estado,
                    'Validado' => ($this->cbuNosis->Estado !== 'APROBADO' ? FALSE : TRUE),
                )                
            ),
        );
        return $respuesta;
    }
    
    private function getJSON($cbu = NULL,$nroCelular = NULL,$IdConsulta = NULL,$TokenSms = NULL){
        if(!empty($cbu)){
            $this->consulta['Cbu'] = $cbu;
        }
        if(!empty($nroCelular)){
            $this->consulta['Celular'] = $nroCelular;
        }
        if(!empty($IdConsulta)){
            $this->consulta['IdConsulta'] = $IdConsulta;
        }        
        if(!empty($TokenSms)){
            $this->consulta['TokenSms'] = $TokenSms;
        }
        return json_encode($this->consulta);
    }


    private function serviceCall($metodo,$json){
	$cliente = curl_init();
	curl_setopt($cliente, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($cliente, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($cliente, CURLOPT_POST,1);
	curl_setopt($cliente, CURLOPT_POSTFIELDS,$json);
	curl_setopt($cliente, CURLOPT_URL, $this->urlApi . $metodo);	
	$result = curl_exec($cliente);
        if(!$result){ return FALSE;}
        if (!curl_errno($cliente)){
            $info = curl_getinfo($cliente);
            curl_close($cliente);
            if($info['http_code'] == 200){
                $response = json_decode($result);
                $this->resultado = $response->Contenido->Resultado;
                if($this->resultado->Estado >= 200 && $this->resultado->Estado < 400) {
                    $this->pedido = $response->Contenido->Pedido;
                    $this->idConsulta = (isset($response->Contenido->Datos->IdConsulta) ? $response->Contenido->Datos->IdConsulta : NULL);
                    $this->personaNosis = $response->Contenido->Datos->Persona;
                    $this->smsNosis = (isset($response->Contenido->Datos->Sms) ? $response->Contenido->Datos->Sms : NULL);
                    $this->cbuNosis = (isset($response->Contenido->Datos->Cbu) ? $response->Contenido->Datos->Cbu : NULL);
                }else if($this->resultado->Estado >= 400){
                    $this->pedido = $response->Contenido->Pedido;
                    $this->personaNosis = $response->Contenido->Datos->Persona;
                }                
            }else{
                return FALSE;
            }
        }
	        
    }
    
    
    
}
