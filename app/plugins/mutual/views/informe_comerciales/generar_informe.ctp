<?php echo $this->renderElement('head',array('title' => 'INFORMES COMERCIALES','plugin' => 'config'))?>
<?php echo $this->renderElement('informe_comerciales/menu_nav',array('plugin'=>'mutual'))?>
<div class="areaDatoForm">
    <h3>Generar Nuevo Informe</h3>
    <?php echo $form->create(null,array('name'=>'formNuevoInforme','id'=>'formNuevoInforme','action' => 'generar_informe'));?>
	<table class="tbl_form">
		<tr>
			<td>
					<?php echo $this->renderElement('global_datos/combo_global',array(
                                                        'plugin'=>'config',
                                                        'metodo' => 'get_empresas_info_deuda',
                                                        'label' => 'EMPRESA / CANAL',
                                                        'model' => 'SocioInforme.empresa',
                                                        'prefijo' => 'MUTUSOIN',
                                                        'disable' => false,
                                                        'empty' => false,
                                                        'logico' => true,
                                                        'selected' => $empresa,
					))?>			
			</td>
                        <td><?php echo $frm->periodo('SocioInforme.periodo_corte','PERIODO DE CORTE',(isset($periodo_corte) ? $periodo_corte :  null),date('Y')-2,date('Y'),false)?></td>
                        <td><input type="submit" value="CARGAR DATOS"></td>
                </tr>
	</table>
<?php echo $form->end();?>    
    
</div>

<?php if(!empty($socios)):?>

<h2>EMPRESA/CANAL: <?php echo $util->globalDato($empresa)?> |  <?php echo $util->periodo($periodo_corte)?></h2>
<?php echo $form->create(null,array('name'=>'formNuevoInformeProcess','id'=>'formNuevoInformeProcess','action' => 'generar_informe'));?>
<table>
    <tr>
        <th>DNI</th>
        <th>Socio</th>
        <th>Periodo</th>
        <th>Deuda</th>
        <th></th>
        
    </tr>
    <?php foreach($socios as $socio):?>
    <tr>
        <td><?php echo $socio['Socio']['Persona']['documento']?></td>
        <td><?php echo $socio['Socio']['Persona']['apellido']?>, <?php echo $socio['Socio']['Persona']['nombre']?></td>
        <td style="text-align: center;"><?php echo $util->periodo($socio['SocioInforme']['periodo_hasta'])?></td>
        <td style="text-align: right;"><?php echo $util->nf($socio['SocioInforme']['deuda_informada'])?></td>
        <td><input type="checkbox" name="data[SocioInforme][id][<?php echo $socio['SocioInforme']['id']?>]" value="<?php echo $socio['SocioInforme']['id']?>"></td>
    </tr>
    <?php endforeach;?>
</table>
<?php echo $frm->hidden('SocioInforme.empresa',array('value' => $empresa)); ?>
<?php echo $frm->hidden('SocioInforme.periodo_hasta',array('value' => $periodo_corte)); ?>
<?php echo $form->end("GENERAR LOTE Y DESCARGAR ARCHIVO");?>
<?php // debug($socios)?>

<?php endif; ?>

