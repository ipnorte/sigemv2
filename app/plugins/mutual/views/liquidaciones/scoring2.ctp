<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: SCORING :: ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'],'concepto_1')))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3>ANALISIS DEL STOCK DE DEUDA</h3>
<?php if(!empty($scoring)):?>
<table>
    <tr>
        <th colspan="2"></th>
        <th colspan="12">ANTIG&Uuml;EDAD (en meses)</th>
    </tr>
    <tr>
        <th>Total Liquidado</th>
        <th>Cargos Adic.</th>
        <th colspan="2">0</th>
        <th colspan="2">0 - 3</th>
        <th colspan="2">3 - 6</th>
        <th colspan="2">6 - 9</th>
        <th colspan="2">9 - 12</th>
        <th colspan="2">+12</th>        
    </tr>
    <tr>
        <td class="destacado" style="text-align: right;width: 10%;vertical-align: top;" rowspan="6"><?php echo $util->nf($scoring['total'])?></td>
        <td style="text-align: right;width: 10%;vertical-align: top;" class="destacado" rowspan="6" ><?php echo $util->nf($scoring['cargos_adicionales'])?></td>
        <td class="destacado" style="text-align: right;background: #CDEB8B;width: 10%;"><?php echo $scoring['porc_00']?>%</td><td style="width: 1%;background: #CDEB8B;"></td>
        <td class="destacado" style="text-align: right;background: #FFFF88;width: 10%;"><?php echo $scoring['porc_03']?>%</td><td style="width: 1%;background: #FFFF88;"></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring['porc_06']?>%</td><td style="width: 1%;background: #EFA7B0;"></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring['porc_09']?>%</td><td style="width: 1%;background: #EFA7B0;"></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring['porc_12']?>%</td><td style="width: 1%;background: #EFA7B0;"></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring['porc_13']?>%</td><td style="width: 1%;background: #EFA7B0;"></td>
    </tr>
    <tr>
        <td class="destacado" style="text-align: right;background: #CDEB8B;"><?php echo $util->nf($scoring['00'])?></td><td style="width: 1%;background: #CDEB8B;"><?php echo $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'].'/XLS/00','controles/ms_excel.png','',array('target' => 'blank'))?></td>
        <td class="destacado" style="text-align: right;background: #FFFF88;"><?php echo $util->nf($scoring['03'])?></td><td style="width: 1%;background: #FFFF88;"><?php echo $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'].'/XLS/03','controles/ms_excel.png','',array('target' => 'blank'))?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['06'])?></td><td style="width: 1%;background: #EFA7B0;"><?php echo $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'].'/XLS/06','controles/ms_excel.png','',array('target' => 'blank'))?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['09'])?></td><td style="width: 1%;background: #EFA7B0;"><?php echo $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'].'/XLS/09','controles/ms_excel.png','',array('target' => 'blank'))?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['12'])?></td><td style="width: 1%;background: #EFA7B0;"><?php echo $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'].'/XLS/12','controles/ms_excel.png','',array('target' => 'blank'))?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['13'])?></td><td style="width: 1%;background: #EFA7B0;"><?php echo $controles->botonGenerico('/mutual/liquidaciones/scoring/'.$liquidacion['Liquidacion']['id'].'/XLS/13','controles/ms_excel.png','',array('target' => 'blank'))?></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>        
    </tr>
    <tr>
        <td colspan="12">
            
<?php 

App::import('Vendor','Graph',array('file' => 'jpgraph/src/jpgraph.php'));
App::import('Vendor','LinePlot',array('file' => 'jpgraph/src/jpgraph_line.php'));


$ydata = array($scoring['00']/1000,$scoring['03']/1000,$scoring['06']/1000,$scoring['09']/1000,$scoring['12']/1000,$scoring['13']/1000);

// Create the graph. These two calls are always required
$graph = new Graph(600,300);	
$graph->SetScale("intlin");
$graph->ygrid->SetLineStyle('longdashed');

// Create the linear plot
$lineplot=new LinePlot($ydata);

$lineplot->value->Show();
$lineplot->value->SetColor("red");
$lineplot->value->SetFont(FF_ARIAL,FS_BOLD,10);
$graph->xaxis->SetTickLabels(array(0,'0-3','3-6','6-9','9-12','+12'));
$lineplot->SetFillColor('lightblue');
$lineplot->mark->SetType(MARK_FILLEDCIRCLE);

