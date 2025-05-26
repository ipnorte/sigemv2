<?php 

$UID = intval(mt_rand(100,999));

$datos = $this->requestAction('/config/global_datos/' . $metodo);
$name = array('GlobalDato','id');
if(isset($model))$name = explode(".", $model);

if(!isset($selected) || $selected == "") $selected = array();
if(!isset($header)) $header = true;


?>

<?php if(!empty($datos)):?>
	<script language="Javascript" type="text/javascript">
	var rows_<?php echo $UID?> = <?php echo count($datos)?>;
	var checkAll_<?php echo $UID?> = false;
	function checkUnCheckAll_<?php echo $UID?>(rows_<?php echo $UID?>){
		if(checkAll_<?php echo $UID?>)checkAll_<?php echo $UID?> = false;
		else checkAll_<?php echo $UID?> = true;
		for (i = 0; i < rows_<?php echo $UID?>; i++){
			objCHK_<?php echo $UID?> = document.getElementById('<?php echo $UID?>_' + i);
			if(!objCHK_<?php echo $UID?>.disabled){
				if(checkAll_<?php echo $UID?>)objCHK_<?php echo $UID?>.checked = true;
				else objCHK_<?php echo $UID?>.checked = false;
			}
		}
		chkOnclick_<?php echo $UID?>();
	}

	function chkOnclick_<?php echo $UID?>(){
		for (i = 0; i < rows_<?php echo $UID?>; i++){
			objCHK_<?php echo $UID?> = document.getElementById('<?php echo $UID?>_' + i);
			toggleCell("tr" + i,objCHK_<?php echo $UID?>);
		}
	}			

	</script>
	<table class="tbl_form">
		<?php if($header):?>
		<tr><th><?php echo $controles->btnCallJS("checkUnCheckAll_$UID(".count($datos).")","","controles/12-em-check.png")?></th><th>CONCEPTO</th></tr>
		<?php endif;?>
		<?php $i = 0;?>
		<?php foreach ($datos as $key => $concepto):?>
			<tr id="tr<?php echo $i?>">
				<td><input type="checkbox" name="data[<?php echo $name[0]?>][<?php echo $name[1]?>][<?php echo $key?>]" value="<?php echo $key?>" id="<?php echo $UID?>_<?php echo $i?>" <?php echo (in_array($key, $selected) ? "checked" : "")?> onclick="chkOnclick_<?php echo $UID?>()"/></td>
				<td><?php echo $concepto?></td>
			</tr>
			<?php $i++;?>
		<?php endforeach;?>
	</table>
<?php endif;?>

<?php // debug($datos)?>