<?php echo $this->renderElement('head',array('title' => 'NOTIFICACION DE ESTADO DE CUENTA','plugin' => 'config'))?>
<?php echo $this->renderElement('liquidacion/notifica_deuda_nav',array('plugin'=>'mutual'))?>

<div class="areaDatoForm">
    Lote: <strong>#<?php echo $notificacion['Notificacion']['id']?></strong>
    -
    Per&iacute;odo: <strong><?php echo $util->periodo($notificacion['Notificacion']['periodo'])?></strong>
    -
    Fecha de Emisi&oacute;n: <strong><?php echo $notificacion['Notificacion']['fecha']?></strong>
</div>

<?php 

// debug($notificacion['NotificacionSocio']);

$totalDeuda = $totalPago = 0;
$cantidad = count($notificacion['NotificacionSocio']);

echo '<table border="1" cellpadding="4" cellspacing="0">';
echo '<thead>';
echo '<tr>';
echo '<th>Socio</th>';
echo '<th>Documento</th>';
echo '<th>Apellido y Nombre</th>';
echo '<th>Email</th>';
echo '<th>Pagado</th>';
echo '<th>Saldo</th>';
echo '<th>SD</th>';
echo '<th>Error</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($notificacion['NotificacionSocio'] as $ns) {
    
    $persona = $ns['Socio']['Persona'];
    $apenom = $persona['apellido'] . ', ' . $persona['nombre'];

    $totalDeuda += (float) $ns['saldo'];
    $totalPago += (float) $ns['pagado'];

    echo '<tr>';
    echo '<td>' . h($ns['socio_id']) . '</td>';
    echo '<td>' . h($persona['documento']) . '</td>';
    echo '<td>' . h($apenom) . '</td>';
    echo '<td>' . h($ns['email']) . '</td>';
    echo '<td style="text-align:right;">' . number_format($ns['pagado'], 2, ',', '.') . '</td>';
    echo '<td style="text-align:right;font-weight: bold;">' . number_format($ns['saldo'], 2, ',', '.') . '</td>';
    echo '<td style="text-align:center;">' . ($ns['stop_debit'] ? 'S' : 'N') . '</td>';
    echo '<td style="text-align:center;">' . ($ns['error'] ? 'S' : 'N') . '</td>';
    echo '</tr>';
}

// fila de resumen
echo '<tr style="font-weight: bold; background-color: #f0f0f0;">';
echo '<td colspan="4" style="text-align:right;">TOTAL DEUDA (cantidad: '.$cantidad.')</td>';
echo '<td style="text-align:right;">' . number_format($totalPago, 2, ',', '.') . '</td>';
echo '<td style="text-align:right;">' . number_format($totalDeuda, 2, ',', '.') . '</td>';
echo '<td style="text-align:center;" colspan="2"></td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';


?>

