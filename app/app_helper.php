<?php
/**
 *
 * @author ADRIAN TORRES
 * @package general
 * @subpackage helper
 */
App::import('Core', 'Helper');

class AppHelper extends Helper{

	var $helpers = array('Html','Ajax','Javascript','Time','Number','Form','Text','Fck','JsCalendar','Excel');


	function buildSecureLinkArgs(){
		 
		$lview   = ClassRegistry::getObject('view');
		$hashKey = $lview->loaded['session']->read('SecureGet.hashKey');
		 
		if(!$hashKey)
			$hashKey = CAKE_SESSION_STRING;

		$args    = func_get_args();
		$lid     = implode('', $args);

		$args[]  =  sha1($hashKey.$lid);

		return implode('/', $args);
	}

}
?>