<?php

class BackupsController extends SeguridadAppController{
	
	var $name = 'Backups';
	var $uses = null;
	var $pathMySQL;

	
	function __construct(){
		parent::__construct();
		$this->pathMySQL = APP."plugins".DS."seguridad".DS."views".DS."backups".DS."files".DS;
	}
	
	function beforeFilter(){
		$this->Seguridad->allow(array('download'));
		parent::beforeFilter();
	}	
	
	
        function index() {
            $path = $this->pathMySQL;
            $ficheros = array_diff(scandir($path), array('..', '.'));

            // Filtrar solo archivos válidos (evitando "empty" y otros que no sean archivos)
            $ficheros = array_filter($ficheros, function($file) use ($path) {
                return is_file($path . DS . $file) && $file !== 'empty';
            });

            // Ordenar por fecha de modificación descendente
            usort($ficheros, function($a, $b) use ($path) {
                return filemtime($path . DS . $b) - filemtime($path . DS . $a);
            });

            $this->set('pathMySQL', $this->pathMySQL);
            $this->set('ficheros', $ficheros);
        }

	function download($fileName){
		
            // $filename = $this->pathMySQL . $fileName;
            // header("Content-length:".filesize($filename));
            // header('Content-Type: application/octet-stream'); // ZIP file
            // header('Content-Disposition: attachment; filename="'.$fileName.'"');
            // header('Content-Transfer-Encoding: binary');
            // ob_end_clean();
            // readfile($filename);
            // exit();  
            
            if (is_file($this->pathMySQL.$fileName))
            {

                $fileInfo = pathinfo($this->pathMySQL.$fileName); 
                $fileName  = $fileInfo['basename']; 
                $fileExtnesion   = $fileInfo['extension']; 
                $contentType = "application/octet-stream";
 
                $this->sendHeaders($this->pathMySQL.$fileName, $contentType,$fileName);
                $chunkSize = 1024 * 1024;
                $handle = fopen($this->pathMySQL.$fileName, 'rb');
                while (!feof($handle))
                {
                    $buffer = fread($handle, $chunkSize);
                    echo $buffer;
                    ob_flush();
                    flush();
                }
                fclose($handle);
                exit;
            }            

        
//        $size = filesize($filePath);
//        $offset = 0;
//        $length = $size;
//        $contentType = "application/x-gzip";
//        $matches = array();
//        //HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
//        if(isset($_SERVER['HTTP_RANGE']))
//        {
//            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
//            $offset = intval($matches[1]);
//            $length = intval($matches[2]) - $offset;
//            $fhandle = fopen($filePath, 'r');
//            fseek($fhandle, $offset); // seek to the requested offset, this is 0 if it's not a partial content request
//            $data = fread($fhandle, $length);
//            fclose($fhandle);
//            header('HTTP/1.1 206 Partial Content');
//            header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $size);
//        }//HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
//        //USUAL HEADERS FOR DOWNLOAD
//        header("Content-Disposition: attachment;filename=".$fileName);
//        header('Content-Type: '.$contentType);
//        header("Accept-Ranges: bytes");
//        header("Pragma: public");
//        header("Expires: -1");
//        header("Cache-Control: no-cache");
//        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
//        header("Content-Length: ".filesize($filePath));
//        $chunksize = 8 * (1024 * 1024); //8MB (highest possible fread length)
//        if ($size > $chunksize)
//        {
//          $handle = fopen($_FILES["file"]["tmp_name"], 'rb');
//          $buffer = '';
//          while (!feof($handle) && (connection_status() === CONNECTION_NORMAL)) 
//          {
//            $buffer = fread($handle, $chunksize);
//            print $buffer;
//            ob_flush();
//            flush();
//          }
//          if(connection_status() !== CONNECTION_NORMAL)
//          {
//            echo "Connection aborted";
//          }
//          fclose($handle);
//        }
//        else 
//        {
//          ob_clean();
//          flush();
//          readfile($filePath);
//        }        
        
//		header('Content-Description: Backup File Transfer');
//		header('Content-Type: application/x-gzip');
//		header('Content-Disposition: attachment; filename='.basename($file));
//		header('Content-Transfer-Encoding: base64');
//		header('Expires: 0');
//		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//		header('Pragma: public');
//		header('Content-Length: ' . filesize($file));
//        ob_clean();
//        flush();
//        readfile($file);
//        exit;
//		$gestor = fopen($file, "r");
//		if ($gestor){
//			while (($búfer = fgets($gestor, 4096)) !== false){
//				echo $búfer;
//			}
//			if (!feof($gestor)){
//				echo "Error: fallo inesperado de fgets()\n";
//			}
//			fclose($gestor);
//		}
//		exit;		
	}

