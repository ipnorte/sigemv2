<?php 
$INI_FILE = (isset($_SESSION['MUTUAL_INI']) ? $_SESSION['MUTUAL_INI'] : NULL);
$MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);
?>
<div class="bg-secondary text-white pl-2 p-1 col-sm-8 float-left">
    <strong><?php echo strtoupper(Configure::read('APLICACION.nombre_fantasia'))?></strong>
</div>
<div class="float-right col-sm-4 pr-2 bg-secondary text-white p-1 text-right">
        <strong><?php echo Configure::read('APLICACION.nombre')?>&nbsp;@v<?php echo Configure::read('APLICACION.version')?></strong>
</div>

<div class="clearfix"></div>   
<div class="bg-secondary text-white pl-2 pr-2 text-right">
    <small><i class="fas fa-user-check"></i>&nbsp;
        <strong><?php echo strtoupper($Seguridad['Usuario']['descripcion'])?> </strong>
        &nbsp;|&nbsp;<i class="fas fa-plug"></i>&nbsp;<?php echo $_SERVER['REMOTE_ADDR']?>
    </small>
</div>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-1">
    <a class="navbar-brand" href="#"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>    
  <div class="collapse navbar-collapse" id="navbarColor01">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item <?php echo (isset($searchActive) && $searchActive ? " active " : "") ?>">
          <a class="nav-link" href="<?php echo $this->base . "/ventas/solicitudes"?>"><i class="fas fa-home"></i>&nbsp;Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item <?php echo (isset($altaActive) && $altaActive ? " active " : "") ?>">
          <a class="nav-link" href="<?php echo $this->base . "/ventas/solicitudes/alta"?>"><i class="fas fa-handshake"></i>&nbsp;Nueva Solicitud</a>
      </li>
      <li class="nav-item <?php echo (isset($estado_cuentaActive) && $estado_cuentaActive ? " active " : "") ?>">
          <a class="nav-link" href="<?php echo $this->base . "/ventas/solicitudes/estado_cuenta"?>"><i class="fas fa-address-book"></i>&nbsp;Estado de Deuda</a>
      </li>
      <li class="nav-item <?php echo (isset($consultar_intranetActive) && $consultar_intranetActive ? " active " : "") ?>">
          <a class="nav-link" href="<?php echo $this->base . "/ventas/solicitudes/consultar_intranet"?>"><i class="fas fa-network-wired"></i>&nbsp;Consultar Intranet</a>
      </li>
      
      <?php if($MOD_SIISA):?>
          <li class="nav-item <?php echo (isset($consultar_SIISAActive) && $consultar_SIISAActive ? " active " : "") ?>">
              <a class="nav-link" href="<?php echo $this->base . "/ventas/solicitudes/consultar_siisa"?>"><i class="fas fa-business-time"></i>&nbsp;SIISA</a>
          </li>
      <?php endif;?>
      
        <li class="nav-item <?php echo (isset($listadoActive) && $listadoActive ? " active " : "") ?>">
            <a class="nav-link" href="<?php echo $this->base . "/ventas/solicitudes/listado"?>"><i class="fas fa-print"></i>&nbsp;Reportes</a>
        </li>
        <li class="nav-item <?php echo (isset($passwordActive) && $passwordActive ? " active " : "") ?>">
            <a class="nav-link" href="<?php echo $this->base . "/ventas/solicitudes/password"?>"><i class="fas fa-key"></i>&nbsp;Password</a>
        </li>                
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $this->base . "/seguridad/usuarios/logout"?>"><i class="fas fa-sign-out-alt"></i>&nbsp;Salir</a>
        </li>
        <?php if(isset($Seguridad) && empty($Seguridad['Usuario']['vendedor_id'])):?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $this->base . "/home"?>"><i class="fas fa-desktop"></i></a>
        </li>
        <?php endif;?>
    </ul>  
<!--    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="text" placeholder="Solicitud">
      <button class="btn btn-secondary my-2 my-sm-0" type="submit">Buscar</button>
    </form>-->
  </div>
</nav>

<?php // debug($Seguridad)?>
