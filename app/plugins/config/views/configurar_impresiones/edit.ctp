<h3>MODIFICACION CONFIGURACION DE CHEQUES</h3>
<?php echo $form->create(null,array('name'=>'formCnfImpresion','id'=>'formCnfImpresion','onsubmit' => ""));?>

<style type="text/css">

	input:focus{
	   background-color: #8AAEC6;
	}

</style>

<script language="Javascript" type="text/javascript">



	Event.observe(window, 'load', function() {
		var campo;
		var activo;
		
		<?php	
		foreach($aOpcion as $campo => $renglon):?>
			campo = "<?php echo $campo;?>";
			activo = <?php echo $renglon['activo'];?>;
			
			if(campo === 'DV') $('CnfImpresionDiaVen').value = activo;
			if(campo === 'MV') $('CnfImpresionMesVen').value = activo;
			if(campo === 'AV') $('CnfImpresionAnoVen').value = activo;
			if(campo === 'FV') $('CnfImpresionFecVen').value = activo;
			if(campo === 'DE') $('CnfImpresionDiaEmi').value = activo;
			if(campo === 'ME') $('CnfImpresionMesEmi').value = activo;
			if(campo === 'AE') $('CnfImpresionAnoEmi').value = activo;
			if(campo === 'FE') $('CnfImpresionFecEmi').value = activo;
			if(campo === 'DS') $('CnfImpresionDestin').value = activo;
			if(campo === 'CN') $('CnfImpresionCntNro').value = activo;
			if(campo === 'CL') $('CnfImpresionCntLtr').value = activo;
		<?php endforeach;
		$nValor = $aOpcion['DE']['activo'];
		?>
		
		selectMostrar('DE',<?php echo $nValor?>);
	});
		

	function trResaltar(objeto, color){
		var nValor = 0;
		
		objeto.style.background = color;
		
		if(objeto.cells[1].childNodes[0].nodeValue === 'Si') nValor = 1;
		
		selectMostrar(objeto.id, nValor);
		
	}
		
		
	function trCambiar(trObjeto){
		var nValor = 0;
		
		if(trObjeto.cells[1].childNodes[0].nodeValue === 'Si')
		{
			trObjeto.cells[1].innerHTML = 'No';
			nValor = 0;
		}
		else
		{
			trObjeto.cells[1].innerHTML = 'Si';
			nValor = 1;
		};
                
		selectMostrar(trObjeto.id, nValor);
	}


	function selectMostrar(vSelectIndex, nValor){
		
		$('diaVen').hide();
		$('mesVen').hide();
		$('anoVen').hide();
		$('fecVen').hide();
		$('diaEmi').hide();
		$('mesEmi').hide();
		$('anoEmi').hide();
		$('fecEmi').hide();
		$('destin').hide();
		$('cntNro').hide();
		$('cntLet').hide();
		
		
		if(vSelectIndex === 'DV'){
			$('diaVen').show();
                        $('CnfImpresionDiaVen').value = nValor;
			if(nValor === 0){
				$('CnfImpresionDiaVenIzq').disable();
				$('CnfImpresionDiaVenSup').disable();
				$('CnfImpresionDiaVenAnc').disable();
				$('CnfImpresionDiaVenAlt').disable();
				$('CnfImpresionDiaVenFor').disable();
			}
			else{
				$('CnfImpresionDiaVenIzq').enable();
				$('CnfImpresionDiaVenSup').enable();
				$('CnfImpresionDiaVenAnc').enable();
				$('CnfImpresionDiaVenAlt').enable();
				$('CnfImpresionDiaVenFor').enable();
				$('CnfImpresionDiaVenIzq').select();
				$('CnfImpresionDiaVenIzq').focus();
			};
		};
		
		if(vSelectIndex === 'MV'){
			$('mesVen').show();
                        $('CnfImpresionMesVen').value = nValor;
			if(nValor === 0){
				$('CnfImpresionMesVenIzq').disable();
				$('CnfImpresionMesVenSup').disable();
				$('CnfImpresionMesVenAnc').disable();
				$('CnfImpresionMesVenAlt').disable();
				$('CnfImpresionMesVenFor').disable();
			}
			else{
				$('CnfImpresionMesVenIzq').enable();
				$('CnfImpresionMesVenSup').enable();
				$('CnfImpresionMesVenAnc').enable();
				$('CnfImpresionMesVenAlt').enable();
				$('CnfImpresionMesVenFor').enable();
				$('CnfImpresionMesVenIzq').select();
				$('CnfImpresionMesVenIzq').focus();
			};
		};
		
		if(vSelectIndex === 'AV'){
			$('anoVen').show();
                        $('CnfImpresionAnoVen').value = nValor;
			if(nValor === 0){
				$('CnfImpresionAnoVenIzq').disable();
				$('CnfImpresionAnoVenSup').disable();
				$('CnfImpresionAnoVenAnc').disable();
				$('CnfImpresionAnoVenAlt').disable();
				$('CnfImpresionAnoVenFor').disable();
			}
			else{
				$('CnfImpresionAnoVenIzq').enable();
				$('CnfImpresionAnoVenSup').enable();
				$('CnfImpresionAnoVenAnc').enable();
				$('CnfImpresionAnoVenAlt').enable();
				$('CnfImpresionAnoVenFor').enable();
				$('CnfImpresionAnoVenIzq').select();
				$('CnfImpresionAnoVenIzq').focus();
			};
		};
		
		if(vSelectIndex === 'FV'){
			$('fecVen').show();
                        $('CnfImpresionFecVen').value = nValor;
			if(nValor === 0){
				$('CnfImpresionFecVenIzq').disable();
				$('CnfImpresionFecVenSup').disable();
				$('CnfImpresionFecVenAnc').disable();
				$('CnfImpresionFecVenAlt').disable();
				$('CnfImpresionFecVenFor').disable();
			}
			else{
				$('CnfImpresionFecVenIzq').enable();
				$('CnfImpresionFecVenSup').enable();
				$('CnfImpresionFecVenAnc').enable();
				$('CnfImpresionFecVenAlt').enable();
				$('CnfImpresionFecVenFor').enable();
				$('CnfImpresionFecVenIzq').select();
				$('CnfImpresionFecVenIzq').focus();
			};
		};
		
		if(vSelectIndex === 'DE'){
			$('diaEmi').show();
                        $('CnfImpresionDiaEmi').value = nValor;
			if(nValor === 0){
				$('CnfImpresionDiaEmiIzq').disable();
				$('CnfImpresionDiaEmiSup').disable();
				$('CnfImpresionDiaEmiAnc').disable();
				$('CnfImpresionDiaEmiAlt').disable();
				$('CnfImpresionDiaEmiFor').disable();
			}
			else{
				$('CnfImpresionDiaEmiIzq').enable();
				$('CnfImpresionDiaEmiSup').enable();
				$('CnfImpresionDiaEmiAnc').enable();
				$('CnfImpresionDiaEmiAlt').enable();
				$('CnfImpresionDiaEmiFor').enable();
				$('CnfImpresionDiaEmiIzq').select();
				$('CnfImpresionDiaEmiIzq').focus();
			};
		};
		
		if(vSelectIndex === 'ME'){
			$('mesEmi').show();
                        $('CnfImpresionMesEmi').value = nValor;
			if(nValor === 0){
				$('CnfImpresionMesEmiIzq').disable();
				$('CnfImpresionMesEmiSup').disable();
				$('CnfImpresionMesEmiAnc').disable();
				$('CnfImpresionMesEmiAlt').disable();
				$('CnfImpresionMesEmiFor').disable();
			}
			else{
				$('CnfImpresionMesEmiIzq').enable();
				$('CnfImpresionMesEmiSup').enable();
				$('CnfImpresionMesEmiAnc').enable();
				$('CnfImpresionMesEmiAlt').enable();
				$('CnfImpresionMesEmiFor').enable();
				$('CnfImpresionMesEmiIzq').select();
				$('CnfImpresionMesEmiIzq').focus();
			};
		};
		
		if(vSelectIndex === 'AE'){
			$('anoEmi').show();
                        $('CnfImpresionAnoEmi').value = nValor;
			if(nValor === 0){
				$('CnfImpresionAnoEmiIzq').disable();
				$('CnfImpresionAnoEmiSup').disable();
				$('CnfImpresionAnoEmiAnc').disable();
				$('CnfImpresionAnoEmiAlt').disable();
				$('CnfImpresionAnoEmiFor').disable();
			}
			else{
				$('CnfImpresionAnoEmiIzq').enable();
				$('CnfImpresionAnoEmiSup').enable();
				$('CnfImpresionAnoEmiAnc').enable();
				$('CnfImpresionAnoEmiAlt').enable();
				$('CnfImpresionAnoEmiFor').enable();
				$('CnfImpresionAnoEmiIzq').select();
				$('CnfImpresionAnoEmiIzq').focus();
			};
		};
		
		if(vSelectIndex === 'FE'){
			$('fecEmi').show();
                        $('CnfImpresionFecEmi').value = nValor;
			if(nValor === 0){
				$('CnfImpresionFecEmiIzq').disable();
				$('CnfImpresionFecEmiSup').disable();
				$('CnfImpresionFecEmiAnc').disable();
				$('CnfImpresionFecEmiAlt').disable();
				$('CnfImpresionFecEmiFor').disable();
			}
			else{
				$('CnfImpresionFecEmiIzq').enable();
				$('CnfImpresionFecEmiSup').enable();
				$('CnfImpresionFecEmiAnc').enable();
				$('CnfImpresionFecEmiAlt').enable();
				$('CnfImpresionFecEmiFor').enable();
				$('CnfImpresionFecEmiIzq').select();
				$('CnfImpresionFecEmiIzq').focus();
			};
		};
		
		if(vSelectIndex === 'DS'){
			$('destin').show();
                        $('CnfImpresionDestin').value = nValor;
			if(nValor === 0){
				$('CnfImpresionDestinIzq').disable();
				$('CnfImpresionDestinSup').disable();
				$('CnfImpresionDestinAnc').disable();
				$('CnfImpresionDestinAlt').disable();
			}
			else{
				$('CnfImpresionDestinIzq').enable();
				$('CnfImpresionDestinSup').enable();
				$('CnfImpresionDestinAnc').enable();
				$('CnfImpresionDestinAlt').enable();
				$('CnfImpresionDestinIzq').select();
				$('CnfImpresionDestinIzq').focus();
			};
		};
		
		if(vSelectIndex === 'CN'){
			$('cntNro').show();
                        $('CnfImpresionCntNro').value = nValor;
			if(nValor === 0){
				$('CnfImpresionCntNroIzq').disable();
				$('CnfImpresionCntNroSup').disable();
				$('CnfImpresionCntNroAnc').disable();
				$('CnfImpresionCntNroAlt').disable();
			}
			else{
				$('CnfImpresionCntNroIzq').enable();
				$('CnfImpresionCntNroSup').enable();
				$('CnfImpresionCntNroAnc').enable();
				$('CnfImpresionCntNroAlt').enable();
				$('CnfImpresionCntNroIzq').select();
				$('CnfImpresionCntNroIzq').focus();
			};
		};
		
		if(vSelectIndex === 'CL'){
			$('cntLet').show();
                        $('CnfImpresionCntLtr').value = nValor;
			if(nValor === 0){
				$('CnfImpresionCntLtrIzq').disable();
				$('CnfImpresionCntLtrSup').disable();
				$('CnfImpresionCntLtrAnc').disable();
				$('CnfImpresionCntLtrAlt').disable();
			}
			else{
				$('CnfImpresionCntLtrIzq').enable();
				$('CnfImpresionCntLtrSup').enable();
				$('CnfImpresionCntLtrAnc').enable();
				$('CnfImpresionCntLtrAlt').enable();
				$('CnfImpresionCntLtrIzq').select();
				$('CnfImpresionCntLtrIzq').focus();
			};
		};
		
	};
	
	
	
	
		
