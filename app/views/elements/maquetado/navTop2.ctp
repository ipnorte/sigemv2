<strong style="float: left;font-size: large;"><?php echo strtoupper(Configure::read('APLICACION.nombre_fantasia'))?></strong>
&nbsp;&nbsp;&nbsp;&nbsp;
<strong>SIGEM</strong>
<?php echo Configure::read('APLICACION.version')?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo $this->requestAction('/seguridad/usuarios/quick_menu')?>
