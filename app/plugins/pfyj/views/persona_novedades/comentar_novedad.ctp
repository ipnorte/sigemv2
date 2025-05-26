<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>REGISTRO DE NOVEDADES :: AGREGAR NUEVO COMENTARIO</h3>
<div class="areaDatoForm2">
	<h3>DETALLE NOVEDAD #<?php echo $novedad['PersonaNovedad']['id']?></h3>
	<table class="tbl_frm">
		<tr>
			<td>FECHA:</td>
			<td><strong><?php echo $util->armaFecha($novedad['PersonaNovedad']['fecha'])?></strong></td>
		</tr>
		<tr>
			<td>EMITIDA POR:</td>
			<td><strong><?php echo $novedad['PersonaNovedad']['usuario']?></strong></td>
		</tr>
		
		<tr>
			<td>NOVEDAD</td>
			<td><?php echo $novedad['PersonaNovedad']['descripcion']?></td>
		</tr>
		
		<tr>
			<td></td>
			<td>
				<?php if(!empty($novedad['PersonaNovedad']['archivo_adjunto'])):?>
					<a href="<?php echo $this->base?>/files/socios/<?php echo $novedad['PersonaNovedad']['archivo_adjunto']?>" target="_blank"><?php echo $html->image('controles/attach.png',array("border"=>"0",'alt'=>'Ver Adjunto'))?> ADJUNTO</a>
				<?php endif;?>			
			</td>
		</tr>		
		
	</table>

</div>
<?php echo $form->create(null,array('action'=>'comentar_novedad/'.$novedad['PersonaNovedad']['id'],'type' => 'file'));?>

<div class="areaDatoForm">
	
	<h3>NUEVO COMENTARIO</h3>
	
	
	<table class="tbl_form">
	
		<tr>
			<td>EMITIDO POR: <strong><?php echo strtoupper($user['Usuario']['usuario'])?></strong></td>
		</tr>
		<tr>
			<td>FECHA: <strong><?php echo date("d-m-Y")?></strong></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $frm->textarea('PersonaNovedadComentario.descripcion',array('cols' => 80, 'rows' => 15))?></td>
		</tr>
		<tr><td>ADJUNTAR ARCHIVO</td><td><?php echo $frm->file('PersonaNovedadComentario.archivo',array('size' => 50))?></td></tr>
		<tr><td>ARCHIVOS VALIDOS</td><td><?php echo strtoupper(implode(", ",$tipos_permitidos))?></td></tr>
	</table>

</div>
<?php echo $frm->hidden('PersonaNovedadComentario.persona_novedad_id',array('value' => $novedad['PersonaNovedad']['id'])); ?>
<?php echo $frm->hidden('PersonaNovedadComentario.usuario',array('value' => strtoupper($user['Usuario']['usuario']))); ?>
<?php echo $frm->hidden('PersonaNovedadComentario.fecha',array('value' => date("Y-m-d"))); ?>
<?php echo $frm->hidden('PersonaNovedadComentario.id',array('value' => 0)); ?>

<?php echo $frm->btnGuardarCancelar(array('URL' => '/pfyj/persona_novedades/index/'.$novedad['PersonaNovedad']['persona_id']))?>