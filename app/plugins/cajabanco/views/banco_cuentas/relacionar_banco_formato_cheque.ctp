<?php // debug($cuenta); ?>
<?php // debug($combo); ?>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION DE CUENTAS BANCARIAS :: FORMATO IMPRESION DE CHEQUES'))?>
<?php echo $this->renderElement('banco_cuentas/info_cuenta',array('plugin' => 'cajabanco','banco_cuenta_id' => $banco_cuenta_id))?>
<div class="actions">
<?php echo $form->create(null,array('action'=>'relacionar_banco_formato_cheque/' . $banco_cuenta_id));?>
<div class="areaDatoForm">
<h3>SELECCIONAR EL FORMATO DE IMPRESION DE CHEQUE</h3>

<?php echo $frm->input('BancoCuenta.formato_cheque',array('type'=>'select','options'=>$combo,'empty'=> "",'label' => "", 'selected' => $cuenta['BancoCuenta']['formato_cheque']));?>
</div>
</div>
<?php echo $frm->hidden('BancoCuenta.id',array('value' => $banco_cuenta_id)); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/cajabanco/banco_cuentas'))?>                