
<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 * @subpackage components
 */

class SecureGetComponent extends Object {
	
     var $securedActions  = array();
     var $components      = array('Session', 'RequestHandler');
     var $securityLog     = 'security';
     var $flashMessage    = 'Security alert';
     var $flashKey        = 'sec';
     var $redirectFail    = '/';

     /* initiialization, checks parameters overidden in config */
     
     function initialize(&$controller){
          $this->_initFromConfig();

          if(!$this->Session->check('SecureGet.hashKey'))
             $this->_generateHashKey();          
     }

     /* when starting up, verify the requested action (if get) */

     function startup(&$controller){
          if((!isset($controller->params['requested']) || $controller->params['requested'] != 1) && !$this->RequestHandler->isPost())
          {
               $this->_check($controller);
          }
     }

     /* set the secured actions */
     
     function secureActions(){
          $args = func_get_args();

          if(!empty($args))
               $this->securedActions = am($this->securedActions, $args);
     }
     
     /* check the incoming action if not a post and not a request action */

     function _check(&$controller){
          // check if the action is in the array

          if(in_array($controller->params['action'], $this->securedActions))
          {
               $rc = false;

               if(isset($controller->passedArgs) && count($controller->passedArgs) > 0)
               {
                    $localargs = $controller->passedArgs;

                    // extract the last argument

                    $key = array_pop($localargs);
                    $lid  = implode('', $localargs);
                    $nval = sha1($this->_getHashKey().$lid);

                    if($nval === $key)
                       $rc = true;
               }

               if(!$rc)
                    $this->_logSecurity($controller);
	        }
     }

     /* log and flash message in case of failure */

     function _logSecurity($controller){
     	  if(!empty($this->securityLog)){
               if(!class_exists('CakeLog'))
                    uses('cake_log');

               $message = "Mismatch security arguments: ".isset($controller->params['url']['url']) ? $controller->params['url']['url'] : $controller->name."/".$controller->params['action'];
               CakeLog::write($this->securityLog, $message);
          }

          // we redirect by logout with flash message
          if(!empty($this->flashMessage))
               $controller->Session->setFlash($this->flashMessage, 'default', array(), isset($this->flashKey) ? $this->flashKey : null);
          $this->log($this->flashKey);

          $controller->redirect(!empty($this->redirectFail) ? $this->redirectFail : null, null, true);
     }

     /* initdefault from config file (if present) */

     function _initFromConfig(){
          $v = Configure::read('SecureGet');

          if($v)
          {
               $local = array('securityLog', 'redirectFail', 'flashKey', 'flasMessage');

               foreach($local as $value)
               {
                    if(isset($v[$value]))
                         $this->{$value} = $v[$value];
               }
          }
     }

     /* generate and store the hash key into the session if not present */
          
     function _generateHashKey(){
     	$CAKE_SESSION_STRING =  Configure::read('Security.salt');
          $this->Session->write('SecureGet.hashKey', sha1($CAKE_SESSION_STRING.mt_rand()));
     }
               
     /* retreive the hashKey from session (if there) */
     
     function _getHashKey(){
     	    $hashKey = $this->Session->read('SecureGet.hashKey');
     	    return  !$hashKey ? Configure::read('Security.salt') : $hashKey;
     }
}
?>
