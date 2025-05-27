<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: RESUMEN PROCESO DE PRE-IMPUTACION'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<h3>ANALISIS DE ARCHIVOS DE COBRANZA :: <?php echo $liquidacion['Liquidacion']['organismo']?> </h3>

<?php if($organismo == 22):?>


	<?php if(!empty($resumenes)):?>

				<?php
				$totalCobrado_segun_imputacion = round((isset($total_reintegros['total']) ? $total_reintegros['total'] + $total_reintegros['total_anticipos'] : 0) + $total_imputado - $recuperosEmitidos,2);
				$totalCobrado_segun_bancos = round($resumenes['total_cobrado'],2);
				?>


				<?php
				$tabs = array(
						0 => array('url' => '/mutual/liquidaciones/resumen_cruce_informacion/'.$liquidacion['Liquidacion']['id'].'/1','label' => 'IMPRIMIR RESUMEN', 'icon' => 'controles/pdf.png','atributos' => array('target' => 'blank'), 'confirm' => null),
						1 => array('url' => '/mutual/liquidaciones/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/1/0/' . ($liquidacion['Liquidacion']['imputada'] == 0 ? 1 : 0),'label' => 'PLANILLA GENERAL PROVEEDORES', 'icon' => 'controles/pdf.png','atributos' => array('target' => 'blank'), 'confirm' => null),
				);
				if($liquidacion['Liquidacion']['imputada'] == 0 && ($totalCobrado_segun_imputacion == $totalCobrado_segun_bancos)){

                                    $tabs[2] = array('url' => '/mutual/liquidaciones/imputar_pagos/'.$liquidacion['Liquidacion']['id'],'label' => 'IMPUTAR PAGOS EN CUENTA CORRIENTE DEL SOCIO', 'icon' => 'controles/money_add.png','atributos' => array(), 'confirm' => "IMPUTAR PAGOS EN CUENTA CORRIENTE DEL SOCIO?");

				// }else{

                                    // $tabs[2] = array('url' => '/mutual/stopdebit/index/'.$liquidacion['Liquidacion']['id'],'label' => 'ADMINISTRAR STOP DEBIT', 'icon' => 'controles/stop1.png','atributos' => array(), 'confirm' => NULL);
                                }

				echo $cssMenu->menuTabs($tabs,false);

				?>

				<table>

					<tr class="grilla_destacada">
						<td><strong>1)&nbsp;<?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? 'IMPUTADO EN CUENTA CORRIENTE':'A IMPUTAR EN CUENTA CORRIENTE')?></strong></td>
						<td align="right"><strong><?php echo $util->nf($total_imputado)?></strong></td>
						<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/imputados/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png',null,array('target' => '_blank'))?></td>
						<td></td>
					</tr>
					<?php if(!empty($total_reintegros)):?>
					<tr>
						<td>2)&nbsp;REINTEGROS <?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? 'EMITIDOS':'A EMITIR')?> [<?php echo $total_reintegros['cantidad']?>]</td>
						<td align="right"><?php echo $util->nf($total_reintegros['total'])?></td>
						<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_pdf/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png',null,array('target' => 'blank'))?></td>
						<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png',null,array('target' => 'blank'))?></td>
					</tr>
					<tr>
						<td>3)&nbsp;REINTEGROS ANTICIPADOS EMITIDOS [<?php echo $total_reintegros['cantidad_anticipos']?>]</td>
						<td align="right"><?php echo $util->nf($total_reintegros['total_anticipos'])?></td>
						<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_pdf/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png')?></td>
						<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png')?></td>
					</tr>

					<?php endif;?>
					<?php if(!empty($recuperosEmitidos)):?>
					<tr>
						<td>4)&nbsp;RECUPEROS EMITIDOS</td>
						<td align="right"><?php echo $util->nf($recuperosEmitidos)?></td>
						<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/listado_recuperos/'.$liquidacion['Liquidacion']['id'].'/PDF','controles/pdf.png')?></td>
						<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/listado_recuperos/'.$liquidacion['Liquidacion']['id'].'/XLS','controles/ms_excel.png')?></td>
					</tr>
					<?php endif;?>
					<tr class="totales">

						<td style="border-top: 1px solid;"><h3>TOTAL ACREDITADO EN BANCOS ( 1 + 2 + 3 <?php echo ($recuperosEmitidos != 0 ? " - 4 = 5" : " = 4")?>)</h3></td>
						<td style="border-top: 1px solid;"><h3 style="padding:3px; color:white;background-color: <?php echo ($totalCobrado_segun_imputacion == $totalCobrado_segun_bancos ? "green" : "red")?>;"><?php echo $util->nf($totalCobrado_segun_imputacion)?></h3></td>
						<td style="border-top: 1px solid;"><?php echo ($totalCobrado_segun_imputacion == $totalCobrado_segun_bancos ? $html->image('controles/check.png',array("border"=>"0")) : $html->image('controles/error.png',array("border"=>"0")))?></td>
						<td style="border-top: 1px solid;"></td>
					</tr>
					<?php if(!empty($total_liquidados_no_rendidos)):?>

					<tr class="grilla_texto_error">
						<td><strong>NO RENDIDOS A LA FECHA [<?php echo $total_liquidados_no_rendidos['cantidad']?>]</strong></td>
						<td align="right"><strong><?php echo $util->nf($total_liquidados_no_rendidos['total'])?></strong></td>
						<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_no_encontrados_pdf/'.$liquidacion['Liquidacion']['id'].'/0','controles/pdf.png',null,array('target' => 'blank'))?></td>
						<td align="center">
                                                    <?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_no_encontrados_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png',null,array('target' => 'blank'))?>
                                                </td>
					</tr>
                                        <?php endif;?>


                                        <?php if(!empty($total_mora_cuota_uno)):?>

                                        <tr class="grilla_error">
                                            <td><?php echo $html->image('controles/error.png',array('border'=>0))?>&nbsp;<strong>MORA PRIMER CUOTA</strong>&nbsp;[<?php echo $total_mora_cuota_uno?> ORDENE/S]</td>
                                            <td></td>
                                            <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_cuota_uno_pdf/'.$liquidacion['Liquidacion']['id'].'/0','controles/pdf.png',null,array('target' => 'blank'))?></td>
                                            <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_cuota_uno_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png',null,array('target' => 'blank'))?></td>
                                        </tr>

					<?php endif;?>

                                        <?php if(!empty($total_mora_temprana)):?>

                                        <tr class="grilla_error">
                                            <td><?php echo $html->image('controles/error.png',array('border'=>0))?>&nbsp;<strong>MORA TEMPRANA</strong>&nbsp;[<?php echo $total_mora_temprana?> ORDENE/S]</td>
                                            <td></td>
                                            <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_temprana_pdf/'.$liquidacion['Liquidacion']['id'].'/0','controles/pdf.png',null,array('target' => 'blank'))?></td>
                                            <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_temprana_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png',null,array('target' => 'blank'))?></td>
                                        </tr>

					<?php endif;?>

				</table>


