<?php 
$UID = intval(mt_rand());
$backgroundProgress = $this->base . "/img/controles/pagea.jpg";
$errorIcon = $this->base . "/img/controles/error.png";
$errorURL = $this->base . "/shells/asincronos/errores/$PID";
$noStop = (isset($noStop) ? $noStop : FALSE); 
?>
<style>
#progress_bar_container {
    width: 700px;
    padding: 10px;
    border-radius: 10px;  /* Bordes redondeados */
    border: 1px solid #ddd;  /* Color de borde más suave */
    background-color: #f9f9f9;  /* Fondo más claro */
    margin-top: 5px;
    margin-bottom: 10px;
    color: #000000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);  /* Sombra suave */
}

#progress_bar {
    width: 0%;  /* Inicia con la barra vacía */
    height: 25px;
    border-radius: 10px;  /* Bordes redondeados */
    background-image: linear-gradient(90deg, #a8dadc, #457b9d);  /* Degradado de color pastel azul/celeste */
    position: relative;
    text-align: center;
    color: #ffffff;
    line-height: 25px;
    font-weight: bold;
    transition: width 0.5s ease;  /* Transición suave al actualizar el ancho */
}

.progress_bar_porcentaje {
    text-align: center;
    color: #333;  /* Color de texto más oscuro */
    padding: 0px 2px;
    float: left;
    font-weight: bold;
    margin-top: 3px;
    width: 40px;
    height: 20px;
    line-height: 20px;
    margin-right: 2px;
    background-color: #e0e0e0;
    border-radius: 5px;  /* Bordes redondeados */
}

.progress_bar_mensaje {
    color: #555;  /* Color de texto más suave */
    padding-left: 3px;
    float: left;
    margin-top: 3px;
    width: 602px;
    height: 20px;
    line-height: 20px;
    overflow: hidden;
    font-family: 'Arial', sans-serif;  /* Tipografía más moderna */
    font-size: 11px;
}

#progress_bar_controles {
    margin-top: 10px;
    border-top: 1px solid #ddd;  /* Color de borde más suave */
    padding-top: 10px;
    width: 690px;
    float: left;
    text-align: right;
}

#progress_bar_titulo {
    width: 700px;
    font-size: 10px;
    margin-bottom: 5px;
    overflow: hidden;
}

#progress_bar_subtitulo {
    width: 700px;
    font-size: 9px;
    margin-bottom: 5px;
    overflow: hidden;
}

.progress_bar_errores {
    float: left;
    color: red;
    background-image: url("<?php echo $errorIcon?>");
    background-repeat: no-repeat;
    text-indent: 20px;
    padding: 3px;
    cursor: pointer;
    font-size: 9px;
    font-weight: bold;
}
</style>

<div id="progress_bar_container">
    <p id="progress_bar_titulo">#<?php echo $PID?> - <strong><?php echo $titulo?></strong></p>
    <p id="progress_bar_subtitulo"><?php echo $subtitulo?></p>
    <div id="progress_bar" class="progress_bar"></div>
    <div id="progress_bar_porcentaje_<?php echo $UID?>" class="progress_bar_porcentaje">0%</div>
    <div id="progress_bar_mensaje_<?php echo $UID?>" class="progress_bar_mensaje"></div>
    <div style="clear: both;"></div> 

    <div id="progress_bar_controles"> 
        
        
        <!-- <div id="loading_indicator" style="display: none; float: left; margin-right: 10px;">
            <img src="<?php echo $this->base; ?>/img/controles/ajax-loader.gif" alt="Cargando..." width="20" height="20" style="vertical-align: middle;"/>
            <span style="font-size: 11px; font-family: 'Arial', sans-serif; color: #555; vertical-align: middle;">Espere, Procesando...</span>
        </div> -->   
        
        
        <div id="loading_indicator" style="display: none; float: left; margin-right: 10px;">
            <span id="loading_spinner" style="display: inline-block; width: 10px; font-weight: bold; font-size: 11px; font-family: 'Arial', sans-serif; color: #555; vertical-align: middle; font-weight: bold;">-</span>
            <span style="font-size: 11px; font-family: 'Arial', sans-serif; color: #555; vertical-align: middle;">Espere, Procesando...</span>
        </div>        
        
        <!-- <span class="progress_bar_errores" id="progress_bar_errores_<?php //echo $UID?>" onclick="window.open('<?php //echo $errorURL?>','_blank');"></span> -->
        <input type="button" value="COMENZAR" id="progress_bar_start_<?php echo $UID?>"/>  
        <input type="button" value="DETENER" id="progress_bar_stop_<?php echo $UID?>" disabled/>  <!-- Botón DETENER deshabilitado inicialmente -->
        <input type="button" value="CONSULTAR" id="progress_bar_action_<?php echo $UID?>" disabled  onclick="window.open('<?php echo $url_action?>/?pid=<?php echo $PID?>','<?php echo $url_action_target?>');" />
    </div>
    <div style="clear: both;"></div>         
