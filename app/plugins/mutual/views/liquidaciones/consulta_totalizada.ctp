<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: LISTADO DE LIQUIDACIONES EMITIDAS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php //debug($liquidaciones);exit;?>
<table class="tbl_grilla">
	<tr>
		<th></th>
		<th></th>
		<th>EXPO</th>
		<th>IMPO</th>
		<th>LIQ.</th>
		<th>ORGANISMO</th>
		<th>TOTAL LIQUIDAD0</th>
                <th>A DEBITAR</th>
		<th>DEBITADO</th>
		<!--<th>NO COBRADO</th>-->
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
        <?php $ACUM_SALDO_ACTUAL = $ACUM_IMPO_DTO = $ACUM_IMPO_DEB = $ACUM_IMPO_IMPU = $ACUM_IMPO_REINT = 0;?>
        <?php $PRIMERO = TRUE;?>
	<?php foreach($liquidaciones as $liquidacion):?>
        
            <?php // echo $ACUM_IMPO_IMPU;?>
        
		<?php $organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,2);?>
        
		<?php if($periodo != $liquidacion['Liquidacion']['periodo']):
				$periodo = $liquidacion['Liquidacion']['periodo'];
		?>
                <?php if(!$PRIMERO):?>
        <tr>
            <th colspan="6" style="text-align: right;">TOTALES</th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_SALDO_ACTUAL)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_DTO)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_DEB)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_IMPU)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_REINT)?></th>
            <th colspan="7"></th>
        </tr>
                <?php endif;?>
                <?php $ACUM_SALDO_ACTUAL = $ACUM_IMPO_DTO = $ACUM_IMPO_DEB = $ACUM_IMPO_IMPU = $ACUM_IMPO_REINT = 0;?>
                <?php $PRIMERO = FALSE;?>
		<tr>
			<th colspan="15" style="font-size:16px;background-color: #e2e6ea;border:0">
			<h4 style="text-align: left;color:#000000;">
			<?php echo $util->periodo($liquidacion['Liquidacion']['periodo'],true)?></h4>
			</th>
                        <th style="font-size:16px;background-color: #e2e6ea;border:0"><?php // echo $controles->botonGenerico('/mutual/liquidaciones/notificacion/'.$liquidacion['Liquidacion']['periodo'],'controles/email.png')?></th>
                        <th style="font-size:16px;background-color: #e2e6ea;border:0"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reporte_proveedores/0/0/0/0/'.$liquidacion['Liquidacion']['periodo'],'controles/book.png')?></th>
                        
		</tr>
                

                
		
<!--		<tr>
			<td colspan="16" style="text-align: right;">
				<?php // echo ($organismo != 66 ? $controles->botonGenerico('/mutual/liquidaciones/exportar_masivo/'.$liquidacion['Liquidacion']['periodo'],'controles/disk.png','Exportar Lote General') : '')?>
				<?php // echo $controles->botonGenerico('/mutual/liquidaciones/importar_masivo/'.$liquidacion['Liquidacion']['periodo'],'controles/database_save.png','Importar Lote General')?>
			</td>
		</tr>-->
		
		<?php endif;?>
                
		<?php $ACUM_SALDO_ACTUAL += $liquidacion['Liquidacion']['saldo_actual'];?>
                <?php $ACUM_IMPO_DTO += $liquidacion['Liquidacion']['importe_dto'];?>
                <?php $ACUM_IMPO_DEB += $liquidacion['Liquidacion']['importe_debitado'];?>
                <?php $ACUM_IMPO_IMPU += $liquidacion['Liquidacion']['imputado'];?>
                <?php $ACUM_IMPO_REINT += $liquidacion['Liquidacion']['reintegros'];?>                
                
                
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
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/resumencontrol/'.$liquidacion['Liquidacion']['periodo'].'/'.$liquidacion['Liquidacion']['codigo_organismo'],'controles/pdf.png','',array('target' => '_blank'))?></td>
			<td align="center"><?php echo ($organismo != 66 ? $controles->botonGenerico('/mutual/liquidaciones/exportar/'.$liquidacion['Liquidacion']['id'],'controles/disk.png') : '')?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'],'controles/database_save.png')?></td>
			<td align="center">#<?php echo $liquidacion['Liquidacion']['id']?></td>
			<td><?php echo $liquidacion['Liquidacion']['concepto_1']?><?php // echo $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['saldo_actual'])?></td>
                        <td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['importe_dto'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['importe_debitado'])?></td>
			<!--<td align="right"><?php // echo $util->nf($liquidacion['Liquidacion']['importe_nodebitado'])?></td>-->
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['imputado'])?></td>
			<td align="right"><?php echo $util->nf($liquidacion['Liquidacion']['reintegros'])?></td>
			<td align="center"><?php if(!empty($liquidacion['Liquidacion']['fecha_imputacion'])) echo $util->armaFecha($liquidacion['Liquidacion']['fecha_imputacion'])?></td>
			<td align="center"><?php echo $liquidacion['Liquidacion']['nro_recibo']?></td>
			<td align="center"><?php echo ($liquidacion['Liquidacion']['archivos_procesados'] == 1 ? $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion/'.$liquidacion['Liquidacion']['id'],'controles/arrow_switch.png') : '')?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reporte_proveedores/'.$liquidacion['Liquidacion']['id'],'controles/book.png')?></td>
			<td align="center"><?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? $controles->botonGenerico('/mutual/liquidacion_socios/recupero_cartera/'.$liquidacion['Liquidacion']['id'],'controles/money_add.png') : '')?></td>
            <td align="center"><?php echo ($liquidacion['Liquidacion']['scoring'] == 1 ? $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'],'controles/calendar_2.png') : '')?></td>
		</tr>
	<?php endforeach;?>
                
        <tr>
            <th colspan="6" style="text-align: right;">TOTALES</th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_SALDO_ACTUAL)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_DTO)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_DEB)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_IMPU)?></th>
            <th style="text-align: right;"><?php echo $util->nf($ACUM_IMPO_REINT)?></th>
            <th colspan="7"></th>
        </tr>                
</table>
<?php //debug($liquidaciones)?>