<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: IMPORTAR DATOS','plugin' => 'config'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda_new',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<div class="areaDatoForm" id="formFileUpContainer">
    <h3>SUBIR ARCHIVO AL SERVIDOR</h3>
    <?php echo $frm->create(null,array('action'=>'importar_nuevo/'.$liquidacion['Liquidacion']['id'],'type' => 'file','id' => 'formUpLoadFile', "onsubmit" => "return validateForm();"))?>
    <table class="tbl_form">

                    <?php if($liquidacion['Liquidacion']['mostar_bancos'] == 1):?>
                            <tr>
                            <td align="right">BANCO EMISOR</td>
                            <td>
                                    <?php echo $this->renderElement('banco/combo_global', array(

                                            'plugin' => 'config',
                                            'model' => 'LiquidacionIntercambio.banco_id',
                                            'tipo' => 5,
                                            'empty' => false,
                                            'selected' => (isset($this->data['LiquidacionIntercambio']['banco_id']) ? $this->data['LiquidacionIntercambio']['banco_id'] : "")

                                    ))?>				
                            </td>
                            </tr>
                    <?php endif;?>


            <tr><td>ARCHIVO</td><td><div class="input select"><label for="LiquidacionIntercambioArchivo"></label><?php echo $frm->file('LiquidacionIntercambio.archivo',array('size' => 25))?></div></td></tr>		
            <tr><td colspan="2">OBSERVACIONES</td></tr>
            <tr>
                    <td colspan="2"><?php echo $frm->textarea('LiquidacionIntercambio.observaciones',array('cols' => 60, 'rows' => 10))?></td>
            </tr>
            <tr>
            <tr>
                    <td align="center"><?php echo $frm->submit("SUBIR ARCHIVO",array('id' => 'btn_fileUp'))?></td>
                    <td>
        <div class="submit"><input type="submit" name="data[LiquidacionIntercambio][subdividir]" value="*** SUBDIVIDIR ARCHIVO POR LIQUIDACIONES ***"/></div>                    
                            <?php echo $frm->btnForm(array('URL' => '/mutual/liquidaciones/importar_generar_lote/' . $liquidacion['Liquidacion']['id'].'/00011','LABEL' => 'BANCO NACION ** GENERAR LOTE DE RENDICION **'))?>
                            <?php echo $frm->btnForm(array('URL' => '/mutual/liquidaciones/importar_generar_lote/' . $liquidacion['Liquidacion']['id'].'/99999','LABEL' => 'MUTUAL ** GENERAR LOTE DE RENDICION **'))?>
                    </td>
            </tr>
    </table>
    <?php echo $frm->hidden('LiquidacionIntercambio.liquidacion_id', array('value' => $liquidacion['Liquidacion']['id']))?>
    <?php echo $frm->hidden('LiquidacionIntercambio.periodo', array('value' => $liquidacion['Liquidacion']['periodo']))?>
    <?php echo $frm->hidden('LiquidacionIntercambio.codigo_organismo', array('value' => $liquidacion['Liquidacion']['codigo_organismo']))?>

    <?php echo $frm->end();?>
</div>
