<?php 
if(isset($Seguridad)):
    
    if(isset($Seguridad['Usuario']['grupo_id']) && $Seguridad['Usuario']['activo'] == 1):
        $menus = $this->requestAction('/seguridad/permisos/opcionesMenu/'.$Seguridad['Usuario']['grupo_id']);
        if(!empty($menus)):            
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-1">
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <small>
      <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
          <a class="nav-link" href="<?php echo $this->base . '/home'?>"><i class="fas fa-home"></i>&nbsp;Home <span class="sr-only">(current)</span></a>
      </li>          
        <?php foreach($menus as $menu):?>
          <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle font-weight-bold" href="#" id="navbarDropdown_<?php echo $menu['Permiso']['id']?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?php echo $menu['Permiso']['descripcion']?>
                </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown_<?php echo $menu['Permiso']['id']?>">
                  <!--<small>-->
                <?php 
                $subMenus = $this->requestAction('/seguridad/permisos/opcionesMenu/'.$Seguridad['Usuario']['grupo_id'].'/'.$menu['Permiso']['id']);
                if(!empty($subMenus)):
                    foreach($subMenus as $subMenu):
                ?>
                  <a class="dropdown-item" href="<?php echo $this->base . $subMenu['Permiso']['url']?>"><?php echo $subMenu['Permiso']['descripcion']?></a>
                    <?php endforeach;?>
                <?php endif;?>
              </div>
          </li>
        <?php endforeach;?>
       </ul>
       </small>   
  </div>
</nav>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>