</script>
		



<?php echo $frm->create(null,array('name'=>'formCnfImpresion','id'=>'formCnfImpresion', 'action' => "/config/configurar_impresiones/edit/" . $aCheque['id']));?>
	<div class="areaDatoForm">

		<table class="tbl_form">
			<tr id="select1">
				<td>
					<table borde=1>
						<tr borde=1>
							<th>Campos de datos</th>
							<th>Mostrar</th>
						</tr>
						<?php
							$par = 0;
							foreach ($aOpcion as $tdId => $tdTexto):
								$background = ""; //"#EFBD9C"; // este es el onmouseover
								if ($par++ % 2 === 0) {
									$background = "#dde2ee";
								}?>
								<tr borde=1 id="<?php echo $tdId?>" style="background-color: <?php echo $background?>" onMouseOver="trResaltar(this,'#EFBD9C');" onMouseOut="trResaltar(this,'<?php echo $background?>');" ondblClick = "trCambiar(this);">
									<td><?php echo $tdTexto['texto']?></td>
									<td><?php echo ($tdTexto['activo'] === '1' ? 'Si' : 'No')?></td>
								</tr>
						<?php endforeach;?>
					</table>
				</td>
				<td>
					<div>
							<table id='diaVen'>
								<tr>
									<th colspan="2" align="center">DIA VENCIMIENTO</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaVenIzq','', $aOpcion['DV']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaVenSup','', $aOpcion['DV']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaVenAnc','', $aOpcion['DV']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaVenAlt','', $aOpcion['DV']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.DiaVenFor', array('type' => 'select','options' => array('0' => 'Texto: para indicar que aparezca (veinticinco)', '1' => 'd: Uno o dos dígitos para el día (8, 10, 25)', '2' => 'dd: Dos dígitos para el día (08, 10, 25)'), 'selected' => $aOpcion['DV']['Formato']));?></td>
								</tr>
							</table>

							<table id='mesVen'>
								<tr>
									<th colspan="2" align="center">MES VENCIMIENTO</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.MesVenIzq','', $aOpcion['MV']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.MesVenSup','', $aOpcion['MV']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.MesVenAnc','', $aOpcion['MV']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.MesVenAlt','', $aOpcion['MV']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.MesVenFor', array('type' => 'select','options' => array('0' => 'm: Uno o dos dígitos para el mes (8, 12)', '1' => 'mm: Dos dígitos para el mes (08, 12)', '2' => 'mmm: Mes abreviado (Ene, Feb, Mar)', '3' => 'mmmm: Mes completo (Enero, Febrero)'), 'selected' => $aOpcion['MV']['Formato']));?></td>
								</tr>
							</table>

							<table id='anoVen'>
								<tr>
									<th colspan="2" align="center">A&Ntilde;O VENCIMIENTO</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoVenIzq','', $aOpcion['AV']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoVenSup','', $aOpcion['AV']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoVenAnc','', $aOpcion['AV']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoVenAlt','', $aOpcion['AV']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.AnoVenFor', array('type' => 'select','options' => array('0' => 'aa: 2 dígitos para el año (05, 06)', '1' => 'aaaa: 4 dígitos para el año (2005, 2006)'), 'selected' => $aOpcion['AV']['Formato']));?></td>
								</tr>
							</table>

							<table id='fecVen'>
								<tr>
									<th colspan="2" align="center">FECHA VENCIMIENTO</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.FecVenIzq','', $aOpcion['FV']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.FecVenSup','', $aOpcion['FV']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.FecVenAnc','', $aOpcion['FV']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.FecVenAlt','', $aOpcion['FV']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.FecVenFor', array('type' => 'select','options' => array('0' => '31/12/07', '1' => '31/12/2007', '2' => '31 de Diciembre de 07', '3' => '31 de Dic. de 07', '4' => '31 de Diciembre de 2007', '5' => '31 de Dic. de 2007'), 'selected' => $aOpcion['FV']['Formato']));?></td>
								</tr>
							</table>

							<table id='diaEmi'>
								<tr>
									<th colspan="2" align="center">DIA EMISION</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaEmiIzq','', $aOpcion['DE']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaEmiSup','', $aOpcion['DE']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaEmiAnc','', $aOpcion['DE']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.DiaEmiAlt','', $aOpcion['DE']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.DiaEmiFor', array('type' => 'select','options' => array('0' => 'Texto: para indicar que aparezca (veinticinco)', '1' => 'd: Uno o dos dígitos para el día (8, 10, 25)', '2' => 'dd: Dos dígitos para el día (08, 10, 25)'), 'selected' => $aOpcion['DE']['Formato']));?></td>
								</tr>
							</table>

							<table id='mesEmi'>
								<tr>
									<th colspan="2" align="center">MES EMISION</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.MesEmiIzq','', $aOpcion['ME']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.MesEmiSup','', $aOpcion['ME']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.MesEmiAnc','', $aOpcion['ME']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.MesEmiAlt','', $aOpcion['ME']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.MesEmiFor', array('type' => 'select','options' => array('0' => 'm: Uno o dos dígitos para el mes (8, 12)', '1' => 'mm: Dos dígitos para el mes (08, 12)', '2' => 'mmm: Mes abreviado (Ene, Feb, Mar)', '3' => 'mmmm: Mes completo (Enero, Febrero)'), 'selected' => $aOpcion['ME']['Formato']));?></td>
								</tr>
							</table>

							<table id='anoEmi'>
								<tr>
									<th colspan="2" align="center">A&Ntilde;O EMISION</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoEmiIzq','', $aOpcion['AE']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoEmiSup','', $aOpcion['AE']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoEmiAnc','', $aOpcion['AE']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.AnoEmiAlt','', $aOpcion['AE']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.AnoEmiFor', array('type' => 'select','options' => array('0' => 'aa: 2 dígitos para el año (05, 06)', '1' => 'aaaa: 4 dígitos para el año (2005, 2006)'), 'selected' => $aOpcion['AE']['Formato']));?></td>
								</tr>
							</table>

							<table id='fecEmi'>
								<tr>
									<th colspan="2" align="center">FECHA EMISION</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.FecEmiIzq','', $aOpcion['FE']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.FecEmiSup','', $aOpcion['FE']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.FecEmiAnc','', $aOpcion['FE']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.FecEmiAlt','', $aOpcion['FE']['Alto']) ?>
								</tr>
								<tr>
									<td>Formato:</td>
									<td><?php echo $frm->input('CnfImpresion.FecEmiFor', array('type' => 'select','options' => array('0' => '31/12/07', '1' => '31/12/2007', '2' => '31 de Diciembre de 07', '3' => '31 de Dic. de 07', '4' => '31 de Diciembre de 2007', '5' => '31 de Dic. de 2007'), 'selected' => $aOpcion['FE']['Formato']));?></td>
								</tr>
							</table>

							<table id='destin'>
								<tr>
									<th colspan="2" align="center">DESTINATARIO</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.DestinIzq','', $aOpcion['DS']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.DestinSup','', $aOpcion['DS']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.DestinAnc','', $aOpcion['DS']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.DestinAlt','', $aOpcion['DS']['Alto']) ?>
								</tr>
							</table>

							<table id='cntNro'>
								<tr>
									<th colspan="2" align="center">CANTIDAD EN NUMERO</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.CntNroIzq','', $aOpcion['CN']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.CntNroSup','', $aOpcion['CN']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.CntNroAnc','', $aOpcion['CN']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.CntNroAlt','', $aOpcion['CN']['Alto']) ?>
								</tr>
							</table>

							<table id='cntLet'>
								<tr>
									<th colspan="2" align="center">CANTIDAD EN LETRA</th>
								</tr>
								<tr>
									<td>Izquierda:</td>
									<td><?php echo $frm->money('CnfImpresion.CntLtrIzq','', $aOpcion['CL']['Izquierda']) ?>
								</tr>
								<tr>
									<td>Superior:</td>
									<td><?php echo $frm->money('CnfImpresion.CntLtrSup','', $aOpcion['CL']['Superior']) ?>
								</tr>
								<tr>
									<td>Ancho:</td>
									<td><?php echo $frm->money('CnfImpresion.CntLtrAnc','', $aOpcion['CL']['Ancho']) ?>
								</tr>
								<tr>
									<td>Alto:</td>
									<td><?php echo $frm->money('CnfImpresion.CntLtrAlt','', $aOpcion['CL']['Alto']) ?>
								</tr>
							</table>
					</div>
				</td>

				<td>
					<table>
						<tr>
							<td>Descripcion del Cheque:</td>
							<td><?php echo $frm->input('CnfImpresion.Descripcion',array('value' => $aCheque['texto'],'size'=>40,'maxlenght'=>100)) ?>
						</tr>
						
						<tr>
							<td>Ancho del Cheque:</td>
							<td><?php echo $frm->money('CnfImpresion.Ancho','', $aCheque['ancho']) ?>
						</tr>
						
						<tr>
							<td>Alto del Cheque:</td>
							<td><?php echo $frm->money('CnfImpresion.Alto','', $aCheque['alto']) ?>
						</tr>
						<tr>
							<td>Modelo de Talonario:</td>
							<td><?php echo $frm->input('CnfImpresion.Talonario',array('type' => 'select','options' => array('0' => 'Papel continuo o cheques sueltos en horizontal', '1' => 'Hoja de cheques (varios cheques en una hoja)', '2' => 'Cheques sueltos en vertical'), 'selected' => $aCheque['talonario']));?></td>
						</tr>
					</table>
				</td>

				<td>
					<div class="areaDatoForm">
						<p>Haga Click en la lista de campos disponibles</p>
						<p>para cambiar el valor de "MOSTRAR", </p>
						<p>establezca los valores de posicion y tamaño de</p>
						<p>cada control para ajustarlo a la disposicion</p>
						<p>de su cheque y haga Click en GUARDAR para salvar</p>
						<p>la configuracion. Todo esta expresado en cm.</p>
						<p></p>
					</div>
				</td>
			</tr>
			
			<tr>
				<td></td>
			</tr>
		</table>
	</div>

<?php echo $frm->hidden('CnfImpresion.id',array('value' => $aCheque['id'])); ?>
<?php echo $frm->hidden('CnfImpresion.DiaVen',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.MesVen',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.AnoVen',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.FecVen',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.DiaEmi',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.MesEmi',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.AnoEmi',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.FecEmi',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.Destin',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.CntNro',array('value' => 0)); ?>
<?php echo $frm->hidden('CnfImpresion.CntLtr',array('value' => 0)); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/configurar_impresiones/edit'))?> 
