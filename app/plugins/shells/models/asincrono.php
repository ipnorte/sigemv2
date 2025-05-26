<?php
/**
 * LANZADOR DE APLICACIONES POR BACKGROUND
 * @author adrian
 * ESTADOS
 * C = CREADO
 * P = PROCESANDO
 * S = DETENIDO
 * F = FINALIZADO
 * E = ERROR
 */

App::import('Vendor','exec',array('file' => 'exec.php'));

class Asincrono extends ShellsAppModel{
	
	var $name = "Asincrono";
	var $auditable = FALSE;
                
	var $validate = array('proceso',VALID_NOT_EMPTY);
	
	
	function save($data = null, $validate = true, $fieldList = array()){
		$this->auditable = FALSE;
		return parent::save($data);
	}
	
	function setTotal($total){
		$this->auditable = FALSE;
		$this->saveField('total',$total);
	}
	
	
	function setValue($field,$value){
		$this->auditable = FALSE;
		return $this->saveField($field,$value);
	}
	
       
        function actualizar($value = 1, $total = 0, $msg = '', $estado = 'P') {
            
            // $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
            // $newVersion = (isset($INI_FILE['general']['newVersion']) && $INI_FILE['general']['newVersion'] != 0 ? TRUE : FALSE);
            // $newVersion = false;
            
            
            $porcentaje = ($value / $total) * 100;
            $porcentaje = round($porcentaje);
            $pid = $this->id; 
            /*
            if($newVersion) {
                echo json_encode([
                    'msg' => $msg,
                    'contador' => $value,
                    'porcentaje' => (!empty($total) ? $porcentaje : 0)
                ]) . "\n";  // Asegura que haya una nueva línea
                if (function_exists('ob_flush') && ob_get_length()) {
                    ob_flush();
                }                
                flush();                
                
            } else {
            */    
		$this->auditable = FALSE;
		$asinc = array();
		$asinc['Asincrono']['id'] = $pid;
		$asinc['Asincrono']['msg'] = $msg;
		$asinc['Asincrono']['contador'] = $value;
                $asinc['Asincrono']['estado'] = $estado;
                $asinc['Asincrono']['total'] = $total;
		$asinc['Asincrono']['porcentaje'] = (!empty($total) ?  $porcentaje : 0);

		return parent::save($asinc);                
                
            //}
            

        }

	function stop($id,$msg=null){
		$asinc = array();
                $this->auditable = FALSE;
		$asinc = $this->read(null,$id);
		$asinc['Asincrono']['msg'] = (!empty($msg) ? $msg : 'DETENIDO POR EL USUARIO');
		$asinc['Asincrono']['estado'] = "S";
		if(!empty($asinc['Asincrono']['shell_pid'])){
                    $output = array();
                    exec("kill -9 " . $asinc['Asincrono']['shell_pid'], $output);
		}
		return parent::save($asinc);
	}
	
	function fin($msg=null){
            $dbCONFIG = new DATABASE_CONFIG();
            $link = mysqli_connect($dbCONFIG->default['host'],$dbCONFIG->default['login'], $dbCONFIG->default['password'],$dbCONFIG->default['database']);            
	    $sql = sprintf("update asincronos SET estado = 'F', msg = '".(!empty($msg) ? $msg : '**** PROCESO FINALIZADO ****')."', porcentaje = 100, final= NOW()  where id = %u;",$this->id);
	    $ret = mysqli_query($link,$sql);
	    if(!$ret) {error_log($link->error);}
            $msg = (empty($msg) ? '**** PROCESO FINALIZADO ****' : $msg);
            $this->actualizar(100, 100, $msg , 'F');        
	    return $ret;            
	}
	
	function error($msg=null){
            $this->auditable = FALSE;
            $asinc = array();
            $asinc['Asincrono']['id'] = $this->id;
            $asinc['Asincrono']['final'] = date('Y-m-d H:m:s');
            $asinc['Asincrono']['estado'] = 'E';
            $asinc['Asincrono']['msg'] = (!empty($msg) ? $msg : 'ERROR EN PROCESO');
            return parent::save($asinc);
	}	
	
	function detenido(){
		$asinc = $this->read(null,$this->id);
		$estado = $asinc['Asincrono']['estado'];
		if($estado == 'S'){
			$this->stop($this->id);
			return true;
		}else{
			return false;
		}
	}
	
