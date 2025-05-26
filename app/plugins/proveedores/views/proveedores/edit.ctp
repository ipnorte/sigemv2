<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $this->data))?>
<?php



$checkedA = '';
$checkedB = '';
if($this->data['Proveedor']['tipo_saldo']==0) $checkedA = 'checked';
else $checkedB = 'checked';

$disabledSaldo = '';
if($this->data['Proveedor']['estado']!='A') $disabledSaldo = 'disabled';

?>

<script type="text/javascript">
Event.observe(window, 'load', function(){
   <?php echo ($this->data['Proveedor']['pagare_blank'] ? " $('ProveedorDireccionPagare').disable() " : " $('ProveedorDireccionPagare').enable(); ")?>
})

function proveedorPagareBlankOnClick(oChk){
    if(oChk.checked){
        $('ProveedorDireccionPagare').disable();
    }else{
        $('ProveedorDireccionPagare').enable();
    }

}

</script>


<h3>DATOS DEL PROVEEDOR</h3>
<?php echo $form->create(null,array('name'=>'formEditProveedor','id'=>'formEditProveedor','onsubmit' => ""));?>
<div class="areaDatoForm">
            <table class="tbl_form">
		<tr>
                    <td>CUIT</td>
                    <td><?php echo $frm->input('Proveedor.cuit',array('label'=>'','size'=>'15','maxlength'=>'11','disabled'=>'disabled'));?></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>RAZON SOCIAL</td>	
                    <td><?php echo $frm->input('Proveedor.razon_social',array('label'=>'','size'=>60,'maxlength'=>100)); ?></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>RAZON SOCIAL RESUMIDA</td>	
                    <td><?php echo $frm->input('Proveedor.razon_social_resumida',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
                    <td>TIPO PERSONA</td>
                    <td><?php echo $frm->input('Proveedor.tipo_persona',array('type' => 'select','options' => array('1' => 'FISICA','2' => 'IDEAL O JURIDICA'),'selected' => (isset($this->data['Proveedor']['tipo_persona']) ? $this->data['Proveedor']['tipo_persona'] : 2))); ?></td>
                    <td colspan="5"></td>
		</tr>
            </table>
            <hr>
            <table class="tbl_form">
		<tr>
			<td>CALLE</td>	
			<td><?php echo $frm->input('Proveedor.calle',array('label'=>'','size'=>40,'maxlength'=>100)); ?></td>
			<td>NUMERO</td>	
			<td><?php echo $frm->number('Proveedor.numero_calle',array('label'=>'')); ?></td>
			<td>PISO</td>	
			<td><?php echo $frm->input('Proveedor.piso',array('label'=>'','size'=>3,'maxlength'=>3)); ?></td>
			<td>DPTO</td>	
			<td><?php echo $frm->input('Proveedor.dpto',array('label'=>'','size'=>3,'maxlength'=>3)); ?></td>
		</tr>
		<tr>
                    <td>BARRIO</td>	
                    <td><?php echo $frm->input('Proveedor.barrio',array('label'=>'','size'=>50,'maxlength'=>100)); ?></td>
                    <td>LOCALIDAD</td>	
                    <td><?php echo $frm->input('Proveedor.localidad',array('label'=>'','size'=>30,'maxlength'=>100)); ?></td>
                    <td>C.P.</td>	
                    <td><?php echo $frm->input('Proveedor.codigo_postal',array('label'=>'','size'=>11,'maxlength'=>100)); ?></td>
                    <td colspan="2"></td>
		</tr>
		<tr>
                    <td>TELEFONO FIJO</td>	
                    <td><?php echo $frm->input('Proveedor.telefono_fijo',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
                    <td>TELEFONO MOVIL</td>	
                    <td><?php echo $frm->input('Proveedor.telefono_movil',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
                    <td>FAX</td>
                    <td><?php echo $frm->input('Proveedor.fax', array('label' => '', 'size' => 20, 'maxlength' => 50)); ?></td>
                    <td colspan="2"></td>
		</tr>
		<tr>
                    <td>EMAIL</td>	
                    <td><?php echo $frm->input('Proveedor.email',array('label'=>'','size'=>30,'maxlength'=>50)); ?></td>
                    <td colspan="6"></td>
		</tr>
            </table>
            <hr>
            <table class="tbl_form">
		<tr>
                    <td>CONDICION ANTE I.V.A.</td>	
                    <td>
                        <?php echo $this->renderElement('global_datos/combo',array(
                            'plugin'=>'config',
                            'label' => " ",
                            'model' => 'Proveedor.condicion_iva',
                            'prefijo' => 'PERSXXTI',
                            'disable' => false,
                            'empty' => false,
                            'selected' => (!empty($this->data['Proveedor']['condicion_iva']) ? $this->data['Proveedor']['condicion_iva'] : '0'),
                            'logico' => true,
                        ))?>			
                    </td>
                    <td>TIPO PROVEEDOR</td>	
                    <td><?php echo $frm->comboTipoProveedor($this->data['Proveedor']['tipo_proveedor']); ?></td>
                    <td>CONCEPTO DEL GASTO</td>
                    <td><?php echo $this->renderElement('global_datos/combo_global',array(
                            'plugin'=>'config',
                            'label' => "",
                            'model' => 'Proveedor.concepto_gasto',
                            'disabled' => false,
                            'empty' => false,
                            'selected' => $this->data['Proveedor']['concepto_gasto'],
                            'metodo' => 'get_concepto_gasto'
                        ))?>				
                    </td>
                    <td colspan="2"></td>
		</tr>
		<tr>
                    <td>TIPO ASIENTO</td>
                    <td><?php echo $this->renderElement('tipo_asiento/combo_tipo_asiento',array(
                            'plugin'=>'proveedores',
                            'label' => "",
                            'model' => 'Proveedor.proveedor_tipo_asiento_id',
                            'disabled' => false,
                            'empty' => false,
                            'selected' => $this->data['Proveedor']['proveedor_tipo_asiento_id']
                        ))?>
                    </td>			
                    <td>IMP.CTA.CONTABLE</td>
                    <td colspan="5"><?php echo $this->renderElement('combo_plan_cuenta',array(
                            'plugin'=>'contabilidad',
                            'label' => "",
                            'model' => 'Proveedor.co_plan_cuenta_id',
                            'disabled' => false,
                            'empty' => false,
                            'selected' => $this->data['Proveedor']['co_plan_cuenta_id']
                        ))?>
                    </td>			
		</tr>
		<tr>
                    <td>CHEQUE / RESPONSABLE</td>	
                    <td><?php echo $frm->input('Proveedor.responsable',array('label'=>'','size'=>60,'maxlength'=>100)); ?></td>
        </tr>
        <tr>                
                    <td>CONTACTO</td>	
                    <td colspan="5"><?php echo $frm->input('Proveedor.contacto',array('label'=>'','size'=>60,'maxlength'=>100)); ?></td>
		</tr>
        <tr>
            <td>DIRECCION PAGARE</td>
            <td colspan="7"><?php echo $frm->input('Proveedor.direccion_pagare',array('label'=>'','size'=>80,'maxlength'=>100)); ?></td>
            
        </tr>
        <tr>
        <td>PAGARE LIMPIO</td>
        <td><?php echo $frm->input('Proveedor.pagare_blank',array('label'=>'','onclick' => 'proveedorPagareBlankOnClick(this)')) ?></td>
        </tr>
            </table>
            <hr>
            <table class="tbl_form">
		<tr>
                    <td>SALDO INICIALES AL:</td>
                    <td><?php echo $frm->input('Proveedor.fecha_saldo',array('label' => '', 'dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1, 'disabled' => $disabledSaldo))?></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>IMPORTE:</td>
                    <td><div class="input text"><label for="ProveedorImporteSaldo"></label><input name="data[Proveedor][importe_saldo]" type="text" value="<?php echo $this->data['Proveedor']['importe_saldo'] ?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" id="ProveedorImporteSaldo" <?php echo $disabledSaldo?>/></div></td>
                    <!-- <td><?php echo $frm->money('Proveedor.importe_saldo','',$this->data['Proveedor']['importe_saldo']) ?></td> -->
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>SALDO:</td>
                    <td></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td></td>
                    <td><input type="radio" name="data[Proveedor][tipo_saldo]" id="ProveedorAccion_a" value="0" <?php echo $checkedA?> <?php echo $disabledSaldo?>/>DEUDOR</td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td></td>
                    <td><input type="radio" name="data[Proveedor][tipo_saldo]" id="ProveedorAccion_b" value="1" <?php echo $checkedB?> <?php echo $disabledSaldo?>/>ACREEDOR</td>
                    <td colspan="6"></td>
		</tr>
            </table>
            <hr>
            <table class="tbl_form">
		<tr>
                    <td>ACTIVO</td>	
                    <td><?php echo $frm->input('Proveedor.activo',array('label'=>'')) ?></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>REASIGNABLE</td>	
                    <td><?php echo $frm->input('Proveedor.reasignable',array('label'=>'')) ?></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>HABILITADO PARA VENDEDORES</td>	
                    <td><?php echo $frm->input('Proveedor.vendedores',array('label'=>'')) ?></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>LIQUIDA PRESTAMO:</td>	
                    <td><?php echo $frm->input('Proveedor.liquida_prestamo',array('label'=>'')) ?></td>
                    <td colspan="6"></td>
		</tr>
		<tr>
                    <td>EMP. COBRANZA:</td>	
                    <td><?php echo $frm->input('Proveedor.empcob',array('label'=>'')) ?></td>
                    <td colspan="6"></td>
		</tr> 
        <?php 
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        if(isset($INI_FILE['general']['cuota_social_permanente']) && $INI_FILE['general']['cuota_social_permanente'] == 0):
        ?>
        
		<tr>
                    <td>GENERAR CUOTA SOCIAL</td>	
                    <td><?php echo $frm->input('Proveedor.genera_cuota_social',array('label'=>'')) ?></td>
                    <td colspan="6"></td>
		</tr>
        <?php endif;?>       
        <tr>
            <td>PIN WEB SERVICE</td>
            <td>
                <?php echo $frm->input('Proveedor.codigo_acceso_ws',array('size'=>12,'maxlength'=>12)); ?>
                <?php echo $html->image('controles/arrow_refresh.png',array('border' => 0, 'id' => 'arrow_refresh' ,'style' => 'cursor: pointer;','onclick' => 'regeneratePIN()'))?>
                <?php echo $html->image('controles/ajax-loader.gif',array("border"=>"0",'id' => "ajax_loader_ws", 'style' => "display: none;"));?>
            </td>
            <td colspan="6"></td>
        </tr>    
        </table>
        <hr>
</div>
    <script type="text/javascript">
        Event.observe(window, 'load', function(){
            //genPIN();
            <?php 
                if(empty($this->data['Proveedor']['codigo_acceso_ws'])){
                    echo "genPIN();";
                }
            ?>
        });
        function regeneratePIN(){
            if(confirm("Generar un nuevo PIN de acceso para el WebService?.\nEl nuevo PIN será confirmado una vez guardado los cambios.")){
               genPIN();
            }
        }
        function genPIN(){
            $('ajax_loader_ws').hide();
            var url = '<?php echo $this->base?>/proveedores/proveedores/genwspin/<?php echo $this->data['Proveedor']['id']?>';
            new Ajax.Updater('ProveedorCodigoAccesoWs',
                    url, 
                    {
                        asynchronous:true, 
                        evalScripts:true, 
                        onComplete:function(request) {
                            $('ajax_loader_ws').hide();
                            responseValue = request.responseText;
                            $('arrow_refresh').show();
                            document.getElementById('ProveedorCodigoAccesoWs').value = responseValue;
                        },
                        onLoading:function() {
                            $('arrow_refresh').hide();
                            $('ajax_loader_ws').show();
                        }, 
                        requestHeaders:['X-Update', 'ProveedorCodigoAccesoWs']
                    }
            );             
        }
    </script>    
<?php echo $frm->hidden('Proveedor.proveedor_factura_id', array('value' => $this->data['Proveedor']['proveedor_factura_id'])); ?>
<?php echo $frm->hidden('Proveedor.estado', array('value' => $this->data['Proveedor']['estado'])); ?>
<?php echo $frm->hidden('Proveedor.id'); ?>
<?php echo $frm->submit('GUARDAR')?>
<?php echo $form->end();?>

    <?php // debug($this->data)?>