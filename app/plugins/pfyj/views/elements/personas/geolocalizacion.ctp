<?php
$persona = $this->requestAction('/pfyj/personas/get_persona/'.$persona_id);
if(empty($persona)){
    echo "*** NO SE PUDO OBTENER INFORMACION DE LA PERSONA ***";
    exit;
}
//$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
//if(isset($INI_FILE['general']['google_api_key']) &&  !empty($INI_FILE['general']['google_api_key'])){
//    $GOOGLE_API_KEY = $INI_FILE['general']['google_api_key'];
//}else{
//    echo "*** ERROR DE CONFIGURACION DEL SERVICIO GOOGLE MAPS ***";
//    exit;
//}
//debug($persona);
//exit;
$GOOGLE_API_KEY = 'AIzaSyDO42vzZW7i_kDvxsBeC-Q2gf30x7mSRPA';
?>
<?php if(!empty($persona['Persona']['maps_latitud']) && !empty($persona['Persona']['maps_longitud'])):?>

<!DOCTYPE html>
<html>
  <head>
	<title>
		<?php echo Configure::read('APLICACION.nombre')?>
		&nbsp;@v<?php echo Configure::read('APLICACION.version')?>
	</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      
      /* The popup bubble styling. */
      .popup-bubble {
        /* Position the bubble centred-above its parent. */
        position: absolute;
        top: 0;
        left: 0;
        transform: translate(-50%, -100%);
        /* Style the bubble. */
        background-color: white;
        padding: 5px;
        border-radius: 5px;
        font-family: sans-serif;
        overflow-y: auto;
        max-height: 60px;
        box-shadow: 0px 2px 10px 1px rgba(0,0,0,0.5);
/*        background: #660000;
        color: wheat;*/
      }
      /* The parent of the bubble. A zero-height div at the top of the tip. */
      .popup-bubble-anchor {
        /* Position the div a fixed distance above the tip. */
        position: absolute;
        width: 100%;
        bottom: /* TIP_HEIGHT= */ 8px;
        left: 0;
      }
      /* This element draws the tip. */
      .popup-bubble-anchor::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        /* Center the tip horizontally. */
        transform: translate(-50%, 0);
        /* The tip is a https://css-tricks.com/snippets/css/css-triangle/ */
        width: 0;
        height: 0;
        /* The tip is 8px high, and 12px wide. */
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: /* TIP_HEIGHT= */ 8px solid white;
        
      }
      /* JavaScript will position this div at the bottom of the popup tip. */
      .popup-container {
        cursor: auto;
        height: 0;
        position: absolute;
        /* The max width of the info window. */
        width: 550px;
      }      
      
    </style>
  </head>
  <body>
    <div id="map"></div>
      <div id="content">
        <p style="font-weight: bold;"><?php echo $persona['Persona']['tdoc_ndoc_apenom']?></p>
        <p><?php echo $persona['Persona']['domicilio']?></p>
        
    </div>    
      
    <script>
      // This example displays a map with the language and region set
      // to Japan. These settings are specified in the HTML script element
      // when loading the Google Maps JavaScript API.
      // Setting the language shows the map in the language of your choice.
      // Setting the region biases the geocoding results to that region.
      var map, popup, Popup;
      
function initMap() {
  var myLatLng = {lat: <?php echo $persona['Persona']['maps_latitud']?>, lng: <?php echo $persona['Persona']['maps_longitud']?>};

  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 16,
    center: myLatLng
  });

  Popup = createPopupClass();
  popup = new Popup(
      new google.maps.LatLng(<?php echo $persona['Persona']['maps_latitud']?>, <?php echo $persona['Persona']['maps_longitud']?>),
      document.getElementById('content'));
  popup.setMap(map);
  
//          var marker = new google.maps.Marker({
//          position: myLatLng,
//          map: map,
//          title: 'Hello World!'
//        });

//  var marker = new google.maps.Marker({
//    position: myLatLng,
//    map: map,
//    title: 'Hello World!'
//  });
}      

/**
 * Returns the Popup class.
 *
 * Unfortunately, the Popup class can only be defined after
 * google.maps.OverlayView is defined, when the Maps API is loaded.
 * This function should be called by initMap.
 */
function createPopupClass() {
  /**
   * A customized popup on the map.
   * @param {!google.maps.LatLng} position
   * @param {!Element} content The bubble div.
   * @constructor
   * @extends {google.maps.OverlayView}
   */
  function Popup(position, content) {
    this.position = position;

    content.classList.add('popup-bubble');

    // This zero-height div is positioned at the bottom of the bubble.
    var bubbleAnchor = document.createElement('div');
    bubbleAnchor.classList.add('popup-bubble-anchor');
    bubbleAnchor.appendChild(content);

    // This zero-height div is positioned at the bottom of the tip.
    this.containerDiv = document.createElement('div');
    this.containerDiv.classList.add('popup-container');
    this.containerDiv.appendChild(bubbleAnchor);

    // Optionally stop clicks, etc., from bubbling up to the map.
    google.maps.OverlayView.preventMapHitsAndGesturesFrom(this.containerDiv);
  }
  // ES5 magic to extend google.maps.OverlayView.
  Popup.prototype = Object.create(google.maps.OverlayView.prototype);

  /** Called when the popup is added to the map. */
  Popup.prototype.onAdd = function() {
    this.getPanes().floatPane.appendChild(this.containerDiv);
  };

  /** Called when the popup is removed from the map. */
  Popup.prototype.onRemove = function() {
    if (this.containerDiv.parentElement) {
      this.containerDiv.parentElement.removeChild(this.containerDiv);
    }
  };

  /** Called each frame when the popup needs to draw itself. */
  Popup.prototype.draw = function() {
    var divPosition = this.getProjection().fromLatLngToDivPixel(this.position);

    // Hide the popup when it is far out of view.
    var display =
        Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ?
        'block' :
        'none';

    if (display === 'block') {
      this.containerDiv.style.left = divPosition.x + 'px';
      this.containerDiv.style.top = divPosition.y + 'px';
    }
    if (this.containerDiv.style.display !== display) {
      this.containerDiv.style.display = display;
    }
  };

  return Popup;
}

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLE_API_KEY?>&callback=initMap"
    async defer>
    </script>
  </body>
</html>
    
    
<?php else:?>
<div class="notices_error2">
    EL DOMICILIO <strong><?php echo $persona['Persona']['domicilio']?></strong> NO SE ENCUENTRA GEOLOCALIZADO.
    <br/>
    Deber&aacute; indicar los valores de coordenadas geogr&aacute;ficas (LATITUD / LONGITUD) en los datos personales.
</div>
<?php // debug($persona);?>
<?php endif; ?>
