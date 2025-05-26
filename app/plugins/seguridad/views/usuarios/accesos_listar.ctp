<h2>Usuarios :: Consulta de Accesos</h2>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'accesos_listar'))?>
        <table class="tbl_form">
            <tr>
                <td>DESDE FECHA</td><td><?php echo $frm->calendar('UsuarioAcceso.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
                <td>HASTA FECHA</td><td><?php echo $frm->calendar('UsuarioAcceso.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
                <td><?php echo $frm->submit("CONSULTAR")?></td>
            </tr>
        </table>
    <?php echo $frm->end()?>
</div>
<?php if(!empty($accesos)):?>
<table>

    <tr>
        <th>Login</th>
        <th>Usuario</th>
        <th>Descripcion</th>
        <th>Terminal</th>
        <th>Host</th>
        <th>Agente</th>
    </tr>
    <?php 
    $i = 0;
    foreach($accesos as $acceso):
        if ($i++ % 2 == 0) {
            $class = ' class="altrow"';
        }    
    ?>
        <tr <?php //echo $class?>>
            <td style="text-align: center;"><?php echo $acceso['UsuarioAcceso']['logon_at']?></td>
            <td><?php echo $acceso['Usuario']['usuario']?></td>
            <td><?php echo $acceso['Usuario']['descripcion']?></td>
            <td style="text-align: center;"><?php echo $acceso['UsuarioAcceso']['ip']?></td>
            <td><?php echo $acceso['UsuarioAcceso']['host']?></td>
            <td><?php echo $acceso['UsuarioAcceso']['agente']?></td>
        </tr>
    <?php endforeach;?>
</table>

<?php 
endif;
// debug($accesos);

?>