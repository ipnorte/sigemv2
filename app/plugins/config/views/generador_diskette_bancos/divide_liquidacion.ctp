<?php echo $this->renderElement('head',array('title' => 'DIVIDE ARCHIVOS POR LIQUIDACION'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>
<div class="areaDatoForm">
    <?php echo $frm->create(null,array('action' => 'divide_liquidacion','type' => 'file'))?> 
    
    <table class="tbl_form">
		<tr>
			<td>BANCO</td>
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
                <tr><td>ARCHIVO</td><td><div class="input select"><label for="LiquidacionIntercambioArchivo"></label><?php echo $frm->file('LiquidacionIntercambio.archivo',array('size' => 25))?></div></td></tr>
    </table>
    <?php echo $frm->end("*** SUBDIVIDIR ARCHIVO POR LIQUIDACIONES ***")?>
</div>

<?php if(!empty($files)):?>


    <div class="areaDatoForm3">
        <h3>SUBDIVISION DEL LOTE :: DETALLE DE ARCHIVOS GENERADOS</h3>
        
        <table>
            <tr>
                <th></th>
                <th>LIQ</th>
                <th>ARCHIVO</th>
                <th>REGISTROS</th>
            </tr>
            <?php $registros = 0;?>
            <?php foreach($files as $lid => $file):?>
            <?php $registros += $file['lineas'];?>
            <tr>    
                <td><?php echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'].'/'.$file['uuid'],'controles/disk.png','',array('target' => '_blank'))?></td>
                <td style="font-weight: bold;"><?php echo $lid?></td>
                <td><?php echo $file['archivo']?></td>
                <td style="text-align: center;"><?php echo $file['lineas']?></td>
            </tr>
            <?php endforeach;?>
            <tr class="subtotales">
                <th colspan="3">Total Registros Le&iacute;dos</th>
                <th><?php echo $registros?></th>
            </tr>    
        </table>    
        
            <?php //   debug($files)?>
        
        
    </div>

<?php endif; ?>
