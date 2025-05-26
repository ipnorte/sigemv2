<?php 
echo $html->css('creditCard');
echo $javascript->link('aplicacion/creditCard');

$meses = array();
for($i=1;$i<=12;$i++){
    $mes = str_pad($i,2,'0',STR_PAD_LEFT);
    $meses[$mes] = $mes;
}
$anios = array();
for($i=intval(date('y'));$i<= (intval(date('y')) + 40);$i++){
    $anio = str_pad($i,2,'0',STR_PAD_LEFT);
    $anios[$anio] = $anio;
}


?>

<script type="text/javascript">
   
/*   function validarVtoTarjeta(){
   		ret = true;
   		$('TarjetaDebitoCardExpirationMonth').removeClassName('form-error');
   		$('TarjetaDebitoCardExpirationYear').removeClassName('form-error'); 
   		var ccNumb = document.getElementById('TarjetaDebitoCardNumber').value;
   		
        if(!isNaN(ccNumb) && ccNumb !== ''){
        
           var mes = document.getElementById('TarjetaDebitoCardExpirationMonth').value;
           var anio = document.getElementById('TarjetaDebitoCardExpirationYear').value;
           ret = controlVigenciaTarjetaDebito(mes,anio,3);  
           if(!ret){
           		$('TarjetaDebitoCardExpirationMonth').focus();
           		$('TarjetaDebitoCardExpirationMonth').addClassName('form-error');
           		$('TarjetaDebitoCardExpirationYear').addClassName('form-error');
           }         
        
        }
        return ret;   		  
  
   }
*/   
</script>

<H3>Tarjeta de Débito</H3>
<?php if(empty($this->data['PersonaBeneficio']['tarjeta_numero'])):?>
<table class="tbl_form">
    <tr>
        <td colspan="4"><?php echo $frm->input('TarjetaDebito.card_holder_name',array('label'=>'TITULAR *','size'=>60)); ?></td>
    </tr>
    <tr>
        <td>
            <?php echo $frm->number('TarjetaDebito.card_number',array('div' => false,'label'=>'NUMERO *','size'=>19,'maxlength'=>19, 'onblur' => 'validateNumber(this)')); ?>
        </td><td><div id="card_icon"></div></td>
        <td>
            <?php // echo $frm->number('TarjetaDebito.card_expiration',array('label'=>'VIGENCIA (MMAA)','size'=>4,'maxlength'=>4)); ?>
            <?php echo $frm->input('TarjetaDebito.card_expiration_month',array('div' => false,'label' => 'VENCE EL *','type' => 'select','options' => $meses, 'selected' => date('m')))?>
            <?php echo $frm->input('TarjetaDebito.card_expiration_year',array('div' => false,'label' => '/','type' => 'select','options' => $anios, 'selected' => date('y')))?>
        </td>
        <td><?php echo $frm->number('TarjetaDebito.security_code',array('div' => false,'label'=>'CODIGO SEG *','size'=>3,'maxlength'=>3)); ?></td>
    </tr>
</table>
<?php else:?>
    <table class="tbl_form">
        <tr>
            <td>TITULAR</td><td><strong><?php echo $this->data['PersonaBeneficio']['tarjeta_titular']?></strong></td>
            <td>NUMERO</td><td><strong><?php echo $this->data['PersonaBeneficio']['tarjeta_numero']?></strong></td>
            <td><?php echo $controles->btnModalBox(array('title' => 'VER TARJETA DE DEBITO','img'=> 'vcard.png','texto' => '','url' => '/pfyj/persona_beneficios/tarjeta/'.$this->data['PersonaBeneficio']['id'],'h' => 450, 'w' => 850))?></td>
        </tr>
    </table>
<?php endif;?>



