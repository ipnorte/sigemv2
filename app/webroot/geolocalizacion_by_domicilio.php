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
$domilicio =  unserialize(base64_decode($_GET['params']));
$domilicio_map = $domilicio['domicilio_calle']." ".$domilicio['domicilio_numero'].",".$domilicio['domicilio_localidad'].",".$domilicio['domicilio_provincia'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Geocoding service</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        margin: 0 auto;
        padding: 0;
      }
      #map {
        height: 100%;
        width: 100%;
      }
#floating-panel {
  position: absolute;
  top: 10px;
  left: 5%;
  z-index: 5;
  background-color: #fff;
  padding: 5px;
  border: 1px solid #999;
  text-align: center;
  font-family: 'Roboto','sans-serif';
  line-height: 30px;
  padding-left: 10px;
}

    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>
		//-31.414186, -64.186289
		//ALVEAR 240,CRUZ DEL EJE
function initMap() {
    document.getElementById("mensajeErrorMaps").style.visibility="hidden";
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 15,
    center: {lat: <?php echo $LATITUD?>, lng: <?php echo $LONGITUD?>}
  });
  var geocoder = new google.maps.Geocoder();
//  var infowindow = new google.maps.InfoWindow;

	geocodeAddress(geocoder, map);
  //document.getElementById('submit').addEventListener('click', function() {
  //  geocodeAddress(geocoder, map);
  //});
}

function geocodeAddress(geocoder, resultsMap) {
  var address = document.getElementById('address').value;
  geocoder.geocode({'address': address}, function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      resultsMap.setCenter(results[0].geometry.location);
      var marker = new google.maps.Marker({
        map: resultsMap,
        position: results[0].geometry.location
      });
//      infowindow.setContent(results[1].formatted_address);
//      infowindow.open(map, marker);
    } else {
        document.getElementById("mensajeErrorMaps").style.visibility="visible";
//      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}
    </script>
    <input id="address" type="hidden" value="<?php echo $domilicio_map?>">
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLE_API_KEY?>&signed_in=false&callback=initMap" async defer></script>
    <div id="mensajeErrorMaps" style="background: red;color: white; padding: 10px;width: 95%;position: absolute;top: 20px;text-justify: auto; ">
        El domicilio "<?php echo $domilicio_map?>" NO pudo ser geolocalizado
    </div>
  </body>
</html>
