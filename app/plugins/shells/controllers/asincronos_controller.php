<?php

class AsincronosController extends ShellsAppController{
	
	var $name = "Asincronos";
	var $autorizar = array('index','crear','execute','show','start','stop','jobs','delete_job','stop_job','getAsincrono','job','new_control','errores','estadisticas',);
	var $cacheAction = false;
	
	var $php_pharser;
	public $os;
	var $caller = 'asinc_dispatcher.php';
	
	function __construct(){
		
		$str = explode(" ",php_uname());
		$this->os = trim($str[0]);
                $this->php_pharser = PHP_BINDIR . "/" . ($this->os == 'Windows' ? "php.exe" : "php5");
		
//		if($this->os == 'Windows'){
////			$DIR_PHP = ini_get('extension_dir');
////			$PHP = explode(DS,$DIR_PHP);
////			
////			$atemp = array();
////			foreach($PHP as $idx => $value){
////				if($value != 'ext'){
////					array_push($atemp,$value);
////				}else{
////					break;
////				}
////			}			
////			
////			$PHP_ROOT = implode(DS,$atemp);
////			$PHP_ROOT .= DS;				
//			$this->php_pharser = PHP_BINDIR .'/php.exe';
//		}else{
//			$this->php_pharser = PHP_BINDIR . '/php5 ';
//		}
		parent::__construct();
		
	}
	
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index(){$this->redirect(array('action'=>'jobs'));}
	
