<?php

App::import(array('Form','Html'));

/**
 * 
 * @author ADRIAN TORRES
 * @package general
 */


class FrmHelper extends FormHelper{
	
	var $txtBtnGuardar = "GUARDAR";
	var $txtBtnCancelar = "CANCELAR";
	

	/**
	 * btnGuardarCancelar
	 * Funcion que renderiza el boton guardar y cancelar, por parametro se pasa la URL donde redirige el boton cancelar
	 * 
	 * @param $optCancel
	 * @return unknown_type
	 */
	
	function btnGuardarCancelar($optCancel){
		$str = "";
		
		$RAIZ =  Configure::read('APLICACION.folder_install');
		$URL = $this->base . $optCancel['URL'];
		$this->txtBtnGuardar = (!empty($optCancel['TXT_GUARDAR']) ? $optCancel['TXT_GUARDAR'] : $this->txtBtnGuardar);
		$this->txtBtnCancelar = (!empty($optCancel['TXT_CANCELAR']) ? $optCancel['TXT_CANCELAR'] : $this->txtBtnCancelar);
                
                $IDBTN_GUARDAR = (isset($optCancel['ID_GUARDAR']) ? $optCancel['ID_GUARDAR'] : 'btn_submit');
                $IDBTN_CANCELAR = (isset($optCancel['ID_CANCELAR']) ? $optCancel['ID_CANCELAR'] : 'btn_cancel');
                
                $confirm = NULL;
                if(isset($optCancel['CONFIRM'])){
                    $msg = (!empty($optCancel['CONFIRM']) ? $optCancel['CONFIRM'] : 'Continuar?');
                    $confirm = "onclick=\"return confirm('".$msg."')\"";
                }
		
		$str = "<div class=\"submit\">";
//		$str .= "	<input type=\"button\" value=\"".$this->txtBtnCancelar."\" onclick=\"javascript:window.location='".$URL."';\" class=\"btn_cancelar\"/>";
		$str .= "	<input type=\"button\" value=\"".$this->txtBtnCancelar."\" id=\"$IDBTN_CANCELAR\" onclick=\"javascript:window.location='".$URL."';\"/>";
		$str .= "&nbsp;&nbsp;";
//		$str .= "	<input type=\"submit\" value=\"".$this->txtBtnGuardar."\" id=\"btn_submit\" class=\"btn_guardar\"/>";
		$str .= "	<input type=\"submit\" value=\"".$this->txtBtnGuardar."\" id=\"$IDBTN_GUARDAR\" $confirm />";
		$str .= "</div>";
		$str .= "</form>";			
		
		return $str;
		
	}
	
	function btnForm($opts){
		$str = "";
		$URL = $this->base . $opts['URL'];
		$txt = (isset($opts['LABEL']) ? $opts['LABEL'] : 'Click Aqui');
		$name = (isset($opts['NAME']) ? $opts['NAME'] : 'btn_'.rand(0,999));
		$str = "<div class=\"submit\">";
		$str .= "	<input type=\"button\" name=\"".$name."\" value=\"".$txt."\" onclick=\"this.disabled = true;javascript:window.location='".$URL."';\"/>";
		$str .= "</div>";	
		
		return $str;
	}
	
	function number($id,$options=array()){
		if(!key_exists('size',$options))$options['size'] = 5;
		if(!key_exists('maxlength',$options))$options['maxlength'] = 5;
		if(!key_exists('class',$options))$options['class'] = 'input_number';
		$options['onkeypress'] =  'return soloNumeros(event,false,false)';
		return $this->input($id,$options);
	}	
	
	/**
	 * genera un campo text con formato moneda
	 * @return unknown_type
	 */
	function money($id,$label='',$default='',$negativos=false,$size = 12,$maxlength = 12){
//		$ctrlFloat = "var key = ((window.Event ? false : true) ? event.which : event.keyCode);return (key <= 13 || (key >= 48 && key <= 57) || key == 46 || key == 45);";
//		return $this->input($id,array('label' => $label,'size' => 12,'maxlength' => 12,'class' =>'input_number', 'onkeypress' => $ctrlFloat));
		return $this->input($id,array('label' => $label,'value' => $default,'size' => $size,'maxlength' => $maxlength,'class' =>'input_number', 'onkeypress' => "return soloNumeros(event,true,".($negativos ? "true" : "false").")"));	
	}
	

	
	function calendar($fieldName,$label='',$dateSelected = null,$minYear = null,$maxYear = null,$disable = ''){
		
		$minYear = (!empty($minYear) ? $minYear : date('Y'));
		$maxYear = (!empty($maxYear) ? $maxYear : date('Y'));
		
		$d = date('d');
		$m = date('m');
		$y = date('Y');
		
		if(!empty($dateSelected)){
			$d = date('d',strtotime($dateSelected));
			$m = date('m',strtotime($dateSelected));
			$y = date('Y',strtotime($dateSelected));
		}
		
		$dia = $this->day($fieldName,$d,array('disabled'=> ($disable ? 'disabled' : '')),false);
		$mes = $this->month($fieldName,$m,array('disabled'=> ($disable ? 'disabled' : '')),false);
		$anio = $this->year($fieldName,$minYear,$maxYear,$y,array('disabled'=> ($disable ? 'disabled' : '')),false);
		$calendar = "<div class=\"input date\"><label for=''>$label</label>".$dia .' - '. $mes .' - '. $anio."</div>";
		return $calendar;
	}
	

