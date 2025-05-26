<?php
$contenido = "Content-Disposition: attachment; filename=Mayor-General.xls";
header('Content-type: application/vnd.ms-excel');
header($contenido);
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

App::import('Model', 'contabilidad.PlanCuenta');

$oPlanCuenta = new PlanCuenta();

?>

<h1>LIBRO MAYOR GENERAL</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($fecha_desde)?></h1>
<h1>FECHA HASTA :: <?php echo $util->armaFecha($fecha_hasta)?></h1>

<table align="center" width="100%">

	<col width="100" />
	<col width="300" />
	<col width="100" />
	<col width="100" />
	<col width="800" />
	<col width="100" />
	<col width="100" />

	<tr border="0">
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">CUENTA</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">DESCRIPCION</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">FECHA</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">NRO.AS.</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">REFERENCIA</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">DEBE</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">HABER</th>
	</tr>
	
	<?php	
	$i=0;
	while($i < count($libroMayorGeneral)):
	
		
		$DEBE  = 0;
		$HABER = 0;
		$SALDO = 0;
		
		$cuenta = $libroMayorGeneral[$i]['AsientoRenglon']['co_plan_cuenta_id'];
	
	
		while($cuenta == $libroMayorGeneral[$i]['AsientoRenglon']['co_plan_cuenta_id'] && $i < count($libroMayorGeneral)):
			$DEBE += $libroMayorGeneral[$i]['AsientoRenglon']['debe'];
			$HABER += $libroMayorGeneral[$i]['AsientoRenglon']['haber'];
			$SALDO = $DEBE - $HABER;?>
			
			
			<tr>
				<td style="border-right: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($libroMayorGeneral[$i]['PlanCuenta']['cuenta'], $ejercicio)?></td>
				<td style="border-right: 1px solid black;font-size: small;"><?php echo $libroMayorGeneral[$i]['PlanCuenta']['descripcion']?></td>
				<td align="center" style="border-right: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($libroMayorGeneral[$i]['Asiento']['fecha']))?></td>
				<td style="border-right: 1px solid black;font-size: small;"><?php echo $libroMayorGeneral[$i]['Asiento']['nro_asiento']?></td>
				<td style="border-right: 1px solid black"><?php echo $libroMayorGeneral[$i]['Asiento']['referencia']?></td>
				<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo ($libroMayorGeneral[$i]['AsientoRenglon']['debe'] >  0 ? number_format($libroMayorGeneral[$i]['AsientoRenglon']['debe'],2)  : '')?></td>
				<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo ($libroMayorGeneral[$i]['AsientoRenglon']['haber'] > 0 ? number_format($libroMayorGeneral[$i]['AsientoRenglon']['haber'],2) : '')?></td>
			</tr>	
			
					
			<?php
			$i++;
			if($i >= count($libroMayorGeneral)) break;
		endwhile;

		if($DEBE > 0 || $HABER > 0):?>

			<tr>
				<td colspan="5" align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">T O T A L </td>
				<td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;"><?php echo number_format($DEBE,2)?></td>
				<td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;"><?php echo number_format($HABER,2)?></td>
			</tr>

		<?php
		endif;


		if($i >= count($libroMayorGeneral)) break;
		
	endwhile;?>

</table>
