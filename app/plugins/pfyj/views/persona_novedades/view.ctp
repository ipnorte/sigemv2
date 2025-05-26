<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>REGISTRO DE NOVEDADES</h3>

<div class="areaDatoForm2">
<!--	<h3>DETALLE NOVEDAD #<?php echo $novedad['PersonaNovedad']['id']?></h3>-->
	<table class="tbl_form">
		<tr>
			<td>FECHA:<strong><?php echo $util->armaFecha($novedad['PersonaNovedad']['fecha'])?></strong></td>
		</tr>
		<tr>
			<td>EMITIDA POR:<strong><?php echo $novedad['PersonaNovedad']['usuario']?></strong></td>
		</tr>
		<tr><td><br/></td></tr>
		<tr>
			<td><?php echo $novedad['PersonaNovedad']['descripcion']?></td>
		</tr>
	</table>

</div>


<div class="actions">
<?php echo $controles->btnRew("REGRESAR AL LISTADO DE NOVEDADES",'index/'.$persona['Persona']['id'])?>
&nbsp;|&nbsp;
<?php echo $controles->botonGenerico('comentar_novedad/'.$novedad['PersonaNovedad']['id'],'controles/pin.png','CARGAR NUEVO COMENTARIO')?>
</div>
<?php if(!empty($novedad['PersonaNovedadComentario'])):?>
	<br/>
	<h4>DETALLE DE COMENTARIOS EMITIDOS PARA ESTA NOVEDAD</h4>
	<br/>
	<table>
	
		<tr>
			<th>FECHA</th>
			<th>EMITIDA POR</th>
			<th>NOVEDAD</th>
		</tr>
		
		<?php foreach($novedad['PersonaNovedadComentario'] as $comentario):?>
		
			<tr>
				<td><?php echo $util->armaFecha($comentario['fecha'])?></td>
				<td><?php echo $comentario['usuario']?></td>
				<td><?php echo $comentario['descripcion']?></td>
			
			</tr>
		
		
		<?php endforeach;?>
		
	
	</table>

<?php //   debug($novedad['PersonaNovedadComentario'])?>
<?php endif;?>