	function periodo($fieldName,$label='',$selected = null,$minYear = null,$maxYear = null,$disable = ''){
		$minYear = (!empty($minYear) ? $minYear : date('Y'));
		$maxYear = (!empty($maxYear) ? $maxYear : date('Y'));
		
		$d = date('d');
		$m = date('m');
		$y = date('Y');
		$dateSelected = date('Y-m-d');
		if(!empty($selected)){
			$d = "01";
			$m = substr($selected,4,2);
			$y = substr($selected,0,4);
                        $dateSelected = date('Y-m-d', strtotime($y."-".$m."-01"));
		}
//		debug($m);
		$mes = $this->month($fieldName,$dateSelected,array('disabled'=> ($disable ? 'disabled' : '')),false);
		$anio = $this->year($fieldName,$minYear,$maxYear,$dateSelected,array('disabled'=> ($disable ? 'disabled' : '')),false);
		if($label!='')$periodo = "<div class=\"input date\"><label for=''>$label</label>".$mes . $anio."</div>";
		else $periodo = "<div class=\"input date\">".$mes . $anio."</div>";
		return $periodo;
		
	}
	
	
	function meses($fieldName,$label='',$selected = null,$disable = ''){
		
		$d = date('d');
		
		if(!empty($selected)){
			$m = $selected;
		}
		
		$mes = $this->month($fieldName,$m,array('disabled'=> ($disable ? 'disabled' : '')),false);
		$meses = "<div class=\"input date\"><label for=''>$label</label>".$mes."</div>";
		return $meses;
		
	}

	
	function checkAll($rows,$id_prefix,$rowColor=false){
		
		$uid = intval(rand());
		
		$lblChk = "Todos";
		$lblUnChk = "Ninguno";
		$cssCellSelected = 'selected';
		
		$fName = "checkAll$uid";
		$fNameToggleCells = "ToggleCell$uid";
		
		$script = "";
		$script .= "<script language=\"Javascript\" type=\"text/javascript\">";
		$script .= "	var rows = $rows;";
		$script .= "	function $fName(check){";
		$script .= "		for (i=1;i<=rows;i++){";
		$script .= "			idChk = '$id_prefix' +  i;";
		$script .= "			$fNameToggleCells('TRL_' + i,check);";
		$script .= "			oChkCheck = document.getElementById(idChk);";
		$script .= "			oChkCheck.checked = check;";
		$script .= "		}";
		$script .= "	}";
		$script .= "	function $fNameToggleCells(idRw,OnOff){";
		$script .= "		var celdas = $(idRw).immediateDescendants();";
		$script .= "		if(OnOff)celdas.each(function(i){i.addClassName('$cssCellSelected')});";
		$script .= "		else celdas.each(function(i){i.removeClassName('$cssCellSelected')});";
		$script .= "	}";
		$script .= "</script>";
		$script .= "<a href=\"javascript:$fName(true)\">$lblChk</a>|<a href=\"javascript:$fName(false)\">$lblUnChk</a>";
		return $script;		
	}
	
	/**
	 * sobrecarga del metodo input para evitar que ponga por default el nombre del campo como label salvo que se especifique
	 */
	function input($fieldName,$options=null){
		$options['label'] = (isset($options['label']) ? $options['label'] : "");
		return parent::input($fieldName,$options);	
	}

	function inputFecha($fieldName,$options=null){
		parent::setEntity($fieldName);
		$model =& ClassRegistry::getObject(parent::model());
		$field = parent::field();
		$type = $model->getColumnType($field);
		if(!empty($model->data[parent::model()][$field]))$fecha = date('d/m/Y',strtotime($model->data[parent::model()][$field]));
		else $fecha = null;
		$options['value'] = $fecha;
		$options['label'] = (isset($options['label']) ? $options['label'] : "");
		return parent::input($fieldName,$options);	
	}
	
	
	function submit($caption = null, $options = array()){
//		$options['class'] = (isset($options['class']) ? $options['class'] : "btn_guardar");
		return parent::submit($caption,$options);
	}
	
	/**
	 * genera una imagen con un js para restablecer el formulario, si el segundo parametro es
	 * true hace un submit del formulario
	 * @param $formID
	 * @param $submit
	 */
	function reset($formID,$submit=false){
		$script = "";
		$script .= "<script language=\"Javascript\" type=\"text/javascript\">";
		$script .= "	function resetForm(){";
		$script .= "		var elementosForm = $('$formID').getInputs('text');";
		$script .= "		elementosForm.each(function(e){";
		$script .= "			e.clear();";
		$script .= "		});";
		if($submit){
			$script .= "	$('$formID').submit();";
		}
		$script .= "	}";
		$script .= "</script>";
		$atributos = array("border"=>"0","style"=>"cursor:pointer;padding:3px;");
		$btn = "<div onclick='resetForm()'>".$this->Html->image("controles/reload3.png",$atributos)."</div>".$script;
		return $btn;
	}
	
