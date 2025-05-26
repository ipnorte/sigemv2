<?php echo $this->renderElement('head',array('title' => 'INFORMES COMERCIALES','plugin' => 'config'))?>
<?php echo $this->renderElement('informe_comerciales/menu_nav',array('plugin'=>'mutual'))?>
<div class="areaDatoForm">
    <h3>Consultar Informes</h3>
</div>
<?php if(!empty($lotes)):?>
<table>
    <tr>
        <th></th>
        <th>#</th>
        <th>Empresa</th>
        <th>Periodo</th>
        <th>Emitido</th>
        <th>Usuario</th>
        <th></th>
    </tr>
    <?php foreach($lotes as $lote):?>
    <tr>
        <td><?php echo $controles->btnDrop(null,'/mutual/informe_comerciales/del/'.$lote['SocioInformeLote']['id'],'Borrar el lote #'.$lote['SocioInformeLote']['id'].'?')?></td>
        <td><?php echo $lote['SocioInformeLote']['id']?></td>
        <td><?php echo $util->globalDato($lote['SocioInformeLote']['empresa'])?></td>
        <td><?php echo $util->periodo($lote['SocioInformeLote']['periodo_hasta'])?></td>
        <td><?php echo $lote['SocioInformeLote']['fecha']?></td>
        <td><?php echo $lote['SocioInformeLote']['user_created']?></td>
        <td><?php echo $controles->botonGenerico('/mutual/informe_comerciales/download_lote_xls/'.$lote['SocioInformeLote']['id'],'controles/ms_excel.png','',array('target' => 'blank'))?></td>
    </tr>
    <?php endforeach;?>    
</table>


<?php // debug($lotes)?>

<?php endif; ?>
