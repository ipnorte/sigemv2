<?php echo $this->renderElement('head',array('title' => 'PROCESAR ARCHIVO CELESOL (BCO. CORDOBA)'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<h3>PROCESAR ARCHIVO CELESOL (BCO. CORDOBA)</h3>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'divide_celesol/DIVIDE','type' => 'file'))?>
	<table class="tbl_form">
            <tr>
            <td>ARCHIVO DEBITO AUTOMATICO (Desencriptado)</td>
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

<?php if(!empty($archivos)):?>
<h3>DETALLE DE ARCHIVOS</h3>
<table> 
	<tr>
		<th></th><th>Contenido</th><th>Registros</th><th>Enviado</th><th>Cobrado</th>
	</tr>
	<?php foreach($archivos as $clave => $archivo):?>
	<tr>
		<td><?php echo $controles->botonGenerico('/config/generador_diskette_bancos/divide_celesol/DIVIDE/'.$clave.'/1','controles/disk.png','',array('target' => '_blank'))?></td>
		<td><?php echo $archivo['LABEL']?></td>
		<td style="text-align: center;"><?php echo $archivo['COUNT']?></td>
		<td style="text-align: right;"><?php echo $util->nf($archivo['ENVIADO'])?></td>
		<td style="text-align: right; font-weight: bold;"><?php echo $util->nf($archivo['COBRADO'])?></td>
	</tr>
	<?php endforeach;?>
</table>
<?php // debug($archivos)?>
<?php endif; ?>


<h3>UNIFICAR ARCHIVOS CELESOL</h3>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'divide_celesol/UNIFICA/'. $UID_FILESU.'/0','type' => 'file'))?>
	<table class="tbl_form">
            <tr>
            <td>ARCHIVO DEBITO AUTOMATICO (Desencriptado)</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td><?php echo $frm->number('GeneradorDisketteBanco.nro_convenio_cba',array('label' => 'CONVENIO','maxlength' => 5,'size' => 5))?></td>            
            
            <td>
                <input type="submit" name="data[GeneradorDisketteBanco][procesar]" value="AGREGAR" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>

</div>

<?php if(!empty($files)):?> 
<h4>Unificar y encriptar archivos Banco Córdoba</h4>
<br>
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
        <?php echo $controles->botonGenerico('divide_celesol/UNIFICA/'.$UID_FILESU.'/1','controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
        </th>         
    </tr>
    
</table>

<?php endif; ?>