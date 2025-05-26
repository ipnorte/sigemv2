<?php echo $this->renderElement('head',array('title' => 'ADMINISTRACION DE SERVICIOS :: Alta Servicio','plugin' => 'config'))?>

<?php echo $this->renderElement("proveedor/datos_proveedor",array('plugin' => 'proveedores', 'proveedor_id' => $proveedor_id))?>
<h3>ALTA NUEVO SERVICIO DEL PROVEEDOR</h3>
<script type="text/javascript">
    function validateForm(){
        return true;
    }
</script>

    <?php echo $frm->create(null,array('action' => 'add/'.$proveedor_id,'id' => 'NuevoServicio','onsubmit' => "return validateForm();"))?>
<div class="areaDatoForm">
    <table class="tbl_form">
        <tr>
            <td>Tipo Servicio</td>
            <td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'MutualServicio.tipo_producto',
																			'prefijo' => 'MUTUCORG',
																			'disabled' => false,
																			'empty' => false,
																			'metodo' => "get_tipo_producto_servicios",
			))?>                
                
            </td>
        </tr>
        <tr>
            <td>Día Corte</td>
            <td><?php echo $frm->number('MutualServicio.dia_corte',array('label'=>'')); ?></td>
        </tr>
        <tr>
            <td>Meses Antes</td>
            <td><?php echo $frm->number('MutualServicio.meses_antes_dia_corte',array('label'=>'')); ?></td>
        </tr>
        <tr>
            <td>Meses Desp&uacute;es</td>
            <td><?php echo $frm->number('MutualServicio.meses_despues_dia_corte',array('label'=>'')); ?></td>
        </tr>        
    </table>
</div>
<?php echo $frm->hidden('MutualServicio.proveedor_id',array('value' => $proveedor_id)); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_servicios/servicios_by_proveedor/'.$proveedor_id))?>