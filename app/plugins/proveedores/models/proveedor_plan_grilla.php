<?php

/**
*
* proveedor_plan_grilla.php
* @author adrian [* 20/12/2012]
*/

App::import('Vendor','GeneraXLS',array('file' => 'genera_xls.php'));

class ProveedorPlanGrilla extends ProveedoresAppModel{
	
	var $name = 'ProveedorPlanGrilla';
	
	function getById($id) {
	    $grilla = $this->read(null, $id);
	    if(!empty($grilla)){
	        App::import('model', 'proveedores.metodo_calculo_cuota');
	        $oCALC = new MetodoCalculoCuota();
	        $grilla['ProveedorPlanGrilla']['tea'] = $oCALC->tna_to_tea($grilla['ProveedorPlanGrilla']['tna']);
	    }
	    return $grilla;
	}
	
    function getByPlan($plan_id){

        $SQL = "
            SELECT 
                ProveedorPlanGrilla.id,
                ProveedorPlanGrilla.proveedor_plan_id,
                ProveedorPlanGrilla.descripcion,
                ProveedorPlanGrilla.vigencia_desde,
                ProveedorPlanGrilla.tna,
                ProveedorPlanGrilla.tnm,
                ProveedorPlanGrilla.tem,
                ProveedorPlanGrilla.gasto_admin,
                ProveedorPlanGrilla.sellado,
                ProveedorPlanGrilla.iva,
                ProveedorPlanGrilla.metodo_calculo,
                ProveedorPlanGrilla.tipo_cuota_gasto_admin,
                ProveedorPlanGrilla.tipo_cuota_sellado,
                ProveedorPlanGrilla.gasto_admin_base_calculo,
                ProveedorPlanGrilla.sellado_base_calculo,
                ProveedorPlanGrilla.user_created,
                ProveedorPlanGrilla.created,
                ProveedorPlanGrilla.observaciones,
                ProveedorPlanGrilla.cuotas,
                TipoCuotaGtoAdm.concepto_1 as tipo_cuota_gasto_admin_desc,
                TipoCuotaGtoSell.concepto_1 as tipo_cuota_sellado_desc
            FROM
                proveedor_plan_grillas AS ProveedorPlanGrilla
            LEFT JOIN 
                global_datos TipoCuotaGtoAdm on TipoCuotaGtoAdm.id = ProveedorPlanGrilla.tipo_cuota_gasto_admin 
            LEFT JOIN 
                global_datos TipoCuotaGtoSell on TipoCuotaGtoSell.id = ProveedorPlanGrilla.tipo_cuota_sellado    
            WHERE
                ProveedorPlanGrilla.proveedor_plan_id = $plan_id
            ORDER BY ProveedorPlanGrilla.vigencia_desde DESC , ProveedorPlanGrilla.created DESC;        
        ";
        $grillas = $this->query($SQL);
        
        if(!empty($grillas)){
            
            App::import('model', 'proveedores.metodo_calculo_cuota');
            $oCALC = new MetodoCalculoCuota(); 
            
            foreach ($grillas as $i => $value) {
                $TEA = round($oCALC->tna_to_tea($value['ProveedorPlanGrilla']['tna']),2);
                $value['ProveedorPlanGrilla']['tea'] = $TEA;                
                $grillas[$i] = $value;
            }
        }
        
        
    	// $grillas = $this->find('all',array('conditions' => array('ProveedorPlanGrilla.proveedor_plan_id' => $plan_id),'order' => array('ProveedorPlanGrilla.vigencia_desde,ProveedorPlanGrilla.created DESC')));
        // DEBUG($grillas);
        // exit;
    	return $grillas;
    }	
	
