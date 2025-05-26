<div class="areaDatoForm">

<table class="tbl_form">
        <tr><td>USUARIO</td><td><strong><?php echo strtoupper($user['Usuario']['usuario'])?> </strong></td></tr>
        <tr><td>DESCRIPCION</td><td><strong><?php echo strtoupper($user['Usuario']['descripcion'])?> </strong></td></tr>
        <tr><td>GRUPO / PERFIL</td><td><strong><?php echo $user['Grupo']['nombre']?></strong></td></tr>
    </table>
</div>
<h3>Historial de los últimos 30 días</h3>
<table>

    <tr>
        <th>Login</th>
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
        <tr <?php // echo $class?>>
            <td style="text-align: center;"><?php echo $acceso['UsuarioAcceso']['logon_at']?></td>
            <td style="text-align: center;"><?php echo $acceso['UsuarioAcceso']['ip']?></td>
            <td><?php echo $acceso['UsuarioAcceso']['host']?></td>
            <td><?php echo $acceso['UsuarioAcceso']['agente']?></td>
        </tr>
    <?php endforeach;?>
</table>