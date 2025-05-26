<h1>ORDEN DE COBRO POR CAJA EMITIDAS :: <?php echo date('d-m-Y')?></h1>
<hr>
<?php echo $controles->botonGenerico('/mutual/orden_caja_cobros','controles/reload3.png','ACTUALIZAR')?>
<p>&nbsp;</p>
<?php if(count($ordenes)!=0):?>
<table>
  <tr>
  	 <th></th>
    <th>#</th>
    <th>APELLIDO Y NOMBRE</th>
    <th>IMPORTE</th>
    <th>EMITIDO POR</th>
    <th></th>
  </tr>
  <?php foreach($ordenes as $orden):?>
	  <tr>
	    <td><?php echo $controles->botonGenerico('/mutual/orden_descuento_cobros/add_recibo/'.$orden['OrdenCajaCobro']['id'],'controles/money_dollar.png')?></td>	  	
	    <td style="font-size: 12px;"><?php echo $orden['OrdenCajaCobro']['id']?></td>
	    <td style="font-size: 12px;"><strong><?php echo $orden['Socio']['Persona']['apellido'].', '.$orden['Socio']['Persona']['nombre']?></strong></td>
	    <td align="right" style="font-size: 12px;"><strong><?php echo number_format($orden['OrdenCajaCobro']['importe_cobrado'],2)?></strong></td>
	    <td style="color:gray;"><strong><?php echo $orden['OrdenCajaCobro']['user_created']?></strong> - <?php echo $orden['OrdenCajaCobro']['created']?></td>
	    <td><?php echo $controles->getAcciones($orden['OrdenCajaCobro']['id'],false,false) ?></td>    
	  </tr>
  <?php endforeach;?>
</table>
<?php else:?>
	<div class="areaDatoForm2">NO EXISTEN ORDENES DE COBRO POR CAJA PENDIENTES DE PROCESAR... </div>
<?php endif;?>
<?php //   debug($ordenes)?>