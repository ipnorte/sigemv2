<?php 
$beneficios = $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$persona_id);
?>

<?php foreach($beneficios as $beneficio):?>

	<?php debug($beneficio)?>

<?php endforeach;?>

