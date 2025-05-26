<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

	


<?php if(!empty($turnos)):?>


    <div class="areaDatoForm">
        <h3>DETALLE DE EMPRESAS / TURNOS LIQUIDADOS PARA <?php echo $util->periodo($periodo,true)?></h3>
        <table>
            <tr>
                <th>TURNO</th>
                <th>EMPRESA</th>
                <th>REPARTICION</th>
                <th>REGISTROS</th>
                <th>IMPORTE</th>
            </tr>
            <?php $actual = null?>
            <?php $subtotal = $total = 0;?>
            <?php foreach ($turnos as $key => $value) {?>
            
                
    
                <?php if($actual != $value['LiquidacionSocio']['codigo_organismo']): ?>
                <?php $actual = $value['LiquidacionSocio']['codigo_organismo'];?>
                <?php if($subtotal != 0):?>
                <tr class="subtotales">
                    <th colspan="4" style="text-align: right;">SUB-TOTAL</th>
                    <th><?php echo $util->nf($subtotal) ?></th>
                </tr>
                <?php $subtotal = 0;?>
                <?php endif;?>
                <tr>
                    <td colspan="5" style="font-size:16px;background-color: #e2e6ea;border:0"><?php echo $value['Organismo']['concepto_1'] ?></td>
                </tr>
                <?php endif;?>
                <?php $subtotal += $value[0]['importe_adebitar']?>
                <?php $total += $value[0]['importe_adebitar']?>
            <tr>
                <td><?php echo $value[0]['turno_pago'] ?></td>
                <td><?php echo $value['Empresa']['concepto_1'] ?></td>
                <td><?php echo $value[0]['turno'] ?></td>
                <td style="text-align: center;"><?php echo $value[0]['cant'] ?></td>
                <td style="text-align: right;"><?php echo $util->nf($value[0]['importe_adebitar']) ?></td>
            </tr>
            
            <?php }?>
                <tr class="subtotales">
                    <th colspan="4" style="text-align: right;">SUB-TOTAL</th>
                    <th><?php echo $util->nf($subtotal) ?></th>
                </tr>            
                  <tr class="totales">
                    <th colspan="4" style="text-align: right;">TOTAL</th>
                    <th><?php echo $util->nf($total) ?></th>
                </tr>           
            
        </table>
        <div class="areaDatoForm">

                <h3>SELECCIONAR BANCO POR EL CUAL SE EMITIRA EL LOTE DE COBRANZA PARA <?php echo $util->periodo($periodo,true)?></h3>
                <hr/>
                <table class="tbl_form">
                <tr>
                        <td align="right">GENERAR ARCHIVO PARA BANCO</td><td colspan="2"><?php echo $this->requestAction('/config/bancos/combo/LiquidacionSocio.banco_intercambio/0/0/5')?></td>
                </tr>
                <tr>
                        <td align="right">FECHA DE DEBITO</td>
                        <td colspan="2">
                        <?php echo $frm->calendar('LiquidacionSocio.fecha_debito','',date('Y-m-d'),date("Y")-1,date("Y") + 1)?>
                        </td>
                </tr>
                <tr id="bcoNacionNroArchivo">
                        <td align="right">NUMERO DE ARCHIVO</td>
                        <td><?php echo $frm->number('LiquidacionSocio.nro_archivo',array('maxlength' => 2,'size' => 2))?></td>
                </tr>
                <tr>
                        <td align="right">FECHA DE PRESENTACION</td>
                        <td colspan="2">
                        <?php echo $frm->calendar('LiquidacionSocio.fecha_presentacion','',date('Y-m-d'),date("Y")-1,date("Y") + 1)?>
                        </td>
                </tr>		

                </table>
                


        </div>
    </div>
<?php //   debug($turnos)?>
<?php endif;?>

