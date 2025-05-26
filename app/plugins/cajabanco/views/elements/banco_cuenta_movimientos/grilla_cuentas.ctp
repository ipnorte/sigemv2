<?php 
if(!isset($accion))$accion = '/cajabanco/banco_cuenta_movimientos/resumen/';
?>

<?php if(!empty($cuentas)):?>

<?php echo $this->renderElement('paginado')?>

<script type="text/javascript">

function toggleCellMouseOver(idRw, status){
	var celdas = $(idRw).immediateDescendants();
	if(status)celdas.each(function(td){td.addClassName("selected2");});
	else celdas.each(function(td){td.removeClassName("selected2");});
}

</script>

<table cellpadding="0" cellspacing="0">

	<tr>
		<th></th>
		<th><?php echo $paginator->sort('BANCO','Banco.nombre');?></th>
		<th>DENOMINACION</th>
		<th>NRO CUENTA</th>
		<th><?php echo $paginator->sort('ESTADO','BancoCuenta.activo');?></th>
		<th><?php echo $paginator->sort('CHEQUERAS','BancoCuenta.chequeras');?></th>
		<th>FECHA APERTURA</th>
		<th>CUENTA CONTABLE</th>
	</tr>


	<?php
		$i = 0; 
		foreach($cuentas as $cuenta):
			$i++;
			$click = "onclick = \"window.location.href = '".$html->url($accion.$cuenta['BancoCuenta']['id'],true)."'\" style=\"cursor: pointer;\"";
	?>
		
			<tr <?php echo ($cuenta['BancoCuenta']['banco_id'] == 99999 ? ' class="altrow"' : "")?>id="LTR_<?php echo $i?>" onmouseover="toggleCellMouseOver('LTR_<?php echo $i?>',true)" onmouseout="toggleCellMouseOver('LTR_<?php echo $i?>',false)">
				<td <?php echo $click?>>#<?php echo $cuenta['BancoCuenta']['id']?></td>
				<td <?php echo $click?>><strong><?php echo $cuenta['BancoCuenta']['banco']?></strong></td>
				<td <?php echo $click?>><?php echo $cuenta['BancoCuenta']['denominacion']?></td>
				<td <?php echo $click?>><strong><?php echo $cuenta['BancoCuenta']['numero']?></strong></td>
				<td align="center" <?php echo $click?>><?php echo $controles->onOff($cuenta['BancoCuenta']['activo'])?></td>
				<td align="center" <?php echo $click?>><?php echo $controles->onOff($cuenta['BancoCuenta']['chequeras'],true)?></td>
				<td align="center" <?php echo $click?>><?php echo $util->armaFecha($cuenta['BancoCuenta']['fecha_apertura'])?></td>
				<td <?php echo $click?>><?php echo $cuenta['BancoCuenta']['cuenta_contable']?></td>
			</tr>
			
		<?php endforeach;?>

</table>



<?php echo $this->renderElement('paginado')?>
<?php endif;?>