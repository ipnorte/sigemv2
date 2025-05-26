<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: DETALLE DEL TURNO '))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3><?php echo $descripcion_turno?></h3>
<?php 
//debug($socios);

//$socios = NULL;
?>
<?php if(!empty($socios)):?>

	<script type="text/javascript">
	
		function chkOnclick(idr,oChkCheck){
                    if(oChkCheck.checked){
                        $(idr).removeClassName("activo_0");
                        $(idr).addClassName("activo_1");
                    }else{
                        $(idr).removeClassName("activo_1");
                        $(idr).addClassName("activo_0");
                    }    
		}
	</script>

	<?php echo $frm->create(null,array('action'=>'detalle_turno_diskette/'.$liquidacion['Liquidacion']['id'].'/'.$turno,'id' => 'formGenDiskette'))?>
	<div class="areaDatoForm">Seleccione los registros que <strong>NO se enviar&aacute;n</strong> en el diskette</div>
	<table>
            <tr>
                <td style="text-align: left;">
                            <input type="button" onclick="javascript:window.location='<?php echo $this->base?>/mutual/liquidaciones/detalle_turno_diskette3/<?php echo $liquidacion['Liquidacion']['id'].'/'.$turno.'/SOCIO/NOENVIAR_ALL'?>'" value="QUITAR TODOS"/>
                            <input type="button" onclick="javascript:window.location='<?php echo $this->base?>/mutual/liquidaciones/detalle_turno_diskette3/<?php echo $liquidacion['Liquidacion']['id'].'/'.$turno.'/SOCIO/ENVIAR_ALL'?>'" value="ENVIAR TODOS"/>
                    
                </td>
                <td colspan="5"></td>
                <td colspan="3" style="text-align: right;"><input type="submit" value="GUARDAR"></td>
            </tr>
		<tr>
                    <th>SOCIO</th>
			<th>#</th>
			
			<th>CALIFICACION</th>
			<th>REG</th>
			<th>CBU</th>
			<th>SUCURSAL - CUENTA</th>
			<th>IMPORTE</th>
			<th></th>
			<th></th>
		</tr>
		
		<?php $i=0;?>
		<?php $reg = 0?>
		<?php $ACU_IMPORTE = 0?>
		
		<?php foreach($socios as $socio):?>
                
                    <?php $reg++?>
                    <?php $i++?>
                    <?php $apenom = $socio['LiquidacionSocioNoimputada']['documento'] ." - <strong>" .$socio['LiquidacionSocioNoimputada']['apenom']."</strong>";?>		
                    <?php $ACU_IMPORTE += $socio['LiquidacionSocioNoimputada']['importe_adebitar']?>


                <tr id="TRL_<?php echo $i?>" class="<?php echo ($socio['LiquidacionSocioNoimputada']['diskette'] == 1 ? "activo_1" : "activo_0")?>">
                        
                        <td nowrap="nowrap"><?php echo $this->renderElement('socios/link_to_estado_cuenta',array('plugin' => 'pfyj', 'texto' =>  $apenom,'socio_id' => $socio['LiquidacionSocioNoimputada']['socio_id']))?></td>
                        <td align="center"><?php echo $reg?></td>
                        <td align="center"><?php echo $socio['Calificacion']['concepto_1']?></td>
                        <td align="center"><?php echo $socio['LiquidacionSocioNoimputada']['registro']?></td>
                        <td align="center"><?php echo $socio['LiquidacionSocioNoimputada']['cbu']?></td>
                        <td align="center"><?php echo $socio['LiquidacionSocioNoimputada']['sucursal']?> - <?php echo $socio['LiquidacionSocioNoimputada']['nro_cta_bco']?></td>
                        <td align="right"><?php echo $util->nf($socio['LiquidacionSocioNoimputada']['importe_adebitar'])?></td>
                        <td align="right"><?php echo $controles->onOff($socio['LiquidacionSocioNoimputada']['diskette'])?></td>
                        <td align="center"><input type="checkbox" name="data[LiquidacionSocio][noenvia_diskette][<?php echo $socio['LiquidacionSocioNoimputada']['id']?>]" value="<?php echo $socio['LiquidacionSocioNoimputada']['id']?>" id="chk_<?php echo $i?>" onclick="chkOnclick('TRL_<?php echo $i?>',this)"/></td>
                        
                    </tr>
		<?php // debug($socio);?>
		
		<?php endforeach;?>
		<tr class="totales">
                    <th style="text-align: left;">
                            <!--<input type="button" onclick="checkUncheck(true)" value="QUITAR TODOS"/>-->
                            <input type="button" onclick="javascript:window.location='<?php echo $this->base?>/mutual/liquidaciones/detalle_turno_diskette3/<?php echo $liquidacion['Liquidacion']['id'].'/'.$turno.'/SOCIO/NOENVIAR_ALL'?>'" value="QUITAR TODOS"/>
                            <input type="button" onclick="javascript:window.location='<?php echo $this->base?>/mutual/liquidaciones/detalle_turno_diskette3/<?php echo $liquidacion['Liquidacion']['id'].'/'.$turno.'/SOCIO/ENVIAR_ALL'?>'" value="ENVIAR TODOS"/>
			</th>
			<th align="right" colspan="5">TOTAL GENERAL (<?php echo $reg?> REGISTROS)</th>
			<th align="right"><?php echo $util->nf($ACU_IMPORTE)?></th>
                        <th colspan="2"></th>
		</tr>		
		
	</table>		
	<?php echo $frm->hidden('LiquidacionSocioNoimputada.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->hidden('LiquidacionSocioNoimputada.turno_pago',array('value' => $turno))?>
        <?php echo $frm->hidden('LiquidacionSocioNoimputada.action')?>
	
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/mutual/liquidaciones/exportar3/".$liquidacion['Liquidacion']['id'] : $fwrd) ))?>
	
	
	<?php echo $frm->end()?>


<?php endif;?>