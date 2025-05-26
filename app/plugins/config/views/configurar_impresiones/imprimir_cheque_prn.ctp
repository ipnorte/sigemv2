    <?php
// debug($chqConfiguracion);
debug(basename($archivo));
// $down = 'C:/Temporal/';
$arch = basename($archivo);
// exit;


// Rutas al archivo (local y FTP)
$local_file = 'c:/temporal/' . $arch; //Nombre archivo en nuestro PC
$server_file = $arch; //Nombre archivo en FTP

debug($local_file);

// Establecer la conexión
$ftp_server='mutualaman.com';
$ftp_user_name='backup@mutualaman.com';
$ftp_user_pass='backup*180407';
$conn_id = ftp_connect($ftp_server);

if ((!$conn_id)) {
		echo "Fallo en la conexión \n"; die;
	} else {
		echo "Conectado con el Server.\n";
	}

// Loguearse con usuario y contraseña
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
if ((!$login_result)) {
		echo "Fallo en el Login\n"; die;
	} else {
		echo "Logeado al Servidor\n";
	}

ftp_pasv($conn_id, true);
echo "<br> Cambio a modo pasivo<br />";

//obtenemos una lista con los archivos del servidor
$files = ftp_nlist($conn_id, '.');
foreach ($files as $file) {
echo $file . "\n";
}

// Descarga el $server_file y lo guarda en $local_file
if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
echo "Se descargado el archivo con éxito\n";
} else {
echo "Ha ocurrido un error\n";
}

// Cerrar la conexión
ftp_close($conn_id);



// header('Content-Description: File Transfer');
// header('Content-Type: application/force-download');
// header('Content-Disposition: attachment; filename='.$arch);
// header('Content-Transfer-Encoding: binary');
// header('Expires: 0');
// header('Cache-Control: must-revalidate');
// header('Pragma: public');
// header('Content-Length: ' . filesize($archivo));
// ob_clean();
// flush();
// readfile($archivo);

// exit;


// header('Content-Description: File Transfer');
// header('Content-Type: application/octet-stream');
// header('Content-Disposition: attachment; filename='.$arch);
// header('Content-Transfer-Encoding: binary');
// header('Expires: 0');
// header('Cache-Control: must-revalidate');
// header('Pragma: public');
// header('Content-Length: ' . filesize($archivo));
// ob_clean();
// flush();
// readfile($archivo);

// exit;
