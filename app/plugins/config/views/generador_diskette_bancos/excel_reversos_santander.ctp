<?php echo $this->renderElement('head',array('title' => 'EXCEL REVRESOS SANTANDER RIO'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<div class="areaDatoForm">
<h3>PROCESAR EXCEL REVERSOS SANTANDER RIO</h3>
<hr/>
    <?php echo $frm->create(null,array('action' => 'excel_reversos_santander','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO EXCEL</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>           
            <td>
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="GENERAR TXT" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>

</div>