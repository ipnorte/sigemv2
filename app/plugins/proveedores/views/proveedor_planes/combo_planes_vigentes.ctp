<?php if(!empty($datos)):?>
	<?php if($opcion == 'P'):?>
		<?php foreach($datos as $dato):?>
		<option value="<?php echo $dato['ProveedorPlan']['id']?>">
			<?php echo $dato['ProveedorPlan']['cadena']?>
		</option>
		<?php endforeach;?>
	<?php endif;?>
	<?php if($opcion == 'M'):?>
	<?php foreach($datos as $dato):?>
                <option value="<?php echo $dato['ProveedorPlanGrillaCuota']['id']?>">$ <?php echo number_format($dato['ProveedorPlanGrillaCuota']['liquido'],2,'.','')?></option>
	<?php endforeach;?>
	<?php endif;?>	
	<?php if($opcion == 'C'):?>
	<?php foreach($datos as $dato):?>
                <option value="<?php echo $dato['ProveedorPlanGrillaCuota']['id']?>">
                    <?php 
                    $cadena = str_pad($dato['ProveedorPlanGrillaCuota']['cuotas'], 2,0,STR_PAD_LEFT)." cuotas de $ " . number_format($dato['ProveedorPlanGrillaCuota']['importe'],2,'.','');
                    if(!empty($dato['ProveedorPlanGrilla']['tem']) && $dato['ProveedorPlanGrilla']['tem'] > 0 && !empty($dato['ProveedorPlanGrillaCuota']['cft'])){
                        $cadena .= " (TEM " . $dato['ProveedorPlanGrilla']['tem']."% | CFT " . $dato['ProveedorPlanGrillaCuota']['cft'] ."%)";
                    }
                    echo $cadena;
                    ?>
                </option>
	<?php endforeach;?>
	<?php endif;?>	
<?php endif;?>

                <?php // debug($datos)?>
