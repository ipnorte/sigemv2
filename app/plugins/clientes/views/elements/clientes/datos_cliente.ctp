<div class="areaDatoForm">
<h4>DATOS DEL CLIENTE</h4>
C.U.I.T.: <strong><?php echo $cliente['Cliente']['cuit']?></strong>
&nbsp;
RAZON SOCIAL: <strong><?php echo $cliente['Cliente']['razon_social']?></strong>
<br/>
DOMICILIO:&nbsp; <strong><?php echo $cliente['Cliente']['domicilio']?></strong>
<br/>
TELEFONO FIJO:&nbsp; <strong><?php echo $cliente['Cliente']['telefono_fijo']?></strong>
&nbsp;
TELEFONO MOVIL:&nbsp; <strong><?php echo $cliente['Cliente']['telefono_movil']?></strong>
&nbsp;
<?php if(!empty($cliente['Cliente']['email'])):?>
<br/>
EMAIL:&nbsp; <strong><?php echo $text->autoLinkEmails($cliente['Cliente']['email']);?></strong>
<?php endif;?>
<br/>
RESPONSABLE:&nbsp; <strong><?php echo rtrim($cliente['Cliente']['responsable'])?></strong>
&nbsp;
CONTACTO:&nbsp; <strong><?php echo rtrim($cliente['Cliente']['contacto'])?></strong>
</div>