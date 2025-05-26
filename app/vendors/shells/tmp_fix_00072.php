<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tmp_fix_00072
 *
 * @author adrian
 * 
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php tmp_fix_00072 0 -app /home/adrian/Trabajo/www/sigemv2/app/
 * 
 */

App::import('Model','mutual.LiquidacionIntercambio');
App::import('Model','config.Banco');
App::import('Model','pfyj.SocioReintegro');


class TmpFix00072Shell extends Shell {
    
    public function  main(){
        
        $this->out("START");
        
        $oLI = new LiquidacionIntercambio();
        $oBANCO = new Banco();
        $oSR = new SocioReintegro();
        
        $sql = "select l.id,l.periodo,l.codigo_organismo,i.total_registros,i.lote,
                i.id as intercambio_id,i.banco_id from liquidacion_intercambios i
                inner join liquidaciones l on l.id = i.liquidacion_id
                where i.created > '2020-01-29'
                and banco_id = '00072' and l.imputada = 1;";
        $datos = $oLI->query($sql);
        
        $oLI->query("DELETE FROM tmp_liquidacion_socio_rendiciones_00072");
        
        if(!empty($datos)){
            
            foreach($datos as $dato){
                
                $liquidacionId = $dato['l']['id'];
                $periodo = $dato['l']['periodo'];
                $codigo_organismo = $dato['l']['codigo_organismo'];
                
                $bancoIntercambio = $dato['i']['banco_id'];
                $intercambio_id = $dato['i']['intercambio_id'];
                $total_registros = $dato['i']['total_registros'];
                $lote = $dato['i']['lote'];
                
                $registros = explode("\r\n", $lote);
                $rows = array();
                
                if(!empty($registros)){
                    $primero = array_shift($registros);
                    foreach($registros as $registro){
                        if(!empty($registro)){array_push($rows, $registro);}
                    }
                    $ultimo = $rows[count($rows) - 1];
                }
                
                // cuento los que existen

                if($total_registros < count($rows)){

                    debug($ultimo);
                    debug($total_registros);
                    debug($liquidacionId." * ".$periodo ." * ".$codigo_organismo);
                    
                    $oLI->begin();
                    
                    $decode = $oBANCO->decodeStringDebitoBancoSantanderRio($ultimo);
                    
                    $INSERT = "INSERT INTO liquidacion_socio_rendiciones(periodo,liquidacion_id,
                                liquidacion_intercambio_id,socio_id,codigo_organismo,banco_id,
                                sucursal,tipo_cta_bco,nro_cta_bco,cbu,banco_intercambio,importe_debitado,status,
                                indica_pago,fecha_debito,registro,nro_cuota) ";

                    $values = array();
                    array_push($values, "'".$periodo."'");
                    array_push($values, $liquidacionId);
                    array_push($values, $intercambio_id);                        
                    array_push($values, $decode['socio_id']);
                    array_push($values, "'".$codigo_organismo."'");   
                    array_push($values, "'".$decode['banco_id']."'");
                    array_push($values, "'".$decode['sucursal']."'");
                    array_push($values, "'".$decode['tipo_cta_bco']."'");
                    array_push($values, "'".$decode['nro_cta_bco']."'");
                    array_push($values, "'".$decode['cbu']."'");
                    array_push($values, "'".$bancoIntercambio."'");
                    array_push($values, $decode['importe_debitado']);
                    array_push($values, "'".$decode['status']."'");
                    array_push($values, $decode['indica_pago']);
                    array_push($values, "'".$decode['fecha_debito']."'");
                    array_push($values, "'".$ultimo."'");
                    array_push($values, 1);

                    $INSERT .= "VALUES (".implode(",", $values).")"; 
                    $oLI->query($INSERT);                    
                    
                    if($decode['indica_pago']){
                        $reintegro = array('SocioReintegro' => array(
                            'id' => 0,
                            'socio_id' => $decode['socio_id'],
                            'liquidacion_id' => $liquidacionId,
                            'periodo' => $periodo,
                            'anticipado' => 0,
                            'importe_reintegro' => $decode['importe_debitado'],
                            'user_created' => 'TmpFix00072Shell',
                            'created' => '2020-03-04'
                        ));
                        $oSR->save($reintegro);
                    }
                    
                    
                    $oLI->commit();
                    
                }
                
            }
            
        }
        
    }

    //put your code here
}

/*




select id,total_registros,registros_cobrados,importe_cobrado
,(select count(*) from liquidacion_socio_rendiciones where liquidacion_intercambio_id = i.id) as total
,(select count(*) from liquidacion_socio_rendiciones where liquidacion_intercambio_id = i.id
and indica_pago = 1) as cobrados
,(select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_intercambio_id = i.id
and indica_pago = 1) as cobrado
 from liquidacion_intercambios i
where i.created > '2020-01-29'
and banco_id = '00072'
having total_registros <> total;

-- ACTUALIZO CABECERA ARCHIVOS

update liquidacion_intercambios i
set total_registros = (select count(*) from liquidacion_socio_rendiciones where liquidacion_intercambio_id = i.id),
registros_cobrados = (select count(*) from liquidacion_socio_rendiciones where liquidacion_intercambio_id = i.id
and indica_pago = 1),
importe_cobrado = (select sum(importe_debitado) from liquidacion_socio_rendiciones where liquidacion_intercambio_id = i.id
and indica_pago = 1)
where i.created > '2020-01-29'
and banco_id = '00072';

-- REPORTE
select p.documento,
p.apellido,p.nombre,l.liquidacion_id,
l.periodo,l.importe_debitado,status,indica_pago from liquidacion_socio_rendiciones l
inner join liquidaciones li on li.id = l.liquidacion_id
inner join socios s on s.id = l.socio_id
inner join personas p on p.id = s.persona_id
where li.periodo = '202001' and l.nro_cuota = 1
order by p.apellido,p.nombre;



 * 
 * 
 * 
 * 
 *  */
