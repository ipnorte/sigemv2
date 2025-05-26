<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 */
class ControlesHelper extends AppHelper {
	

	/**
	 * getAcciones
	 * Muestra los iconos para consultar, editar o borrar un registro
	 *
	 * @param int $id ID del registro
	 * @param boolean $ver especifica si se muestra el icono ver
	 * @param boolean $editar especifica si se muestra el icono editar
	 * @param boolean $borrar especifica si se muestra el icono borrar
	 * @return string
	 */
	function getAcciones($id,$ver=true,$editar=true,$borrar=true){
		$action = "";
		if($ver)$action .= $this->Html->link($this->Html->image('controles/search.png',array("border"=>"0",'alt'=>'Ver')), array('action'=>'view',$id),null,false,false);
		if($editar)$action .= $this->Html->link($this->Html->image('controles/edit.png',array("border"=>"0",'alt'=>'Modificar')), array('action'=>'edit',$id),null,false,false);
		if($borrar)$action .= $this->Html->link($this->Html->image('controles/user-trash-full.png',array("border"=>"0",'alt'=>'Borrar')), array('action'=>'del',$id), null, sprintf(__('Borrar el registro #%s?', true), $id),false);
		return $this->output($action);
	}

	/**
	 * OnOff
	 * Muestra un icono de activo o desactivo segun el estado pasado por parametro
	 * @param int $estado
	 * @param boolean $soloOn
	 * @return HTMLstring
	 */
	function OnOff($estado=0,$soloOn=false){
		$img = "controles/12-em-cross.png";
		if($estado==1){$img = "controles/12-em-check.png";}
		$btn = $this->Html->image($img);
		if($soloOn && ($estado=='0' || $estado=='')) {$btn = '';}
		return $this->output($btn);
	}	
	
	function OnOff2($estado=0,$soloOn=false){
		$img = "controles/redled.png";
		if($estado==1){$img = "controles/accept.png";}
		$btn = $this->Html->image($img);
		if($soloOn && ($estado=='0' || $estado=='')){ $btn = '';}
		return $this->output($btn);
	}
	
	function errorIcon($error){
		$img = "controles/error.png";
		if($error==0)$img = "controles/accept.png";
		$btn = $this->Html->image($img);
		return $this->output($btn);
	}	
	
	function vencida($estado){
		$img = "controles/redled.png";
		if($estado == 0)$img = "controles/greenled.png";
		$btn = $this->Html->image($img);
		return $this->output($btn);
	}
	
