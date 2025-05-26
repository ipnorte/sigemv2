<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "comun.php";

require_once PHPEXCEL_APP . 'PHPExcel.php';
require_once PHPEXCEL_APP . 'PHPExcel' . DIRECTORY_SEPARATOR . 'IOFactory.php';

$file = "/home/adrian/trabajo/scripts/aman/bancoColumbia/RGM/RENDICION AMAN 07-2015-2.xlsx";

$oXLS = PHPExcel_IOFactory::load($file);
$objWorksheet = $oXLS->getActiveSheet();
$highestRow = $objWorksheet->getHighestRow();

$reintegros = "";
$sqlCob = "";

$TOTAL_IMPUTADO = $TOTAL_IMPUTADO_BY_SOCIO = $TOTAL_DEBITADO = 0;

for ($xlsrow = 2; $xlsrow <= $highestRow; ++$xlsrow){
    
    $ndoc = $objWorksheet->getCellByColumnAndRow(0, $xlsrow)->getValue();
    $apenom = $objWorksheet->getCellByColumnAndRow(1, $xlsrow)->getValue()." ".$objWorksheet->getCellByColumnAndRow(2, $xlsrow)->getValue();
    $apenom = substr($apenom,0,42);
    
    $ordenDto = $objWorksheet->getCellByColumnAndRow(4, $xlsrow)->getValue();
    $importeDebitado = $objWorksheet->getCellByColumnAndRow(8, $xlsrow)->getValue();

    
    $ndoc = str_pad($ndoc,8,0,STR_PAD_LEFT);
    $cadena = str_pad($ndoc . " - " . $apenom,50,' ',STR_PAD_RIGHT) . "\t" .$ordenDto."\t" . str_pad(number_format(strval($importeDebitado),2,".",""),10,' ',STR_PAD_LEFT);
    echo $cadena ."\n";
    
    $sql = "SELECT id,socio_id,estado,situacion,nro_cuota,periodo,importe,(importe - ifnull((select sum(importe) "
            . "from orden_descuento_cobro_cuotas cocu where cocu.orden_descuento_cuota_id = cu.id),0)) as saldo FROM orden_descuento_cuotas cu "
            . " WHERE cu.orden_descuento_id = $ordenDto and cu.importe > ifnull((select sum(importe) "
            . "from orden_descuento_cobro_cuotas cocu where cocu.orden_descuento_cuota_id = cu.id),0) order by nro_cuota";
    $result = mysql_query($sql,dbLink());
    $cantidad = mysql_num_rows($result);
    $cuotas_pagas = array();
    $saldoDebitoSocio = $importeDebitado;
    $TOTAL_DEBITADO += $saldoDebitoSocio;
    
    if(mysql_num_rows($result) != 0){
        
        $i = 0;
        $TOTAL_IMPUTADO_BY_SOCIO = 0;
        
        while($row = mysql_fetch_assoc($result)){
            
            $cuota_id = $row['id'];
            $cuota_socio = $row['socio_id'];
            $cuota_nro = $row['nro_cuota'];
            $cuota_periodo = $row['periodo'];
            $cuota_estado = $row['estado'];
            $cuota_situacion = $row['situacion'];
            $cuota_saldo = round($row['saldo']);

            if($saldoDebitoSocio >= $cuota_saldo):
                $importeImputaCuota = $cuota_saldo;
                $saldoDebitoSocio -= $cuota_saldo;
            else:
                $importeImputaCuota = $saldoDebitoSocio;
                $saldoDebitoSocio -= $importeImputaCuota;
            endif;  

            $importeImputaCuota = round($importeImputaCuota,2);
            
            $TOTAL_IMPUTADO += $importeImputaCuota;
            $TOTAL_IMPUTADO_BY_SOCIO += $importeImputaCuota;
            
            $cuotas_pagas[$i] = array(
                $cuota_id,$cuota_socio,$cuota_nro,$cuota_periodo,$cuota_estado,$cuota_situacion,$cuota_saldo,$importeImputaCuota
            );
            echo implode("\t", $cuotas_pagas[$i])."\n";
            if($saldoDebitoSocio == 0) break;
            $i++;
        }
        
        if( round(($TOTAL_IMPUTADO_BY_SOCIO - $importeDebitado),2) != 0){
            $reintegros .= $cadena."\t".str_pad(number_format(strval($importeDebitado - $TOTAL_IMPUTADO_BY_SOCIO),2,".",""),10,' ',STR_PAD_LEFT)."\n";
        }
        
        mysql_free_result($result);
        
        if(!empty($cuotas_pagas)){
            $ERROR = FALSE;
            $socio_id = $cuotas_pagas[0][1];
//            mysql_query("START TRANSACTION",dbLink());
            $sqlCob = "insert into sigem_db.orden_descuento_cobros(socio_id,tipo_cobro,fecha,periodo_cobro,importe,user_created,created) "
                    . "VALUES($socio_id, 'MUTUTCOBCRGM','2015-08-31','201508',$importeDebitado,'RGM_201508','2015-08-31');";
//            if(!mysql_query($sqlCob,dbLink())){
//                echo mysql_error();
//                mysql_query("ROLLBACK",dbLink());
//                break;
//            }
            mysql_query( "SELECT LAST_INSERT_ID()");
            $cobro_id = mysql_insert_id();
            foreach ($cuotas_pagas as $cuota){
                $sqlCob = "insert into sigem_db.orden_descuento_cobro_cuotas(periodo_cobro,orden_descuento_cobro_id,
                            orden_descuento_cuota_id,proveedor_id,importe,alicuota_comision_cobranza,comision_cobranza)
                            VALUES ('201508',$cobro_id,".$cuota[0].",13,".$cuota[7].",2,".($cuota[7] * (2 / 100)).");";
//                if(!mysql_query($sqlCob,dbLink())){
//                    echo mysql_error();
//                    mysql_query("ROLLBACK",dbLink());
//                    $ERROR = TRUE;
//                    break;                    
//                }else if($cuota[4] == 'B'){
//                    mysql_query("UPDATE sigem_db.orden_descuento_cuotas set situacion = 'MUTUSICUBCOL' where id = " . $cuota[0] ,dbLink());
//                }
                
                
                
            }
//            if(!$ERROR)mysql_query("COMMIT",dbLink());
//            break;
        }
        
        
        
        
        
        
    }else{
        $reintegros .= $cadena . "\t" . str_pad(number_format(strval($importeDebitado),2,".",""),10,' ',STR_PAD_LEFT) ."\n";
    }
    
   
    
//    print_r($cuotas_pagas);
    echo "\n\n";
//    if(empty($cuotas_pagas)){
//        $reintegros .= $cadena;
//    }

}
echo $TOTAL_IMPUTADO . "\t" . $TOTAL_DEBITADO . "\n";
//echo $sqlCob;
echo "----------------------------------------------\n";
echo $reintegros;
?>
