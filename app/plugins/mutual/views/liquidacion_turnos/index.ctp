<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION  DE TURNOS'))?>

<?php if(!empty($empresas)):?>

<table>
    <tr>
        <th>EMPRESA</th>
        <th colspan="4">NOMBRE</th>
    </tr>
    <?php foreach($empresas as $codigo => $empresa):?>
    
        <tr>
            <td style="font-size:13px;background-color: #e2e6ea;border:0"><h4 style="text-align: left;color:#000000;"><?php echo substr($codigo,-4)?></h4></td>
            <td colspan="4" style="font-size:13px;background-color: #e2e6ea;border:0"><h4 style="text-align: left;color:#000000;"><?php echo $empresa['empresa']?></h4></td>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>TURNO</th>
            <th>COD.REP.</th>
            <th>DESCRIPCION</th>
        </tr>
        <?php if(!empty($empresa['turnos'])):?>
            <?php foreach($empresa['turnos'] as $id => $turno):?>

                <tr>
                    <td></td>
                    <td></td>
                    <td><?php echo substr($turno['turno'],-5)?></td>
                    <td><?php echo $turno['codigo_reparticion']?></td>
                    <td><?php echo $turno['descripcion']?></td>
                </tr>

            <?php endforeach;?>
        <?php else:?>
                <tr>
                    <td colspan="5"><div class="notices_error">*** NO TIENE TURNOS ASIGNADOS ***</div></td>
                </tr>
        <?php endif;?>   
    <?php endforeach;?>
    
</table>

<?php endif;?>


<?php // debug($empresas)?>