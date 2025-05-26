<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'))?>
<?php if(!empty($socio)) echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'plugin' => 'pfyj','link' => ($linkToPadronPersona == 1 ? true : false)))?>
<?php if(!empty($orden)):?>
<div class="areaDatoForm3">
	<?php // debug($orden)?>
	<h4>ORDEN DE DESCUENTO 
		<?php if($detalle==0):?> 
			<?php echo $controles->linkModalBox('#'.$orden['OrdenDescuento']['id'],array('title' => 'ORDEN DE DESCUENTO #' . $orden['OrdenDescuento']['id'],'url' => '/mutual/orden_descuentos/view/'.$orden['OrdenDescuento']['id'].'/'.$orden['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 650))?>
		<?php else:?>
			<?php echo '#'.$orden['OrdenDescuento']['id']?>
		<?php endif;?>
		<?php if($orden['OrdenDescuento']['permanente'] == 1):?>
			- PERMANENTE
		<?php endif;?>
		<?php if($orden['OrdenDescuento']['reprogramada'] == 1):?>
			(REP.)
		<?php endif;?>
	</h4>
	<div class="row">
		REF: <strong><?php echo $orden['OrdenDescuento']['tipo_orden_dto']?>
		#<?php echo $orden['OrdenDescuento']['numero']?> </strong>
		&nbsp;|&nbsp;FECHA ORDEN DTO.: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['fecha'])?></strong>
	</div>
	<div class="row">
		PRODUCTO: <strong><?php echo $orden['OrdenDescuento']['proveedor_producto']?></strong>
	</div>
	<div class="row">
		BENEFICIO: <strong><?php echo $orden['OrdenDescuento']['beneficio_str']?><?php //   echo $this->requestAction('/pfyj/persona_beneficios/view/'.$orden['OrdenDescuento']['persona_beneficio_id'])?></strong>
	</div>
	<div class="row">
		INICIA: <strong><?php echo $util->periodo($orden['OrdenDescuento']['periodo_ini'])?></strong>
		<?php //   echo ($orden['OrdenDescuento']['permanente'] == 1 ? ' (PERMANENTE)' : '')?>
		&nbsp;	&nbsp;
		VTO 1er CUOTA: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_socio'])?></strong>
		&nbsp;	&nbsp;
		VTO PROVEEDOR: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_proveedor'])?></strong>
	</div>
        <div class="row">
            SOLICITADO: <strong><?php echo number_format($orden['OrdenDescuento']['importe_solicitado'],2)?></strong>
            &nbsp;	&nbsp;
            IMPORTE CUOTA: <strong><?php echo number_format($orden['OrdenDescuento']['importe_cuota'],2)?></strong>
            <?php echo ($orden['OrdenDescuento']['permanente'] == 0 ? ' ('.$orden['OrdenDescuento']['cuotas']. ' CUOTAS) ' : ' (MENSUAL)')?>
            <?php if($orden['OrdenDescuento']['permanente'] == 0):?>
                    &nbsp;	&nbsp;	
                    IMPORTE TOTAL: <strong><?php echo number_format($orden['OrdenDescuento']['importe_total'],2)?></strong>
            <?php endif;?>
	</div>
	<?php if($orden['OrdenDescuento']['permanente'] == 1 && $orden['OrdenDescuento']['activo'] == 0):?>
	<div class="areaDatoForm2">
		<strong style="color:red;">BAJA</strong> 
		<?php if(!empty($orden['OrdenDescuento']['periodo_hasta'])):?>
		(A PARTIR DE <strong><?php echo $util->periodo($orden['OrdenDescuento']['periodo_hasta'],true)?></strong>)
		<?php endif;?>
	</div>
	<?php endif;?>
	<?php if($orden['OrdenDescuento']['activo'] == 0 && $orden['OrdenDescuento']['nueva_orden_descuento_id'] == 0):?>
                <div style="background-color: red; color:white;font-weight: bold;padding: 2px;">ANULADA &nbsp;
                    <span style="font-weight: normal;">(<?php echo $orden['OrdenDescuento']['modified'] ?>)</span>
                </div>
	<?php endif;?>
	<?php if($orden['OrdenDescuento']['activo'] == 1 && $orden['OrdenDescuento']['anterior_orden_descuento_id'] != 0):?>
		<div style="background-color: green; color:white;padding: 2px;margin-top: 5px;">
			<strong>EMITIDA POR NOVACION</strong> | ORDEN DE DESCUENTO ANTERIOR:
			<strong><?php echo $controles->linkModalBox("#".$orden['OrdenDescuento']['anterior_orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $orden['OrdenDescuento']['anterior_orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$orden['OrdenDescuento']['anterior_orden_descuento_id'].'/'.$orden['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 750))?></strong>
		</div>
	<?php endif;?>
	<?php if($orden['OrdenDescuento']['activo'] == 0 && $orden['OrdenDescuento']['anterior_orden_descuento_id'] != 0):?>
		<div style="color:green;padding: 2px;margin-top: 5px;">
			<strong>EMITIDA POR NOVACION</strong> | ORDEN DE DESCUENTO ANTERIOR:
			<strong><?php echo $controles->linkModalBox("#".$orden['OrdenDescuento']['anterior_orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $orden['OrdenDescuento']['anterior_orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$orden['OrdenDescuento']['anterior_orden_descuento_id'].'/'.$orden['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 750))?></strong>
		</div>
	<?php endif;?>						
	<?php if($orden['OrdenDescuento']['activo'] == 0 && $orden['OrdenDescuento']['nueva_orden_descuento_id'] != 0):?>
		<div style="background-color: red; color:white;padding: 2px;margin-top: 5px;">
			<strong>ANULADA POR NOVACION</strong> | ORDEN DE DESCUENTO NUEVA:
			<strong><?php echo $controles->linkModalBox("#".$orden['OrdenDescuento']['nueva_orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $orden['OrdenDescuento']['nueva_orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$orden['OrdenDescuento']['nueva_orden_descuento_id'].'/'.$orden['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 750))?></strong>
			<br/>
			<?php echo $orden['OrdenDescuento']['motivo_novacion']?>
		</div>
	<?php endif;?>		
	<div class="row">Created: <?php if(!empty($orden['OrdenDescuento']['user_created'])){echo $orden['OrdenDescuento']['user_created'] . ' | ' . $orden['OrdenDescuento']['created'];}?></div>
	<div class="row">Modified: <?php if(!empty($orden['OrdenDescuento']['user_modified'])){echo $orden['OrdenDescuento']['user_modified'] . ' | ' . $orden['OrdenDescuento']['modified'];}?></div>

</div>

<?php if(!empty($orden['OrdenDescuento']['observaciones'])):?>
	<div class="areaDatoForm2" style="font-size: 10px;">
		<?php echo $orden['OrdenDescuento']['observaciones']?>
	</div>
<?php endif;?>

<?php if( isset($orden['OrdenDescuento']['resumen']) && !empty($orden['OrdenDescuento']['resumen'])):?>
<div class="areaDatoForm" style="font-size: 10px;">
    <h3>Resúmen Analítico a <strong><?php echo $util->periodo($orden['OrdenDescuento']['resumen']['periodo_corte'],TRUE)?></strong></h3>
    <table class="tbl_form">
        <tr>
            <th>Total</th>
            <th>Devengado</th>
            <th>Cobrado</th>
            <th>Saldo Conciliado</th>
            <th>Acreditación Pendiente</th>
            <th>Saldo a Conciliar</th>
            <th>Cuotas Adeudadas</th>
            <th>No Vencido</th>
            <th>Cuotas No Vencidas</th>
            
        </tr>
        <tr>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['importe_total'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['devengado'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['cobrado'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['pendiente_acreditar'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo_aconciliar'],2)?></strong></td>
            <td style="text-align: center;"><strong><?php echo $orden['OrdenDescuento']['resumen']['cuotas_vencidas']?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo_avencer'],2)?></strong></td>
            <td style="text-align: center;"><strong><?php echo $orden['OrdenDescuento']['resumen']['cuotas_avencer']?></strong></td>
        </tr>
    </table>
    <h3>Distribución del Saldo Adeudado (Antiguedad de la Deuda)</h3>
    <table class="tbl_form">
        <tr>
            <th>Saldo</th>
            <th>0 a 3 Meses</th>
            <th>3 a 6 Meses</th>
            <th>6 a 9 Meses</th>
            <th>9 a 12 Meses</th>
            <th>mas de 12 Meses</th>
        </tr>
        <tr>
            <td style="text-align: right;" class="success"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo_0003'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo_0306'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo_0609'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo_0912'],2)?></strong></td>
            <td style="text-align: right;"><strong><?php echo number_format($orden['OrdenDescuento']['resumen']['saldo_1213'],2)?></strong></td>
        </tr>
    </table>
<?php // debug($orden)?>    
</div>


<?php endif;?>



<?php if($detalle==1 && $orden['OrdenDescuento']['nueva_orden_descuento_id'] == 0):?>
		<div class="areaDatoForm3">
			<h4>DETALLE DE CUOTAS</h4>
			<p>&nbsp;</p>
			<?php echo $this->renderElement('orden_descuento_cuotas/grilla_cuotas',array('plugin'=>'mutual','orden_descuento_id' => $orden['OrdenDescuento']['id']))?>
		</div>
	
<?php endif;?>
<?php else:?>
	<div class="notices_error"><strong>ERROR:</strong> LA ORDEN DE DESCUENTO <strong>#<?php echo $id?></strong> NO EXISTE!.</div>
	
<?php endif;?>