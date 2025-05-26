<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>
<h3>LISTADO GENERAL DE DEUDA</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('frm_listado_deuda').disable();
	<?php endif;?>
	
//        $('tr_ListadoServiceCantidadCuotas').hide();
//        $('ListadoServiceCantidadCuotas').disable();
        
	document.getElementById("ListadoServiceCodigoOrganismo").value = "<?php echo (isset($codigo_organismo) ? $codigo_organismo : "");?>";
//	document.getElementById("ListadoServiceProveedorId").value = "<?php echo (isset($proveedor_id) ? $proveedor_id : 0);?>";
	// document.getElementById("ListadoServicePeriodoCorteMonth").value = "<?php echo (isset($periodo_corte) ? substr($periodo_corte,4,2): date('m'));?>";
	document.getElementById("ListadoServicePeriodoCorteYear").value = "<?php echo (isset($periodo_corte) ? substr($periodo_corte,0,4) : date('Y'));?>";
	//document.getElementById("ListadoServiceConsolidado").checked = <?php // echo (isset($consolidado) && $consolidado == 1 ? 'true' : 'false');?>;
	getComboEmpresas();

	$('ListadoServiceCodigoOrganismo').observe('change',function(){
		getComboEmpresas();		
    
        });
        
//	$('ListadoServiceTipoListado').observe('change',function(){
////            alert($('ListadoServiceTipoListado').getValue());
//            if($('ListadoServiceTipoListado').getValue() === "4"){
//                $('tr_ListadoServiceCantidadCuotas').show();
//                $('ListadoServiceCantidadCuotas').enable();
//            }else{
//                $('tr_ListadoServiceCantidadCuotas').hide();
//                $('ListadoServiceCantidadCuotas').disable();
//            }    
//        });        
	
});
function getComboEmpresas(){
	organismo = $('ListadoServiceCodigoOrganismo').getValue();
	if(organismo==="") return;
	new Ajax.Updater('ListadoServiceCodigoEmpresa','<?php echo $this->base?>/config/global_datos/combo_empresas_ajax/'+ organismo + '/<?php echo (!empty($this->data['ListadoService']['codigo_empresa']) ? $this->data['ListadoService']['codigo_empresa'] : "0")?>/1/1', {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'ListadoServiceCodigoEmpresa']});
	
}
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'listado_deuda','id' => 'frm_listado_deuda'))?>
	<table class="tbl_form">
		<tr>
			<td>ORGANISMO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
                            'plugin'=>'config',
                            'metodo' => "get_organismos",
                            'model' => 'ListadoService.codigo_organismo',
                            'empty' => true,
			))?>			
			</td>			
		</tr>
		<tr>
			<td>EMPRESA</td>
			<td>
				<select name="data[ListadoService][codigo_empresa]" id="ListadoServiceCodigoEmpresa">
				</select>
				<div id="spinner" style="display: none; float: left;color:red;font-size:xx-small;">
				<?php echo $html->image('controles/ajax-loader.gif'); ?>
				</div>
			</td>		
		</tr>
		<tr>
			<td>PROVEEDOR</td>
			<td>
			<?php echo $this->renderElement('proveedor/combo_general',array(
                           'plugin'=>'proveedores',
                           'metodo' => "proveedores_list/1",
                           'model' => 'ListadoService.proveedor_id',
						   'empty' => true,
						   'selected' => (isset($this->data['ListadoService']['proveedor_id']) ? $this->data['ListadoService']['proveedor_id'] : NULL),
			))?>			
			</td>
		</tr>
		<tr>
			<td>PERIODO DE CORTE</td><td><?php echo $frm->periodo('ListadoService.periodo_corte','',(isset($periodo_corte) ? $periodo_corte :  null),date('Y')-1,date('Y')+5,false)?></td>
		</tr>

<!--                <tr>
                    <td>PRODUCTO</td>
                    <td>
			<?php // echo $this->renderElement('global_datos/combo_global',array(
//                            'plugin'=>'config',
//                            'metodo' => "get_tipo_productos_consumos/1",
//                            'model' => 'ListadoService.tipo_producto',
//                            'empty' => true,
//                            'selected' => (isset($this->data['ListadoService']['tipo_producto']) ? $this->data['ListadoService']['tipo_producto'] : NULL),
//			))?>                        
                    </td>
		<tr>-->
        <!--<tr>-->
<!--            <td>CUOTA</td>
            <td>
		<?php // echo $this->renderElement('global_datos/combo_global',array(
//                        'plugin'=>'config',
//                        'label' => " ",
//                        'model' => 'ListadoService.tipo_cuota',
//                        'prefijo' => 'MUTUTCUO',
//                        'disabled' => false,
//                        'empty' => true,
//                        'metodo' => "get_tipo_cuotas",
//                        'selected' => (isset($this->data['ListadoService']['tipo_cuota']) ? $this->data['ListadoService']['tipo_cuota'] : "")	
//		))?>                
            </td>-->
<!--            </tr>                    
			<td>TIPO LISTADO</td>
			<td><?php // echo $frm->input('ListadoService.tipo_listado',array('type' => 'select', 'options' => $opcionesListado))?></td>
		</tr>-->
<!--                <tr id="tr_ListadoServiceCantidadCuotas">
                    <td>HASTA (CUOTAS)</td><td><?php // echo $frm->number('ListadoService.cantidad_cuotas')?></td>
                </tr>-->
		<!-- 
		<tr>
			<td>CONSOLIDADO POR SOCIO</td>
			<td><input type="checkbox" name="data[ListadoService][consolidado]" value="1" id="ListadoServiceConsolidado"/></td>
		</tr>
		 -->				
<!--		<tr>
			<td>FORMATO</td><td><?php // echo $frm->tipoReporte($this->data['ListadoService']['tipo_reporte'])?></td>
		</tr>		-->
		<tr><td colspan="2"><input type="submit" value="GENERAR LISTADO" id="btn_submit" /></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if($show_asincrono == 1):?>
	<?php 
	$subtitulo =  (!empty($codigo_organismo) ? $util->globalDato($codigo_organismo)." - " : "") . $util->periodo($periodo_corte,true,"-");
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_deuda_xls',
											'accion' => '.mutual.listados.listado_deuda.'.$tipo_salida.'.'.$tipo_listado,
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "LISTADO GENERAL DE DEUDA - FORMATO $tipo_salida",
											'subtitulo' => $subtitulo,
											'p1' => $codigo_organismo,
											'p2' => $periodo_corte,
											'p3' => $proveedor_id,
											'p4' => $tipo_listado,
											'p5' => $codigo_empresa,	
											'p6' => $turno_pago,
											'p7' => $cantidad_cuotas,
											'p8' => $tipo_producto,  
											'p9' => $tipo_cuota,    
	));
	?>
<?php endif?>

<?php // debug($proveedor_id)?>