<?php echo $this->renderElement('head',array('title' => 'ANULAR ORDEN DE DESCUENTO','plugin' => 'config'))?>
<?php echo $this->renderElement('orden_descuento/form_search_by_numero',array('accion' => 'anular_orden','plugin' => 'mutual','orden_descuento_id' => $orden['OrdenDescuento']['id']))?>
<?php if(!empty($orden)):?>
<?php echo $this->renderElement('socios/apenom',array('socio_id'=>$orden['OrdenDescuento']['socio_id'],'plugin' => 'pfyj'))?>
<div class="areaDatoForm3">
	<?php //   debug($orden)?>
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
		<div style="background-color: red; color:white;font-weight: bold;padding: 2px;">ANULADA</div>
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


</div>

<?php if(!empty($orden['OrdenDescuento']['observaciones'])):?>
	<div class="areaDatoForm2" style="font-size: 10px;">
		<?php echo $orden['OrdenDescuento']['observaciones']?>
	</div>
<?php endif;?>

<?php // debug($orden)?>
<?php // debug($solicitud)?>

<div class="areaDatoForm3">
    <h4>DETALLE DE CUOTAS</h4>
    <p>&nbsp;</p>
    	<table>
            <tr>
                <th>TIPO / NUMERO</th>
                <th>PERIODO</th>
                <th>PROVEEDOR - PRODUCTO</th>
                <th>CUOTA</th>
                <th>CONCEPTO</th>
                <th>ESTADO</th>
                <th>SITUACION</th>
                <th>VENCIMIENTO</th>
                <th>IMPORTE</th>
                <th>SALDO</th>
                <th></th>
            </tr>
            <?php foreach ($cuotas as $cuota):?>
            <?php
            $bloqueo = array();
            if(!empty($cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'])) $bloqueo = $cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'];
            ?>
            <tr id="LTR_<?php echo $orden['OrdenDescuento']['id']?>_<?php echo $cuota['OrdenDescuentoCuota']['id']?>" class="<?php echo $cuota['OrdenDescuentoCuota']['estado']?>">
                <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
                <td><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
                <td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
                <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
                <td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
                <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
                <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
                <td align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></td>
                <td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['importe'])?></td>
                <td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['saldo_cuota'])?></td>
                <td>
                    <?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
                        <span style="color: red;"><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
                    <?php endif;?>
                </td>

            </tr>
            <?php endforeach;?>
        </table>
