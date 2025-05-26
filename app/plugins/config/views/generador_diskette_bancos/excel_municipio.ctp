<?php echo $this->renderElement('head',array('title' => 'PROCESAR ARCHIVO MUNICIPIO'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<h3>PROCESA ARCHIVO EXCEL MUNICIPIO</h3>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'excel_municipio','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO ENVIADO POR EL MUNICIPIO</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
        </tr>
		<tr>
			<td align="right">FECHA DEBITO (AAAAMMDD)</td>
			<td colspan="2">
			<?php echo $frm->calendar('GeneradorDisketteBanco.fecha_debito',null,null,null,date('Y')+1)?>			
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
			<th colspan="6">REGISTROS LEIDOS</th>
		</tr>
		<tr>
			<th>LINEA</th>
			<th>DOCUMENTO</th>
			<th>NOMBRE</th>
			<th>FECHA DEBITO</th>
			<th>IMPORTE</th>
			<th>STATUS</th>
			
		</tr>
		<?php $i = 0;?>
		<?php $ACUM = 0;?>
		<?php $ACUM_ERROR = $ACUM_OK = 0;?>
		<?php foreach($datos as $dato):?>
		
			<?php if($dato['error'] == 0)$ACUM += $dato['importe'];?>
			<?php if($dato['error'] == 1)$ACUM_ERROR++;?>
			<?php if($dato['error'] == 0)$ACUM_OK++;?>
			
			<?php $i++;?>
			
			<tr>
				<td align="center"><?php echo $i?></td>
				<td><?php echo $dato['ndoc']?></td>
				<td><?php echo $dato['apenom']?></td>
				<td align="center"><?php echo $util->armaFecha($dato['fecha_debito'])?></td>
				<td align="right"><?php echo $util->nf($dato['importe'])?></td>
				<td align="center"><span style="color: <?php echo ($dato['error'] == 1 ? "red" : "green")?>;font-weight: bold;"><?php echo $dato['status']?></span></td>
			
			</tr> 
        <?php endforeach;?>
        <tr class="totales">
            <th colspan="4">IMPORTE TOTAL (STATUS = OK)</th>
			<th><?php echo $util->nf($ACUM)?></th>
            <th></th>
        </tr>    
		<tr class="totales">
            <th colspan="3" style="text-align: left;">
				LEIDOS: <?php echo $i?> | 
				<span style="color: green;">OK: <?php echo $ACUM_OK?></span> | 
				<?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ERRORES: $ACUM_ERROR</span>" : "")?>
			</th>
			<th colspan="3" style="text-align: right;">
				<?php if($ACUM_ERROR == 0):?>
					<?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
				<?php endif;?>
			</th>

		</tr>            
</table>
<?php endif; ?>