|
				<h2>RESUMEN GENERAL DE LOS ARCHIVOS RECIBIDOS VINCULADOS A ESTA LIQUIDACION</h2>

				<table>

					<?php // debug($resumenes)?>

					<?php foreach($resumenes['detalle'] as $resumen):?>

						<tr>
							<td colspan="7" style="text-align: left;"><h3><?php echo $resumen['nombre']?></h3></td>
						</tr>
						<tr>
							<th>COD</th>
							<th>CONCEPTO</th>
							<th>REG</th>
							<th>%</th>
							<th>IMPORTE</th>
							<th>%</th>
							<th></th>
						</tr>
						<?php foreach($resumen['codigos'] as $codigo):?>

							<tr class="<?php echo ($codigo['indica_pago'] == 1 ? "grilla_destacada" : "")?>">
								<td><?php echo $codigo['status']?></td>
								<td><?php echo $codigo['descripcion']?></td>
								<td align="center"><?php echo $codigo['cantidad_recibida']?></td>
								<td align="right"><?php echo $util->nf($codigo['cantidad_recibida_porc'])?>%</td>
								<td align="right"><?php echo $util->nf($codigo['importe_debitado'])?></td>
								<td align="right"><?php echo $util->nf($codigo['importe_debitado_porc'])?>%</td>
								<td align="center">
                                                                    <?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_detalle_codigo_pdf/'.$liquidacion['Liquidacion']['id'].'/'.$codigo['banco_intercambio'].'/'.$codigo['status'].'/'.(isset($opcionResumen) ? $opcionResumen : 1),'controles/pdf.png',null,array('target' => 'blank'))?>
                                                                    <?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_detalle_codigo_pdf/'.$liquidacion['Liquidacion']['id'].'/'.$codigo['banco_intercambio'].'/'.$codigo['status'].'/'.(isset($opcionResumen) ? $opcionResumen : 1).'/1','controles/ms_excel.png',null,array('target' => 'blank'))?>
                                                                </td>
							</tr>

						<?php endforeach;?>

						<tr class="totales">
							<th colspan="4" style="text-align:right;">DEBITOS COBRADOS</th>
							<th style="color: green;text-align:right;"><?php echo $util->nf($resumen['total_cobrado'])?></th>
							<th style="text-align:right;"><?php echo $util->nf($resumen['total_cobrado_porc'])?>%</th>
							<th></th>
						</tr>
						<tr class="totales">
							<th colspan="4" style="text-align:right;">DEBITOS NO COBRADO</th>
							<th style="text-align:right;"><?php echo $util->nf($resumen['total_no_cobrado'])?></th>
							<th style="text-align:right;"><?php echo $util->nf($resumen['total_no_cobrado_porc'])?>%</th>
							<th></th>
						</tr>
						<tr class="totales">
							<th colspan="4" style="text-align:right;">SUBTOTAL LOTE <?php echo $resumen['nombre']?></th>
							<th style="text-align:right;"><?php echo $util->nf($resumen['total_archivo'])?></th>
							<th style="text-align:right;"><?php echo $util->nf($resumen['total_archivo_porc'])?>%</th>
							<th></th>
						</tr>

					<?php endforeach;?>

					<tr><td colspan="7"><br/></td></tr>
					<tr>
						<th colspan="4" style="text-align: left;font-size: 15px;background: #CDEB8B;color:black;"><?php echo ($recuperosEmitidos != 0 ? "5" : "4")?>)&nbsp;TOTAL ACREDITADO EN BANCOS</th>
						<th style="text-align: right;font-size: 15px;background: #CDEB8B;color: green;"><?php echo $util->nf($resumenes['total_cobrado'])?></th>
						<th style="text-align: right;background: #CDEB8B;color:black;"><?php echo $util->nf($resumenes['total_cobrado_porc'])?>%</th>
						<th style="text-align: right;font-size: 15px;background: #CDEB8B;color:black;"></th>
					</tr>
					<tr>
						<th colspan="4" style="text-align: left;font-size: 15px;background: #EFA7B0;color:black;">TOTAL DEBITOS NO COBRADOS</th>
						<th style="text-align: right;font-size: 15px;background: #EFA7B0;color:black;" ><?php echo $util->nf($resumenes['total_no_cobrado'])?></th>
						<th style="text-align: right;background: #EFA7B0;color:black;"><?php echo $util->nf($resumenes['total_no_cobrado_porc'])?>%</th>
						<th style="text-align: right;font-size: 15px;background: #EFA7B0;color:black;"></th>
					</tr>
					<tr>
						<th colspan="4" style="text-align: left;font-size: 15px;">TOTAL GENERAL LOTE</th>
						<th style="text-align: right;font-size: 15px;"><?php echo $util->nf($resumenes['total_archivo'])?></th>
						<th style="text-align: right;"></th>
						<th style="text-align: right;font-size: 15px;"></th>
					</tr>


			</table>


	<?php else:?>
		<h4>NO EXISTEN DATOS</h4>
	<?php endif;?>

