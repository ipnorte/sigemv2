<div class='app_name'>
	<span style="font-size: 14px;"><?php echo Configure::read('EMPRESA.razon_social')?></span>
	<strong><?php echo Configure::read('APLICACION.nombre')?></strong>
	<span style="font-size: 14px;">@v<?php echo Configure::read('APLICACION.version')?></span>
	<br>
	<span style="font-size: 14px;"><?php echo $util->hoy(true,true)?></span>
	<span style="font-size: 14px;"><?php echo Configure::read('EMPRESA.razon_social')?></span>
	<span style="font-size: 14px;"><?php echo (Configure::read('debug') != 0 ? 'MODO DEBUG' : '')?></span>
</div>

<table id="user_options">
	<tr>
		<td><?php echo $html->image('maquetado/DatosUser_r1_c1.gif')?></td>
		<td class="data">
			<div style="text-align: right;padding-top: 10px;">
				<?php echo $this->requestAction('/seguridad/usuarios/quick_menu')?>
			</div>		
		</td>
		<td><?php echo $html->image('maquetado/DatosUser_r1_c3.gif')?></td>
	</tr>

</table>
<div style='clear:both;'></div>