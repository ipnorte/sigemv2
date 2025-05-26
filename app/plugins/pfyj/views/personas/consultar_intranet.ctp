<?php if(empty($id)):?>


<?php echo $this->renderElement('personas/menu_inicial',array('plugin' => 'pfyj'))?>
<h3>SERVICIO DE CONSULTA DE DATOS EN LA INTRANET</h3>
<div class="areaDatoForm">
    <?php echo $form->create(null,array('action' => 'consultar_intranet'));?>
    <table class="tbl_form">
        <tr>
            <td>
                <?php echo $frm->number('Persona.documento',array('label'=>'DOCUMENTO','size'=>12,'maxlength'=>11)); ?>
            </td><td><input type="submit" class="btn_consultar" value="CONSULTAR"></td>
        </tr>
    </table>
    <?php echo $form->end();?>
</div>
<?php else:?>
    <?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<?php endif;?>
<?php echo $this->renderElement('personas/consulta_intranet_info',array('plugin' => 'pfyj','informe' => $informe,'response' => $response))?>

