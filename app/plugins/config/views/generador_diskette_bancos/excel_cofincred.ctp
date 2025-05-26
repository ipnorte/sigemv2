<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL COFINCRED'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    
    <h3>PROCESAR EXCEL COFINCRED</h3>
    <?php echo $frm->create(null,array('action' => 'excel_cofincred','type' => 'file'))?>
    
	<table class="tbl_form">
        <tr>
            <td>Excel EIV</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>Nro.Archivo (1,2,3...,N)</td>
            <td><?php echo $frm->number('GeneradorDisketteBanco.nro_archivo_banco_nacion',array('maxlength' => 2,'size' => 2, 'value' => 1))?></td>
            <td align="right">Fecha D&eacute;bito</td>
            <td><?php echo $frm->calendar('GeneradorDisketteBanco.fecha_debito',null,null,null,date('Y')+1)?>	</td>
            <td><input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="PROCESAR" /></td>
            
        </tr>
	</table>    
    
    <?php echo $frm->end()?> 
    
</div>

<?php if(!empty($datos)):?>
<h3>Registros Le&iacute;dos</h3>
<table>
    
    
    <tr>
        
        <th>#</th>
        <th>Socio</th>
        <th>NDoc</th>
        <th>Nombre</th>
        <th>CBU</th>
        <th>Sucursal</th>
        <th>Cuenta</th>
        <th>Importe</th>
        
    </tr>
    
    <?php $ACUM = 0;?>
    <?php foreach ($datos as $value): ?>
    <?php $ACUM += $value['importe'];?>
    <tr>
        <td><?php echo $value['ln']?></td>
        <td><?php echo $value['socio']?></td>
        <td><?php echo $value['ndoc']?></td>
        <td><?php echo $value['nombre']?></td>
        <td><?php echo $value['cbu']?></td>
        <td><?php echo $value['sucursal']?></td>
        <td><?php echo $value['cuenta']?></td>
        <td align="right"><?php echo $util->nf($value['importe'])?></td>
    </tr>
    
    <?php endforeach;?>
    <tr class="totales">
        
        <th colspan="3" style="text-align: left;">
            <?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
        </th>        
        
        <th colspan="4">IMPORTE TOTAL</th>
        <th><?php echo $util->nf($ACUM)?></th>        
        
    </tr>
    
</table>


<?php endif; ?>
