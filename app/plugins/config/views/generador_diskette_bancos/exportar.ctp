<?php echo $this->renderElement('head',array('title' => 'GENERADOR DE DISKETTES PARA COBRANZA POR BANCOS - DEBITO AUTOMATICO'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>


<h3>EXPORTAR DATOS :: GENERAR EL ARCHIVO DE DEBITO AUTOMATICO</h3>

<script language="Javascript" type="text/javascript">
Event.observe(window, 'load', function(){

	disableAll();
	
	var banco = $('GeneradorDisketteBancoBancoIntercambio').getValue();
	if(banco === '00011')BancoNacion();
	if(banco === '00020')BancoCordoba();
	if(banco === '00430')BancoStandar();

	$('GeneradorDisketteBancoBancoIntercambio').observe('change',function(){
		banco = $('GeneradorDisketteBancoBancoIntercambio').getValue();
		disableAll();
		if(banco === '00011')BancoNacion();
		if(banco === '00020')BancoCordoba();
		if(banco === '00430')BancoStandar();

	});
	
	
});

function disableAll(){

	$('LN_1').hide();
	$('LN_2').hide();
	$('LN_3').hide();
	$('LN_4').hide();
	$('LN_5').hide();
	$('LN_6').hide();
	$('LN_7').hide();
	$('LN_8').hide();
	$('LN_9').hide();
	$('LN_10').hide();
	$('LN_11').hide();
	$('LN_12').hide();
	
	$('GeneradorDisketteBancoNroCuit').disable();
	$('GeneradorDisketteBancoNroEmpresa').disable();
	$('GeneradorDisketteBancoPrestacion').disable();
	$('GeneradorDisketteBancoTipoCuentaBancoNacion').disable();
	$('GeneradorDisketteBancoSucursalBcoNacion').disable();
	$('GeneradorDisketteBancoCuentaBancoNacion').disable();
	$('GeneradorDisketteBancoMonedaCuentaBancoNacion').disable();
	$('GeneradorDisketteBancoFechaDebitoDay').disable();
	$('GeneradorDisketteBancoFechaDebitoMonth').disable();
	$('GeneradorDisketteBancoFechaDebitoYear').disable();
	$('GeneradorDisketteBancoArchivoSalida').disable();
	$('GeneradorDisketteBancoNroArchivoBancoNacion').disable();
	$('GeneradorDisketteBancoLoteBancoNacion').disable();
	$('GeneradorDisketteBancoArchivoDatos').disable();
}

function BancoNacion(){
	$('LN_4').show();
	$('LN_5').show();
	$('LN_6').show();
	$('LN_7').show();
	$('LN_8').show();
	$('LN_9').show();
	$('LN_10').show();
	$('LN_11').show();
	$('LN_12').show();	
	$('GeneradorDisketteBancoTipoCuentaBancoNacion').enable();
	$('GeneradorDisketteBancoSucursalBcoNacion').enable();
	$('GeneradorDisketteBancoCuentaBancoNacion').enable();
	$('GeneradorDisketteBancoMonedaCuentaBancoNacion').enable();
	$('GeneradorDisketteBancoFechaDebitoDay').enable();
	$('GeneradorDisketteBancoFechaDebitoMonth').enable();
	$('GeneradorDisketteBancoFechaDebitoYear').enable();

	$('GeneradorDisketteBancoArchivoSalida').enable();	
	$('GeneradorDisketteBancoNroArchivoBancoNacion').enable();
	$('GeneradorDisketteBancoLoteBancoNacion').enable();
	$('GeneradorDisketteBancoArchivoDatos').enable();
}

function BancoCordoba(){
	$('LN_2').show();
	$('LN_8').show();
//	$('LN_9').show();
	$('LN_12').show();
	$('GeneradorDisketteBancoNroEmpresa').enable();
	$('GeneradorDisketteBancoFechaDebitoDay').enable();
	$('GeneradorDisketteBancoFechaDebitoMonth').enable();
	$('GeneradorDisketteBancoFechaDebitoYear').enable();

	$('GeneradorDisketteBancoArchivoSalida').enable();	
	$('GeneradorDisketteBancoArchivoDatos').enable();	
}

function BancoStandar(){
	$('LN_1').show();
	$('LN_3').show();
	$('LN_7').show();
	$('LN_8').show();
	$('LN_9').show();
	$('LN_12').show();	
	$('GeneradorDisketteBancoNroCuit').enable();
	$('GeneradorDisketteBancoMonedaCuentaBancoNacion').enable();
	$('GeneradorDisketteBancoPrestacion').enable();
	$('GeneradorDisketteBancoFechaDebitoDay').enable();
	$('GeneradorDisketteBancoFechaDebitoMonth').enable();
	$('GeneradorDisketteBancoFechaDebitoYear').enable();
	$('GeneradorDisketteBancoArchivoSalida').enable();	
	$('GeneradorDisketteBancoArchivoDatos').enable();		
}


</script>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'exportar','type' => 'file'))?>
	
	<table class="tbl_form">
		
		<tr>
			<td>BANCO</td>
			<td><?php echo $this->requestAction('/config/bancos/combo/GeneradorDisketteBanco.banco_intercambio/0/0/5')?></td>
		</tr>
		<tr id="LN_1">
			<td>NRO.CUIT</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.nro_cuit',array('maxlength' => 11,'size' => 11))?></td>
		</tr>		
		<tr id="LN_2">
			<td>NRO.EMPRESA (SUMINISTADO POR EL BANCO)</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.nro_empresa',array('maxlength' => 4,'size' => 4))?></td>
		</tr>
		<tr id="LN_3">
			<td>PRESTACION</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.prestacion',array('maxlength' => 10,'size' => 10))?></td>
		</tr>		
		<tr id="LN_4">
			<td>TIPO CUENTA</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.tipo_cuenta_banco_nacion',array('type' => 'select','options' => array('10' => '10 - CUENTA CORRIENTE $','11'=>'11 - CUENTA CORRIENTE U$S','20' => '20 - CAJA DE AHORRO $','21' => '21 - CAJA DE AHORRO U$S','27' => '27 - CTA.CTE. ESPECIAL $','28' => '28 - CTA.CTE. ESPECIAL U$S')))?></td>
		</tr>		
		<tr id="LN_5">
			<td>SUCURSAL</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.sucursal_bco_nacion',array('maxlength' => 10,'size' => 10))?></td>
		</tr>
		<tr id="LN_6">
			<td>NRO.CUENTA</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.cuenta_banco_nacion',array('maxlength' => 20,'size' => 20))?></td>
		</tr>
		<tr id="LN_7">
			<td>MONEDA</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.moneda_cuenta_banco_nacion',array('type' => 'select','options' => array('P' => 'P - PESOS','D'=>'D - DOLARES')))?></td>
		</tr>								
		<tr id="LN_8">
			<td align="right">FECHA A DEBITAR / FECHA TOPE (AAAAMMDD)</td>
			<td colspan="2">
			<?php echo $frm->calendar('GeneradorDisketteBanco.fecha_debito',null,null,null,date('Y')+1)?>			
			<?php //   echo $frm->number('GeneradorDisketteBanco.fecha_debito',array('maxlength' => 8,'size' => 8))?></td>
		</tr>		
		<tr id="LN_9">
			<td>NOMBRE DEL ARCHIVO A GENERAR</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.archivo_salida',array('maxlength' => 30,'size' => 30))?></td>
		</tr>
		<tr id="LN_10">
			<td>NRO.DE ARCHIVO (1,2,3...,N)</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.nro_archivo_banco_nacion',array('maxlength' => 2,'size' => 2))?></td>
		</tr>
		<tr id="LN_11">
			<td>INDICADOR DE LOTE (EMP.BCO.NACION)</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.lote_banco_nacion',array('type' => 'select','options' => array('EMP' => 'EMP - CLIENTES DEL LOTE NO SON EMPLEADOS DEL BNA PERO PERCIBEN HABERES EN BNA','BNA'=>'BNA - TODOS LOS CLIENTES DEL LOTE SON EMPLEADOS DEL BNA','REE' => 'REE - AL MENOS UN (1) CLIENTE DEL LOTE NO PERCIBE HABERES EN BNA (CLIENTE COMUN)')))?></td>
		</tr>
        <tr id="LN_12">
            <td>ARCHIVO DE DATOS (EXCEL)</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            	<strong><?php echo $html->link("Formato de Archivo Excel","formatos",array('target' => 'blank'))?></strong>
            </td>
        </tr>						
		
	</table>
	<?php echo $frm->end("PROCESAR ARCHIVO")?>
