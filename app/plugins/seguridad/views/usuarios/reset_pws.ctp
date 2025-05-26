<h3>BLANQUEO DE CLAVE USUARIO</h3>
    <script type="text/javascript">
    window.onload=function(){$("UsuarioEmail").focus();};
    function validate(){
        if(!validRequired('UsuarioEmail','')){
            $(contenedorMsgError).update('DEBE INDICAR LA DIRECCION DE EMAIL DONDE SE ENVIA EL PIN DE ACTIVACION');
            return false;
        }
        return confirm("Resetear la Clave del Usuario <?php echo $user['Usuario']['usuario']?>");
    }
    </script>
<?php echo $frm->create(null,array('action' => 'reset_pws/'.$user['Usuario']['id'],'onsubmit' => "return validate()"))?>
	<div class="areaDatoForm">
	
	<table class="tbl_form">
		<tr>
			<td>USUARIO</td>
			<td><?php echo $frm->input('usuario',array('size'=>10,'maxlength'=>10, 'disabled' => 'disabled','value' => $user['Usuario']['usuario']));?></td>
		</tr>
		<tr>
			<td>NOMBRE COMPLETO</td>
			<td><?php echo $frm->input('descripcion',array('size'=>50,'maxlength'=>50,'disabled' => 'disabled','value' => $user['Usuario']['descripcion']));?></td>
		</tr>
		<tr>
			<td>GRUPO AL QUE PERTENECE</td>
			<td><?php echo $frm->input('grupo_id',array('size'=>50, 'disabled' => 'disabled','value' => $user['Grupo']['nombre']));?></td>
		</tr>
		<tr>
			<td>ACTIVO</td>
			<td><?php echo $frm->input('activo',array('disabled' => 'disabled','checked' => ($user['Usuario']['activo'] ? 'checked' : '')));?></td>
		</tr>
		<tr>
			<td>EMAIL DONDE RECIBE EL PIN DE VALIDACION</td>
                        <td><?php echo $frm->input('email',array('size'=>45,'maxlength'=>45,'value' => $user['Usuario']['email'],'disabled' => (!empty($user['Usuario']['email']) ? 'disabled' : '')));?></td>
		</tr>
                
	</table>
 	<?php echo $frm->hidden('id',array('value' => $user['Usuario']['id']));?>		
	
	</div>
    
    
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/seguridad/usuarios','TXT_GUARDAR' => '*** BLANQUEAR CLAVE DEL USUARIO ***'))?>
<?php //   debug($user)?>
