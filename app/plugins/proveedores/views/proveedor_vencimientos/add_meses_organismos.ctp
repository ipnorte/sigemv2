<h1>FECHA DE CORTE Y VENCIMIENTOS :: AGREGAR</h1>
<hr>


<?php echo $form->create(null,array('action' => 'add'));?>
<div class="areaDatoForm">
	<div class="row">
		<?php echo $this->renderElement('global_datos/combo_global',array(
																		'plugin'=>'config',
																		'label' => " ",
																		'model' => 'ProveedorVencimiento.codigo_organismo',
																		'prefijo' => 'MUTUCORG',
																		'disabled' => false,
																		'empty' => false,
																		'metodo' => "get_organismos",
		))?>	
		<?php //   echo $this->requestAction('/config/global_datos/combo/ORGANISMO/ProveedorVencimiento.codigo_organismo/MUTUCORG');?>
	</div>
	<div class="row">
		<table cellpadding="0" cellspacing="0">
		
			<tr>
				<td align="right">Dia de Corte</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.d_corte',array('label'=>'')); ?></td>
			</tr>
			<tr>	
				<td align="right">Dia Vencimiento (Socio)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.d_vto_socio',array('label'=>'')); ?></td>
			</tr>
			<tr>
				<td align="right">Antes del Corte Inicia (+m)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.m_ini_socio_ac_suma',array('label'=>'')); ?></td>
			</tr>			
			<tr>
				<td align="right">Despues del Corte Inicia (+m)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.m_ini_socio_dc_suma',array('label'=>'')); ?></td>
			</tr>

			<tr>
				<td align="right">Vto. Socio (+m Desp. Inicio)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.m_vto_socio_suma',array('label'=>'')); ?></td>
			</tr>			
			<tr>
				<td align="right">Vto. Proveedor (+d Desp.Vto.Socio)</td><td align="right"><?php echo $frm->number('ProveedorVencimiento.d_vto_proveedor_suma',array('label'=>'')); ?></td>
			</tr>
			<tr>
			
				<td align="right">
					<div><strong>MESES APLICADOS</strong></div>
					<?php echo $this->renderElement('global_datos/grilla_meses',array('modelo' => 'ProveedorVencimiento','plugin'=>'config'))?>
				</td>
				<td>
					<div><strong>PROVEEDOR</strong></div>
					<?php echo $this->renderElement('proveedor/grilla_checks',array('modelo' => 'ProveedorVencimiento','soloActivos' => 0, 'plugin' => 'proveedores'))?>
				</td>
			</tr>
		
		</table>
	</div>

	<div style="clear: both;"></div>
</div>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/proveedores/proveedor_vencimientos/index" : $fwrd) ))?>