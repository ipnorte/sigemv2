<?php echo $this->renderElement('head',array('title' => 'PROCESAR ARCHIVO ZENRISE'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>

<h3>PROCESA ARCHIVO EXCEL ZENRISE</h3>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'excel_zenrise','type' => 'file'))?>
	<table class="tbl_form">
        <tr>
            <td>ARCHIVO ENVIADO POR ZENRISE</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
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
			<th colspan="14">REGISTROS LEIDOS</th>
		</tr>
		<tr>
			<th>NOMBRE COMPLETO</th>
			<th>EMAIL</th>
			<th>DESCRIPCIÓN</th>
			<th>REF. EXT. CONTACTO</th>
			<th>REF. EXT. FACTURA</th>
			<th>MONTO A PAGAR</th>
      <th>MONTO PAGADO</th>
      <th>MEDIO DE PAGO</th>
      <th>1ra. FECHA VENCIMIENTO</th>
      <th>2da. FECHA VENCIMIENTO</th>
      <th>FECHAS DE PAGO</th>
      <th>FECHA ESTIMADA TRANSFERENCIA</th>
      <th>NOMBRE DE GRUPOS</th>
      <th>ESTADO</th>
		</tr>
		<?php $i = 0;?>
		<?php $ACUM = 0;?>
		<?php $ACUM_ERROR = $ACUM_OK = 0;?>
		<?php foreach($datos as $dato):?>

			<?php if($dato['error'] == 0)$ACUM += $dato['monto_a_pagar'];?>
			<?php if($dato['error'] == 1)$ACUM_ERROR++;?>
			<?php if($dato['error'] == 0)$ACUM_OK++;?>

			<?php $i++;?>

			<tr>
				<td><?php echo $dato['nombre_completo']?></td>
				<td><?php echo $dato['email']?></td>
				<td><?php echo $dato['descripcion']?></td>
        <td><?php echo $dato['ref_ext_contacto']?></td>
        <td><?php echo $dato['ref_ext_factura']?></td>
				<td align="right"><?php echo $util->nf($dato['monto_a_pagar'])?></td>
        <td align="right"><?php echo $util->nf($dato['monto_pagado'])?></td>
				<td><?php echo $dato['medio_pago']?></td>
        <td><?php echo $dato['primera_fecha_venc']?></td>
        <td><?php echo $dato['segunda_fecha_venc']?></td>
        <td><?php echo $dato['fechas_pago']?></td>
        <td><?php echo $dato['fecha_transferencia']?></td>
        <td><?php echo $dato['nombre_grupos']?></td>
        <td><?php echo $dato['estado']?></td>

			</tr>
        <?php endforeach;?>
        <tr class="totales">
            <th colspan="13">IMPORTE TOTAL (STATUS = OK)</th>
			<th><?php echo $util->nf($ACUM)?></th>
        </tr>
		<tr class="totales">
            <th colspan="4" style="text-align: left;">
				LEIDOS: <?php echo $i?> |
				<span style="color: green;">OK: <?php echo $ACUM_OK?></span> |
				<?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ERRORES: $ACUM_ERROR</span>" : "")?>
			</th>
			<th colspan="10" style="text-align: right;">
				<?php if($ACUM_ERROR == 0):?>
					<?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
				<?php endif;?>
			</th>

		</tr>
</table>
<?php endif; ?>
