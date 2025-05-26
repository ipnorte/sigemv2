<div class="areaDatoForm2">
    <h3>REESTABLECER PASSWORD :: PASO 2 - Verificaci&oacute;n de PIN</h3>  


    <p>Se envio un correo electronico a la cuenta <strong><?php echo $user['Usuario']['email']?></strong> con el <strong>n&uacute;mero de PIN generado</strong>.</p>
    <br/>
    <p style="color: red;"><strong>VERIFIQUE SU BUZON DE EMAIL.</strong>  Si no lo encuentra en su bandeja de entrada, verifique que el mismo no se encuentre marcado como SPAM por su proveedor de correo electr&oacute;nico.</p>
    <br/>
    <p>Tenga en cuenta que email puede demorar unos minutos en llegar a su casilla de correo.</p>
    <br/>
    <p>Copie y pegue el PIN recibido en el siguiente campo y proceda a validarlo.</p>
    <br/>
    <br/>

</div>
<div class="areaDatoForm">  
    <script type="text/javascript">
    function validate(){
        return confirm("VALIDAR PIN?");
    }
    </script>    
    <?php echo $frm->create(null,array('action' => 'password_check/2','onsubmit' => "return validate()"))?>
    <table class="tbl_form">
        <tr>
            <td>INGRESE EL PIN RECIBIDO</td><td><input type="text" name="data[Usuario][pin_confirma]" id="UsuarioPinConfirma" size="12" maxlength="10"/></td>
            <td><input type="submit" value="*** VERIFICAR PIN ***" id="btn_submit" /></td>
        </tr>
    </table>    
    <?php echo $frm->end()?>
</div>