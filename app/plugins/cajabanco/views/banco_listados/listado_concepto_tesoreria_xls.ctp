<?php 


App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();


	$i=0;
	$hoja = 0;
	$renglon = 0;
	
	while($i < count($aDatos)):
	  	$concepto = $aDatos[$i]['AsincronoTemporal']['clave_1'];
	  	
	  	## TITULO
	  	$oPHPExcel->createSheet($hoja);
		$oPHPExcel->setActiveSheetIndex($hoja);
		$oPHPExcel->getActiveSheet()->setTitle(substr($aDatos[$i]['AsincronoTemporal']['texto_1'], 0, 30));
		
		$oPHPExcel->getActiveSheet()->setCellValue('B1','LISTADO CONCEPTO DE TESORERIA :');
		$oPHPExcel->getActiveSheet()->setCellValue('B2','DESDE FECHA: ');
		$oPHPExcel->getActiveSheet()->setCellValue('C2',utf8_encode($util->armaFecha($fecha_desde)));
		$oPHPExcel->getActiveSheet()->setCellValue('D2','HASTA FECHA: ');
		$oPHPExcel->getActiveSheet()->setCellValue('E2',utf8_encode($util->armaFecha($fecha_hasta)));
		
		$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(16);
		$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
			
		
		$oPHPExcel->getActiveSheet()->setCellValue('B4','CONCEPTO:');
		$oPHPExcel->getActiveSheet()->setCellValue('C4',$aDatos[$i]['AsincronoTemporal']['texto_1']);
		
		$oPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setSize(14);
		$oPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);

		
		
		$renglon = 3;

		while($concepto == $aDatos[$i]['AsincronoTemporal']['clave_1'] && $i < count($aDatos)):
			$banco = $aDatos[$i]['AsincronoTemporal']['clave_2'];
			
			$renglon += 3;
			
			$oPHPExcel->getActiveSheet()->setCellValue('B' . $renglon,$aDatos[$i]['AsincronoTemporal']['texto_2']);
		
			$oPHPExcel->getActiveSheet()->getStyle('B' . $renglon)->getFont()->setSize(12);
			$oPHPExcel->getActiveSheet()->getStyle('B' . $renglon)->getFont()->setBold(true);

			$renglon += 1;
			
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$renglon,"NRO.MOV.");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$renglon,"FECHA");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$renglon,"F.VENCIM.");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$renglon,"DESTINATARIO");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$renglon,"TIPO DOC.");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$renglon,"DESCRIPCION");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$renglon,"NRO.OPERACION");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$renglon,"DEBE");
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8,$renglon,"HABER");
	
			$oPHPExcel->getActiveSheet()->getStyle("A$renglon")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$oPHPExcel->getActiveSheet()->getStyle("A$renglon")->getFill()->getStartColor()->setRGB('969696');
			$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$renglon"), "B$renglon:".$oPHPExcel->getActiveSheet()->getHighestColumn().$renglon);

			for ($j=2; $j<9; $j++) {
				$oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
			}
			
			## CAMBIO DE BANCO
			while($banco == $aDatos[$i]['AsincronoTemporal']['clave_2'] && $concepto == $aDatos[$i]['AsincronoTemporal']['clave_1'] && $i < count($aDatos)):
				$anulado = '';
				if($aDatos[$i]['AsincronoTemporal']['texto_3'] == '1'):
					$anulado = ' (ANULADO)';
				endif;
				
				$renglon += 1;
			
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$renglon,utf8_encode($aDatos[$i]['AsincronoTemporal']['entero_1']));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$renglon,utf8_encode($util->armaFecha($aDatos[$i]['AsincronoTemporal']['texto_8'])));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$renglon,utf8_encode($util->armaFecha($aDatos[$i]['AsincronoTemporal']['texto_9'])));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$renglon,utf8_encode($aDatos[$i]['AsincronoTemporal']['texto_4'] . $anulado));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$renglon,utf8_encode($aDatos[$i]['AsincronoTemporal']['texto_5']));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$renglon,utf8_encode($aDatos[$i]['AsincronoTemporal']['texto_7'] . '  ' . $aDatos[$i]['AsincronoTemporal']['texto_13']));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$renglon,utf8_encode($aDatos[$i]['AsincronoTemporal']['texto_10']));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$renglon,utf8_encode($aDatos[$i]['AsincronoTemporal']['decimal_1']));
				$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8,$renglon,utf8_encode($aDatos[$i]['AsincronoTemporal']['decimal_2']));

				
				
				$i++;
			endwhile;
		endwhile;
		$hoja += 1;
	endwhile;


$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="listado_concepto_tesoreria.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>
