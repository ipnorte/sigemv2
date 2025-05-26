<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL BANCO DE COMERCIO'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>PROCESAR EXCEL BANCO DE COMERCIO</h3>
<hr/>
    <?php echo $frm->create(null,array('action' => 'excel_bcocomer','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO</td>
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
<h4>DETALLE DE REGISTROS LEIDOS</h4>
<table>
    <tr>
        <th>#</th>
        <th>Fe Vto Orig</th>
        <th>Fe Envio</th>
        <th>Fe Recibo</th>
        <th>Cod Empresa</th>
        <th>Nom Empresa</th>
        <th>Nro Op</th>
        <th>Cuota</th>
        <th>Intento</th>
        <th>Importe</th>
        <th>Cliente</th>
        <th>Respuesta</th>
        <th>Desc Rechazo</th>
        <th>CBU</th>
    </tr>
    <?php $ACUM = 0;?>
    <?php foreach($datos as $dato):?>
    <?php $ACUM += $dato['importe'];?>
    <tr>
        <td><?php echo $dato['linea']?></td>
        <td><?php echo $dato['fecha_vto_orig']?></td>
        <td><?php echo $dato['fecha_envio']?></td>
        <td><?php echo $dato['fecha_recibo']?></td>
        <td><?php echo $dato['cod_empresa']?></td>
        <td><?php echo $dato['nom_empresa']?></td>
        <td><?php echo $dato['nro_op']?></td>
        <td><?php echo $dato['cuota']?></td>
        <td><?php echo $dato['intento']?></td>
        <td style="text-align: right;"><?php echo $util->nf($dato['importe'])?></td>
        <td><?php echo $dato['cliente']?></td>
        <td><?php echo $dato['respuesta']?></td>
        <td><?php echo $dato['desc_rechazo']?></td>
        <td><?php echo $dato['cbu']?></td>
    </tr>
    
    <?php endforeach;?> 
    <tr class="totales">
        <th colspan="9" style="text-align: right;">TOTAL COBRADO</th>
        <th style="text-align: right;"><?php echo $util->nf($ACUM)?></th>
        <th colspan="5" style="text-align: right;">
        <?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
        </th>         
    </tr>
</table>


<?php endif; ?>
