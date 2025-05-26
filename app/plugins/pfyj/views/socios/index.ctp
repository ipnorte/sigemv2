<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona));?>

<h3>INFORMACION ACTUAL DEL SOCIO</h3>

<script type="text/javascript">
    function confirmarModificaCuotaForm(){
            var msgConfirm = "ATENCION!\n\n";
            msgConfirm = msgConfirm + "MODIFICAR CUOTA SOCIAL #<?php echo $socio['Socio']['id']?>\n";
            msgConfirm = msgConfirm + "<?php echo $util->globalDato($socio['Persona']['tipo_documento'])." ".$socio['Persona']['documento']." - ".$socio['Persona']['apellido'].", ".$socio['Persona']['nombre']?>";
            msgConfirm = msgConfirm + "\n\n";
            var oIMPO = document.getElementById("SocioImporteCuotaSocial");
            impoTXT = new Number(oIMPO.value);
            if(impoTXT == 0) {
                msgConfirm = msgConfirm + "IMPORTE MENSUAL: ** GENERAL **";
            } else { 
                impoTXT = impoTXT.toFixed(2);                
                msgConfirm = msgConfirm + "IMPORTE MENSUAL: " + impoTXT;
            }
            
            var checkbox = document.getElementById('periodo_hasta_checkbox');
            var periodoHastaTd = document.getElementById('periodo_hasta_td');
            if (checkbox && periodoHastaTd) {
                var selectElements = periodoHastaTd.querySelectorAll('select');
                if (!checkbox.checked) {
                    periodoHastaTd.style.display = 'none';
                    selectElements.forEach(function(select) {
                        select.disabled = true;
                    });
                }
            }            
            
            return confirm(msgConfirm);
    }  

function togglePeriodoHasta() {
    var checkbox = document.getElementById('periodo_hasta_checkbox');
    var periodoHastaTd = document.getElementById('periodo_hasta_td');
    if (!periodoHastaTd) return; // Asegurarse de que el elemento existe

    var selectElements = periodoHastaTd.querySelectorAll('select');

    if (checkbox.checked) {
        periodoHastaTd.style.display = 'table-cell';
        selectElements.forEach(function(select) {
            select.disabled = false;
        });
    } else {
        periodoHastaTd.style.display = 'none';
        selectElements.forEach(function(select) {
            select.disabled = true;
        });
    }
}

Event.observe(window, 'load', function(){
    var checkbox = document.getElementById('periodo_hasta_checkbox');
    var periodoHastaTd = document.getElementById('periodo_hasta_td');
    if (checkbox && periodoHastaTd) {
        var selectElements = periodoHastaTd.querySelectorAll('select');
        if (!checkbox.checked) {
            periodoHastaTd.style.display = 'none';
            selectElements.forEach(function(select) {
                select.disabled = true;
            });
        }
    }
});

</script>

<?php if(empty($socio)):?>

	<div class='notices_error'>PERSONA NO ASOCIADA A LA MUTUAL!</div>
	<div class="row">
		<?php echo $controles->botonGenerico('alta_directa/'.$persona['Persona']['id'],'controles/add.png','ALTA DIRECTA COMO SOCIO')?>
	</div>
        <?php // exit;?>