	function cargarGrilla($datos){
		
		
		$grilla = array();
		$grilla['ProveedorPlanGrilla']['id'] = 0;
		$grilla['ProveedorPlanGrilla']['proveedor_plan_id'] = $datos['ProveedorPlanGrilla']['proveedor_plan_id'];
		$grilla['ProveedorPlanGrilla']['descripcion'] = $datos['ProveedorPlanGrilla']['descripcion'];
		$grilla['ProveedorPlanGrilla']['vigencia_desde'] = $datos['ProveedorPlanGrilla']['vigencia_desde'];
		$grilla['ProveedorPlanGrilla']['cuotas'] = serialize($datos['ProveedorPlanGrilla']['cuotas']);
                $grilla['ProveedorPlanGrilla']['tna'] = $datos['ProveedorPlanGrilla']['tna'];
                $grilla['ProveedorPlanGrilla']['tnm'] = $datos['ProveedorPlanGrilla']['tnm'];
                $grilla['ProveedorPlanGrilla']['tem'] = $datos['ProveedorPlanGrilla']['tem'];
//                $grilla['ProveedorPlanGrilla']['cft'] = $datos['ProveedorPlanGrilla']['cft'];
		$grilla['ProveedorPlanGrilla']['xls'] = $datos['ProveedorPlanGrilla']['excel'];
		$grilla['ProveedorPlanGrilla']['observaciones'] = $datos['ProveedorPlanGrilla']['observaciones'];
		
//		debug($grilla);
//                exit;
		
                if(!$this->save($grilla)){ return false;}
		
		$grillaID = $this->getLastInsertID();


		
		//guardo las cuotas
		if(!empty($datos['ProveedorPlanGrilla']['cuotas']['detalle'])){
                    
                        App::import('model','proveedores.metodo_calculo_cuota');
                        $oCALC = new MetodoCalculoCuota();                    
			
			$this->query("DELETE FROM proveedor_plan_grilla_cuotas WHERE proveedor_plan_grilla_id = $grillaID;");
			
			App::import('Model','proveedores.ProveedorPlanGrillaCuota');
			$oPLANCUO = new ProveedorPlanGrillaCuota();
			$cuotas = array();
			$cuotas['ProveedorPlanGrillaCuota'] = array();
			foreach($datos['ProveedorPlanGrilla']['cuotas']['detalle'] as $row){

				$fields = array_keys($row);
				$cuota = array();
				
				foreach($fields as $field){
					$cuota['ProveedorPlanGrillaCuota']['id'] = 0;
					$cuota['ProveedorPlanGrillaCuota']['proveedor_plan_grilla_id'] = $grillaID;
					
					if($field == 'EN_MANO'){
						$cuota['ProveedorPlanGrillaCuota']['liquido'] = $row['EN_MANO'];
					}else if($field == 'SOLICITADO'){
						$cuota['ProveedorPlanGrillaCuota']['capital'] = $row['SOLICITADO'];
					}else{
						$cuota['ProveedorPlanGrillaCuota']['cuotas'] = $field;
						$cuota['ProveedorPlanGrillaCuota']['importe'] = $row[$field];
						if($cuota['ProveedorPlanGrillaCuota']['importe'] != 0){
							$oPLANCUO->id = 0;
                                                        $cuota['ProveedorPlanGrillaCuota']['tna'] = $datos['ProveedorPlanGrilla']['tna'];
                                                        $cuota['ProveedorPlanGrillaCuota']['tem'] = $datos['ProveedorPlanGrilla']['tem'];
                                                        $cuota['ProveedorPlanGrillaCuota']['cft'] = $oCALC->cft($cuota['ProveedorPlanGrillaCuota']['liquido'], $cuota['ProveedorPlanGrillaCuota']['importe'], $cuota['ProveedorPlanGrillaCuota']['cuotas']);
							$oPLANCUO->save($cuota);
						}
					}
				}
				
			}
			
		}
		
		
		return true;
	}	
	
	
	function borrar($id){
		$this->query("DELETE FROM proveedor_plan_grilla_cuotas WHERE proveedor_plan_grilla_id = $id;");
		$this->query("DELETE FROM proveedor_plan_grillas WHERE id = $id;");
	}	