<?php endif;?>

<?php if($organismo == 77):?>
	<?php
	$tabs = array();
	$tabs[0] = array('url' => '/mutual/liquidaciones/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/1/0/' . ($liquidacion['Liquidacion']['imputada'] == 0 ? 1 : 0),'label' => 'PLANILLA GENERAL PROVEEDORES', 'icon' => 'controles/pdf.png','atributos' => array('target' => 'blank'), 'confirm' => null);
	if($liquidacion['Liquidacion']['imputada'] == 0){
		$tabs[2] = array('url' => '/mutual/liquidaciones/imputar_pagos/'.$liquidacion['Liquidacion']['id'],'label' => 'IMPUTAR PAGOS EN CUENTA CORRIENTE DEL SOCIO', 'icon' => 'controles/money_add.png','atributos' => array(), 'confirm' => null);
	}

	echo $cssMenu->menuTabs($tabs,false);

	?>

	<table>

		<tr class="grilla_destacada">
			<td><?php echo $html->image('controles/check.png',array("border"=>"0"))?>&nbsp;<strong><?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? 'IMPUTADO EN CUENTA CORRIENTE':'A IMPUTAR EN CUENTA CORRIENTE')?></strong></td>
			<td align="right"><strong><?php echo $util->nf($total_imputado)?></strong></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/imputados/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png',null,array('target' => 'blank'))?></td>
			<td></td>
		</tr>
		<?php if(!empty($total_reintegros)):?>
		<tr class="amarillo">
			<td><?php echo $html->image('controles/alert.png',array("border"=>"0"))?>&nbsp;REINTEGROS <?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? 'EMITIDOS':'A EMITIR')?> [<?php echo $total_reintegros['cantidad']?>]</td>
			<td align="right"><?php echo $util->nf($total_reintegros['total'])?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_pdf/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png')?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png')?></td>
		</tr>
		<tr>
			<td>REINTEGROS ANTICIPADOS EMITIDOS [<?php echo $total_reintegros['cantidad_anticipos']?>]</td>
			<td align="right"><?php echo $util->nf($total_reintegros['total_anticipos'])?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_pdf/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png')?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png')?></td>
		</tr>
		<?php endif;?>
		<?php if(!empty($recuperosEmitidos)):?>
		<tr>
			<td>RECUPEROS EMITIDOS</td>
			<td align="right"><?php echo $util->nf($recuperosEmitidos)?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/listado_recuperos/'.$liquidacion['Liquidacion']['id'].'/PDF','controles/pdf.png')?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/listado_recuperos/'.$liquidacion['Liquidacion']['id'].'/XLS','controles/ms_excel.png')?></td>
		</tr>
		<?php endif;?>
		<tr class="totales">
			<th>TOTAL RETENIDO DE HABERES</th>
			<th><?php echo $util->nf((isset($total_reintegros['total']) ? $total_reintegros['total'] + $total_reintegros['total_anticipos'] : 0) + $total_imputado - $recuperosEmitidos)?></th>
			<th><?php echo $html->image('controles/check.png',array("border"=>"0"))?></th>
			<th></th>
		</tr>

		<?php if(!empty($total_liquidados_no_rendidos_0)):?>
		<tr class="grilla_texto_error">
			<td><strong>CUOTAS SOCIALES LIQUIDADAS NO INFORMADAS POR EL ORGANISMO [<?php echo $total_liquidados_no_rendidos_0['cantidad']?>]</strong></td>
			<td align="right"><strong><?php echo $util->nf($total_liquidados_no_rendidos_0['total'])?></strong></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_no_encontrados_pdf/'.$liquidacion['Liquidacion']['id'].'/0/1/0','controles/pdf.png','')?></td>
			<td></td>
		</tr>
		<?php endif;?>

		<?php if(!empty($total_liquidados_no_rendidos_1)):?>
		<tr>
			<td><strong>CONSUMOS LIQUIDADOS NO INFORMADOS POR EL ORGANISMO [<?php echo $total_liquidados_no_rendidos_1['cantidad']?>]</strong></td>
			<td align="right"><strong><?php echo $util->nf($total_liquidados_no_rendidos_1['total'])?></strong></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_no_encontrados_pdf/'.$liquidacion['Liquidacion']['id'].'/0/1/1','controles/pdf.png','')?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_nocob_cbu/'.$liquidacion['Liquidacion']['id'].'/0/1/1','controles/disk.png','',array('target' => 'blank'))?></td>
		</tr>
		<?php endif;?>

		<?php if(!empty($total_enviado_no_liquidado)):?>
		<tr class="grilla_texto_error">
			<td><strong>INFORMADOS POR EL ORGANISMO NO LIQUIDADOS [<?php echo $total_enviado_no_liquidado['cantidad']?>]</strong></td>
			<td align="right"><strong><?php echo $util->nf($total_enviado_no_liquidado['total'])?></strong></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/registros_enviados_no_encontrados_pdf/'.$liquidacion['Liquidacion']['id'].'/2','controles/pdf.png','')?></td>
			<td></td>
		</tr>


		<?php endif;?>
                
                <?php if(!empty($total_liquidados_no_rendidos)):?>

                <tr class="grilla_texto_error">
                        <td><strong>NO RENDIDOS A LA FECHA [<?php echo $total_liquidados_no_rendidos['cantidad']?>]</strong></td>
                        <td align="right"><strong><?php echo $util->nf($total_liquidados_no_rendidos['total'])?></strong></td>
                        <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_no_encontrados_pdf/'.$liquidacion['Liquidacion']['id'].'/0','controles/pdf.png',null,array('target' => 'blank'))?></td>
                        <td align="center">
                            <?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_no_encontrados_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png',null,array('target' => 'blank'))?>
                        </td>
                </tr>
                <?php endif;?>


                <?php if(!empty($total_mora_cuota_uno)):?>

                <tr class="grilla_error">
                    <td><?php echo $html->image('controles/error.png',array('border'=>0))?>&nbsp;<strong>MORA PRIMER CUOTA</strong>&nbsp;[<?php echo $total_mora_cuota_uno?> ORDENE/S]</td>
                    <td></td>
                    <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_cuota_uno_pdf/'.$liquidacion['Liquidacion']['id'].'/0','controles/pdf.png',null,array('target' => 'blank'))?></td>
                    <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_cuota_uno_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png',null,array('target' => 'blank'))?></td>
                </tr>

                <?php endif;?>

                <?php if(!empty($total_mora_temprana)):?>

                <tr class="grilla_error">
                    <td><?php echo $html->image('controles/error.png',array('border'=>0))?>&nbsp;<strong>MORA TEMPRANA</strong>&nbsp;[<?php echo $total_mora_temprana?> ORDENE/S]</td>
                    <td></td>
                    <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_temprana_pdf/'.$liquidacion['Liquidacion']['id'].'/0','controles/pdf.png',null,array('target' => 'blank'))?></td>
                    <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/mora_temprana_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png',null,array('target' => 'blank'))?></td>
                </tr>

                <?php endif;?>                
                

	</table>
