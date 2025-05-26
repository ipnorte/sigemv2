<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package personas
 * @subpackage model
 */

if (!defined('MODULO_V1')){
    $file = parse_ini_file(CONFIGS.'mutual.ini', true);
    define('MODULO_V1',(isset($file['general']['habilitar_modulo_v1']) && !empty($file['general']['habilitar_modulo_v1']) ? TRUE : FALSE));
}

class PfyjAppModel extends AppModel{
    
}
?>