    function cargar_grilla_calculada_nueva($datos){

       
        parent::begin();

        $grilla = array();
        $grilla['ProveedorPlanGrilla']['id'] = 0;
        $grilla['ProveedorPlanGrilla']['proveedor_plan_id'] = $datos['PLAN_ID'];
        $grilla['ProveedorPlanGrilla']['descripcion'] = $datos['DESCRIPCION'];
        $grilla['ProveedorPlanGrilla']['vigencia_desde'] = $datos['VIGENCIA'];
        $grilla['ProveedorPlanGrilla']['cuotas'] = $datos['OBJ_OPCIONES'];
        $grilla['ProveedorPlanGrilla']['tna'] = $datos['TNA'];
        $grilla['ProveedorPlanGrilla']['tnm'] = $datos['TNM'];
        $grilla['ProveedorPlanGrilla']['tem'] = $datos['TEM'];
        $grilla['ProveedorPlanGrilla']['gasto_admin'] = $datos['GTO'];
        $grilla['ProveedorPlanGrilla']['sellado'] = $datos['SELL'];
        $grilla['ProveedorPlanGrilla']['iva'] = $datos['IVA'];
        $grilla['ProveedorPlanGrilla']['metodo_calculo'] = $datos['METODO'];

        $grilla['ProveedorPlanGrilla']['tipo_cuota_gasto_admin'] = (!empty($datos['TIPO_CUOTA_GADM']) ? $datos['TIPO_CUOTA_GADM'] : NULL);
        $grilla['ProveedorPlanGrilla']['gasto_admin_base_calculo'] = $datos['BASE_CALCULO_GADM'];
        $grilla['ProveedorPlanGrilla']['tipo_cuota_sellado'] = (!empty($datos['TIPO_CUOTA_SELL']) ? $datos['TIPO_CUOTA_SELL'] : NULL);
        $grilla['ProveedorPlanGrilla']['sellado_base_calculo'] = $datos['BASE_CALCULO_SELL'];        


        if(!$this->save($grilla)){
            parent::rollback();
            parent::notificar("SE PRODUJO UN ERROR AL GUARDAR LOS DATOS DE LA GRILLA");
            return FALSE;
        }
        $grillaID = $this->getLastInsertID();

        $cuotas = json_decode($grilla['ProveedorPlanGrilla']['cuotas']);
        
        if(!empty($cuotas)){
            App::import('Model','proveedores.ProveedorPlanGrillaCuota');
            $oPLANCUO = new ProveedorPlanGrillaCuota();
            foreach($cuotas as $valor){
                foreach($valor->objetosCalculo as $objeto){

                    $cuota = array();
                    $cuota['ProveedorPlanGrillaCuota']['id'] = 0;
                    $cuota['ProveedorPlanGrillaCuota']['proveedor_plan_grilla_id'] = $grillaID;
                    $cuota['ProveedorPlanGrillaCuota']['liquido'] = $objeto->solicitado;
                    $cuota['ProveedorPlanGrillaCuota']['capital'] = $objeto->liquidacion->capitalSolicitado;
                    $cuota['ProveedorPlanGrillaCuota']['cuotas'] = $objeto->cuotas;
                    $cuota['ProveedorPlanGrillaCuota']['importe'] = $objeto->cuotaPromedio->importe;
                    $cuota['ProveedorPlanGrillaCuota']['capital_puro'] = $objeto->cuotaPromedio->capital;
                    $cuota['ProveedorPlanGrillaCuota']['interes'] = $objeto->cuotaPromedio->interes;
                    $cuota['ProveedorPlanGrillaCuota']['gasto_admin'] = $objeto->liquidacion->gastoAdminstrativo->importe;
                    $cuota['ProveedorPlanGrillaCuota']['sellado'] = $objeto->liquidacion->sellado->importe;
                    $cuota['ProveedorPlanGrillaCuota']['iva'] = $objeto->cuotaPromedio->iva;
                    $cuota['ProveedorPlanGrillaCuota']['cft'] = $objeto->cuotaPromedio->cft;
                    $cuota['ProveedorPlanGrillaCuota']['calculo'] = json_encode($objeto);
                    
                    $oPLANCUO->id = 0;
                    if(!$oPLANCUO->save($cuota)){

                        parent::notificar("ERROR AL GUARDAR LA CUOTA #".$objeto->cuotas);
                        break;
                    }

                }
            }
        }else{
            parent::rollback();
            parent::notificar("NO EXISTEN DATOS DEL CALCULO DE LAS CUOTAS");
            return FALSE;
        }

        parent::commit();
            
        return TRUE;      

    }
        
