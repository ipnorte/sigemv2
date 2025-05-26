<div class="areaDatoForm2">
<h3>REESTABLECER PASSWORD</h3>    
<div class="notices_error">
    <p>SU PASSWORD FUE REESTABLECIDO POR EL ADMINISTRADOR DE USUARIOS</p>
    <p>&nbsp;</p>
    <p>Deber&aacute; cambiar el misma por uno nuevo, el que deber&aacute; tener las siguientes caracter&iacute;sticas:</p>
    <ul>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>No podr&aacute; ser id&eacute;ntica a la actual.</strong></li>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>Deber&aacute; tener como m&iacute;nimo 8 (ocho) caracteres en total (m&aacute;ximo 12).</strong></li>
        <li style="text-indent:0px;margin:5px 0px 5px 25px;"><strong>Deber&aacute; tener letras y n&uacute;meros (abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789).</strong></li>
    </ul>
    <p>&nbsp;</p>
<!--    <p>Algunas recomendaciones para elegir una clave segura:</p>
    <p>&nbsp;</p>
    <p>Mezclar dos palabras y alternando mayusculas (ej. Juan Perez – jPuEaRnEz). </p>
    <p>Mezclar una palabra con números (ej. flash 9708 – F9L7A0S8H). </p>
    <p>Utilizar mayúsculas de manera desordenada pero consistente (por ejemplo, siempre en mayúscula la segunda y quinta letras). </p>
    <p>Crear un acrónimo con sentido para uno mismo (NpRMn – No Puedo Recordar Mi Nombre). </p>
    <p>No utilizar todo o una parte del nombre propio. </p>
    <p>No repetir el nombre de usuario. </p>
    <p>Evitar utilizar nombres de personas de tu familia, de tus mascotas, o de tus amigos. </p>
    <p>No utilizar números importantes o significativos para ti, como el número de teléfono, de DNI, de seguridad social o la fecha de nacimiento, ya que pueden ser fácilmente identificables. </p>
    <p>Evitar los nombres de lugares favoritos, de origen o de residencia. </p>
    
    <p>&nbsp;</p>-->
    <p><strong>En caso de que Ud. olvide su PASSWORD, podr&aacute; requerir al Administrador de Usuarios que inicie el proceso de reestablecimiento de password.</strong></p>
</div>
<div class="areaDatoForm">
    <h3>Datos de su Acceso</h3>
    <table class="tbl_form">
        <tr><td>USUARIO</td><td><strong><?php echo strtoupper($user['Usuario']['usuario'])?> </strong></td></tr>
        <tr><td>DESCRIPCION</td><td><strong><?php echo strtoupper($user['Usuario']['descripcion'])?> </strong></td></tr>
        <tr><td>GRUPO / PERFIL</td><td><strong><?php echo $user['Grupo']['nombre']?></strong></td></tr>
        <tr><td>EMAIL DE VALIDACION</td><td><strong><?php echo $user['Usuario']['email']?></strong></td></tr>
        <tr><td>TERMINAL</td><td><strong><?php echo $_SERVER['REMOTE_ADDR']?></strong></td></tr>
    </table>
    <hr/>
    <script type="text/javascript">
        function validate(){
//            var ret = true;
//            var psw = $('UsuarioPasswordActual').getValue();
//            $('UsuarioPasswordActual').removeClassName('form-error');
//            if(psw === ''){
//                $('UsuarioPasswordActual').focus();
//                $('UsuarioPasswordActual').addClassName('form-error');
//                alert("Debe indicar el Password Actual");
//                return false;            
//            }
//            var psw1 = $('UsuarioPasswordNuevo').getValue();
//            $('UsuarioPasswordNuevo').removeClassName('form-error');
//            if(psw1 === ''){
//                $('UsuarioPasswordNuevo').focus();
//                $('UsuarioPasswordNuevo').addClassName('form-error');
//                alert("Debe indicar el Nuevo Password");
//                return false;            
//            }
//            var psw2 = $('UsuarioPasswordNuevoConfirm').getValue();
//            $('UsuarioPasswordNuevoConfirm').removeClassName('form-error');
//            if(psw2 === ''){
//                $('UsuarioPasswordNuevoConfirm').focus();
//                $('UsuarioPasswordNuevoConfirm').addClassName('form-error');
//                alert("Debe confirmar su nuevo Password");
//                return false;            
//            }
            return true;
        }
    </script>
    <?php echo $frm->create(null,array('action' => 'password_check','onsubmit' => "return validate()"))?>
    <div class="notices">Cuando su PASSWORD sea modificado correctamente, deber&aacute; revalidar su acceso.</div>
    <table class="tbl_form">
        <tr>
            <td>PASSWORD ACTUAL</td><td><input type="password" name="data[Usuario][password_actual]" id="UsuarioPasswordActual" size="20" maxlength="20"/></td>
        </tr>
        <tr>
            <td>NUEVO PASSWORD</td><td><input type="password" name="data[Usuario][password_nuevo]" id="UsuarioPasswordNuevo" size="20" maxlength="12"/></td>
        </tr>
        <tr>
            <td>CONFIRMAR NUEVO PASSWORD</td><td><input type="password" name="data[Usuario][password_nuevo_confirm]" id="UsuarioPasswordNuevoConfirm" size="20" maxlength="12"/></td>
        </tr>
        <tr>
            <td colspan="4"><input type="submit" value="CAMIBIAR CONTRASEÑA Y REVALIDAR ACCESO" id="btn_submit" /></td>
        </tr>
    </table>
    
    <?php echo $frm->end()?>
</div>
</div>
