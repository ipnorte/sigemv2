<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>CONSTANCIAS DE PRESENTACION</h3>
<hr/>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' =>'listado_remitos_vendedor'))?>    
    <table class="tbl_form">
        <tr>
                <td>EMITIDAS DESDE</td><td><?php echo $frm->calendar('VendedorRemito.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
                <td>HASTA</td><td><?php echo $frm->calendar('VendedorRemito.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
                <td><input type="submit" value="CONSULTAR" id="btn_submit" /></td>
        </tr>        
    </table>
<?php echo $frm->end()?>    
</div>

<?php if(!empty($remitos)):?>
    <hr/>
    <?php if(count($remitos) == 50):?>
    <div class="notices">
        SE MUESTRAN LAS PRIMERAS 50
    </div>
    <?php endif;?>
    <table>
            <tr>
                    <th>#</th>
                    <th>FECHA</th>
                    <th>GENERADO POR</th>
                    <th>OBSERVACIONES</th>
            </tr>
            <?php foreach($remitos as $remito):?>
                    <tr>
                            <td><strong><?php echo $controles->linkModalBox($remito['VendedorRemito']['id'],array('title' => 'CONSTANCIA DE PRESENTACION #' . $remito['VendedorRemito']['id'],'url' => '/ventas/vendedores/ficha_remito/'.$remito['VendedorRemito']['id'],'h' => 450, 'w' => 850))?></strong></td>
                            <td><?php echo $remito['VendedorRemito']['created']?></td>
                            <td><?php echo $remito['VendedorRemito']['user_created']?></td>
                            <td><?php echo $remito['VendedorRemito']['observaciones']?></td>
                    </tr>
            <?php endforeach;?>		
    </table>
<?php else:?>
	<h4>NO EXISTEN CONSTANCIAS EMITIDAS PARA EL VENDEDOR</h4>
<?php endif; ?>