    function cargar_grilla_calculada($datos){

            parent::begin();
            
            // debug($datos);
            // exit;
            

            $grilla = array();
            $grilla['ProveedorPlanGrilla']['id'] = 0;
            $grilla['ProveedorPlanGrilla']['proveedor_plan_id'] = $datos['PLAN_ID'];
            $grilla['ProveedorPlanGrilla']['descripcion'] = $datos['DESCRIPCION'];
            $grilla['ProveedorPlanGrilla']['vigencia_desde'] = $datos['VIGENCIA'];
            // $grilla['ProveedorPlanGrilla']['cuotas'] = serialize($datos['OBJ_OPCIONES']);
            $grilla['ProveedorPlanGrilla']['cuotas'] = $datos['OBJ_OPCIONES'];
            $grilla['ProveedorPlanGrilla']['tna'] = $datos['TNA'];
            $grilla['ProveedorPlanGrilla']['tnm'] = $datos['TNM'];
            $grilla['ProveedorPlanGrilla']['tem'] = $datos['TEM'];
            $grilla['ProveedorPlanGrilla']['gasto_admin'] = $datos['GTO'];
            $grilla['ProveedorPlanGrilla']['sellado'] = $datos['SELL'];
            $grilla['ProveedorPlanGrilla']['iva'] = $datos['IVA'];
            $grilla['ProveedorPlanGrilla']['metodo_calculo'] = $datos['METODO'];

            $grilla['ProveedorPlanGrilla']['tipo_cuota_gasto_admin'] = $datos['TIPO_CUOTA_GADM'];
            $grilla['ProveedorPlanGrilla']['gasto_admin_base_calculo'] = $datos['BASE_CALCULO_GADM'];
            $grilla['ProveedorPlanGrilla']['tipo_cuota_sellado'] = $datos['TIPO_CUOTA_SELL'];
            $grilla['ProveedorPlanGrilla']['sellado_base_calculo'] = $datos['BASE_CALCULO_SELL'];

//            $grilla['ProveedorPlanGrilla']['xls'] = "<table><tr><td>1</td><td>2</td><td>3</td></tr></table>";



            if(!$this->save($grilla)){
                parent::rollback();
                parent::notificar("SE PRODUJO UN ERROR AL GUARDAR LOS DATOS DE LA GRILLA");
                return FALSE;
            }



//
            $grillaID = $this->getLastInsertID();
            
            $ERROR = FALSE;
            
            $oXLS = new GeneraXLS("grilla.xls");
            
//            $oXLS->setXLSObject(2);
            
            $set = array();
            $set['sheet_title'] = 'GRILLA_CUOTAS';
            $set['labels'] = array(
                'A1' => 'DESCRIPCION',
                'B1' => $datos['DESCRIPCION'],
                'A2' => 'VIGENCIA',
                'B2' => $datos['VIGENCIA'],
                'A3' => 'TNA[%]',
                'B3' => $datos['TNA'],
                'A4' => 'TNM[%]',
                'B4' => $datos['TNM'],
                'A5' => 'GASTO ADMIN[%]',
                'B5' => $datos['GTO'],
                'A6' => 'SELLADO[%]',
                'B6' => $datos['SELL'],
                'A7' => 'IVA[%]',
                'B7' => $datos['IVA'],
                'A8' => 'METODO CALCULO',
                'B8' => $datos['METODO'],
            );
            
            $oXLS->prepareXLSSheet(0, $set,FALSE,10);
            $oXLS->bolderColumnValue(array("B1","B2","B3","B4","B5","B6","B7","B8"));
            
            $set['sheet_title'] = 'DETALLE';
            $oXLS->prepareXLSSheet(1,$set,FALSE,10);
            $oXLS->bolderColumnValue(array("B1","B2","B3","B4","B5","B6","B7","B8"),1);            
            
            
            $result = Set::extract("{n}.{n}.CUOTA",$datos['CALCULO']);
            $cuotas = $result[0];
            $rowindex = 10;
            if(!empty($cuotas)){
                $oXLS->getXLSObject()->setActiveSheetIndex(0);
                $oXLS->writeXLSCell("EN_MANO",0,$rowindex);
                $oXLS->writeXLSCell("SOLICITADO",1,$rowindex);
                foreach($cuotas as $i => $cuota){
                    $oXLS->writeXLSCell($cuota,($i + 2),$rowindex);
                }
                
//                $rowindex = 10;
                
                // hoja 2
                $oXLS->getXLSObject()->setActiveSheetIndex(1);
                $oXLS->writeXLSCell("EN_MANO",0,$rowindex);
                $oXLS->writeXLSCell("SOLICITADO",1,$rowindex);
                
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );                

                $oXLS->getXLSObject()->setActiveSheetIndex(1)->mergeCells("A10:A11");
                $oXLS->getXLSObject()->setActiveSheetIndex(1)->getStyle("A10:A11")->applyFromArray($style);
//                $sheet->getStyle("A1:B1")->applyFromArray($style);
                
                $oXLS->getXLSObject()->setActiveSheetIndex(1)->mergeCells("B10:B11");
                $oXLS->getXLSObject()->setActiveSheetIndex(1)->getStyle("B10:B11")->applyFromArray($style);
                
                $oXLS->bolderColumnValue("A10",1);
                $oXLS->bolderColumnValue("B10",1);
                $oXLS->fillerColumnValue("A10",1);
                $oXLS->fillerColumnValue("B10",1);                
                
                $columIndex = 2;
                foreach($cuotas as $cuota){
                    
                    $coord = $oXLS->get_coordenadas($columIndex, $rowindex);
                    
                    $combine = $oXLS->get_coordenadas($columIndex, $rowindex).":".$oXLS->get_coordenadas($columIndex + 6, $rowindex);
                    
                    $oXLS->writeXLSCell($cuota,$columIndex,$rowindex);
                    
                    $oXLS->bolderColumnValue($coord,1);
                    $oXLS->fillerColumnValue($coord,1);
                    
                    $oXLS->writeXLSCell("CAPITAL_PROM",$columIndex,$rowindex + 1);
                    $oXLS->writeXLSCell("INTERES_PROM",$columIndex + 1,$rowindex + 1);
                    $oXLS->writeXLSCell("IVA_PROM",$columIndex + 2,$rowindex + 1);
                    $oXLS->writeXLSCell("GASTO_PROM",$columIndex + 3,$rowindex + 1);
                    $oXLS->writeXLSCell("SELLADO_PROM",$columIndex + 4,$rowindex + 1);
                    $oXLS->writeXLSCell("CUOTA_PROM",$columIndex + 5,$rowindex + 1);
                    $oXLS->writeXLSCell("CFT_PROM",$columIndex + 6,$rowindex + 1);
                    $columIndex += 7;
                    $oXLS->getXLSObject()->setActiveSheetIndex(1)->mergeCells($combine);
                    $oXLS->getXLSObject()->setActiveSheetIndex(1)->getStyle($combine)->applyFromArray($style);
                    
                }
                $rowindex = 11;                
            }
            
//            debug($cuotas);
//            debug($datos['CALCULO']);
            
