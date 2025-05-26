<?php echo $this->renderElement('head',array('title' => 'REASIGNACION DE BENEFICIO','plugin' => 'config'))?>
<?php //   echo $this->renderElement('personas/datos_personales',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => true,'plugin' => 'pfyj'))?>
<?php //   echo $this->renderElement('persona_beneficios/beneficios_by_persona',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'))?>

<h3>LISTADO DE ORDENES DE DESCUENTOS VIGENTES</h3>
<div class="notices"><strong>ATENCION!: </strong> Este proceso afecta &uacute;nicamente a las Ordenes de Descuentos y Cuotas que estan ADEUDADAS.</div>



<?php echo $frm->create(null,array('action' => 'reasignar_beneficio/'.$persona['Persona']['id'], 'onsubmit' => "return validate()"))?>

<?php if(!empty($ordenes)):?>

<script type="text/javascript">
	function validate(){
		var validar = false;
		var rows = <?php echo count($ordenes)?>;
		var beneficio_sel_id = $('OrdenDescuentoPersonaBeneficioId').getValue();
		var beneficio_sel = getTextoSelect("OrdenDescuentoPersonaBeneficioId");
		var ords = "";
		var msg = "";
		var cant = 0;
		
		for (i=1;i<=rows;i++){

			oCHK = $("chk_" + i);
			
			descripcion = $("descripcion_" + i).getValue();
			beneficio_id = $("beneficio_" + i).getValue();

//			alert(oCHK.checked);
//			alert(beneficio_sel_id);
//			alert(beneficio_id);
			
			if(oCHK.checked && beneficio_sel_id != beneficio_id){
				validar = true;
				ords = ords + descripcion + "\n";
				cant++;
//			}else{
//				validar = false;
			}
			
		}
		
		if(validar){
			msg = "ATENCION!\n";
			msg = msg + "REASIGNAR LAS SIGUIENTES ORDENES:\n" + ords;
			msg = msg + "AL BENEFICIO: " + beneficio_sel + "\n";
			msg = msg + "DICHAS ORDENES SERAN ANULADAS POR NOVACION Y EMITIDAS BAJO UN NUEVO NUMERO\n";
			msg = msg + "DESEA CONTINUAR?";
			return confirm(msg);
		}else{
			alert("DEBE INDICAR AL MENOS UNA ORDEN! O EL BENEFICIO SELECCIONADO ES EL MISMO DE LA ORDEN");
			return false;
		}
		
		
	}	
</script>

<table>

	<tr>
		<th>ORDEN</th>
		<th>INICIA</th>
		<th>1er VTO</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th colspan="2">DEVENGADO</th>
		<th>IMPORTE CUOTA</th>
		<th colspan="2">VENCIDO</th>
		<th colspan="2">A VENCER</th>
		<th colspan="2">PAGADO</th>
		<th>PER</th>
		<th>BENEFICIO</th>
		<th></th>
	</tr>

<?php
$i = 0;
$TOTAL = 0;
$VENCIDO = 0;
$AVENCER = 0;
$PAGADO = 0;

foreach ($ordenes as $ord):

	$TOTAL += $ord['OrdenDescuento']['importe_devengado'];
	$VENCIDO += $ord['OrdenDescuento']['importe_vencido'];
	$AVENCER += $ord['OrdenDescuento']['importe_avencer'];
	$PAGADO += $ord['OrdenDescuento']['importe_pagado'];

	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
  	$bloqueo = array();
  	if(!empty($ord['OrdenDescuento']['bloqueo_liquidacion'])) $bloqueo = $ord['OrdenDescuento']['bloqueo_liquidacion'];
	
