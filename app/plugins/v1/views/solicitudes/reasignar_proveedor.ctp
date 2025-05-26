<?php echo $this->renderElement('head',array('title' => 'SOLICITUDES DE CREDITO :: REASIGNAR PROVEEDOR','plugin' => 'config'))?>
<div class="areaDatoForm">
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($showGrilla == 1):?>
		$('reasignar_proveedor_search').disable();
		
	<?php endif;?>
});
</script>
	<?php echo $form->create(null,array('action' => 'reasignar_proveedor', 'id' => 'reasignar_proveedor_search', 'onsubmit' => "document.getElementById('SolicitudCodigoProductoDescripcion').value = getTextoSelect('SolicitudCodigoProducto');"));?>
	<table class="tbl_form">
		<tr>
			<td>PRODUCTO</td><td><?php echo $frm->input('Solicitud.codigo_producto',array('type'=>'select','options'=>$productos, 'selected' => $codigoProducto)); ?></td><td><?php echo $frm->submit("BUSCAR SOLICITUDES")?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('Solicitud.codigo_producto_descripcion',array('value' => ""))?>
	<?php echo $frm->end()?>
</div>

<?php if($showGrilla == 1):?>




	<h3><?php echo $codigoProductoDescripcion?> :: SOLICITUDES DE CREDITO</h3>
	
	<?php if(!empty($solicitudes)):?>
	
		<form id="formReasignarSolicitudes" onsubmit="event.returnValue = false; return false;" method="post" action="<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor">
		<fieldset style="display:none;"><input type="hidden" name="_method" value="POST" /></fieldset>
		
		
		<script language="Javascript" type="text/javascript">
		var rows = <?php echo count($solicitudes)?>;
	//	var rows = $("Rows").getValue();
		var totalSeleccionado = 0;
		
		function chkOnclick(){
			  SelSum();
		}
		
		function SelSum(){
	
			totalSeleccionado = 0;
			rows = $("Rows").getValue();
			
			for (i=1;i<=rows;i++){
				var celdas = $('TRL_' + i).immediateDescendants();
				oChkCheck = document.getElementById('SolicitudNroSolicitud_' + i);
	
				toggleCell('TRL_' + i,oChkCheck);
				
				if (oChkCheck.checked){
					totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
				}	
			}
	
			totalSeleccionado = totalSeleccionado/100;
			totalSeleccionado = FormatCurrency(totalSeleccionado);
			
			$('total_seleccionado').update(totalSeleccionado);
			
		}
	
		function procesaFormByAjax(){
			new Ajax.Updater('grilla_solicitudes',
					'<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor', 
					{
						asynchronous:true, 
						evalScripts:true, 
						onComplete:function(request, json) {
							$('spinner').hide();
							totalSeleccionado = 0;
						}, 
						onLoading:function(request) {
							$('spinner').show();
						}, 
						parameters:Form.serialize('formReasignarSolicitudes'), 
						requestHeaders:['X-Update', 'grilla_solicitudes']
					});	
		}
	
		Event.observe('formReasignarSolicitudes', 'submit', function(event) {
	
			if(totalSeleccionado == 0){
				alert("DEBE SELECCIONAR AL MENOS UNA SOLICITUD!");
				return;
			}	
			
			if(confirm("ASIGNAR SOLICITUDES SELECCIONADAS A " + getTextoSelect('SolicitudReasignarProveedorId') + " POR UN TOTAL DE " + totalSeleccionado + "?")){
				procesaFormByAjax();
				totalSeleccionado = 0;
			}	
	
		});	
		
		</script>	
		
			<div id="grilla_solicitudes">
		
			<table>
			
				<tr>
				
					
					<th>SOLICITANTE</th>
					<th>SOLICITUD NRO</th>
					<th>FECHA</th>
					<th>ESTADO</th>
					<th>SOLICITADO</th>
					<th>CUOTAS</th>
					<th></th>
				
				</tr>
				<?php $i = 0;?>
				<?php foreach($solicitudes as $solicitud):?>
				
					<?php $i++;?>
				
					<tr id="TRL_<?php echo $i?>">
						
						<td><?php echo $solicitud['solicitante']?></td>
						<td align="center"><?php echo $solicitud['nro_solicitud']?></td>
						<td><?php echo $util->armaFecha($solicitud['fecha_solicitud'])?></td>
						<td><?php echo $solicitud['estado_descripcion']?></td>
						<td align="right"><?php echo number_format($solicitud['en_mano'],2)?></td>
						<td align="center"><?php echo $solicitud['cuotas']?></td>
						<td><input type="checkbox" name="data[Solicitud][nro_solicitud][<?php echo $solicitud['nro_solicitud']?>]" value="<?php echo number_format(round($solicitud['en_mano'],2) * 100,0,".","")?>" id="SolicitudNroSolicitud_<?php echo $i?>" onclick="chkOnclick()"/></td>
					</tr>
				
				<?php endforeach;?>
				<tr class="totales">
					<th colspan="4">TOTAL SELECCIONADO</th><th id="total_seleccionado">0.00</th><th></th><th></th>
				</tr>
				<tr>
					<td align="right">ASIGNAR ORDEN DE DESCUENTO A</td>
					<td colspan="3">
					<?php echo $this->renderElement('proveedor/combo_general',array(
																					'plugin'=>'proveedores',
																					'metodo' => "proveedores_reasignables_list",
																					'model' => 'Solicitud.reasignar_proveedor_id',
																					'empty' => false,
					))?>				
					</td>
					<td><input type="submit" value="REASIGNAR" id="btnReasignar"/><input type="button" value="CANCELAR" onclick="javascript:window.location='<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor'" /></td>
					<td colspan="2"><div id="spinner" style="display: none; float: left;color:red;"><?php echo $html->image('controles/ajax-loader.gif'); ?></div></td>
				</tr>
			
			</table>
			<input type="hidden" name="Rows" id="Rows" value="<?php echo count($solicitudes)?>" />
			</div>
			<?php echo $frm->hidden('Solicitud.codigo_producto',array('value' => $codigoProducto))?>
			
			</form>
	<?php else:?>
	
		<div class='notices_error' style="width: 100%">
			NO EXISTEN SOLICITUDES PARA ESTE PRODUCTO SIN REASIGNAR
		</div>
		<input type="button" value="CANCELAR" onclick="javascript:window.location='<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor'" />
	
	<?php endif;?>

<?php endif;?>