</div>


<script>
    
$('progress_bar_action_<?php echo $UID?>').disable();    
$('progress_bar_stop_<?php echo $UID?>').disable(); 
   
let isStopped = false;  // Bandera para saber si el proceso ha sido detenido


let spinnerInterval;  // Variable para controlar el intervalo del spinner
let spinnerChars = ['--', '\\', '|', '/'];  // Array de caracteres para la animación
let spinnerIndex = 0;  // Índice para los caracteres del spinner

function startSpinner() {
    const spinnerElem = document.getElementById('loading_spinner');
    spinnerInterval = setInterval(() => {
        spinnerElem.textContent = spinnerChars[spinnerIndex];  // Cambia el carácter del spinner
        spinnerIndex = (spinnerIndex + 1) % spinnerChars.length;  // Incrementa el índice cíclicamente
    }, 200);  // Cambiar cada 200ms para simular el movimiento
}

function stopSpinner() {
    clearInterval(spinnerInterval);  // Detener el intervalo del spinner
    const spinnerElem = document.getElementById('loading_spinner');
    spinnerElem.textContent = '-';  // Restablecer al carácter inicial
}



// Función para iniciar el proceso y habilitar el botón "DETENER"
function startProgress() {
    const progressBar = document.getElementById('progress_bar');
    const porcentajeElem = document.getElementById('progress_bar_porcentaje_<?php echo $UID?>');
    const mensajeElem = document.getElementById('progress_bar_mensaje_<?php echo $UID?>');
    const stopButton = document.getElementById('progress_bar_stop_<?php echo $UID?>');
    const startButton = document.getElementById('progress_bar_start_<?php echo $UID?>');
    const consultarBtn = document.getElementById('progress_bar_action_<?php echo $UID?>');
    
    const loadingIndicator = document.getElementById('loading_indicator'); // Indicador de loading

    // Mostrar el indicador de "loading"
    loadingIndicator.style.display = 'block'; 
    startSpinner();

    // Reiniciar la bandera de detención
    isStopped = false;

    // Deshabilitar el botón COMENZAR y habilitar el botón DETENER
    startButton.disabled = true;
    stopButton.disabled = true;
    consultarBtn.disabled = true;
    
    window.addEventListener('beforeunload', handleBeforeUnload);

    // Iniciar la solicitud de progreso
    fetch('<?php echo $url_response_server?>?PID=<?php echo $PID?>&ACTION=START')  // URL para iniciar el proceso
        .then(response => {
            const reader = response.body.getReader();
            const decoder = new TextDecoder("utf-8");
            let receivedLength = 0; // Bytes recibidos

            return reader.read().then(function processText({ done, value }) {
                if (isStopped) {
                    console.log("Proceso detenido por el usuario.");
                    return;
                }

                if (done) {
                    window.removeEventListener('beforeunload', handleBeforeUnload);
                    // console.log("Proceso completado.");
                    stopSpinner(); 
                    loadingIndicator.style.display = 'none';
                    
                    stopButton.disabled = true;  // Deshabilitar el botón DETENER al finalizar el proceso
                    consultarBtn.disabled = false;  // Habilitar el botón CONSULTAR
                    startButton.disabled = false;  // Habilitar nuevamente el botón COMENZAR
                    return;
                }

                receivedLength += value.length;
                const chunk = decoder.decode(value, { stream: true });

                // Dividimos el chunk por líneas, ya que cada línea contiene un objeto JSON separado
                const lines = chunk.split('\n').filter(line => line.trim() !== '');  // Filtra líneas vacías

                let partialChunk = ''; // Para acumular fragmentos de JSON incompletos

                lines.forEach(line => {
                    // Unimos cualquier fragmento incompleto que pueda existir de la lectura anterior
                    partialChunk += line.trim();

                    // Encontrar los objetos JSON completos usando una expresión regular
                    const jsonObjects = partialChunk.match(/{[^}]+}/g);

                    if (jsonObjects !== null) {
                        jsonObjects.forEach(jsonString => {
                            try {
                                const data = JSON.parse(jsonString);
                                if (data.msg !== undefined && data.contador !== undefined && data.porcentaje !== undefined) {
                                    // Actualizar barra de progreso
                                    const porcentaje = data.porcentaje;
                                    const msg = data.msg;
                                    progressBar.style.width = data.porcentaje + "%";
                                    porcentajeElem.textContent = data.porcentaje + "%";
                                    mensajeElem.textContent = msg;
                                }
                            } catch (e) {
                                console.error("Error al parsear JSON: ", e);
                            }
                        });

                        // Limpiar el fragmento parcial si ya fue procesado
                        partialChunk = '';
                    }
                });

                // Continuar leyendo el siguiente chunk si no está detenido
                return reader.read().then(processText);
            });
        })
        .catch(error => {
            if (!isStopped) {
                console.error("Error al obtener el progreso:", error);
            }
        });
}