    function downloadFiles($filePath) 
    {     

        // set_time_limit(0); 
        // ini_set('memory_limit', '512M'); 
        // /*set your download file path here.   */ 
        // $filePath = "E:/MyFiles/Software/XYZ.rar"; 
        // /* calls download function  */
        // downloadFiles($filePath); 

        if(!empty($filePath)) 
        { 
            $fileInfo = pathinfo($filePath); 
            $fileName  = $fileInfo['basename']; 
            $fileExtnesion   = $fileInfo['extension']; 

            $default_contentType = "application/octet-stream"; 
            // $content_types_list = mimeTypes(); 
            // if (array_key_exists($fileExtnesion, $content_types_list))  
            // { 
            //     $contentType = $content_types_list[$fileExtnesion]; 
            // } 
            // else 
            // { 
            //     $contentType =  $default_contentType; 
            // }
            
            $contentType = $default_contentType;

            if(file_exists($filePath)) 
            { 
                $size = filesize($filePath); 
                $offset = 0; 
                $length = $size; 
                if(isset($_SERVER['HTTP_RANGE'])) 
                { 
                    preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches); 
                    $offset = intval($matches[1]); 
                    $length = intval($matches[2]) - $offset; 
                    $fhandle = fopen($filePath, 'r'); 
            fseek($fhandle, $offset); 
                    $data = fread($fhandle, $length); 
                    fclose($fhandle); 
                    header('HTTP/1.1 206 Partial Content'); 
                    header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $size); 
                }
                //Heasers for download
                header("Content-Disposition: attachment;filename=".$fileName); 
                header('Content-Type: '.$contentType); 
                header("Accept-Ranges: bytes"); 
                header("Pragma: public"); 
                header("Expires: -1"); 
                header("Cache-Control: no-cache"); 
                header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0"); 
                header("Content-Length: ".filesize($filePath)); 
                $chunksize = 8 * (1024 * 1024); //8MB (highest possible fread length) 
                if ($size > $chunksize) 
                { 
                  $handle = fopen($_FILES["file"]["tmp_name"], 'rb'); 
                  $buffer = ''; 
                  while (!feof($handle) && (connection_status() === CONNECTION_NORMAL))  
                  { 
                    $buffer = fread($handle, $chunksize); 
                    print $buffer; 
                    ob_flush(); 
                    flush(); 
                  } 
                  if(connection_status() !== CONNECTION_NORMAL) 
                  { 
                    echo "Connection aborted"; 
                  } 
                  fclose($handle); 
                } 
                else  
                { 
                  ob_clean(); 
                  flush(); 
                  readfile($filePath); 
                } 
             } 
             else 
             { 
               echo 'File does not exist!'; 
             } 
        } 
        else 
        { 
            echo 'There is no file to download!'; 
        } 
    } 

    /**
     * Copy remote file over HTTP one small chunk at a time.
     *
     * @param $infile The full URL to the remote file
     * @param $outfile The path where to save the file
     */
    function copyfile_chunked($infile, $outfile) {
        $chunksize = 10 * (1024 * 1024); // 10 Megs

        /**
         * parse_url breaks a part a URL into it's parts, i.e. host, path,
         * query string, etc.
         */
        $parts = parse_url($infile);
        $i_handle = fsockopen($parts['host'], 80, $errstr, $errcode, 5);
        $o_handle = fopen($outfile, 'wb');

        if ($i_handle == false || $o_handle == false) {
            return false;
        }

        if (!empty($parts['query'])) {
            $parts['path'] .= '?' . $parts['query'];
        }

        /**
         * Send the request to the server for the file
         */
        $request = "GET {$parts['path']} HTTP/1.1\r\n";
        $request .= "Host: {$parts['host']}\r\n";
        $request .= "User-Agent: Mozilla/5.0\r\n";
        $request .= "Keep-Alive: 115\r\n";
        $request .= "Connection: keep-alive\r\n\r\n";
        fwrite($i_handle, $request);

        /**
         * Now read the headers from the remote server. We'll need
         * to get the content length.
         */
        $headers = array();
        while(!feof($i_handle)) {
            $line = fgets($i_handle);
            if ($line == "\r\n") break;
            $headers[] = $line;
        }

        /**
         * Look for the Content-Length header, and get the size
         * of the remote file.
         */
        $length = 0;
        foreach($headers as $header) {
            if (stripos($header, 'Content-Length:') === 0) {
                $length = (int)str_replace('Content-Length: ', '', $header);
                break;
            }
        }

        /**
         * Start reading in the remote file, and writing it to the
         * local file one chunk at a time.
         */
        $cnt = 0;
        while(!feof($i_handle)) {
            $buf = '';
            $buf = fread($i_handle, $chunksize);
            $bytes = fwrite($o_handle, $buf);
            if ($bytes == false) {
                return false;
            }
            $cnt += $bytes;

            /**
             * We're done reading when we've reached the conent length
             */
            if ($cnt >= $length) break;
        }

        fclose($i_handle);
        fclose($o_handle);
        return $cnt;
    }
    
    
    function sendHeaders($file, $type, $name = NULL)
    {
        if (empty($name))
        {
            $name = basename($file);
        }
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="'.$name.'";');
        header('Content-Type: ' . $type);
        header('Content-Length: ' . filesize($file));
    }    
	
}


// $url  = 'http://www.example.com/a-large-file.zip';
// $path = $_SERVER['DOCUMENT_ROOT'] . '/downloads/a-large-file.zip';

// $fp = fopen($path, 'w');

// $ch = curl_init($url);
// curl_setopt($ch, CURLOPT_FILE, $fp);

// $data = curl_exec($ch);

// curl_close($ch);
// fclose($fp);

?>
