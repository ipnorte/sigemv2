<?php echo $this->renderElement('head',array('title' => 'UNIFICAR ARCHIVOS CRONOCRED'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
<h3>UNIFICAR ARCHIVOS CRONOCRED</h3> 
<hr/>
    <?php echo $frm->create(null,array('action' => 'unificar_cronocred/'. $UID,'type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>
                <input type="submit" name="data[GeneradorDisketteBanco][AGREGAR]" value="AGREGAR" />
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
        <?php echo $controles->botonGenerico('unificar_cronocred/'.$UID.'/1','controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
        </th>         
    </tr>
    
</table>

<?php endif; ?>