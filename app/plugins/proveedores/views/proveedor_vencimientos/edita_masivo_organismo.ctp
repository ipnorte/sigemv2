<h1>FECHA DE CORTE Y VENCIMIENTOS :: MODIFICAR</h1>
<hr>

<?php echo $form->create(null,array('action' => "edita_masivo_organismo/$organismo/$mes"));?>
<div class="areaDatoForm">
	<h3 style="border-bottom: 1px solid;">
		<?php echo $this->requestAction('/config/global_datos/valor/'.$organismo.'/concepto_1')?> 
		:: 
		<?php echo $util->mesToStr($mes,true)?>
	</h3>
	<div class="row">
		<table cellpadding="0" cellspacing="0">
		
			<tr>
				<td>Dia de Corte</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.d_corte',array('label'=>'')); ?></td>
			</tr>
			<tr>	
				<td>Dia Vencimiento (Socio)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.d_vto_socio',array('label'=>'')); ?></td>
			</tr>
			<tr>
				<td>Antes del Corte Inicia (+m)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.m_ini_socio_ac_suma',array('label'=>'')); ?></td>
			</tr>			
			<tr>
				<td>Despues del Corte Inicia (+m)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.m_ini_socio_dc_suma',array('label'=>'')); ?></td>
			</tr>

			<tr>
				<td>Vto. Socio (+m Desp. Inicio)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.m_vto_socio_suma',array('label'=>'')); ?></td>
			</tr>			
			
			<tr>
				<td>Vto. Proveedor (+d Desp.Vto.Socio)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.d_vto_proveedor_suma',array('label'=>'')); ?></td>
			</tr>
			
			<tr>
				<td>Aplicar para TODOS LOS MESES</td>
				<td align="right">
					<input type="checkbox" name="data[ProveedorVencimiento][aplicar_atodoslosmeses]" value="1" id="ProveedorVencimientoAplicarAtodoslosmeses" />
				</td>
			</tr>			
			
		</table>
		
		<div class="row">
		
		</div>
		
		
	</div>

	<div style="clear: both;"></div>
</div>
<?php echo $frm->hidden('ProveedorVencimiento.codigo_organismo',array('value' => $organismo))?>
<?php echo $frm->hidden('ProveedorVencimiento.mes',array('value' => $mes))?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/proveedores/proveedor_vencimientos/index?data[ProveedorVencimiento][codigo_organismo]=".$organismo."&data[ProveedorVencimiento][mes][month]=".$mes : $fwrd) ))?>


