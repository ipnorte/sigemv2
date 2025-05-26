<?php // echo $this->renderElement('head',array('title' => 'PADRON DE PERSONAS / SOCIOS :: IMPRIMIR PADRON'))?>
<!--<hr>-->
<!--<div class="actions">-->
<?php // echo $controles->btnL('Regresar','/pfyj/personas')?>
<!--</div>-->
<?php echo $this->renderElement('personas/menu_inicial',array('plugin' => 'pfyj'))?>
<h3>PADRON DE PERSONAS / SOCIOS :: IMPRIMIR PADRON</h3>
<script language="Javascript" type="text/javascript">

Event.observe(window, 'load', function(){

//	$('periodo_corte_deuda').hide();
	$('incluye_deuda').observe('click',function(){
//		if(document.getElementById('incluye_deuda').checked) $('periodo_corte_deuda').show();
//		else $('periodo_corte_deuda').hide();
	});
	
	$('PersonaP1').observe('change',function(){
//		if($('PersonaP1').getValue() == 5 || $('PersonaP1').getValue() == 6){
//			$('incluye_deuda').disable();
////			$('periodo_corte_deuda').hide();
////			document.getElementById('incluye_deuda').checked = false;
//		}else{
////			$('incluye_deuda').enable();
////			if(document.getElementById('incluye_deuda').checked) $('periodo_corte_deuda').show();
//		}	
	});




	<?php if(isset($this->data['Persona']['p2'])):?>
		document.getElementById('incluye_deuda').checked = true;
//		$('periodo_corte_deuda').toggle();
		document.getElementById('PersonaPeriodoCorteMonth').value = "<?php echo $this->data['Persona']['periodo_corte']['month']?>";
		document.getElementById('PersonaPeriodoCorteYear').value = "<?php echo $this->data['Persona']['periodo_corte']['year']?>";
	<?php endif;?>
	
	
});

function FormReporte(disable){
	if(disable==1)$('imprimir_padron').disable();
	else $('imprimir_padron').enable();
}

</script>

<?php echo $frm->create(null,array('action' => 'imprimir_padron','id' => 'imprimir_padron'));?>
<div class="areaDatoForm">
	<h3>OPCIONES DE FILTRADO</h3>
	<table class="tbl_form">
		<tr>
			<td>PADRON DE </td>
			<td><?php echo $frm->input('Persona.p1',array('type' => 'select','options' => $opciones,'label'=>'','selected' => (!empty($this->data['Persona']['p1']) ? $this->data['Persona']['p1'] : 1))) ?></td>
		</tr>
		<tr>
			<td>INCLUIR TOTAL DEUDA</td>
			<td><input type="checkbox" name="data[Persona][p2]" value="1" id="incluye_deuda"/></td>
		</tr>
		
		<tr id="periodo_corte_deuda">
			<td>CORTE DE DEUDA A</td>
			<td><?php echo $frm->periodo('Persona.periodo_corte','',$this->data['Persona']['periodo_corte']['year'].$this->data['Persona']['periodo_corte']['month'].'01',date('Y')-1)?></td>
		</tr>
		
		<tr>
			<td>TIPO REPORTE</td>
			<td><?php echo $frm->tipoReporte((!empty($this->data['Persona']['tipo_reporte']) ? $this->data['Persona']['tipo_reporte'] : "PDF")) ?></td>
		</tr>		
	</table>
	
</div>
<?php echo $frm->end("GENERAR PROCESO PARA EL LISTADO")?>

<?php if(!empty($this->data)):?>

<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'reporte_padron_personas',
										'accion' => '.pfyj.personas.imprimir_padron.'.$this->data['Persona']['tipo_reporte'],
										'target' => '_blank',
										'btn_label' => 'CONSULTAR LISTADO PADRON DE PERSONAS',
										'titulo' => 'LISTADO PADRON DE PERSONAS',
										'subtitulo' => $opciones[$this->data['Persona']['p1']]." ".(isset($this->data['Persona']['p2']) ? "*** INCLUYE DEUDA ***" : "") . " [".$this->data['Persona']['tipo_reporte']."]",
										'p1' => $this->data['Persona']['p1'],
										'p2' => (isset($this->data['Persona']['p2']) ? "1" : "0"),
										'p3' => $this->data['Persona']['periodo_corte']['year'].$this->data['Persona']['periodo_corte']['month'],
                                                                                'txt1' => base64_encode(serialize($opciones)),
										'remote_call_start' => 'FormReporte(1)',
										'remote_call_stop' => 'FormReporte(0)'
));

?>	

<?php endif;?>
