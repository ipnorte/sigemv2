<?php 

$UID = intval(mt_rand(100,999));

$noCobrados = (isset($noCobrados) ? $noCobrados : true);
$noCobrados = ($noCobrados ? 1 : 0);
$intercambioId = (isset($intercambioId) ? $intercambioId : 0);
$model = (isset($model) ? $model : "LiquidacionSocioRendicion.resumen_empresas");
$empty = (isset($empty) ? $empty : false);
$selected = (isset($selected) ? $selected : null);
$type = (isset($type) ? $type : 'combo');
$method = (isset($method) ? $method : 'get_resumen_turnos_by_archivo_list');

if(isset($model))$name = explode(".", $model);


$datos = $this->requestAction("/mutual/liquidacion_socio_rendiciones/$method/$intercambioId/$noCobrados");

// if($type == 'combo' && !empty($datos)){
// 	$values = array();	
// 	foreach($datos as $dato):
// 		$values[$dato['LiquidacionSocioRendicion']['key_select']] = $dato['LiquidacionSocioRendicion']['key_select_label'];
// 	endforeach;
// 	echo $frm->input($model,array('type' => 'select', 'options' => $values, 'selected' => $selected));
// }
?>


<?php if($type == 'combo' && !empty($datos)):?>
	<div class="input select">
		<label for="LiquidacionSocioFiltroEmpresa"></label>
		<select name="data[<?php echo $name[0]?>][<?php echo $name[1]?>]" id="<?php echo Inflector::camelize(str_replace(".","", $model))?>">
			<?php if($empty):?>
				<option value=""></option>
			<?php endif;?>
			<?php foreach($datos as $dato):?>
				<option value="<?php echo $dato['LiquidacionSocioRendicion']['key_select']?>" <?php echo ($dato['LiquidacionSocioRendicion']['key_select'] == $selected ? "selected = 'selected'" : "")?>><?php echo $dato['LiquidacionSocioRendicion']['key_select_label']?></option>
			<?php endforeach;?>
		</select>
	</div>

<?php endif;?>

<?php if($type == 'grilla_ckeck' && !empty($datos)):?>

	<?php //   debug($datos);?>

	<script language="Javascript" type="text/javascript">
	var rows_<?php echo $UID?> = <?php echo count($datos)?>;
	var checkAll_<?php echo $UID?> = false;
	function checkUnCheckAll_<?php echo $UID?>(rows){
		if(checkAll_<?php echo $UID?>)checkAll_<?php echo $UID?> = false;
		else checkAll_<?php echo $UID?> = true;
		for (i = 0; i < rows_<?php echo $UID?>; i++){
			objCHK_<?php echo $UID?> = document.getElementById('<?php echo Inflector::camelize(str_replace(".","", $model))?>_' + i);
			if(!objCHK_<?php echo $UID?>.disabled){
				if(checkAll_<?php echo $UID?>)objCHK_<?php echo $UID?>.checked = true;
				else objCHK_<?php echo $UID?>.checked = false;
			}
		}
		chkOnclick_<?php echo $UID?>();
	}

	function chkOnclick_<?php echo $UID?>(){
		for (i = 0; i < rows_<?php echo $UID?>; i++){
			objCHK_<?php echo $UID?> = document.getElementById('<?php echo Inflector::camelize(str_replace(".","", $model))?>_' + i);
			toggleCell("tr" + i,objCHK_<?php echo $UID?>);
		}
	}			

	</script>

	<table class="tbl_grilla">
		<tr>
			<th><?php echo $controles->btnCallJS("checkUnCheckAll_$UID(".count($datos).")","","controles/12-em-check.png")?></th>
			<th>EMPRESA</th>
			<th>TURNO</th>
			<th>IMPORTE</th>
			<th>MOTIVO</th>
		</tr>
		<?php $i = 0;?>
		<?php foreach($datos as $dato):?>
			<tr id="tr<?php echo $i?>">
				<td><input type="checkbox" name="data[<?php echo $name[0]?>][<?php echo $name[1]?>][<?php echo $dato['LiquidacionSocioRendicion']['key_select']?>]" value="<?php echo $dato['LiquidacionSocioRendicion']['key_select']?>" id="<?php echo Inflector::camelize(str_replace(".","", $model))?>_<?php echo $i?>" <?php echo (in_array($dato['LiquidacionSocioRendicion']['key_select'], $selected) ? "checked" : "")?> onclick="chkOnclick_<?php echo $UID?>()"/></td>
				<td><?php echo $dato['LiquidacionSocioRendicion']['codigo_empresa_desc']?></td>
				<td><?php echo $dato['LiquidacionSocioRendicion']['turno_pago_desc']?></td>
				<td style="text-align: right;"><?php echo $util->nf($dato['LiquidacionSocioRendicion']['importe'])?></td>
				<td><?php echo $dato['LiquidacionSocioRendicion']['codigo_rendicion_desc']?></td>
			</tr>
			<?php $i++;?>
		<?php endforeach;?>
	</table>


<?php endif;?>
