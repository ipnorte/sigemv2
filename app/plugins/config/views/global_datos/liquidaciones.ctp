<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DEL LIQUIDADOR'))?>

<?php if(empty($this->data)):?>
<table>
<tr>
    <th></th>
<th>ORGANISMO</th>
<th>METODO PERIODO</th>
<th>METODO MORA</th>
<th>MIN</th>
<th>MAX</th>
<th>MIN</th>
<th>PROM</th>
<th>MAX</th>
<th>REGISTRO</th>
<th>DEBITO</th>
</tr>

<?php 
$i = 0;
foreach ($datos as $dato):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = 'class="altrow"';
	}
?>
    <tr <?php echo $class;?>>
        <td><?php echo $controles->botonGenerico('/config/global_datos/liquidaciones/'.$dato['GlobalDato']['id'],'controles/edit.png')?></td>
        <td><?php echo $dato['GlobalDato']['concepto_1']?></td>
        <td><?php echo $spLiquidaPeriodo[$dato['GlobalDato']['concepto_4']]?></td>
        <td><?php echo $spLiquidaMora[$dato['GlobalDato']['concepto_5']]?></td>
        <td style="text-align: center;"><?php echo $dato['GlobalDato']['entero_6']?></td>
        <td style="text-align: center;"><?php echo $dato['GlobalDato']['entero_7']?></td>
        <td style="text-align: center;"><?php echo $dato['GlobalDato']['entero_3']?></td>
        <td style="text-align: center;"><?php echo $dato['GlobalDato']['entero_4']?></td>
        <td style="text-align: center;"><?php echo $dato['GlobalDato']['entero_5']?></td>
        <td><?php echo $dato['GlobalDato']['decimal_1']?></td>
        <td><?php echo $dato['GlobalDato']['decimal_2']?></td>
    </tr>
<?php endforeach;?>
</table>
<?php else: ?>
<h3>MODIFICAR CONFIGURACION :: <?php echo $this->data['GlobalDato']['concepto_1']?> </h3>

<?php echo $frm->create('GlobalDato',array('action' => 'liquidaciones'));?>

<div class="areaDatoForm">
    
<h4>PROCEDIMIENTOS</h4>
<hr>
    <table class="tbl_form">
        <tr>
            <td>1. LIQUIDACION PERIODO</td>
            <td><?php echo $frm->input('GlobalDato.concepto_4',array('type' => 'select','empty' => FALSE, 'selected' => $this->data['GlobalDato']['concepto_4'],'options' => $spLiquidaPeriodo))?></td>
        </tr>
        <tr>
            <td>2. LIQUIDACION MORA</td>
            <td><?php echo $frm->input('GlobalDato.concepto_5',array('type' => 'select','empty' => TRUE, 'selected' => $this->data['GlobalDato']['concepto_5'],'options' => $spLiquidaMora))?></td>
        </tr>
    </table>
    <h4>PARAMETROS DE TRATAMIENTO DE LA MORA ** CBU **</h4>
    <hr>
    <table class="tbl_form">
        <tr>
            <td>3.IMPORTE MENOR FRACCION</td>
            <td><?php echo $frm->input('GlobalDato.entero_6',array('size'=>10,'maxlength'=>10,'class' =>'input_number','onkeypress' => "return soloNumeros(event,true,true)")) ?></td>
        </tr>               
        <tr>
            <td>4.IMPORTE MAYOR FRACCION</td>
            <td><?php echo $frm->input('GlobalDato.entero_7',array('size'=>10,'maxlength'=>10,'class' =>'input_number','onkeypress' => "return soloNumeros(event,true,true)")) ?></td>
        </tr>
        <tr>
            <td>5.FRACCION (MORA < MIN)</td>
            <td><?php echo $frm->input('GlobalDato.entero_3',array('size'=>4,'maxlength'=>4,'class' =>'input_number','onkeypress' => "return soloNumeros(event,false,false)")) ?></td>
        </tr>                 
        <tr>
            <td>6.FRACCION (MIN < MORA < MAX)</td>
            <td><?php echo $frm->input('GlobalDato.entero_4',array('size'=>4,'maxlength'=>4,'class' =>'input_number','onkeypress' => "return soloNumeros(event,false,false)")) ?></td>        </tr> 
        <tr>
            <td>7.FRACCION (MORA > MAX)</td>
            <td><?php echo $frm->input('GlobalDato.entero_5',array('size'=>4,'maxlength'=>4,'class' =>'input_number','onkeypress' => "return soloNumeros(event,false,false)")) ?></td></tr>
        <tr>
            <td>8.MAXIMO POR REGISTRO MORA</td>
            <td><?php echo $frm->input('GlobalDato.decimal_1',array('size'=>10,'maxlength'=>10,'class' =>'input_number','onkeypress' => "return soloNumeros(event,true,true)")) ?></td>
        </tr>
        <tr>
            <td>9.MAXIMO POR REGISTRO BANCO</td>
            <td><?php echo $frm->input('GlobalDato.decimal_2',array('size'=>10,'maxlength'=>10,'class' =>'input_number','onkeypress' => "return soloNumeros(event,true,true)")) ?></td>
        </tr>                                            
    </table>

</div>
<?php echo $frm->hidden('id',array('value'=>$this->data['GlobalDato']['id'])) ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/global_datos/liquidaciones'))?>

<?php endif;?>