?>	
	<tr class="<?php echo $ord['OrdenDescuento']['tipo_orden_dto']?>">
	
		<td align="center"><?php echo $controles->linkModalBox($ord['OrdenDescuento']['id'],array('title' => 'ORDEN DE DESCUENTO #' . $ord['OrdenDescuento']['id'],'url' => '/mutual/orden_descuentos/view/'.$ord['OrdenDescuento']['id'].'/'.$ord['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 750))?></td>
		<td nowrap="nowrap"><?php echo $util->periodo($ord['OrdenDescuento']['periodo_ini'])?></td>
		<td nowrap="nowrap"><?php echo $util->armaFecha($ord['OrdenDescuento']['primer_vto_socio'])?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['tipo_nro']?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['proveedor_producto']?></td>
		
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_devengado'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['cuotas']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_cuota'],2)?></td>
		<td align="right" nowrap="nowrap"><?php echo number_format($ord['OrdenDescuento']['importe_vencido'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['vencidas']?></td>
		<td align="right" nowrap="nowrap"><?php echo number_format($ord['OrdenDescuento']['importe_avencer'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['avencer']?></td>	
		<td align="right" nowrap="nowrap"><?php echo number_format($ord['OrdenDescuento']['importe_pagado'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['pagadas']?></td>				
		<td align="center"><?php echo $controles->OnOff($ord['OrdenDescuento']['permanente'],true)?></td>
		<td><?php echo $ord['OrdenDescuento']['beneficio_str']?></td>
		<td>
			<?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
			<input type="checkbox" disabled="disabled" name="data[OrdenDescuento][orden_descuento_check_id][<?php echo $ord['OrdenDescuento']['id']?>]" value="1"/>
			<span style="color: red;"><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
			<?php else:?>
			<input type="checkbox" name="data[OrdenDescuento][orden_descuento_check_id][<?php echo $ord['OrdenDescuento']['id']?>]" value="<?php echo $ord['OrdenDescuento']['id']?>" id="chk_<?php echo $i?>"/>
			<input type="hidden" id="descripcion_<?php echo $i?>" value="<?php echo $ord['OrdenDescuento']['id']."|".$ord['OrdenDescuento']['tipo_nro']."|".$ord['OrdenDescuento']['proveedor_producto']?>" />
			<input type="hidden" id="beneficio_<?php echo $i?>" value="<?php echo $ord['OrdenDescuento']['persona_beneficio_id']?>" />
			<?php endif;?>
		</td>
	</tr>

<?php endforeach;?>	

	<tr class="totales">
		<th colspan="5">TOTALES</th>
		<th colspan="2"><?php echo number_format($TOTAL,2)?></th>
		<th></th>
		<th colspan="2"><?php echo number_format($VENCIDO,2)?></th>
		<th colspan="2"><?php echo number_format($AVENCER,2)?></th>
		<th colspan="2"><?php echo number_format($PAGADO,2)?></th>
		<th colspan="2"></th>
		<th></th>
	</tr>

</table>



<div class="areaDatoForm">
    <h4>NUEVO BENEFICIO AL CUAL SE ASIGNAN</h4>
    <table class="tbl_form">
        <tr>
            <td><?php echo $this->requestAction('/pfyj/persona_beneficios/combo/OrdenDescuento/'.$persona['Persona']['id'])?></td>
            <td style="color: red;font-weight: bold;">A PARTIR DE</td>
            <td>
                <?php echo $frm->periodo('PersonaBeneficio.periodo_desde','',(isset($periodo_corte) ? $periodo_corte :  null),date('Y')-1,date('Y')+1,false)?>
                <?php // echo $this->renderElement("liquidacion/periodos_liquidados",array('plugin' => 'mutual','order' => 'ASC','model' => 'PersonaBeneficio.periodo_desde', 'facturados' => false, 'organismo' => $beneficio['PersonaBeneficio']['codigo_beneficio']))?></td>
            </tr>        
            
    </table>
    
	
	<?php // echo $this->requestAction('/pfyj/persona_beneficios/combo/OrdenDescuento/'.$persona['Persona']['id'])?>
</div>


<?php echo $frm->hidden('reasignar_beneficio',array('value' => 1))?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'REASIGNAR','URL' => ( empty($fwrd) ? "/mutual/orden_descuentos/reasignar_beneficio" : $fwrd) ))?>

<div style="clear:both;"></div>

<?php else:?>
<h4>NO EXISTEN ORDENES DE CONSUMOS VIGENTES</h4>	
<?php endif;?>