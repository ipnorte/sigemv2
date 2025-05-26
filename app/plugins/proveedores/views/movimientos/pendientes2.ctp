<script language="Javascript" type="text/javascript">
	var estadoFiltro = 0;
//	var colum = 2;		
	Event.observe(window, 'load', function() {
		$('filter').hide();
	});

	function mostrarFiltro()
	{

		if(estadoFiltro == 0)
		{
			estadoFiltro = 1;
			$('filter').show();
		}	
		else
		{
			estadoFiltro = 0;
			$('filter').hide();
		}
	}

	function mostrarSubTabla(linea, param1, param2)
	{
		oLinea = $('TRL_'+linea);
		oContenedor = $('div_'+linea);
		
		if (oLinea.style.display=="none")
		{
			oLinea.style.display="";

			$('imgon_'+linea).hide();
			$('imgoff_'+linea).show();
			toggleCellMouseOver('LTR_'+linea, true);

			new Ajax.Updater('div_'+linea,'<?php echo $this->base?>/proveedores/movimientos/factura_detalle/'+param1+'/'+param2, {asynchronous:true, evalScripts:true,onLoading:function(request) {$('spinner_'+linea).show();},onComplete:function(request) {$('spinner_'+linea).hide();}, requestHeaders:['X-Update', 'div_'+linea]});

		}
		
		else
		{
			oLinea.style.display="none"
			$('imgon_'+linea).show();
			$('imgoff_'+linea).hide();
			oContenedor.update('');
			toggleCellMouseOver('LTR_'+linea, false);
		} 
				
	}

	function toggleCellMouseOver(idRw, status){
		var celdas = $(idRw).immediateDescendants();
		if(status)celdas.each(function(td){td.removeClassName("dato");td.addClassName("selected2");});
		else celdas.each(function(td){td.removeClassName("selected2");td.addClassName("dato");});
	}
		

        function filtra(txt, colum) {
          var t = document.getElementById('data_table');
          var filas = t.getElementsByTagName('tr');
          for (var i = 0; i < filas.length; i++) {
            var ele = filas[i];
            var texto = ele.getElementsByTagName('td')[colum].innerHTML.toUpperCase();
            var posi = (texto.indexOf(txt.toUpperCase()) !== -1);
            ele.style.display = posi ? '' : 'none';
          } 
        }


</script>

<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores, 'plugin' => 'proveedores'))?>
<h3>PENDIENTES DE PAGOS</h3>

<div class="contenedor">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col width="10" />
		<col width="163" />
		<col width="519" />
		<col width="95" />
		<col width="80" />
		<col width="80" />
		<col width="80" />
		<col width="74" />
		<col width="80" />
		
		<tr>
			<th class="dato"></th>
			<th class="dato">Comprobante</th>
			<th class="dato">Comentario</th>
			<th class="dato" align="center">Fecha Comp.</th>
			<th class="dato">Vencim.</th>
			<th class="dato" align="right">Imp.Comp.</th>
			<th class="dato" align="right">Imp.Venc.</th>
			<th class="dato" align="right">Pagado</th>
			<th class="dato" align="right">Saldo</th>
		</tr>
		<tr id="filter">
			<td class="dato" colspan="2"></td>
			<td class="dato"><?php echo $frm->input('Movimiento.comentario', array('label'=>'','size'=>60,'maxlength'=>60, 'onkeyup' => 'filtra(this.value,2);')) ?></td>
                        <td class="dato" colspan="6"></td>
		</tr>

	</table>

	<div class="cuerpo">
		<table id="data_table" border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="10" />
			<col width="210" />
			<col width="550" />
			<col width="100" />
			<col width="80" />
			<col width="80" />
			<col width="80" />
			<col width="80" />
		
			<?php
				$i = 0;
				$nTotal = 0;
			  	foreach($facturaPendiente as $fPendiente):
                                    
                                    $fPendiente = $fPendiente['t'];
                                    $i++;
                                    $linea = "TRL_" . $i;
                                    $param1 = $fPendiente['id'];
                                    $nTotal += $fPendiente['saldo']; 

		  		?>
		  		<tr id='LTR_<?php echo $i;?>'>
		  			<td class="dato">
		  			<?php echo $html->image('controles/arrow_right.gif', array("border"=>"0",'style' => 'cursor: pointer;','id' => "imgon_$i",'onclick' => "mostrarSubTabla('$i','".$param1."','". $fPendiente['tipo_pago']."');"))?>
		  			<?php echo $html->image('controles/arrow_down.gif', array("border"=>"0",'style' => 'cursor: pointer; display:none;','id' => "imgoff_$i",'onclick' => "mostrarSubTabla('$i','0','0');"))?>
		  			</td>
					<td class="dato"><?php echo $fPendiente['tipo_comprobante_desc'] ?></td>
					<td class="dato"><?php echo $fPendiente['comentario'] ?></td>
					<td class="dato" align="center"><?php echo $fPendiente['fecha_comprobante'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente['fecha_comprobante'])?></td>
					<td class="dato" align="center"><?php echo $fPendiente['vencimiento'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente["vencimiento"])?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente['total_comprobante'],2) ?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente["importe"],2) ?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente["pago"],2) ?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente["saldo"],2) ?></td>
		  		</tr>
		  				<tr id="TRL_<?php echo $i?>" style="display:none">
		  					<td colspan="2"></td>
		  					<td colspan="2">
		  					<?php echo $controles->ajaxLoader('spinner_'.$i,'CARGANDO DETALLE...')?>
								<div id='div_<?php echo $i?>'>

								</div>
							</td>
		  					<td colspan="5"></td>
		  				</tr>
	  		<?php endforeach;?>
 		</table>
 	</div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="1%">
					<?php echo $controles->btnCallJS("mostrarFiltro()", "", "controles/icono_filtro.png")?>
				</td>
				<td width="1%">
					<?php echo $controles->botonGenerico('/proveedores/movimientos/salida/' . $proveedores['Proveedor']['id'] . '/XLS','controles/ms_excel.png')?>
				</td>
				<td width="1%">
					<?php echo $controles->botonGenerico('/proveedores/movimientos/salida/' . $proveedores['Proveedor']['id'] . '/PDF','controles/pdf.png', null, array('target' => '_blank'))?>
				</td>
				<td colspan="6" align="right">SALDO PROVEEDOR: <?php echo number_format($nTotal,2) ?></td>
				</td>
			</tr>
		</table> 	
 	
 </div>

<?php // debug($facturaPendiente)?>
