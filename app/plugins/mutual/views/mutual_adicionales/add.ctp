<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DE CONCEPTOS ADICIONALES :: NUEVO CONCEPTO','plugin' => 'config'))?>
<?php echo $form->create(null);?>
	<div class="areaDatoForm">
		<table class="tbl_form">
			
			<tr>
				<td>DEUDA PROVEEDOR (BLANCO = TODOS)</td>
				<td>
					<?php // echo $this->requestAction('/proveedores/proveedores/combo/0/');?>
					<?php echo $this->renderElement('proveedor/combo_general',array(
																					'plugin'=>'proveedores',
																					'metodo' => "proveedores_list/1",
																					'model' => 'MutualAdicional.proveedor_id',
																					'empty' => true,
					))?>					
				</td>
			</tr>			
			<tr>
				<td>IMPUTAR A</td>
				<td>
					<?php // echo $this->requestAction('/proveedores/proveedores/combo/0/./MutualAdicional.imputar_proveedor_id');?>
					<?php echo $this->renderElement('proveedor/combo_general',array(
																					'plugin'=>'proveedores',
																					'metodo' => "proveedores_list/1",
																					'model' => 'MutualAdicional.imputar_proveedor_id',
																					'empty' => true,
					))?>					
				</td>
			</tr>			
			<tr>
				<td>ORGANISMO</td>
                <td>
				<?php echo $this->renderElement('global_datos/combo_global',array(
																				'plugin'=>'config',
																				'label' => " ",
																				'model' => 'MutualAdicional.codigo_organismo',
																				'prefijo' => 'MUTUCORG',
																				'disabled' => false,
																				'empty' => false,
																				'metodo' => "get_organismos",
																				'selected' => ""
				))?>                    
                    <?php // echo $this->requestAction('/config/global_datos/combo/./MutualAdicional.codigo_organismo/MUTUCORG')?>
                </td>
			</tr>
			
			<tr>
				<td>PORCENTAJE / IMPORTE</td><td><?php echo $frm->input('tipo',array('type' => 'select','options' => $tipos))?> <?php echo $frm->money('valor')?></td>
			</tr>			
			<tr>
				<td>APLICAR SOBRE</td><td><?php echo $frm->input('deuda_calcula',array('type' => 'select','options' => $aplicar_sobre))?></td>
			</tr>
			<tr>
				<td>DEVENGAR CUOTA PREVIAMENTE</td><td><?php echo $frm->input('devengado_previo',array('label'=>'')) ?></td>
			</tr>
			<tr>
				<td>APLICAR ESTE CONCEPTO (PERIODO DESDE / HASTA)</td><td><?php echo $frm->number('periodo_desde',array('label'=>'','size'=>6,'maxlength'=>6)); ?> <?php echo $frm->number('periodo_hasta',array('label'=>'','size'=>6,'maxlength'=>6)); ?> (AAAAMM)</td>
			</tr>						
			<tr>
				<td>TIPO CUOTA A GENERAR</td>
				<td>
					<?php // echo $this->requestAction('/config/global_datos/cmb_tipoCuota/./MutualAdicional.tipo_cuota')?>
					<?php echo $this->renderElement('global_datos/combo_cuotas_puntuales',array(
																					'plugin'=>'config',
																					'label' => " ",
																					'model' => 'MutualAdicional.tipo_cuota',
																					'disable' => false,
																					'empty' => false,
																					'selected' => "",
					))?>					
				</td>
			</tr>
			<tr>
				<td>ACTIVO</td><td><?php echo $frm->input('activo',array('label'=>'','checked'=>'checked')) ?></td>
			</tr>									
			
		</table>
	</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_adicionales'))?>