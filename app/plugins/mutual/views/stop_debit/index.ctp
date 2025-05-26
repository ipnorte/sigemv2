<?php echo $this->renderElement('head')?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3>ADMINISTRACION DE STOP DEBIT :: <?php echo $liquidacion['Liquidacion']['organismo']?> </h3>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'index/'. $liquidacion['Liquidacion']['id']))?>
    <table class="tbl_form">
        <tr>
            <td>BANCO</td>
            <td>
                <?php echo $frm->input('LiquidacionSocioRendicion.banco_intercambio',array('type' => 'select', 'options' => $bancoIntercambios))?>
            </td>
            <td><input type="submit" value="FILTRAR" <?php echo (empty($bancoIntercambios) ? ' disabled="" ' : '') ?> ></td>
        </tr>
    </table>
    <?php echo $frm->hidden('LiquidacionSocioRendicion.accion',array('value' => 'FILTRAR')); ?>
    <?php echo $frm->hidden('LiquidacionSocioRendicion.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id'])); ?>
    <?php echo $frm->end()?>
</div>
<?php // debug($bancoIntercambios)?>
<?php if(!empty($socios)):?>


<?php echo $frm->create(null,array('action' => 'index/'. $liquidacion['Liquidacion']['id'],'onsubmit' => "return confirm('Cambiar de Organismo?')"))?>

<script language="Javascript" type="text/javascript">
    var rows = <?php echo count($socios)?>;
    var checkAll = false;
    function checkUnCheckAll(rows){
        
        if(checkAll){checkAll = false;}
        else {checkAll = true;}
        
        for (i = 0; i < rows; i++){
            objCHK = document.getElementById('chk_' + i);
            if(!objCHK.disabled){
                if(checkAll){objCHK.checked = true;}
                else {objCHK.checked = false;}
            }
        }
        chkOnclick();
    }
    function chkOnclick(){
        for (i = 0; i < rows; i++){
            objCHK = document.getElementById('chk_' + i);
            toggleCell("tr_" + i,objCHK);
        }
    }    
</script>
<table>
    <tr>
        <th>SOCIO</th>
        <th>DOCUMENTO</th>
        <th>NOMBRE</th>
        <th>TOTAL LIQUIDADO</th>
        <th>MORA</th>
        <th>PERIODO</th>
        <th>ADICIONALES</th>
        <th>TOTAL STOP</th>
        <th>NUEVO ORGANISMO</th>
        <th><?php echo $controles->btnCallJS("checkUnCheckAll(".count($socios).")","","controles/12-em-check.png")?></th>
    </tr>
    <?php $i = 0;?>
    <?php foreach($socios as $socio):?>
    
    <tr id="tr_<?php echo $i?>">
        <td style="font-weight: bold;"><?php echo $controles->openWindow($socio['LiquidacionSocioRendicion']['socio_id'],'/mutual/liquidaciones/by_socio/'.$socio['LiquidacionSocioRendicion']['socio_id'].'/1')?></td>
        <td><?php echo $socio['Persona']['documento']?></td>
        <td><?php echo $socio['Persona']['apellido'].', '.$socio['Persona']['nombre']?></td>
        <td style="text-align: right;"><?php echo $util->nf($socio[0]['total_liquidado'])?></td>
        <td style="text-align: right;"><?php echo $util->nf($socio[0]['saldo_mora'])?></td>
        <td style="text-align: right;"><?php echo $util->nf($socio[0]['saldo_periodo'])?></td>
        <td style="text-align: right;"><?php echo $util->nf($socio[0]['cargos_adicionales'])?></td>
        <td style="text-align: right;"><?php echo $util->nf($socio[0]['importe_stop'])?></td>
        <td><?php echo $socio['Organismo']['concepto_1']?></td>
        <td>
            <input type="checkbox" <?php echo ( !empty($socio['LiquidacionSocioRendicionStop']['id']) ? 'disabled=""' : '')?> onclick="toggleCell('tr_<?php echo $i?>',this);" name="data[LiquidacionSocioRendicion][socios][<?php echo $socio['LiquidacionSocioRendicion']['socio_id']?>]" value="<?php echo $socio['LiquidacionSocioRendicion']['socio_id']?>" id="chk_<?php echo $i?>">
        </td>
    </tr>
    <?php $i++;?>
    
    <?php endforeach;?>

    
</table>
<h3>Acci&oacute;n a Ejecutar :: Reasignar a Nuevo Beneficio</h3>
<table class="tbl_form" style="margin-top: 5px;">
<!--    <tr>
        <td>A PARTIR DE <strong><?php // echo $util->periodo($liquidacion['Liquidacion']['periodo'])?></strong></td>
    </tr>-->
    <tr>
        <td>
            <?php echo $this->renderElement('global_datos/combo_global',array(
                'plugin'=>'config',
                'metodo' => "get_organismos",
                'model' => 'LiquidacionSocioRendicion.codigo_organismo',
                'empty' => false,
                'label' => 'ORGANISMO'
            ))?>            
            <?php
            echo $this->renderElement('global_datos/combo', array(
                'plugin' => 'config',
                'label' => 'MOTIVO DE BAJA DEL ACTUAL',
                'model' => 'LiquidacionSocioRendicion.codigo_baja',
                'prefijo' => 'MUTUBABE',
                'disable' => false,
                'empty' => false,
                'selected' => '0',
                'logico' => true,
            ))
            ?>				
            </td>
    </tr>
<!--    <tr>            <td style="color: red;font-weight: bold;">DAR DE BAJA CUOTAS</td>
				<td>
					<input type="radio" name="data[PersonaBeneficio][accion]" id="PersonaBeneficioAccion_b" value="B" onclick="$('PersonaBeneficioPersonaBeneficioId').disable();"/>
				</td>
			</tr>    
    <tr>            -->
        <td><input type="submit" value="PROCESAR" <?php echo (empty($bancoIntercambio) ? ' disabled="" ' : '') ?> ></td>
    </tr>    
</table> 


<?php echo $frm->hidden('LiquidacionSocioRendicion.accion',array('value' => 'PROCESAR')); ?>
<?php echo $frm->hidden('LiquidacionSocioRendicion.banco_intercambio',array('value' => $bancoIntercambio)); ?>
<?php echo $frm->hidden('LiquidacionSocioRendicion.periodo',array('value' => $liquidacion['Liquidacion']['periodo'])); ?>
<?php echo $frm->hidden('LiquidacionSocioRendicion.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id'])); ?>
<?php echo $frm->end()?>


<?php // debug($socios)?>

<?php endif; ?>
