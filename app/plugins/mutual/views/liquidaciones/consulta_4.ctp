<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda_new',array('plugin'=>'mutual'))?>
<table class="tbl_grilla">
	<tr>
		<th></th>
		<th></th>
		<th>EXPO</th>
		<th>IMPO</th>
		<th>LIQ.</th>
		<th>ORGANISMO</th>
		<th>TOTAL LIQUIDAD0</th>
		<th>COBRADO</th>
		<th>NO COBRADO</th>
		<th>IMPUTADO</th>
		<th>REINTEGROS</th>
		<th>FECHA</th>
		<th>COMPROBANTE</th>
		<th>CRUCE</th>
		<th>IMPUTACION</th>
		<th></th>
        <th></th>        
	</tr>
	<?php $periodo = null;?>
	<?php $ACUM_TOTAL = 0;?>
	<?php foreach($liquidaciones as $liquidacion):?>
		<?php $organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,2);?>
		<?php $ACUM_TOTAL += $liquidacion['Liquidacion']['total'];?>
		<?php if($periodo != $liquidacion['Liquidacion']['periodo']):
                    $periodo = $liquidacion['Liquidacion']['periodo'];
		?>
                    <tr>
                            <th colspan="16" style="font-size:16px;background-color: #e2e6ea;border:0">
                            <h4 style="text-align: left;color:#000000;">
                            <?php echo $util->periodo($liquidacion['Liquidacion']['periodo'],true)?></h4>
                            <th colspan="16" style="font-size:16px;background-color: #e2e6ea;border:0"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reporte_proveedores/0/0/0/0/'.$liquidacion['Liquidacion']['periodo'],'controles/book.png')?></th>
                            </th>
                    </tr>
		<?php endif;?>
		<tr class="activo_<?php echo $liquidacion['Liquidacion']['imputada']?>">
			<td align="center">
				<?php 
//				if($liquidacion['Liquidacion']['imputada'] == 1) echo $html->image('controles/lock.png');
				if($liquidacion['Liquidacion']['imputada'] == 0 && $liquidacion['Liquidacion']['bloqueada'] == 1 && $liquidacion['Liquidacion']['asincrono_id'] != 0) echo $controles->btnModalBox(array('img' => 'ajax-loader.gif','url' => '/shells/asincronos/job/' . $liquidacion['Liquidacion']['asincrono_id'], 'h' => 250, 'w' => 750 , 'title' => 'ESTADO DEL PROCESO #' . $liquidacion['Liquidacion']['asincrono_id']));
				if($liquidacion['Liquidacion']['imputada'] == 0 && $liquidacion['Liquidacion']['bloqueada'] == 0){
//					echo $html->image('controles/lock_unlock.png');
					echo $controles->btnAjaxToggleOnOff('cierre_liquidacion/'.$liquidacion['Liquidacion']['id'],$liquidacion['Liquidacion']['cerrada'],"CERRAR LA LIQUIDACION?","ABRIR LA LIQUIDACION?","controles/lock.png","controles/lock_unlock.png");
				}
				?>
			</td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/listados/reporte_liquidacion_deuda/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png','',array('target' => '_blank'))?></td>
			<td align="center"><?php echo ($organismo != 66 ? $controles->botonGenerico('/mutual/liquidaciones/exportar/'.$liquidacion['Liquidacion']['id'],'controles/disk.png') : '')?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/importar_nuevo/'.$liquidacion['Liquidacion']['id'],'controles/database_save.png')?></td>
			<td align="center">#<?php echo $liquidacion['Liquidacion']['id']?></td>
			<td><?php echo $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['total'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['importe_cobrado'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['importe_no_cobrado'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['importe_imputado'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['importe_reintegro'])?></td>
			<td align="center"><?php if(!empty($liquidacion['Liquidacion']['fecha_imputacion'])) echo $util->armaFecha($liquidacion['Liquidacion']['fecha_imputacion'])?></td>
			<td align="center"><?php echo $liquidacion['Liquidacion']['nro_recibo']?></td>
			<td align="center"><?php echo ($liquidacion['Liquidacion']['archivos_procesados'] == 1 ? $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion/'.$liquidacion['Liquidacion']['id'],'controles/arrow_switch.png') : '')?></td>
			<td align="center"><?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? $controles->botonGenerico('/mutual/liquidaciones/reporte_proveedores/'.$liquidacion['Liquidacion']['id'],'controles/book.png') : '')?></td>
			<td align="center"><?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? $controles->botonGenerico('/mutual/liquidacion_socios/recupero_cartera/'.$liquidacion['Liquidacion']['id'],'controles/money_add.png') : '')?></td>
            <td align="center"><?php echo ($liquidacion['Liquidacion']['scoring'] == 1 ? $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'],'controles/calendar_2.png') : '')?></td>
		</tr>
	<?php endforeach;?>
</table>
