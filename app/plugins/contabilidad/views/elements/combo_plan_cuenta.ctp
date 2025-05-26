<?php 
//$plan_cuenta = $this->requestAction('/contabilidad/plan_cuentas/comboPlanCuenta');
//echo $frm->input($model,array('type'=>'select','options'=>$plan_cuenta,'empty'=>($empty == 0 ? false : true),'selected' => (isset($selected) ? $selected : 0),'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));
$plan_cuenta = $this->requestAction('/contabilidad/plan_cuentas/comboPlanCuentaCompleto');
$model = (isset($model) ? $model : "PlanCuenta.id");
$aModel = explode(".", $model);
$seleccionar_todas = (isset($seleccionar_todas) && $seleccionar_todas ? true : false); 
$empty = (isset($empty) ? ($empty == 1 ? true : false) : false);
?>
<select name="data[<?php echo $aModel[0]?>][<?php echo $aModel[1]?>]"  id="<?php echo $aModel[0].Inflector::camelize($aModel[1])?>" style="background: white;">
<?php $fix = (isset($selected) ? $selected : 0);?>
<?php if($empty):?>
	<option value="0"></option>
<?php endif;?>

<?php foreach($plan_cuenta as $id => $value):?>
	<?php 
		$style="";
		$optDisable = "";
		if(!empty($value)){
			if($value['imputable'] == 0){
				$style="style='background-color:#666666;color:white;'";
				if(!$seleccionar_todas) $optDisable = "disabled='disabled'";
			}else{
				$optDisable = "";
				$style="style='color:#666666;'";
			}
		}
	?>
	
	<?php if(!empty($value)):?>
		<option value="<?php echo $id?>" <?php echo $style?> <?php echo $optDisable?> <?php echo ($fix == $id ? "selected='selected'" : "")?>><?php echo $value['cuenta']?></option>
	<?php endif;?>
<?php endforeach;?>
</select>