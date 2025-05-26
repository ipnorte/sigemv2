<?php echo $this->renderElement('head',array('title' => 'NOTIFICACION DE ESTADO DE CUENTA','plugin' => 'config'))?>
<?php echo $this->renderElement('liquidacion/notifica_deuda_nav',array('plugin'=>'mutual'))?>
<?php if(!empty($notificaciones)):?>
<table>
    <tr>
        <th>#</th>
        <th>Emitida el</th>
        <th>Periodo</th>        
        <th>Socios</th>
        <th>Deuda Notificada</th>
        <th>Errores</th>
        <th></th>
    </tr>
    <?php foreach($notificaciones as $notificacion):?>
    <tr>
        <td><?php echo $notificacion['Notificacion']['id']?></td>
        <td style="text-align: center;"><?php echo $notificacion['Notificacion']['fecha']?></td>
        <td style="text-align: center;"><?php echo $util->periodo($notificacion['Notificacion']['periodo'])?></td>
        <td style="text-align: center;"><?php echo $notificacion[0]['total_notificados']?></td>
        <td style="text-align: right;"><?php echo $util->nf($notificacion[0]['total_deuda'])?></td>
        <td style="text-align: center;"><?php echo $notificacion[0]['total_con_error']?></td>
        <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/notifica_deuda_detalle/'.$notificacion['Notificacion']['id'],'controles/email_open.png','')?></td>
    </tr>
    <?php endforeach;?>    
</table>


<?php // debug($notificaciones)?>

<?php endif; ?>

