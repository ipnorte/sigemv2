<?php echo $this->renderElement('head',array('title' => 'PROCESAR ARCHIVO BANCO NACION'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>PROCESAR ARCHIVO BANCO NACION</h3>
    <hr/>
    <?php echo $frm->create(null,array('action' => 'bna/'. $UID,'type' => 'file'))?>
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