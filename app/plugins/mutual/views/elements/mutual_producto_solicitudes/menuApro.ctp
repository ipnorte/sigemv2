<?php 
$tabs = array(
				0 => array('url' => '/mutual/mutual_producto_solicitudes/pendientes_aprobar_opago','label' => 'OPERACIONES PARA APROBAR', 'icon' => 'controles/money.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/mutual_producto_solicitudes/pendientes_aprobar_opago','label' => 'OPERACIONES EN ANALISIS', 'icon' => 'controles/report_go.png','atributos' => array(), 'confirm' => null),
			);
//echo $cssMenu->menuTabs($tabs,false);			

$accion = (isset($accion) ? $accion : 'search_operaciones');

?>
<?php echo $form->create(null,array('id' => 'searchPersonaForm','action'=> $accion,'onsubmit' => "return fillDocumento('PersonaDocumento')"));?>
<div class="areaDatoForm">
    <h3>Búsqueda y/o Filtrado</h3>
    <hr/>
    <table class="tbl_form">
        <tr>
            <td>
                <?php echo $frm->number('MutualProductoSolicitud.numero',array('label'=>'Solicitud','size'=>7,'maxlength'=>7, 'value' => '')); ?>
            </td>
            <td>
                <?php echo $frm->number('Persona.documento',array('label'=>'Documento','size'=>8,'maxlength'=>8, 'value' => '')); ?>
            </td>
            <td>
                <?php echo $frm->input('Persona.apellido',array('label'=>'Apellido','size'=>30,'maxlength'=>30, 'value' => '')); ?>
            </td>
            <td><?php echo $frm->input('Persona.nombre',array('label'=>'Nombre/s','size'=>30,'maxlength'=>30, 'value' => '')); ?></td>
            <td>
                <input type="submit" class="btn_consultar" value="APROXIMAR" />
            </td>
            <td>
                <?php echo $frm->reset('searchPersonaForm',TRUE)?>
            </td>
        </tr>
    </table>
</div>
<?php echo $frm->hidden("MutualProductoSolicitud.search",array('value' => 1))?>
<?php echo $form->end();?>
