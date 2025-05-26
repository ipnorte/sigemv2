<?php echo $this->renderElement('title')?>
<?php echo $this->renderElement('menu')?>

<?php //   debug($tickets)?>

<?php if(!empty($tickets)):?>

	<table>
	
		<tr>
			<th>#</th>
			<th>EMITIDO POR</th>
			<th>INICIADO EL</th>
			<th>ESTADO</th>
			<th>PRIORIDAD</th>
			<th>ASUNTO</th>
			<th>ARCHIVO ADJUNTO</th>
		</tr>
		
		<?php foreach($tickets as $ticket):?>
		
			<tr>
			
				<td align="center"><?php echo $ticket['SoporteTicket']['id']?></td>
				<td align="center"><?php echo $ticket['SoporteTicket']['emitido_por']?></td>
				<td align="center"><?php echo $ticket['SoporteTicket']['fecha_inicio']?></td>
				<td align="center"><?php echo $estados[$ticket['SoporteTicket']['estado']]?></td>
				<td align="center"><?php echo $prioridades[$ticket['SoporteTicket']['prioridad']]?></td>
				<td><?php echo $ticket['SoporteTicket']['asunto']?></td>
				<td><?php echo (!empty($ticket['SoporteTicket']['archivo']) ? $html->link($ticket['SoporteTicket']['archivo'],"/files/soporte/".$ticket['SoporteTicket']['archivo'],array('target' => '_blank')) : "")?></td>
			
			</tr>
		
		<?php endforeach;?>
	
	</table>

<?php endif;?>