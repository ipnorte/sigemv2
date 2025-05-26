
<script type="text/javascript">

Event.observe(window, 'load', function() {
	$('grilla_productos_by_proveedor').hide();
	$('MutualProductoSolicitudProveedorId').observe('change',function(){
		var prov = $('MutualProductoSolicitudProveedorId').getValue();
		if(prov != ''){
			$('grilla_productos_by_proveedor').show();
//			$('grilla_productos_by_proveedor').update(prov);


			new Ajax.Updater('grilla_productos_by_proveedor',
							'/<?php echo $this->base?>/mutual/mutual_productos/by_proveedor/' + prov, 
							{asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'grilla_productos_by_proveedor']}
			);

			
		}else{
			$('grilla_productos_by_proveedor').hide();
		}		
	});
	
});

</script>

<div class="row">
<?
echo $frm->input('proveedor_id',array('type'=>'select','options'=>$proveedores,'empty'=>TRUE,'selected' => '','label'=>'PROVEEDOR','disabled' => ''));
?> 
</div>
<div style="clear: both;"></div>
<div id="grilla_productos_by_proveedor"></div>
<div style="clear: both;"></div>


<?php //   debug($proveedores)?>

