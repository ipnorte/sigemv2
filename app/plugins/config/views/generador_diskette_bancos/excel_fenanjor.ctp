<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL FENANJOR'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<div class="areaDatoForm">
<h3>PROCESAR EXCEL FENANJOR</h3>
<hr/>
    <?php echo $frm->create(null,array('action' => 'excel_fenanjor/XLS_TO_TXT','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO EXCEL</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>
                <?php echo $frm->input('GeneradorDisketteBanco.tipo_reporte',array('type' => 'select','options' => array(1 => 'BANCO NACION',2 => 'OTROS BANCOS'),'label'=>'FORMATO'));?>
            </td>
            
            <td colspan="2">
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="GENERAR TXT" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>

</div>

<div class="areaDatoForm">
<h3>GENERAR EXCEL FENANJOR</h3> 
<hr/>
    <?php echo $frm->create(null,array('action' => 'excel_fenanjor/TXT_TO_XLS','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO DE TEXTO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td colspan="2">
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="GENERAR XLS" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>

</div>

<?php if(!empty($datos)):?>
<h4>DETALLE DE REGISTROS LEIDOS</h4>
<table>
    <tr>
        <th>#</th>
        <th>LIN</th>
        <th>ORGANISMO</th>
        <th>SOCIO #</th>
        <th>DOCUMENTO</th>
        <th>NOMBRE</th>
        <th>SUCURSAL</th>
        <th>CUENTA</th>
        <th>FECHA DEBITO</th>
        <th>IMPORTE</th>
        <th>ESTADO</th>
    </tr>
    <?php $i = 0;?>
    <?php $ACUM = 0;?>
    <?php $ACUM_ERROR = $ACUM_OK = 0;?>
    
    <?php foreach($datos as $dato):?>
    <?php if($dato['indica_pago'] == 1)$ACUM += $dato['importe'];?>
    <?php if($dato['error'] == 1)$ACUM_ERROR++;?>
    <?php if($dato['error'] == 0)$ACUM_OK++;?>
    <?php $i++;?>    
    <tr class="<?php echo ($dato['error'] == 1 ? "activo_0" : "")?>">
        <td align="center"><?php echo $i?></td>
        <td align="center"><?php echo $dato['renglon']?></td>
        <td style="font-weight: bold;"><?php echo $dato['codigo_organismo_descripcion']?></td>
        <td><?php echo $dato['socio_id']?></td>
        <td><?php echo $dato['ndoc']?></td>
        <td><?php echo utf8_encode($dato['apenom'])?></td>
        <td><?php echo $dato['nro_sucursal']?></td>
        <td><?php echo $dato['nro_cta_banco']?></td>
        <td align="center"><?php echo $util->armaFecha($dato['fecha_debito'])?></td>
        <td align="right"><?php echo $util->nf($dato['importe'])?></td>
        <td align="left"><span style="color: <?php echo ($dato['indica_pago'] == 0 ? "red" : "green")?>;font-weight: bold;"><?php echo $dato['estado_descripcion']?></span></td>
    </tr>
    
    <?php endforeach;?>   
    <tr class="totales">
        <th colspan="9">IMPORTE TOTAL (STATUS = OK)</th>
                    <th><?php echo $util->nf($ACUM)?></th>
        <th></th>
    </tr> 
    <tr class="totales">
    <th colspan="8" style="text-align: left;">
                    LEIDOS: <?php echo $i?> | 
                    <span style="color: green;">OK: <?php echo $ACUM_OK?></span> | 
                    <?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ERRORES: $ACUM_ERROR</span>" : "")?>
            </th>
            <th colspan="5" style="text-align: right;">
                
                <?php if(!empty($diskettes)):?>
                
                <table class="tbl_form">
                    
                    <?php foreach($diskettes as $diskette):?>
                    
                        <?php // debug($diskette['archivo'])?>
                    <tr><td>
                            <?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$diskette['uid'],'controles/disk.png',$diskette['archivo'],array('target' => 'blank','style' => 'color:black;'))?>
                        </td></tr>   
                    <?php endforeach;?>
                
                <?php endif;?>
                </table>
                
            <?php // echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO ** BANCO NACION **',array('target' => 'blank','style' => 'color:black;'))?>
            </th>

    </tr>     
    
</table>


<?php // debug($datos);?>

<?php endif; ?>
