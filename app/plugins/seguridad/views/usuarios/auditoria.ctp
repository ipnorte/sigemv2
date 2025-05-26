<?php echo $this->renderElement('head',array('title' => 'ANALISIS DE AUDITORIA','plugin' => 'config'))?>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action'=>'auditoria','id' => 'formAuditaArchivo'))?>
	<table class="tbl_form">
		<tr>
			<td>ARCHIVO</td>
			<td><?php echo $frm->input('Usuario.file_auditoria',array('type' => 'select', 'options' => $files))?></td>
		</tr>
	</table>
	<?php echo $frm->submit("PROCESAR ARCHIVO PARA ANALISIS",array('id' => 'btn_process'))?>	
</div>
<?php if($process == 1):?>
<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'analisis_archivo_auditoria',
										'accion' => '.mutual.liquidaciones.detalle_archivo_general.'.$liquidacion['Liquidacion']['id'],
										'target' => '',
										'btn_label' => 'ANALISIS AUDITORIA',
										'titulo' => 'ANALISIS AUDITORIA',
										'subtitulo' => 'ARCHIVO: ' . $this->data['Usuario']['file_auditoria'],
										'p1' => $this->data['Usuario']['file_auditoria'],
));
?>
<?php endif;?>