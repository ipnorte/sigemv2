<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: LISTADO CONTROL DE IMPUTACION'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'reporte_control_imputacion',
										'accion' => '.mutual.liquidaciones.imputados.'.$liquidacion['Liquidacion']['id'].'.1',
										'target' => '',
										'btn_label' => 'CONSULTAR REPORTE CONTROL DE IMPUTACION',
										'titulo' => 'GENERA REPORTE CONTROL DE IMPUTACION',
										'p1' => $liquidacion['Liquidacion']['id'],
));

?>