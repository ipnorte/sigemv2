<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<div class="areaDatoForm">
	<h3>PROCESO DE LIQUIDACION DE DEUDA</h3>
	<?php echo $frm->create(null,array('action' => 'proceso', 'id' => 'FrmLiqCsoc'))?>
	
	<table class="tbl_form">

		<tr>
			<td>PERIODO A LIQUIDAR</td>
			<td><?php echo $frm->periodo('Liquidacion.periodo_ini','',$periodo,date('Y') - 10,date('Y') + 1)?></td>
		</tr>

		<tr>
			<td>ORGANISMO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'Liquidacion.codigo_organismo',
																			'prefijo' => 'MUTUCORG',
																			'disabled' => false,
																			'empty' => false,
																			'metodo' => "get_organismos",
			))?>			
			<?php //   echo $this->requestAction('/config/global_datos/combo/./Liquidacion.codigo_organismo/MUTUCORG')?>
			</td>
		</tr>
		<tr>
			<td>PRE-IMPUTACION</td><td><input type="checkbox" name="data[Liquidacion][pre_imputacion]" value="1"/></td>
		</tr>
                <tr>
                    <td>TIPO LIQUIDACION</td>
                    <td>
                        <?php echo $frm->input('Liquidacion.tipo_deuda_liquida',array('type' => 'select','empty' => false, 'selected' => "0",'options' => $tiposLiquidacion))?>
                    </td>
                </tr>
		<tr>
			<td colspan="2"><?php echo $frm->submit("GENERAR PROCESO")?></td>
		</tr>		
	</table>
	
	<?php echo $frm->end()?>	
</div>