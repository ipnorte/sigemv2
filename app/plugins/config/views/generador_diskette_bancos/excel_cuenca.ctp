<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL CUENCA'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>PROCESAR EXCEL CUENCA</h3>
<hr/>
    <?php echo $frm->create(null,array('action' => 'excel_cuenca','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO ENVIADO POR CUENCA</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td colspan="2">
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="PROCESAR" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>    
</div>
<?php if(!empty($files)):?>
<div class="areaDatoForm3">
    <h3>SUBDIVISION DEL LOTE :: DETALLE DE ARCHIVOS GENERADOS</h3>

    <table>
        <tr>
            <th></th>
            <th>LIQ</th>
            <th>ARCHIVO</th>
            <th>REGISTROS</th>
        </tr>
        <?php $registros = 0;?>
        <?php foreach($files as $lid => $file):?>
        <?php $registros += $file['lineas'];?>
        <tr>    
            <td><?php echo $controles->botonGenerico('/config/generadorDisketteBancos/excel_cuenca'.'/'.$file['uuid'],'controles/disk.png','',array('target' => '_blank'))?></td>
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

        <?php // debug($files)?>


</div>
<?php endif;?>
