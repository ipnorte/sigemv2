<h1>FECHA DE CORTE Y VENCIMIENTOS</h1>
<hr>
<table>

	<?php foreach($organismos as $org):?>
	
		<tr>
			<th colspan="9" style="text-align: left;">
				<div style="text-align: right;"><?php echo $controles->btnAdd('Agregar','add/'.$org['GlobalDato']['id'])?></div>
				<h4 style="color:#ffffff;"><?php echo $org['GlobalDato']['concepto_1']?></h4>
			</th>
		</tr>
		<?php echo $this->requestAction('/proveedores/proveedor_vencimientos/grilla_by_organismo/'.$org['GlobalDato']['id'])?>
		
	
	
	<?php endforeach;?>

</table>
<?php //   debug($organismos)?>

