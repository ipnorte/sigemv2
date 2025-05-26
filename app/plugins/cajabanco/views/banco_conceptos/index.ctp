<?php
if($tipo == 7):
	$txtHeader = 'CONFIGURACION CONCEPTOS DE CAJA';
else:
	$txtHeader = 'CONFIGURACION CONCEPTOS DE BANCO';
endif;
?>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => $txtHeader))?>
<div class="actions">
<?echo $controles->botonGenerico('add/' . $tipo,'controles/add.png','Nuevo Concepto')?>
</div>

<?php echo $this->renderElement('paginado')?>

	<table>
	
		<tr>
			<th></th>
			<th></th>
			<th><?php echo $paginator->sort('CONCEPTO','BancoConcepto.concepto');?></th>
			<th>INGRESO - EGRESO</th>
<!-- 			<th>CUENTA CONTABLE</th> -->
		</tr>
		<?php foreach($conceptos as $concepto):?>
		
			<tr>
				<td>#<?php echo $concepto['BancoConcepto']['id']?></td>
				<td><a href="<?php echo $this->base?>/cajabanco/banco_conceptos/edit/<?php echo $concepto['BancoConcepto']['id']?>"><img src="<?php echo $this->base?>/img/controles/edit.png" border="0" alt="Modificar" /></a>
				<?php if($concepto['BancoConcepto']['tipo'] == 6 || $concepto['BancoConcepto']['tipo'] == 7): ?>
				<a href="<?php echo $this->base?>/cajabanco/banco_conceptos/del/<?php echo $concepto['BancoConcepto']['id']?>" onclick="return confirm(&#039;Borrar el registro <?php echo $concepto['BancoConcepto']['id']?> - <?php echo $concepto['BancoConcepto']['concepto']?>?&#039;);"><img src="<?php echo $this->base?>/img/controles/user-trash-full.png" border="0" alt="Borrar" /></a></td>
				<?php endif; ?>
				<td><strong><?php echo $concepto['BancoConcepto']['concepto']?></strong></td>
				<td><?php echo $concepto['BancoConcepto']['debe_haber'] == 0 ? 'INGRESO' : 'EGRESO' ?>
<!-- 				<td><?php echo $concepto['BancoConcepto']['cuenta_contable']?></td> -->
				
			
			</tr>
		
		<?php endforeach;?>
	
	</table>


<?php echo $this->renderElement('paginado')?>