<?php echo $this->renderElement('head',array('title' => 'GENERADOR DE DISKETTES PARA COBRANZA POR BANCOS - DEBITO AUTOMATICO'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>



<h3>EXPORTAR DATOS :: GENERAR EL ARCHIVO DE DEBITO AUTOMATICO</h3>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'index','type' => 'file'))?>
	
	<table class="tbl_form">
		
		<tr>
		
			<td>BANCO</td>
			<td><?php echo $this->requestAction('/config/bancos/combo/GeneradorDisketteBanco.banco_intercambio/0/0/5')?></td>
			
		</tr>
		<tr>
			<td>NRO.CUIT</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.nro_cuit',array('maxlength' => 11,'size' => 11))?></td>
		</tr>		
		<tr>
			<td>NRO.EMPRESA (SUMINISTADO POR EL BANCO)</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.nro_empresa',array('maxlength' => 30,'size' => 30))?></td>
		</tr>
		<tr>
			<td>PRESTACION</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.prestacion',array('maxlength' => 10,'size' => 10))?></td>
		</tr>		
		<tr>
			<td>TIPO CUENTA</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.tipo_cuenta_banco_nacion',array('type' => 'select','options' => array('10' => '10 - CUENTA CORRIENTE $','11'=>'11 - CUENTA CORRIENTE U$S','20' => '20 - CAJA DE AHORRO $','21' => '21 - CAJA DE AHORRO U$S','27' => '27 - CTA.CTE. ESPECIAL $','28' => '28 - CTA.CTE. ESPECIAL U$S')))?></td>
		</tr>		
		<tr>
			<td>SUCURSAL</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.sucursal_bco_nacion',array('maxlength' => 10,'size' => 10))?></td>
		</tr>
		<tr>
			<td>NRO.CUENTA</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.cuenta_banco_nacion',array('maxlength' => 20,'size' => 20))?></td>
		</tr>
		<tr>
			<td>MONEDA</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.moneda_cuenta_banco_nacion',array('type' => 'select','options' => array('P' => 'P - PESOS','D'=>'D - DOLARES')))?></td>
		</tr>								
		<tr>
			<td align="right">FECHA A DEBITAR / FECHA TOPE (AAAAMMDD)</td><td colspan="2"><?php echo $frm->number('GeneradorDisketteBanco.fecha_debito',array('maxlength' => 8,'size' => 8))?></td>
		</tr>		
		<tr>
			<td>NOMBRE DEL ARCHIVO A GENERAR</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.archivo_salida',array('maxlength' => 30,'size' => 30))?></td>
		</tr>
		<tr>
			<td>NRO.DE ARCHIVO (1,2,3...,N)</td>
			<td><?php echo $frm->number('GeneradorDisketteBanco.nro_archivo_banco_nacion',array('maxlength' => 2,'size' => 2))?></td>
		</tr>
		<tr>
			<td>INDICADOR DE LOTE (EMP.BCO.NACION)</td>
			<td><?php echo $frm->input('GeneradorDisketteBanco.lote_banco_nacion',array('type' => 'select','options' => array('EMP' => 'EMP - CLIENTES DEL LOTE NO SON EMPLEADOS DEL BNA PERO PERCIBEN HABERES EN BNA','BNA'=>'BNA - TODOS LOS CLIENTES DEL LOTE SON EMPLEADOS DEL BNA','REE' => 'REE - AL MENOS UN (1) CLIENTE DEL LOTE NO PERCIBE HABERES EN BNA (CLIENTE COMUN)')))?></td>
		</tr>
        <tr>
            <td>ARCHIVO DE DATOS (EXCEL)</td><td><input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/></td>
        </tr>						
		
	</table>
	<?php echo $frm->end("PROCESAR ARCHIVO")?>
</div>