            if(!empty($datos['CALCULO'])){
                
                $this->query("DELETE FROM proveedor_plan_grilla_cuotas WHERE proveedor_plan_grilla_id = $grillaID;");

                App::import('Model','proveedores.ProveedorPlanGrillaCuota');
                $oPLANCUO = new ProveedorPlanGrillaCuota();
                $cuotas = array();
                $cuotas['ProveedorPlanGrillaCuota'] = array();
                foreach($datos['CALCULO'] as $capital => $calculos){
                    
                    if(!empty($calculos)){
                        
                        $xlsRow = array();
                        $xlsRow2 = array();
                        
                        array_push($xlsRow, $capital);
                        array_push($xlsRow, $capital);
                        
                        array_push($xlsRow2, $capital);
                        array_push($xlsRow2, $capital);
                        
                        
                        foreach($calculos as $calculo){
                            
                            array_push($xlsRow, $calculo['IMPORTE']);
                            
                            
                            array_push($xlsRow2, $calculo['CAPITAL']);
                            array_push($xlsRow2, $calculo['INTERES']);
                            array_push($xlsRow2, $calculo['IVA']);
                            array_push($xlsRow2, $calculo['ADICIONAL']);
                            array_push($xlsRow2, $calculo['SELLADO']);
                            array_push($xlsRow2, $calculo['IMPORTE']);
                            array_push($xlsRow2, $calculo['CFT']);
                            
                            $cuota = array();
                            $cuota['ProveedorPlanGrillaCuota']['id'] = 0;
                            $cuota['ProveedorPlanGrillaCuota']['proveedor_plan_grilla_id'] = $grillaID;
                            $cuota['ProveedorPlanGrillaCuota']['liquido'] = $capital;
                            $cuota['ProveedorPlanGrillaCuota']['capital'] = $capital;
                            $cuota['ProveedorPlanGrillaCuota']['cuotas'] = $calculo['CUOTA'];
                            $cuota['ProveedorPlanGrillaCuota']['importe'] = $calculo['IMPORTE'];
                            $cuota['ProveedorPlanGrillaCuota']['capital_puro'] = $calculo['CAPITAL'];
                            $cuota['ProveedorPlanGrillaCuota']['interes'] = $calculo['INTERES'];
                            $cuota['ProveedorPlanGrillaCuota']['gasto_admin'] = $calculo['ADICIONAL'];
                            $cuota['ProveedorPlanGrillaCuota']['sellado'] = $calculo['SELLADO'];
                            $cuota['ProveedorPlanGrillaCuota']['iva'] = $calculo['IVA'];
                            $cuota['ProveedorPlanGrillaCuota']['cft'] = $calculo['CFT'];
                            
                            $oPLANCUO->id = 0;
                            if(!$oPLANCUO->save($cuota)){
                                $ERROR = TRUE;
                                parent::notificar("ERROR AL GUARDAR LA CUOTA #".$calculo['CUOTA']);
                                break;
                            }
                            $i++;
                        }

                        $oXLS->writeXLSRow(0,$xlsRow);
//                        debug($xlsRow2);
                        $oXLS->writeXLSRow(1,$xlsRow2);
                        $rowindex++;
                    }else{
                        $ERROR = TRUE;
                        parent::notificar("NO EXISTEN VALORES DE CALCULO");
                        break;
                    }
                }                
                
            }else{
                parent::rollback();
                parent::notificar("NO EXISTEN DATOS DEL CALCULO DE LAS CUOTAS");
                return FALSE;
            }
            
            if($ERROR){
                parent::rollback();
                return FALSE;
            }
            
            $oXLS->saveToXLSFile();
            
            // $grilla = array();
            // $grilla['ProveedorPlanGrilla']['id'] = $grillaID; 
            // $grilla['ProveedorPlanGrilla']['xls'] = $oXLS->getXLSFileBuffer();
            // if(!$this->save($grilla)){
            //     parent::rollback();
            //     parent::notificar("SE PRODUJO UN ERROR AL GUARDAR EL ARCHIVO XLS DE LA GRILLA");
            //     return FALSE;
            // }         
            
//            parent::rollback();
//            exit;            
            
            $oXLS->borrarXLSFile();
            

            
            parent::commit();
            
            return TRUE;
            
        }
        
}

?>