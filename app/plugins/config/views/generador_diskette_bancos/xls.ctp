<?php 
//debug($planilla_XLS);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();


$oPHPExcel->getActiveSheet()->setTitle(substr($planilla_XLS['hoja'],0,20));
$offSet = 2;

if(isset($planilla_XLS['titulos']) && !empty($planilla_XLS['titulos'])):
	foreach($planilla_XLS['titulos'] as $coordenada => $valor):
		$oPHPExcel->getActiveSheet()->setCellValue($coordenada,$valor);
		$offSet++;
	endforeach;
endif;


if(!empty($planilla_XLS)):
	
	$i=0;
	foreach ($planilla_XLS['columnas'] as $field => $value) {
		$columnName = Inflector::humanize($value);
		$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i++, $offSet, $columnName);
	}
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFont()->setBold(true);
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->getStartColor()->setRGB('969696');
	$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$offSet"), "B$offSet:".$oPHPExcel->getActiveSheet()->getHighestColumn().$offSet);
	for ($j=1; $j<$i; $j++) {
		$oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
	}
	$i = $offSet + 1;
	foreach ($planilla_XLS['renglones'] as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j,$i)->setValueExplicit($value);
			$j++;
		}
		$i++;
	}	

//	debug($planilla);

endif;

$fileName = str_replace(" ","_",$banco);
$fileName = str_replace(".","",$fileName).".xls";

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>