<?php endif;?>

<?php if($organismo == 66):?>
	<?php
	$tabs = array();
	$tabs[0] = array('url' => '/mutual/liquidaciones/reporte_proveedores/'.$liquidacion['Liquidacion']['id'].'/1/0/' . ($liquidacion['Liquidacion']['imputada'] == 0 ? 1 : 0),'label' => 'PLANILLA GENERAL PROVEEDORES', 'icon' => 'controles/pdf.png','atributos' => array('target' => 'blank'), 'confirm' => null);
	if($liquidacion['Liquidacion']['imputada'] == 0){
		$tabs[2] = array('url' => '/mutual/liquidaciones/imputar_pagos/'.$liquidacion['Liquidacion']['id'],'label' => 'IMPUTAR PAGOS EN CUENTA CORRIENTE DEL SOCIO', 'icon' => 'controles/money_add.png','atributos' => array(), 'confirm' => null);
	}

	echo $cssMenu->menuTabs($tabs,false);

	?>

	<table>

		<tr class="grilla_destacada">
			<td><?php echo $html->image('controles/check.png',array("border"=>"0"))?>&nbsp;<strong><?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? 'IMPUTADO EN CUENTA CORRIENTE':'A IMPUTAR EN CUENTA CORRIENTE')?></strong></td>
			<td align="right"><strong><?php echo $util->nf($total_imputado)?></strong></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/imputados/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png',null,array('target' => 'blank'))?></td>
			<td></td>
		</tr>
		<?php if(!empty($total_reintegros)):?>
		<tr class="amarillo">
			<td><?php echo $html->image('controles/alert.png',array("border"=>"0"))?>&nbsp;REINTEGROS <?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? 'EMITIDOS':'A EMITIR')?> [<?php echo $total_reintegros['cantidad']?>]</td>
			<td align="right"><?php echo $util->nf($total_reintegros['total'])?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_pdf/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png')?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png')?></td>
		</tr>
		<tr>
			<td>REINTEGROS ANTICIPADOS EMITIDOS [<?php echo $total_reintegros['cantidad_anticipos']?>]</td>
			<td align="right"><?php echo $util->nf($total_reintegros['total_anticipos'])?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_pdf/'.$liquidacion['Liquidacion']['id'],'controles/pdf.png')?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/reintegros_xls/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png')?></td>
		</tr>
		<?php endif;?>
		<tr>
			<td>RECUPEROS EMITIDOS</td>
			<td align="right"><?php echo $util->nf($recuperosEmitidos)?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/listado_recuperos/'.$liquidacion['Liquidacion']['id'].'/PDF','controles/pdf.png')?></td>
			<td align="center"><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/listado_recuperos/'.$liquidacion['Liquidacion']['id'].'/XLS','controles/ms_excel.png')?></td>
		</tr>
		<tr class="totales">
			<th>TOTAL RETENIDO DE HABERES</th>
			<th><?php echo $util->nf((isset($total_reintegros['total']) ? $total_reintegros['total'] + $total_reintegros['total_anticipos'] : 0) + $total_imputado - $recuperosEmitidos)?></th>
			<th><?php echo $html->image('controles/check.png',array("border"=>"0"))?></th>
			<th></th>
		</tr>

		<?php if(!empty($total_liquidados_no_rendidos)):?>
		<tr class="grilla_texto_error">
			<td><strong>CONCEPTOS LIQUIDADOS NO INFORMADOS POR EL ORGANISMO [<?php echo $total_liquidados_no_rendidos['cantidad']?>]</strong></td>
			<td align="right"><strong><?php echo $util->nf($total_liquidados_no_rendidos['total'])?></strong></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/resumen_cruce_informacion_no_encontrados_pdf/'.$liquidacion['Liquidacion']['id'].'/0','controles/pdf.png','')?></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/diskette_cjp_nocob_cbu/'.$liquidacion['Liquidacion']['id'].'/0/1/1','controles/disk.png','',array('target' => 'blank'))?></td>
		</tr>
		<?php endif;?>

		<?php if(!empty($total_enviado_no_liquidado)):?>
		<tr class="grilla_texto_error">
			<td><strong>INFORMADOS POR EL ORGANISMO NO LIQUIDADOS [<?php echo $total_enviado_no_liquidado['cantidad']?>]</strong></td>
			<td align="right"><strong><?php echo $util->nf($total_enviado_no_liquidado['total'])?></strong></td>
			<td><?php echo $controles->botonGenerico('/mutual/liquidaciones/registros_enviados_no_encontrados_pdf/'.$liquidacion['Liquidacion']['id'].'/2','controles/pdf.png','')?></td>
			<td></td>
		</tr>


		<?php endif;?>

	</table>
<?php endif;?>
