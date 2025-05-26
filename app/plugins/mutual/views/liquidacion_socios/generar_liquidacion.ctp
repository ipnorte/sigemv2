<h3>GENERAR LIQUIDACION</h3>
<?php echo $frm->create(null,array('action' => 'generar_liquidacion/'. $socio_id,'id' => 'form_genera_liquidacion'))?>
<table class="tbl_form">
    <tr>
        <td>PERIODO (*)</td>
        <td><?php echo $this->renderElement("liquidacion/periodos_liquidados",array('plugin' => 'mutual', 'abiertos' => 1, 'imputados' => 0));?></td>
        <td><input type="submit" value="GENERAR LIQUIDACION"/></td>
    </tr>
    <tr>
        <td colspan="3">(*) Las liquidaciones que se muestran son aquellas que NO est&aacute;n cerradas!.</td>
    </tr>
</table>
    
<?php echo $frm->end();?>
