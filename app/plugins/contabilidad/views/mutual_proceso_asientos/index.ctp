<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PROCESO DE ASIENTOS'))?>


<?php echo $this->renderElement('paginado');?>

<?php if($nuevoProceso == 1) echo $controles->botonGenerico('/contabilidad/mutual_proceso_asientos/procesar_asientos/','controles/add.png','NUEVO PROCESO DE ASIENTOS');?>

<table cellpadding="0" cellspacing="0">

	<tr>
		<th><?php echo $paginator->sort('NRO. PROCESO','id');?></th>
		<th><?php echo $paginator->sort('FECHA DESDE','fecha_desde');?></th>
		<th><?php echo $paginator->sort('FECHA HASTA','fecha_hasta');?></th>
		<th>ESTADO</th>
		<th>ACCION</th>
		<th>LIBRO DIARIO</th>
		<th>BALANCE S.S.</th>
		<th>LIBRO MAYOR</th>
		<th>BAL. S.S. CONS.</th>
	</tr>
	<?php
	$i = 0;
	foreach ($aMutualProcesoAsiento as $proceso):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><?php echo $proceso['MutualProcesoAsiento']['id']?></td>
			<td><?php echo date('d/m/Y',strtotime($proceso['MutualProcesoAsiento']['fecha_desde']))?></td>
			<td><?php echo date('d/m/Y',strtotime($proceso['MutualProcesoAsiento']['fecha_hasta']))?></td>
			<?php if($proceso['MutualProcesoAsiento']['cerrado'] == 0):?>
				<td align="center"><?php echo $frm->btnForm(array('URL'=>'/contabilidad/mutual_proceso_asientos/procesar_asientos/','LABEL' => 'REPROCESAR'))?></td>
				<td align="center"><?php echo $frm->btnForm(array('URL'=>'/contabilidad/mutual_proceso_asientos/aprobar_asientos/'.$proceso['MutualProcesoAsiento']['id'],'LABEL' => 'APROBAR ASIENTOS'))?></td>
			<?php else:?>
				<td align="center">CERRADO</td>
				<td align="center">APROBADO</td>
			<?php endif;?>
			<td>
				<?php 
				echo $controles->botonGenerico('/contabilidad/mutual_proceso_asientos/view_libro_diario_borrador/' . $proceso['MutualProcesoAsiento']['id'],'controles/HTML-globe.png', null, array('id' => 'html'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_borrador_xls/' . $proceso['MutualProcesoAsiento']['id'],'controles/ms_excel.png', null, array('target' => 'blank'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_borrador_pdf/' . $proceso['MutualProcesoAsiento']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
			<td>
				<?php 
				echo $controles->botonGenerico('/contabilidad/mutual_proceso_asientos/view_balance_borrador/' . $proceso['MutualProcesoAsiento']['id'],'controles/HTML-globe.png', null, array('id' => 'html'));
				echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_xls/' . $proceso['MutualProcesoAsiento']['id'],'controles/ms_excel.png', null, array('id' => 'xls'));
				echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_pdf/' . $proceso['MutualProcesoAsiento']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
			<td>
				<?php 
				echo $controles->botonGenerico('/contabilidad/mutual_proceso_asientos/view_mayor_general_borrador/' . $proceso['MutualProcesoAsiento']['id'],'controles/HTML-globe.png', null, array('id' => 'html'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_borrador_xls/' . $proceso['MutualProcesoAsiento']['id'],'controles/ms_excel.png', null, array('id' => 'xls'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_borrador_pdf/' . $proceso['MutualProcesoAsiento']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
			<td>
				<?php 
				echo $controles->botonGenerico('/contabilidad/mutual_proceso_asientos/view_balance_borrador/' . $proceso['MutualProcesoAsiento']['id'] . '/1','controles/HTML-globe.png', null, array('id' => 'html'));
				echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_xls/' . $proceso['MutualProcesoAsiento']['id'] . '/1','controles/ms_excel.png', null, array('id' => 'xls'));
				echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_pdf/' . $proceso['MutualProcesoAsiento']['id'] . '/1','controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
		</tr>
	<?php endforeach; ?>	
</table>
<?php echo $this->renderElement('paginado')?>
