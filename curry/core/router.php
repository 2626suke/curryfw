<?php

/**
 * Router
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
class Router extends CurryClass
{
	/**
	 * Wheater the access routed using mod_rewrite is done or not.
	 * 
	 * @var boolean
	 */
	protected $_isRewrite = true;
		
	/**
	 * Controller key in url
	 * 
	 * @var string
	 */
	protected $_controller = 'index';
	
	/**
	 * Action key in url
	 * 
	 * @var string
	 */
	protected $_action = 'index';
	
	/**
	 * Controller sub directory path in url
	 * 
	 * @var string
	 */
	protected $_subdir;
		
	/**
	 * Parameters in url
	 * 
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * Routing pattern
	 * 
	 * @var string
	 */
	protected $_routingPath = 'controller/action';
	
	/**
	 * Whether controller subdirectory is used.
	 * 
	 * @var boolean
	 */
	protected $_subControllerEnabled = true;
			
	/**
	 * Query string key of controller in url, if mod_rewrite is enabled.
	 * 
	 * @var string
	 */
	protected $_controllerQueryKey = 'c';
	
	/**
	 * Query string key of action in url, if mod_rewrite is enabled.
	 * 
	 * @var string
	 */
	protected $_actionQueryKey = 'a';
	
	/**
	 * Get whether process as accesse by mod_rewrite.
	 * Or alias of "setIsRewrite" When a parameter is not specified.
	 * 
	 * @param boolean $bool
	 * @return boolean|void
	 */
	public function isRewrite($bool = null)
	{
		if (is_bool($bool)) {
			$this->setIsRewrite($bool);
		} else {
			return $this->_isRewrite;
		}
	}
	
	/**
	 * Set whether process as accesse by mod_rewrite
	 * 
	 * @param boolean $bool
	 * @return void
	 */
	public function setIsRewrite($bool)
	{
		$this->_isRewrite = $bool;
	}

	/**
	 * Set default controller name.
	 * 
	 * @param string $contollerName
	 * @return void
	 */
	public function setDefaultController($contollerName)
	{
		$this->_controller = $contollerName;
	}
	
	/**
	 * Set default action name.
	 * 
	 * @param string $actionName
	 * @return void
	 */
	public function setDefaultAction($actionName)
	{
		$this->_action = $actionName;
	}
	
	/**
	 * Set query string key of action in url
	 * 
	 * @param string $key
	 * @return void
	 */
	public function setControllerQueryKey($key)
	{
		$this->_controllerQueryKey = $key;
	}

	/**
	 * Set query string key of controller in url
	 * 
	 * @param string $key
	 * @return void
	 */
	public function setActionQueryKey($key)
	{
		$this->_actionQueryKey = $key;
	}

	/**
	 * Set controller sub directory enabled.
	 * 
	 * @param booelan $enabled
	 * @return void
	 */
	public function enableSubController($enabled)
	{
		$this->_subControllerEnabled = $enabled;
	}
	
	/**
	 * Get routing info from url
	 * 
	 * @param string $basePath
	 * @return array
	 */
	public function route($basePath)
	{
		if ($this->_isRewrite) {
			$this->_getParamsRewrite($basePath);
		} else {
			$this->_getParamsNoRewrite();
		}
		$ret['subdir'] = $this->_subdir;
		$ret['controller'] = $this->_controller;
		$ret['action'] = $this->_action;
		$ret['params'] = $this->_params;	
		return $ret;
	}
		
	/**
	 * Get params as url path separated by "/" when mod rewrite enabled
	 * 
	 * @param string $basePath
	 * @return void
	 */	
	protected function _getParamsRewrite($basePath)
	{
		// get path from url
		$paramStr = trim($_SERVER['REQUEST_URI'], '/');
		$paramStr = str_replace(trim($basePath, '/'), '', $paramStr);
		$paramStr = str_replace('index.php', '', $paramStr);
		$paramStr = preg_replace('/\?.*/', '', $paramStr);
		$paramStr = trim($paramStr, '/');
		
		$controller = '';
		$action = '';
		
		// read routing setting by "routing.ini", if exists.
		Loader::load('Ini', 'core');
		$ini = Ini::load('routing.ini');
		if ($ini !== false) {
			foreach ($ini as $setting) {
				if (!isset($setting['request'])) {
					continue;
				}
				// Check request path matching
				$match = false;
				$requests = explode(',', $setting['request']);
				foreach ($requests as $request) {
					$req = trim(trim($request), '/');
					if ($req == '*') {
						$match = true;
					} else if ($req == '') {
						if ($paramStr == '') {
							$match = true;
						}
					} else {
						// Segment match
						$req = str_replace('*/', '[^/]+/', $req);
						// Forward match
						$req = preg_replace('|/\*$|', '/.*', $req);
						// If setting of "request" section is contained in url, enable setting of correspond section of routing.ini
						if (preg_match('|^' . $req . '$|', $paramStr)) {
							$match = true;
						}
					}
				}
				
				// Get setting of route when a request path matches
				if ($match) {
					foreach ($setting as $key => $val) {
						if ($key == 'default_controller') {
							$this->_controller = $val;
						} else if ($key == 'default_action') {
							$this->_action = $val;
						} else if ($key == 'controller') {
							$controller = $val;
						} else if ($key == 'action') {
							$action = $val;
						} else if ($key == 'route') {
							$this->_routingPath = trim($val, '/');
						} else {
							$this->_params[$key] = $val;
						}
					}
					break;
				}
			}
		}
		// Explode "route" setting by "/"
		$this->_routingPath = preg_replace('/dir\//', '', $this->_routingPath);
		$routingParams = explode('/', $this->_routingPath);
		
		// Explode url parameter by "/"
		$params = array();
		if ($paramStr !== '') {
			$params = explode('/', $paramStr);
		}
		if ($this->_subControllerEnabled) {
			$params = $this->_getSubdirRemovedParams($params);
		}
		
		$i = 0;
		foreach ($params as $idx => $param) {
			if (array_key_exists($idx, $routingParams)) {
				$key = $routingParams[$idx];
				if ($key == 'dir') {
					continue;
				} else if ($key == 'controller') {
					if ($controller == '') {
						$controller = $param;
					}
				} else if ($key == 'action') {
					if ($action == '') {
						$action = $param;
					}
				} else if (!isset($this->_params[$key]) || $this->_params[$key] = '') {
					$this->_params[$key] = $param;
					$i++;
				}
			} else {
				$key = $i;
				$val = $param;
				if (strpos($param, '=')) {
					$splited = explode('=', $param);
					$key = $splited[0];
					$val = $splited[1];
				}
				$this->_params[$key] = $val;
				$i++;
			}
		}		
		if ($controller != '') {
			$this->_controller = $controller;
		}
		if ($action != '') {
			$this->_action = $action;
		}
	}
	
	/**
	 * Get params as url path separated by "/" when mod rewrite disabled
	 * 
	 * @return void
	 */
	protected function _getParamsNoRewrite()
	{		
		foreach ($_GET as $key => $val) {
			if ($key == $this->_controllerQueryKey) {
				$this->_controller = strtolower($val);
			} else if ($key == $this->_actionQueryKey) {
				$this->_action = strtolower($val);
			} else {
				$this->_params[$key] = $val;
			}
		}
	}
	
	/**
	 * Get path sub directory path is removed from url
	 * 
	 * @param array $params Parts of url path splitted by "/"
	 * @return array $params Parts of url path sub directory path is removed.
	 */	
	protected function _getSubdirRemovedParams($params)
	{
		$controllerDir = PathManager::getControllerDirectory();
		$subDirs = $this->_getSubDirs($controllerDir . '/' . trim($this->_subdir, '/'));
		if (count($subDirs) > 0) {
			foreach ($params as $key => $param) {
				if (in_array($param, $subDirs)) {
					$this->_subdir .= '/' . $param;
					$newParams = $params;
					unset($newParams[$key]);
					$newParams = array_merge($newParams);
					return $this->_getSubdirRemovedParams($newParams);
				} else {
					return $params;
				}
			}
		}
		return $params;
	}
	
	/**
	 * Get the subdirectories in the directory specified by the parameter
	 * 
	 * @param  string $dirPath
	 * @return array $dirs Names of subdirectory
	 */	
	protected function _getSubdirs($dirPath)
	{
    	$dir = dir($dirPath);
    	$dirs = array();
    	while ($content = $dir->read()) {
			if ($content == '.' || $content == '..') {
				continue;
			}
            if (is_dir(sprintf("%s/%s", $dirPath, $content))) {
                $dirs[] = $content;
            }
    	}
    	$dir->close();
		
		return $dirs;
	}
}