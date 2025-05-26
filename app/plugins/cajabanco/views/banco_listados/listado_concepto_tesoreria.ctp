<script language="Javascript" type="text/javascript">
	
	function mostrarConcepto(){
		var valConcepto = $('AsincronoTemporalConceptoId').getValue();
		var valBanco = $('AsincronoTemporalBancoId').getValue();
		var selConcepto = $('AsincronoTemporalBancoId');
		var selBanco = $('AsincronoTemporalBancoId');
		var tblPrincipal = $('tblPrincipal');
		var idRow, tblConcepto, idTblConcepto, idConcepto, idBanco;
		var xls = $('xls'), pdf = $('pdf');

		$('xls').href = xls.href.substring(0,52) + '/' + valConcepto + '/' + valBanco;
		$('pdf').href = xls.href.substring(0,52) + '/' + valConcepto + '/' + valBanco;
		


		if(valConcepto == 0 && valBanco == 0) showAll();
		else
		{
			hideAll();

			if(valConcepto != 0 && valBanco == 0)
			{
				idConcepto = "concepto_" + valConcepto;
				for(var i=0; i<tblPrincipal.rows.length; i++)
				{
					idRow = tblPrincipal.rows[i].id;
					if(idRow == idConcepto)
					{
						$(idRow).show();
						idTblConcepto = 'tblConcepto' + idRow.substring(8)
						tblConcepto = $(idTblConcepto);
						for(var j=1; j<tblConcepto.rows.length; j++)
						{
							idRowConcepto = tblConcepto.rows[j].id;
							$(idRowConcepto).show();
						}

					}
				}
			}
			
			if(valConcepto == 0 && valBanco != 0)
			{
				for(var i=0; i<tblPrincipal.rows.length; i++)
				{
					idRow = tblPrincipal.rows[i].id;
					idTblConcepto = 'tblConcepto' + idRow.substring(8)
					tblConcepto = $(idTblConcepto);
					idBanco = "banco" + idRow.substring(8) + "_" + valBanco;
					for(var j=1; j<tblConcepto.rows.length; j++)
					{
						idRowConcepto = tblConcepto.rows[j].id;
						if(idBanco == idRowConcepto)
						{
							$(idRow).show();
							$(idRowConcepto).show();
						}
					}
				}
			}
			
			if(valConcepto != 0 && valBanco != 0)
			{
				idConcepto = "concepto_" + valConcepto;
				for(var i=0; i<tblPrincipal.rows.length; i++)
				{
					idRow = tblPrincipal.rows[i].id;
					if(idRow == idConcepto)
					{
						idTblConcepto = 'tblConcepto' + idRow.substring(8)
						tblConcepto = $(idTblConcepto);
						idBanco = "banco" + idRow.substring(8) + "_" + valBanco;
						for(var j=1; j<tblConcepto.rows.length; j++)
						{
							idRowConcepto = tblConcepto.rows[j].id;
							if(idBanco == idRowConcepto)
							{
								$(idRow).show();
								$(idRowConcepto).show();
							}
						}

					}
				}
			}
			
		}


	}


	function showAll(){
		var tblPrincipal = $('tblPrincipal');
		var idRow, tblConcepto, idTblConcepto;
		

		for(var i=0; i<tblPrincipal.rows.length; i++)
		{
			idRow = tblPrincipal.rows[i].id;
			$(idRow).show();
			idTblConcepto = 'tblConcepto' + idRow.substring(8)
			tblConcepto = $(idTblConcepto);
			for(var j=1; j<tblConcepto.rows.length; j++)
			{
				idRowConcepto = tblConcepto.rows[j].id;
				$(idRowConcepto).show();
			}
		}
		
		return true;
	}


	function hideAll()
	{
		var tblPrincipal = $('tblPrincipal');
		var idRow, tblConcepto, idTblConcepto;
		

		for(var i=0; i<tblPrincipal.rows.length; i++)
		{
			idRow = tblPrincipal.rows[i].id;
			$(idRow).hide();
			idTblConcepto = 'tblConcepto' + idRow.substring(8)
			tblConcepto = $(idTblConcepto);
			for(var j=1; j<tblConcepto.rows.length; j++)
			{
				idRowConcepto = tblConcepto.rows[j].id;
				$(idRowConcepto).hide();
			}
		}
		
		return true;
	}
	
