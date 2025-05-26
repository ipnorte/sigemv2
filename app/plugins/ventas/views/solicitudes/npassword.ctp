<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-key"></i>&nbsp;Password</div>
    <div class="card-body">

        <div class="card mb-2">
            <div class="card-body">
                <div class="row mb-1 ">
                    <div class="col-2">Usuario Actual: <strong><?php echo strtoupper($user['Usuario']['usuario'])?></strong></div>
                    <div class="col-3">Descripción: <strong><?php echo strtoupper($user['Usuario']['descripcion'])?></strong></div>
                    <div class="col-3">Grupo/Perfil: <strong><?php echo $user['Grupo']['nombre']?></strong></div>
                </div>
                <div class="row mb-1 ">
                    <div class="col-3">E-mail: <strong><?php echo $user['Usuario']['email']?></strong></div>
                    <div class="col-2 text-danger">Caduca: <strong><?php echo $user['Usuario']['caduca']?></strong></div>
                    <div class="col-1">IP: <?php echo $_SERVER['REMOTE_ADDR']?></div>
                    <div class="col-3">HOST: <?php echo gethostbyaddr($_SERVER['REMOTE_ADDR'])?></div>
                </div>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body text-primary">
                <h4>Deber&aacute; indicar una nueva clave, la que deber&aacute; tener las siguientes caracter&iacute;sticas:</h4>
                  <ul>
                      <li style="text-indent:0px;margin:5px 0px 5px 25px;">No podr&aacute; ser id&eacute;ntica a la actual.</li>
                      <li style="text-indent:0px;margin:5px 0px 5px 25px;">No podr&aacute; contener el nombre de usuario.</li>
                      <li style="text-indent:0px;margin:5px 0px 5px 25px;">Deber&aacute; tener como m&iacute;nimo 8 (ocho) caracteres en total (m&aacute;ximo 20).</li>
                      <li style="text-indent:0px;margin:5px 0px 5px 25px;">Deber&aacute; tener al menos cuatro (4) letras y cuatro (4) n&uacute;meros</li>
                      <li style="text-indent:0px;margin:5px 0px 5px 25px;">Caracteres permitidos (Letras may&uacute;sculas y/o min&uacute;sculas, n&uacute;meros): abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789</li>
                  </ul>
                <hr/>
                <script type="text/javascript">
                $(document).ready(function (){
                    $('#UsuarioPassword').focus();
                })
                </script>
                <?php echo $frm->create(null,array('action' => 'password','onsubmit' => "return confirm('CAMBIAR SU CLAVE DE ACCESO Y REVALIDAR EL ACCESO?')"));?> 
                <div class="form-row mt-10">
                    <div class="form-group col-md-2">
                        <label for="UsuarioPassword">Password Actual</label>
                        <input class="form-control" required="" type="password" name="data[Usuario][password]" id="UsuarioPassword"  size="19" maxlength="20"/>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="UsuarioPasswordNew">Password Nuevo</label>
                        <input class="form-control" required="" type="password" name="data[Usuario][password_nuevo]" id="UsuarioPasswordNew"  size="19" maxlength="20"/>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="UsuarioPasswordNewConfirm">Confirmar Password</label>
                        <input class="form-control" required="" type="password" name="data[Usuario][password_nuevo_confirm]" id="UsuarioPasswordNewConfirm"  size="19" maxlength="20"/>
                    </div> 
                    <div class="form-group col-md-2">
                        <label for="btn_submit">&nbsp;</label>
                        <button type="submit" id="btn_submit" name="btn_submit" class="form-control btn btn-secondary btn-small"><i class="fas fa-key"></i>&nbsp;Cambiar Password</button>
                    </div>                     
                </div>
                <?php echo $frm->end();?>
            </div>
        </div>
       
        <?php // debug($user);?>
    </div>
</div>
