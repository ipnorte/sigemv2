<?php 

$grilla = json_decode($xls['ProveedorPlanGrilla']['cuotas']);

$objetoCalculo = $grilla[0]->objetosCalculo[0];



// debug($grilla);


// debug($grilla);
// exit;

App::import('Vendor','GeneraXLS',array('file' => 'genera_xls.php'));
$oXLS = new GeneraXLS("grilla.xls");

$set = array();
$set['sheet_title'] = 'OPCIONES DE CUOTAS';
$set['labels'] = array(
    'B1' => 'OPCIONES DE CUOTAS',
    // 'B1' => $xls['ProveedorPlanGrilla']['descripcion'],
    'A2' => 'VIGENCIA',
    'B2' => $xls['ProveedorPlanGrilla']['vigencia_desde'],
    'A3' => 'TNA[%]',
    'B3' => $objetoCalculo->tna,
    'A4' => 'TEA[%]',
    'B4' => $objetoCalculo->tea,
    'A5' => 'TEM[%]',
    'B5' => $objetoCalculo->tem,
    'A6' => $objetoCalculo->liquidacion->gastoAdminstrativo->descripcion,
    'B6' => $objetoCalculo->liquidacion->gastoAdminstrativo->porcentaje,
    'A7' => $objetoCalculo->liquidacion->sellado->descripcion,
    'B7' => $objetoCalculo->liquidacion->sellado->porcentaje,
    'A8' => 'IVA[%]',
    'B8' => $objetoCalculo->ivaAlicuota,
    'A9' => 'METODO CALCULO',
    'B9' => $objetoCalculo->metodoCalculoFormula,
);

$oXLS->prepareXLSSheet(0, $set,FALSE,10);
$oXLS->bolderColumnValue(array("B1","B2","B3","B4","B5","B6","B7","B8"));


$sheet = 0;
$oXLS->getXLSObject()->setActiveSheetIndex($sheet);

$rowindex = 10;

// debug($grilla[0]->objetosCalculo);
// exit;

$oXLS->writeXLSCell("EN_MANO",0,$rowindex,$sheet);
$oXLS->writeXLSCell("SOLICITADO",1,$rowindex,$sheet);

$oXLS->bolderColumnValue($oXLS->get_coordenadas(0,$rowindex),$sheet);
$oXLS->bolderColumnValue($oXLS->get_coordenadas(1,$rowindex),$sheet);
$oXLS->fillerColumnValue($oXLS->get_coordenadas(0,$rowindex),$sheet);
$oXLS->fillerColumnValue($oXLS->get_coordenadas(1,$rowindex),$sheet);

foreach($grilla[0]->objetosCalculo as $i => $objetoCalculo){
    $oXLS->writeXLSCell($objetoCalculo->cuotas,($i + 2),$rowindex,$sheet);
    $oXLS->bolderColumnValue($oXLS->get_coordenadas(($i + 2),$rowindex),$sheet);
    $oXLS->fillerColumnValue($oXLS->get_coordenadas(($i + 2),$rowindex),$sheet);    
}
$rowindex = 11;

$cuotaDetalles = array();

foreach($grilla as $i => $objetoCalculos){

    $colindex = 0;
    // debug($objetoCalculos);

    foreach($objetoCalculos->objetosCalculo as $i => $objetoCalculo){

        $oXLS->writeXLSCellNumberValue($objetoCalculo->solicitado,$colindex,$rowindex,0);
        $oXLS->writeXLSCellNumberValue($objetoCalculo->liquidacion->capitalSolicitado,$colindex + 1,$rowindex,0);
        $oXLS->writeXLSCellNumberValue($objetoCalculo->cuotaPromedio->importe,($colindex + $i + 2),$rowindex,0);

        $oXLS->bolderColumnValue($oXLS->get_coordenadas($colindex,$rowindex),$sheet);
        $oXLS->fillerColumnValue($oXLS->get_coordenadas($colindex,$rowindex),$sheet,'e4fd96'); 
        $oXLS->fillerColumnValue($oXLS->get_coordenadas(($colindex + $i + 2),$rowindex),$sheet,'96fdaf'); 

        // $colindex++;
        $cuotaDetalles[$objetoCalculo->cuotas][$objetoCalculo->solicitado]= array($objetoCalculo->liquidacion->capitalSolicitado,$objetoCalculo->cuotaPromedio);
    }
    $rowindex++;
}

