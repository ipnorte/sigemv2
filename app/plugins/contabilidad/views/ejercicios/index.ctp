<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'DEFINICION DE EJERCICIOS CONTABLE'))?>
<?php 
$tabs = array(
				0 => array('url' => '/contabilidad/ejercicios/add','label' => 'Nuevo Ejercicio', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/contabilidad/ejercicios/imprimir_ejercicio','label' => 'Imprimir Ejercicios', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);			
?>

<table>

	<tr>
	
		<th>EJERCICIO</th>
		<th>CONCEPTO</th>
		<th>FECHA INICIAL</th>
		<th>FECHA FINAL</th>
		<th>FECHA CIERRE</th>
		<th>NIVEL</th>
		<th>DIG.N.1</th>
		<th>DIG.N.2</th>
		<th>DIG.N.3</th>
		<th>DIG.N.4</th>
		<th>DIG.N.5</th>
		<th>DIG.N.6</th>
		<th></th>
	</tr>

	<?php foreach($Ejercicios as $dato):
		?>
		<tr>
			<td><?php echo $dato['Ejercicio']['id']?></td>
			<td><?php echo $dato['Ejercicio']['descripcion']?></td>
			<td><?php echo $dato['Ejercicio']['fecha_desde']?></td>
			<td><?php echo $dato['Ejercicio']['fecha_hasta']?></td>
			<td><?php echo $dato['Ejercicio']['fecha_cierre']?></td>
			<td><?php echo $dato['Ejercicio']['nivel']?></td>
			<td><?php echo $dato['Ejercicio']['nivel_1']?></td>
			<td><?php echo $dato['Ejercicio']['nivel_2']?></td>
			<td><?php echo $dato['Ejercicio']['nivel_3']?></td>
			<td><?php echo $dato['Ejercicio']['nivel_4']?></td>
			<td><?php echo $dato['Ejercicio']['nivel_5']?></td>
			<td><?php echo $dato['Ejercicio']['nivel_6']?></td>
			<td class="actions"><?php echo $controles->getAcciones($dato['Ejercicio']['id'],false) ?></td>
		</tr>
	
	<?php endforeach;?>
</table>
