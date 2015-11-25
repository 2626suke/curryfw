<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * Dispatcher
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
class Dispatcher extends CurryClass
{
	/**
	 * Whether plugin enabled.
	 * 
	 * @var boolean
	 */
	protected $_pluginEnabled        = true;
	
	/**
	 * Class name of view
	 * 
	 * @var string
	 */
	protected $_viewClassName        = 'ViewStandard';
	
	/**
	 * Class name of validator
	 * 
	 * @var string
	 */
	protected $_validatorClassName   = 'Validator';
	
	/**
	 * Whether send a http status code "404" or not when routing failed.
	 * 
	 * @var boolean
	 */
	protected $_isSend404            = true;
	
	/**
	 * Text that meaning the application environment
	 *
	 * @var string
	 */
	protected $_appEnv;
	
	/**
	 * Set text that meaning the application environment
	 * 
	 * @param string $appEnv
	 * @return void
	 */
	public function setAppEnv($appEnv)
	{
		$this->_appEnv = $appEnv;
	}
	
	/**
	 * Set class name of view to use
	 * 
	 * @param string $className Class name of View
	 * @return void
	 */
	public function setViewClass($className)
	{
		if ($className == 'ViewSmarty') {
			$curryPath = PathManager::getFrameworkRoot();
			if (file_exists($curryPath . '/Smarty/libs/Smarty.class.php')) {
				require_once $curryPath . '/Smarty/libs/Smarty.class.php';
			}
		}
		$this->_viewClassName = $className;
	}	
		
	/**
	 * Set class name of validator to use
	 * 
	 * @param string $className Class name of Validator 
	 * @return void
	 */
	public function setValidatorClass($className)
	{
		$this->_validatorClassName = $className;
	}
	
	/**
	 * Set plugin enabled
	 * 
	 * @param booelan $enabled
	 * @return void
	 */
	public function enablePlugin($enabled)
	{
		$this->_pluginEnabled = $enabled;
	}
	
	/**
	 * Set whether send a http status code "404" or not when routing failed.
	 * 
	 * @param boolean $isSend
	 * @return void
	 */
	public function isSend404($isSend)
	{
		$this->_isSend404 = $isSend;
	}
	
	/**
	 * Routing the controller actions based on url, to run a class method in question.
	 * 
	 * @return void
	 * @throws Exception
	 * @throws NotFoundException
	 * @throws MemberInaccessibleException
	 */
	public function dispatch()
	{
		Loader::load('Request', 'core');
		Loader::load('ViewAbstract', 'core');
		$request = new Request();
		$response = new Response();
		$view = null;
		$viewInitialized = false;
		$subdir = '';
		
		try
		{
			// check system root setting.
			$sysRoot = PathManager::getSystemRoot();
			if ($sysRoot == null) {
				throw new Exception('Path of the system root has not been specified.');
			} else if (!file_exists($sysRoot)) {
				throw new Exception('Path of the system root does not exist.');
			}
			
			$router = new Router();

			// initialize by Initializer.
			$initializer = $this->_searchIntializer();
			if ($initializer instanceof InitializerStandard) {
				$initializer->setDispatcher($this);
				$initializer->setRouter($router);
				$initializer->applyConfig();
				$initializer->initEnv();
				$initializer->initialize();
			}
			
			$params = $router->route($request->getBasePath());
			
			// create view class instance.
			Loader::load($this->_viewClassName, null, true, false);
			$viewClass = $this->_viewClassName;
			$view = new $viewClass();			
			
			// set request instance to controller.
			$subdir = $params['subdir'];
			$request->setController($params['controller'], $subdir);
			$request->setAction($params['action']);
			$request->setParams($params['params']);
			
			$view->setRequest($request);
			$view->initialize();
			$viewInitialized = true;
			
			// create controller class instance.
			$controller = $this->_getControllerInstance($params['controller'], $params['subdir']);
			if ($controller == false) {
				throw new NotFoundException($params['controller'], '');
			}
			$controller->setAppEnv($this->_appEnv);
			$controller->setRequest($request);
			
			// set response instance to controller.
			$controller->setResponse($response);
			
			// set view to controller.
			$controller->setView($view);
			
			// get action method name.
			$reqestMethod = null;
			$actionMethod = '';
			if ($controller instanceof RestController) {
				$request->isRestAccess(true);
				$reqestMethod = $request->getMethod();
				if ($params['action'] == 'index' && $reqestMethod != 'GET') {
					$restDefaultMethod = NameManager::convertActionToMethod($reqestMethod);
					if (method_exists($controller, $restDefaultMethod)) {
						$actionMethod = $restDefaultMethod;
					}
				}
			}
			if ($actionMethod == '') {
				$actionMethod = NameManager::convertActionToMethod($params['action'], $reqestMethod);
			}
						
			// initialize validator			
			$validatorClass = $this->_validatorClassName;
			$validator = new $validatorClass();
			$controller->setValidator($validator);
			$controller->initValidator();
			
			$controller->initialize();
			
			// Create plugin instance.
			$plugin = null;
			if ($this->_pluginEnabled) {
				$plugin = $this->_searchPlugin($params['subdir']);
				if ($plugin instanceof PluginAbstract) {
					$plugin->setControllerInstance($controller);
					$plugin->preProcess();
				}
			}

			$controller->preProcess();
			
			if ($controller->isDispatched() == false) {
	 			// Check action method exists.
				if (!method_exists($controller, $actionMethod)) {
					$controllerParam = $params['controller'];
					if ($params['subdir'] != null) {
						$controllerParam = $params['subdir'] . '/' . $controllerParam;
					} 
					throw new NotFoundException($controllerParam, $params['action']);
				} else if (!is_callable(array($controller, $actionMethod))) {
					throw new MemberInaccessibleException($params['controller'], $params['action']);
				}
				$controller->$actionMethod();
			}
			
			$controller->postProcess();
			
			if ($this->_pluginEnabled) {
				if ($plugin instanceof PluginAbstract) {
					$plugin->postProcess();
				}
			}
			
			if ($controller instanceof RestController) {
				if ($controller->isAutoRedirect() == true && $request->getMethod() != 'GET') {
					$url = '';
					if ($params['action'] != 'index') {
						$url = $params['controller'] . '/' . $params['action'];
					} else if ($params['controller'] != 'index') {
						$url = $params['controller'];
					}
					$url = $request->getBaseUrl() . '/' . $url;
					$response->redirect($url);
				}
			}
			
			$response->send();
			
			if ($response->isOutput() == false && $view->getRenderingEnabled() == true) {
				$viewScript = $this->_getViewScriptInstance($params['controller'], $params['subdir']);
				if ($viewScript instanceof ViewScript) {
					if (method_exists($viewScript, $actionMethod)) {
						$viewScript->setView($view);
						$viewScript->setRequest($request);
						$viewScript->$actionMethod();
					}
				}
				$view->render();
			}
		}
		catch (Exception $e)
		{
			$handler = $this->_searchErrorHandler($subdir);
			if ($handler instanceof ErrorHandlerStandard) {
				$action = 'error';
				$actionMethod = '';
				$className = get_class($e);
				if ($className != 'Exception') {
					$actionMethod =  str_replace('Exception', '', $className);
					$actionMethod = NameManager::toMethod($actionMethod);
				}
				if (method_exists($handler, $actionMethod)) {
					$action = $actionMethod;
				}
				$handler->setException($e);
				$handler->setRequest($request);
				$handler->setResponse($response);
				if ($viewInitialized == true) {
					$handler->setView($view);
				}
				if ($action == 'notFound') {					
					if ($this->_isSend404 == true) {
						$response->setHttpStatus('404');
						$response->send();
					}
				}
				$handler->$action();
			} else {
				throw $e;
			}
		}
	}
		
