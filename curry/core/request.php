<?php

/**
 * Request
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    core
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Request extends CurryClass
{
	/**
	 * Relative path of the directory
	 * where the index.php as seen from the document root
	 *
	 * @var string
	 */
	protected $_basePath;
	
	/**
	 * Site root url
	 *
	 * @var string
	 */
	protected $_baseUrl;
	
	/**
	 * Requested uri path
	 *
	 * @var string
	 */
	protected $_path;
	
	/**
	 * Path of the subdirectory that contains
	 * the controller class php file that is routed from url
	 *
	 * @var string
	 */
	protected $_controllerSubDir;
	
	/**
	 * Controller key contained in the url
	 *
	 * @var string
	 */
	protected $_controller;
	
	/**
	 * Action key contained in the url
	 *
	 * @var string
	 */
	protected $_action;
	
	/**
	 * Parameter keys and values contained in the url
	 *
	 * @var string
	 */
	protected $_params;
	
	/**
	 * Whether to access as rest request.
	 *
	 * @var boolean
	 */
	protected $_isRestAccess = false;
		
	/**
	 * Request parameters on rest access
	 *
	 * @var array
	 */
	protected $_restParams;
	
	/**
	 * Whether to trim request parameters
	 *
	 * @var boolean
	 */
	protected static $_autoTrim = false;
	
	/**
	 * Set whether to trim request parameters
	 *
	 * @params boolean $autoTrim
	 * @return void
	 */
	public static function setAutoTrim($autoTrim)
	{
		self::$_autoTrim = $autoTrim;
	}
	
	/**
	 * Set whether to access as rest request.
	 *
	 * @param boolean $isRest
	 * @return void
	 */
	public function isRestAccess($isRest)
	{
		$this->_isRestAccess = $isRest;
	}
		
	/**
	 * Set the relative path of the directory
	 * where the index.php as seen from the document root
	 *
	 * @param string $path
	 * @return void
	 */
	public function setBasePath($path)
	{
		$this->_basePath = $path;
	}
	
	/**
	 * Get the relative path of the directory
	 * where the index.php as seen from the document root
	 *
	 * @return string
	 */
	public function getBasePath()
	{
		if ($this->_basePath == null) {
			$this->_basePath = preg_replace('|/[^/]*\.php$|', '', $_SERVER['SCRIPT_NAME']);
		}
		return $this->_basePath;
	}
		
	/**
	 * Get url of top page of site
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		if ($this->_baseUrl == null) {		
			$schema = 'http';
			if ($this->isSsl()) {
				$schema = 'https';
			}			
			$reqestPort = $_SERVER["SERVER_PORT"];
			$port = '';
			if (($schema == 'http' && $reqestPort != '80') || ($schema == 'https' && $reqestPort != '443')) {
				$port = ':' . $_SERVER["SERVER_PORT"];			
			}
			$path = trim($this->getBasePath(), '/');
			$url = sprintf('%s://%s%s/%s', $schema, $_SERVER['SERVER_NAME'], $port, $path);
			$this->_baseUrl = trim($url, '/');
		}
		return $this->_baseUrl;
	}
	
	/**
	 * Get requested uri path
	 *
	 * @return string Requested uri path
	 */
	public function getPath()
	{
		if ($this->_path == null) {
			$this->_path = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
			$this->_path = rtrim($this->_path, '/');
		}
		return $this->_path;
	}
		
	/**
	 * Set the controller key requested by url 
	 *
	 * @param string $controller Controller key
	 * @param string $controllerSubDir Relative path of controller subdirectory
	 * @return void
	 */
	public function setController($controller, $controllerSubDir = null)
	{
		$this->_controller = $controller;
		$this->_controllerSubDir = trim($controllerSubDir, '/');
	}
	
	/**
	 * Set the action key requested by url 
	 *
	 * @param string $action Action key
	 * @return void
	 */
	public function setAction($action)
	{
		$this->_action = $action;
	}
	
	/**
	 * Set parameters requested by url 
	 *
	 * @param array $urlParams parameters
	 * @return void
	 */
	public function setParams($urlParams)
	{
		$this->_params = $urlParams;
	}
	
	/**
	 * Get relative path of controller subdirectory
	 *
	 * @return string
	 */
	public function getControllerSubDirectory()
	{
		return $this->_controllerSubDir;
	}

	/**
	 * Get controller key requested by url 
	 *
	 * @return string
	 */
	public function getController()
	{
		return $this->_controller;
	}
	
	/**
	 * Get action key requested by url 
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->_action;
	}
	
	/**
	 * Get value(s) of $_POST
	 *
	 * @param string $key
	 * @return array|string
	 */
	public function getPost($key = null)
	{
        $ret = null;
        if (null === $key) {
            $ret = $_POST;
        } else {
        	if (isset($_POST[$key])) {
				$ret = $_POST[$key];
        	}
        }
		if (self::$_autoTrim == true) {
			$ret = $this->_trim($ret);
		}
        return $ret;
	}
		
	/**
	 * Get value(s) of $_GET
	 *
	 * @param string $key
	 * @return array|string
	 */
	public function getQuery($key = null)
	{
        $ret = null;
        if (null === $key) {
            $ret = $_GET;
        } else {
        	if (isset($_GET[$key])) {
            	$ret = $_GET[$key];
        	}
        }
		if (self::$_autoTrim == true) {
			$ret = $this->_trim($ret);
		}
        return $ret;
	}
	
	/**
	 * Get value(s) of parameter(s) requested by url
	 *
	 * @param string $key
	 * @return array|string
	 */
	public function getParams($key = null)
	{
        $ret = null;
        if (null === $key) {
            $ret = $this->_params;
        } else {
        	if (isset($this->_params[$key])) {
            	$ret = $this->_params[$key];
        	}
        }
        return $ret;
	}
	
	/**
	 * Get requested http method
	 *
	 * @return string
	 */
	public function getMethod()
	{
		$method = $this->getServer('REQUEST_METHOD');
		if ($this->_isRestAccess == true) {
			$m = null;
			if ($method == 'POST' && isset($_POST['_method'])) {
				$m = strtoupper($_POST['_method']);
			} else if ($method == 'GET' && isset($_GET['_method'])) {
				$m = strtoupper($_GET['_method']);
			}
			if ($m == 'PUT' || $m == 'DELETE' || $m == 'POST' || $m == 'GET') {
				$method = $m;
			}
		}
		return $method;
	}
	
	/**
	 * Get request parameters on rest access
	 *
	 * @param string $key
	 * @return array|string
	 */
	public function getRestParams($key = null)
	{
		if ($this->_isRestAccess == false) {
			return null;
		}
		if ($this->_restParams === null) {
			$params = array();
			switch ($this->getMethod())
			{
				case 'GET':
					$params = $this->getQuery();
					break;
				case 'POST':
					$params = $this->getPost();
					break;
				case 'PUT':
				case 'DELETE':
					$input = file_get_contents('php://input');
					$inputs = explode('&', $input);
					foreach ($inputs as $part) {
						$ex = explode('=', $part);
						if (count($ex) == 2) {
							$inputKey = urldecode($ex[0]);
							$inputVal = urldecode($ex[1]);
							$params[$inputKey] = $inputVal;
						}
					}
					if (self::$_autoTrim == true) {
						$params = $this->_trim($params);
					}
					break;
			}
			if (array_key_exists('_method', $params)) {
				unset($params['_method']);
			}
			$this->_restParams = $params;
		}
        $ret = null;
        if (null === $key) {
            $ret = $this->_restParams;
        } else {
        	if (isset($this->_restParams[$key])) {
            	$ret = $this->_restParams[$key];
        	}
        }
        return $ret;
	}
	
	/**
	 * Get value(s) of $_SERVER
	 *
	 * @param string $key
	 * @return string|array
	 */
	public function getServer($key)
	{
        $ret = null;
        if (null === $key) {
            $ret = $_SERVER;
        } else {
        	if (isset($_SERVER[$key])) {
            	$ret = $_SERVER[$key];
        	}
        }
        return $ret;
	}
	
	/**
	 * Get value(s) of $_ENV
	 *
	 * @param string $key
	 * @return string|array
	 */
	public function getEnv($key)
	{
        $ret = null;
        if (null === $key) {
            $ret = $_ENV;
        } else {
        	if (isset($_ENV[$key])) {
            	$ret = $_ENV[$key];
        	}
        }
        return $ret;
	}
	
	/**
	 * Get client ip address
	 *
	 * @return string
	 */
	public function getIpAddress()
	{
		$ip = $this->getServer('REMOTE_ADDR');
		return $ip;
	}
	
	/**
	 * Get client host name
	 *
	 * @return string
	 */
	public function getHostname()
	{
		$ip = $this->getIpAddress();
		$host = gethostbyaddr($ip);
		return $host;
	}
	
	/**
	 * Whether it is an XMLHttpRequest request 
	 *
	 * @return boolean
	 */
	public function isXmlHttp()
    {
    	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    		return false;
    	}
		if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			return true;
		}
		return false;
    }
    
	/**
	 * Whether it is an SSL request 
	 *
	 * @return boolean
	 */
	public function isSsl()
    {
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off')) {
    		return true;
    	}
		return false;
    }
	
	/**
	 * Trim array data recursively
	 * 
	 * @param string|array $data
	 * @return string|array
	 */
	private function _trim($data)
	{
		$ret = array();
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				$ret[$key] = $this->_trim($val);
			}
		} else {
			$ret = trim($data);
		}
		return $ret;
	}

}