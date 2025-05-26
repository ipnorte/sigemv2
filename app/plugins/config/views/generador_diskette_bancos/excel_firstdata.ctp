<?php echo $this->renderElement('head',array('title' => 'FIRSTDATA'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<div class="areaDatoForm">
<h3>PROCESAR EXCEL FIRSTADA</h3>
<hr/>
<?php echo $frm->create(null,array('action' => 'excel_firstdata','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO EXCEL</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
        </tr>
		<tr>
			<td align="right">FECHA PRESENTACION</td>
			<td colspan="2">
			<?php echo $frm->calendar('GeneradorDisketteBanco.fecha_presentacion',null,null,date('Y')-1,date('Y')+1)?>			
			<?php //   echo $frm->number('GeneradorDisketteBanco.fecha_debito',array('maxlength' => 8,'size' => 8))?></td>
		</tr>            
		<tr>            
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
    	<th colspan="8">REGISTROS LEIDOS</th>
    </tr>
    <tr>
    	<th>REG</th>
    	<th>NOMBRE</th>
        <th>DOCUMENTO</th>
    	<th>MONTO</th>
        <th>TIPO</th>
        <th>TARJETA</th>
    	<th>VTO</th>
        <th>ESTADO</th>
    </tr>
    
		<?php $i = 0;?>
		<?php $ACUM = 0;?>
		<?php $ACUM_ERROR = $ACUM_OK = 0;?>
		<?php foreach($datos as $dato):?>
		
			<?php $ACUM += $dato['monto'];?>
			<?php if($dato['error'] == 1)$ACUM_ERROR++;?>
			<?php if($dato['error'] == 0)$ACUM_OK++;?>
			
			<?php $i++;?>
			
			<tr class="<?php echo ($dato['error'] == 1 ? "amarillo" : "")?>">
				<td align="center"><?php echo $i?></td>
				<td><?php echo utf8_encode($dato['nombre_completo'])?></td>
				<td><?php echo utf8_encode($dato['dni'])?></td>			
				<td align="right"><?php echo $util->nf($dato['monto'])?></td>
				<td><?php echo utf8_encode($dato['tipo_tarjeta'])?></td>
				<td><?php echo utf8_encode($dato['nro_tarjeta'])?></td>
				<td><?php echo $dato['primer_fecha_cobro']?></td>
				<td align="center"><?php echo $dato['error_msg']?></td>
			</tr>

		<?php endforeach;?>    
		
        <tr class="totales">
            <th colspan="3">IMPORTE TOTAL (STATUS = OK)</th>
			<th><?php echo $util->nf($ACUM)?></th>
            <th colspan="4"></th>
        </tr> 		
		<tr class="totales">
            <th colspan="5" style="text-align: left;">
				LEIDOS: <?php echo $i?> | 
				<span style="color: green;">OK: <?php echo $ACUM_OK?></span> | 
				<?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ALERTAS: $ACUM_ERROR</span>" : "")?>
			</th>
			<th colspan="3" style="text-align: right;">
			<?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
			</th>

		</tr>    
    
</table>		


<?php //debug($datos)?>

<?php endif; ?>