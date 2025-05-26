<h3><?php echo $datos['ente']?> :: LIQUIDACION DE DEUDA</h3>
<?php if($datos['error'] == 0):?>

<div class="areaDatoForm2">
    CLIENTE:&nbsp;<strong><?php echo $datos['cliente']?></strong>
    <hr/>

    <p></p>
</div>    


<?php else:?>

<div class="notices_error"><?php echo $datos['msg']?></div>

<?php endif; ?>