	/**
	 * botonGenerico
	 *
	 * @param unknown_type $controller
	 * @param unknown_type $img
	 * @param unknown_type $texto
	 * @param unknown_type $atributos
	 * @param unknown_type $confirm_msj
	 */
	function botonGenerico($controller,$img ,$texto='' ,$atributos=null, $confirm_msj=null){
		$title = $this->Html->image($img,array("border"=>"0"))." ".$texto;
		$btn = $this->Html->link($title,$controller,$atributos,$confirm_msj,false);
		return $this->output($btn);
	}
	
	
	function btnRew($texto, $controller, $confirm_msj = null){
		$img = "controles/player_rew.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}

	function btnFwd($texto, $controller, $confirm_msj = null){
		$img = "controles/player_fwd.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}

	function btnUp($texto, $controller, $confirm_msj = null){
		$img = "controles/arrow_up.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}	
	
	function btnDown($texto, $controller, $confirm_msj = null){
		$img = "controles/arrow_down.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}

	function btnR($texto, $controller, $confirm_msj = null){
		$img = "controles/arrow_right.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}	
	
	function btnL($texto, $controller, $confirm_msj = null){
		$img = "controles/arrow_left.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}	
	
	function btnEdit($texto, $controller, $confirm_msj = null){
		$img = "controles/edit.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}	
	
	function btnAdd($texto, $controller, $confirm_msj = null){
		$img = "controles/add.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}
	
	function btnDrop($texto, $controller, $confirm_msj = null){
		$img = "controles/user-trash-full.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}	

	function btnView($texto, $controller, $confirm_msj = null){
		$img = "controles/search.png";
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos)." ".$texto;
		$btn = $this->Html->link($title,$controller,null,$confirm_msj,false);
		return $this->output($btn);
	}

	function btnImprimir($texto, $controller,$target='',$confirm_msj=null){
		return $this->botonGenerico($controller,"controles/pdf.png",$texto,array('target' => $target));
	}
	
	function btnImprimirPDF($texto, $controller,$target='',$confirm_msj=null){
		return $this->botonGenerico($controller,"controles/pdf.png",$texto,array('target' => $target));
	}
		
	function linkAjax($txt,$url,$update,$formId = null,$msg = null){
		$UID = intval(mt_rand());
		$imgAjax = "controles/ajax-loader.gif";
		$atributosAjax = array("border"=>"0");		
		if(!empty($formId))$html = $this->Ajax->link($txt,$url,array('before'=>"$('ajax_loader_$UID').show();", 'complete'=>"$('ajax_loader_$UID').hide();","update" => "$update",'url' => "$url",'with' => "$('".$formId."').serialize()"));
		else $html = $this->Ajax->link($txt,$url,array('before'=>"$('ajax_loader_$UID').show();", 'complete'=>"$('ajax_loader_$UID').hide();","update" => "$update",'url' => "$url"),$msg,false);
		$html .= "<span id=\"ajax_loader_$UID\" style=\"display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;\">";
		$html .= $this->Html->image($imgAjax,$atributosAjax);
		$html .= "</span>";
		return $html;
	}
	
	function btnAjax($image,$url,$update,$formId = null,$confirm_msj=null){
		
		$UID = intval(mt_rand());
		
		$title = $this->Html->image($image,array("border"=>"0"));
		
		$imgAjax = "controles/ajax-loader.gif";
		$atributosAjax = array("border"=>"0");		
		if(!empty($formId)) $html = $this->Ajax->link($title,$url,array('before'=>"$('ajax_loader_".$UID."').show();", 'complete'=>"$('ajax_loader_".$UID."').hide();","update" => "$update",'url' => "$url",'with' => "$('".$formId."').serialize()"),$confirm_msj,false);
		else $html = $this->Ajax->link($title,$url,array('before'=>"$('ajax_loader_".$UID."').show();", 'complete'=>"$('ajax_loader_".$UID."').hide();","update" => "$update",'url' => "$url"),$confirm_msj,false);
		$html .= "<span id=\"ajax_loader_".$UID."\" style=\"display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;\">";
		$html .= $this->Html->image($imgAjax,$atributosAjax);
		$html .= "</span>";
		return $html;
	}
	
	function ajaxLoader($id='spinner',$msg='PROCESANDO....'){
		$html = '<div id="'.$id.'" style="display: none;color:red;clear:both;width: 100%;height: 20px;background-color: #F5f7f7;border:1px solid #D8DBD4;">';
		$html .= $this->Html->image('controles/ajax-loader.gif').'&nbsp;'.$msg.'</div>';
		return $html;
	}

	
	function linkModalBox($label,$params,$confirm_msj = null){
		
// 		echo $this->Javascript->link('scriptaculous.js?load=effects');
		
		$href = $params['url'];
		$title = Configure::read('APLICACION.nombre')." @ v".Configure::read('APLICACION.version')." :: ";
		$title .= (isset($params['title']) ? $params['title'] : 'Aviso del Sistema');
		$w = (isset($params['w']) ? $params['w'] : 600);
		$h = (isset($params['h']) ? $params['h'] : 600);
		
		$htmlAttributes = array(
			'title' => $title,
			'onclick' => "Modalbox.show(this.href, {title: this.title, width: $w,height: $h}); return false;"
		);
		$modal = $this->Html->link($label,$href,$htmlAttributes,$confirm_msj,false);
		return $this->output($modal);
	}

	function btnModalBox($params,$confirm_msj = null){
//		$img = "controles/search.png";
		$img = "controles/".(isset($params['img']) ? $params['img'] : 'search.png');
		$atributos = array("border"=>"0");
		$title = $this->Html->image($img,$atributos);
		$title .= (isset($params['texto']) ? $params['texto'] : ''); 
		return $this->linkModalBox($title,$params,$confirm_msj);
	}
	
	function openWindow($label='',$controller='',$confirm_msj=null,$img=null){
		$atributos = array("border"=>"0");
		if(!empty($img))$title = $this->Html->image($img,$atributos)." ".$label;
		else $title = $label;		
		$btn = $this->Html->link($title,$controller,array('target' => '_blank'),$confirm_msj,false);
		return $this->output($btn);
	}	


	function btnToggle($domID,$label="",$img= null,$style = null){
		$btn = "";
		if(empty($img)) $img = "controles/arrow_down.png";
		if(empty($style))$style = "cursor:pointer;padding:3px;font-size:11px;border:1px solid #666666;background-color:#D8DBD4;";
		$atributos = array("border"=>"0");
		if(!empty($img))$title = $this->Html->image($img,$atributos)." ".$label;
		else $title = $label;
		$btn = "<div onclick=\"$('".$domID."').toggle();\" style=\"$style\">$title</div>";
		return $btn;
	}
	
	function btnCallJS($funcJS,$label="",$img="controles/script_go.png",$id='btnCallJS'){
		$btn = "";
		$style = "cursor:pointer;padding:3px;font-size:11px;text-decoration:underline;";
		$atributos = array("border"=>"0");
		if(!empty($img))$title = $this->Html->image($img,$atributos)." ".$label;
		else $title = $label;
		$btn = "<div id=\"$id\" onclick=\"$funcJS;\" style=\"$style\">$title</div>";
		return $btn;
	}
	
	function linkAjaxN($txt,$url,$update,$formId = null,$msg = null, $before=null, $complete=null){
		$UID = intval(mt_rand());
		$imgAjax = "controles/ajax-loader.gif";
		$atributosAjax = array("border"=>"0");		
		if(!empty($before)) $before = "$before; $('ajax_loader_$UID').show(); ";
		else $before = "$('ajax_loader_$UID').show();";
		if(!empty($complete)) $complete = "$complete; $('ajax_loader_$UID').hide();";
		else $complete = "$('ajax_loader_$UID').hide();";
		if(!empty($formId))$html = $this->Ajax->link($txt,$url,
		array(
			'before'=> $before, 
			'complete'=> $complete,
			"update" => $update,
			'url' => $url,
			'with' => "$('".$formId."').serialize()"));
		else $html = $this->Ajax->link($txt,$url,
		array(
			'before'=> $before, 
			'complete'=> $complete,
			"update" => $update,
			'url' => $url),
		$msg,false);
		$html .= "<span id=\"ajax_loader_$UID\" style=\"display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;\">";
		$html .= $this->Html->image($imgAjax,$atributosAjax);
		$html .= "</span>";
		return $html;
	}
	
	
	/**
	 * Renderiza un boton OnOff y genera los comandos para ejecutar por ajax una funcion del controller
	 * 
	 * //utilizacion en la vista
	 * echo $controles->btnAjaxToggleOnOff('set/publicar/'.$noticia['Noticia']['id'],$noticia['Noticia']['publicar'],"PUBLICAR?","DEJAR DE PUBLICAR?")
	 * //function del controller
	 * 	function admin_set($field,$id,$option){
	 *		Configure::write('debug',0);
	 *		$noticia = $this->Noticia->read(null, $id);
	 *		$noticia['Noticia'][$field] = $option;
	 *		$this->Noticia->save($noticia);
	 *		echo $option;
	 *		exit;
	 *	}
	 * 
	 * @param $accion funcion a llamar del controller
	 * @param $estado valor actual del campo booleano
	 * @param $confirm_msjOn mensaje cuando esta No activo y se lo va activar
	 * @param $confirm_msjOff mensaje cuando esta Activo y se lo va a desactivar
	 */
	function btnAjaxToggleOnOff($accion,$estado,$confirm_msjOn = null,$confirm_msjOff = null,$srcON="controles/12-em-check.png",$srcOFF = "controles/12-em-cross.png"){

//		$srcON 			= "controles/12-em-check.png";
//		$srcOFF 		= "controles/12-em-cross.png";
		$UID 			= intval(mt_rand());
		$responserAjax 	= Router::url($accion,true);
		
		$imagenON = $this->Html->image($srcON,array("border"=>"0",'style' => 'cursor: pointer;','id' => "ON_$UID",'onclick' => "toggleIcon_$UID(0);"));
		$imagenOFF = $this->Html->image($srcOFF,array("border"=>"0",'style' => 'cursor: pointer;','id' => "OFF_$UID",'onclick' => "toggleIcon_$UID(1);"));
		$ajaxLoader = "controles/ajax-loader.gif";
		$ajaxScript = 	"<script type=\"text/javascript\">
							$(\"ajax_loader_$UID\").hide();
							$(\"responser$UID\").hide();
							
							var valorActual_$UID = $estado;
							//alert(valorActual_$UID);
							Event.observe(window, 'load', function(){
								if(valorActual_$UID == 0){
									$(\"ON_$UID\").hide();
									$(\"OFF_$UID\").show();	
								}else{
									$(\"ON_$UID\").show();
									$(\"OFF_$UID\").hide();	
								}
							});
							function toggleIcon_$UID(opt){
								if(opt == 0){
									".(!empty($confirm_msjOff) ? "if(!confirm(\"$confirm_msjOff\")) return;" : "")."
								}else{
									".(!empty($confirm_msjOn) ? "if(!confirm(\"$confirm_msjOn\")) return;" : "")."
								}
								var responseValue;
								var url = '$responserAjax/' + opt;
								new Ajax.Updater('responser$UID',
										url, 
										{
											asynchronous:true, 
											evalScripts:true, 
											onComplete:function(request, json) {
												$(\"ajax_loader_$UID\").hide();
												responseValue = request.responseText;
												//alert(responseValue);
												if(responseValue == 0){
													$(\"ON_$UID\").hide();
													$(\"OFF_$UID\").show();
												}else{
													$(\"ON_$UID\").show();
													$(\"OFF_$UID\").hide();
												}
											},
											onLoading:function(request) {
												$(\"ON_$UID\").hide();
												$(\"OFF_$UID\").hide();
												$(\"ajax_loader_$UID\").show();
											}, 
											requestHeaders:['X-Update', 'responser$UID']
										}
								);								
							}
						</script>";
		$btn = $imagenON . $imagenOFF;
		$btn .= $this->Html->image($ajaxLoader,array("border"=>"0",'id' => "ajax_loader_$UID", 'style' => "display: none;"));
		$btn .= "<div id=\"responser$UID\"></div>";
		$btn .= $ajaxScript;
		return $this->output($btn);
	}	
	
	
	function ordenDescuentoPopPup($ordenDescuentoId,$socioId,$etiqueta=null){
		return $this->linkModalBox((empty($etiqueta) ? $ordenDescuentoId : $etiqueta),array('title' => 'ORDEN DE DESCUENTO #' . $ordenDescuentoId,'url' => '/mutual/orden_descuentos/view/'.$ordenDescuentoId.'/'.$socioId,'h' => 450, 'w' => 750));
	}
	
	function ordenDescuentoCuotaPopPup($ordenDescuentoCuotaId,$etiqueta=null){
		return $this->linkModalBox((empty($etiqueta) ? $ordenDescuentoCuotaId : $etiqueta),array('title' => 'DETALLE CUOTA #' . $etiqueta,'url' => '/mutual/orden_descuento_cuotas/view/'.$ordenDescuentoCuotaId,'h' => 450, 'w' => 750));
	}	
	
	
}
?>