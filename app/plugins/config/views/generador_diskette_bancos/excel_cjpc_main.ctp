<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL CAJA DE JUBILACIONES DE CORDOBA'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>PROCESAR ARCHIVO CONSUMOS/SERVICIOS CAJA DE JUBILACIONES</h3>
    <hr/>
    <?php echo $frm->create(null,array('action' => 'excel_cjpc_main/'. $UID,'type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>
                <input type="submit" name="data[GeneradorDisketteBanco][PROCESAR]" value="PROCESAR" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>    
</div>



<?php if(!empty($diskettes)):?>
<table>
    <tr>
        <th>Tipo</th><th>Registros</th><th>Archivo</th><th></th>
    </tr>
    <?php foreach ($diskettes as $key => $value):?>
    <tr>
        <td style="text-align: center; font-weight: bold;"><?php echo $key?></td>
        <td style="text-align: center;"><?php echo count($value['registros'])?></td>
        <td><?php echo $value['archivo']?></td>
        <td><?php echo $controles->botonGenerico('/config/generador_diskette_bancos/excel_cjpc_main/'.$value['uuid'],'controles/disk.png','',array('target' => '_blank'))?></td>
    </tr>
    <?php endforeach;?>
</table>
<?php endif; ?>
