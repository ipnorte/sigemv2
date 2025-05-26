<?php 

$enableAPPLET = false;

$mensaje_error = "";
$error = false;

if(!isset($process)){
	$mensaje_error = "No esta seteado el nombre del shell a ejecutar!.";
	$error = true;	
} 
if(!isset($accion)){
	$mensaje_error = "No esta seteada la accion.";
	$error = true;		
}
if(!isset($bloqueado)){
	$bloqueado = '0';
}
if(!isset($target)){
	$target = 'blank';
}
if(!isset($btn_label)){
	$btn_label = 'Salida Proceso Asincrono';
}
if(!isset($titulo)){
	$titulo = 'PROCESO ASINCRONO';
}
if(!isset($subtitulo)){
	$subtitulo = '';
}
if(!isset($p1)){
	$p1 = '';
}
if(!isset($p2)){
	$p2 = '';
}if(!isset($p3)){
	$p3 = '';
}
if(!isset($p4)){
	$p4 = '';
}
if(!isset($p5)){
	$p5 = '';
}
if(!isset($p6)){
	$p6 = '';
}
if(!isset($p7)){
	$p7 = '';
}
if(!isset($p8)){
	$p8 = '';
}
if(!isset($p9)){
	$p9 = '';
}
if(!isset($p10)){
	$p10 = '';
}
if(!isset($p11)){
	$p11 = '';
}
if(!isset($p12)){
	$p12 = '';
}
if(!isset($p13)){
	$p13 = '';
}
if(!isset($txt1)){
	$txt1 = '';
}
if(!isset($txt2)){
	$txt2 = '';
}
if(!isset($noStop)){
	$noStop = FALSE;
}

if(!$error):
if(!isset($pUID) || empty($pUID)){
    $pUID = $this->requestAction("/shells/asincronos/crear/process:$process/action:$accion/target:$target/btn_label:$btn_label/titulo:$titulo/subtitulo:$subtitulo/p1:$p1/p2:$p2/p3:$p3/p4:$p4/p5:$p5/p6:$p6/p7:$p7/p8:$p8/p9:$p9/p10:$p10/p11:$p10/p12:$p12/p13:$p13/txt1:$txt1/txt2:$txt2");
}
$asincrono = $this->requestAction('/shells/asincronos/getAsincrono/'.$pUID);

//debug($asincrono);

endif;
?>
<script src="<?php echo $this->base ?>/js/jquery-3.4.1.min.js"></script>
<script src="<?php echo $this->base ?>/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
  <div class="card">
      <div class="card-header">
          #<?php echo $asincrono['pid'];?>&nbsp;-&nbsp;
          <?php echo $asincrono['titulo'];?>
          &nbsp;|&nbsp;<small><?php echo $asincrono['subtitulo'];?></small></div>
      <div class="card-body">
          <div class="row mb-1">
                <div class="col-8">
                <div class="progress" style="height: 40px;">
                  <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 0%;font-size: 12px; font-weight: bold;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>                    
                </div>
                <div class="col-4">
                    
                    <!--<button id="btnStop" class="btn btn-primary" onclick="btnStopClick()"><i class="fas fa-stop-circle"></i>&nbsp;Detener</button>-->
                    <a id="btnDownload" class="btn btn-primary" target="_blank" href="<?php echo FULL_BASE_URL . $this->base . $asincrono['action_do'] ."/?pid=" . $asincrono['pid']?>"><i class="fas fa-download"></i>&nbsp;Descargar</a>
                </div>              
          </div>
            <div class="row mb-1 ">               
                <div class="col-12 text-primary"><i id="spinner" class="fas fa-sync-alt fa-spin"></i>&nbsp;&nbsp;<span id="mensajesAsincronos"></span></div>
            </div>          
      </div>
  </div>
  
 
<script>
var urlSTART = "<?php echo FULL_BASE_URL . $this->base . "/asincrono.php?PID=" . $asincrono['pid']."&ACTION=START"?>";
var urlSTOP = "<?php echo FULL_BASE_URL . $this->base . "/asincrono.php?PID=" . $asincrono['pid']."&ACTION=STOP"?>";
var urlSTATUS = "<?php echo FULL_BASE_URL . $this->base . "/asincrono.php?PID=" . $asincrono['pid']."&ACTION=STATUS"?>";
    
$( document ).ready(function() {
    
    $('#btnDownload').hide();
//    $( "#spinner" ).hide();
//    $('#btnStop').show();
//    console.log( urlSTART + " * " + urlSTOP);
    $.ajax({
        url: "<?php echo FULL_BASE_URL . $this->base . "/asincrono.php"?>",
        data: {
            PID: <?php echo $asincrono['pid']?>,
            ACTION: 'START'
        },
        type: "GET",
        dataType : "json"
    }).always(function( xhr, status ) {
//        $( "#spinner" ).show();
        checkStatus(<?php echo $asincrono['pid']?>);
    });    
}); 

function btnStopClick(){
    $.ajax({
        url: "<?php echo FULL_BASE_URL . $this->base . "/asincrono.php"?>",
        data: {
            PID: <?php echo $asincrono['pid']?>,
            ACTION: 'STOP'
        },
        type: "GET",
        dataType : "json"
    }).always(function( xhr, status ) {
        $('#btnDownload').hide();
//        checkStatus(<?php // echo $asincrono['pid']?>);
    });
}

function checkStatus(pid){
    $.ajax({
        url: "<?php echo FULL_BASE_URL . $this->base . "/asincrono.php"?>",
        data: {
            PID: <?php echo $asincrono['pid']?>,
            ACTION: 'STATUS'
        },
        type: "GET",
        dataType : "json"
    }).done(function( json ) {
        var p = parseInt(json.PORCENTAJE);
        $('#mensajesAsincronos').html((json.ESTADO !== 'S' ?  p + "% | " + json.MENSAJE : '*** DETENIDO POR EL USUARIO ***'));
//        $( "#progressbar" ).progressbar({
//          value: parseInt(json.PORCENTAJE)
//        }); 
//        $(".progress-bar").css("width", p + "%").text(p + " %");
        $(".progress-bar").css("width", p + "%");
        if(parseInt(json.PORCENTAJE) < 100 &&  json.ESTADO !== 'S'){
            setTimeout(5);
            setTimeout(function(){ 
                checkStatus(<?php echo $asincrono['pid']?>); 
            }, 1000);            
        }else{
            $( ".progress-bar" ).removeClass( "bg-info progress-bar-striped" ).addClass( "bg-success" );
//            $('#btnStop').hide();
            $('#btnDownload').show();
            $( "#spinner" ).removeClass( "fa-spin" );
//            $( "#spinner" ).hide();
            
        }    
            
    });


}
 

</script>