<?php //   debug($cuotas)?>
</div>
<div class="areaDatoForm2">
<?php echo $frm->create(null,array('action' => 'anular_orden','id' => 'FormAnular_' . $orden['OrdenDescuento']['id'], 'onsubmit' => "return confirm('".($orden['OrdenDescuento']['activo'] == 0 ? 'ACTIVAR' : 'ANULAR')." LA ORDEN DE DESCUENTO #".$orden['OrdenDescuento']['id']."')"))?>
<table class="tbl_form">
    <?php if($orden['OrdenDescuento']['activo'] == 1):?>
    <tr>
        <td>MOTIVO DE ANULACION</td>
					<td>
	            <?php echo $this->renderElement('global_datos/combo',array(
	                    'plugin'=>'config',
	                    'label' => '.',
	                    'model' => 'OrdenDescuentoCuota.situacion',
	                    'prefijo' => 'MUTUSICU',
	                    'disable' => false,
	                    'empty' => false,
	                    'selected' => '0',
	                    'logico' => true,
	            ))?>
					</td>
    </tr>


		<?php if ($orden["OrdenDescuento"]['permanente'] == 1): ?>
			<tr>
				<td>A PARTIR DE</td>
					<td>


					<?php echo $frm->input('OrdenDescuento.periodo_hasta',array('type' => 'select', 'options' => $periodos))?>
					</td>
				</td>
			</tr>
		<?php endif; ?>
    <tr>
        <td valign="top">OBSERVACIONES</td>
				<td><?php echo $frm->textarea('OrdenDescuentoCuota.observaciones',array('cols' => 60, 'rows' => 10))?></td>
    </tr>
    <?php endif;?>
    <tr>
        <td colspan="2">
            <?php if(empty($solicitud)):?>
            <?php echo $frm->submit(($orden['OrdenDescuento']['activo'] == 0 ? 'ACTIVAR' : 'ANULAR').' ORDEN #' . $orden['OrdenDescuento']['id'])?>
            <?php else:?>
                <div class="areaDatoForm3">
                    <h4>ORDEN DE CONSUMO / SERVICIO ::
                    <?php echo $solicitud['MutualProductoSolicitud']['tipo_orden_dto']?> #<?php echo $solicitud['MutualProductoSolicitud']['id']?>
                    <?php if($solicitud['MutualProductoSolicitud']['sin_cargo'] == 1) echo " *** SIN CARGO ***"?>
                    </h4>
                    <div classs="row">
                            FECHA CARGA: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?>
                            &nbsp;|&nbsp;FECHA PAGO: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha_pago'])?>
                            &nbsp;|&nbsp;ESTADO: <strong><?php echo ($solicitud['MutualProductoSolicitud']['aprobada'] == 1 && $solicitud['MutualProductoSolicitud']['anulada'] == 0 ? 'APROBADA' : ($solicitud['MutualProductoSolicitud']['anulada'] == 1 ? "<span style='color:red;'>ANULADA</span>" : 'EMITIDA'))?></strong>
                    </div>
                    <div classs="row">
                            INICIA EN: <strong><?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?></strong>
                            &nbsp;|&nbsp;1er. VTO SOCIO: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['primer_vto_socio'])?></strong>
                    </div>
                    <div classs="row">
                            IMPORTE CUOTA: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_cuota'],2);?></strong>
                            <?php echo ($solicitud['MutualProductoSolicitud']['permanente'] == 0 ? ' ('.$solicitud['MutualProductoSolicitud']['cuotas']. ' CUOTAS) ' : ' (PERMANENTE)')?>
                            <?php if($solicitud['MutualProductoSolicitud']['permanente'] == 0):?>
                            &nbsp;|&nbsp;TOTAL: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_total'],2);?></strong>
                            <?php endif;?>
                    </div>
                </div>
                <?php if($orden['OrdenDescuento']['activo'] == 0):?>
                <?php // if($orden['OrdenDescuento']['activo'] == 0 && $solicitud['MutualProductoSolicitud']['anulada'] == 0):?>
                    <?php echo $frm->submit('ACTIVAR ORDEN #' . $orden['OrdenDescuento']['id'])?>
                <?php endif;?>

                <?php // if($orden['OrdenDescuento']['activo'] == 0 && $solicitud['MutualProductoSolicitud']['anulada'] == 1):?>
                    <!--<div class="notices_error"><strong>NO SE PUEDE ACTIVAR ESTA ORDEN</strong> Porque la Solicitud <?php // echo $controles->linkModalBox("#".$solicitud['MutualProductoSolicitud']['id'],array('title' => 'ORDEN DE CONSUMO #' . $solicitud['MutualProductoSolicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$solicitud['MutualProductoSolicitud']['id'].'/1','h' => 450, 'w' => 850))?> asociada a esta Orden se encuentra ANULADA.</div>-->
                <?php // endif;?>
                <?php // if($orden['OrdenDescuento']['activo'] == 1 && $solicitud['MutualProductoSolicitud']['anulada'] == 0):?>
                    <!--<div class="notices_error"><strong>NO SE PUEDE ANULAR ESTA ORDEN</strong> Porque la Solicitud <?php // echo $controles->linkModalBox("#".$solicitud['MutualProductoSolicitud']['id'],array('title' => 'ORDEN DE CONSUMO #' . $solicitud['MutualProductoSolicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$solicitud['MutualProductoSolicitud']['id'].'/1','h' => 450, 'w' => 850))?> asociada a esta Orden se encuentra VIGENTE. Deber&aacute; ANULAR la Solicitud.</div>-->
                <?php // endif;?>

                <?php if($orden['OrdenDescuento']['activo'] == 1):?>
                    <?php // if($orden['OrdenDescuento']['activo'] == 1 && $solicitud['MutualProductoSolicitud']['anulada'] == 1):?>
                    <?php echo $frm->submit('ANULAR ORDEN #' . $orden['OrdenDescuento']['id'])?>
                <?php endif;?>

            <?php endif;?>
        </td>
    </tr>
</table>
<?php echo $frm->hidden('OrdenDescuentoCuota.orden_descuento_id',array('value' => $orden['OrdenDescuento']['id']));?>
<?php echo $frm->hidden('OrdenDescuentoCuota.ANULAR_ORDEN',array('value' => 1));?>
<?php echo $frm->hidden('OrdenDescuentoCuota.orden_activa',array('value' => $orden['OrdenDescuento']['activo']));?>
<?php echo $frm->end();?>

</div>


<?php endif; ?>
<?php //   debug($orden)?>
