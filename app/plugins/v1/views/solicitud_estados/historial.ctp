<H1>HISTORIAL DE LA SOLICITUD Nro.<?php echo $nro_solicitud?></H1>
<hr>
<table>
  <tr>
    <th>Entrada</th>
    <th>Salida</th>
    <th>Estado</th>
    <th>Observacion</th>
    <th>Entrada</th>
    <th>Salida</th>
    <th>Hs.</th>
  </tr>
  <?php foreach($historial as $histo):?>
  <tr>
    <td nowrap="nowrap"><?php echo $histo['solicitud_estados_hist']['fecha_in']?></td>
    <td nowrap="nowrap"><?php echo $histo['solicitud_estados_hist']['fecha_out']?></td>
    <td nowrap="nowrap"><?php echo $histo['solicitud_codigo_estados']['descripcion']?></td>
    <td><?php echo $histo['solicitud_estados_hist']['observacion']?></td>
    <td><?php echo $histo['solicitud_estados_hist']['usuario']?></td>
    <td><?php echo $histo['solicitud_estados_hist']['usuario_to']?></td>
    <td><?php echo $histo['solicitud_estados_hist']['horas_habiles']?></td>
  </tr>
  <?php endforeach;?>
</table>