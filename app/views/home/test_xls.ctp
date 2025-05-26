<?php  


App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

$oPHPExcel->getActiveSheet()->setTitle('PROBANDO XLS');
$oPHPExcel->getActiveSheet()->setCellValue('A1','CELDA A1');
$oPHPExcel->getActiveSheet()->setCellValue('B1','CELDA B1');

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment;filename="test_xls.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>