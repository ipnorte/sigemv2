<?php if(empty($id)):?>

    <?php echo $this->renderElement('personas/menu_inicial',array('plugin' => 'pfyj'))?>
<h3>SERVICIO DE CONSULTA DE DATOS B.C.R.A.</h3>
<div class="areaDatoForm">
    <?php echo $form->create(null,array('action' => 'consultaBCRA'));?>
    <table class="tbl_form">
        <tr>
            <td>
                <?php echo $frm->number('Persona.cuit_cuil',array('label'=>'CUIT','size'=>12,'maxlength'=>11)); ?>
            </td><td><input type="submit" class="btn_consultar" value="CONSULTAR"></td>
        </tr>
    </table>
    <?php echo $form->end();?>
</div>

<?php else:?>
<?php echo $this->renderElement('personas/datos_personales',array('persona_id'=> $id,'plugin' => 'pfyj','infoSocio' => FALSE))?>
<div style="background-color: #666666;color: white;padding: 5px;margin-top: 10px;">
    <h1>CONSULTA BANCO CENTRAL</h1>    
</div>

<?php endif;?>
<?php echo $this->renderElement('personas/consulta_bcra',array('cuit'=> $cuitCuil,'plugin' => 'pfyj','historico' => TRUE, 'afip' => FALSE))?>
