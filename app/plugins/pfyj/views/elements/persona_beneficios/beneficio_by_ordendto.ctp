<?php 

$beneficio = $this->requestAction('/pfyj/persona_beneficios/get_by_OrdenDto/'.$orden_dto_id);
?>
<?php if(!empty($beneficio)):?>
<div class="row">
<?php echo $beneficio['PersonaBeneficio']['string']?>
</div>

<?php else:?>

	<div class="notices_error">
		**** ERROR INTERNO DE DATOS ****
	</div>

<?php endif;?>