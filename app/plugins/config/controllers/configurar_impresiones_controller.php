<?php
class ConfigurarImpresionesController extends ConfigAppController{

	var $name = 'ConfigurarImpresiones';

	var $autorizar = array('imprimir_cheque_pdf', 'relacionar_banco_formato_cheque', 'imprimir_cheque_ejemplo_pdf');

	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}


	function index($error=0){
		$this->paginate = array(
				'limit' => 30
			);

		$this->set('error', $error);
		$this->set('cnfImpresion',$this->paginate(null));
		$this->render('index');
	
	}
	
	
	function add(){
		if (!empty($this->data)){
			
			if ($this->ConfigurarImpresion->Grabar($this->data)){
				$this->Mensaje->okGuardar();
				$this->redirect('index');
			}else{
				$this->Mensaje->errorGuardar();
			}
		}
		
		$aOpcion = array(
				'DE' => array('texto' => 'Día (Emisión)',       'activo' => 1, 'Izquierda' =>  4.50, 'Superior' => 1.25, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'd'),
				'ME' => array('texto' => 'Mes (Emisión)',       'activo' => 1, 'Izquierda' =>  5.30, 'Superior' => 1.25, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'mmmm'),
				'AE' => array('texto' => 'Año (Emisión)',       'activo' => 1, 'Izquierda' =>  8.60, 'Superior' => 1.25, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'aaaa'),
				'FE' => array('texto' => 'Fecha (Emisión)',     'activo' => 0, 'Izquierda' =>  4.50, 'Superior' => 1.25, 'Ancho' =>  3.00, 'Alto' => 1.00, 'Formato' => 3),
				'DV' => array('texto' => 'Día (Vencimiento)',   'activo' => 1, 'Izquierda' =>  4.00, 'Superior' => 1.75, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'd'),
				'MV' => array('texto' => 'Mes (Vencimiento)',   'activo' => 1, 'Izquierda' =>  5.00, 'Superior' => 1.75, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'mmmm'),
				'AV' => array('texto' => 'Año (Vencimiento)',   'activo' => 1, 'Izquierda' =>  8.00, 'Superior' => 1.75, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'aaaa'),
				'FV' => array('texto' => 'Fecha (Vencimiento)', 'activo' => 0, 'Izquierda' =>  4.00, 'Superior' => 1.75, 'Ancho' =>  3.00, 'Alto' => 1.00, 'Formato' => 3),
				'DS' => array('texto' => 'Destinatario',        'activo' => 1, 'Izquierda' =>  5.00, 'Superior' => 2.20, 'Ancho' =>  3.00, 'Alto' => 1.00, 'Formato' => ''),
				'CN' => array('texto' => 'Cantidad (Número)',   'activo' => 1, 'Izquierda' => 12.80, 'Superior' => 0.61, 'Ancho' =>  3.00, 'Alto' => 1.50, 'Formato' => ''),
				'CL' => array('texto' => 'Cantidad (Letra)',    'activo' => 1, 'Izquierda' =>  6.50, 'Superior' => 3.00, 'Ancho' =>  4.00, 'Alto' => 1.00, 'Formato' => '')
		);
		
                $aCheque = array(
                    'texto' => 'FORMATO DE CHEQUE COMUN',
                    'ancho' => 18.00,
                    'alto'  =>  7.42
                );
                        
		$this->set('aOpcion', $aOpcion);
                $this->set('aCheque', $aCheque);
                
	}
	
	function edit($id=null){
	
            if (empty($id)) {
                $this->redirect('index');
            }
            
            if (!empty($this->data)){
		if ($this->ConfigurarImpresion->save($this->data)){
                    $this->Mensaje->okGuardar();
                    $this->redirect('index');
		}else{
                    $this->Mensaje->errorGuardar();
		}
            }
            
            $aOpcion = array(
                            'DE' => array('texto' => 'Día (Emisión)',       'activo' => 1, 'Izquierda' =>  4.50, 'Superior' => 1.25, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'd'),
                            'ME' => array('texto' => 'Mes (Emisión)',       'activo' => 1, 'Izquierda' =>  5.30, 'Superior' => 1.25, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'mmmm'),
                            'AE' => array('texto' => 'Año (Emisión)',       'activo' => 1, 'Izquierda' =>  8.60, 'Superior' => 1.25, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'aaaa'),
                            'FE' => array('texto' => 'Fecha (Emisión)',     'activo' => 0, 'Izquierda' =>  4.50, 'Superior' => 1.25, 'Ancho' =>  3.00, 'Alto' => 1.00, 'Formato' => 3),
                            'DV' => array('texto' => 'Día (Vencimiento)',   'activo' => 1, 'Izquierda' =>  4.00, 'Superior' => 1.75, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'd'),
                            'MV' => array('texto' => 'Mes (Vencimiento)',   'activo' => 1, 'Izquierda' =>  5.00, 'Superior' => 1.75, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'mmmm'),
                            'AV' => array('texto' => 'Año (Vencimiento)',   'activo' => 1, 'Izquierda' =>  8.00, 'Superior' => 1.75, 'Ancho' =>  0.10, 'Alto' => 1.00, 'Formato' => 'aaaa'),
                            'FV' => array('texto' => 'Fecha (Vencimiento)', 'activo' => 0, 'Izquierda' =>  4.00, 'Superior' => 1.75, 'Ancho' =>  3.00, 'Alto' => 1.00, 'Formato' => 3),
                            'DS' => array('texto' => 'Destinatario',        'activo' => 1, 'Izquierda' =>  5.00, 'Superior' => 2.20, 'Ancho' =>  3.00, 'Alto' => 1.00, 'Formato' => ''),
                            'CN' => array('texto' => 'Cantidad (Número)',   'activo' => 1, 'Izquierda' => 12.80, 'Superior' => 0.61, 'Ancho' =>  3.00, 'Alto' => 1.50, 'Formato' => ''),
                            'CL' => array('texto' => 'Cantidad (Letra)',    'activo' => 1, 'Izquierda' =>  6.50, 'Superior' => 3.00, 'Ancho' =>  4.00, 'Alto' => 1.00, 'Formato' => '')
            );

            $chqConfiguracion = $this->ConfigurarImpresion->find('all', array('conditions' => array('ConfigurarImpresion.id' => $id)));
 
            $aCheque = array(
                'id'         => $chqConfiguracion[0]['ConfigurarImpresion']['id'],
                'texto'      => $chqConfiguracion[0]['ConfigurarImpresion']['descripcion'],
                'ancho'      => $chqConfiguracion[0]['ConfigurarImpresion']['ancho'],
                'alto'       => $chqConfiguracion[0]['ConfigurarImpresion']['alto'],
                'talonario'  => $chqConfiguracion[0]['ConfigurarImpresion']['talonario']
            );
                        
            foreach($chqConfiguracion[0]['ConfigurarImpresionDetalle'] as $aDetalle):
                switch($aDetalle['variable']) {
                
                    case 'DIAVEN':
                        $aOpcion['DV']['activo']    = $aDetalle['imprime'];
                        $aOpcion['DV']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['DV']['Superior']  = $aDetalle['superior'];
                        $aOpcion['DV']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['DV']['Alto']      = $aDetalle['alto'];
                        $aOpcion['DV']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'MESVEN':
                        $aOpcion['MV']['activo']    = $aDetalle['imprime'];
                        $aOpcion['MV']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['MV']['Superior']  = $aDetalle['superior'];
                        $aOpcion['MV']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['MV']['Alto']      = $aDetalle['alto'];
                        $aOpcion['MV']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'ANOVEN':
                        $aOpcion['AV']['activo']    = $aDetalle['imprime'];
                        $aOpcion['AV']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['AV']['Superior']  = $aDetalle['superior'];
                        $aOpcion['AV']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['AV']['Alto']      = $aDetalle['alto'];
                        $aOpcion['AV']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'FECVEN':
                        $aOpcion['FV']['activo']    = $aDetalle['imprime'];
                        $aOpcion['FV']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['FV']['Superior']  = $aDetalle['superior'];
                        $aOpcion['FV']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['FV']['Alto']      = $aDetalle['alto'];
                        $aOpcion['FV']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'DIAEMI':
                        $aOpcion['DE']['activo']    = $aDetalle['imprime'];
                        $aOpcion['DE']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['DE']['Superior']  = $aDetalle['superior'];
                        $aOpcion['DE']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['DE']['Alto']      = $aDetalle['alto'];
                        $aOpcion['DE']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'MESEMI':
                        $aOpcion['ME']['activo']    = $aDetalle['imprime'];
                        $aOpcion['ME']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['ME']['Superior']  = $aDetalle['superior'];
                        $aOpcion['ME']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['ME']['Alto']      = $aDetalle['alto'];
                        $aOpcion['ME']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'ANOEMI':
                        $aOpcion['AE']['activo']    = $aDetalle['imprime'];
                        $aOpcion['AE']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['AE']['Superior']  = $aDetalle['superior'];
                        $aOpcion['AE']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['AE']['Alto']      = $aDetalle['alto'];
                        $aOpcion['AE']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'FECEMI':
                        $aOpcion['FE']['activo']    = $aDetalle['imprime'];
                        $aOpcion['FE']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['FE']['Superior']  = $aDetalle['superior'];
                        $aOpcion['FE']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['FE']['Alto']      = $aDetalle['alto'];
                        $aOpcion['FE']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'DESTIN':
                        $aOpcion['DS']['activo']    = $aDetalle['imprime'];
                        $aOpcion['DS']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['DS']['Superior']  = $aDetalle['superior'];
                        $aOpcion['DS']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['DS']['Alto']      = $aDetalle['alto'];
                        $aOpcion['DS']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'CNTNRO':
                        $aOpcion['CN']['activo']    = $aDetalle['imprime'];
                        $aOpcion['CN']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['CN']['Superior']  = $aDetalle['superior'];
                        $aOpcion['CN']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['CN']['Alto']      = $aDetalle['alto'];
                        $aOpcion['CN']['Formato']   = $aDetalle['formato'];
			break;

                
                    case 'CNTLTR':
                        $aOpcion['CL']['activo']    = $aDetalle['imprime'];
                        $aOpcion['CL']['Izquierda'] = $aDetalle['izquierda'];
                        $aOpcion['CL']['Superior']  = $aDetalle['superior'];
                        $aOpcion['CL']['Ancho']     = $aDetalle['ancho'];
                        $aOpcion['CL']['Alto']      = $aDetalle['alto'];
                        $aOpcion['CL']['Formato']   = $aDetalle['formato'];
			break;

                
                
                }
            
            endforeach;
            
            $this->set('aOpcion', $aOpcion);
            $this->set('aCheque', $aCheque);
	}
	
	
	function del($id = null){
            if (empty($id)) {
                $this->redirect('index');
            }
            if ($this->ConfigurarImpresion->del($id)) {
			$this->Mensaje->okBorrar();
			$this->redirect('index');
		}else{
			$this->Mensaje->errorBorrar();
		}
	}
	
	
        function imprimir_cheque_ejemplo_pdf($id=NULL){
            if (empty($id)) {
                $this->redirect('index');
            }
            
            $chqConfiguracion = $this->ConfigurarImpresion->getEjemploId($id);
            
            $this->set('chqConfiguracion', $chqConfiguracion);
            $this->render('imprimir_cheque_pdf', 'pdf');
            
            
        }
	
	
        function imprimir_cheque_pdf($id=NULL){
            if (empty($id)) {
                $this->redirect('index');
            }
            
            $chqConfiguracion = $this->ConfigurarImpresion->getChequeId($id);
            
            $this->set('chqConfiguracion', $chqConfiguracion);
            $this->render('imprimir_cheque_pdf', 'pdf');
            
            
        }
}
?>