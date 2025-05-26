<?php ?>
<h3><?php echo $util->globalDato($socio['Persona']['tipo_documento'])." ".$socio['Persona']['documento']." - ".$socio['Persona']['apellido'].", ".$socio['Persona']['nombre']?></h3>
<?php echo $this->requestAction('/pfyj/socios/view/'.$socio['Socio']['id'])?>
<?php echo $form->create(null,array('name'=>'formModificaCuotaSocial','id'=>'formModificaCuotaSocial','onsubmit' => "return confirmarModificaCuotaForm()",'action' => 'modificar_cuotasocial/'. $socio['Socio']['id']));?>
<div class="areaDatoForm">
    <h4>Valor particular de Cuota Social</h4>
    <hr>
    <table class="tbl_form">
        <tr>
            <td>IMPORTE MENSUAL</td>
            <td><?php echo $frm->money('Socio.importe_cuota_social','',(isset($socio['Socio']['importe_cuota_social']) ? $socio['Socio']['importe_cuota_social'] : 0))?>
            </td>
            <td>
                <input type="checkbox" id="periodo_hasta_checkbox" onclick="togglePeriodoHasta()"> Periodo Hasta
            </td>
            <td id="periodo_hasta_td" style="display:none;">
                <?php echo $frm->periodo('Socio.periodo_hasta_importe_cuota_social','',$periodo,date('Y'),date('Y') + 10)?>
            </td>            
        </tr>
    </table>
</div>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ACTUALIZAR IMPORTE','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$socio['Persona']['id'] : $fwrd) ))?>

<script type="text/javascript">

</script>
