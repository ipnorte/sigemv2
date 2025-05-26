<?php 
$limit = 10;
$novedades = $this->requestAction('/pfyj/persona_novedades/novedades_by_persona/'.$persona_id.'/'.$limit);
?>
<?php if(!empty($novedades)):?>
<div class="areaDatoForm">

	<h4><?php echo (count($novedades) > $limit ? "ULITMAS $limit ": "") ?>NOVEDADES REGISTRADAS</h4>

	<table>
	
		<tr>
			<th>#</th>
			<th>FECHA</th>
			<th>EMISOR</th>
			<th>NOVEDAD</th>
		
		</tr>
		
		<?php foreach($novedades as $novedad):?>
		
			<tr>
				<td><?php echo $novedad['PersonaNovedad']['id']?></td>
				<td nowrap="nowrap"><strong><?php echo $util->armaFecha($novedad['PersonaNovedad']['fecha'])?></strong></td>
				<td><strong><?php echo $novedad['PersonaNovedad']['usuario']?></strong></td>
				<td><?php echo $novedad['PersonaNovedad']['descripcion']?></td>
			
			</tr>
		
		<?php endforeach;?>
	
	</table>

</div>
<?php endif;?>