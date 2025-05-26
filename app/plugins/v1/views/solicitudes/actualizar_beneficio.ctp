<h2>ACTUALIZAR DATOS DEL BENEFICIO</h2>

<?php echo $form->create(null,array('onsubmit' => "return validateFormBeneficio()",'action' => 'actualizar_beneficio/'. $this->data['PersonaBeneficio']['idr'].'/'.$nro_solicitud));?>


	<table>
	
		<tr>
			<td colspan="4"><?php echo $this->requestAction('/config/global_datos/combo/ORGANISMO/PersonaBeneficio.codigo_beneficio/MUTUCORG/0/0/'.$this->data['PersonaBeneficio']['codigo_beneficio'])?></td>
		</tr>
		<tr>
			<td><?php echo $frm->input('PersonaBeneficio.tipo',array('label'=>'TIPO','size'=>1,'maxlenght'=>1)); ?></td>
			<td><?php echo $frm->input('PersonaBeneficio.nro_ley',array('label'=>'LEY','size'=>2,'maxlenght'=>2)); ?></td>
			<td><?php echo $frm->input('PersonaBeneficio.nro_beneficio',array('label'=>'BENEFICIO','size'=>20,'maxlenght'=>50)); ?></td>
			<td><?php echo $frm->input('PersonaBeneficio.sub_beneficio',array('label'=>'SUB-BENEFICIO','size'=>2,'maxlenght'=>2)); ?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $this->requestAction('/config/global_datos/combo/EMPRESA/PersonaBeneficio.codigo_empresa/MUTUEMPR/0/0/'.$this->data['PersonaBeneficio']['codigo_empresa'])?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $frm->input('PersonaBeneficio.codigo_reparticion',array('label'=>'COD.REPARTICION','size'=>11,'maxlenght'=>11)); ?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $frm->input('PersonaBeneficio.nro_legajo',array('label'=>'NRO.LEGAJO','size'=>11,'maxlenght'=>11)); ?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $frm->input('PersonaBeneficio.fecha_ingreso',array('dateFormat' => 'DMY','label'=>'FECHA DE INGRESO','minYear'=>'1900', 'maxYear' => date("Y") - 1))?></td>
			
		</tr>
		<tr>
			<td colspan="4">
				<?php echo $frm->number('PersonaBeneficio.cbu',array('label'=>'CBU','size'=>23,'maxlength'=>23)); ?>
			</td>
		</tr>
		<?php if(!empty($this->data['PersonaBeneficio']['cbu'])):?>	
			<tr>
				<td colspan="4">
					<?php echo $this->requestAction('/config/bancos/info_cbu/'.$this->data['PersonaBeneficio']['cbu'].'/1')?>
				</td>
			</tr>
		<?php endif;?>		
		<tr>
			<td colspan="4">
				<?php echo $frm->input('PersonaBeneficio.activo',array('label' => 'ACTIVO'))?>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<?php echo $frm->submit("GUARDAR CAMBIOS")?>
			</td>
		</tr>															
	</table>
	
	
<?php echo $frm->hidden('PersonaBeneficio.id'); ?>
<?php echo $frm->hidden('PersonaBeneficio.idr'); ?>	
<?php echo $frm->hidden('PersonaBeneficio.persona_id'); ?>	

<?php echo $frm->hidden('PersonaBeneficio.cbu_codigo_banco'); ?>
<?php echo $frm->hidden('PersonaBeneficio.cbu_sucursal'); ?>	
<?php echo $frm->hidden('PersonaBeneficio.cbu_tipo_cta_bco'); ?>
<?php echo $frm->hidden('PersonaBeneficio.cbu_nro_cta_bco'); ?>
	
</form>
<?php //   debug($this->data)?>