<?php else:?>
	<div class="areaDatoForm">
		<h3>SOCIO #<?php echo $socio['Socio']['id']?></h3>
		<div class="row">
			FECHA DE ALTA:&nbsp;<strong><?php echo $util->armaFecha($socio['Socio']['fecha_alta'])?></strong>
			&nbsp;&nbsp;
			ESTADO:&nbsp;<strong><?php echo ($socio['Socio']['activo'] == 1 ? '<span style="color:green;">VIGENTE</span>' : '<span style="color:red;">NO VIGENTE</span>')?></strong>
			<?php if($socio['Socio']['activo'] == 0):?>
				(<strong><?php echo $util->armaFecha($socio['Socio']['fecha_baja'])?></strong>)
				<?php if(!empty($socio['Socio']['periodo_hasta'])):?>
				&nbsp;|&nbsp;
				A PARTIR DE: <strong><?php echo $util->periodo($socio['Socio']['periodo_hasta'])?></strong>
				<?php endif;?>
				&nbsp;|&nbsp;
				MOTIVO: <strong><?php echo $util->globalDato($socio['Socio']['codigo_baja'])?></strong>
				<div class="row"><br/></div>
				<div class="row">
					<?php if($socio['Persona']['fallecida'] == 0) echo $controles->botonGenerico('reactivar/'.$persona['Socio']['id'],'controles/accept.png','VOLVER A VIGENTE')?>
				</div>				
			<?php endif;?>
			<br/>
			<div class="row">
			CATEGORIA: <strong><?php echo $util->globalDato($socio['Socio']['categoria'])?></strong>
			<?php if($socio['Persona']['fallecida'] == 0) echo $controles->botonGenerico('modificar_categoria/'.$persona['Socio']['id'],'controles/edit.png','')?>
			</div>			
		</div>
		<?php if(!empty($socio['Socio']['observaciones'])):?>
			<div class="areaDatoForm2">
				OBSERVACIONES:<br/><br/>
				<?php echo $socio['Socio']['observaciones']?>
			</div>
		<?php endif;?>
                <div class="areaDatoForm2">
                    
                    <?php echo $this->renderElement('orden_descuento/resumen_by_id',array('id'=>$ordenDto['OrdenDescuento']['id'],'detallaCuotas'=>false,'plugin' => 'mutual')) ?>
                    <div class="areaDatoForm3">
                        CUOTA SOCIAL GENERAL (Mensual): <strong><?php echo $cuotaSocialGeneral?></strong>
                    </div>
                    
                    <h4>Cuota Social del Socio</h4>
                    IMPORTE MENSUAL: 
                    <?php if($socio['Socio']['importe_cuota_social'] !== 0): ?>
                    <strong style="color: white; background-color: red; padding: 3px;"><?php echo $socio['Socio']['importe_cuota_social']?></strong>
                        <?php if(!empty($socio['Socio']['periodo_hasta_importe_cuota_social'])): ?>
                    &nbsp;|&nbsp;Hasta <strong><?php echo $util->periodo($socio['Socio']['periodo_hasta_importe_cuota_social'])?></strong> &nbsp;|&nbsp;
                        <?php endif;?>
                    <?php else:?>
                    ** GENERAL **
                    <?php endif;?>
                    <?php if($socio['Persona']['fallecida'] == 0) echo $controles->btnModalBox(array('t itle' => 'CUOTA SOCIAL PARTICULAR','img'=> 'edit.png','texto' => '','url' => 'modificar_cuotasocial/'.$persona['Socio']['id'],'h' => 350, 'w' => 550))?>
                    
                    <?php if(floatval($socio['Socio']['importe_cuota_social']) != 0 || floatval($cuotaSocialGeneral) != 0): ?>
                    &nbsp;|&nbsp;<strong><?php if($socio['Persona']['fallecida'] == 0) echo $controles->botonGenerico('cobro_cuota_adelantada/'.$persona['Socio']['id'],'controles/money.png','Cobrar Cuotas Sociales Adelantadas', array('target' => 'blank'))?></strong>
                    <?php endif;?>
                </div>
	</div>
	<?php if($socio['Socio']['activo'] == 1 || $socio['Persona']['fallecida'] == 0):?>
		<div class="row">
			<?php echo $controles->botonGenerico('baja/'.$socio['Socio']['id'],'controles/stop1.png','DAR DE BAJA AL SOCIO')?>
		</div>	
	<?php endif;?>


                
        <div class="areaDatoForm">
            <h3>INFORMES CREDITICIOS</h3>
                <div class="row">

                        <?php if($socio['Persona']['fallecida'] == 0) echo $controles->botonGenerico('alta_informe/'.$socio['Socio']['id'],'controles/add.png','Nuevo Informe')?>
                </div>
            <?php if(!empty($socio['SocioInforme'])):?>
            <br/>
            <table>
                <tr>
                    <th>Lote</th>
                    <th>Empresa</th>
                    <th>Tipo</th>
                    <th>Periodo Calculo</th>
                    
                    <th>Deuda</th>
                    <th>Usuario</th>
                    <th>created</th>

                </tr>
                <?php foreach($socio['SocioInforme'] as $socioinfo):?>
                <tr>
                    <td><?php echo $socioinfo['socio_informe_lote_id']?></td>
                    <td><?php echo $util->globalDato($socioinfo['empresa'])?></td>
                    <td style="text-align: center;"><?php echo $socioinfo['tipo_novedad']?></td>
                    <td style="text-align: center;"><?php echo $util->periodo($socioinfo['periodo_hasta'])?></td>
                    
                    <td style="text-align: right;"><?php echo $util->nf($socioinfo['deuda_informada'])?></td>
                    <td><?php echo $socioinfo['user_created']?></td>
                    <td><?php echo $socioinfo['created']?></td>
                </tr>
                <?php endforeach;?>
            </table>
            <?php endif;?>  
        </div>         
              
<?php endif;?>
        
       
        
<?php if(!empty($persona['SocioSolicitud'])):?>
	<div class="areaDatoForm">
	<h3>SOLICITUDES</h3>
	
		<table>
		
			<tr>
			
				<th>NRO</th>
				<th>TIPO</th>
				<th>ESTADO</th>
				<th>FECHA</th>
				<th>BENEFICIO DTO. C.SOC.</th>
				<th>INICIA C.SOC.</th>
