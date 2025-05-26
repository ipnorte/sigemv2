<?php echo $this->renderElement('head',array('title' => 'ACTUALIZACION MASIVA DE IMPORTES :: ORDENES DE DESCUENTO ','plugin' => 'config'))?>

<div class="areaDatoForm">
		<?php echo $frm->create(null,array('action' => 'actualizar_importe','id' => 'frm_modifi_impo'))?>
		<table class="tbl_form">
			<tr>
				<td>PRODUCTO</td><td>
                <?php 
                        echo $this->renderElement('mutual_productos/combo_productos_permanentes',
                            array(
                                    'plugin' => 'mutual',
                                    'selected' => (isset($this->data['MutualProductoSolicitud']['tipo_producto_mutual_producto_id']) ? $this->data['MutualProductoSolicitud']['tipo_producto_mutual_producto_id'] : null),
                                )
                        )
                ?></td>
			</tr>
			<tr>
				<td>ORGANISMO</td>
				<td>
				<?php echo $this->renderElement('global_datos/combo_global',array(
																				'plugin'=>'config',
																				'metodo' => "get_organismos",
																				'model' => 'MutualProductoSolicitud.codigo_organismo',
																				'empty' => false,
				))?>				
				</td>
			</tr>
			<tr>
				<td>PERIODO</td><td><?php echo $frm->periodo('MutualProductoSolicitud.periodo_corte','',null,date('Y') + 1)?></td>
			</tr>
			<tr>
				<td>VALOR ACTUAL</td><td><?php echo $frm->money("MutualProductoSolicitud.actual","",(isset($this->data['MutualProductoSolicitud']['actual']) ? $this->data['MutualProductoSolicitud']['actual'] : 0 ))?></td>
			</tr>			
			<tr>
				<td>NUEVO VALOR</td><td><?php echo $frm->money("MutualProductoSolicitud.nuevo","",(isset($this->data['MutualProductoSolicitud']['nuevo']) ? $this->data['MutualProductoSolicitud']['nuevo'] : 0 ))?></td>
			</tr>			
			<tr>
				<td>NOVAR ORDEN</td><td><input type="checkbox" name="data[MutualProductoSolicitud][novar]" value="1" id="MutualProductoSolicitudNovar"/></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $frm->submit("CARGAR ORDENES")?><td>
			</tr>
		</table>
		<?php echo $frm->hidden("MutualProductoSolicitud.header",array('value' => 1))?>
		<?php echo $frm->end()?>
</div>

<?php if($showGrilla != 0):?>

	<h3>LISTADO DE ORDENES</h3>
	
	<?php if(!empty($ordenes)):?>
	
		<?php echo $frm->create(null,array('action' => 'actualizar_importe','id' => 'frm_modifi_impo','onsubmit' => "return confirm('".($novar == 1 ? "NOVAR ORDENES Y " : "")."ACTUALIZAR VALORES?')"))?>
		
		<table>
		
			<tr>
				<th>#</th>
				<th>TIPO / NUMERO</th>
				<th>SOCIO</th>
				<th>COSTO ACT.</th>
				<th>UNIDADES</th>
				<th>IMPORTE</th>
				<th>COSTO NVO</th>
				<th>NUEVO IMP</th>
				
			</tr>
			<?php foreach($ordenes as $orden):?>
			
				<tr>
					<td><?php echo $orden['OrdenDescuento']['id']?></td>
					<td><strong><?php echo $orden['OrdenDescuento']['tipo_numero']?></strong></td>
					<td><?php echo $orden['OrdenDescuento']['persona_documento']?> - <?php echo $orden['OrdenDescuento']['persona_apenom']?></td>
					<td align="right"><?php echo $util->nf($orden['OrdenDescuento']['valor_unidad_actual'])?></td>
					<td align="center"><strong><?php echo $orden['OrdenDescuento']['unidades']?></strong></td>
					<td align="right"><strong><?php echo $util->nf($orden['OrdenDescuento']['importe_actual'])?></strong></td>
					<td align="right"><?php echo $util->nf($orden['OrdenDescuento']['valor_unidad_nuevo'])?></td>
					<td><input type="text" class="input_number" onkeypress = "return soloNumeros(event,true)" size="12" maxlength="12" name="data[OrdenDescuento][id][<?php echo $orden['OrdenDescuento']['id']?>]" value="<?php echo number_format($orden['OrdenDescuento']['importe_nuevo'],2,".","")?>"/></td>
				</tr>
			
			<?php endforeach;?>
		</table>
		<?php if($novar == 1):?>
			<div class="notices_error" style="width: 100%;margin:10px 0px 10px 0px;"><strong>NOVAR ORDENES!</strong> Se emitir&aacute;n nuevas ordenes para los nuevos importes.</div>
		<?php endif;?>
		<?php echo $frm->submit("ACTUALIZAR VALORES")?>
		<?php echo $frm->hidden("MutualProductoSolicitud.header",array('value' => 0))?>
		<?php echo $frm->hidden("MutualProductoSolicitud.novar",array('value' => $novar))?>
		<?php echo $frm->end()?>		
	
	<?php endif;?>

<?php endif;?>

<?php 
//debug($this->data);
?>