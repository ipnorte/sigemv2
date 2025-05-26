<?php 
$proveedor = $this->requestAction('/proveedores/proveedores/get_proveedor/'.$proveedor_id .'/1');

?>
<div class="areaDatoForm">
<h4>DATOS DEL PROVEEDOR</h4>
C.U.I.T.: <strong><?php echo $proveedor['Proveedor']['cuit']?></strong>
&nbsp;
RAZON SOCIAL: <strong><?php echo $proveedor['Proveedor']['razon_social']?></strong>
<br/>
DOMICILIO:&nbsp; <strong><?php echo $proveedor['Proveedor']['domicilio']?></strong>
<br/>
TELEFONO FIJO:&nbsp; <strong><?php echo $proveedor['Proveedor']['telefono_fijo']?></strong>
&nbsp;
TELEFONO MOVIL:&nbsp; <strong><?php echo $proveedor['Proveedor']['telefono_movil']?></strong>
&nbsp;
<?php if(!empty($proveedor['Proveedor']['email'])):?>
<br/>
EMAIL:&nbsp; <strong><?php echo $text->autoLinkEmails($proveedor['Proveedor']['email']);?></strong>
<?php endif;?>
<br/>
RESPONSABLE:&nbsp; <strong><?php echo rtrim($proveedor['Proveedor']['responsable'])?></strong>
&nbsp;
CONTACTO:&nbsp; <strong><?php echo rtrim($proveedor['Proveedor']['contacto'])?></strong>
<?php if(isset($proveedor['Proveedor']['saldo'])): ?>
<h3>SALDO: <?php echo number_format($proveedor['Proveedor']['saldo'],2) ?></h3>
<?php endif;?>
</div>