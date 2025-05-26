<div class="areaDatoForm">
<h3>REVERSAR PAGOS</h3> 
<hr/>
    <?php echo $frm->create(null,array('action' => 'reversar_cobros_masivo/'. $UID,'type' => 'file'))?>
	<table class="tbl_form">
            <tr>
                <td>BANCO</td>
                <td><?php echo $this->requestAction('/config/bancos/combo/reversos.banco_intercambio/0/0/5')?></td>
                <td># LIQ</td>
                <td><?php echo $frm->number('reversos.liquidacion',array('maxlength' => 4,'size' => 4))?></td>
            </tr>
        <tr>
            <td>ARCHIVO</td>
            <td colspan="2">
            	<input type="file" name="data[reversos][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            </td>
            <td>
                <input type="submit" value="PREVISUALIZAR" />
            </td>
        </tr>
	</table>
<?php echo $frm->end()?>

</div>

<?php if(!empty($listadoReverso)):?>

<?php if($sinCoincidencia): ?>

<div class="areaDatoForm2">
    <h4>Sin Coincidencia para liquidacion, socio y monto reversado con pagos registrados</h4>
    <br>
    <table>
        <tr>
        <th>LIQ</th>
        <th>ORGANISMO</th>
        <th>SOCIO</th>
        <th>DOCUMENTO</th>
        <th>APELLIDO Y NOMBRE</th>
        <th>DEBITADO</th>
        <th>COBRO#</th>
        <th>TIPO</th>
        <th>FECHA</th>
        <th>IMPORTE</th>
        <th>REV</th>
        </tr>
        <?php foreach ($listadoReverso as $n => $reverso):?>
        <?php if(empty($reverso['datos']) && !empty($reverso['sin_info'])):?>
            <tr>
                <tr>
                    <td style="text-align: center;"><?php echo $reverso['sin_info']['ls']['liquidacion_id']?></td>
                    <td><?php echo $reverso['sin_info']['org']['organismo']?></td>
                    <td style="text-align: center;"><?php echo $reverso['sin_info']['ls']['socio_id']?></td>
                    <td style="text-align: center;"><?php echo $reverso['sin_info']['ls']['documento']?></td>
                    <td><?php echo $controles->openWindow($reverso['sin_info']['ls']['apenom'],'/mutual/orden_descuento_cobros/by_socio/'.$reverso['sin_info']['ls']['socio_id'])?></td>
                    <td style="text-align: right;"><?php echo $util->nf($reverso['sin_info'][0]['importe_debitado'])?></td>
                    <td style="font-weight: bold;"></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;"></td>
                    <td style="text-align: center;"></td>

                </tr>            
            </tr>
        <?php endif; ?>
        <?php endforeach;?>
    </table>
</div>
<?php endif; ?>

<table>
    <tr><th colspan="11">Coincidencia para liquidacion, socio y monto reversado con pagos registrados</th></tr>
    <tr>
        <th>LIQ</th>
        <th>ORGANISMO</th>
        <th>SOCIO</th>
        <th>DOCUMENTO</th>
        <th>APELLIDO Y NOMBRE</th>
        <th>DEBITADO</th>
        <th>COBRO#</th>
        <th>TIPO</th>
        <th>FECHA</th>
        <th>IMPORTE</th>
        <th>REV</th>
    </tr>
    <?php $TOTAL = 0;?>
    <?php foreach ($listadoReverso as $n => $reverso):?>
    <?php if(!empty($reverso['datos'])):?>
        <tr class="<?php echo ($reverso['datos'][0]['cuotas'] == 0 ? "activo_0" : "activo_1")?>">
            <td style="text-align: center;"><?php echo $reverso['datos']['lc']['liquidacion_id']?></td>
            <td><?php echo $reverso['datos']['org']['organismo']?></td>
            <td style="text-align: center;"><?php echo $reverso['datos']['lc']['socio_id']?></td>
            <td style="text-align: center;"><?php echo $reverso['datos']['p']['documento']?></td>
            <td><?php echo $controles->openWindow($reverso['datos'][0]['apenom'], '/mutual/orden_descuento_cobros/by_socio/' . $reverso['datos']['lc']['socio_id'])?></td>
            <td style="text-align: right;"><?php echo $util->nf($reverso['datos'][0]['importe'])?></td>
            <td style="font-weight: bold;"><?php echo $controles->linkModalBox($reverso['datos']['co']['cobro'],array('title' => 'ORDEN DE COBRO #' . $reverso['datos']['co']['cobro'],'url' => '/mutual/orden_descuento_cobros/view/'.$reverso['datos']['co']['cobro'],'h' => 450, 'w' => 750))?></td>
            <td><?php echo $reverso['datos']['tco']['tipo_cobro']?></td>
            <td><?php echo $reverso['datos']['co']['fecha']?></td>
            <td style="text-align: right; font-weight: bold;"><?php echo $util->nf($reverso['datos'][0]['importe'])?></td>
            <td style="text-align: center;"><?php echo ( $reverso['datos'][0]['cuotas'] == 0 ? "<span style='background-color:red;color:white;padding:2px;font-weight:bold;'>R</span>" : "") ?></td>

        </tr>
        <?php $TOTAL += $reverso['datos'][0]['importe'];?>
    <?php endif; ?>
    <?php endforeach;?>
        <tr>
            <th class="totales" colspan="9">TOTAL REVERSADO</th>
            <th class="totales"><?php echo $util->nf($TOTAL)?></th>
            <th class="totales"></th>
        </tr>
</table>
    <?php if($paraReversar):?>
    <?php echo $frm->create(null,array('action' => 'reversar_cobros_masivo_process', 'onSubmit' => ' return confirm("REVERSAR COBROS?")'))?>
    <div class="areaDatoForm">
        <h3>DATOS DEL REVERSO</h3> 

    <table class="tbl_form">
    <tr>
            <td>FECHA DE NOVEDAD</td><td><strong><?php echo date('d-m-Y')?></strong></td>
    </tr>
    <tr>
            <td>PERIODO A INFORMAR AL PROVEEDOR</td>
            <td>
    <?php //   echo $frm->periodo('OrdenDescuentoCobroCuota.periodo_proveedor_reverso','',null,date('Y')-1,date('Y'))?>
    <?php echo $this->renderElement("liquidacion/periodos_liquidados",array('plugin' => 'mutual', 'facturados' => false))?>
    </td>
    </tr>

    <tr>
            <td colspan="2"><input type="submit" value = "REVERSAR" id = "btn_submit"/></td>
    </tr>


    </table>    

    </div>
    <?php echo $frm->hidden('OrdenDescuentoCobro.uuid',array('value' => $UID))?>
    <?php echo $frm->end();?>
    <?php endif; ?>
<?php endif; ?>


<?php // debug($listadoReverso)?>