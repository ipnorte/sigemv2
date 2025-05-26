<?php 

$response = $this->requestAction('/pfyj/personas/get_consulta_bcra/' . $cuit);

$historico = (isset($historico) ? $historico : FALSE);
$afip = (isset($afip) ? $afip : FALSE);

?>
<?php if(!empty($response)):?>

    <div style="margin-top: 5px;">
        <div class="areaDatoForm" style="width: 99%;">
        <h3>Ultimo Informe Actualizado por B.C.R.A.</h3>
        <?php if(!empty($response->deudor)):?>

            <table class="tbl_frm">
                <tr>
                <th style="background: white;color: black;">Entidad</th>
                <th style="background: white;color: black;">Periodo</th>
                <th style="background: white;color: black;">CUIT</th>
                <th style="background: white;color: black;">Situacion</th>
                <th style="background: white;color: black;">Monto</th>
            </tr>
            <?php foreach ($response->deudor as $bcra):?>

                <?php 

                    $color = "";
                    //$bcra->situacion = 5;
                    switch (intval($bcra->situacion)) {
                        case 0:
                            $color = "green";
                            break;             
                        case 1:
                            $color = "green";
                            break;             
                        case 2:
                            $color = "orange";
                            break;            
                        case 3:
                            $color = "orangered";
                            break;          
                        default:
                            $color = "red";
                            break;
                    }
                    $bcra->monto = str_replace(",",".", $bcra->monto);
                ?>

                <tr>
                    <td><?php echo $bcra->entidad?></td>
                    <td style="text-align: center;font-weight: bold"><?php echo $util->periodo($bcra->fecha_informacion)?></td>
                    <td style="text-align: center;font-weight: bold"><?php echo $bcra->nro_identificacion?></td>
                    <td style="text-align: center;font-weight: bold;color: <?php echo $color?>;"><h1 style="color: <?php echo $color?>"><?php echo $bcra->situacion?></h1></td>
                    <td style="text-align: center"><?php echo $util->nf($bcra->monto)?></td>
                </tr>

                <?php // debug($bcra);?>

            <?php endforeach;?>
            </table>
        
            <?php if($historico):?>
        
            <div style="width: 98%;margin-top: 10px;">
                <?php if(!empty($response->m24dsf)):?>
                
                <?php if(!empty($response->m24dsf)):?>

                        <table class="tbl_frm">
                            <tr>
                                <th rowspan="3">Entidad</th>
                                <th rowspan="3">CUIT</th>
                                <th colspan="12">Ultimos 6 Meses</th>
                            </tr>
                            <tr>
                                <?php $n = 0;?>
                                <?php for ($mes = 1; $mes <= 6; $mes++):?>
                                    <th colspan="2">-<?php echo $mes?></th>
                                    <?php $n = $mes;?>
                                <?php endfor;?>
                            </tr>
                            <tr>
                                <th>Situacion</th><th>Monto</th><th>Situacion</th><th>Monto</th><th>Situacion</th><th>Monto</th><th>Situacion</th><th>Monto</th>
                                <th>Situacion</th><th>Monto</th><th>Situacion</th><th>Monto</th>
                            </tr>
                            <?php foreach ($response->m24dsf as $bcra):?>
                            <tr>
                                <td><?php echo $bcra->entidad?></td>
                                <td style="text-align: center;font-weight: bold"><?php echo $bcra->nro_identificacion?></td>
                                <?php 
                                    $color = "";
                                    switch (intval($bcra->periodo_01->situacion_2_p1)) {
                                        case 0:
                                            $color = "color:green";
                                            break;             
                                        case 1:
                                            $color = "color:green";
                                            break;             
                                        case 2:
                                            $color = "color:orange";
                                            break;            
                                        case 3:
                                            $color = "color:orangered";
                                            break;          
                                        default:
                                            $color = "color:red";
                                            break;
                                    }
                                    $bcra->periodo_01->monto_2_p1 = str_replace(",",".", $bcra->periodo_01->monto_2_p1);
                                ?>                          
                                <td style="text-align: center;font-weight: bold;<?php echo $color?>;"><?php echo $bcra->periodo_01->situacion_2_p1?></td>
                                <td style="text-align: center;"><?php echo $util->nf($bcra->periodo_01->monto_2_p1)?></td>                        

                                <?php 
                                    $color = "";
                                    switch (intval($bcra->periodo_02->situacion_3_p1)) {
                                        case 0:
                                            $color = "color:green";
                                            break;             
                                        case 1:
                                            $color = "color:green";
                                            break;             
                                        case 2:
                                            $color = "color:orange";
                                            break;            
                                        case 3:
                                            $color = "color:orangered";
                                            break;          
                                        default:
                                            $color = "color:red";
                                            break;
                                    }
                                    $bcra->periodo_02->monto_3_p1 = str_replace(",",".", $bcra->periodo_02->monto_3_p1);
                                ?>                          
                                <td style="text-align: center;font-weight: bold;<?php echo $color?>;"><?php echo $bcra->periodo_02->situacion_3_p1?></td>
                                <td style="text-align: center;"><?php echo $util->nf($bcra->periodo_02->monto_3_p1)?></td>                         

                                <?php 
                                    $color = "";
                                    switch (intval($bcra->periodo_03->situacion_4_p1)) {
                                        case 0:
                                            $color = "color:green";
                                            break;             
                                        case 1:
                                            $color = "color:green";
                                            break;             
                                        case 2:
                                            $color = "color:orange";
                                            break;            
                                        case 3:
                                            $color = "color:orangered";
                                            break;          
                                        default:
                                            $color = "color:red";
                                            break;
                                    }
                                    $bcra->periodo_03->monto_4_p1 = str_replace(",",".", $bcra->periodo_03->monto_4_p1);
                                ?>                          
                                <td style="text-align: center;font-weight: bold;<?php echo $color?>;"><?php echo $bcra->periodo_03->situacion_4_p1?></td>
                                <td style="text-align: center;"><?php echo $util->nf($bcra->periodo_03->monto_4_p1)?></td>                           

                                <?php 
                                    $color = "";
                                    switch (intval($bcra->periodo_04->situacion_5_p1)) {
                                        case 0:
                                            $color = "color:green";
                                            break;             
                                        case 1:
                                            $color = "color:green";
                                            break;             
                                        case 2:
                                            $color = "color:orange";
                                            break;            
                                        case 3:
                                            $color = "color:orangered";
                                            break;          
                                        default:
                                            $color = "color:red";
                                            break;
                                    }
                                    $bcra->periodo_04->monto_5_p1 = str_replace(",",".", $bcra->periodo_04->monto_5_p1);
                                ?>                          
                                <td style="text-align: center;font-weight: bold;<?php echo $color?>;"><?php echo $bcra->periodo_04->situacion_5_p1?></td>
                                <td style="text-align: center;"><?php echo $util->nf($bcra->periodo_04->monto_5_p1)?></td>  

                                <?php 
                                    $color = "";
                                    switch (intval($bcra->periodo_05->situacion_6_p1)) {
                                        case 0:
                                            $color = "color:green";
                                            break;             
                                        case 1:
                                            $color = "color:green";
                                            break;             
                                        case 2:
                                            $color = "color:orange";
                                            break;            
                                        case 3:
                                            $color = "color:orangered";
                                            break;          
                                        default:
                                            $color = "color:red";
                                            break;
                                    }
                                    $bcra->periodo_05->monto_6_p1 = str_replace(",",".", $bcra->periodo_05->monto_6_p1);
                                ?>                          
                                <td style="text-align: center;font-weight: bold;<?php echo $color?>;"><?php echo $bcra->periodo_05->situacion_6_p1?></td>
                                <td style="text-align: center;"><?php echo $util->nf($bcra->periodo_05->monto_6_p1)?></td>   

                                <?php 
                                    $color = "";
                                    switch (intval($bcra->periodo_06->situacion_7_p1)) {
                                        case 0:
                                            $color = "color:green";
                                            break;             
                                        case 1:
                                            $color = "color:green";
                                            break;             
                                        case 2:
                                            $color = "color:orange";
                                            break;            
                                        case 3:
                                            $color = "color:orangered";
                                            break;          
                                        default:
                                            $color = "color:red";
                                            break;
                                    }
                                    $bcra->periodo_06->monto_7_p1 = str_replace(",",".", $bcra->periodo_06->monto_7_p1);
                                ?>                          
                                <td style="text-align: center;font-weight: bold;<?php echo $color?>;"><?php echo $bcra->periodo_06->situacion_7_p1?></td>
                                <td style="text-align: center;"><?php echo $util->nf($bcra->periodo_06->monto_7_p1)?></td>                         

                            </tr>
                            <?php endforeach;?>
                        </table>



                            <?php endif;?>                
                
                <?php endif;?>
            </div>
        
            <?php endif;?>

        <?php elseif($response->statusCode == '401'):?>
        
        <div class="notices_error">
            Sus credenciales de acceso al servicio son incorrectas.
        </div>
        
        <?php else:?>

            *** SIN INFORMACION ***

        <?php endif;?>
        </div>



        <?php if($afip):?>
        <div class="areaDatoForm3" style="width: 98%;">
            <h3>Consulta Padrón AFIP</h3>
            <?php if(!empty($response->personaAfip)):?>


                    <strong><?php echo $response->personaAfip->cuitCuilCdi?></strong>
                    &nbsp;-&nbsp;<?php echo $response->personaAfip->denominacion?>
                    <?php if(!empty($response->personaAfip->afip_actividades)):?>
                    <br>
                    <span style="font-size: 70%"><?php echo $response->personaAfip->afip_actividades->codigo?> | <?php echo $response->personaAfip->afip_actividades->descripcion?></span>
                    <?php endif;?>

            <?php else:?>

                *** SIN INFORMACION ***  
            <?php endif;?>
            </div> 
        <?php endif;?>
    </div>
<?php endif; ?>

<?php // debug($response);?>
