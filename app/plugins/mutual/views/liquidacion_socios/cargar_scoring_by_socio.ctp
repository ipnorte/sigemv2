<?php 

App::import('Vendor','Graph',array('file' => 'jpgraph/src/jpgraph.php'));
App::import('Vendor','LinePlot',array('file' => 'jpgraph/src/jpgraph_line.php'));


?>

<h3>SCORE</h3>
<table class="tblform">
    <tr>
        <td style="text-align: right;">COBRANZA PROMEDIO HISTORICA</td><td><h1><?php echo $score['score']?>%</h1></td>
    </tr>
    <tr>
        <td colspan="2">
        <?php 
        
        $ydata = Set::extract("{n}.0.score",$scores);
        $xdata = Set::extract("{n}.0.periodo",$scores);
        $graph = new Graph(600,300);	
        $graph->SetScale("intlin");
        $graph->ygrid->SetLineStyle('longdashed');
        
        // Create the linear plot
        $lineplot=new LinePlot($ydata);
        
        $lineplot->value->Show();
        $lineplot->value->SetColor("red");

        $lineplot->value->SetFont(FF_ARIAL,FS_BOLD,10);
        $graph->xaxis->SetTickLabels($xdata);
        $graph->xaxis->SetLabelAngle(45);


        // $lineplot->SetFillColor('lightblue');
        $lineplot->mark->SetType(MARK_FILLEDCIRCLE);
        
        // Add the plot to the graph
        $graph->Add($lineplot);
        
        $graph->img->SetMargin(40,20,20,10);
        // $graph->title->Set("EVOLUCION DEL INDICE DE COBRANZA");
        $graph->xaxis->title->Set("Liquidaciones");
        $graph->yaxis->title->Set("Porcentaje de Cobranza");
        
        $IDGRAPHSCORE = rand(0,10);
        $fileNameSores = "files" . DS . "graphics" . DS ."scoring_".$IDGRAPHSCORE.".png";
        if (file_exists(WWW_ROOT . $fileNameSores)) {
            unlink(WWW_ROOT . $fileNameSores);
        }
        $graph->Stroke(WWW_ROOT . $fileNameSores);
        
        ?>
        <img src="<?php echo $this->base . DS . $fileNameSores?>"/> 
        </td>
    </tr>
</table>

<h3>ANALISIS DEL STOCK DE DEUDA</h3>
<table>
    <tr>
        <th colspan="2"></th>
        <th colspan="6">ANTIG&Uuml;EDAD (en meses)</th>
    </tr>
    <tr>
        <th>Total Liquidado</th>
        <th>Cargos Adic.</th>
        <th>0</th>
        <th>0 - 3</th>
        <th>3 - 6</th>
        <th>6 - 9</th>
        <th>9 - 12</th>
        <th>+12</th>        
    </tr>
    <tr>
        <td class="destacado" style="text-align: right;width: 10%;vertical-align: top;" rowspan="6"><?php echo $util->nf($scoring['LiquidacionSocioScore']['saldo_actual'])?></td>
        <td style="text-align: right;width: 10%;vertical-align: top;" class="destacado" rowspan="6" ><?php echo $util->nf($scoring['LiquidacionSocioScore']['cargos_adicionales'])?></td>
        <td class="destacado" style="text-align: right;background: #CDEB8B;width: 10%;"><?php echo $scoring[0]['porc_00']?>%</td>
        <td class="destacado" style="text-align: right;background: #FFFF88;width: 10%;"><?php echo $scoring[0]['porc_03']?>%</td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring[0]['porc_06']?>%</td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring[0]['porc_09']?>%</td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring[0]['porc_12']?>%</td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;width: 10%;"><?php echo $scoring[0]['porc_13']?>%</td>
    </tr>
    <tr>
        <td class="destacado" style="text-align: right;background: #CDEB8B;"><?php echo $util->nf($scoring['LiquidacionSocioScore']['00'])?></td>
        <td class="destacado" style="text-align: right;background: #FFFF88;"><?php echo $util->nf($scoring['LiquidacionSocioScore']['03'])?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['LiquidacionSocioScore']['06'])?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['LiquidacionSocioScore']['09'])?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['LiquidacionSocioScore']['12'])?></td>
        <td class="destacado" style="text-align: right;background: #EFA7B0;"><?php echo $util->nf($scoring['LiquidacionSocioScore']['13'])?></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="6">
            
<?php 



$ydata = array($scoring['LiquidacionSocioScore']['00']/1000,$scoring['LiquidacionSocioScore']['03']/1000,$scoring['LiquidacionSocioScore']['06']/1000,$scoring['LiquidacionSocioScore']['09']/1000,$scoring['LiquidacionSocioScore']['12']/1000,$scoring['LiquidacionSocioScore']['13']/1000);

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


$fileName = "files" . DS . "graphics" . DS ."scoring_".$scoring['LiquidacionSocioScore']['liquidacion_id'].".png";
if (file_exists(WWW_ROOT . $fileName)) {
    unlink(WWW_ROOT . $fileName);
}
$graph->Stroke(WWW_ROOT . $fileName);
 


?>
<img src="<?php echo $this->base . DS . $fileName?>"/>            
            
        </td>
    </tr>
</table>    
<?php 
// debug($scores);
?>