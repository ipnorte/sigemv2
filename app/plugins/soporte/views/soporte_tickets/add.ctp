<?php echo $this->renderElement('title')?>
<?php echo $this->renderElement('menu')?>
<h3>CARGAR NUEVO TICKET</h3>

<?php echo $frm->create(null,array('type' => 'file','id' => 'formUpLoadFile'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>EMITIDO</td><td><strong><?php echo date('d-m-Y')?></strong> - <strong><?php echo $_SESSION['NAME_USER_LOGON_SIGEM']?></strong></td>
		</tr>		
		<tr>
			<td>TIPO</td>
			<td><?php echo $frm->input('tipo',array('options' => $tipos))?></td>
		</tr>
		<tr><td>ASUNTO</td><td><?php echo $frm->input('asunto',array('size' => 80, 'maxlenght' => 150))?></td></tr>
		<tr><td colspan="2"><?php echo $frm->textarea('descripcion',array('cols' => 80, 'rows' => 20))?></td></tr>		
		<tr><td>ARCHIVO</td><td><?php echo $frm->file('archivo_adjunto',array('size' => 50))?></td></tr>		
		
	
	</table>

</div>
<?php echo $frm->hidden('emitido_por',array('value' => $_SESSION['NAME_USER_LOGON_SIGEM']))?>
<?php echo $frm->hidden('fecha_inicio',array('value' => date('Y-m-d')))?>
<?php echo $frm->hidden('estado',array('value' => 'SOL'))?>
<?php echo $frm->end("GENERAR TICKET");?>	