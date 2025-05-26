<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL ARCOFISA'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>PROCESAR EXCEL ARCOFISA</h3>
<hr/>
    <?php echo $frm->create(null,array('action' => 'excel_arcofisa2','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO ENVIADO POR ARCOFISA</td>
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
<?php if(!empty($datos)):?>
<table>
    <tr>
            <th colspan="9">REGISTROS LEIDOS</th>
    </tr>
    <tr>
            <th>LINEA</th>
            <th>NOMBRE REGISTRADO</th>
            <th>CUIT</th>
            <th>DNI</th>
            <th>FECHA DEBITO</th>
            <th>IMPORTE</th>
            <th>CODIGO</th>
            <th>CONCEPTO</th>
            <th>ERROR</th>
    </tr>    
    <?php $i = 0;?>
    <?php $ACUM = 0;?>
    <?php $ACUM_ERROR = $ACUM_OK = 0;?> 
    <?php foreach($datos as $dato):?>
        <?php if($dato['error'] == 0)$ACUM += $dato['importe'];?>
        <?php if($dato['error'] == 1)$ACUM_ERROR++;?>
        <?php if($dato['error'] == 0)$ACUM_OK++;?>
        <?php $i++;?>    
        <tr class="<?php echo ($dato['error'] ? "activo_0" : "")?>">
            <td align="center"><?php echo $i?></td>
            <td><?php echo utf8_encode($dato['apenom'])?></td>
            <td><?php echo $dato['cuit_cuil']?></td>
            <td><?php echo $dato['ndoc']?></td>
            <td style="text-align: center;"><?php echo $dato['fecha_debito']?></td>
            <td style="text-align: right;"><?php echo $util->nf($dato['importe'])?></td>
            <td style="text-align: center;"><?php echo $dato['codigo']?></td>
            <td><?php echo $dato['codigo_concepto']?></td>
            <td align="center"><span style="color: <?php echo ($dato['error'] ? "red" : "green")?>;font-weight: bold;"><?php echo $dato['error_msg']?></span></td>
        </tr>
    <?php endforeach;?>
    <tr class="totales">
         <th colspan="8">IMPORTE TOTAL (STATUS = OK)</th>
         <th><?php echo $util->nf($ACUM)?></th>
     </tr> 
     <tr class="totales">
         <th colspan="6" style="text-align: left;">
            LEIDOS: <?php echo $i?> | 
            <span style="color: green;">OK: <?php echo $ACUM_OK?></span> | 
            <?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ERRORES: $ACUM_ERROR</span>" : "")?>             
         </th>
        <th colspan="3" style="text-align: right;">
        <?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
        </th>         
     </tr>     
</table>
<?php // debug($datos)?>

<?php endif; ?>
