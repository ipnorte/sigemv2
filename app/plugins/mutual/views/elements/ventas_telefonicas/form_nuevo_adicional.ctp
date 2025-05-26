		<h3>ALTA NUEVO ADICIONAL</h3>
		
		<script type="text/javascript">

			Event.observe(window, 'load', function(){
				document.getElementById("MutualServicioSolicitudAdicionalFechaAltaDay").value = "<?php echo date("d",strtotime($fechaCobertura))?>";
				document.getElementById("MutualServicioSolicitudAdicionalFechaAltaMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
				document.getElementById("MutualServicioSolicitudAdicionalFechaAltaYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

				$('MutualServicioSolicitudAdicionalFechaAltaDay').disable();
				$('MutualServicioSolicitudAdicionalFechaAltaMonth').disable();
				$('MutualServicioSolicitudAdicionalFechaAltaYear').disable();

				<?php if(date("Ym",strtotime($fechaCobertura)) > $solicitud['MutualServicioSolicitud']['periodo_desde']):?>
				
				document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
				document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

				<?php else:?>
				document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeMonth").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],4,2)?>";
				document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeYear").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],0,4)?>";
				<?php endif;?>
				
				$('MutualServicioSolicitudAdicionalPeriodoDesdeMonth').disable();
				$('MutualServicioSolicitudAdicionalPeriodoDesdeYear').disable();

			});

		
			function validateFormAdicional(){
				
				if(!validRequired('SocioAdicionalDocumento','')) return false;
				if(!validRequired('SocioAdicionalApellido','')) return false;
				if(!validRequired('SocioAdicionalNombre','')) return false;
				document.getElementById('SocioAdicionalDocumento').value = rellenar($('SocioAdicionalDocumento').getValue(),'0',8,'L');
				if(!validRequired('SocioAdicionalCalle','')) return false;
				if(!validRequired('SocioAdicionalNumeroCalle','')) return false;
				if(!validRequired('SocioAdicionalLocalidadAproxima','')) return false;
				if(!validRequired('SocioAdicionalCodigoPostal','')) return false;

				//MENSAJE DE CONFIRMACION
				var c = $("MutualServicioSolicitudCount").getValue();
				var msg = "**** ALTA NUEVO ADICIONAL ****\n\n";
				msg = msg + getTextoSelect('SocioAdicionalTipoDocumento') + " " + $('SocioAdicionalDocumento').getValue() + " | ";
				msg = msg + $('SocioAdicionalApellido').getValue() + ", " + $('SocioAdicionalNombre').getValue() + "\n";
				var msgServ = "";
				if(c != 0){
					for (i=0; i < c; i++){
						oChkCheck = document.getElementById('chk_' + i);
						otxt = document.getElementById('servicio_' + i);
						if(oChkCheck.checked){
							msgServ = msgServ + otxt.value + "\n"; 
						}
					}
				}
				if(msgServ != ""){
					msg = msg + "\n--- INCORPORARLO A LOS SIGUIENTES SERVICIOS: ---\n\n" + msgServ + "\n";
					msg = msg + "FECHA ALTA SERVICIO: " + getStrFecha("MutualServicioSolicitudAdicionalFechaAlta") + "\n";
					msg = msg + "LIQUIDAR A PARTIR DE: " + getStrPeriodo("MutualServicioSolicitudAdicionalPeriodoDesde") + "\n";
				}
				
				return confirm(msg);
			}
		</script>		
		<?php echo $form->create(null,array('action' => "index/".$persona['Persona']['id'].'/2','name'=>'formAddAdicional','id'=>'formAddAdicional','onsubmit' => "return validateFormAdicional()" ));?>
		<div class="areaDatoForm">
			<h4>DATOS PERSONALES</h4>
			<hr/>
			<table class='tbl_form'>
				<tr>
					<td colspan="4"><?php echo $this->requestAction('/config/global_datos/combo/TIPO DOCUMENTO/SocioAdicional.tipo_documento/PERSTPDC/0/0/');?></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $frm->number('SocioAdicional.documento',array('label'=>'NRO DOCUMENTO','size'=>'15','maxlength'=>'11'));?></td>
				</tr>				
				<tr>
					<td colspan="1"><?php echo $frm->input('SocioAdicional.apellido',array('label'=>'APELLIDO','size'=>40,'maxlength'=>100)); ?></td>
					<td colspan="3"><?php echo $frm->input('SocioAdicional.nombre',array('label'=>'NOMBRE','size'=>40,'maxlength'=>100)); ?></td>
				</tr>
				<tr>
					<td><?php echo $frm->input('SocioAdicional.fecha_nacimiento',array('dateFormat' => 'DMY','label'=>'FECHA DE NACIMIENTO','minYear'=>'1900', 'maxYear' => date("Y")))?></td>					
					<td><?php echo $form->input('SocioAdicional.sexo',array('type'=>'select','options'=>array('M' =>'MASCULINO', 'F'=>'FEMENINO'),'empty'=>false,'label'=>'SEXO'));?></td>
					<td colspan="2"><?php echo $this->requestAction('/config/global_datos/combo/VINCULO/SocioAdicional.vinculo/PERSVINC/0/0/');?></td>
				</tr>
				<tr>
					<td><?php echo $frm->input('SocioAdicional.calle',array('label'=>'CALLE','size'=>40,'maxlength'=>100,'value' => $persona['Persona']['calle'])); ?></td>
					<td><?php echo $frm->number('SocioAdicional.numero_calle',array('label'=>'NUMERO','value' => $persona['Persona']['numero_calle'])); ?></td>
					<td><?php echo $frm->input('SocioAdicional.piso',array('label'=>'PISO','size'=>3,'maxlength'=>3,'value' => $persona['Persona']['piso'])); ?></td>
					<td><?php echo $frm->input('SocioAdicional.dpto',array('label'=>'DPTO','size'=>3,'maxlength'=>3,'value' => $persona['Persona']['dpto'])); ?></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $frm->input('SocioAdicional.barrio',array('label'=>'BARRIO','size'=>30,'maxlength'=>100,'value' => $persona['Persona']['barrio'])); ?></td>
				</tr>
			</table>
			
			<div class='row'>
				<?php echo $this->requestAction('/config/localidades/form/SocioAdicional/'.$persona['Persona']['localidad_id'].'/'.$persona['Persona']['localidad'].'/'.$persona['Persona']['codigo_postal'].'/'.$persona['Persona']['provincia_id']); ?>
			</div>
			<?php $solicitudes = $this->requestAction('mutual/mutual_servicio_solicitudes/get_solicitudes/' . $persona_id);?>
			<?php if(!empty($solicitudes)):?>
				<br/>
				<h4>INCORPORARLO A LOS SIGUIENTES SERVICIOS</h4>
				<hr/>
					<table>
						<tr>
							<th></th>
							<th>OSERV #</th>
							<th></th>
							<th>COBERTURA DESDE</th>
							<th>SERVICIO</th>
							<th>BENEFICIO</th>
						</tr>
						<?php $i=0;?>
						<?php foreach($solicitudes as $solicitud):?>
							<?php if($solicitud['MutualServicioSolicitud']['estado_actual_min'] != 'B' && empty($solicitud['MutualServicioSolicitud']['cuotas'])):?>
							<tr class="<?php echo (!empty($solicitud['MutualServicioSolicitud']['periodo_hasta']) ? "activo_0" : ($solicitud['MutualServicioSolicitud']['aprobada'] == 1 ? "alt" : "amarillo"))?>">
								<td>
									<input type="checkbox" id="chk_<?php echo $i?>" name="data[MutualServicioSolicitud][mutual_servicio_id][<?php echo $solicitud['MutualServicioSolicitud']['id']?>]" value="<?php echo $solicitud['MutualServicioSolicitud']['persona_beneficio_id']?>"/>
									<input type="hidden" id="servicio_<?php echo $i?>" value="<?php echo $solicitud['MutualServicioSolicitud']['tipo_numero'] . " | " . $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio_ref']?>"/>
								</td>
								<td>#<?php echo $solicitud['MutualServicioSolicitud']['id']?></td>
								<td><?php echo $solicitud['MutualServicioSolicitud']['estado_actual_min']?></td>
								<td align="center">
									<span style="color:green;"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio'])?></span>
									<?php if(!empty($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])):?>
									|
									<span style="color:red;"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])?></span>
									<?php endif;?>
								</td>
								<td><?php echo $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio_ref']?></td>
								<td><?php echo $solicitud['MutualServicioSolicitud']['beneficio']?></td>
							</tr>
							<?php $i++;?>
							<?php endif;?>
						<?php endforeach;?>
					</table>
					<br/>
					<div class="row">
					<table class="tbl_form">
						<tr>
							<td>FECHA COBERTURA DESDE</td>
							<td><?php echo $frm->input('MutualServicioSolicitudAdicional.fecha_alta',array('dateFormat' => 'DMY','label'=>'','minYear' => date('Y'), 'maxYear' => date("Y") + 1))?></td>
						</tr>		
						<tr>
							<td>LIQUIDAR A PARTIR DE</td>
							<td><?php echo $frm->periodo('MutualServicioSolicitudAdicional.periodo_desde',null,date('Y')."-".date('m')."-01",date('Y'),date('Y')+1)?></td>
						</tr>
					</table>
					</div>
				<?php //   debug($solicitudes)?>			
			<?php endif;?>
			
						
		</div>
		<?php echo $frm->hidden('MutualServicioSolicitudAdicional.fecha_alta',array('value' => $fechaCobertura)); ?>
		<?php echo $frm->hidden('MutualServicioSolicitudAdicional.periodo_desde',array('value' => date("Ym",strtotime($fechaCobertura))));?>
		<?php echo $frm->hidden('MutualServicioSolicitud.count',array('value' => $i)); ?>
		<?php echo $frm->hidden('SocioAdicional.persona_id',array('value' => $persona['Persona']['id'])); ?>
		<?php echo $frm->hidden('SocioAdicional.socio_id',array('value' => (!empty($persona['Socio']) ? $persona['Socio']['id'] : 0))); ?>
		<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/ventastelefonicas/index/'.$persona['Persona']['id']))?>