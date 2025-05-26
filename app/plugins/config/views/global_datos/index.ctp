<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DE DATOS GLOBALES'))?>

<div class="actions">
	<?php echo $controles->botonGenerico('add'.'/'.$ln_s.'/'.$pref_s,'controles/add.png','Nuevo Dato Global')?>
	<?php if($ln_s > 2):?>
		&nbsp;|&nbsp;
		<?php echo $controles->btnUp('Nivel Anterior','/config/global_datos/index/'.$ln_a.'/'.$pref_a)?>
	<?php endif;?>
</div>




<?php //   echo $this->renderElement('paginado')?>

<?php if($ln_s > 2):?>
<h3><?php echo $pref_s . ' :: ' . $this->requestAction('/config/global_datos/valor/'.$pref_s)?></h3>
<?php endif;?>
<table>

	<tr>
	
		<th>CODIGO</th>
		<th>CONCEPTO I</th>
		<th>CONCEPTO II</th>
		<th>CONCEPTO III</th>
		<th>CONCEPTO IV</th>
		<th>LOGICO I</th>
		<th>LOGICO II</th>
        <th>LOGICO III</th>
		<th>ENTERO I</th>
		<th>ENTERO II</th>
		<th>DECIMAL I</th>
		<th>DECIMAL II</th>
		<th>FECHA I</th>
		<th>FECHA II</th>
		<th></th>	
	</tr>

<?php
$i = 0;
foreach ($datos as $dato):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>	

	<tr<?php echo $class;?>>
<!--		<td><?php echo $dato['GlobalDato']['id'] ?></td>-->
		<td><?php echo ($ln_s == 4 ? $dato['GlobalDato']['id'] : '<strong>'.$html->link($dato['GlobalDato']['id'],'index/'.$ln_s.'/'.$dato['GlobalDato']['id']) . '</strong>')  ?></td>
		<td><?php echo $dato['GlobalDato']['concepto_1']?></td>
		<td><?php echo $dato['GlobalDato']['concepto_2']?></td>
		<td><?php echo $dato['GlobalDato']['concepto_3']?></td>
		<td><?php echo $dato['GlobalDato']['concepto_4']?></td>
		<td style="text-align: center;"><?php echo $dato['GlobalDato']['logico_1']?></td>
		<td style="text-align: center;"><?php echo $dato['GlobalDato']['logico_2']?></td>
        <td style="text-align: center;"><?php echo $dato['GlobalDato']['logico_3']?></td>
		<td style="text-align: center;"><?php echo $dato['GlobalDato']['entero_1']?></td>
		<td style="text-align: center;"><?php echo $dato['GlobalDato']['entero_2']?></td>
		<td style="text-align: center;"><?php echo $dato['GlobalDato']['decimal_1']?></td>
		<td style="text-align: center;"><?php echo $dato['GlobalDato']['decimal_2']?></td>
		<td><?php echo $dato['GlobalDato']['fecha_1']?></td>
		<td><?php echo $dato['GlobalDato']['fecha_2']?></td>
		<td class="actions"><?php echo $controles->getAcciones($dato['GlobalDato']['id'].'/'.$ln_s.'/'.$pref_s,false) ?></td>
	</tr>

<?php endforeach;?>

</table>

<?php //   debug($datos)?>

<?php // echo $this->renderElement('paginado')?>