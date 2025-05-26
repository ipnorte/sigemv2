<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: '.$util->globalDato($organismo).' :: RESUMEN PERIODO ' . $util->periodo($periodo)))?>
<div class="areaDatoForm2">
	<h3><?php echo $util->periodo($periodo,true) . ' | ' . $util->globalDato($organismo)?></h3>
	FECHA PROCESO: <strong><?php echo $resumen['Liquidacion']['created']?></strong>
	<br/>
	ESTADO: <strong><?php echo ($resumen['Liquidacion']['cerrada'] == 1 ? '<span style="color:red;">CERRADA</span>' : '<span style="color:green;">ABIERTA</span>')?></strong>
	&nbsp;|&nbsp; TOTAL LIQUIDADO ($): <strong><?php echo $util->nf($resumen['Liquidacion']['total_periodo'] + $resumen['Liquidacion']['total_vencido'])?></strong>
</div>

<?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_liquidacion_deuda/'.$periodo.'/'.$organismo.'/1','controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?>
<h3>TOTALES LIQUIDADOS</h3>
<?php echo $this->renderElement('liquidacion/grilla_resumen',array('deuda' => $deuda, 'plugin' => 'mutual'))?>

<?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_liquidacion_deuda/'.$periodo.'/'.$organismo.'/1','controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?>