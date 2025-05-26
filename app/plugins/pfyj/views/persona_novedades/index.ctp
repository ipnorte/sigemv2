<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>REGISTRO DE NOVEDADES</h3>

<div class="actions">
<?php echo $controles->botonGenerico('add/'.$persona['Persona']['id'],'controles/note.png','NUEVA NOVEDAD')?>
</div>

<?php if(!empty($novedades)):?>
	<?php echo $this->renderElement('paginado')?>
	<table style="width: 90%;">
	
		<tr>
			<th>#</th>
			<th></th>
			<th><?php echo $paginator->sort('FECHA','PersonaNovedad.fecha');?></th>
			<th><?php echo $paginator->sort('EMITIDA POR','PersonaNovedad.usuario');?></th>
			<th>NOVEDAD</th>
			<th>ADJUNTO</th>
		</tr>
		
		<?php foreach($novedades as $novedad):?>
		
			<tr>
				<td align="center"><?php echo $novedad['PersonaNovedad']['id']?></td>
				<td style="width: 1%;"><?php echo $controles->botonGenerico('comentar_novedad/'.$novedad['PersonaNovedad']['id'],'controles/comments.png')?></td>
				<td style="width: 5%;text-align: center;" nowrap="nowrap"><strong><?php echo $util->armaFecha($novedad['PersonaNovedad']['fecha'])?></strong></td>
				<td style="width: 10%;text-align: center;"><strong><?php echo $novedad['PersonaNovedad']['usuario']?></strong></td>
				<td style="width: 84%;"><?php echo $novedad['PersonaNovedad']['descripcion']?></td>
				<td align="center">
				<?php if(!empty($novedad['PersonaNovedad']['archivo_adjunto'])):?>
					<a href="<?php echo $this->base?>/files/socios/novedades/<?php echo $novedad['PersonaNovedad']['archivo_adjunto']?>" target="_blank"><?php echo $html->image('controles/attach.png',array("border"=>"0",'alt'=>'Ver Adjunto'))?></a>
				<?php endif;?>
				</td>
			</tr>
			
			<?php if(!empty($novedad['PersonaNovedadComentario'])):?>
		
				<?php foreach($novedad['PersonaNovedadComentario'] as $comentario):?>
					<?php $style="background: #eaecf1;font-style: italic;"?>
					<tr>
						<td style="<?php echo $style?>"></td>
						<td style="<?php echo $style?>"></td>
						<td style="<?php echo $style?>;text-align: center;"><?php echo $util->armaFecha($comentario['fecha'])?></td>
						<td style="<?php echo $style?>;text-align: center;"><?php echo $comentario['usuario']?></td>
						<td style="<?php echo $style?>"><?php echo $comentario['descripcion']?></td>
						<td style="<?php echo $style?>" align="center">
						
						<?php if(!empty($comentario['archivo_adjunto'])):?>
							<a href="<?php echo $this->base?>/files/socios/novedades/<?php echo $comentario['archivo_adjunto']?>" target="_blank"><?php echo $html->image('controles/attach.png',array("border"=>"0",'alt'=>'Ver Adjunto'))?></a>
						<?php endif;?>						
						
						</td>
					</tr>				
				
				<?php endforeach;?>
		
			<?php endif;?>
		
		<?php endforeach;?>
		
	
	</table>
	<?php echo $this->renderElement('paginado')?>

<?php //   debug($novedades)?>

<?php endif;?>