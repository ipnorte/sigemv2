<table style="margin: 0 auto;width: auto;" class="tbl_form">
	<tr>
		<td style="text-align: right;">USUARIO</td><td><input type="text" id="UsuarioName" name="data[Usuario][usuario]" value="<?php echo (isset($this->data['Usuario']['usuario']) ? $this->data['Usuario']['usuario'] : '')?>"/></td>
		<td></td>
	</tr>
	<tr>
		<td style="text-align: right;">CONTRASE&Ntilde;A</td><td><input type="password" name="data[Usuario][password]"/></td>
		<td align="center" style="width:20px;"><div id="spinner_submit" style="display: none;"><?php echo $html->image('ajax-loader.gif'); ?></div></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;">
			<input type="submit" value="INGRESAR"/>
		</td>
	</tr>

</table>
<?php if(isset($error) && !empty($error)):?>
	<div style="background-color: #FFBBBB;margin:5px;width: 582px;padding: 3px;color: #B30000;border: 1px solid #B30000;background-image: url('../img/controles/error.png');background-repeat: no-repeat;background-position: 5px 5px;text-indent: 20px;min-height: 16px;font-size: small;">
		<?php echo $error?>
	</div>
<?php endif;?>