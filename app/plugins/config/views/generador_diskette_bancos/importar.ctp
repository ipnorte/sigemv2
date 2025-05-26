<?php echo $this->renderElement('head',array('title' => 'ANALIZADOR DE ARCHIVO COBRANZA POR BANCOS - DEBITO AUTOMATICO'))?>
<?php echo $this->renderElement('generador_diskette_bancos/menu',array('plugin' => 'config'))?>


<h3>IMPORTAR DATOS :: ANALIZAR EL ARCHIVO DE DEBITO AUTOMATICO</h3>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'importar','type' => 'file'))?>
	<table class="tbl_form">
		<tr>
			<td>BANCO</td>
			<td><?php echo $this->requestAction('/config/bancos/combo/GeneradorDisketteBanco.banco_intercambio/0/0/5')?></td>
		</tr>
        <tr>
            <td>ARCHIVO CON LA RENDICION</td>
            <td>
            	<input type="file" name="data[GeneradorDisketteBanco][archivo_datos]" id="GeneradorDisketteBancoArchivoDatos"/>
            	<strong><?php echo $html->link("Formato de Archivo Excel","formatos",array('target' => 'blank'))?></strong>
            </td>
        </tr>
     			
	</table>
<?php echo $frm->end("PROCESAR ARCHIVO Y DESCARGAR DATOS EN PLANILLA EXCEL")?>
</div>