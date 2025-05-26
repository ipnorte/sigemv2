<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS :: ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'],'concepto_1')))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<?php //   if(!empty($socios)):?>
	<table>
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/0/A','controles/pdf.png','',array('target' => 'blank'))?></td><td>CUOTA SOCIAL ALTAS</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/0/A','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/0/A','controles/ms_excel.png')?></td>
		</tr>
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/0/B','controles/pdf.png','',array('target' => 'blank'))?></td><td>CUOTA SOCIAL BAJAS</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/0/B','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/0/B','controles/ms_excel.png')?></td>
		</tr>
		<!--				
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/1','controles/pdf.png','',array('target' => 'blank'))?></td><td>TODOS LOS CONSUMOS (ALTAS Y VIGENTES)</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/1','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png')?></td>
		</tr>
		-->
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/1/A','controles/pdf.png','',array('target' => 'blank'))?></td><td>CONSUMOS ALTAS Y MODIFICACIONES </td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/1/A','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/1/A','controles/ms_excel.png')?></td>
		</tr>		
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_pdf/'.$liquidacion['Liquidacion']['id'].'/1/1/B','controles/pdf.png','',array('target' => 'blank'))?></td><td>CONSUMOS BAJAS</td><td><?php if($liquidacion['Liquidacion']['imputada'] != 1) echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_txt/'.$liquidacion['Liquidacion']['id'].'/1/1/B','controles/disk.png','',array('target' => 'blank'),"**** ATENCION! *** SOLAMENTE SERAN INCLUIDOS LOS REGISTROS QUE TIENEN STATUS OK! VERIFIQUE SI NO EXISTEN ERRORES ANTES DE CONTINUAR.") ?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/control_diskette_cjp/'.$liquidacion['Liquidacion']['id'].'/1/B','controles/ms_excel.png')?></td>
		</tr>		
	
	</table>
