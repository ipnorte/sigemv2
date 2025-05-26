<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION AFIP :: WEB SERVICES'))?>

<div class="actions">
	<?php if($tipoAbm == 0){
            echo $controles->botonGenerico('edit','controles/add.png','Ingresar Datos');
        }else{
            echo $controles->botonGenerico('edit','controles/add.png','Modificar Datos');
        }
        ?>
</div>

<div class="areaDatoForm">
			
    <table class='tbl_form'>

            <tr>
                    <td>C.U.I.T. EMPRESA:</td><td><?php echo $frm->input('cuit',array('size'=>11,'maxlength'=>11,'disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['cuit'])) ?></td>
            </tr>
            <tr>
                    <td>ARCHIVO LLAVE (KEY):</td><td><?php echo $frm->input('key',array('size'=>60,'maxlength'=>100,'disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['key'])) ?></td>
            </tr>
            <tr>
                    <td>CERTIFICADO PRODUCCION (CRT):</td><td><?php echo $frm->input('pcrt',array('size'=>60,'maxlength'=>100,'disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['pcrt'])) ?></td>
            </tr>
            <tr>
                    <td>CERTIFICADO HOMOLOGACION (PEM):</td><td><?php echo $frm->input('hcrt',array('size'=>60,'maxlength'=>100,'disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['hcrt'])) ?></td>
            </tr>
            <tr>
                <td>PUNTO DE VENTA:</td><td><?php echo $frm->number('pvta',array('disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['pvta'],'size'=>4,'maxlength'=>4)) ?></td>
            </tr>												
            <tr>
                <td>FACTURA TIPO:</td><td><?php echo $frm->number('factura',array('disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['factura'],'size'=>2,'maxlength'=>2)) ?></td>
            </tr>
            <tr>
                <td>NOTA DEBITO TIPO:</td><td><?php echo $frm->number('ndebito',array('disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['ndebito'],'size'=>2,'maxlength'=>2)) ?></td>
            </tr>
            <tr>
                <td>NOTA CREDITO TIPO:</td><td><?php echo $frm->number('ncredito',array('disabled'=>'disabled','value'=>$datoAfip['GlobalDato']['ncredito'],'size'=>2,'maxlength'=>2)) ?></td>
            </tr>                
            <tr>
                 <td>FORMA:</td><td><?php echo $frm->input('forma',array('type' => 'select','options' => array('' => 'Seleccionar.....', 1 => 'PRODUCCION',2 => 'HOMOLOGACION'),'label'=>'FORMA ACCESO AFIP', 'selected' => $datoAfip['GlobalDato']['entero_1'])) ?></td>
            </tr>
    </table>

</div>

