<?php 
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 */

class CssMenuHelper extends Helper{

	var $helpers = array('Html');
	
	function menuPrincipal($data=array(), $type='right'){
		global $cm_css_inc;
		$out ='';
		if($cm_css_inc != true){
			$cm_css_inc = true;
			$out .= $this->Html->css('css_menu');
		}
		return $this->output($out . $this->_cm_render_menu_principal_down($data));
	}

	
//	function menu($data=array(), $type='right'){
//		global $cm_css_inc;
//		$out ='';
//		if($cm_css_inc != true){
//			$cm_css_inc = true;
//			$out .= $this->Html->css('css_menu');
//		}
//		debug($data);
//		return $this->output($out . $this->_cm_render($data));
//	}	
	
	function _cm_render_menu_principal_down($data=array()){
		
		$out='';
		
		if($data == array()) return;
		
		if(is_array($data)){
			
			$out .= "<ul class=\"css_menu cm_down\">\n";
			
			foreach($data as $key => $item){
				
				if(is_array($item)){

					$out .= '<li class="parent">'. $this->Html->image('menu/exec.png').'&nbsp;'.$key. "\n";
					$out .= $this->_cm_render_menu_principal_down($item);
					$out .= "</li>\n";
					
				}else{
					
					$out .= '<li><a href="'. $item. '">'. $this->Html->image('menu/arrow_right2.gif').'&nbsp;' . $key. '</a></li>'. "\n";
				}
				
			}
			$out .=  "</ul>\n";
		}
		return $out;		
	}
	
	
	function menuTabs($params,$principal=true){
		if($principal){
			$styleUL = "list-style-type: none;border-left: 1px solid #666666;border-bottom:1px solid #666666;height: 36px;margin-bottom: 5px;";
			$styleLI = "float: left;background-color: #e2e6ea;padding: 7px 3px 3px 3px;height: 25px;border-top:1px solid #666666;border-right:1px solid #666666;";
		}else{
			$styleUL = "list-style-type: none;border-left: 1px solid #D8DBD9;border-bottom:1px solid #D8DBD9;height: 36px;;margin-bottom: 5px;";
			$styleLI = "float: left;background-color: #F5F7F7;padding: 7px 3px 3px 3px;height: 25px;border-top:1px solid #D8DBD9;border-right:1px solid #D8DBD9;font-size: 10px";
		}
		
		$tab = "<ul style='".$styleUL."'>";
		foreach($params as $parametro){
			if(!empty($parametro)) $tab .= "<li style='".$styleLI."'>".$this->boton($parametro['url'],$parametro['icon'],$parametro['label'],$parametro['atributos'],$parametro['confirm'])."</li>\n";
		}
		$tab .= "</ul>";
		$tab .= "<div style=\"clear:both;width:100%;\"></div>";
		return $tab;
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
	function boton($controller,$img ,$texto='' ,$atributos=null, $confirm_msj=null){
		$title = $this->Html->image($img,array("border"=>"0"))." ".$texto;
		$btn = $this->Html->link($title,$controller,$atributos,$confirm_msj,false);
		return $this->output($btn);
	}	
	
	/* render a menu. 
	 * This is a helper for recursion.  The arguments are the 
	 * same as for $this->menu().
	 */
	function _cm_render($data=array(), $type='right'){
		$out='';
		if($data == array()) return;
		if(is_array($data)){
			$out .= "<ul class=\"css_menu cm_$type\">\n";
			foreach($data as $key => $item){
				if(is_array($item)){
					$out .= '<li class="parent">'. $this->Html->image('menu/exec.png').'&nbsp;'.$key. "\n";
					$out .= $this->_cm_render($item, $type);
					$out .= "</li>\n";
				}else{
					$out .= '<li><a href="'. $item. '" style="color:#000000;">'. $this->Html->image('menu/arrow_right2.gif').'&nbsp;' . $key. '</a></li>'. "\n";
				}
			}
			$out .=  "</ul>\n";
		}
		return $out;
	}
}
?>