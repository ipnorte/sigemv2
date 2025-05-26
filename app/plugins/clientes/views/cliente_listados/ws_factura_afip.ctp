<?php
// debug($aClntFct);
// debug($aCliente);
    
?>
<div class="areaDatoForm">
    <table>
        <caption>FACTURA GENERADA CORRECTAMENTE</caption>

        <tr>
            <th style="font-size:14px;background-color: #e2e6ea">C&oacute;digo Cbte.</th>
            <th style="font-size:14px;background-color: #e2e6ea">Pto. Vta.</th>
            <th style="font-size:14px;background-color: #e2e6ea">Nro. Cbte.</th>
            <th style="font-size:14px;background-color: #e2e6ea">C&oacute;digo Aut.(CAE)</th>
            <th style="font-size:14px;background-color: #e2e6ea">Fecha Vencimiento</th>
        </tr>
        
        <tr>
            <td style="font-size:14px" align="right"><?php echo $aClntFct['Afip_CbteTipo']?></td>
            <td style="font-size:14px" align="right"><?php echo $aClntFct['Afip_PtoVta']?></td>
            <td style="font-size:14px" align="right"><?php echo $aClntFct['Afip_NroCbte']?></td>
            <td style="font-size:14px" align="right"><?php echo $aClntFct['Afip_CodAutorizacion']?></td>
            <td style="font-size:14px" align="right"><?php echo date('d/m/Y', strtotime($aClntFct['Afip_FchVto']))?></td>
        </tr>
        
    </table>	

</div>

<?php echo $controles->botonGenerico('/clientes/cliente_listados/imprimir_factura_afip/'.$aClntFct['id'],'controles/pdf.png')?>
