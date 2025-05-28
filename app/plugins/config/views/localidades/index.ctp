<?php echo $this->renderElement('head',array('title' => 'LOCALIDADES'))?>
<div class="actions"><?php echo $controles->botonGenerico('add','controles/add.png','Nueva Localidad')?></div>

<div class="areaDatoForm">
    <h3>Búsqueda de Localidades</h3>
    <?php echo $form->create(null,array('action'=> 'index'));?>
    <table class="tbl_form">
        <tr>
            <td>
            <?php echo $this->renderElement('localidad/combo_provincias',array(
                'plugin'=>'config',
                'model' => 'Localidad.provincia_id',
                'empty' => false,
                'selected' => (!empty($provincia_id) ? $provincia_id : ""),
                'label' => 'Provincia'
            ))?>                 
            </td>
            <td>
                <?php echo $frm->number('Localidad.cp',array('label'=>'CP','size'=>8,'maxlenght'=>8)); ?>
            </td>
            <td>
                <?php echo $frm->input('Localidad.nombre',array('label'=>'LOCALIDAD','size'=>50,'maxlenght'=>100)); ?>
            </td>
            <td>
                <input type="submit" class="btn_consultar" value="APROXIMAR" />
            </td>
        </tr>
    </table>
    <?php echo $form->end();?>
</div>

<?php // echo $this->renderElement('paginado')?>
<?php if(!empty($localidades)):?>
<table>

	<tr>
		<th>CP</th>
		<th>LOCALIDAD</th>
		<th>PROVINCIA</th>
		<th>LETRA</th>
		<th class="actions"></th>
	</tr>
<?php
$i = 0;
foreach ($localidades as $localidad):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>

<tr<?php echo $class;?>>
	<td><?php echo $localidad['Localidad']['cp']?></td>
	<td><?php echo $localidad['Localidad']['nombre']?></td>
	<td><?php echo $localidad['Provincia']['nombre']?></td>
	<td align="center"><?php echo $localidad['Provincia']['letra']?></td>
	<td class="actions"><?php echo $controles->getAcciones($localidad['Localidad']['id'],false) ?></td>
</tr>

<?php endforeach;?>
</table>
<?php endif;?>
<?php //   debug($localidades)?>
<?php // echo $this->renderElement('paginado')?>