function handleBeforeUnload(event) {
    event.preventDefault();
    event.returnValue = ''; // Algunos navegadores requieren que esta propiedad esté presente para mostrar la alerta
}


// Función para detener el proceso y recargar la página
function stopProgress() {
    if (confirm("¿Está seguro de que desea detener el proceso?")) {
        // Cambiar la bandera para detener el proceso de lectura
        isStopped = true;
        const mensajeElem = document.getElementById('progress_bar_mensaje_<?php echo $UID?>');
        const stopButton = document.getElementById('progress_bar_stop_<?php echo $UID?>');
        const startButton = document.getElementById('progress_bar_start_<?php echo $UID?>');
        const consultarBtn = document.getElementById('progress_bar_action_<?php echo $UID?>');
        const loadingIndicator = document.getElementById('loading_indicator');
        
        // Realizar la solicitud al servidor para detener el proceso
        fetch('<?php echo $url_response_server ?>?PID=<?php echo $PID ?>&ACTION=STOP')
        .then(response => response.text())  // Convertir la respuesta a texto
        .then(data => {
            if (data.trim() === "1") {
                // Proceso detenido exitosamente, mostrar mensaje
                alert("El proceso fue detenido exitosamente.");
                mensajeElem.textContent = 'Detenido por el usuario...';
                // Aquí puedes actualizar la interfaz de usuario si es necesario
                stopButton.disabled = true;  // Deshabilitar el botón DETENER al finalizar el proceso
                consultarBtn.disabled = true;  // Habilitar el botón CONSULTAR
                startButton.disabled = false;  // Habilitar nuevamente el botón COMENZAR 
                stopSpinner(); 
                loadingIndicator.style.display = 'none';
                
            } else {
                alert("Error al detener el proceso. Respuesta del servidor: " + data);
            }
        })
        .catch(error => {
            alert("Hubo un error al intentar detener el proceso: " + error.message);
        });
    }
}


// Asignar eventos "click" a los botones
document.getElementById('progress_bar_start_<?php echo $UID?>').addEventListener('click', startProgress);
document.getElementById('progress_bar_stop_<?php echo $UID?>').addEventListener('click', stopProgress);

</script>