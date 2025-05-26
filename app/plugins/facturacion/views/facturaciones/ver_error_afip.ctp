<?php echo $this->renderElement('head',array('title' => 'ERROR EN LA FACTURACION','plugin' => 'config'))

// debug($aFactura)

?>

<table class="tbl_form">
	<col width="200" />
        
        <tr>
            <td>ORDEN DESCUENTO COBRO:</td>
            <td><h3><?php echo $aFactura['Factura']['orden_descuento_cobro_id']?></h3></td>
        </tr>
        <tr>
            <td>NUMERO DE DOCUMENTO:</td>
            <td><?php echo $aFactura['Factura']['numero_dni']?></td>
        </tr>
        <tr>
            <td>NOMBRE Y APELLIDO:</td>
            <td><?php echo $aFactura['Factura']['nom_apel']?></td>
        </tr>
        <tr>
            <td>C.U.I.T.:</td>
            <td><?php echo $aFactura['Factura']['numero_documento']?></td>
        </tr>
        <tr>
            <td>IMPORTE NO GRAVADO:</td>
            <td align="right"><?php echo $aFactura['Factura']['importe_total_concepto']?></td>
        </tr>
        <tr>
            <td>IMPORTE GRAVADO:</td>
            <td align="right"><?php echo $aFactura['Factura']['importe_neto']?></td>
        </tr>
        <tr>
            <td>IMPORTE I.V.A.:</td>
            <td align="right"><?php echo $aFactura['Factura']['importe_iva']?></td>
        </tr>
        <tr>
            <td>IMPORTE TOTAL:</td>
            <td align="right"><?php echo $aFactura['Factura']['importe_total']?></td>
        </tr>
        <tr>
            <td><h3>ERROR:</h3></td>
            <td><h3><?php echo $aFactura['Factura']['e_mensaje']?></h3></td>
        </tr>
</table>
