<?php echo $this->renderElement('head',array('title' => 'SOLICITUDES DE CREDITO :: REASIGNAR PROVEEDOR :: CONFIGURACION','plugin' => 'config'))?>

<?php 
$tabs = array(
				0 => array('url' => '/v1/solicitudes/reasignar_proveedor','label' => 'REASIGNAR SOLICITUD', 'icon' => 'controles/pin.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/v1/solicitudes/reasignar_proveedor_config','label' => 'CONFIGURACION', 'icon' => 'controles/16-security-key.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);			
?>

<h3>CONFIGURACION VIGENTE</h3>


<?php if(!empty($configuraciones)):?>

	<table>
		<tr>
			<th><?php echo $controles->botonGenerico('/v1/solicitudes/reasignar_proveedor_config/DELETEALL','controles/user-trash-full.png',null,null,"QUITAR TODAS LAS OPCIONES DE REASIGNACION ?")?></th>
			<th>COD.</th>
			<th>ASIGNACION</th>
			<th>USUARIOS ACTIVOS HABILITADOS</th>
			<th>ACTUALIZO</th>
		</tr>
		<?php foreach($configuraciones as $configuracion):?>
			<tr>
				<td><?php echo $controles->botonGenerico('/v1/solicitudes/reasignar_proveedor_config/'.$configuracion['GlobalDato']['id'],'controles/user-trash-full.png',null,null,"QUITAR LA OPCION DE REASIGNACION ".$configuracion['GlobalDato']['concepto_1']."?")?></td>
				<td><?php echo $configuracion['GlobalDato']['concepto_2']?></td>
				<td><strong><?php echo $configuracion['GlobalDato']['concepto_1']?></strong></td>
				<td style="color: green;"><?php echo implode(", ", unserialize($configuracion['GlobalDato']['texto_1']))?></td>
				<td>[<?php echo $configuracion['GlobalDato']['user_modified']?> - <?php echo $configuracion['GlobalDato']['modified']?>]</td>
			</tr>
		<?php endforeach;?>	
	</table>
<?php else:?>
	<div class="notices_error">NO EXISTEN CONFIGURACIONES HABILITADAS</div>
<?php endif;?>

<?php //   debug($configuraciones)?>




<h3>AGREGAR NUEVA CONFIGURACION</h3>



<div class="areaDatoForm">

	<?php echo $form->create(null,array('action' => 'reasignar_proveedor_config', 'id' => 'reasignar_proveedor_config', 'onsubmit' => "return confirm('GUARDAR NUEVA CONFIGURACION?')"));?>

	
	<h3>1) Seleccionar el Producto a reasignar (solo vigentes)</h3>
	<table>
		<tr>
			<th></th>
			<th>CODIGO</th>
			<th>PROVEEDOR</th>
			<th>PRODUCTO DESCRIPCION</th>
		</tr>
	<?php foreach($productos as $id => $producto):?>
		<tr id="GlobalDatoCodigoProducto_<?php echo $id?>">
			<td><input type="checkbox" name="data[GlobalDato][codigo_producto][<?php echo $id?>]" value="<?php echo $id."|".$producto[1]?>" onclick="toggleCell('GlobalDatoCodigoProducto_<?php echo $id?>',this);"/></td>
			<td><?php echo $id?></td>
			<td><?php echo $producto[0]?></td>
			<td><?php echo $producto[1]?></td>
		</tr>
	<?php endforeach;?>
	</table>
	<br/>
	<h3>2) Indicar a que Proveedor/Proveedores se puede reasignar (solo Activos)</h3>
	<table>
		<tr>
			<th></th>
			<th>CUIT</th>
			<th>RAZON SOCIAL</th>
		</tr>	
	<?php foreach($proveedores as $proveedor):?>
		<tr id="GlobalDatoProveedorId_<?php echo $proveedor['Proveedor']['id']?>">
			<td><input type="checkbox" name="data[GlobalDato][proveedor_id][<?php echo $proveedor['Proveedor']['id']?>]" value="<?php echo $proveedor['Proveedor']['id']."|".$proveedor['Proveedor']['cuit']."|".$proveedor['Proveedor']['razon_social_resumida']?>" onclick="toggleCell('GlobalDatoProveedorId_<?php echo $proveedor['Proveedor']['id']?>',this);"/></td>
			<td><?php echo str_pad($proveedor['Proveedor']['cuit'],11,0,STR_PAD_LEFT)?></td>
			<td><?php echo $proveedor['Proveedor']['razon_social']?></td>
		</tr>	
	<?php endforeach;?>
	</table>
	<br/>
	<h3>3) Indicar que Usuarios pueden efectuar esta operaci&oacute;n (solo Activos)</h3>
	<table>
		<tr>
			<th></th>
			<th>USUARIO</th>
			<th>DESCRIPCION</th>
			<th>GRUPO</th>
		</tr>	
	<?php foreach($usuarios as $usuario):?>
		<tr id="GlobalDatoUsuarioId_<?php echo $usuario['Usuario']['id']?>">
			<td><input type="checkbox" name="data[GlobalDato][usuario_id][<?php echo $usuario['Usuario']['id']?>]" value="<?php echo $usuario['Usuario']['usuario']?>" onclick="toggleCell('GlobalDatoUsuarioId_<?php echo $usuario['Usuario']['id']?>',this);"/></td>
			<td><?php echo $usuario['Usuario']['usuario']?></td>
			<td><?php echo $usuario['Usuario']['descripcion']?></td>
			<td><?php echo $usuario['Grupo']['descripcion']?></td>
		</tr>	
	<?php endforeach;?>
	</table>
	
	<?php echo $frm->submit("GUARDAR NUEVA CONFIGURACION",array('id' => 'btn_genera_orden'))?>
		
</div>