<!--				<th></th>-->
				<th></th>
			</tr>
			
			<?php foreach($persona['SocioSolicitud'] as $solicitud):?>
			
				<tr>
				
					<td align="center"><?php echo $solicitud['id']?></td>
					<td align="center"><?php echo ($solicitud['tipo_solicitud'] == 'A' ? 'ALTA' : ($solicitud['tipo_solicitud'] == 'B' ? 'BAJA' : ($solicitud['tipo_solicitud'] == 'R' ? 'REEMP.' : 'MODIF.')))?></td>
					<td align="center"><?php echo ($solicitud['aprobada'] == 1 ? '<span style="color:green;">APROBADA</span>' : '<span style="color:red;">PENDIENTE</span>')?></td>
					<td><?php echo $util->armaFecha($solicitud['fecha'])?></td>
					<td><?php if(!empty($solicitud['persona_beneficio_id']))echo $this->requestAction('/pfyj/persona_beneficios/view/'. $solicitud['persona_beneficio_id'])?></td>
					<td align="center"><?php echo $util->periodo(($solicitud['periodo_ini']!='000000' ? $solicitud['periodo_ini'] : null))?></td>
<!--					<td align="center"><?php //   echo  ($solicitud['aprobada'] == 1 ? $solicitud['user_modified'] .' - '. $solicitud['modified'] : '<strong>' . $html->link('APROBAR','/pfyj/socio_solicitudes/aprobar/'.$solicitud['id']) . '</strong>') ?></td>-->
					<td align="center"><?php echo $controles->btnImprimir('','/pfyj/socio_solicitudes/view/'.$solicitud['id'].'/view_pdf/blank','blank')?></td>
				</tr>
			
			<?php endforeach;?>
		
		</table>
	</div>
<?php endif;?>
<?php if(!empty($socio)):?>
	<div class="areaDatoForm">	
	<h3>HISTORIAL DE CALIFICACIONES</h3>
			<div class="row">
				<?php if($socio['Persona']['fallecida'] == 0) echo $controles->botonGenerico('nueva_calificacion/'.$socio['Socio']['id'],'controles/add.png','NUEVA CALIFICACION')?>
			</div>			
			<?php if(count($socio['SocioCalificacion']) != 0):?>
				<table>
					<tr><th colspan="5">REGISTRO DE LAS ULTIMAS 12 CALIFICACIONES</th></tr>
					<tr>
						<th>FECHA</th>
                                                <th>PERIODO</th>
						<th>CALIFICACION</th>
						<th>USUARIO</th>
                                                <th></th>
					</tr>
					<?php foreach($socio['SocioCalificacion'] as $calificacion):?>
						<tr>
							<td class="<?php echo $calificacion['calificacion']?>"><?php echo $calificacion['created']?></td>
                                                        <td class="<?php echo $calificacion['calificacion']?>"><?php echo $util->periodo($calificacion['periodo'])?></td>
							<td class="<?php echo $calificacion['calificacion']?>"><?php echo $util->globalDato($calificacion['calificacion'])?></td>
							<td class="<?php echo $calificacion['calificacion']?>"><?php echo $calificacion['user_created']?></td>
                                                        <td class="<?php echo $calificacion['calificacion']?>"><?php if($calificacion['prioritaria']) echo $controles->onOff($calificacion['prioritaria'])?></td>
						</tr>
					<?php endforeach;?>
				</table>
			<?php endif;?>
	
	</div>
	<?php $socio['SocioHistorico'] = null?>
	<?php if(!empty($socio['SocioHistorico'])):?>	
		<div class="areaDatoForm">	
		<h3>HISTORIAL DE MODIFICACIONES DEL SOCIO</h3>
					<table>
						<tr>
							<th>#</th>
							<th>FECHA NOVEDAD</th>
							<th>ESTADO</th>
							<th>FECHA ALTA</th>
							<th>FECHA BAJA</th>
							<th>MOTIVO BAJA</th>
							<th>USUARIO</th>
						</tr>
						<?php foreach($socio['SocioHistorico'] as $historia):?>
							<tr>
								<td><?php echo $historia['id']?></td>
								<td><?php echo $historia['created']?></td>
								<td><?php echo ($historia['activo'] == 1 ? '<span style="color:green;">VIGENTE</span>' : '<span style="color:red;">NO VIGENTE</span>')?></td>
								<td><?php echo $util->armaFecha($historia['fecha_alta'])?></td>
								<td><?php echo $util->armaFecha($historia['fecha_baja'])?></td>
								<td><?php if(!empty($historia['codigo_baja'])) echo $util->globalDato($historia['codigo_baja'])?></td>
								<td><?php echo $historia['user_created']?></td>
							</tr>
						<?php endforeach;?>
					</table>
	
		</div>
	<?php endif;?>
<?php endif;?>
<?php // DEBUG($socio)?>