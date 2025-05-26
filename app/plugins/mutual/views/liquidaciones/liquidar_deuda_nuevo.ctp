<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA II'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<script type="text/javascript">
function confirmarForm() {
    var msgConfirm = "PROCESO DE LIQUIDACION DE DEUDA\n\n";
    msgConfirm = msgConfirm + "Período: *** " + getTextoSelect('LiquidacionPeriodoIniMonth') + '/' + getTextoSelect('LiquidacionPeriodoIniYear') + " ***\n";
    // msgConfirm = msgConfirm + "\n";
    msgConfirm = msgConfirm + "Organismo: " + getTextoSelect('LiquidacionCodigoOrganismo') + "\n";
    msgConfirm = msgConfirm + "Tipo: " + getTextoSelect('LiquidacionTipoDeudaLiquida') + "\n";
    if(document.getElementById("liquidacionPreImputacion").checked){
        msgConfirm = msgConfirm + "\n";
        msgConfirm = msgConfirm + "ATENCION!: *** SOBRE PREIMPUTACION ***" + "\n";
    }    
    msgConfirm = msgConfirm + "\n\n";
    msgConfirm = msgConfirm + "Continuar?";
    return confirm(msgConfirm);
}
</script>
<div class="areaDatoForm">
	<h3>PROCESO DE LIQUIDACION DE DEUDA II</h3>
	<?php echo $frm->create(null,array('action' => 'proceso_nuevo', 'id' => 'FrmLiqCsoc', 'onsubmit' => "return confirmarForm()"))?>
	
	<table class="tbl_form">

		<tr>
			<td>PERIODO A LIQUIDAR</td>
			<td><?php echo $frm->periodo('Liquidacion.periodo_ini','',$periodo,date('Y') - 10,date('Y') + 1)?></td>
		</tr>

		<tr>
			<td>ORGANISMO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'Liquidacion.codigo_organismo',
																			'prefijo' => 'MUTUCORG',
																			'disabled' => false,
																			'empty' => false,
																			'metodo' => "get_organismos",
			))?>			
			</td>
			<tr>
			<td>PRE-IMPUTACION</td><td><input type="checkbox" name="data[Liquidacion][pre_imputacion]" id="liquidacionPreImputacion" value="1"/></td>
		</tr>			
                <tr>
                    <td>TIPO LIQUIDACION</td>
                    <td>
                        <?php echo $frm->input('Liquidacion.tipo_deuda_liquida',array('type' => 'select','empty' => FALSE, 'selected' => 0,'options' => $tiposLiquidacion))?>
                    </td>
                </tr>
		<tr>
			<td colspan="2"><?php echo $frm->submit("GENERAR PROCESO")?></td>
		</tr>		
	</table>
	
	<?php echo $frm->end()?>	
</div>