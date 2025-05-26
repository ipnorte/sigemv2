<div class="areaDatoForm2">
    PRODUCTO: <strong><?php echo $util->globalDato($plan['ProveedorPlan']['tipo_producto'])?></strong>
	&nbsp;
	PLAN: <strong>#<?php echo $plan['ProveedorPlan']['id']?> - <?php echo $plan['ProveedorPlan']['descripcion']?></strong>
	ESTADO: <strong><?php echo ($plan['ProveedorPlan']['activo'] == 1 ? "VIGENTE" : "NO VIGENTE")?></strong>
    <?php if(!empty($plan['ProveedorPlan']['metodo_calculo'])):?>
    <hr>     
    <table class="tbl_form">
        <tr>
            <td>

            <?php echo $frm->input('ProveedorPlanGrilla.metodo_calculo',array('label' => 'METODO DE CALCULO','type' => 'select', 'disabled' => 'disabled' ,'options' => $metodos,'value' => $plan['ProveedorPlan']['metodo_calculo']))?></td>
            <td>
                <?php //echo $frm->money('ProveedorPlanGrilla.tna','T.N.A. (TASA NOMINAL ANUAL - %)',$plan['ProveedorPlan']['tna'])?>
                <?php echo $frm->input('ProveedorPlanGrilla.tna',array('label'=>'T.N.A. (TASA NOMINAL ANUAL - %)','size'=> 6 ,'maxlength'=>4,'disabled' => 'disabled', 'class' =>'input_number', 'value' => $plan['ProveedorPlan']['tna'])); ?>
            </td>
            <td>
                <?php //echo $frm->money('ProveedorPlanGrilla.iva_porc','I.V.A. (%)',$plan['ProveedorPlan']['iva'])?>
                <?php echo $frm->input('ProveedorPlanGrilla.iva_porc',array('label'=>'I.V.A. (%)','size'=> 6 ,'maxlength'=>4,'disabled' => 'disabled', 'class' =>'input_number', 'value' => $plan['ProveedorPlan']['iva'])); ?>
            </td>
        </tr>
    </table>  
 
    <!-- <table class="tbl_form">        
	<tr>
            <td><?php //echo $frm->input('ProveedorPlanGrilla.cuotas_disponibles',array('label' => 'OPCIONES DE CUOTAS(6,9,12,18...)','size'=>60,'maxlength'=>100,'value' => $this->data['ProveedorPlanGrilla']['cuotas_disponibles'])); ?></td>
	</tr>
    </table> -->

    <table class="tbl_form"> 
        <tr>
            <td>
            <?php echo $this->renderElement('global_datos/combo_cuotas_puntuales',array(
																					'plugin'=>'config',
																					'label' => "GASTO ADM./OTORGAM.",
																					'model' => 'ProveedorPlanGrilla.tipo_cuota_gasto_admin',
																					'disabled' => true,
																					'empty' => false,
																					'selected' => $plan['ProveedorPlan']['tipo_cuota_gasto_admin'],
					))?>
            </td>
            <td>
                <?php //echo $frm->money('ProveedorPlanGrilla.gasto_admin_porc','%',$plan['ProveedorPlan']['gasto_admin'])?>
                <?php echo $frm->input('ProveedorPlanGrilla.gasto_admin_porc',array('label'=>'%','size'=> 6 ,'maxlength'=>4,'disabled' => 'disabled', 'class' =>'input_number', 'value' => $plan['ProveedorPlan']['gasto_admin'])); ?>
            </td>
            <td>
            <?php echo $frm->input('ProveedorPlanGrilla.gasto_admin_base_calculo',array('label' => 'BASE DE CALCULO','type' => 'select', 'options' => $criterios,'disabled' => 'disabled', 'value' => $plan['ProveedorPlan']['gasto_admin_base_calculo']))?>
            </td>

        </tr>
        <tr>
        <td>
            <?php echo $this->renderElement('global_datos/combo_cuotas_puntuales',array(
																					'plugin'=>'config',
																					'label' => "SELLADO CONTRATO/PAG.",
																					'model' => 'ProveedorPlanGrilla.tipo_cuota_sellado',
																					'disabled' => true,
																					'empty' => false,
																					'selected' => $plan['ProveedorPlan']['tipo_cuota_sellado'],
					))?>
            </td>        
            <td>
                <?php //echo $frm->money('ProveedorPlanGrilla.sellado_porc','%',$this->data['ProveedorPlanGrilla']['sellado_porc'])?>
                <?php echo $frm->input('ProveedorPlanGrilla.sellado_porc',array('label'=>'%','size'=> 6 ,'maxlength'=>4,'disabled' => 'disabled', 'class' =>'input_number', 'value' => $plan['ProveedorPlan']['sellado'])); ?>
            </td>
            <td>
            <?php echo $frm->input('ProveedorPlanGrilla.sellado_base_calculo',array('label' => 'BASE DE CALCULO','type' => 'select', 'disabled' => 'disabled' , 'options' => $criterios, 'value' => $plan['ProveedorPlan']['sellado_base_calculo']))?>
            </td>        
        </tr>       
    </table>
    <?php endif;?>
    </div>

    <?php // debug($plan)?>