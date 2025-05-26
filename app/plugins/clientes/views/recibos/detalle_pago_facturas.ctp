<?php

?>
<div class="areaDatoForm3">
    <table>
        <tr>
            <th colspan="12" style="text-align: left;">F A C T U R A</th>
        </tr>

        <tr>
            <td colspan="4" style="font-size:10px;background-color: #e2e6ea"><?php echo date('d/m/Y',strtotime($aClntFct['fecha_comprobante']))?></td>
            <td colspan="4" style="font-size:10px;background-color: #e2e6ea"><?php echo $aClntFct['tipo_comprobante_desc']?></td>
            <td colspan="4" style="font-size:10px;background-color: #e2e6ea" align="right"><?php echo $util->nf($aClntFct['total_comprobante'])?></td>
        </tr>
        
        <tr>
            <td colspan="4" align="right"></td>
            <td colspan="4" align="right">SALDO DE FACTURA</td>
            <td colspan="4" align="right"><?php echo $util->nf($aClntFct['saldo'])?></td>
        </tr>
        
        <tr>
            <th colspan="12" style="text-align: left;">DETALLE PAGO DE FACTURA</th>
        </tr>

        <?php
            foreach($aDetalleFactura as $dato){?>
            <tr>
            <?php
                if($dato[0]['recibo_id'] > 0){?>
                    <td colspan="3" align="right"><?php echo date('d/m/Y', strtotime($dato[0]['rec_fecha']))?></td>
                    <td colspan="3" align="right">COBRADO CON REC NRO.</td>
                    <td colspan="3" align="right"><?php echo $dato[0]['documento']?></td>
                    <td colspan="3" align="right"><?php echo $util->nf($dato[0]['importe'])?></td>
                    
                <?php
                }else{
                    if($dato[0]['recibo_detalle_id'] > 0){?>
                        <td colspan="3" align="right"><?php echo date('d/m/Y', strtotime($dato[0]['fecha']))?></td>
                        <td colspan="3" align="right">COMPENSA CON REC NRO.</td>
                        <td colspan="3" align="right"><?php echo $dato[0]['documento']?></td>
                        <td colspan="3" align="right"><?php echo $util->nf($dato[0]['importe'])?></td>
                    
                <?php
                    }else{?>
                        <td colspan="3" align="right"><?php echo date('d/m/Y', strtotime($dato[0]['fecha']))?></td>
                        <td colspan="3" align="right">COMPENSA CON N.CREDITO</td>
                        <td colspan="3" align="right"><?php echo $dato[0]['documento']?></td>
                        <td colspan="3" align="right"><?php echo $util->nf($dato[0]['importe'])?></td>
                <?php
                    }
                }
                ?>
            </tr>
                
        <?php
            }
        ?>

        <tr>
            <th colspan="6" align="right"></th>
            <th colspan="3" align="right">TOTAL PAGADO</th>
            <th colspan="3" align="right"><?php echo $util->nf($aClntFct['pagos'])?></th>
        </tr>
    </table>	
</div>