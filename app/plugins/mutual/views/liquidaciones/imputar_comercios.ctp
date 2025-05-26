<?php echo $this->renderElement('head',array('title' => 'PROCESO DE FACTURACION A PROVEEDORES'))?>
<?php 

    if($liquidacion['Liquidacion']['facturada'] === '1'){
            echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'));
    }
?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php 
    if($liquidacion['Liquidacion']['facturada'] === '1'){
        if(isset($factura)){
            if($factura == 'OK'){
                echo $this->renderElement('msg',array('msg' => array('OK' => 'EL PROCESO DE FACTURACION TERMINO CON EXITO')));
            }else{
                echo $this->renderElement('msg',array('msg' => array('ERROR' => 'ERROR EN EL PROCESO DE FACTURACION')));
            }
        }else{
            echo $this->renderElement('msg',array('msg' => array('NOTICE' => 'EL PROCESO DE FACTURACION YA FUE REALIZADO')));
        }
    }
?>

    <table>

        <tr>
            <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                <th colspan="21" style="text-align: left;">PROVEEDOR</th>
            <?php }else{?>
                <th colspan="15" style="text-align: left;">PROVEEDOR</th>
            <?php }?>
        </tr>

        <tr>
            <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                <th colspan="3">FECHA</th>
                <th colspan="3">FACTURA</th>
            <?php }?>
            <th colspan="3">PRODUCTO - CONCEPTO</th>
            <th colspan="3">IMPORTE PROVEEDOR</th>
            <th colspan="3">IMPORTE MUTUAL</th>
            <th colspan="3">IMPORTE A PAGAR</th>
            <th colspan="3"></th>
        </tr>

        <?php $PROVEEDOR = 0;?>
        <?php $PRIMERO = true;?>
        <?php $EGRESO = 0;?>
        <?php $INGRESO = 0;?>
        <?php $TOTAL = 0;?>

        <?php $TEGRESO = 0;?>
        <?php $TINGRESO = 0;?>
        <?php $TTOTAL = 0;?>
        <?php $tienePago = false;?>
        
        <?php foreach($proveedores as $dato){
            if($dato['pagos'] > 0) $tienePago = true;
            ?>


            <?php if($PROVEEDOR != $dato['proveedor_id']){?>

                <?php if($PRIMERO){?>
                        <?php $PRIMERO = false;?>
                <?php }else{?>

                        <tr class="totales">
                            <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                                <th colspan="6"></th>
                            <?php }?>
                            <th colspan="3">TOTAL</th>
                            <th colspan="3"><?php echo $util->nf($EGRESO)?></th>
                            <th colspan="3"><?php echo $util->nf($INGRESO)?></th>
                            <th colspan="3"><?php echo $util->nf($TOTAL)?></th>
                            <th colspan="3"></th>
                        </tr>

                        <tr>
                            <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                                <th colspan="3">FECHA</th>
                                <th colspan="3">FACTURA</th>
                            <?php }?>
                            <th colspan="3">PRODUCTO - CONCEPTO</th>
                            <th colspan="3">IMPORTE PROVEEDOR</th>
                            <th colspan="3">IMPORTE MUTUAL</th>
                            <th colspan="3">IMPORTE A PAGAR</th>
                            <th colspan="3"></th>
                        </tr>

                <?php }?>


                <?php $PROVEEDOR = trim($dato['proveedor_id']);?>
                <tr>
                    <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                        <td colspan="21" style="font-size:13px;background-color: #e2e6ea"><strong><?php echo $dato['razon_social']?></strong></td>
                    <?php }else{?>
                        <td colspan="15" style="font-size:13px;background-color: #e2e6ea"><strong><?php echo $dato['razon_social']?></strong></td>
                    <?php }?>
                </tr>
                <?php $EGRESO = 0;?>
                <?php $INGRESO = 0;?>
                <?php $TOTAL = 0;?>


            <?php }?>

            <?php 
                if($dato['tipo'] == 'E'){
                    if($dato['tipo_documento'] == 'FAC'){
                        $EGRESO += $dato['importe'];
                        $TOTAL += $dato['importe'];
                        $TEGRESO += $dato['importe'];
                        $TTOTAL += $dato['importe'];
                    }else{
                        $EGRESO -= $dato['importe'];
                        $TOTAL -= $dato['importe'];
                        $TEGRESO -= $dato['importe'];
                        $TTOTAL -= $dato['importe'];
                    }
                }else{
                    if($dato['tipo_documento'] == 'FA'){
                        $INGRESO += $dato['importe'];
                        $TOTAL -= $dato['importe'];
                        $TINGRESO += $dato['importe'];
                        $TTOTAL -= $dato['importe'];
                    }else{
                        $INGRESO -= $dato['importe'];
                        $TOTAL += $dato['importe'];
                        $TINGRESO -= $dato['importe'];
                        $TTOTAL += $dato['importe'];
                    }
                }
            ?>

            <tr>

                <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                    <td colspan="3"><?php echo date('d/m/Y',strtotime($dato['fecha']))?></td>
                    <td colspan="3"><?php echo $dato['comprobante']?></td>
                <?php }?>
                <td colspan="3"><strong><?php echo $dato['descripcion']?></td>
                <?php if($dato['tipo'] == 'E'){ 
                    $url = '/proveedores/orden_pagos/detalle_pago_facturas/' .$dato['proveedor_factura_id']?>
                    <td colspan="3" align="right"><?php echo $util->nf($dato['importe'] * ($dato['tipo_documento'] == 'FAC' ? 1 : -1))?></td>
                    <td colspan="3" align="right"></td>
                <?php }else{
                    $url = '/clientes/recibos/detalle_pago_facturas/' .$dato['cliente_factura_id']?>
                    <td colspan="3" align="right"></td>
                    <td colspan="3" align="right"><?php echo $util->nf($dato['importe'] * ($dato['tipo_documento'] == 'FA' ? 1 : -1))?></td>
                <?php } ?>

                <?php if($dato['pagos'] > 0){?>
                    <td colspan="3" align="right"><?php echo $controles->btnModalBox(array('title' => 'DETALLE DE PAGO','img'=> 'information.png','url' => $url,'h' => 450, 'w' => 750))?></td>
<!--                    <td colspan="3"><?php echo $controles->linkModalBox('',array('title' => 'DETALLE DE PAGO','url' => $url,'h' => 450, 'w' => 750))?></td>-->
                <?php }else{?>
                    <td colspan="3" align="right"></td>
                <?php }?>
            </tr>			

        <?php }?>	

        <tr class="totales">
            <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                <th colspan="6"></th>
            <?php }?>
            <th colspan="3">TOTAL</th>
            <th colspan="3"><?php echo $util->nf($EGRESO)?></th>
            <th colspan="3"><?php echo $util->nf($INGRESO)?></th>
            <th colspan="3"><?php echo $util->nf($TOTAL)?></th>
            <th colspan="3"></th>
        </tr>

        <tr class="totales">
            <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
                <th colspan="6"></th>
            <?php }?>
            <th colspan="3">TOTAL GENERAL</th>
            <th colspan="3"><?php echo $util->nf($TEGRESO)?></th>
            <th colspan="3"><?php echo $util->nf($TINGRESO)?></th>
            <th colspan="3"><?php echo $util->nf($TTOTAL)?></th>
            <th colspan="3"></th>
        </tr>

    </table>	


    <?php if($tienePago){
        echo $this->renderElement('msg',array('msg' => array('OK' => 'NO SE PUEDEN ANULAR YA QUE EXISTEN PAGOS EN LAS FACTURAS')));?>
        <div class="row">
            <?php echo $controles->btnRew('Regresar','/mutual/liquidaciones/reporte_proveedores/' . $liquidacion['Liquidacion']['id'] . '/?pid=' . $PID)?>
        </div>
    <?php }else{?>
        <?php if($liquidacion['Liquidacion']['facturada'] === '1'){
            $action = "anular_facturas/";
        }else{
            $action = "imputar_comercios/";
        }?>

        <?php echo $frm->create(null,array('name'=>'formImputarComercios','id'=>'formImputarComercios','onsubmit' => "", 'action' => $action . $liquidacion['Liquidacion']['id'] . '/' . $PID ));?>
        <?php echo $frm->hidden('id', array('value' => $liquidacion['Liquidacion']['id'])); ?>
        <?php echo $frm->hidden('pid', array('value' => $PID)) ?>
        <?php if($liquidacion['Liquidacion']['facturada'] === '1'){?>
            <?php echo $frm->hidden('anular', array('value' => 1)) ?>
            <?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ANULAR FACTURAS','URL' => '/mutual/liquidaciones/reporte_proveedores/' . $liquidacion['Liquidacion']['id'] . '/?pid=' . $PID))?>
        <?php }else{?>
            <?php echo $frm->hidden('anular', array('value' => 0)) ?>
            <?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR FACTURAS','URL' => '/mutual/liquidaciones/reporte_proveedores/' . $liquidacion['Liquidacion']['id'] . '/?pid=' . $PID))?>
        <?php }?>
    <?php }?>
<?php // endif; ?>