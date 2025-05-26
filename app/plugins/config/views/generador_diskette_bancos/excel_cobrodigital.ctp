<?php echo $this->renderElement('head',array('title' => 'PROCESAR ARCHIVO COBRO DIGITAL'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<div class="areaDatoForm">
<h3>GENERA ARCHIVO EXCEL COBRO DIGITAL</h3>

    <?php echo $frm->create(null,array('action' => 'excel_cobrodigital/TXT_TO_XLS','type' => 'file'))?>
	<table class="tbl_form">
         <tr>
            <td>ARCHIVO TEXTO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
        </tr>
		<tr>
			<td align="right">FECHA DEBITO (AAAAMMDD)</td>
			<td colspan="2">
			<?php echo $frm->calendar('GeneradorDisketteBanco.fecha_debito',null,null,date('Y')-1,date('Y')+1)?>			
			<?php //   echo $frm->number('GeneradorDisketteBanco.fecha_debito',array('maxlength' => 8,'size' => 8))?></td>
		</tr>
                <tr>
                    <td>Registros por Archivo Excel</td>
                    <td><input name="data[GeneradorDisketteBanco][registros_xls]" type="text" size="5" maxlenght="5" value="150" class="input_number" onkeypress="return soloNumeros(event)" maxlength="11" value="" id="GeneradorDisketteBancoRegistrosXls" /></td>
                </tr>        
        <tr>
            <td colspan="2">
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="GENERAR ARCHIVO EXCEL" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>


</div>


<div class="areaDatoForm">
    <h3>PROCESA ARCHIVO EXCEL COBRO DIGITAL</h3>
    <?php echo $frm->create(null,array('action' => 'excel_cobrodigital/XLS_TO_TXT','type' => 'file'))?>
	<table class="tbl_form">
         <tr>
            <td>ARCHIVO EXCEL</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
        </tr>
		<tr>
			<td align="right">FECHA DEBITO (AAAAMMDD)</td>
			<td colspan="2">
			<?php echo $frm->calendar('GeneradorDisketteBanco.fecha_debito',null,null,date('Y')-1,date('Y')+1)?>			
			<?php // echo $frm->number('GeneradorDisketteBanco.fecha_debito',array('maxlength' => 8,'size' => 8))?></td>
		</tr>        
        <tr>
            <td colspan="2">
                <input type="submit" name="data[GeneradorDisketteBanco][GENERAR]" value="GENERAR ARCHIVO TXT" />
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
			
			<th>NOMBRE s/EXCEL</th>
                        <th>NOMBRE REGISTRADO</th>
			<th>CUIT</th>
                        <th>DNI</th>
                        <th>FECHA DEBITO</th>
			<th>CONCEPTO</th>
			<th>IMPORTE</th>
                        <th>ESTADO</th>
			
		</tr>
		<?php $i = 1;?>
		<?php $ACUM = 0;?>
		<?php $ACUM_ERROR = $ACUM_OK = 0;?>
		<?php foreach($datos as $dato):?>
		
			<?php if($dato['error'] == 0)$ACUM += $dato['importe'];?>
			<?php if($dato['error'] == 1)$ACUM_ERROR++;?>
			<?php if($dato['error'] == 0)$ACUM_OK++;?>
			
			<?php $i++;?>
			
                <tr class="<?php echo ($dato['error'] == 1 ? "activo_0" : "")?>">
				<td align="center"><?php echo $i?></td>
				
                                <td><?php echo utf8_encode($dato['apenom'])?></td>
                                <td <?php echo ($dato['error_nombre'] == 1 ? "style='color:red;'" : "")?>><strong><?php echo utf8_encode($dato['apenom_sigem'])?></strong> <?php echo ($dato['error_nombre'] == 1 && $dato['error'] == 0 ? " *** NOMBRE DISTINTO ***'" : "")?></td>
                                <td><?php echo $dato['cuit_cuil']?></td>
                                <td><?php echo $dato['ndoc']?></td>
                                
				<td align="center"><?php echo $util->armaFecha($dato['fecha_debito'])?></td>
                                <td><?php echo $dato['concepto']?></td>
				<td align="right"><?php echo $util->nf($dato['importe'])?></td>
				<td align="center"><span style="color: <?php echo ($dato['error'] == 1 ? "red" : "green")?>;font-weight: bold;"><?php echo $dato['estado']?></span></td>
			
			</tr> 
        <?php endforeach;?>
        <tr class="totales">
            <th colspan="7">IMPORTE TOTAL (STATUS = OK)</th>
			<th><?php echo $util->nf($ACUM)?></th>
            <th></th>
        </tr>    
		<tr class="totales">
            <th colspan="6" style="text-align: left;">
				LEIDOS: <?php echo $i?> | 
				<span style="color: green;">OK: <?php echo $ACUM_OK?></span> | 
				<?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ERRORES: $ACUM_ERROR</span>" : "")?>
			</th>
			<th colspan="3" style="text-align: right;">
			<?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID. '/1','controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
			</th>

		</tr>            
</table>
<?php endif; ?>

