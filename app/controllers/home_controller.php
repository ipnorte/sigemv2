<?php
/**
 *
 * @author ADRIAN TORRES
 * @package general
 * @subpackage controller
 */
class HomeController extends AppController{
	var $name = 'Home';
	var $uses = null;

	function beforeFilter(){
		$this->Seguridad->allow('desbloqueardocumentos','desbloquearliquidacion');
		parent::beforeFilter();
	}

	function blank(){$this->render();}

	function index(){}

    function desbloqueardocumentos(){

        App::import('model','config.TipoDocumento');
        $oTD = new TipoDocumento();

        $bloqueados = $oTD->find('all', array('conditions' => array('TipoDocumento.look' => 1)));
        echo "<h5>DESBLOQUEO DE TIPOS DE DOCUMENTOS</h5><hr>";
        if(!empty($bloqueados)){
            foreach($bloqueados as $bloqueado){
                echo "UNLOOK -> " . $bloqueado['TipoDocumento']['documento']." | ".$bloqueado['TipoDocumento']['descripcion']."<br>";
                $bloqueado['TipoDocumento']['look'] = 0;
                $oTD->save($bloqueado);
            }
        }else{
            echo "**** NO EXISTEN TIPOS DE DOCUMENTOS BLOQUEADOS ***";
        }
        exit;

    }

    function desbloquearliquidacion(){

        App::import('Model','Mutual.Liquidacion');
        $oLQ = new Liquidacion();

        $bloqueados = $oLQ->find('all', array('conditions' => array('Liquidacion.bloqueada' => 1)));
        echo "<h5>DESBLOQUEO DE LIQUIDACIONES</h5><hr>";
        if(!empty($bloqueados)){
            foreach($bloqueados as $bloqueado){
                echo "UNLOOK -> #" . $bloqueado['Liquidacion']['id'] . " | ". $bloqueado['Liquidacion']['codigo_organismo']." | ".$bloqueado['Liquidacion']['periodo']."<br>";
                $bloqueado['Liquidacion']['bloqueada'] = 0;
                $oLQ->save($bloqueado);
            }
        }else{
            echo "**** NO EXISTEN LIQUIDACIONES BLOQUEADAS ***";
        }

        exit;

    }

}
?>
