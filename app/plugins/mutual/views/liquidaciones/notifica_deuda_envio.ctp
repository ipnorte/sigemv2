<?php echo $this->renderElement('head',array('title' => 'NOTIFICACION DE ESTADO DE CUENTA','plugin' => 'config'))?>
<?php echo $this->renderElement('liquidacion/notifica_deuda_nav',array('plugin'=>'mutual'))?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('formNuevoEnvio').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
    <h3>Enviar Notificaciones</h3>
    <?php echo $form->create(null,array('name'=>'formNuevoEnvio','id'=>'formNuevoEnvio','action' => 'notifica_deuda_envio'));?>
	<table class="tbl_form">
            <tr>
                <td><?php echo $frm->input('periodo',array('type' => 'select', 'options' => $periodos))?></td>
                <td><input type="submit" value="GENERAR PROCESO"></td>
            </tr>
	</table>
    <?php echo $form->hidden('Liquidacion.codigoNotificacion',['value' => $codigoNotificacion]);?>       
    <?php echo $form->end();?>       
</div>
<?php if($show_asincrono == 1):?>
    <?php
        echo $this->renderElement('show',array(
            'plugin' => 'shells',
            'process' => 'notifica_deuda_email',
            'accion' => '.mutual.liquidaciones.notifica_deuda_detalle',
            'target' => '',
            'titulo' => 'NOTIFICACION DE ESTADO DE CUENTA' ,
            'subtitulo' => 'Periodo: ' .  $util->periodo($periodo),
            'p1' => $periodo,
            'p2' => $codigoNotificacion,
            'p3' => $user_created
        ));
    ?>
<?php endif?>
