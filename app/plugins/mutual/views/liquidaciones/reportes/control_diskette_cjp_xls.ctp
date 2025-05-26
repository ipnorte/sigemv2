<?php 
//debug($liquidacion);
//debug($socios);
//exit;

Configure::write('debug',0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$leyenda = "CONTROL DISKETTE CJP";
if($codDto == 0 && $altaBaja == 'A') $leyenda .= " - CUOTA SOCIAL ALTAS";
if($codDto == 0 && $altaBaja == 'B') $leyenda .= " - CUOTA SOCIAL BAJAS";
if($codDto == 1 && $altaBaja == 'A') $leyenda .= " - CONSUMOS ALTAS / MODIFICACIONES";
if($codDto == 1 && $altaBaja == 'B') $leyenda .= " - CONSUMOS BAJAS";


$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle("CONTROL DISKETTE CJP");

$oPHPExcel->getActiveSheet()->setCellValue("B1",$leyenda);
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);


$oPHPExcel->getActiveSheet()->setCellValue("A2","PERIODO");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['Liquidacion']['periodo'],true));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

//COLUMNAS
$oPHPExcel->getActiveSheet()->setCellValue("A3","LINEA");
$oPHPExcel->getActiveSheet()->setCellValue("B3","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("C3","SOCIO");
$oPHPExcel->getActiveSheet()->setCellValue("D3","TIPO");
$oPHPExcel->getActiveSheet()->setCellValue("E3","LEY");
$oPHPExcel->getActiveSheet()->setCellValue("F3","BENEFICIO");
$oPHPExcel->getActiveSheet()->setCellValue("G3","SUB_BENEFICIO");
$oPHPExcel->getActiveSheet()->setCellValue("H3","CODIGO_DTO");
$oPHPExcel->getActiveSheet()->setCellValue("I3","IMPORTE");
$oPHPExcel->getActiveSheet()->setCellValue("J3","STATUS");
$oPHPExcel->getActiveSheet()->setCellValue("K3","OPERACION");
$oPHPExcel->getActiveSheet()->setCellValue("L3","OPERACION");
$oPHPExcel->getActiveSheet()->setCellValue("M3","TOTAL_ORDEN");
$oPHPExcel->getActiveSheet()->setCellValue("N3","CUOTAS");
$oPHPExcel->getActiveSheet()->setCellValue("O3","IMPOCUOTA");
$oPHPExcel->getActiveSheet()->setCellValue("P3","ADEUDADO");
$oPHPExcel->getActiveSheet()->setCellValue("Q3","VENCIDO");
$oPHPExcel->getActiveSheet()->setCellValue("R3","AVENCER");
$oPHPExcel->getActiveSheet()->setCellValue("S3","NOVEDAD");

$oPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A3"), "B3:S3");


if(!empty($socios)):

	$i = 4;
	$linea = 1;
	foreach($socios as $socio):
	
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValue($linea);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($socio['LiquidacionSocio']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit(utf8_encode($socio['LiquidacionSocio']['apenom']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($socio['LiquidacionSocio']['tipo']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($socio['LiquidacionSocio']['nro_ley']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit(substr(str_pad(trim($socio['LiquidacionSocio']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($socio['LiquidacionSocio']['sub_beneficio']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValueExplicit($socio['LiquidacionSocio']['codigo_dto']."-".$socio['LiquidacionSocio']['sub_codigo']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValue($socio['LiquidacionSocio']['importe_adebitar']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValueExplicit($socio['LiquidacionSocio']['ERROR_INTERCAMBIO']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValueExplicit($socio['LiquidacionSocio']['orden_descuento_id']);
//		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValueExplicit(str_replace("\n","|",$socio['LiquidacionSocio']['formula_criterio_deuda']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValueExplicit($util->armaFecha($socio['LiquidacionSocio']['fecha_otorgamiento']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,$i)->setValue($socio['LiquidacionSocio']['importe_total']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(13,$i)->setValue($socio['LiquidacionSocio']['cuotas']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(14,$i)->setValue($socio['LiquidacionSocio']['importe_cuota']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(15,$i)->setValue($socio['LiquidacionSocio']['importe_deuda']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(16,$i)->setValue($socio['LiquidacionSocio']['importe_deuda_vencida']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(17,$i)->setValue($socio['LiquidacionSocio']['importe_deuda_no_vencida']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(18,$i)->setValue($socio['LiquidacionSocio']['tipo_novedad']);
		$i++;
	
		$linea++;
		
	endforeach;

endif;


$fileName = "control_diskette_cjp".$liquidacion['Liquidacion']['periodo'].".xls";

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');





?>