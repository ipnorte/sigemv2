<?php
/**
* 	adrian
*	28/09/2010
*
*/

App::import('Vendor', 'IPReflectionClass', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPReflectionClass.class.php'));
App::import('Vendor', 'IPReflectionCommentParser', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPReflectionCommentParser.class.php'));
App::import('Vendor', 'IPXMLSchema', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPXMLSchema.class.php'));
App::import('Vendor', 'IPReflectionMethod', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPReflectionMethod.class.php'));
App::import('Vendor', 'WSDLStruct', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'WSDLStruct.class.php'));
App::import('Vendor', 'WSDLException', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'WSDLException.class.php'));

/**
 * Class SoapComponent
 *
 * Generate WSDL and handle SOAP calls
 */
class SoapComponent extends Component
{
	var $params = array();

	function initialize(&$controller)
	{
            $this->params = $controller->params;
            $this->controller =& $controller;             
	}
	
	/**
	 * Get WSDL for specified model.
	 *
	 * @param string $modelId : model name in camel case
	 * @param string $serviceMethod : method of the controller that will handle SOAP calls
	 */
	function getWSDL($modelId, $serviceMethod = 'call')
	{
		$modelClass = $this->__getModelClass($modelId);
		$expireTime = '+1 year';
		$cachePath = $modelClass . '.wsdl';
		
		// Check cache if exist
		$wsdl = cache($cachePath, null, $expireTime);
		// If DEBUG > 0, compare cache modified time to model file modified time
		if ((Configure::read() > 0) && (! is_null($wsdl))) {

			$cacheFile = CACHE . $cachePath;
			if (is_file($cacheFile)) {
				$modelMtime = filemtime($this->__getModelFile($modelId));
				$cacheMtime = filemtime(CACHE . $cachePath);
				if ($modelMtime > $cacheMtime) {
					$wsdl = null;
				}
			}

		}
		$wsdl = null;
		// Generate WSDL if not cached
		ini_set("soap.wsdl_cache_enabled",0);
//		debug($modelClass);
		Configure::write('debug', 0);		
		if (is_null($wsdl)) {
			$refl = new IPReflectionClass($modelClass);
//			debug($refl);
			$controllerName = $this->params['controller'];
			$serviceURL = Router::url("/$controllerName/$serviceMethod", true);
			$wsdlStruct = new WSDLStruct((isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'].$this->controller->base, 
					                     $serviceURL . '/' . $modelId, 
										 SOAP_RPC, 
										 SOAP_LITERAL);							 
			$wsdlStruct->setService($refl);
			try {
				$wsdl = $wsdlStruct->generateDocument();
				// cache($cachePath, $wsdl, $expireTime);
			} catch (WSDLException $exception) {
				if (Configure::read() > 0) {
					$exception->Display();
					exit();
				} else {
					return null;
				}
			}
		}
//		exit;
		return $wsdl;
	}

	/**
	 * Handle SOAP service call
	 *
	 * @param string $modelId : underscore notation of the called model
	 *                          without _service ending
	 * @param string $wsdlMethod : method of the controller that will generate the WSDL
	 */
	function handle($modelId, $wsdlMethod = 'wsdl')
	{
		$modelClass = $this->__getModelClass($modelId);
		$wsdlCacheFile = CACHE . $modelClass . '.wsdl';

		// Try to create cache file if not exists
		if (! is_file($wsdlCacheFile)) {
			$this->getWSDL($modelId);
		}
		ini_set("soap.wsdl_cache_enabled", "0");
		Configure::write('debug', 0);
		if (is_file($wsdlCacheFile)) {
			$server = new SoapServer($wsdlCacheFile);
		} else {
			$controllerName = $this->params['controller'];
			$wsdlURL = Router::url("/$controllerName/$wsdlMethod", true);
			$server = new SoapServer($wsdlURL . '/' . $modelId);
		}
		$server->setClass($modelClass);
		$server->handle();
	}

	/**
	 * Get model class for specified model id
	 *
	 * @access private
	 * @return string : the model id
	 */
	function __getModelClass($modelId)
	{
		$inflector = new Inflector;
		return ($inflector->camelize($modelId) . 'Service');
	}

	/**
	 * Get model id for specified model class
	 *
	 * @access private
	 * @return string : the model id
	 */
	function __getModelId($modelClass)
	{
		$inflector = new Inflector;
		return $inflector->underscore(substr($modelClass, 0, -7));
	}

	/**
	 * Get model file for specified model id
	 *
	 * @access private
	 * @return string : the filename
	 */
	function __getModelFile($modelId)
	{
		$modelDir = dirname(dirname(dirname(__FILE__))) . DS . 'models';
		return $modelDir . DS . $modelId . '_service.php';
	}
        
}


?>