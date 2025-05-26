<?php echo $this->renderElement('head',array('title' => 'ENCRIPTADOR ARCHIVO DEBITO AUTOMATICO BANCO CORDOBA'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<h3>ENCRIPTADOR ARCHIVO DEBITO AUTOMATICO BANCO CORDOBA</h3>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'encriptar_bcocba','type' => 'file'))?>
	<table class="tbl_form">
            <tr>
                <td>ACCION</td>
                <td><?php echo $frm->input('GeneradorDisketteBanco.accion',array('type' => 'select','options' => array('ENCRIPTAR' => 'ENCRIPTAR','DESENCRIPTAR' => 'DESENCRIPTAR', 'NORMALIZAR' => 'NORMALIZAR'),'label'=>''));?></td>
            <td>ARCHIVO DEBITO AUTOMATICO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>
                <input type="submit" name="data[GeneradorDisketteBanco][procesar]" value="PROCESAR" />
                <!--<input type="submit" value="ENCRIPTAR" name="data[GeneradorDisketteBanco][ENCRIPTAR]"/>--> 
                <!--&nbsp;&nbsp;&nbsp;-->
                <!--<input type="submit" name="data[GeneradorDisketteBanco][DESENCRIPTAR]" value="DESENCRIPTAR" />-->
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>

</div>

<?php if(!empty($datos)):?>

<h3>DETALLE DE ARCHIVOS GENERADOS POR EL DESENCRIPTADOR</h3>
<table>
    <tr>
        <th></th>
        <th>LIQ</th>
        <th>ARCHIVO</th>
        <th>REGISTROS</th>
    </tr>
    <?php $registros = 0;?>
    <?php foreach($datos as $lid => $file):?>
    <?php $registros += $file['lineas'];?>
    <tr>    
        <td><?php echo $controles->botonGenerico('/config/generador_diskette_bancos/encriptar_bcocba/'.$file['uuid'],'controles/disk.png','',array('target' => '_blank'))?></td>
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

<?php // debug($datos);?>

<?php endif; ?>
