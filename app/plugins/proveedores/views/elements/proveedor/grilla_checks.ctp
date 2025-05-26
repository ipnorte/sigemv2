<?php 
$UID = intval(mt_rand(100,999));
$soloActivos = (!isset($soloActivos) ? TRUE : $soloActivos);
$tipoProveedor = (!isset($tipoProveedor) ? 1 : $tipoProveedor);

$proveedores = $this->requestAction('/proveedores/proveedores/proveedores/'.$soloActivos.'/'.$tipoProveedor);
?>
<?php // echo $frm->checkAll(count($proveedores),$modelo."ProveedorId_")?>

<?php if(!empty($proveedores)):?>
	<script language="Javascript" type="text/javascript">
	var rows_<?php echo $UID?> = <?php echo count($proveedores)?>;
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

                <tr>

                        <th><?php echo $controles->btnCallJS("checkUnCheckAll_$UID(".count($proveedores).")","","controles/12-em-check.png")?></th>
                        <th>RAZON SOCIAL</th>

                </tr>
                <?php $i = 0;?>
                <?php foreach($proveedores as $proveedor):?>

                    <tr id="tr<?php echo $i?>">
                            <td><input type="checkbox" name="data[Proveedor][proveedor_id][<?php echo $proveedor['Proveedor']['id']?>]" value="<?php echo $proveedor['Proveedor']['id']?>" id="<?php echo $UID?>_<?php echo $i?>" <?php echo (in_array($proveedor['Proveedor']['id'], $selected) ? "checked" : "")?> onclick="chkOnclick_<?php echo $UID?>()"/></td>
                            <td><?php echo $proveedor['Proveedor']['razon_social']?></td>
                    </tr>
                    <?php $i++;?>    
                <?php endforeach;?>

        </table>
<?php endif;?>        
<?php // debug($proveedores)?>