</div>

<?php if(!empty($datos)):?>


	<table>
		
		<tr>
			<th colspan="10">REGISTROS LEIDOS</th>
		</tr>
		
		<tr>
			<th>#</th>
			<th>IDENTIFICADOR</th>
			<th>R</th>
			<th>COMPROBANTE</th>
			<th>SUCURSAL</th>
			<th>CUENTA</th>
			<th>CBU</th>
			<th>FECHA DEBITO</th>
			<th>IMPORTE</th>
			<th>STATUS</th>
			
		</tr>
		<?php $i = 0;?>
		<?php $ACUM = 0;?>
		<?php $ACUM_ERROR = $ACUM_OK = 0;?>
		<?php foreach($datos as $dato):?>
		
			<?php if($dato['error'] == 0)$ACUM += $dato['importe'];?>
			<?php if($dato['error'] == 1)$ACUM_ERROR++;?>
			<?php if($dato['error'] == 0)$ACUM_OK++;?>
			
			<?php $i++;?>
			
			<tr>
				<td align="center"><?php echo $i?></td>
				<td><?php echo $dato['identificador']?></td>
				<td><?php echo $dato['registro']?></td>
				<td align="center"><?php echo $dato['comprobante']?></td>
				<td align="center"><?php echo $dato['sucursal']?></td>
				<td align="center"><?php echo $dato['cuenta']?></td>
				<td align="center"><?php echo $dato['cbu']?></td>
				<td align="center"><?php echo $util->armaFecha($dato['fecha_debito'])?></td>
				<td align="right"><?php echo $util->nf($dato['importe'])?></td>
				<td align="center"><span style="color: <?php echo ($dato['error'] == 1 ? "red" : "green")?>;font-weight: bold;"><?php echo $dato['status']?></span></td>
			
			</tr>
			
			
		
		<?php endforeach;?>
		
		<tr class="totales">
			<th colspan="3" style="text-align: left;">
				LEIDOS: <?php echo $i?> | 
				<span style="color: green;">OK: <?php echo $ACUM_OK?></span> | 
				<?php echo ($ACUM_ERROR != 0 ? "<span style='color:red;'>ERRORES: $ACUM_ERROR</span>" : "")?>
			</th>
			<th colspan="3" style="text-align: left;">
				<?php if($ACUM_ERROR == 0):?>
					<?php echo $controles->botonGenerico('/config/generador_diskette_bancos/exportar/'.$UID,'controles/disk.png','DESCARGAR ARCHIVO',array('target' => 'blank','style' => 'color:black;'))?>
				<?php endif;?>
			</th>
			<th colspan="2">IMPORTE TOTAL (STATUS = OK)</th>
			<th><?php echo $util->nf($ACUM)?></th>
			<th></th>
		</tr>
	
	</table>
	
	<?php //    debug($UID);?>
	
	<?php if($ACUM_ERROR != 0):?>
		<div class='notices_error'>
			ATENCION:<br/>
			Se detectaron <strong><?php echo $ACUM_ERROR?></strong> error/es, deber&aacute; corregir los mismos en el archivo original y volver a 
			procesarlo nuevamente.
			<div style='clear:both;'></div>
		</div>
	<?php endif;?>	

<?php endif;?>
