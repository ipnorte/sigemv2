<?php echo $this->renderElement('head',array('title' => 'BANCO COINAG - UNIFICAR ARCHIVOS Y GENERAR ZIP'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>BANCO COINAG - UNIFICAR ARCHIVOS Y GENERAR ZIP</h3>
<hr/>
    <?php echo $frm->create(null,array('action' => 'zip_coinag/'.$UID.'/0/1','type' => 'file'))?>
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



<div class="areaDatoForm">
<h3>DIVIDIR ARCHIVOS BANCO COINAG</h3> 
<hr/>
    <?php echo $frm->create(null,array('action' => 'zip_coinag/'. $UID . '/0/2','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>
                <input type="submit" name="data[GeneradorDisketteBanco][AGREGAR]" value="DIVIDIR" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>    
</div>


<?php if(!empty($files)):?>
    <?php if($tipo == 1):?>
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

    <?php if($tipo == 2):?>

        <table>
            <tr>
                <th></th>
                <th>LIQ</th>
                <th>ARCHIVO</th>
                <th>REGISTROS</th>
            </tr>
            <?php $registros = 0;?>
            <?php // debug($files);?>
            <?php foreach($files as $lid => $file):?>
            <?php $registros += $file['lineas'];?>
            <tr>    
                <td><?php echo $controles->botonGenerico('zip_coinag/'.$UID.'/1/2/' . $lid,'controles/disk.png','',array('target' => '_blank'))?></td>
                <td style="font-weight: bold;"><?php echo $lid?></td>
                <td><?php echo $file['archivo']?></td>
                <td style="text-align: center;"><?php echo $file['lineas']?></td>
            </tr>
            <?php endforeach;?>
            <tr class="subtotales">
                <th colspan="3">Total Registros Le&iacute;dos</th>
                <th><?php echo $registros?></th>
            </tr>    
        </table>    


    <?php endif; ?>



<?php endif; ?>