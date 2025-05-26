<?php echo $this->renderElement('head',array('title' => 'CONSULTA RECUPERO DE CARTERA'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if(!empty($recuperos)):?>

<?php debug($recuperos)?>


<?php endif;?>