<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>REGISTRO DE NOVEDADES :: CARGA DE NOVEDAD</h3>

<?php echo $form->create(null,array('action'=>'add/'.$persona['Persona']['id'],'type' => 'file'));?>

<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>EMITIDA POR: <strong><?php echo strtoupper($user['Usuario']['usuario'])?></strong></td>
		</tr>
		<tr>
			<td>FECHA: <strong><?php echo date("d-m-Y")?></strong></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $frm->textarea('PersonaNovedad.descripcion',array('cols' => 80, 'rows' => 15))?></td>
		</tr>
		<tr><td>ADJUNTAR ARCHIVO</td><td><?php echo $frm->file('archivo',array('size' => 50))?></td></tr>
		<tr><td>ARCHIVOS VALIDOS</td><td><?php echo strtoupper(implode(", ",$tipos_permitidos))?></td></tr>
	</table>

</div>
<?php echo $frm->hidden('PersonaNovedad.id',array('value' => 0)); ?>
<?php echo $frm->hidden('PersonaNovedad.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->hidden('PersonaNovedad.usuario',array('value' => strtoupper($user['Usuario']['usuario']))); ?>
<?php echo $frm->hidden('PersonaNovedad.fecha',array('value' => date("Y-m-d"))); ?>

<?php echo $frm->btnGuardarCancelar(array('URL' => '/pfyj/persona_novedades/index/'.$persona['Persona']['id']))?>