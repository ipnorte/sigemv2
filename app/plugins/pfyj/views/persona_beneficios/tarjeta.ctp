<?php 
// debug($user);

$IP = filter_input(INPUT_SERVER,'REMOTE_ADDR');
$dns = gethostbyaddr($IP);

?>
<h3>ATENCION: Informaci&oacute;n Cr&iacute;tica</h3>

<div class="notices">
El usuario <strong><?php echo $user['Usuario']['usuario']?></strong> 
registrado desde <strong><?php echo $IP?> | <?php echo $dns?></strong> esta siendo auditado.
</div>


<div class="areaDatoForm">
    <h4>Datos Registrados de la Tarjeta</h4>
    <hr>
<table class="tbl_form">
    <tr><td colspan="2"></td></tr>
        <tr><td>TITULAR</td><td><strong><?php echo $tarjeta->card_holder_name?></strong></td></tr>
        <tr><td>NUMERO</td><td><strong><?php echo $tarjeta->card_number?></strong></td></tr>
        <tr><td>VENCIMIENTO</td><td><strong><?php echo $tarjeta->card_expiration_month?>/<?php echo $tarjeta->card_expiration_year?></strong></td></tr>
        <tr><td>CODIGO</td><td><strong><?php echo $tarjeta->security_code?></strong></strong></td></tr>
        </tr>
    </table>
</div>