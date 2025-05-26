
<?php 

    foreach ($datos as $valor)
    {   
        //debug($valor["GlobalDato"]["concepto_1"]);
        echo '<tr>';
        echo '<td style="vertical-align:middle;">';
        echo  $valor["GlobalDato"]["concepto_1"]; 
        echo '</td>';
        echo '<td><input type="file" name="data[ProveedorPlanDocumento]['. $valor["GlobalDato"]["id"] .'|'. $valor["GlobalDato"]["concepto_1"].']" id=ProveedorPlanDocumento'.$valor["GlobalDato"]["concepto_1"].'"/></td>';
        echo '</tr>'; 
    }
                           
?>