<?php 

defined('SECURITY_ENCRYPTION_ALGORITHM') OR defined('SECURITY_ENCRYPTION_ALGORITHM') OR define('SECURITY_ENCRYPTION_ALGORITHM', 'sha256');
defined('SECURITY_ENCRYPTION_KEY') OR defined('SECURITY_ENCRYPTION_KEY') OR define('SECURITY_ENCRYPTION_KEY', 'xVOHUb1mvRJgbkZh');
defined('SECURITY_IV') OR defined('security_iv') OR define('SECURITY_IV', '2456378494765488');
defined('SECURITY_ENCRYPTION_MECHANISM') OR defined('SECURITY_ENCRYPTION_MECHANISM') OR define('SECURITY_ENCRYPTION_MECHANISM', 'aes-256-cbc');


class Crypt{

    /**
     * 
     */
    function encrypt($string){
        $output = false;
        $key = hash(SECURITY_ENCRYPTION_ALGORITHM, SECURITY_ENCRYPTION_KEY);
        $iv = substr(hash(SECURITY_ENCRYPTION_ALGORITHM, SECURITY_IV), 0, 16);
        $result = openssl_encrypt($string, SECURITY_ENCRYPTION_MECHANISM, $key, 0, $iv);
        $output = base64_encode($result);
        return $output;
    }
    
    /**
     * 
     */
    function decrypt($string){
        $output = false;
        $key = hash(SECURITY_ENCRYPTION_ALGORITHM, SECURITY_ENCRYPTION_KEY);
        $iv = substr(hash(SECURITY_ENCRYPTION_ALGORITHM, SECURITY_IV), 0, 16);
        $output = openssl_decrypt(base64_decode($string), SECURITY_ENCRYPTION_MECHANISM, $key, 0, $iv);
        return $output;
        
    }



}

?>