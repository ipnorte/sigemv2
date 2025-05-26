<?php echo $this->renderElement('head',array('title' => 'REPROGRAMAR ORDEN DE DESCUENTO #'.$orden_descuento_id,'plugin' => 'config'))?>
<?php //   echo $this->renderElement('personas/datos_personales',array('persona_id' => $orden['Socio']['persona_id'],'plugin' => 'pfyj'))?>
<?php echo $this->requestAction('/mutual/orden_descuentos/view/'.$orden_descuento_id.'/'.$orden['Socio']['id'].'/0/0')?>

<?php // if($orden['OrdenDescuento']['permanente'] != 1):?>

<?php 

echo $ajax->form(array('type' => 'post',
    'options' => array(
        'model'=>'OrdenDescuento',
        'update'=>'vista_previa_reprogramacion',
        'url' => array('plugin' => 'mutual','controller' => 'orden_descuentos', 'action' => 'reprogramar'),
		'loading' => "$('spinner').show();$('vista_previa_reprogramacion').hide();",
		'complete' => "$('vista_previa_reprogramacion').show();$('spinner').hide();"
    )
));
 


?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>A PARTIR DEL PERIODO</td>
			<td><?php echo $frm->input('fecha',array('dateFormat' => 'MY','label'=>'','minYear'=>'1980', 'maxYear' => date("Y") + 1))?></td>
			<td><?php echo $frm->submit("VISTA PREVIA DE LA REPROGRAMACION",array('id' => 'btn_calcula_reprogramacion'))?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('reprogramar',array('value' => 1))?>
	<?php echo $frm->hidden('id',array('value' => $orden_descuento_id))?>
</div>
</form>
<div id="spinner" style="display: none; float: left;color:red;"><?php echo $html->image('controles/ajax-loader.gif'); ?><strong>&nbsp;CALCULANDO REPROGRAMACION...</strong></div>
<div id="vista_previa_reprogramacion"></div>
<?php // else:?>
<!--<div class='notices_error' style="width: 100%">
    La Orden de Descuento <strong>NO PUEDE SER REPROGRAMADA PORQUE ES PERMANENTE</strong>
</div>-->
<?php // endif;?>

<?php //   debug($orden)?>