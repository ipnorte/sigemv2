<?php if(Configure::read('debug') < 2):?>
<h2>MENSAJE DEL SISTEMA</h2>
<p class="error">
	<strong>ERROR:</strong>
	El recurso solicitado no existe.
</p>
<?php else:?>
<h2><?php __('Missing Controller'); ?></h2>
<p class="error">
	<strong><?php __('Error'); ?>: </strong>
	<?php echo sprintf(__('%s could not be found.', true), "<em>" . $controller . "</em>");?>
</p>
<p class="error">
	<strong><?php __('Error'); ?>: </strong>
	<?php echo sprintf(__('Create the class %s below in file: %s', true), "<em>" . $controller . "</em>", APP_DIR . DS . "controllers" . DS . Inflector::underscore($controller) . ".php");?>
</p>
<pre>
&lt;?php
class <?php echo $controller;?> extends AppController {

	var $name = '<?php echo $controllerName;?>';
}
?&gt;
</pre>
<p class="notice">
	<strong><?php __('Notice'); ?>: </strong>
	<?php echo sprintf(__('If you want to customize this error message, create %s', true), APP_DIR . DS . "views" . DS . "errors" . DS . "missing_controller.ctp");?>
</p>
<?php endif;?>