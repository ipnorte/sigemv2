<?php echo $this->renderElement('head',array('title' => 'IMPRESION DE HOJAS FOLIADAS'))?>
<hr>
<script type="text/javascript">
function validateForm(){
	var libroNro = $("PersonaLibroSociosNumero").getValue();
	var hojaDesde = $("PersonaHojaDesde").getValue();
	var hojaHasta = $("PersonaHojaHasta").getValue();

//	if(libroNro === ""){
//		alert("DEBE INDICAR EL NUMERO DE LIBRO DE SOCIOS A GENERAR!");
//		$("PersonaLibroSociosNumero").focus();
//		return false;
//	}
	if(hojaDesde === ""){
		alert("DEBE INDICAR EL NUMERO DE HOJA INICIAL!");
		$("PersonaHojaDesde").focus();
		return false;
	}
	if(hojaHasta === ""){
		alert("DEBE INDICAR EL NUMERO DE HOJA FINAL!");
		$("PersonaHojaHasta").focus();
		return false;
	}
	var fillNroLibro = parseInt($("PersonaFillNroLibro").getValue());
	var fillNroHoja = $("PersonaFillNroHoja").getValue();
	
	var msg = "**** GENERAR HOJAS FOLIADAS ****\n";
	msg = msg + "NOMBRE: " + $("PersonaLibroSociosNombre").getValue() + "\n";
    msg = msg + "LIBRO Nro: " + rellenar(libroNro,0,fillNroLibro,"L") + "\n";
	msg = msg + "DESDE FOLIO Nro " + rellenar(hojaDesde,0,fillNroHoja,"L") + " HASTA " + rellenar(hojaHasta,"0",fillNroHoja,"L");
	return confirm(msg);
	
}
</script>
<?php echo $frm->create(null,array('action' => 'imprimir_folios','id' => 'imprimir_folios','onsubmit' => 'return validateForm();'));?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>NOMBRE DEL LIBRO</td><td><?php echo $frm->input('Persona.libro_socios_nombre',array('size' => 30, 'maxlength' => 30))?></td>
		</tr>        
		<tr>
			<td>LIBRO Nro:</td><td><?php echo $frm->number('Persona.libro_socios_numero')?></td>
		</tr>
		<tr>
			<td>DESDE HOJA Nro</td><td><?php echo $frm->number('Persona.hoja_desde')?></td>
		</tr>
		<tr>
			<td>HASTA HOJA Nro</td><td><?php echo $frm->number('Persona.hoja_hasta')?></td>
		</tr>
	</table>
</div>
<?php echo $frm->hidden('Persona.fill_nro_libro',array('value' => $fillNroLibro))?>
<?php echo $frm->hidden('Persona.fill_nro_hoja',array('value' => $fillNroHoja))?>
<?php echo $frm->end("IMPRIMIR FOLIOS")?>