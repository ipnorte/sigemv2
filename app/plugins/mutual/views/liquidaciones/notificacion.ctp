<?php echo $this->renderElement('head',array('title' => 'NOTIFICACION DE ESTADO DE CUENTA'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

<?php 
echo $this->renderElement('show',array(
    'plugin' => 'shells',
    'process' => 'notifica_deuda_email',
    'accion' => '.mutual.liquidaciones.consulta',
    'target' => '',
    'titulo' => 'NOTIFICACION DE ESTADO DE CUENTA' ,
    'subtitulo' => 'Periodo: ' .  $util->periodo($periodo),
    'p1' => $periodo,
    'p2' => $user_created
));

?>
