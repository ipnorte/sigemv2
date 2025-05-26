<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE VENDEDORES :: ALTA '))?>
<script type="text/javascript">
	function validateForm(){
            if(!controlCUIT($('PersonaCuitCuil').getValue(),'PersonaCuitCuil','')) return false;
            return true;
	}
        
        function confirmar(){
            var msg = "*** ALTA VENDEDOR ***";
            msg = msg + "\n";
            msg = msg + "CUIT/CUIL: <?php echo $persona['Persona']['cuit_cuil']?>";
            msg = msg + "\n";
            msg = msg + "NOMBRE: <?php echo $persona['Persona']['apellido'].", ".$persona['Persona']['nombre']?>";
            msg = msg + "\n";
            msg = msg + "\n";
            msg = msg + "DAR DE ALTA?";
            return confirm(msg);
        }
</script>
<?php echo $form->create(null,array('name'=>'formAddPersona','id'=>'formAddPersona','onsubmit' => "return validateForm()",'action' => 'alta' ));?>
<div class="areaDatoForm">
    <table class="tbl_form">
        <tr>
            <td><?php echo $frm->number('Persona.cuit_cuil',array('label'=>'CUIT/CUIL del Vendedor:','size'=>12,'maxlength'=>11)); ?></td>
            <td><input type="submit" value="SIGUIENTE" id="btnProcessCUIT"/></td>            
        </tr>
    </table>
</div>
<?php echo $frm->hidden('Persona.process_cuitcuil',array('value' => 1)); ?>
<?php echo $frm->end()?>

<?php if(!empty($persona)):?>
<hr>
    <?php echo $this->renderElement('personas/datos_personales',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'))?>
<hr>
<?php echo $form->create(null,array('name'=>'formAddPersona','id'=>'formAddPersona','action' => 'alta','onsubmit' => "return confirmar()" ));?>
<?php echo $frm->hidden('Persona.id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/ventas/vendedores'))?>

<?php elseif(isset($this->data['Persona']['cuit_cuil'])): ?>
<div class="notices_error">
    <p>NO EXITE UNA PERSONA REGISTRADA BAJO EL CUIT/CUIL <?php echo $this->data['Persona']['cuit_cuil']?>.<?php echo $controles->botonGenerico("/pfyj/personas/add/","controles/add.png","Dar de Alta")?></p>
</div>
<?php endif; ?>
<?php // debug($persona);?>