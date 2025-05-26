<?php 
$UID = intval(mt_rand());
echo $ajax->form(array('type' => 'post',
    'options' => array(
		'id' => "FormLogin$UID",
        'model'=>'Usuario',
        'update'=> "FormLoginResponse$UID",
        'url' => '/usuarios/verify/?h=' . $hashKey,
		'loading' => "$('FormLogin$UID').disable();$('spinner_submit').show();",
		'complete' => "$('FormLogin$UID').enable();$('spinner_submit').hide();Event.stop(event);"
    )
));
?>
<script type="text/javascript">

Event.observe(window, 'load', function(){
	$("FormLogin<?php echo $UID?>").enable();
	$("UsuarioName").focus();
});

</script>
<div id="FormLoginResponse<?php echo $UID?>" style="width:100%;margin: 0 auto;">
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
</div>
<input type="hidden" name="data[Usuario][secureKey]" value="<?php echo $hashKey?>"/>
<?php echo $frm->end();?>