	function crear(){
		if(!empty($this->params['named'])){
			
			$this->data['Asincrono']['id'] = 0;
			$this->data['Asincrono']['proceso'] = $this->params['named']['process'];
			$this->data['Asincrono']['estado'] = 'C';
			
			$user = $this->Seguridad->user();
			
			$this->data['Asincrono']['propietario'] = $user['Usuario']['usuario'];
			$this->data['Asincrono']['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$this->data['Asincrono']['action_do'] = str_replace('.','/',$this->params['named']['action']);
			$this->data['Asincrono']['target'] = $this->params['named']['target'];
			$this->data['Asincrono']['btn_label'] = $this->params['named']['btn_label'];
			$this->data['Asincrono']['titulo'] = $this->params['named']['titulo'];
			$this->data['Asincrono']['subtitulo'] = $this->params['named']['subtitulo'];
			
			if(!empty($this->params['named']['p1']))$this->data['Asincrono']['p1'] = $this->params['named']['p1'];
			if(!empty($this->params['named']['p2']))$this->data['Asincrono']['p2'] = $this->params['named']['p2'];
			if(!empty($this->params['named']['p3']))$this->data['Asincrono']['p3'] = $this->params['named']['p3'];
			if(!empty($this->params['named']['p4']))$this->data['Asincrono']['p4'] = $this->params['named']['p4'];
			if(!empty($this->params['named']['p5']))$this->data['Asincrono']['p5'] = $this->params['named']['p5'];
			if(!empty($this->params['named']['p6']))$this->data['Asincrono']['p6'] = $this->params['named']['p6'];
			if(!empty($this->params['named']['p7']))$this->data['Asincrono']['p7'] = $this->params['named']['p7'];
			if(!empty($this->params['named']['p8']))$this->data['Asincrono']['p8'] = $this->params['named']['p8'];
			if(!empty($this->params['named']['p9']))$this->data['Asincrono']['p9'] = $this->params['named']['p9'];
			if(!empty($this->params['named']['p10']))$this->data['Asincrono']['p10'] = $this->params['named']['p10'];
			if(!empty($this->params['named']['p11']))$this->data['Asincrono']['p11'] = $this->params['named']['p11'];
			if(!empty($this->params['named']['p12']))$this->data['Asincrono']['p12'] = $this->params['named']['p12'];
			if(!empty($this->params['named']['p13']))$this->data['Asincrono']['p13'] = $this->params['named']['p13'];
			if(!empty($this->params['named']['txt1']))$this->data['Asincrono']['txt1'] = $this->params['named']['txt1'];
			if(!empty($this->params['named']['txt2']))$this->data['Asincrono']['txt2'] = $this->params['named']['txt2'];
			
			
//			$CMD = "\"".$this->php_pharser . "\" \"".ROOT.DS.CAKE."console".DS."cake.php\" ". $this->data['Asincrono']['proceso'] . ' '. 1 . " -app \"".APP."\"";
//			debug($CMD);
                        
//                        debug($this->params);
			
			if($this->Asincrono->save($this->data))	return $this->Asincrono->getLastInsertID();
			else return 0;	
		}
	}
	
	function start($pid=null){
		if(!empty($pid)){
			$proceso = $this->Asincrono->read(null,$pid);
			$this->Asincrono->saveField('estado','P');
			$exec = $proceso['Asincrono']['proceso'];
			
			if($this->os == 'Windows'){
				$WshShell = new COM("WScript.Shell");
				$CMD = "\"".$this->php_pharser . "\" \"".ROOT.DS.CAKE."console".DS."cake.php\" ". $exec . ' '. $pid . " -app \"".APP."\"";
//				echo $CMD."<br/>";
//				error_log($CMD."\n\n", 3,'D:\Desarrollo\Proyectos\Mutual_AMAN\v2\logs\asincrono.txt');				
				$WshShell->Run($CMD,0,false);
				unset($WshShell);
			}else{
				//LINUX
				$CMD = $this->php_pharser . " " . ROOT.DS.CAKE."console".DS."cake.php ". $exec . ' '. $pid . " -app ".APP;
//				$this->Auditoria->debugLog($CMD . " > /dev/null &");
				exec($CMD . " > /dev/null &");				
			}
			$this->Asincrono->saveField('estado','P');
		}
	}

	function stop($pid=null){
		if(!empty($pid)){
			$this->Asincrono->stop($pid);
		}
	}	
	
	function show($pid=null){
		if(!empty($pid)){
			$conexion = array();
			$this->set('pathToResponser',$this->pathToResponser);
			$this->set('refreshPogressBar',$this->refreshPogressBar);
			
			$db = & ConnectionManager::getDataSource($this->Asincrono->useDbConfig);
			$conexion['host'] = $db->config['host'];
			$conexion['login'] = $db->config['login'];
			$conexion['password'] = $db->config['password'];
			$conexion['database'] = $db->config['database'];
			$this->set('conexion',base64_encode(serialize($conexion)));
			$this->set('pid',$pid);
			
			$proceso = $this->Asincrono->read(null,$pid);
			
		
			#VERIFICO QUE NO HAYA OTROS PROCESOS DEL MISMO SHELL EJECUTANDOSE
			$otros = $this->Asincrono->find('all',array('conditions' => array('Asincrono.proceso' => $proceso['Asincrono']['proceso'],'Asincrono.id <>' => $proceso['Asincrono']['id'])));

			$this->set('btn_label',$proceso['Asincrono']['btn_label']);
			$this->set('action_do',$proceso['Asincrono']['action_do']);
			$this->set('target',$proceso['Asincrono']['target']);
			
			$this->set('titulo',$proceso['Asincrono']['titulo']);
			$this->set('subtitulo',$proceso['Asincrono']['subtitulo']);
			
			$this->set('otros',null);
			
			//funciones remotas
			if(!empty($this->params['named']['remote_call_start']))$this->set('remote_call_start',$this->params['named']['remote_call_start']);
			if(!empty($this->params['named']['remote_call_stop']))$this->set('remote_call_stop',$this->params['named']['remote_call_stop']);
			
		}
		$this->render(null,"ajax");		
	}

	
	function getAsincrono($pid=null){
		$asincrono = array();
		if(!empty($pid)){
			$conexion = array();

			$asincrono['pathToResponser'] = $this->pathToResponser;
			$asincrono['refreshPogressBar'] = $this->refreshPogressBar;
			
		
			$db = & ConnectionManager::getDataSource($this->Asincrono->useDbConfig);
			$conexion['host'] = $db->config['host'];
			$conexion['login'] = $db->config['login'];
			$conexion['password'] = $db->config['password'];
			$conexion['database'] = $db->config['database'];
			
			$asincrono['conexion'] = base64_encode(serialize($conexion));
			$asincrono['pid'] = $pid;
			
			$proceso = $this->Asincrono->read(null,$pid);
			
			#VERIFICO QUE NO HAYA OTROS PROCESOS DEL MISMO SHELL EJECUTANDOSE
			$otros = $this->Asincrono->find('all',array('conditions' => array('Asincrono.proceso' => $proceso['Asincrono']['proceso'],'Asincrono.id <>' => $proceso['Asincrono']['id'])));

			$asincrono['btn_label'] = $proceso['Asincrono']['btn_label'];
			$asincrono['action_do'] = $proceso['Asincrono']['action_do'];
			$asincrono['target'] = $proceso['Asincrono']['target'];
			
			$asincrono['titulo'] = $proceso['Asincrono']['titulo'];
			$asincrono['subtitulo'] = $proceso['Asincrono']['subtitulo'];
			
			$asincrono['otros'] = null;
			
		}
		return $asincrono;	
	}	
	
	function jobs(){
		$this->paginate = array('limit' => 50,'order' => array('Asincrono.created' => 'DESC'));
                $jobs = $this->paginate();
                
                $nJobs = $this->Asincrono->validarLiquidacionJobs($jobs);
		$this->set('jobs', $nJobs);
	}
	
	function job($id = null){
		if(empty($id)) parent::noDisponible();
		$job = $this->Asincrono->getJob($id);
		if(empty($job)) parent::noDisponible();
		$this->set('job',$job);
	}
	
	
	function stop_job($pid=null){
		if(empty($pid)) $this->redirect(array('action'=>'jobs'));
		$this->stop($pid);
		$this->redirect(array('action'=>'jobs'));
	}
	
	
	function delete_job($pid=null){
		if(empty($pid)) $this->redirect(array('action'=>'jobs'));
		$this->Asincrono->del($pid);
		$this->redirect(array('action'=>'jobs'));
	}
	
	
	function new_control(){
		
	}
	
	
	function errores($id){
		if(empty($id)) parent::noDisponible();
		$job = $this->Asincrono->read(null,$id);
		if(empty($job)) parent::noDisponible();
		App::import('Model','Shells.AsincronoError');
		$oERROR = new AsincronoError();	
		
		$this->set('job',$job);	
		$this->set('errores',$oERROR->getErroresByAsincronoId($id));		
	}
	
        
        function estadisticas($periodo = NULL,$meses = NULL){
            
            $periodo = (empty($periodo) ? date('Ym') : $periodo);
            $meses = (empty($meses) ? 12 : $meses);
            
            if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
                App::import('model','Shells.Asincrono');
                $oASINC = new Asincrono();
                $asinc = $oASINC->read('p1,p2,p3,p4,p6',$this->params['url']['pid']);  
                $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p6']);                
            }
            
            $this->set('periodo',$periodo);
            $this->set('meses',$meses);
            
        }
        
}
?>