	/**
	 * Search initializer class and return its instance
	 * 
	 * @return InitializerStandard
	 */
	protected function _searchIntializer()
	{
		$path = PathManager::getAppDirectory() . '/initializer.php';
		
		$initializer = false;
		if (file_exists($path)) {
			$className = NameManager::toClass('initializer');
			Loader::load($className, PathManager::getAppDirectory());
			$initializer = new $className($this->_appEnv);
		}		
		if (!($initializer instanceof InitializerStandard)) {
			$initializer = new InitializerStandard($this->_appEnv);	
		}
		return $initializer;
	}
	
	/**
	 * Search plugin class and return its instance
	 * 
	 * @return PluginAbstract|boolean
	 */
	protected function _searchPlugin($subdir)
	{
		$controllerDir = PathManager::getControllerDirectory();
		$dirs = array();
		if ($subdir != '') {
			$dirs = explode('/', trim($subdir, '/'));
		}
		$path = '';
		while (true) {
			$path = $controllerDir;
			if (count($dirs) > 0) {
				$path .= '/' . implode('/', $dirs);
			}
			if (file_exists($path . '/plugin.php')) {
				Loader::load('PluginAbstract');
				require_once $path . '/plugin.php';
				$className = 'plugin';
				if (count($dirs) > 0) {
					$className = NameManager::toClass(implode('_', $dirs) . '_plugin');
					if (Loader::classExists($className)) {
						$plugin = new $className();
						return $plugin;
					}
				}
				if (Loader::classExists('Plugin')) {
					$plugin = new Plugin();
					return $plugin;
				}
			}
			if ($path == $controllerDir) {
				break;
			}
			array_pop($dirs);
		}
		return false;
	}
	
	/**
	 * Search error handler class and return its instance
	 * 
	 * @param string $subdir
	 * @return ErrorHandlerStandard
	 */
	protected function _searchErrorHandler($subdir)
	{
		$controllerDir = PathManager::getControllerDirectory();
		$dirs = array();
		if ($subdir != '') {
			$dirs = explode('/', trim($subdir, '/'));
		}
        $handler = null;
		$path = '';
		while (true) {
			$path = $controllerDir;
			if (count($dirs) > 0) {
				$path .= '/' . implode('/', $dirs);
			}
			if (file_exists($path . '/error_handler.php')) {
				require_once $path . '/error_handler.php';
				$className = NameManager::toClass(implode('_', $dirs) . '_error_handler');
				if (Loader::classExists($className)) {
					$handler = new $className();
					break;
				} else if (Loader::classExists('ErrorHandler')) {
					$handler = new ErrorHandler();
					break;
				}
			}
			if ($path == $controllerDir) {
				break;
			}
			array_pop($dirs);
		}
        if (!($handler instanceof ErrorHandlerStandard)) {
            $handler = new ErrorHandlerStandard();
        }
		return $handler;
	}
				
	/**
	 * Get an instance of the controller class for the controller name given in the parameter
	 * 
	 * @param string $controllerName
	 * @param string $subdir
	 * @return Controller
	 */
	protected function _getControllerInstance($controllerName, $subdir)
	{
        $className = NameManager::convertControllertoClass($controllerName);
		$ins = Loader::getControllerInstance($className, $subdir);
        return $ins;
	}
	
	/**
	 * Get an instance of the view script class for the controller name given in the parameter
	 * 
	 * @param string $controllerName
	 * @param string $subdir
	 * @return ViewScript
	 */
	protected function _getViewScriptInstance($controllerName, $subdir)
	{
        $className = NameManager::convertControllerToViewClass($controllerName);
		$ins = Loader::getViewScriptInstance($className, $subdir);
        return $ins;
	}
}
