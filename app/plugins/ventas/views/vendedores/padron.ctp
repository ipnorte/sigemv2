<?php echo $this->renderElement('vendedores/menu_padron',array('vendedor' => $vendedor))?>
<?php echo $this->renderElement('personas/datos_personales',array('persona_id'=>$vendedor['Persona']['id'],'plugin' => 'pfyj'))?>
<div class="areaDatoForm">
<h3>SEGURIDAD Y ACCESO</h3>
USUARIO: <strong><?php echo $vendedor['Usuario']['usuario']?></strong>
&nbsp;
GRUPO: <strong><?php echo $vendedor['Usuario']['Grupo']['nombre']?></strong>
&nbsp;STATUS: <strong><?php echo ($vendedor['Usuario']['activo'] ? "<span style='color:green;'>CUENTA ACTIVA</span>" : "<span style='color:red;'>CUENTA SUSPENDIDA</span>")?></strong>

&nbsp;&nbsp;&nbsp;BLANQUEO DE CLAVE: <?php echo $controles->botonGenerico('/seguridad/usuarios/reset_pws/'.$vendedor['Usuario']['id'],'controles/16-security-key.png','',array("target" => "_blank"))?>

<hr>
</div>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'padron/' . $vendedor['Vendedor']['id']))?>
<h3>SUPERVISIÓN</h3>
<table class="tbl_form">
    <tr>
        <td style="vertical-align: middle;">SUPERVISOR</td>
        <td>
            <?php
                echo $this->renderElement('vendedores/combo', array(
                    'modelo' => 'Vendedor',
                    'plugin' => 'ventas',
                    'empty' => true,
                    'selected' =>(isset($vendedor['Vendedor']['supervisor_id']) ? $vendedor['Vendedor']['supervisor_id'] : array()),
                ));
            ?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;">EMAILS DE CONTACTO</td>
        <td>
            <?php echo $frm->input('Vendedor.mail_contacto',array('value' => $vendedor['Vendedor']['mail_contacto'],'size'=>50,'maxlength'=>50));?>
        </td>
    </tr>
<hr>
</table>
<?php echo $frm->btnGuardar(array('URL' => '/ventas/vendedores'))?>

<?php echo $form->end()?>
<!--<table class="tbl_form">
    <tr>
        <td>Estado de Cuenta</td><td><input name="data[Vendedor][consultar_deuda]" value="1" <?php echo ($vendedor['Vendedor']['consultar_deuda'] ? "checked" : "")?> type="checkbox"></td>
        <td>Consultar Intranet</td><td><input name="data[Vendedor][consultar_intranet]" value="1" <?php echo ($vendedor['Vendedor']['consultar_intranet'] ? "checked" : "")?> type="checkbox"></td>
        <td><input type="submit" name="data[guardar]" value="GUARDAR"></td>
    </tr>
    
</table>-->



</div>

