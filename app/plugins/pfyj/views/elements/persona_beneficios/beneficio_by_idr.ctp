<div id="beneficio_by_idr">
<?php 
$beneficio = $this->requestAction('/pfyj/persona_beneficios/get_by_idr/'.$idr);


switch ($beneficio['PersonaBeneficio']['tipo']) {
	case 'AC':
?>
		<div class="row">
			EMPRESA: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_empresa_desc']?></strong>
			|
			LUGAR DE PAGO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_reparticion']?></strong>
		</div>
		<div class="row">
			<strong><?php echo $beneficio['PersonaBeneficio']['banco']?></strong> - 
			SUC: <strong><?php echo $beneficio['PersonaBeneficio']['nro_sucursal']?></strong> - 
			CTA: <strong><?php echo $beneficio['PersonaBeneficio']['nro_cta_bco']?></strong>
		</div>
		<div class="row">
			CBU: <span style="font-size:18px;font-weight:bold;color:green;"><?php echo $beneficio['PersonaBeneficio']['cbu']?></span>
		</div>		
<?php
		break;
	case 'JP':
?>		
		<div class="row">
			ORGANISMO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_beneficio_desc']?></strong>
		</div>
		<div class="row">
			BENEFICIO: <span style="font-size:18px;font-weight:bold;"><?php echo $beneficio['PersonaBeneficio']['nro_beneficio']?></span>
			- LEY: <span style="font-size:18px;font-weight:bold;"><?php echo $beneficio['PersonaBeneficio']['nro_ley']?></span>
		</div>
<?php
		break;
	case 'JN':
?>		
		<div class="row">
			ORGANISMO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_beneficio_desc']?></strong>
		</div>
		<div class="row">
			BENEFICIO: <span style="font-size:18px;font-weight:bold;"><?php echo $beneficio['PersonaBeneficio']['nro_beneficio']?></span>
		</div>
<?php 
		break;
}
?>
</div>
