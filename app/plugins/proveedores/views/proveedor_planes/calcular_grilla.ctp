<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PLANES :: GRILLAS :: CALCULAR NUEVA GRILLA</h3>

<div class="areaDatoForm3">
    

    <?php echo $frm->create(null,array('action' => 'calcular_grilla/' . $plan['ProveedorPlan']['id']))?>

    <?php echo $this->renderElement('proveedor_planes/info_plan',array('plan' => $plan))?>
  
    <?php echo $frm->hidden('ProveedorPlanGrilla.metodo_calculo',array('value' => $plan['ProveedorPlan']['metodo_calculo'])); ?>
    <?php echo $frm->hidden('ProveedorPlanGrilla.tna',array('value' => $plan['ProveedorPlan']['tna'])); ?>
    <?php echo $frm->hidden('ProveedorPlanGrilla.iva_porc',array('value' => $plan['ProveedorPlan']['iva'])); ?>
    <?php echo $frm->hidden('ProveedorPlanGrilla.tipo_cuota_gasto_admin',array('value' => $plan['ProveedorPlan']['tipo_cuota_gasto_admin'])); ?>
    <?php echo $frm->hidden('ProveedorPlanGrilla.gasto_admin_porc',array('value' => $plan['ProveedorPlan']['gasto_admin'])); ?>            
    <?php echo $frm->hidden('ProveedorPlanGrilla.gasto_admin_base_calculo',array('value' => $plan['ProveedorPlan']['gasto_admin_base_calculo'])); ?>            
    <?php echo $frm->hidden('ProveedorPlanGrilla.tipo_cuota_sellado',array('value' => $plan['ProveedorPlan']['tipo_cuota_sellado'])); ?>   
    <?php echo $frm->hidden('ProveedorPlanGrilla.sellado_porc',array('value' => $plan['ProveedorPlan']['sellado'])); ?>   
    <?php echo $frm->hidden('ProveedorPlanGrilla.sellado_base_calculo',array('value' => $plan['ProveedorPlan']['sellado_base_calculo'])); ?>            
    


 
<table class="tbl_form">
	<tr>
            <td><?php echo $frm->input('ProveedorPlanGrilla.descripcion',array('label' => 'DESCRIPCION','size'=>60,'maxlength'=>100)); ?></td>
	</tr>
	<tr>
            <td><?php echo $frm->calendar('ProveedorPlanGrilla.vigencia_desde','VIGENCIA DESDE',null,null,date('Y') + 1)?></td>
	</tr>
    </table>
    <table class="tbl_form">
        <tr>
            <td><?php echo $frm->money('ProveedorPlanGrilla.capital_minimo','MINIMO SOLICITADO',$this->data['ProveedorPlanGrilla']['capital_minimo']); ?></td>
            <td><?php echo $frm->money('ProveedorPlanGrilla.capital_maximo','MAXIMO SOLICITADO',$this->data['ProveedorPlanGrilla']['capital_maximo']); ?></td>
            <td><?php echo $frm->money('ProveedorPlanGrilla.capital_incremento','INCREMENTO',$this->data['ProveedorPlanGrilla']['capital_incremento']); ?></td>
            <td><?php echo $frm->input('ProveedorPlanGrilla.cuotas_disponibles',array('label' => 'OPCIONES DE CUOTAS(6,9,12,18...)','size'=>20,'maxlength'=>100,'value' => $this->data['ProveedorPlanGrilla']['cuotas_disponibles'])); ?></td>
	</tr>
    </table>       

    <hr/>
<?php echo $frm->hidden('ProveedorPlan.UID',array('value' => $UID)); ?>
<?php echo $frm->hidden('ProveedorPlanGrilla.PREVIEW',array('value' => 1)); ?>
<?php echo $frm->hidden('ProveedorPlanGrilla.proveedor_plan_id',array('value' => $plan['ProveedorPlan']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'PREVISUALIZAR','URL' => ( empty($fwrd) ? "/proveedores/proveedor_planes/grillas/".$plan['ProveedorPlan']['id'] : $fwrd) ))?>
    
