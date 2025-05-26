<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL SICON'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
<h3>GENERAR EXCEL SICON</h3> 
<hr/>
    <?php echo $frm->create(null,array('action' => 'excel_sicon/XLS_TO_XLS','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO XLS (Listado Control)</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>LOTE</td>
            <td><?php echo $frm->input('GeneradorDisketteBanco.nro_lote',array('maxlength' => 2,'size' => 2,'value' => 1))?></td>
            <td colspan="2">
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="GENERAR XLS" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>

</div>