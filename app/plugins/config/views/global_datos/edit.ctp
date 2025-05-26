<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DE DATOS GLOBALES :: MODIFICAR DATO'))?>

<?php if($nivel > 1):?>
<h3><?php echo $prefijo . ' :: ' . $this->requestAction('/config/global_datos/valor/'.$prefijo)?></h3>
<?php endif;?>

<?php echo $frm->create('GlobalDato',array('action' => 'edit/'.$this->data['GlobalDato']['id'].'/'.$nivel.'/'.$prefijo));?>

<div class="areaDatoForm">
			
			<table class='tbl_form'>
			
				<tr>
					<td>CODIGO</td><td><?php echo $frm->input('codigo',array('size'=>14,'maxlength'=>12,'disabled'=>'disabled','value'=>$this->data['GlobalDato']['id'])) ?></td>
				</tr>
				<tr>
					<td>CONCEPTO I</td><td><?php echo $frm->input('concepto_1',array('size'=>60,'maxlength'=>100)) ?></td>
				</tr>
				<tr>
					<td>CONCEPTO II</td><td><?php echo $frm->input('concepto_2',array('size'=>60,'maxlength'=>100)) ?></td>
				</tr>
				<tr>
					<td>CONCEPTO III</td><td><?php echo $frm->input('concepto_3',array('size'=>60,'maxlength'=>100)) ?></td>
				</tr>
				<tr>
					<td>CONCEPTO IV</td><td><?php echo $frm->input('concepto_4',array('size'=>60,'maxlength'=>100)) ?></td>
				</tr>	
				<tr>
					<td>CONCEPTO V</td><td><?php echo $frm->input('concepto_5',array('size'=>60,'maxlength'=>100)) ?></td>
				</tr>															
				<tr>
					<td>LOGICO I</td><td><?php echo $frm->input('logico_1') ?></td>
				</tr>
				<tr>
					<td>LOGICO II</td><td><?php echo $frm->input('logico_2') ?></td>
				</tr>
				<tr>
					<td>LOGICO III</td><td><?php echo $frm->number('logico_3') ?></td>
				</tr>                
				<tr>
					<td>ENTERO I</td><td><?php echo $frm->number('entero_1',array('value'=>$this->data['GlobalDato']['entero_1'],'size'=>10,'maxlength'=>10)) ?></td>
				</tr>
				<tr>
					<td>ENTERO II</td><td><?php echo $frm->number('entero_2',array('value'=>$this->data['GlobalDato']['entero_2'],'size'=>10,'maxlength'=>10)) ?></td>
				</tr>
				<tr>
					<td>ENTERO III</td><td><?php echo $frm->number('entero_3',array('size'=>10,'maxlength'=>10)) ?></td>
				</tr>
				<tr>
					<td>ENTERO IV</td><td><?php echo $frm->number('entero_4',array('size'=>10,'maxlength'=>10)) ?></td>
				</tr>
				<tr>
					<td>ENTERO V</td><td><?php echo $frm->number('entero_5',array('size'=>10,'maxlength'=>10)) ?></td>
				</tr>
				<tr>
					<td>ENTERO VI</td><td><?php echo $frm->number('entero_6',array('size'=>10,'maxlength'=>10)) ?></td>
				</tr>
				<tr>
					<td>ENTERO VII</td><td><?php echo $frm->number('entero_7',array('size'=>10,'maxlength'=>10)) ?></td>
				</tr>								

				<tr>
					<td>DECIMAL I</td><td><?php echo $frm->input('decimal_1',array('size'=>10,'maxlength'=>10,'class' =>'input_number','onkeypress' => "return soloNumeros(event,true,true)")) ?></td>
				</tr>
				<tr>
					<td>DECIMAL II</td><td><?php echo $frm->input('decimal_2',array('size'=>10,'maxlength'=>10,'class' =>'input_number','onkeypress' => "return soloNumeros(event,true,true)")) ?></td>
				</tr>												
								<tr>
					<td>FECHA I</td><td><?php echo $frm->calendar('GlobalDato.fecha_1',null,$this->data['GlobalDato']['fecha_1'],date('Y'),date('Y'),false)?></td>
				</tr>				
				<tr>
					<td>FECHA II</td><td><?php echo $frm->calendar('GlobalDato.fecha_2',null,$this->data['GlobalDato']['fecha_2'],date('Y'),date('Y'),false)?></td>
				</tr>
				<tr>
					<td>TEXTO I</td><td><textarea name="data[GlobalDato][texto_1]" cols="60" rows="10" id="GlobalDatoTexto1" ><?php echo $this->data['GlobalDato']['texto_1']?></textarea></td>
				</tr>				
				<tr>
					<td>TEXTO II</td><td><textarea name="data[GlobalDato][texto_2]" cols="60" rows="10" id="GlobalDatoTexto2" ><?php echo $this->data['GlobalDato']['texto_2']?></textarea></td>
				</tr>				
			</table>

 		 		 	 		 
</div>
<?php echo $frm->hidden('id',array('value'=>$this->data['GlobalDato']['id'])) ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/global_datos/index/'.$nivel.'/'.$prefijo))?>

<?php //   debug($this->data['GlobalDato'])?>