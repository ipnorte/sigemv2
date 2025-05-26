<?php 

/**
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php tmp_imagenes 7 -app /home/adrian/Trabajo/www/sigemv2/app/
 */

class TmpImagenesShell extends Shell{

    function main(){

		App::import('Model','Mutual.MutualProductoSolicitudDocumento');
		$oSD = new MutualProductoSolicitudDocumento();

        $sql = "select * from mutual_producto_solicitud_documentos MutualProductoSolicitudDocumento where ifnull(file_data,'') <> '';";

        $files = $oSD->query($sql);

        if(empty($files)){return;}

        if(!is_dir(WWW_ROOT . "files" . DS . "solicitudes")){mkdir(WWW_ROOT . "files" . DS . "solicitudes");}


        foreach($files as $file){

            $fileName = str_replace(' ','',$file['MutualProductoSolicitudDocumento']['file_name']);
            $solicitudNro = $file['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'];
            $fileData = $file['MutualProductoSolicitudDocumento']['file_data'];

            $DIRDATAUPLOAD = WWW_ROOT . "files" . DS . "solicitudes" . DS . $solicitudNro;
            
            if (!is_dir($DIRDATAUPLOAD)){mkdir($DIRDATAUPLOAD);}

            $this->out($DIRDATAUPLOAD.DS.$fileName);

            if(file_exists($DIRDATAUPLOAD.DS.$fileName)) unlink ($DIRDATAUPLOAD.DS.$fileName);

            if(!file_put_contents($DIRDATAUPLOAD.DS.$fileName,$fileData)){
                $this->out("ERROR AL COPIAR");
            }

            // $data = $oSD->read("file_data",$file['MutualProductoSolicitudDocumento']['id']);
            // $data['MutualProductoSolicitudDocumento']['file_data'] = NULL;
            // if(!$oSD->save($data)){
            //     $this->out("ERROR AL BORRAR CAMPO DE LA DB");
            // };
            

        }

        $sql = "update mutual_producto_solicitud_documentos set file_data = NULL where ifnull(file_data,'') <> '';";
        $oSD->query($sql);
    }

}

?>