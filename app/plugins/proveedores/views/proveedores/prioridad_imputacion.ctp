<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>PRIORIDAD DE IMPUTACION DE LA COBRANZA POR DEBITO AUTOMATICO</h3>

<table>
    <tr>
        <th>LIQUIDACIONES DEL ORGANISMO</th>
        <th>PRIORIDAD</th>
        <th></th>
    </tr>
    <?php if(!empty($proveedor['ProveedorPrioridadImputaOrganismo'])):?>
    
        <?php foreach ($proveedor['ProveedorPrioridadImputaOrganismo'] as $i => $item):?>
    
    <tr>
        <td style="font-size:large;"><?php echo $util->globalDato($item['codigo_organismo'])?></td>
        <td style="text-align: center;">
            <script type="text/javascript">
                $("ajax_loader_<?php echo $i?>").hide();

                
                function onChange(id){

                    $("ajax_loader_" + id).show();
                    
                    new Ajax.Updater('responser_' + id, '<?php echo $this->base?>/proveedores/proveedores/prioridad_imputacion_update', 
                    { 
                            asynchronous:true, evalScripts:true, onComplete:function(request, json) 
                            {
                                    $("ajax_loader_" + id).hide();

                                    if(request.responseText === "0"){
                                        alert('SE PRODUJO UN ERROR AL GRABAR LOS DATOS');
                                    }    
                            },
                            parameters:$('formModificaPrioridad_' + id).serialize(), 
                            requestHeaders:['X-Update', 'responser_' + id]
                    });                    
                    
                    
                }
                
            </script>
            <?php echo $frm->create(null,array('name'=>'formModificaPrioridad_'.$i,'id'=>'formModificaPrioridad_'.$i, 'action' => "prioridad_imputacion/".$proveedor['Proveedor']['id'] ));?>
            <?php echo $frm->input('ProveedorPrioridadImputaOrganismo.prioridad',array('type'=>'select','onchange' => "onChange($i)",'options' => array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10),'selected' => $item['prioridad'],'style' => 'font-size:large;font-weight: bold;','disabled' => ($item['proveedor_id'] == 18 ? TRUE : FALSE))) ?>
            <?php echo $frm->hidden('ProveedorPrioridadImputaOrganismo.codigo_organismo',array('value' => $item['codigo_organismo'])); ?>
            <?php echo $frm->hidden('ProveedorPrioridadImputaOrganismo.proveedor_id',array('value' => $item['proveedor_id'])); ?>
            <div id="responser_<?php echo $i?>" style="visibility: hidden;display: none;"></div>
            <?php echo $frm->end()?>
        </td>
        <td><?php echo $html->image('controles/ajax-loader.gif',array("border"=>"0",'id' => "ajax_loader_$i", 'style' => "display: none;"));?></td>
    </tr>
    
    
        <?php endforeach;?>
    
    <?php endif;?>
</table>

<?php // debug($proveedor['ProveedorPrioridadImputaOrganismo'])?>

