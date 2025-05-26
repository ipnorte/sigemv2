<?php echo $this->renderElement('head',array('title' => 'PROCESAR EXCEL CAJA DE JUBILACIONES DE CORDOBA'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <h3>PROCESAR ARCHIVO CONSUMOS/SERVICIOS CAJA DE JUBILACIONES</h3>
    <hr/>
    <?php echo $frm->create(null,array('action' => 'excel_cjpc/'. $UID,'type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>
                <input type="submit" name="data[GeneradorDisketteBanco][PROCESAR]" value="PROCESAR" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>    
</div>

<?php if(!empty($datos)):?>
<table>
    <tr>
        <th colspan="11">REGISTROS LEIDOS</th>
    </tr>
    <tr>
        <th colspan="8">INFORMADO POR LA CAJA</th>
        <th colspan="3">INFORMACION EXISTENTE SIGEM</th>
    </tr>    
    <tr>
        <th>LINEA</th>
        <th>BENEFICIO</th>
        <th>DOCUMENTO</th>
        <th>NOMBRE</th>
        <th>COD-DTO</th>
        <th>OPERACION</th>
        <th>DEBITADO</th>
        <th>SALDO</th>
        <th>SOLICITUD</th>
        <th>IMPORTE CUOTA</th>
        <th>ERROR</th>        
    </tr>
    <?php $ACUM = 0;?>
    <?php $ACUM_ERROR = $ACUM_OK = 0;?>    
    <?php foreach($datos as $dato):?>
        <?php if($dato['error'] == 0)$ACUM += $dato['importe_debitado'];?>
        <?php if($dato['error'] == 1)$ACUM_ERROR++;?>
        <?php if($dato['error'] == 0)$ACUM_OK++;?>    
        <tr <?php echo ($dato['error'] == 1 ? " class='activo_0' style='color:red;font-weight: bold;'" : "")?>>
            <td style="text-align: center;"><?php echo $dato['linea']?></td>
            <td style="text-align: center;"><?php echo $dato['beneficio_str']?></td>
            <td style="text-align: center;"><?php echo $dato['documento']?></td>
            <td><?php echo $dato['apenom']?></td>
            <td style="text-align: center;"><?php echo $dato['codigo_dto'].$dato['sub_codigo']?></td>
            <td style="text-align: center;"><?php echo $dato['orden_descuento_id']?></td>
            <td style="text-align: right;font-weight: bold;"><?php echo $util->nf($dato['importe_debitado'])?></td>
            <td style="text-align: right;background-color: #FFE6B0;"><?php echo $util->nf($dato['saldo_operacion_informado'])?></td>
            <td style="text-align: center;background-color: #FFE6B0;"><?php echo $dato['norden_descuento_id']?></td>
            <td style="text-align: right;background-color: #FFE6B0;"><?php echo $util->nf($dato['importe_cuota'])?></td>
            <td style="text-align: center;background-color: #FFE6B0;"><?php echo $dato['error_msg']?></td>
        </tr>
    
    <?php endforeach;?>
        <tr class="totales">
            <th colspan="6">IMPORTE TOTAL (STATUS = OK)</th>
			<th><?php echo $util->nf($ACUM)?></th>
                        <th colspan="4"></th>
        </tr>    
		<tr class="totales">
            <th colspan="7" style="text-align: left;">
				LEIDOS: <?php echo $i?> | 
				<span style="color: green;">OK: <?php echo $ACUM_OK?></span> | 
				<?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ERRORES: $ACUM_ERROR</span>" : "")?>
			</th>
			<th colspan="4" style="text-align: right;">
			<?php echo $controles->botonGenerico('/config/generador_diskette_bancos/excel_cjpc/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
			</th>

		</tr>         
</table>
<?php // debug($datos)?>

<?php endif; ?>
