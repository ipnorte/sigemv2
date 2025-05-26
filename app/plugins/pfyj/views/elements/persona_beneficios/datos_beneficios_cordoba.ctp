<?php 
$jubilados = $this->requestAction('/pfyj/personas/get_datos_padron_jubilados/' . $documento);
$activos = $this->requestAction('/pfyj/personas/get_datos_padron_gobierno/' . $documento);
?>
<?php if(!empty($jubilados)):?>
	<div class="areaDatoForm">
		<h3>INFORME DE BENEFICIOS EN CAJA DE JUBILACIONES DE LA PCIA. DE CORDOBA</h3>
			<table>
				<tr>
					<th>DOCUMENTO</th>
					<th>NOMBRE</th>
					<th>TIPO</th>
					<th>LEY</th>
					<th>BENEFICIO</th>
					<th>SUB-BENEFICIO</th>
					<th>LUGAR</th>
<!--					<th>FECHA NACIMIENTO</th>-->
					<th>PORCENTAJE</th>
				</tr>
				<?php foreach($jubilados as $jubilado):?>
					<tr>
						<td align="center"><?php echo $jubilado['PadronJubiladoCba']['documento']?></td>
						<td><?php echo $jubilado['PadronJubiladoCba']['apenom']?></td>
						<td align="center"><?php echo $jubilado['PadronJubiladoCba']['tipo']?></td>
						<td align="center"><?php echo $jubilado['PadronJubiladoCba']['ley']?></td>
						<td align="center"><?php echo $jubilado['PadronJubiladoCba']['beneficio']?></td>
						<td align="center"><?php echo $jubilado['PadronJubiladoCba']['sub_beneficio']?></td>
						<td align="center"><?php echo $jubilado['PadronJubiladoCba']['lugar']?></td>
<!--						<td><?php echo $jubilado['PadronJubiladoCba']['nacimiento']?></td>-->
						<td align="right"><?php echo $jubilado['PadronJubiladoCba']['porcentaje']?></td>
					</tr>
					<?php //   debug($jubilado)?>
				<?php endforeach;?>
			</table>
	</div>
<?php endif;?>
<?php if(!empty($jubilados)):?>
	<div class="areaDatoForm">
		<h3>INFORME GOBIERNO PCIA. CORDOBA</h3>
			<table>
				<tr>
					<th>DOCUMENTO</th>
					<th>NOMBRE</th>
					<th>INICIO</th>
					<th>BANCO</th>
					<th>SUCURSAL</th>
					<th>CUENTA</th>
					<th>REPARTICION</th>
				</tr>
			<?php foreach($activos as $activo):?>
				<tr>
					<td><?php echo $activo['PadronGobiernoCba']['sexo']?> - <?php echo $activo['PadronGobiernoCba']['documento']?></td>
					<td><?php echo $activo['PadronGobiernoCba']['apellido']?>, <?php echo $activo['PadronGobiernoCba']['nombre']?></td>
					<td align="center"><?php echo $activo['PadronGobiernoCba']['fecinicio']?></td>
					<td align="center"><?php echo $activo['PadronGobiernoCba']['banco']?></td>
					<td align="center"><?php echo $activo['PadronGobiernoCba']['agencia']?></td>
					<td align="center"><?php echo $activo['PadronGobiernoCba']['cuenta']?></td>
					<td align="center"><?php echo $activo['PadronGobiernoCba']['centropago']?></td>
				</tr>
				<?php //   debug($activo)?>
			<?php endforeach;?>
			</table>
	</div>
<?php endif;?>