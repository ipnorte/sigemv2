
<div class="areaDatoForm2">

	<?php if($check != 0){?>
		<strong><?php echo $banco?></strong>
		<br/>
		SUCURSAL: <strong><?php echo $sucursal?></strong>
		&nbsp;
		CUENTA: <strong><?php echo $nro_cta_bco?></strong>
	<?php }else{?>
		<span style="color:red;"><strong>ATENCION: </strong>EL CBU NO CORRESPONDE A UN BANCO HABILITADO</span>
	<?php }?>
</div>
<div style="clear: both;"></div>