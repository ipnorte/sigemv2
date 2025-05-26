<?php echo $this->renderElement('head',array('title' => 'Actualización de Grillas :: Gestión','plugin' => 'config'))?>

<h4>Listado de Productos Vigentes</h4>

<div class="areaDatoForm2">
<h3><?php echo  $proveedor['ProveedorV1']['codigo_proveedor']?> :: <?php echo  $proveedor['ProveedorV1']['razon_social']?></h3>
</div>

<?php if(!empty($proveedor['ProveedorV1']['direccion_pagare'])):?>
	DIRECCION PAGARE: <strong><?php echo $proveedor['ProveedorV1']['direccion_pagare']?></strong>
<?php endif;?>

<table style="margin-top: 5px;">
  <tr>
  	<th><?php echo $controles->btnRew('','/v1/proveedores')?></th>
  	<th>Codigo</th>
    <th>Descripción</th>
    <th>Fecha de Alta</th>
    <th>Cobranza</th>
    <th>Colocación</th>
    <th>Seguro</th>
    <th>Cuota Social</th>
    <th>Vigente</th>
    <th>Reasignable</th>
    <th>TNA</th>
    <th>Gto.Adm.</th>
    <th>Sellado</th>
    <th>Int.Moratorio</th>
    <th>Costo Cancela</th>
  </tr>
  <?php foreach($productos as $producto):?>
  
  <tr>
  	<td><?php echo $controles->botonGenerico('/v1/proveedores/ver_plan/'.$producto['pp']['codigo_proveedor'].'/'.$producto['pp']['codigo_producto'],'controles/calculator_add.png')?></td>
    <td style="font-weight: bold;"><?php echo $producto['pp']['codigo_producto']?></td>
    
    <td style="font-weight: bold;"><?php echo $producto['pp']['descripcion']?></td>
    <td><?php echo $util->armaFecha($producto['pp']['fecha_alta'])?></td>
    <td style="text-align: center;"><?php echo number_format($producto['pp']['comision_cobranza'],2)?>%</td>
    <td style="text-align: center;"><?php echo number_format($producto['pp']['comision_colocacion'],2)?>%</td>
    <td style="text-align: center;"><?php echo $controles->OnOff($producto['pp']['seguro'],true)?></td>
    <td style="text-align: center;"><?php echo $controles->OnOff($producto['pp']['cuota_social'],true)?></td>
    <td style="text-align: center;"><?php echo $controles->OnOff($producto['pp']['activo'],true)?></td>
    <td style="text-align: center;"><?php echo $controles->OnOff($producto['pp']['reasignable'],false)?></td> 
    
    <td style="text-align: center;"><?php echo number_format($producto['pp']['tna'],2)?>%</td>
    <td style="text-align: center;"><?php echo number_format($producto['pp']['gasto_admin'],2)?>%</td>
    <td style="text-align: center;"><?php echo number_format($producto['pp']['sellado'],2)?>%</td>
    <td style="text-align: center;"><?php echo number_format($producto['pp']['interes_moratorio'],2)?>%</td>
    <td style="text-align: center;"><?php echo number_format($producto['pp']['costo_cancelacion_anticipada'],2)?>%</td>
       
  </tr>
  
  <?php endforeach;?>
  
</table>


<?php //debug($proveedor)?>



