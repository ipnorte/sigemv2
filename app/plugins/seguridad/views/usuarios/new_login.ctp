
<?php echo $form->create('Usuario',array('action' => 'login'));?>

<script type="text/javascript">

Event.observe(window, 'load', function(){
	$("UsuarioName").focus();
});

</script>
<table class="tbl_form" style="margin: 0 auto;">
	<tr>
		<td style="text-align: right;">USUARIO</td><td><input type="text" id="UsuarioName" name="data[Usuario][usuario]" value="<?php echo (isset($this->data['Usuario']['usuario']) ? $this->data['Usuario']['usuario'] : '')?>"/></td>
		<td></td>
	</tr>
	<tr>
		<td style="text-align: right;">CONTRASE&Ntilde;A</td><td><input type="password" name="data[Usuario][password]"/></td>
		<td align="center" style="width:20px;"><div id="spinner_submit" style="display: none;"><?php echo $html->image('controles/ajax-loader.gif'); ?></div></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;">
			<input type="submit" value="INGRESAR"/>
		</td>
	</tr>
</table>
			<?php 
				if ($session->check('Message.auth') && !$session->flash('auth')):
// 			 		//$session->flash('auth');
				endif;
			 ?>
<?php echo $frm->end();?>
