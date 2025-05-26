<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PLANES :: GRILLAS :: NUEVA GRILLA DESDE UN ARCHIVO EXCEL</h3>

<div class="areaDatoForm2">
	PRODUCTO: <strong><?php echo $util->globalDato($plan['ProveedorPlan']['tipo_producto'])?></strong>
	&nbsp;
	PLAN: <strong>#<?php echo $plan['ProveedorPlan']['id']?> - <?php echo $plan['ProveedorPlan']['descripcion']?></strong>
	<br/>
	ESTADO: <strong><?php echo ($plan['ProveedorPlan']['activo'] == 1 ? "VIGENTE" : "NO VIGENTE")?></strong>
</div>

<div class="areaDatoForm">
<h3>DATOS DE LA GRILLA A CARGAR</h3>
<hr/>
<?php echo $frm->create(null,array('action' => 'nueva_grilla/' . $plan['ProveedorPlan']['id'],'type' => 'file'))?>
<div class="areaDatoForm2">
	<h3>FORMATO ARCHIVO EXCEL</h3>
	<p>
		La planilla con los datos de las cuotas a cargar en forma automatizada, deber&aacute; tener la siguiente estructura, <strong>respetando el primer rengl&oacute;n</strong>
		donde se indican el monto en mano o a percibir (en_mano) y el monto o capital solicitado. Las siguientes columnas, deber&aacute;n contener
		los n&uacute;meros que indiquen la cantidad de cuotas disponibles para el monto en mano y el solicitado, tal como muestra el siguiente ejemplo.
		<br/>
		El monto "en_mano" ser&aacute; el que el operador o vendedor pueda seleccionar en la instancia de la carga de la solicitud.
		<br/> 
		Las celdas donde se cargan los importes <strong>NO PODRAN TENER formulas, solamente n&uacute;meros con dos decimales.</strong>
	</p>
	<table style="width: 50%">
		<tr>
			<th align="center">en_mano</th>
			<th align="center">solicitado</th>
			<th align="center">6</th>
			<th align="center">9</th>
			<th align="center">12</th>
			<th align="center">...</th>
			<th align="center">n</th>
		</tr>
		<tr>
			<td align="center">200,00</td>
			<td align="center">208,00</td>
			<td align="center">52,18</td>
			<td align="center">40,68</td>
			<td align="center">35,31</td>
			<td align="center">...</td>
			<td align="center">n</td>
		</tr>
		<tr>
			<td align="center">250,00</td>
			<td align="center">260,00</td>
			<td align="center">65,22</td>
			<td align="center">50,85</td>
			<td align="center">44,13</td>
			<td align="center">...</td>
			<td align="center">n</td>
		</tr>
		<tr>
			<td align="center">300,00</td>
			<td align="center">312,00</td>
			<td align="center">78,27</td>
			<td align="center">61,03</td>
			<td align="center">52,96</td>
			<td align="center">...</td>
			<td align="center">n</td>
		</tr>		
	</table>
</div>
<table class="tbl_form">
	<tr>
		<td>ARCHIVO EXCEL</td><td><input type="file" name="data[ProveedorPlanGrilla][archivo_grilla]" id="ProveedorPlanGrillaArchivoGrilla" size="60"/></td>
	</tr>
	<tr>
		<td>DESCRIPCION</td><td><?php echo $frm->input('ProveedorPlanGrilla.descripcion',array('size'=>60,'maxlength'=>100,'div' => false,'label' => false)); ?></td>
	</tr>
	<tr>
		<td>VIGENCIA DESDE</td><td><?php echo $frm->calendar('ProveedorPlanGrilla.vigencia_desde','',null,null,date('Y') + 1)?></td>
	</tr>
	<tr>
		<td>OBSERVACIONES</td>
		<td><?php echo $frm->textarea('ProveedorPlanGrilla.observaciones',array('cols' => 60, 'rows' => 10))?></td>
	</tr>
        <tr>
            <td>T.N.A. (Tasa Nominal Anual - %)</td><td><input name="data[ProveedorPlanGrilla][tna]" type="text" value="0.00" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,false)" id="ProveedorPlanGrillaTna" /></td>
        </tr>
	
</table>
<?php echo $frm->hidden('ProveedorPlanGrilla.UID',array('value' => $UID)); ?>
<?php echo $frm->hidden('ProveedorPlanGrilla.proveedor_plan_id',array('value' => $plan['ProveedorPlan']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'PREVISUALIZAR','URL' => ( empty($fwrd) ? "/proveedores/proveedor_planes/grillas/".$plan['ProveedorPlan']['id'] : $fwrd) ))?>

</div>

<?php if(!empty($this->data)):?>
	<hr/>
	<h3>VISTA PREVIA DE LA GRILLA A INCORPORAR</h3>
