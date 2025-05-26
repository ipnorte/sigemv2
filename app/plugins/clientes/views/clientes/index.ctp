<?php
//debug($clientes)
?>
<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CLIENTES'))?>

<?php 
$tabs = array(
				0 => array('url' => '/clientes/clientes/add','label' => 'Nuevo Cliente', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/clientes/clientes/imprimir_padron','label' => 'Imprimir Padron', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);
?>

<div class='row'>
	<?php echo $form->create(null,array('action'=> 'index'));?>
	<table>
		<tr>
			<td colspan="7"><strong>CLIENTE</strong></td>
		</tr>
		<tr>
			<td>
				<?php echo $frm->input('Cliente.cuit', array('label' => 'C.U.I.T.:'))?>
			</td>
			<td>
				<?php echo $frm->input('Cliente.razon_social',array('label' => 'Razon Social:'))?>
			</td>
			<td><input type="submit" class="btn_consultar" value="APROXIMAR" /></td>
		</tr>
	</table>
	<?php echo $form->end();?> 
</div>

<?php if(!empty($clientes))echo $this->renderElement('clientes/grilla_clientes_paginada',array('clientes'=>$clientes, 'plugin' => 'clientes'))?>
