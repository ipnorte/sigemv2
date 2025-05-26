<?php echo $this->renderElement('head',array('title' => 'BANCO COINAG - UNIFICAR ARCHIVOS Y GENERAR ZIP'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>BANCO COINAG - UNIFICAR ARCHIVOS Y GENERAR ZIP</h3>
<hr/>
    <?php echo $frm->create(null,array('action' => 'zip_coinag/'.$UID,'type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td colspan="2">
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="ANEXAR" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>    
</div>

<?php if(!empty($files)):?>

<table>
    <tr>
        <th>ARCHIVO</th>
        <th>REGISTROS</th>
    </tr>
    <?php foreach($files as $file):?>
    <tr>
        <td><?php echo $file['file']['name']?></td>
        <td style="text-align: center;"><?php echo count($file['registros'])?></td>
    </tr>
    
    <?php endforeach;?>
    <tr class="totales">
        <th colspan="2" style="text-align: right;">
        <?php echo $controles->botonGenerico('zip_coinag/'.$UID.'/1','controles/zip.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
        </th>         
    </tr>
    
</table>




<?php endif; ?>