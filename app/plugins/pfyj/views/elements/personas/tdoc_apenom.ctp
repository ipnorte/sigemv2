<?php 
    $persona = $this->requestAction('/pfyj/personas/get_persona/'.$persona['Persona']['id']);
    
    $INI_FILE = $_SESSION['MUTUAL_INI'];
    $MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? TRUE : FALSE); 
    
?>

<div class="areaDatoForm2" style="background-color:#C8DDEF;">
<h3 style="font-family: verdana;">

 <?php // echo (strlen($persona['Persona']['cuit_cuil']) == 11 ? $persona['Persona']['cuit_cuil'] : "")?>
<?php if(isset($link) && $link):?>
	<?php echo $this->renderElement('personas/apenom_link_padron',array('persona' => $persona, 'plugin' => 'pfyj'))?>
	<?php //   echo $controles->openWindow($persona['Persona']['apellido'] .', '. $persona['Persona']['nombre'],'/pfyj/personas/view/'.$persona['Persona']['id'])?>
<?php else:?>
	<?php echo $persona['Persona']['apenom'];?>
<?php endif;?>
&nbsp; :: &nbsp;
    <span style="font-size: 90%; font-weight: normal;">
        <?php echo $persona['Persona']['tdoc_ndoc']?>
        &nbsp;|&nbsp;
        CUIT: <?php echo $persona['Persona']['cuit_cuil']?>
    </span> 
 
<?php if($persona['Persona']['fallecida'] == 1):?>
	<span style="color:white;background-color:red;font-size: 10px;font-weight: normal;padding:2px;">F</span>
<?php endif;?> 
        
</h3>
    
<?php if(!empty($persona['Persona']['socio_nro'])):?>
<span style="color:#666666;">
<!-- <div class="areaDatoForm2" style="color:gray;"> -->
	<span style="font-size: 11px;"><strong><?php echo $persona['Persona']['socio']?></strong>
	|
	FECHA DE ALTA:&nbsp;<strong><?php echo $util->armaFecha($persona['Persona']['socio_fecha_alta'])?></strong>
	|
	CATEGORIA: <strong><?php echo $persona['Persona']['socio_categoria']?></strong>
	|
	ESTADO:&nbsp;<strong><?php echo ($persona['Persona']['socio_activo'] == 1 ? '<span style="color:green;">'.$persona['Persona']['socio_status'].'</span>' : '<span style="color:red;">'.$persona['Persona']['socio_status'].'</span>')?></strong>
	<?php if($persona['Persona']['socio_activo'] == 0):?>
		(<strong><?php echo $util->armaFecha($persona['Persona']['socio_fecha_baja'])?></strong>)
		<?php if(!empty($persona['Persona']['socio_periodo_hasta'])):?>
		|A PARTIR DE: <strong><?php echo $util->periodo($persona['Persona']['socio_periodo_hasta'])?></strong>
		<?php endif;?>
		|
		MOTIVO: <strong><?php echo $persona['Persona']['socio_baja']?></strong>
	<?php endif;?>
<!--</div>	-->
	</span>
	<br/>
	<?php if(!empty($persona['Persona']['socio_calificaciones'])):?>
	<span style="font-size:11px;">
	CALIFICACIONES:
	<?php foreach($persona['Persona']['socio_calificaciones'] as $calificacion):?>
		<span class="<?php echo $calificacion['SocioCalificacion']['calificacion']?>">
		<?php echo $calificacion['SocioCalificacion']['calificacion_desc']?>
		(<?php echo $calificacion['SocioCalificacion']['cantidad']?>)
		</span>
		|
	<?php endforeach;?>
	ULTIMA: <strong><span class="<?php //   echo $persona['Persona']['socio_codigo_ultima_calificacion']?>"><?php echo $persona['Persona']['socio_ultima_calificacion']?></span></strong>
	&nbsp;(<?php echo $util->armaFecha($persona['Persona']['socio_fecha_ultima_calificacion'])?>)
	</span>
	<?php else:?>
		<strong>*** SIN CALIFICACIONES ***</strong>
	<?php endif;?>
	|
    <?php //   echo $controles->linkModalBox('ULTIMO STOCK DE DEUDA',array('title' => 'ULTIMO STOCK DE DEUDA','url' => '/mutual/liquidacion_socios/cargar_scoring_by_socio/'.$persona['Persona']['socio_nro'],'h' => 450, 'w' => 750))?>
    <?php echo $controles->btnModalBox(array('title' => 'ULTIMO STOCK DE DEUDA','img'=> 'calendar_2.png','texto' => 'Scoring','url' => '/mutual/liquidacion_socios/cargar_scoring_by_socio/'.$persona['Persona']['socio_nro'],'h' => 450, 'w' => 750))?>   
        <?php if($MOD_BCRA):?>
        &nbsp;|&nbsp;<span style="font-weight: bold;"><?php echo $controles->btnModalBox(array('title' => 'CONSULTA BCRA','img'=> 'vcard.png','texto' => 'BCRA','url' => '/pfyj/personas/consultaBCRA/'.$persona['Persona']['id'],'h' => 450, 'w' => 850))?></span>
        <?php endif;?>
        <?php if(!empty($persona['Persona']['socio_resumen_situaciones'])):?>
        <br/>
        <span style="font-size:11px;">
        SITUACIONES: <?php echo $persona['Persona']['socio_resumen_situaciones']?>            
        </span>
        <?php endif;?>        
</span>
<?php endif;?>
</div>
<?php //   debug($persona)?>