<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>
<h2>REPORTES INAES</h2>
<?php 
$tabs = array(
				0 => array('url' => '/mutual/listados/reporte_inaes','label' => 'LISTADO CONSUMOS', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/mutual/listados/reporte_inaesA9/','label' => 'ARTICULO 9', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/listados/reporte_inaesbe','label' => 'BALANCE ELECTRONICO', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);	

?>

<h3>ARTICULO 9</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('frm_listado_inaesA9').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'reporte_inaesA9','id' => 'frm_listado_inaesA9'))?>
	<table class="tbl_form">
		<tr>
			<td>PERIODO IMPUTADO</td>
			<td><?php echo $this->renderElement("liquidacion/periodos_liquidados",array('plugin' => 'mutual', 'imputados' => true))?></td>
			<td><?php echo $frm->submit("CONSULTAR")?></td>
		</tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if(!empty($datos)):?>

	
	<div class="areaDatoForm">
	
		<?php 
		$ACU_CANT = $ACU_IMPO = $ACU_IMPO_TPROM = $ACU_COB_TPROM = 0;
                
		$ACU_CATEGORIA_CANT = $ACU_CATEGORIA_IMPO = $ACU_CATEGORIA_IMPO_PROM = $ACU_CATEGORIA_COB_PROM = 0;
		$ACU_ORG_CANT = $ACU_ORG_IMPO = $ACU_ORG_IMPO_PROM = $ACU_ORG_COB_PROM = 0;
		$ACU_COB_CANT = $ACU_COB_IMPO = 0;
                
                $ACU_IMPO_PROM = $ACU_COB_PROM = 0;
		
		?>
                
		<?php foreach($datos['COBRANZA'] as $periodo => $dato):?>
                        
			<h2><?php echo $util->periodo($periodo,true)?></h2>
			<?php $count_0 = count($dato);?>
                        
	            
                    <?php if(!empty($datos['PADRON'])):?>

                        <?php foreach($datos['PADRON'] as $periodo => $socios):?>
                        
                        <table>
                            <tr>
                                <th colspan="4">INFORME PADRON SOCIOS</th> 
                            </tr>
                            <tr>
                                <th>CATEGORIA</th>
                                <th>ALTAS DEL PERIODO</th>
                                <th>BAJAS DEL PERIODO</th>
                                <th>PADRON VIGENTE</th>
                            </tr>  
                            <?php $TOT_ALTAS = $TOT_BAJAS = $TOT_PADRON = 0?>
                            <?php foreach($socios as $socio):?>
                            <?php 
                            $TOT_ALTAS += $socio[0]['cantidad_altas'];
                            $TOT_BAJAS += $socio[0]['cantidad_bajas'];
                            $TOT_PADRON += $socio['t1']['cantidad_total'];
                            ?>
                            <tr>
                                <td style="text-align: left;"><?php echo $socio['t1']['concepto_1']?></td>
                                <td style="text-align: center;"><?php echo $socio[0]['cantidad_altas']?></td>
                                <td style="text-align: center;"><?php echo $socio[0]['cantidad_bajas']?></td>
                                <td style="text-align: center;"><h5><?php echo $socio['t1']['cantidad_total']?></h5></td>
                            </tr>
                            <?php endforeach;?>
                            <tr class="totales">
                                <th><h5>TOTALES</h5></th>
                                <th style="text-align: center;"><?php echo $TOT_ALTAS?></th>
                                <th style="text-align: center;"><?php echo $TOT_BAJAS?></th>
                                <th style="text-align: center;"><h5><?php echo $TOT_PADRON?></h5></th> 
                            </tr>
                        <?php endforeach;?>
                    </table>
                    
                    
                    <?php endif;?>
                        
                        <h3>VALORES CALCULADOS PARA CUOTA SOCIAL COBRADA</h3>
                        
			<table>
			
			<tr><td colspan="7" style="text-align: right;"><?php echo $controles->botonGenerico('/mutual/listados/reporte_inaesA9/'.$periodo.'/1','controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?></td></tr>
                        <?php // $count_1 = count($dato);?>
			<?php foreach($dato as $categoria => $valores):?>
                        
				<?php $count_1 = count($valores);?>
			
                                <tr><td colspan="7"><h2><?php echo $util->globalDato($categoria)?></h2></td></tr>
				
				<tr>
					<th colspan="3"></th>
					<th>SOCIOS</th>
                                        <th>CUOTA_PROM</th>
					<th>COBRADO</th>
                                        <th>COBRADO_PROM</th>
				</tr>
				
				<?php foreach($valores as $codOrg => $valores_1):?>
                                        
					<tr>
						<td></td>
						<td colspan="6"><h3><?php echo $util->globalDato($codOrg)?></h3></td>
					</tr>
                                        <?php $count_2 = count($valores_1);?>
					<?php foreach($valores_1 as $codCobr => $valores_2):?>
                                        
                                            
                                        
						<tr>
							<td></td>
							<td></td>
							<td><?php echo $util->globalDato($codCobr)?></td>
							<td align="center"><?php echo $valores_2['cantidad_socios']?></td>
                                                        <td align="right"><?php echo $util->nf($valores_2['importe_promedio'])?></td>
							<td align="right"><?php echo $util->nf($valores_2['cobrado'])?></td>
							<td align="right"><?php echo $util->nf($valores_2['cobrado'] / $valores_2['cantidad_socios'])?></td>
						</tr>
						
						<?php 
						
						$ACU_CANT += $valores_2['cantidad_socios'];
						$ACU_IMPO += $valores_2['cobrado'];
						
						$ACU_CATEGORIA_CANT += $valores_2['cantidad_socios'];
						$ACU_CATEGORIA_IMPO += $valores_2['cobrado'];
						
						$ACU_COB_CANT += $valores_2['cantidad_socios'];
						$ACU_COB_IMPO += $valores_2['cobrado'];
						
                                                $ACU_IMPO_PROM += $valores_2['importe_promedio'];
                                                $ACU_COB_PROM += $valores_2['cobrado_promedio'];
                                                
						
//                                                $ACU_CATEGORIA_IMPO_PROM += $valores_2['importe_promedio'];
//                                                $ACU_CATEGORIA_COB_PROM += $valores_2['cobrado_promedio'];
                                                
						?>
						
				
					<?php endforeach;?>
                                                
                                        <?php 
                                        
                                        $ACU_IMPO_PROM = $ACU_IMPO_PROM / $count_2;
                                        $ACU_COB_PROM = $ACU_COB_PROM / $count_2;
                                        
                                        $ACU_CATEGORIA_IMPO_PROM += $ACU_IMPO_PROM;
                                        $ACU_CATEGORIA_COB_PROM += $ACU_COB_PROM;                                        
                                        
                                        ?>        
					
					<tr>
						<td colspan="3" style="text-align: right;">SUBTOTAL</td>
						<td style="border-top: 1px solid;text-align: center;"><?php echo $ACU_COB_CANT?></td>
                                                <td style="border-top: 1px solid;text-align: right;"><?php echo $util->nf($ACU_IMPO_PROM)?></td>
						<td style="border-top: 1px solid; text-align: right;"><?php echo $util->nf($ACU_COB_IMPO)?></td>
                                                <td style="border-top: 1px solid;text-align: right;"><?php echo $util->nf($ACU_COB_IMPO / $ACU_COB_CANT)?></td>
					</tr>
					
					<?php $ACU_COB_CANT = $ACU_COB_IMPO = $ACU_IMPO_PROM = $ACU_COB_PROM = 0;?>
					
				
				<?php endforeach;?>
                                 
                                <?php 
                                    
                                    $ACU_CATEGORIA_IMPO_PROM = $ACU_CATEGORIA_IMPO_PROM / $count_1;
                                    $ACU_CATEGORIA_COB_PROM = $ACU_CATEGORIA_COB_PROM / $count_1;
                                
                                    $ACU_IMPO_TPROM += $ACU_CATEGORIA_IMPO_PROM;
                                    $ACU_COB_TPROM += $ACU_CATEGORIA_COB_PROM;
                                ?>
                                        
				
				<tr class="totales">
					<th colspan="3">TOTAL <?php echo $util->globalDato($categoria)?></th>
					<th style="text-align: center;"><?php echo $ACU_CATEGORIA_CANT?></td>
                                        <th style="text-align: right;"><?php echo $util->nf($ACU_CATEGORIA_IMPO_PROM)?></td>    
					<th style="text-align: right;"><?php echo $util->nf($ACU_CATEGORIA_IMPO)?></td>
                                        <th style="text-align: right;"><?php echo $util->nf($ACU_CATEGORIA_IMPO / $ACU_CATEGORIA_CANT)?></td>    
				</tr>				
				
				<?php $ACU_CATEGORIA_CANT = $ACU_CATEGORIA_IMPO = $ACU_CATEGORIA_IMPO_PROM = $ACU_CATEGORIA_COB_PROM = 0;?>
				
		
			<?php endforeach;?>
			
			<tr class="totales">
				<th colspan="3">TOTAL GENERAL</th>
				<th style="text-align: center;"><?php echo $ACU_CANT?></td>
                                <th style="text-align: right;"><?php echo $util->nf($ACU_IMPO_TPROM / $count_0)?></td>    
				<th style="text-align: right;"><?php echo $util->nf($ACU_IMPO)?></td>
                                <th style="text-align: right;"><?php echo $util->nf($ACU_IMPO / $ACU_CANT)?></td>    
			</tr>			
			
			<tr><td colspan="7" style="text-align: right;"><?php echo $controles->botonGenerico('/mutual/listados/reporte_inaesA9/'.$periodo.'/1','controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?></td></tr>
			
			</table>
		
                        
		<?php endforeach;?>
                    
                    
	</div>
	



<?php endif?>

        
<?php // debug($datos);?>        