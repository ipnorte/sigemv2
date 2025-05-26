<?php 

$INI_FILE = parse_ini_file(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR.'mutual.ini', true);
if(isset($INI_FILE['general']['domi_fiscal_latitud']) &&  !empty($INI_FILE['general']['domi_fiscal_latitud']) && isset($INI_FILE['general']['domi_fiscal_longitud']) &&  !empty($INI_FILE['general']['domi_fiscal_longitud']) && isset($INI_FILE['general']['google_api_key']) &&  !empty($INI_FILE['general']['google_api_key'])){
    $LATITUD = $INI_FILE['general']['domi_fiscal_latitud'];
    $LONGITUD = $INI_FILE['general']['domi_fiscal_longitud'];
    $GOOGLE_API_KEY = $INI_FILE['general']['google_api_key'];
}else{
    echo "ERROR DE CONFIGURACION DEL SERVICIO GOOGLE MAPS";
    exit;
}

$coods =  base64_decode($_GET['params']);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Reverse Geocoding</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }
      #floating-panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        width: 350px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
      }
      #latlng {
        width: 225px;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>
      function initMap() {
        var input = "<?php echo $coods?>";
        var latlngStr = input.split(',', 2);
        var myLatLng = {lat: parseFloat(latlngStr[0]), lng: parseFloat(latlngStr[1])};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: myLatLng
        });
        var marker = new google.maps.Marker({
          position: myLatLng,
          map: map
        });

      }
    </script>
    <!--<input id="latlng" type="hidden" value="<?php // echo $coods?>">-->
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLE_API_KEY?>&signed_in=false&callback=initMap">
    </script>
<!--    <div id="mensajeErrorMaps" style="background: red;color: white; padding: 10px;width: 95%;position: absolute;top: 20px;text-justify: auto; ">
        Las coordenadas (LAT,LONG) "<?php // echo $coods?>" NO pudieron ser geolocalizadas
    </div>-->
  </body>
</html>
