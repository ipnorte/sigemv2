<?php
/**
*	10/09/2010
*	adrian
*
*/

class SoporteTicketsController extends SoporteAppController{
	
	var $name = 'SoporteTickets';
	
	
	var $autorizar = array(
							'index',
							'add'
	);
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index(){
		$this->paginate = array(
								'limit' => 30,
								'order' => array('SoporteTicket.created' => 'DESC')
								);
		$this->set('tickets', $this->paginate(null));
		$this->set('tipos',$this->SoporteTicket->tipos);
		$this->set('prioridades',$this->SoporteTicket->prioridades);
		$this->set('estados',$this->SoporteTicket->estados);			
	}
	

	/**
	 * Agrega un nuevo ticket
	 * 
	 */
	function add(){
		
		if(!empty($this->data)){

			if($this->data['SoporteTicket']['archivo_adjunto']['error'] != 4){
				
				$allowed = array('xls','doc','pdf','gif','jpg','jpeg','png','txt');
				$ext = trim(substr($this->data['SoporteTicket']['archivo_adjunto']['name'],strrpos($this->data['SoporteTicket']['archivo_adjunto']['name'],".")+1,strlen($this->data['SoporteTicket']['archivo_adjunto']['name'])));
				if(in_array($ext,$allowed)){
					
					$nombre = $this->data['SoporteTicket']['emitido_por']."_";
					$nombre .= str_replace(" ","_",$this->data['SoporteTicket']['archivo_adjunto']['name']);
					$this->data['SoporteTicket']['archivo'] = $nombre;
					//subir_archivo
					$target_path = WWW_ROOT . 'files' . DS . 'soporte' . DS . $nombre;
					
					if($this->SoporteTicket->save($this->data)){
						
						if(move_uploaded_file($this->data['SoporteTicket']['archivo_adjunto']['tmp_name'], $target_path)){
							
							$this->redirect('index');
							
						}else{
							
							$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO ENVIADO!");
							$this->render();
							return;
														
						}
						
					}else{
						
						$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GENERAR EL TICKET!");
						$this->render();
						return;
												
					}
					
				}else{
					
					$this->Mensaje->error("TIPO DE ARCHIVO NO PERMITIDO");
					$this->render();
					return;
					
				}
				
			}else{
				
				if($this->SoporteTicket->save($this->data)){
					
					$this->redirect('index');
					
				}else{
					
					$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GENERAR EL TICKET!");
					$this->render();
					return;
					
				}
				
			}
			
		}
		$this->set('tipos',$this->SoporteTicket->tipos);
		
	}
	
	
}

?>