	/**
	 * Combo con los tipos de reportes
	 * @return unknown_type
	 */
	function tipoReporte($selected='PDF'){
		return $this->input('tipo_reporte',array('type' => 'select','options' => array('PDF' => 'PDF','XLS' => 'XLS'),'label'=>'','selected' => $selected));
	}
	
	/**
	 * Combo para seleccionar SI o NO
	 * @return unknown_type
	 */
	function cmbSiNo($campo, $selected='NO'){
		return $this->input($campo,array('type' => 'select','options' => array(0 => 'NO',1 => 'SI'),'label'=>'','selected' => $selected));
	}

	/**
	 * Boton para seleccionar SI o NO
	 * @return unknown_type
	 */
	function btnSiNo($name, $txt='No'){
		list($model,$field) = explode('.',$name);
		if(empty($txt)) $txt = 'No';
		$str = "<input type=\"button\" name=\"data[$model][$field]\" value=\"".$txt."\" onclick=\"this.value=(this.value=='No' ? 'Si' : 'No'); ; return true\"/>&nbsp;Presionando sobre el boton puede alternar entre SI y NO";
		
		return $str;
	}
	/**
	 * Combo con los tipos de cuentas
	 * @return unknown_type
	 */
	function tipoCuenta($selected='AC'){
		return $this->input('tipo_cuenta',array('type' => 'select','options' => array('AC' => 'ACTIVO','PA' => 'PASIVO','PN' => 'PATRIMONIO NETO','RP' => 'RESULTADO POSITIVO','RN' => 'RESULTADO NEGATIVO'),'label'=>'','selected' => $selected));
	}
	
	/**
	 * Combo con los tipos de cuentas
	 * @return unknown_type
	 */
	function comboDebeHaber($selected='D'){
		return $this->input('Asiento.tipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'','selected' => $selected));
	}
	
	/**
	 * Combo con los tipos de cuentas
	 * @return unknown_type
	 */
	function comboTipoDocumento($selected=''){
		return $this->input('Asiento.tipo_documento',array('type' => 'select','options' => array('' => '', 'CH' => 'CHEQUE','RE' => 'RECIBO', 'OP' => 'ORDEN PAGO'),'label'=>'TIPO DOCUMENTO:','selected' => $selected));
	}
	
	/**
	 * Combo con los tipos de cuentas
	 * @return unknown_type
	 */
	function comboTipoProveedor($selected=''){
		return $this->input('Proveedor.tipo_proveedor',array('type' => 'select','options' => array('' => 'Seleccionar', '0' => 'PROVEEDORES','1' => 'COMERCIO', '2' => 'PRODUCTORES'),'selected' => $selected));
	}
	
	
	function comboDias($model,$selected = null,$disable = false){
		$aDias = array();
		for ($i = 1; $i <= 31; $i++) {
			$aDias[$i] = $i;
		}
		$selected = (empty($selected) ? date('d') : $selected);
		return $this->input($model,array('type' => 'select','options' => $aDias, 'selected' => $selected, 'disabled' => $disable));
	}
        
        	function btnGuardar($optCancel){
		$str = "";
		
		$RAIZ =  Configure::read('APLICACION.folder_install');
		$URL = $this->base . $optCancel['URL'];
		$this->txtBtnGuardar = (!empty($optCancel['TXT_GUARDAR']) ? $optCancel['TXT_GUARDAR'] : $this->txtBtnGuardar);
		//$this->txtBtnCancelar = (!empty($optCancel['TXT_CANCELAR']) ? $optCancel['TXT_CANCELAR'] : $this->txtBtnCancelar);
                
                $confirm = NULL;
                if(isset($optCancel['CONFIRM'])){
                    $msg = (!empty($optCancel['CONFIRM']) ? $optCancel['CONFIRM'] : 'Continuar?');
                    $confirm = "onclick=\"return confirm('".$msg."')\"";
                }
		
		$str = "<div class=\"submit\">";
//		$str .= "	<input type=\"button\" value=\"".$this->txtBtnCancelar."\" onclick=\"javascript:window.location='".$URL."';\" class=\"btn_cancelar\"/>";
		//$str .= "	<input type=\"button\" value=\"".$this->txtBtnCancelar."\" id=\"btn_cancel\" onclick=\"javascript:window.location='".$URL."';\"/>";
		//$str .= "&nbsp;&nbsp;";
//		$str .= "	<input type=\"submit\" value=\"".$this->txtBtnGuardar."\" id=\"btn_submit\" class=\"btn_guardar\"/>";
		$str .= "	<input type=\"submit\" value=\"".$this->txtBtnGuardar."\" id=\"btn_submit\" $confirm />";
		$str .= "</div>";
		$str .= "</form>";			
		
		return $str;
		
	}
	
}
?>