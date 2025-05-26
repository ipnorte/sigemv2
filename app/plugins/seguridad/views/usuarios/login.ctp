<script type="text/javascript">
window.onload=function(){document.getElementById('UsuarioUsuario').focus();};
</script>
<?php echo $form->create('Usuario',array('action' => 'login'));?>
		<table style="margin:2px; padding: 3px; border: 1px solid; width: 410px;font-weight: normal;font-family: verdana;" align="center">
			<tr>
				<td colspan="2"><?php echo $html->image('logos/'.Configure::read('APLICACION.logo_grande'))?></td>
			</tr>
			<tr>
				<td align="center" colspan="2" style="background: #003366;color:#FFFFFF;font-weight: bold;font-size: 13px;" valign="middle">
					<?php echo Configure::read('APLICACION.nombre')?><?php echo ' @v' . Configure::read('APLICACION.version')?>
					<?php //   echo $html->image('controles/news/lock.png')?>
				</td>
			</tr>
			<tr><td colspan="2"><br/></td></tr>
			<tr>
				<td align="right">Usuario</td>
				<td align="left"><?php echo $form->input('usuario',array('label'=>''))?></td>
			</tr>
			<tr>
				<td align="right">Contrase&ntilde;a</td>
				<td align="left"><?php echo $form->input('password',array('label'=>''))?></td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="submit" value="INGRESAR" class="submit_secure"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?
						if ($session->check('Message.auth') && !$session->flash('auth')):
							echo "<div class='notices_error'>";
					 		$session->flash('auth'); 
					 		echo "</div>";
						endif;
					 ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class='mensaje_pie'>
						Recomendamos una resoluci&oacute;n de pantalla mayor a <strong>800 x 600 p&iacute;xeles</strong>
					</div>	
					<div class='banners_pie' align="center">
						<?php echo $html->link($html->image('logos/acrobat.gif', array('border'=>0,'style'=>'margin:5px;opacity:0.4;filter:alpha(opacity=40)','onmouseover' => 'this.style.opacity=1;this.filters.alpha.opacity=100','onmouseout' => 'this.style.opacity=0.4;this.filters.alpha.opacity=40')),'http://www.adobe.com/es/products/acrobat/readstep2.html',array('target'=>'_blank'),false,false)?>
						<?php echo $html->link($html->image('logos/ie.jpg', array('border'=>0,'style'=>'margin:5px;opacity:0.4;filter:alpha(opacity=40)','onmouseover' => 'this.style.opacity=1;this.filters.alpha.opacity=100','onmouseout' => 'this.style.opacity=0.4;this.filters.alpha.opacity=40')),'http://www.microsoft.com/spain/windows/products/winfamily/ie/default.mspx',array('target'=>'_blank'),false,false)?>
						<?php echo $html->link($html->image('logos/firefox_logo.jpg', array('border'=>0,'style'=>'margin:5px;opacity:0.4;filter:alpha(opacity=40)','onmouseover' => 'this.style.opacity=1;this.filters.alpha.opacity=100','onmouseout' => 'this.style.opacity=0.4;this.filters.alpha.opacity=40')),'http://www.mozilla-europe.org/es/products/firefox/',array('target'=>'_blank'),false,false)?>
						<?php echo $html->link($html->image('logos/opera-logo.jpeg', array('border'=>0,'style'=>'margin:5px;opacity:0.4;filter:alpha(opacity=40)','onmouseover' => 'this.style.opacity=1;this.filters.alpha.opacity=100','onmouseout' => 'this.style.opacity=0.4;this.filters.alpha.opacity=40')),'http://www.opera.com/browser/download/',array('target'=>'_blank'),false,false)?>
						<?php echo $html->link($html->image('logos/google-chrome.jpg', array('border'=>0,'style'=>'margin:5px;opacity:0.4;filter:alpha(opacity=40)','onmouseover' => 'this.style.opacity=1;this.filters.alpha.opacity=100','onmouseout' => 'this.style.opacity=0.4;this.filters.alpha.opacity=40')),'http://www.google.com/chrome/index.html?hl=es',array('target'=>'_blank'),false,false)?>
					</div>
				</td>
			</tr>
		</table>

<?php echo $form->end();?>
