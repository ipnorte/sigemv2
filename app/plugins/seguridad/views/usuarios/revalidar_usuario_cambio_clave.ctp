<div class="areaDatoForm2">
    <h3>REESTABLECER PASSWORD :: PASO 3 - Cambio de Clave o Contrase&ntilde;a de Acceso</h3>  

<div class="notices_error">
    <p>Deber&aacute; indicar una nueva clave, la que deber&aacute; tener las siguientes caracter&iacute;sticas:</p>
    <ul>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>No podr&aacute; ser id&eacute;ntica a la actual.</strong></li>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>No podr&aacute; contener el nombre de usuario.</strong></li>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>Deber&aacute; tener como m&iacute;nimo 8 (ocho) caracteres en total (m&aacute;ximo 20).</strong></li>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>Deber&aacute; tener al menos cuatro (4) letras y cuatro (4) n&uacute;meros</strong></li>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>Caracteres permitidos (Letras may&uacute;sculas y/o min&uacute;sculas, n&uacute;meros):</strong> abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789</li>
    </ul>    
</div>
</div>
<div class="areaDatoForm">    
    <script type="text/javascript">
    window.onload=function(){document.getElementById('UsuarioPasswordNuevo').focus();};
    function validate(){
        return confirm("CAMBIAR SU CLAVE DE ACCESO Y REVALIDAR EL ACCESO?");
    }
    </script>
    <?php echo $frm->create(null,array('action' => 'password_check/3','onsubmit' => "return validate()"))?>
    <table class="tbl_form">
        <tr>
            <td>INGRESE SU NUEVA CLAVE</td><td><input type="password" name="data[Usuario][password_nuevo]" id="UsuarioPasswordNuevo" size="19" maxlength="20"/></td>
        </tr>
        <tr>
            <td>CONFIRME SU NUEVA CLAVE</td><td><input type="password" name="data[Usuario][password_nuevo_confirm]"  size="19" maxlength="20"/></td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
    </table>  
    <div class="notices_ok">
        <p>Cuando su PASSWORD sea modificado correctamente, deber&aacute; revalidar su acceso.</p>
        <ul>
            <li style="text-indent:0px;margin:5px 0px 5px 25px;">Recuerde que el password es sensible a las may&uacute;sculas y min&uacute;sculas.</li>    
            <li style="text-indent:0px;margin:5px 0px 5px 25px;">En caso de que Ud. olvide su PASSWORD, podr&aacute; requerir al Administrador de Usuarios que inicie el proceso de reestablecimiento de password.</li>
            <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>ANOTE SU PASSWORD EN UN LUGAR SEGURO</strong></li>
    </ul>   
    
    </div>
    <br/>
    <input type="submit" value="*** CAMBIAR CLAVE DE ACCESO ***" id="btn_submit" />
    <?php echo $frm->end()?>
</div>