</script>


	<div class="areaDatoForm">
		<table class="tbl_form">
			<tr>
				<td>FILTRAR POR CONCEPTO:</td>
				<td>FILTRAR POR BANCO:</td>
			</tr>
			<tr>
				<td><?php echo $frm->input('AsincronoTemporal.concepto_id',array('type'=>'select','options'=>$aCmbConcepto, 'onchange' => 'mostrarConcepto()'));?></td>
				<td><?php echo $frm->input('AsincronoTemporal.banco_id',array('type'=>'select','options'=>$aCmbBanco, 'onchange' => 'mostrarConcepto()'));?></td>
			</tr>
		</table>
		<?php echo $controles->botonGenerico('/cajabanco/listados/salida/XLS/0/0','controles/ms_excel.png', null, array('id' => 'xls'))?>
		<?php echo $controles->botonGenerico('/cajabanco/listados/salida/PDF/0/0','controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'))?>
	</div>
	
	<table class="tbl_grilla" width='100%' id="tblPrincipal">
		<?php
			$i=0;
			while($i < count($aDatos)):
			  	$concepto = $aDatos[$i]['AsincronoTemporal']['clave_1'];
				$i++;
		?>
				<tr id='concepto_<?php echo $concepto . $aDatos[$i]['AsincronoTemporal']['texto_11']?>'>
					<td>
						<table width='100%' id='tblConcepto_<?php echo $concepto . $aDatos[$i]['AsincronoTemporal']['texto_11']?>'>
							<tr>
								<th colspan="9" align="left"><?php echo $aDatos[$i]['AsincronoTemporal']['texto_1']?></th>
							</tr>
			<?php 
				while($concepto == $aDatos[$i]['AsincronoTemporal']['clave_1']):
					$banco = $aDatos[$i]['AsincronoTemporal']['clave_2'];
					$i++;
			?>
					<tr id='banco_<?php echo $concepto . $aDatos[$i]['AsincronoTemporal']['texto_11'] . '_' . $banco?>'>
						<td>
							<table width='100%'>
								<col width="40" />
								<col width="80" />
								<col width="80" />
								<col width="350" />
								<col width="150" />
								<col width="300" />
								<col width="100" />
								<col width="80"/>
								<tr>
									<th colspan="9" align="left"><?php echo $aDatos[$i]['AsincronoTemporal']['texto_2']?></th>
								</tr>
								<tr>
									<th>#</th>
									<th>Fecha</th>
									<th>F.Venc.</th>
									<th>A LA ORDEN DE</th>
									<th>T.Documento</th>
									<th>Descripcion</th>
									<th>NRO.OPERACION</th>
									<th>DEBE</th>
									<th>HABER</th>
								</tr>
				<?php
					while($banco == $aDatos[$i]['AsincronoTemporal']['clave_2']):
				?>
						<tr>
							<td align="right"><?php echo $controles->linkModalBox($aDatos[$i]['AsincronoTemporal']['entero_1'],array('title' => 'MOVIMIENTO #' . $aDatos[$i]['AsincronoTemporal']['entero_1'],'url' => $aDatos[$i]['AsincronoTemporal']['texto_15'],'h' => 450, 'w' => 750))?></td>
							<td align="center"><?php echo $util->armaFecha($aDatos[$i]['AsincronoTemporal']['texto_8'])?></td>
							<td align="center"><?php echo $util->armaFecha($aDatos[$i]['AsincronoTemporal']['texto_9'])?></td>
							<td><?php echo $aDatos[$i]['AsincronoTemporal']['texto_4']?></td>
							<td><?php echo $controles->linkModalBox($aDatos[$i]['AsincronoTemporal']['texto_5'],array('title' => $aDatos[$i]['AsincronoTemporal']['texto_5'],'url' => $aDatos[$i]['AsincronoTemporal']['texto_6'],'h' => 450, 'w' => 750))?></td>
							<td><?php echo $aDatos[$i]['AsincronoTemporal']['texto_7']?></td>
							<td><?php echo $aDatos[$i]['AsincronoTemporal']['texto_10']?></td>
							<td align="right"><?php echo number_format($aDatos[$i]['AsincronoTemporal']['decimal_1'],2)?></td>
							<td align="right"><?php echo number_format($aDatos[$i]['AsincronoTemporal']['decimal_2'],2)?></td>
						</tr>
				<?php 
						$i++;
					endwhile;
				?>
							</table>
						</td>
					</tr>
				<?php 
				endwhile;
				?>
					</table>
				</td>
			</tr>
		<?php 
			endwhile;
		?>
	
	</table>
	