</div>
<?php // debug($plan)?>
<?php if(!empty($opciones)):?>

<div class="areaDatoForm2">
    <h3>DETALLE DEL PLAN : Vista Previa</h3>
    <hr/>
    <div class="areaDatoForm2">
        <h2>Parametros de Cálulo</h2>
        <table style="margin-bottom: 10px;">
            <tr>
                <td style="text-align: left;">VIGENCIA</td><td colspan="8"><strong><?php echo $util->armaFecha($this->data['ProveedorPlanGrilla']['vigencia_desde'])?></strong></td>
                
            </tr>
            <tr>
            <td style="text-align: left;">DESCRIPCION</td><td colspan="8"><strong><?php echo $this->data['ProveedorPlanGrilla']['descripcion'] ?></strong></td>
            </tr>
            <tr>
                <td style="text-align: left;">METODO</td><td colspan="8"><strong><?php echo $liquidacion->metodoCalculoFormula?></strong></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: left;">T.N.A.</td><td style="text-align: right;"><strong><?php echo number_format($liquidacion->tna,2)?>%</strong></td>
                <td style="text-align: left;">T.E.A.</td><td style="text-align: right;"><strong><?php echo number_format($liquidacion->tea,2)?>%</strong></td>
                <td style="text-align: left;">T.E.M.</td><td style="text-align: right;"><strong><?php echo number_format($liquidacion->tem,2)?>%</strong></td>
                <!-- <td style="text-align: left;">GASTO ADM.</td><td style="text-align: right;"><strong><?php //echo number_format($GTO,2)?>%</strong></td> -->
                <!-- <td style="text-align: left;">SELLADO</td><td style="text-align: right;"><strong><?php //echo number_format($SELL,2)?>%</strong></td> -->
                <td style="text-align: left;">IVA</td><td style="text-align: right;"><strong><?php echo number_format($liquidacion->ivaAlicuota,2)?>%</strong></td>
            </tr>
            <?php if(!empty($liquidacion->liquidacion->gastoAdminstrativo->porcentaje)):?>
            <tr>
                <td></td>
                <td style="text-align: left;"><strong><?php echo number_format($liquidacion->liquidacion->gastoAdminstrativo->porcentaje,2)?>%</strong></td>
                <td><td colspan="7"><?php echo $liquidacion->liquidacion->gastoAdminstrativo->descripcion?><br>Base de Calculo = <?php echo $liquidacion->liquidacion->gastoAdminstrativo->baseCalculoCriterio?></td></td>
            </tr>
            <?php endif;?>
            <?php if(!empty($liquidacion->liquidacion->sellado->porcentaje)):?>
            <tr>
                <td></td>
                <td style="text-align: left;"><strong><?php echo number_format($liquidacion->liquidacion->sellado->porcentaje,2)?>%</strong></td>
                <td><td colspan="7"><?php echo $liquidacion->liquidacion->sellado->descripcion?><br>Base de Calculo = <?php echo $liquidacion->liquidacion->sellado->baseCalculoCriterio?></td></td>
            </tr> 
            <?php endif;?>           
        </table>

        <table>

        <?php  //debug($liquidacion);?>
        
    </div>
    
     <?php  if(isset($liquidacion) && is_object($liquidacion) && !empty($liquidacion)):?>
    <table>
        <tr>
            <th>MONTO PERCIBIDO</th><th colspan="<?php echo count($cuotas)?>">CUOTAS [VALORES PROMEDIO]</th>
        </tr>
        
        <?php foreach($montos as $monto):?>
        <tr>
        
            <th style="border-bottom: 1px solid;"><?php echo number_format($monto,2)?></th>
            <?php foreach($opciones[$monto] as $n => $calculo):?>

                <?php // debug($calculo);?>

                <?php 
                    $liquidacion = $calculo['liquidacion']; 
                    
                        $calculo = $calculo['cuotaPromedio']; 
                ?>

            <td style="border-bottom: 1px solid lightgray;">
                <!--<div class="areaDatoForm3" style="margin: 5px;">-->
                    <table class="tbl_form">
                        <tr>
                            <th colspan="2"><?php echo $n?></th>
                        </tr>
						<?php $totalGastos = $liquidacion['gastoAdmin'] + $liquidacion['sellados'];?>
                        <?php if(!empty($totalGastos)):?>
                        
                        <tr><td style="font-size: 90%;">GASTOS</td><td style="text-align: right;font-size: 90%;font-style: italic;"><?php echo number_format($totalGastos,2)?></td></tr>            
                        <?php endif;?> 
                        <tr><td style="font-size: 90%;font-weight: bold;border-bottom: 1px solid;background-color: #CDEB8B;">SOLICITADO</td><td style="text-align: right;font-size: 90%;font-weight: bold;;border-bottom: 1px solid;background-color: #CDEB8B;"><?php echo number_format($liquidacion['capitalSolicitado'],2)?></td></tr>

                        <tr><td style="font-weight: bold;" colspan="2">Detalle de Cuota</td></tr>

                        <tr><td style="font-size: 90%;font-style: italic;">CAPITAL</td><td style="text-align: right;font-size: 90%;font-style: italic;"><?php echo number_format($calculo['CAPITAL'],2)?></td></tr>
                        <?php if($calculo['INTERES'] != 0):?>
                        <tr><td style="font-size: 90%;font-style: italic;">INTERES</td><td style="text-align: right;font-size: 90%;font-style: italic;"><?php echo number_format($calculo['INTERES'],2)?></td></tr>
                        <?php endif;?>
                        <?php if($calculo['IVA'] != 0):?>
                        <tr><td style="font-size: 90%;font-style: italic;">IVA</td><td style="text-align: right;font-size: 90%;font-style: italic;"><?php echo number_format($calculo['IVA'],2)?></td></tr>
                        <?php endif;?>
                        <tr><td style="font-size: 90%;font-weight: bold;border-top: 1px solid;background-color: #FFFF88;">CUOTA</td><td style="text-align: right;font-size: 90%;font-weight: bold;border-top: 1px solid;background-color: #FFFF88;"><?php echo number_format($calculo['IMPORTE'],2)?></td></tr>
                        <tr><td style="font-size: 90%;">CFT</td><td style="text-align: right;font-size: 90%;"><?php echo $calculo['CFT']?>%</td></tr>



                        <!--<tr><td style="font-size: 90%;">CFT Anual</td><td style="text-align: right;font-size: 90%;"><?php // echo $calculo['CFTA']?>%</td></tr>-->
                    </table>

                <!--</div>-->
            </td>    
            <?php endforeach;?>
                
        </tr>
        <?php endforeach;?>
    </table>
    
	 <?php  endif;?>



</div>
	<hr/>
	<script type="text/javascript">
	function confirmForm(){
		var confirma = confirm("DAR DE ALTA GRILLA DE CUOTAS?");
		if(confirma){
			$('btnSubmitFormCargarGrillaDB').disable();
			$('formCargarGrillaDB').submit();
		}	
	}
	</script>
	<?php echo $frm->create(null,array('id' => 'formCargarGrillaDB','action' => 'calcular_grilla/' . $plan['ProveedorPlan']['id'], 'onsubmit' => 'confirmForm()'))?>
	<?php echo $frm->hidden('Proveedor.id',array('value' => $proveedor['Proveedor']['id']))?>
	<?php echo $frm->hidden('Proveedor.UID',array('value' => $UID))?>
        <?php echo $frm->hidden('ProveedorPlanGrilla.vigencia_desde',array('value' => $this->data['ProveedorPlanGrilla']['vigencia_desde']))?>
    <?php if(empty($this->data['ProveedorPlanGrilla']['cuotas']['error'])):?>
	<div class="submit"><input type="submit" value="GUARDAR GRILLA" id="btnSubmitFormCargarGrillaDB" /></div>
    <?php endif;?>
	<?php echo $frm->end()?>



<?php endif; ?>

