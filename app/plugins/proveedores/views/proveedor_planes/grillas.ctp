

<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PLANES :: DETALLE DE GRILLAS</h3>



<?php echo $this->renderElement('proveedor_planes/info_plan',array('plan' => $plan))?>

<table class="tbl_form">
	<tr>
		<td><?php echo $frm->btnForm(array('LABEL' => "REGRESAR",'URL' => '/proveedores/proveedor_planes/index/'.$plan['ProveedorPlan']['proveedor_id']))?></td>
        <td><?php if(empty($plan['ProveedorPlan']['metodo_calculo'])) echo $frm->btnForm(array('LABEL' => "NUEVA GRILLA DE CUOTAS",'URL' => '/proveedores/proveedor_planes/nueva_grilla/'.$plan['ProveedorPlan']['id']))?></td>
        <td><?php if(!empty($plan['ProveedorPlan']['metodo_calculo'])) echo $frm->btnForm(array('LABEL' => "NUEVA GRILLA DE CUOTAS",'URL' => '/proveedores/proveedor_planes/calcular_grilla/'.$plan['ProveedorPlan']['id']))?></td>
	</tr>
</table>

<table>
	<tr>
		<th>#</th>
		<th></th>
		<th>DESCRIPCION</th>
		<th>A PARTIR DEL</th>
		<th>T.N.A.</th>
		<th>T.E.A.</th>
		<th>T.E.M.</th>
        <th>GTO.OTORGAMIENTO</th> 
		<th>SELLADOS</th> 
		<th>OBSERVACIONES</th>       
		<th>UPLOAD BY</th>
		<th></th>
        <th></th>
	</tr>
	<?php foreach($grillas as $grilla):?>

		<tr>
		<td><?php echo $grilla['ProveedorPlanGrilla']['id']?></td>
			<td><?php echo $controles->btnDrop(null,'/proveedores/proveedor_planes/borrar_grilla/'.$grilla['ProveedorPlanGrilla']['id'],'Borrar la grilla "'.$grilla['ProveedorPlanGrilla']['descripcion'].'"?')?></td>
			<td><strong><?php echo $grilla['ProveedorPlanGrilla']['descripcion']?></strong></td>
			<td align="center"><strong><?php echo $util->armaFecha($grilla['ProveedorPlanGrilla']['vigencia_desde'])?></strong></td>
			<td><?php echo (!empty($grilla['ProveedorPlanGrilla']['tna']) ? number_format($grilla['ProveedorPlanGrilla']['tna'],2) : 0)?> %</td>
			<td><?php echo number_format($grilla['ProveedorPlanGrilla']['tea'],2)?> %</td>
			<td><?php echo number_format($grilla['ProveedorPlanGrilla']['tem'],2)?> %</td>
			<td><?php if (!empty($grilla['ProveedorPlanGrilla']['tipo_cuota_gasto_admin'])) echo $grilla['TipoCuotaGtoAdm']['tipo_cuota_gasto_admin_desc']  . "(" . $grilla['ProveedorPlanGrilla']['gasto_admin'] ." %)"?></td>
			<td><?php if (!empty($grilla['ProveedorPlanGrilla']['tipo_cuota_sellado'])) echo $grilla['TipoCuotaGtoSell']['tipo_cuota_sellado_desc'] . "(" . $grilla['ProveedorPlanGrilla']['sellado'] ." %)"?></td>			
			<td><?php echo $grilla['ProveedorPlanGrilla']['observaciones']?></td>
			<td><?php echo $grilla['ProveedorPlanGrilla']['user_created']?> <?php echo $grilla['ProveedorPlanGrilla']['created']?></td>
			<td><?php echo $controles->botonGenerico('/proveedores/proveedor_planes/download_grilla/'.$grilla['ProveedorPlanGrilla']['id'],'controles/ms_excel.png','',array('target' => 'blank'))?></td>
            <td><?php if(!empty($grilla['ProveedorPlanGrilla']['metodo_calculo'])) echo $controles->botonGenerico('/proveedores/proveedor_planes/grillas/'.$plan['ProveedorPlan']['id'].'/'.$grilla['ProveedorPlanGrilla']['id'],'controles/calculator.png','',array('target' => '_blank'))?></td>
		</tr>

	<?php endforeach;?>
</table>

<?php 
//debug($grillas);
//debug(json_decode('{"solicitado":10000,"cuotas":"6","tna":"155","tem":12.92,"metodoCalculo":"1","liquidacion":{"capitalSolicitado":12159.42,"netoPercibe":10000,"totalPrestamo":14975.76,"gastoAdminstrativo":{"porcentaje":"18","importe":1800,"tipoCuota":"MUTUTCUOGOTO","descripcion":"GASTOS DE OTORGAMIENTO","baseCalculo":"1","baseCalculoCriterio":"1 - CAPITAL"},"sellado":{"porcentaje":"2.4","importe":359.42,"tipoCuota":"MUTUTCUOSELL","descripcion":"SELLADOS","baseCalculo":"3","baseCalculoCriterio":"3 - TOTAL A REINTEGRAR"},"interesesDevengados":4975.76},"cuotaPromedio":{"cuota":"6","capital":1666.67,"interes":685.37,"iva":143.93,"importe":2495.96,"cft":49.76,"cfta":99.52,"saldo":0},"detalleCuotas":{"1":{"capital":1203.96,"interes":1067.77,"iva":224.23,"importe":2495.96,"cft":49.76,"cfta":49.76,"saldo":8796.04},"2":{"capital":1359.52,"interes":939.21,"iva":197.23,"importe":2495.96,"cft":49.76,"cfta":49.76,"saldo":7436.52},"3":{"capital":1535.16,"interes":794.05,"iva":166.75,"importe":2495.96,"cft":49.76,"cfta":49.76,"saldo":5901.36},"4":{"capital":1733.5,"interes":630.13,"iva":132.33,"importe":2495.96,"cft":49.76,"cfta":49.76,"saldo":4167.86},"5":{"capital":1957.47,"interes":445.03,"iva":93.46,"importe":2495.96,"cft":49.76,"cfta":49.76,"saldo":2210.39},"6":{"capital":2210.39,"interes":236.02,"iva":49.56,"importe":2495.96,"cft":49.76,"cfta":49.76,"saldo":0}}}'))
?>