<div class="areaDatoForm2">
	PRODUCTO: <strong><?php echo $util->globalDato($plan['ProveedorPlan']['tipo_producto'])?></strong>
	&nbsp;
	PLAN: <strong>#<?php echo $plan['ProveedorPlan']['id']?> - <?php echo $plan['ProveedorPlan']['descripcion']?></strong>
	<br/>
	ESTADO: <strong><?php echo ($plan['ProveedorPlan']['activo'] == 1 ? "VIGENTE" : "NO VIGENTE")?></strong>
	
	<hr/>
	
        <table>

                <tr>
                        <th style="text-align: left;">DESCRIPCION</th><td><h3><?php echo $this->data['ProveedorPlanGrilla']['descripcion']?></h3></td>
                </tr>
                <tr>
                        <th style="text-align: left;">VIGENTE A PARTIR DEL</th><td><h3><?php echo $util->armaFecha($this->data['ProveedorPlanGrilla']['vigencia_desde'])?> </h3></td>
                </tr>
                <tr>
                        <th style="text-align: left;">ARCHIVO</th><td><strong><?php echo $this->data['ProveedorPlanGrilla']['archivo_grilla']['name']?></strong></td>
                </tr>
                <tr>
                        <th style="text-align: left;">OBSERVACIONES</th><td><?php echo $this->data['ProveedorPlanGrilla']['observaciones']?></td>
                </tr>
                <tr>
                    <th style="text-align: left;">T.N.A. (Tasa Nominal Anual - %)</th><td><strong><?php echo $util->nf($this->data['ProveedorPlanGrilla']['tna'])?></strong></td>
                </tr> 
                <tr>
                    <th style="text-align: left;">T.E.M. (Tasa Efectiva Mensual - %)</th><td><strong><?php echo $util->nf($this->data['ProveedorPlanGrilla']['tem'])?></strong></td>
                </tr>  
                </table>	

	



		<h3>CONTENIDO DEL ARCHIVO LEIDO</h3>
        <?php if(empty($this->data['ProveedorPlanGrilla']['cuotas']['error'])):?>
		<table>
		<tr>
			
			<td colspan="2">
			
				<table class="tbl_grilla">
					<tr>
					<?php foreach($this->data['ProveedorPlanGrilla']['cuotas']['columnas'] as $columna):?>
						<th><?php echo $columna?></th>
					<?php endforeach;?>
					</tr>
					<?php $i = 0;?>
					<?php foreach($this->data['ProveedorPlanGrilla']['cuotas']['detalle'] as $idx => $valor):?>
					<?php 
						$class = null;
						if ($i++ % 2 == 0) {
							$class = ' class="altrow"';
						}					
					?>
						<tr <?php echo $class?>>
						<?php foreach($this->data['ProveedorPlanGrilla']['cuotas']['columnas'] as $columna):?>
							<td align="right" style="width: 100px;font-size: 12px;"><?php echo $util->nf($valor[$columna])?></td>
						<?php endforeach;?>
						</tr>
					<?php endforeach;?>
					
					
				</table>
			
			</td>
		</tr>		
        </table>
        <?php else:?>
        <div class="notices_error2">
            <strong>ERROR ANALISIS ARCHIVO</strong>
            <br/>
            <ul>
            <?php foreach($this->data['ProveedorPlanGrilla']['cuotas']['error'] as $error):?>
                <li><?php echo $error?></li>
            <?php endforeach;?>
                </ul>
        </div>
        <?php endif;?>
	<br/>
	<hr/>
	<script type="text/javascript">
	function confirmForm(){
		var confirma = confirm("DAR DE ALTA GRILLA DE CUOTAS?");
		if(confirma){
			$('btnSubmitFormCargarGrillaDB').disable();
			$('formCargarGrillaDB').submit();
		}	
	}
	</script>
	<?php echo $frm->create(null,array('id' => 'formCargarGrillaDB','action' => 'nueva_grilla/' . $plan['ProveedorPlan']['id'], 'onsubmit' => 'confirmForm()'))?>
	<?php echo $frm->hidden('Proveedor.id',array('value' => $proveedor['Proveedor']['id']))?>
	<?php echo $frm->hidden('Proveedor.UID',array('value' => $this->data['ProveedorPlanGrilla']['UID']))?>
    <?php if(empty($this->data['ProveedorPlanGrilla']['cuotas']['error'])):?>
	<div class="submit"><input type="submit" value="GUARDAR GRILLA" id="btnSubmitFormCargarGrillaDB" /></div>
    <?php endif;?>
	<?php echo $frm->end()?>
	<?php //   echo $frm->btnForm(array('LABEL' => "GUARDAR GRILLA",'URL' => '/proveedores/proveedor_planes/nueva_grilla/' . $proveedor['Proveedor']['id'].'/'.$this->data['ProveedorPlanGrilla']['UID']))?>
	</div>

<?php endif;?>
<?php //   debug($this->data)?>

