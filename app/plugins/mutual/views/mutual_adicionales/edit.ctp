<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DE CONCEPTOS ADICIONALES :: NUEVO CONCEPTO','plugin' => 'config'))?>
<?php echo $form->create(null);?>
	<div class="areaDatoForm">
		<table class="tbl_form">
			
			<tr>
				<td>PROVEEDOR (BLANCO = TODOS)</td>
				<td>
					<?php // echo $this->requestAction('/proveedores/proveedores/combo/'.$this->data['MutualAdicional']['proveedor_id']);?>
					<?php echo $this->renderElement('proveedor/combo_general',array(
                                            'plugin'=>'proveedores',
                                            'metodo' => "proveedores_list",
                                            'model' => 'MutualAdicional.proveedor_id',
                                            'empty' => true,
                                            'disabled' => true,
                                            'selected' => (!empty($this->data['MutualAdicional']['proveedor_id']) ? $this->data['MutualAdicional']['proveedor_id'] : ''),
					))?>					
				</td>
			</tr>			
			<tr>
				<td>IMPUTAR A</td>
				<td>
					<?php // echo $this->requestAction('/proveedores/proveedores/combo/'.$this->data['MutualAdicional']['imputar_proveedor_id'].'/./MutualAdicional.imputar_proveedor_id');?>
					<?php echo $this->renderElement('proveedor/combo_general',array(
                                            'plugin'=>'proveedores',
                                            'metodo' => "proveedores_list",
                                            'model' => 'MutualAdicional.imputar_proveedor_id',
                                            'empty' => true,
                                            'disabled' => true,
                                            'selected' => (!empty($this->data['MutualAdicional']['imputar_proveedor_id']) ? $this->data['MutualAdicional']['imputar_proveedor_id'] : ''),
					))?>					
				</td>
			</tr>			
			
			<tr>
				<td>ORGANISMO</td>
				<td>
					<?php // echo $this->requestAction('/config/global_datos/combo/./MutualAdicional.codigo_organismo/MUTUCORG')?>
					<?php echo $this->renderElement('global_datos/combo_global',array(
																					'plugin'=>'config',
																					'label' => " ",
																					'model' => 'MutualAdicional.codigo_organismo',
																					'prefijo' => 'MUTUCORG',
																					'disabled' => true,
																					'empty' => false,
                                                                                    'metodo' => "get_organismos",    
																					'selected' => (!empty($this->data['MutualAdicional']['codigo_organismo']) ? $this->data['MutualAdicional']['codigo_organismo'] : ''),
					))?>					
				</td>
			</tr>
			
			<tr>
                <td>PORCENTAJE / IMPORTE</td><td><?php echo $frm->input('tipo',array('type' => 'select','options' => $tipos, 'disabled' => true))?> <?php //   echo $frm->money('valor','',$this->data['MutualAdicional']['valor'])?> <input name="data[MutualAdicional][valor]" type="text" value="<?php echo $this->data['MutualAdicional']['valor']?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,false)" id="MutualAdicionalValor" /></td>
			</tr>			
			<tr>
				<td>APLICAR SOBRE</td><td><?php echo $frm->input('deuda_calcula',array('type' => 'select','options' => $aplicar_sobre, 'selected' => $this->data['MutualAdicional']['deuda_calcula'], 'disabled' => true))?></td>
			</tr>
			<tr>
				<td>DEVENGAR CUOTA PREVIAMENTE</td><td><?php echo $frm->input('devengado_previo',array('label'=>'','disabled' => 'disabled')) ?></td>
			</tr>
			<tr>
				<td>APLICAR ESTE CONCEPTO (PERIODO DESDE / HASTA)</td><td><?php echo $frm->number('periodo_desde',array('label'=>'','size'=>6,'maxlength'=>6, 'disabled' => 'disabled')); ?> <?php echo $frm->number('periodo_hasta',array('label'=>'','size'=>6,'maxlength'=>6, 'disabled' => 'disabled')); ?> (AAAAMM)</td>
			</tr>						
			<tr>
				<td>TIPO CUOTA A GENERAR</td>
				<td>
					<?php // echo $this->requestAction('/config/global_datos/cmb_tipoCuota/./MutualAdicional.tipo_cuota')?>
					<?php echo $this->renderElement('global_datos/combo_cuotas_puntuales',array(
																					'plugin'=>'config',
																					'label' => " ",
																					'model' => 'MutualAdicional.tipo_cuota',
																					'disabled' => true,
																					'empty' => false,
																					'selected' => (!empty($this->data['MutualAdicional']['tipo_cuota']) ? $this->data['MutualAdicional']['tipo_cuota'] : ''),
					))?>					
				</td>
			</tr>
			<tr>
				<td>ACTIVO</td><td><?php echo $frm->input('activo',array('label'=>'')) ?></td>
			</tr>									
			
		</table>
	</div>
	<?php echo $frm->hidden('id')?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_adicionales'))?>
<?php // debug($this->data)?>