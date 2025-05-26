<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>Servicio de Consulta SIISA</h3>
<?php if($respuestas):?>
<table>
	<tr>
		<th>#</th>
		<th>Producto SIISA</th>
		<th>Política</th>
		<th>Aprobado</th>
		<th>Monto Máximo</th>
		<th>Mínimo Dispo</th>
		<th>Respuesta SIISA</th>
	</tr>
	<?php foreach ($respuestas as $respuesta):?>
		<tr>
			<td><?php echo $respuesta['respuesta']->currentExecId?></td>
			<td><?php echo $respuesta['producto_siisa']?></td>
			<td style="text-align: center;"><?php echo $respuesta['respuesta']->executedPolicy?></td>
			<td style="text-align: center;"><?php echo ($respuesta['respuesta']->aprueba ? "SI": "NO")?></td>
			<td style="text-align: center;"><?php echo $respuesta['respuesta']->monto_max?></td>
			<td style="text-align: center;"><?php echo $respuesta['respuesta']->minimoDisponible?></td>
			<td style="background-color:<?php echo ($respuesta['respuesta']->aprueba == 1 ? "#CDEB8B" : "#FFBBBB")?>;">
			
			<?php 
			if(!$respuesta['respuesta']->onError) {
			    echo $respuesta['respuesta']->decisionResult;
			}else {
			    echo "ERROR SERVICIO COD: <strong>" . $respuesta['respuesta']->oERROR->httpCode . "</strong> | MSG SIISA: " . $respuesta['respuesta']->oERROR->message;
			}
			     
			?>			
			
			</td>
		</tr>
	<?php endforeach;?>
</table>
<?php else:?>
<?php if(!empty($ERROR)):?>
<div class='notices_error'><?php echo $ERROR?></div>
<?php endif;?>
<?php endif;?>
