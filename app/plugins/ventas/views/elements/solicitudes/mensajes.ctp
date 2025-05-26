<small>
<?php if($session->check('Message.NOTICE')):?>
    <div class="alert alert-dismissible alert-warning">
      <h5 class="alert-heading">Atención!</h5>
      <p class="mb-0"><?php echo $session->flash("NOTICE");?></p>
      <?php $session->del('Message.NOTICE')?>
    </div>
<?php endif; ?>
<?php if($session->check('Message.ERROR')):?>
    <div class="alert alert-dismissible alert-danger">
      <h5 class="alert-heading">Atención!</h5>
      <p class="mb-0"><?php echo $session->flash("ERROR");?></p>
      <?php $session->del('Message.ERROR')?>
    </div>
<?php endif; ?>
<?php if($session->check('Message.OK')):?>
    <div class="alert alert-dismissible alert-success">
      <p class="mb-0"><?php echo $session->flash("OK");?></p>
      <?php $session->del('Message.OK')?>
    </div>
<?php endif; ?>
<?php if($session->check('Message.ERRORES')):?>
    <div class="alert alert-dismissible alert-danger">
        <?php if(isset($titulo)):?>
        <h5 class="alert-heading"><?php echo $titulo?></h5>
        <?php endif;?>
        <?php if(!empty($errores)):?>
        <ul>
            <?php foreach($errores as $error):?>
            <li><?php echo $error?></li>
            <?php endforeach;?>            
        </ul>        
      <?php endif;?>
      <?php $session->del('Message.ERRORES')?>  
    </div>
<?php endif; ?>
</small>
