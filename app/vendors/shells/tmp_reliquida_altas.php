<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tmp_reliquida_altas
 *
 * @author adrian
 * 
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php tmp_reliquida_altas 1 -app /home/mutualam/public_html/sigem/app/
 * 
 */
class TmpReliquidaAltas extends Shell {
    
    
    function main(){
        
        $liquidacion_id = 454;
        $periodo = '201609';

        App::import('Model', 'Mutual.LiquidacionSocio');
        $oLS = new LiquidacionSocio();

        $sql = "SELECT socio_id FROM orden_descuentos OrdenDescuento
                        INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
                        INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
                        WHERE PersonaBeneficio.codigo_beneficio = 'MUTUCORG2201' and Persona.fallecida = 0 
                        AND PersonaBeneficio.turno_pago in ('MUTUEMPRN004','MUTUEMPRE024','MUTUEMPRMU01','MUTUEMPRE084','MUTUEMPRE034','MUTUEMPRE053','MUTUEMPRE048','MUTUEMPRE065','MUTUEMPRE038','MUTUEMPRE082','MUTUEMPRE071','MUTUEMPRE043','MUTUEMPRE031','MUTUEMPRE078','MUTUEMPRE033','MUTUEMPRE029','MUTUEMPRE010','MUTUEMPRE120','MUTUEMPRM034','MUTUEMPRN031','MUTUEMPRN030','MUTUEMPRN026','MUTUEMPRM032','MUTUEMPRM008','MUTUEMPRM009','MUTUEMPRM033','MUTUEMPRM031','MUTUEMPRM022','MUTUEMPRM041','MUTUEMPRE012','MUTUEMPRE006','MUTUEMPRM039')
                        and OrdenDescuento.periodo_ini = '201609'
                        GROUP BY socio_id ORDER BY socio_id;";

        $socios = $oLS->query($sql);
        $total = count($socios);

        $i = 1;
        foreach($socios as $socio){

                $socio_id = $socio['OrdenDescuento']['socio_id'];
                $this->out("LIQUIDANDO SOCIO #$socio_id"."\t".date('H:i:s'));
                $p = intval(($i/$total)*100);
                $statusLiqui = $oLS->liquidar($socio_id,$periodo,'MUTUCORG2201',$liquidacion_id,TRUE,FALSE,FALSE,FALSE,NULL,FALSE);
                if(isset($statusLiqui[0]) && $statusLiqui[0] == 1):
                        debug($statusLiqui);	
                        break;
                endif;
                $this->out($i."/".$total."\t$p%"."\t".$socio_id."\t".date('H:i:s'));
                $this->out("");
                $i++;

        }	        
        
    }
    
}
