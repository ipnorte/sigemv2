<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>COMISIONES POR COBRANZA</h3>

<div class="actions"><?php echo $controles->botonGenerico('comision_cobranza/'.$proveedor['Proveedor']['id']."/ADD",'controles/calculator_add.png','NUEVA COMISION')?></div>


<?php if(!empty($comisiones)):?>
	<table>
        <tr>
            <th>ORGANISMO</th>
            <th>COMSION</th>
            <th></th>
        </tr>
        <?php foreach($comisiones as $comision):?>
        
			<tr>
				<td><?php echo $comision['GlobalDato']['concepto_1']?></td>
				<td align="right"><?php echo $util->nf($comision['ProveedorComision']['comision'])?></td>
				<td><?php echo $controles->botonGenerico('comision_cobranza/'.$proveedor['Proveedor']['id']."/DROP/".$comision['ProveedorComision']['id'],'controles/user-trash.png',null,null,"BORRAR COMISION ".$comision['GlobalDato']['concepto_1']." (".$util->nf($comision['ProveedorComision']['comision'])." %)  ?")?></td>
			</tr>            
        
        <?php endforeach;?>

	</table>
<?php //   debug($comisiones)?>

<?php endif;?>