$sheet = 1;
$cuotaIndices = array();

foreach($grilla[0]->objetosCalculo as $i => $objetoCalculo){
    

    $set['sheet_title'] = strval($objetoCalculo->cuotas);;
    $set['labels']['A1'] = 'CUOTAS';
    $set['labels']['B1'] = $objetoCalculo->cuotas;
    $oXLS->prepareXLSSheet($sheet,$set,FALSE,10);
    $oXLS->bolderColumnValue(array("B1","B2","B3","B4","B5","B6","B7","B8"),$sheet);
    $oXLS->getXLSObject()->setActiveSheetIndex($sheet);

    $rowindex = 10;
    $oXLS->writeXLSCell("EN_MANO",0,$rowindex,$sheet);
    $oXLS->writeXLSCell("SOLICITADO",1,$rowindex,$sheet);
    $oXLS->writeXLSCell("CAPITAL",2,$rowindex,$sheet);
    $oXLS->writeXLSCell("INTERES",3,$rowindex,$sheet);
    $oXLS->writeXLSCell("IVA",4,$rowindex,$sheet);
    $oXLS->writeXLSCell("TOTAL",5,$rowindex,$sheet);
    $oXLS->writeXLSCell("CFT",6,$rowindex,$sheet);


    for($j = 0; $j <= 6; $j++){
        $oXLS->bolderColumnValue($oXLS->get_coordenadas($j,$rowindex),$sheet);
        $oXLS->fillerColumnValue($oXLS->get_coordenadas($j,$rowindex),$sheet); 
    }
    
 

    $rowindex = 11;
    array_push($cuotaIndices,array('sheet' => $sheet,'cuotas' => $objetoCalculo->cuotas));
    $sheet++;

    
    

}

foreach($cuotaIndices as $i => $cuotaIndice){

    $sheet = $cuotaIndice['sheet'];
    $cuotas = $cuotaIndice['cuotas'];

    $colindex = 0;
    $rowindex = 11;
    // debug($objetoCalculos);

    foreach($cuotaDetalles[$cuotas] as $solicitado => $cuota){

        $oXLS->writeXLSCellNumberValue($solicitado,$colindex,$rowindex,$sheet);
        $oXLS->writeXLSCellNumberValue($cuota[0],$colindex + 1,$rowindex,$sheet);
        $oXLS->writeXLSCellNumberValue($cuota[1]->capital,($colindex + 2),$rowindex,$sheet);
        $oXLS->writeXLSCellNumberValue($cuota[1]->interes,($colindex + 3),$rowindex,$sheet);
        $oXLS->writeXLSCellNumberValue($cuota[1]->iva,($colindex + 4),$rowindex,$sheet);
        $oXLS->writeXLSCellNumberValue($cuota[1]->importe,($colindex + 5),$rowindex,$sheet);
        $oXLS->writeXLSCellPercentValue($cuota[1]->cft,($colindex + 6),$rowindex,$sheet);

        $oXLS->bolderColumnValue($oXLS->get_coordenadas($colindex,$rowindex),$sheet);
        $oXLS->fillerColumnValue($oXLS->get_coordenadas($colindex,$rowindex),$sheet,'e4fd96'); 
        $oXLS->fillerColumnValue($oXLS->get_coordenadas(($colindex + 5),$rowindex),$sheet,'96fdaf'); 


        // $colindex++;
        $rowindex++;
    }
    
}


$oXLS->saveToXLSFile();
$BUFFER = $oXLS->getXLSFileBuffer();
$oXLS->borrarXLSFile();

Configure::write('debug', 0);
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment;filename="grilla_' . $xls['ProveedorPlanGrilla']['id'] . '.xls"');
header('Cache-Control: max-age=0');
echo $BUFFER;
exit;


?>