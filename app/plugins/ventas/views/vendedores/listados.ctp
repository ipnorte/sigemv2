<?php 
if(isset($menuVendedores) && !empty($menuVendedores)){
    echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'));
    echo "<h3>PADRON DE VENDEDORES :: LISTADO DE SOLICITUDES DE CREDITOS</h3>";
    echo "<hr/>";
}else{
  echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE VENDEDORES :: LISTADO DE SOLICITUDES DE CREDITOS'));  
} 
?>

<?php // echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE VENDEDORES :: LISTADO DE SOLICITUDES DE CREDITOS'))?>

<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' =>'listados/NULL/'.$menuVendedores))?>
	<table class="tbl_form">
		<tr>
			<td>VENDEDOR</td>
                        <td colspan="3">
                            <?php
                            echo $this->renderElement('vendedores/combo', array(
                                'plugin' => 'ventas',
                                'empty' => TRUE,
//                                'solo_activos' => TRUE
                            ));
                            ?>
                            <?php // echo $frm->input('MutualProductoSolicitud.vendedor_id',array('type' => 'select', 'options' => $vendedores, 'empty' => true,'selected' => $this->data['MutualProductoSolicitud']['vendedor_id']))?>
                        </td>
		</tr>
		<tr>
			<td>PROVEEDOR</td>
			<td>
			<?php echo $this->renderElement('proveedor/combo_general',array(
																			'plugin'=>'proveedores',
																			'metodo' => "proveedores_list/1",
																			'model' => 'MutualProductoSolicitud.proveedor_id',
																			'empty' => true,
                                                                            'selected' => $this->data['MutualProductoSolicitud']['proveedor_id'],
			))?>			
			</td>
		</tr>          
		<tr>
			<td>ESTADO</td>
			<td colspan="3">
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'metodo' => "get_estados_solicitud",
																			'model' => 'MutualProductoSolicitud.estado',
																			'empty' => true,
																			'selected' => $this->data['MutualProductoSolicitud']['estado']
			))?>			
			</td>
		</tr>
		<tr>
			<td>EMITIDAS DESDE</td><td><?php echo $frm->calendar('MutualProductoSolicitud.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
			<td>HASTA</td><td><?php echo $frm->calendar('MutualProductoSolicitud.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>PERIODO DE CORTE A</td><td><?php echo $frm->periodo('MutualProductoSolicitud.periodo_corte','',(isset($fecha_corte) ? $fecha_corte :  null),date('Y')-1,date('Y')+5,false)?></td>
		</tr>                
	
		<tr><td colspan="2"><input type="submit" value="GENERAR LISTADO XLS" id="btn_submit" /></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if($show_asincrono == 1):?>
	<?php 
	echo $this->renderElement('show',array(
            'plugin' => 'shells',
            'process' => 'ventas_listado_solicitudes',
            'accion' => '.ventas.vendedores.listados.XLS',
            'target' => '_blank',
            'btn_label' => 'Ver Listado',
            'titulo' => "LISTADO DE SOLICITUDES - FORMATO XLS",
            'subtitulo' => "Emitidas desde " . $util->armaFecha($fecha_desde) . " hasta " . $util->armaFecha($fecha_hasta). " ** ANALISIS A ".$util->periodo($fecha_corte)." ***",
            'p1' => (isset($this->data['MutualProductoSolicitud']['vendedor_id']) ? $this->data['MutualProductoSolicitud']['vendedor_id'] : NULL),
            'p2' => $this->data['MutualProductoSolicitud']['estado'],
            'p3' => $fecha_desde,
            'p4' => $fecha_hasta,
            'p5' => Configure::read('APLICACION.tipo_orden_dto_credito'),
            'p6' => $this->data['MutualProductoSolicitud']['proveedor_id'],        
            'p7' => $periodo_corte,
            'p8' => $vendedor_id,
	));
	?>
<?php endif?>