	function estado(){
		$asinc = $this->read(null,$this->id);
		return $asinc['Asincrono']['estado']; 
	}
	
	function getParametro($paramIdx){
		$asinc = $this->read(null,$this->id);
		return $asinc['Asincrono'][$paramIdx];
	}
	
	
	function getPropietario(){
		$asinc = $this->read(null,$this->id);
		return $asinc['Asincrono']['propietario'];
	}	
	
	function limpiarTablas(){
		
//		App::import('Model','Shells.Asincrono');
//		App::import('Model','Shells.AsincronoTemporal');
//		App::import('Model','Shells.AsincronoTemporalDetalle');
//		App::import('Model','Shells.AsincronoError');
//		
//		$oASINC = new Asincrono();
//		$oASINCTMP = new AsincronoTemporal();
//		$oASINCTMPD = new AsincronoTemporalDetalle();
//		$oASINCERROR = new AsincronoError();
//		
//		//cargo todos los procesos asincronos que estan en STOP o FINALIZADOS
////		$asincronos = $this->find('all',array('conditions' => array('Asincrono.estado' => array('S','F'))));
////		
////		foreach($asincronos as $asincrono){
//////			$oASINCTMPD->deleteAll("1=1");
////		}
//		
////		$oASINCTMPD->deleteAll("1=1");
////		$oASINCTMP->deleteAll("1=1");
////		$oASINCERROR->deleteAll("1=1");
////		$oASINC->deleteAll("1=1");
		
	}
	
	
	function getJob($id){
		$this->bindModel(array('hasMany' => array('AsincronoError')));
		$job = $this->read(null,$id);
		return $job;
	}
	
	function getCadenaInfo($id){
		$cadena = "";
		$job = $this->read(null,$id);
		if(empty($job))return $cadena;
		$cadena .= $job['Asincrono']['created'];
		$cadena .= " | " .$job['Asincrono']['propietario'];
		$cadena .= " | " . $job['Asincrono']['remote_ip'];
		$cadena .= " | " . $job['Asincrono']['titulo'];
		if(!empty($job['Asincrono']['subtitulo'])) $cadena .= " | " . $job['Asincrono']['subtitulo'];
		return $cadena;
	}
	
	function del($id = null, $cascade = true){
		$asinc = $this->read(null,$id);
		if(!empty($asinc['Asincrono']['shell_pid'])){
            $output = array();
			exec("kill -9 " . $asinc['Asincrono']['shell_pid'], $output);
		}
		return parent::del($id);
	}
	
    
    function get_phpcli(){

		$SHELL = new exec();
		return $SHELL->get_phpcli();		
        
        // $str = explode(" ",php_uname());
        // $os = trim($str[0]);
        // $php_pharser = PHP_BINDIR . "/" . ($os == 'Windows' ? "php.exe" : "php5");
        // return $php_pharser;
		
//		if($this->os == 'Windows'){
//			$DIR_PHP = ini_get('extension_dir');
//			$PHP = explode(DS,$DIR_PHP);
//			$atemp = array();
//			foreach($PHP as $idx => $value){
//				if($value != 'ext'){
//					array_push($atemp,$value);
//				}else{
//					break;
//				}
//			}			
//			$PHP_ROOT = implode(DS,$atemp);
//			$PHP_ROOT .= DS;				
//			return $PHP_ROOT .'php.exe';
//		}else{
//			return '/usr/bin/php5 ';
//		}        
    }
    
    
    function validarLiquidacionJobs($jobs){
        if(!empty($jobs)){
            $SHELL = new exec();
            foreach ($jobs as $i => $job){
                $jobs[$i]['Asincrono']['shell_status'] = array();
                $job['Asincrono']['txt1'] = trim($job['Asincrono']['txt1']);
                if($job['Asincrono']['proceso'] == 'liquida_deuda_fraccion' && $job['Asincrono']['porcentaje'] < 100 && !empty($job['Asincrono']['txt1'])){
                    $jobs[$i]['Asincrono']['shell_status'] = array(
                        'run' => ($SHELL->is_running($job['Asincrono']['shell_pid']) ? "1" : "0"),
                        'comando' => trim($job['Asincrono']['txt1']),
                    );
                }
            }
            return $jobs;
        }
        
    }
}
?>