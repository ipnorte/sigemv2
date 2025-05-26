<div class="areaDatoForm2">
    <h3>REESTABLECER PASSWORD :: PASO 1 - Solicitar PIN y Validar direcci&oacute;n de Correo Electr&oacute;nico (email) </h3>   
    
    <table class="tbl_form">
        <tr><td>USUARIO</td><td><?php echo strtoupper($user['Usuario']['usuario'])?></td></tr>
        <tr><td>DESCRIPCION</td><td><?php echo strtoupper($user['Usuario']['descripcion'])?></td></tr>
        <tr><td>GRUPO / PERFIL</td><td><?php echo $user['Grupo']['nombre']?></td></tr>
        <tr><td>EMAIL DE VALIDACION</td><td><strong><?php echo $user['Usuario']['email']?></strong></td></tr>
        <tr><td>CADUCIDAD DE LA CLAVE</td><td style="color: red;"><strong><?php echo $user['Usuario']['caduca']?></strong></td></tr>
        <tr><td>IP</td><td><?php echo $_SERVER['REMOTE_ADDR']?></td></tr>
        <tr><td>HOST</td><td><?php echo gethostbyaddr($_SERVER['REMOTE_ADDR'])?></td></tr>
    </table>    
<!--
<div class="notices_error">
    <p>SU PASSWORD FUE REESTABLECIDO POR EL ADMINISTRADOR DE USUARIOS O SE ENCUENTRA VENCIDA</p>
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
    <!--<p><strong>En caso de que Ud. olvide su PASSWORD, podr&aacute; requerir al Administrador de Usuarios que inicie el proceso de reestablecimiento de password.</strong></p>-->
</div>
<div class="areaDatoForm">
    <script type="text/javascript">
        function validate(){
            return true;
        }
    </script>
    <?php if(!empty($user['Usuario']['email'])):?>
        <?php echo $frm->create(null,array('action' => 'password_check/1','onsubmit' => "return validate()"))?>
        <table class="tbl_form">
            <tr>
                <td>EMAIL DONDE SE ENVIARA EL PIN</td><td><input type="text" name="data[Usuario][email_confirma]" id="UsuarioEmailConfirma" size="45" maxlength="45" value="<?php echo $user['Usuario']['email']?>" <?php echo (!empty($user['Usuario']['email']) ? "disabled" : "")?>/></td>
            </tr>
            <tr>
                <td colspan="4"><input type="submit" value="*** SOLICITAR EL PIN DE ACCESO ***" id="btn_submit" /></td>
            </tr>
        </table>
        <?php echo $frm->hidden('Usuario.email',array('value' => $user['Usuario']['email']))?>
        <?php echo $frm->end()?>
    
        <p>
            <br/>
            <a href="<?php echo $this->base?>/seguridad/usuarios/password_check/2" style="text-decoration: underline;">Ya recib&iacute; en dirección de email un PIN (click aqu&iacute;)</a></p>
    <?php else:?>
    <div class="notices_error">
        <p style="font-weight: bold;">SU USUARIO NO CUENTA CON UNA DIRECCION DE EMAIL VALIDA</p>
        <br/>
        <p>A los efectos de poder iniciar el proceso de cambio de constrase&ntilde;a, deber&aacute; comunicarse con
            la Administraci&oacute;n a los efectos de declarar la cuenta de correo electr&oacute;nico (email) que
            usar&aacute; para este proceso y en lo sucesivo.
        </p>
        <br/>
        <p><?php echo Configure::read('APLICACION.nombre_fantasia')?></p>
        <p><?php echo Configure::read('APLICACION.domi_fiscal')?></p>
        <p><?php echo Configure::read('APLICACION.telefonos')." - email: ".Configure::read('APLICACION.email')?></p>
        <br/> 
        <br/>
        <a href="<?php echo $this->base?>/seguridad/usuarios/logout" style="text-decoration: underline;color: red;">Ya tengo asignada una dirección de email (click aqu&iacute;)</a>
    </div>
    <?php endif;?>
</div>
</div>

