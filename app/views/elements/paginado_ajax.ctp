<div class="paging">
	<?php $paginator->options(array('url' => $this->passedArgs,'update' => 'contenido_ajax','indicator' => 'spinner'));?>
	<?php echo $paginator->counter(array('format' => __('Hoja %page% de %pages%', true)));?>

	<?php echo $paginator->prev('<< '.__('anterior', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('siguiente', true).' >>', array(), null, array('class'=>'disabled'));?>
	<div id="spinner" style="display: none; float: left;"><?php echo $html->image('controles/ajax-loader.gif'); ?></div>
	<div style="clear:both;"></div>
</div>


