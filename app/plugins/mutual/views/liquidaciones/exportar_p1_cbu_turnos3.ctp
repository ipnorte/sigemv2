<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS :: ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'],'concepto_1')))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if(!empty($turnos)):?>


	<script language="Javascript" type="text/javascript">

            var rows = <?php echo count($turnos)?>;

            Event.observe(window, 'load', function() {
                $('total_registros').update("0");
//                $('total_liquidado').update("0");
                $('total_enviado').update("0");
                $('btn_genDiskette').disable();
                <?php if($liquidacion['Liquidacion']['imputada'] == 1): ?>
                    $('formGenFile').disable();
                <?php endif;?>
                SelSum();
                set_parameters($('LiquidacionSocioNoimputadaBancoIntercambio').getValue());
                //LiquidacionSocioBancoIntercambio
                $('LiquidacionSocioNoimputadaBancoIntercambio').observe('change',function(){
                    set_parameters($('LiquidacionSocioBancoIntercambio').getValue());
                });
            
            });
        
        
        
            function set_parameters(banco){
                $('bcoNacionNroArchivo').hide();
                $('bcoCbaNroConvenio').hide();
                $('cuenca_fecha_maxima').hide();
                $('cuenca_ciclos').hide();
                switch(banco){
                    
                    case '00011':
                        $('bcoNacionNroArchivo').show();
                        break;
                    case '00285':
                        $('bcoNacionNroArchivo').show();
                        break; 
                    case '90285':
                        $('bcoNacionNroArchivo').show();
                        break;                        
                    case '00020':
                        $('bcoCbaNroConvenio').show();
                        break;
                    case '65203':
                        $('cuenca_fecha_maxima').show();
                        $('cuenca_ciclos').show();
                        break;
                    default:
                }
            }



            function unSellAll(){
                    for (i=1;i<=rows;i++){
                            oChkCheck = document.getElementById('chk_' + i);
                            oChkCheck.checked = true;
                    }
            }

            function SelSum(){
                    var totalRegistros = 0;
                    var totalLiquidado = 0;
                    var totalEnviado = 0;

                    for (i=1;i<=rows;i++){
                            oChkCheck = document.getElementById('chk_' + i);
                            valCheck = oChkCheck.value;
                            aValCheck = valCheck.split("|");
                            if (oChkCheck.checked){
                                    totalRegistros = totalRegistros + parseInt(aValCheck[0]);
                                    totalLiquidado = totalLiquidado + parseInt(aValCheck[1]);
                                    totalEnviado = totalEnviado + parseInt(aValCheck[2]);
                            }
                    }

                    if(totalRegistros !== 0)$('btn_genDiskette').enable();				
                    else $('btn_genDiskette').disable();				

                    totalLiquidado = FormatCurrency(totalLiquidado / 100);
                    totalEnviado = FormatCurrency(totalEnviado / 100);

                    $('total_registros').update(totalRegistros);
    //			$('total_liquidado').update(totalLiquidado);
                    $('total_enviado').update(totalEnviado);			

            }

            function chkOnclick(idr,oCHK){
                    toggleCell(idr,oCHK);
                     SelSum();
            }
			
	</script>
	<?php echo $frm->create(null,array('action'=>'exportar3/'.$liquidacion['Liquidacion']['id'],'id' => 'formGenFile'))?>
        <h3>DETALLE DE TURNOS</h3>
        
        <div class="actions">
            <?php echo $controles->botonGenerico('/mutual/liquidaciones/exportar3/'.$liquidacion['Liquidacion']['id'].'/1','controles/ms_excel.png','EXPORTAR PLANILLA',array('target' => 'blank'))?>
        </div>
	<table>
		<tr>
			<th>TURNO</th>
			<th>EMPRESA</th>
			<th>REGISTROS</th>
                        <th>CHECK</th>
                        <th></th>
			<!--<th>IMPORTE LIQUIDADO</th>-->
			<th>IMPORTE A DEBITAR</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
                        
		</tr>
		<?php $i=0;?>
		<?php foreach($turnos as $turno):?>
			<?php // debug($turno)?>
			<?php 
				$aTrunos = explode("-",$turno[0]['turno_descripcion']);
			?>
			<?php $i++;?>
			<?php $valCheck = $turno[0]['cantidad']."|".($turno[0]['importe_adebitar']*100)."|".($turno[0]['importe_adebitar']*100)."|".$turno[0]['turno_descripcion']?>
			<tr id="TRL_<?php echo $i?>" class="<?php echo ($turno[0]['error_turno'] === '1' ? "grilla_error" : "")?>">
				<td><?php echo substr(trim($turno[0]['turno']),-5,5)?></td>
				<td><strong><?php echo $aTrunos[0]?></strong><?php echo (isset($aTrunos[1]) ? " - " . $aTrunos[1] : "")?></td>
				<td align="center"><?php echo $turno[0]['cantidad']?></td>
                                <td align="center" style="font-weight: bold; <?php echo ($turno[0]['diskette'] == 0 ? "color:red;" : "color:green;")?>"><?php echo $turno[0]['diskette']?></td>
                                <td><?php if($turno[0]['diskette'] != $turno[0]['cantidad']) echo $html->image('controles/error.png',array('border'=>0))?></td>
                                <!--<td align="right"><?php //   echo $util->nf($turno['saldo_actual'])?></td>-->
				<td align="right"><?php echo $util->nf($turno[0]['importe_adebitar'])?></td>
				<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_turno_diskette3/'.$liquidacion['Liquidacion']['id'].'/'.$turno[0]['turno'],'controles/disk.png')?></td>
				<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_turno_pdf3/'.$liquidacion['Liquidacion']['id'].'/'.$turno[0]['turno'].'/PDF','controles/pdf.png','',array('target' => 'blank'))?></td>
				<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/detalle_turno_pdf3/'.$liquidacion['Liquidacion']['id'].'/'.$turno[0]['turno'].'/XLS','controles/ms_excel.png','',array('target' => 'blank'))?></td>
				<td><input type="checkbox" <?php echo ($turno[0]['diskette'] == 0 ? "disabled" : "")?> name="data[LiquidacionSocioNoimputada][turno_pago][<?php echo $turno[0]['turno']?>]" value="<?php echo $valCheck?>" id="chk_<?php echo $i?>" onclick="chkOnclick('TRL_<?php echo $i?>',this)"></td>				
			</tr>		
		<?php endforeach;?>
	</table>
	<table>
		<tr>
			<th colspan="2">DATOS PARA GENERAR DISKETTE</th>
		</tr>
		<tr>
			<th>TOTAL REGISTROS</th>
			<!--<th>TOTAL LIQUIDADO</th>-->
			<th>TOTAL A DEBITAR</th>
		</tr>
		<tr>
			<td align="center"><h3><span id="total_registros"></span></h3></td>
			<!--<td align="right"><h3><span id="total_liquidado"></span></h3></td>-->
			<td align="right"><h3><span id="total_enviado"></span></h3></td>
		</tr>
		<tr>
			<td colspan="3"><hr/></td>
		</tr>		
		<?php if($liquidacion['Liquidacion']['mostar_bancos'] == 1):?>
		<tr>
			<td align="right">GENERAR ARCHIVO PARA BANCO</td><td colspan="2"><?php echo $this->requestAction('/config/bancos/combo/LiquidacionSocioNoimputada.banco_intercambio/0/0/5')?></td>
		</tr>
		<tr>
			<td align="right">FECHA DE DEBITO</td><td colspan="2"><?php echo $frm->input('LiquidacionSocioNoimputada.fecha_debito',array('dateFormat' => 'DMY'))?></td>
		</tr>
		<tr id="bcoNacionNroArchivo">
			<td align="right">NUMERO DE ARCHIVO</td>
			<td><?php echo $frm->number('LiquidacionSocioNoimputada.nro_archivo',array('maxlength' => 4,'size' => 4))?></td>
		</tr>
		<tr>
			<td align="right">FECHA DE PRESENTACION</td><td colspan="2">
                <?php echo $frm->calendar('LiquidacionSocioNoimputada.fecha_presentacion','',date('Y-m-d'),date("Y"),date("Y") + 1)?>
		</tr>
		<tr id="bcoCbaNroConvenio">
			<td align="right">NUMERO DE CONVENIO</td>
			<td><?php echo $frm->number('LiquidacionSocioNoimputada.nro_convenio_cba',array('maxlength' => 5,'size' => 5))?></td>
		</tr>
                <tr id="cuenca_fecha_maxima">
			<td align="right">FECHA VENCIMIENTO MAXIMA</td><td colspan="2">
                        <?php echo $frm->calendar('LiquidacionSocioNoimputada.fecha_maxima','',date('Y-m-d'),date("Y"),date("Y") + 1)?>
                </tr>
		<tr id="cuenca_ciclos">
			<td align="right">NUMERO DE CICLOS</td>
			<td><?php echo $frm->number('LiquidacionSocioNoimputada.nro_ciclos',array('maxlength' => 2,'size' => 2))?></td>
		</tr>
		<?php endif;?>
		<tr><td colspan="3" align="center"><?php echo $frm->submit("PROCESAR DATOS PARA GENERAR DISKETTE",array('id' => 'btn_genDiskette'))?></td></tr>	
	</table>
	<?php echo $frm->hidden('LiquidacionSocioNoimputada.liquidacion_id', array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->hidden('LiquidacionSocioNoimputada.periodo', array('value' => $liquidacion['Liquidacion']['periodo']))?>
	<?php echo $frm->hidden('LiquidacionSocioNoimputada.codigo_organismo', array('value' => $liquidacion['Liquidacion']['codigo_organismo']))?>
	
	<?php echo $frm->end();?>
<?php // debug($turnos)?>
<?php endif;?>