// Add the plot to the graph
$graph->Add($lineplot);

$graph->img->SetMargin(40,20,20,40);
$graph->title->Set("DISTRIBUCION DEL STOCK DE DEUDA LIQUIDADO");
$graph->xaxis->title->Set("Antiguedad (Meses)");
$graph->yaxis->title->Set("Stock de Deuda (miles de $)");


$fileName = "files" . DS . "graphics" . DS ."scoring_".$liquidacion['Liquidacion']['id'].".png";
if(file_exists(WWW_ROOT . $fileName)) unlink (WWW_ROOT . $fileName);
$graph->Stroke(WWW_ROOT . $fileName);
 


?>
<img src="<?php echo $this->base . DS . $fileName?>"/>            
            
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <?php 
            $datos = array();
            if(!empty($historicos)){
                foreach ($historicos as $historico){
                    $datos[$historico['l']['periodo']] = array(round($historico[0]['saldo_actual']/1000,2),round($historico[0]['importe_debitado']/1000,2));
                }
                ksort($datos);
                $x_axis = array_keys($datos);
                $y1_axis = Set::extract("{n}.0",$datos);
                $y2_axis = Set::extract("{n}.1",$datos);
//                debug($x_axis);
//                debug($y1_axis);
//                debug($y2_axis);
//                debug($datos);
                
                $graph = new Graph(600,300);	
                $graph->SetScale("intlin");
                
                $graph->ygrid->SetLineStyle('longdashed');

                // Create the linear plot
                $lineplot1 = new LinePlot($y1_axis);
                $lineplot2 = new LinePlot($y2_axis);
                
                $lineplot1->value->Show();
                $lineplot1->value->SetColor("red");
                $lineplot1->value->SetFont(FF_ARIAL,FS_BOLD,10);
                $lineplot1->SetLegend('Liquidado');
                $lineplot1->SetFillColor('lightblue');
                
                $lineplot2->value->SetColor("red");
                $lineplot2->value->SetFont(FF_ARIAL,FS_BOLD,10);      
                
                $lineplot2->SetColor("#B22222");
                $lineplot2->SetLegend('Cobrado');    
                $lineplot2->SetFillColor('lightred');
                
                $graph->xaxis->SetTickLabels($x_axis);
                // Add the plot to the graph
                $graph->Add($lineplot1);
                $graph->Add($lineplot2);

                $graph->img->SetMargin(40,20,20,40);
                $graph->title->Set("EVOLUCION DE LOS ULTIMOS 12 MESES IMPUTADOS");
                $graph->xaxis->title->Set("PERIODOS");
                $graph->yaxis->title->Set("Stock de Deuda (miles de $)");
//                $graph->ynaxis[0]->SetColor('teal');
                $graph->legend->SetPos(0.5,0.98,'center','bottom');


                $fileName = "files" . DS . "graphics" . DS ."historico_".$liquidacion['Liquidacion']['id'].".png";
                if(file_exists(WWW_ROOT . $fileName)) unlink (WWW_ROOT . $fileName);
                $graph->Stroke(WWW_ROOT . $fileName);                
                
                echo "<img src=\"". $this->base . DS . $fileName."\"/>"; 

            }
            
            
            ?>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            
            <?php
            
            if($scoring['total'] != 0):
            
            App::import('Vendor','PieGraph',array('file' => 'jpgraph/src/jpgraph_pie.php'));
            $graph = new PieGraph(600,500);
            $graph->SetShadow();

            $data = array(
                0 => $scoring['cargos_adicionales'],
                1 => $scoring['00'],
                2 => ($scoring['03'] + $scoring['06'] + $scoring['09'] + $scoring['12'] + $scoring['13'])
            );
            
            
            $graph->title->Set("COMPOSICION DE LA LIQUIDACION");     
            $p1 = new PiePlot($data);
            $p1->SetLegends(array('Cargos Adicionales','Periodo','Mora'));
            $p1->ExplodeSlice(1);
            $graph->Add($p1);            
            
            $fileName = "files" . DS . "graphics" . DS ."pie_".$liquidacion['Liquidacion']['id'].".png";
            if(file_exists(WWW_ROOT . $fileName)) unlink (WWW_ROOT . $fileName);
            $graph->Stroke(WWW_ROOT . $fileName);                

            echo "<img src=\"". $this->base . DS . $fileName."\"/>"; 
            
            endif;
            ?>
            
        </td>
    </tr>
</table>




<?php endif;?>
