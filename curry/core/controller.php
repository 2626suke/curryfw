<?php

/**
 * Controller
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
class Controller extends CurryClass
{
	/**
	 * Request instance, contains request informations
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * Response instance
	 *
	 * @var Response
	 */
	protected $response;
	
	/**
	 * View instance
	 *
	 * @var ViewAbstract
	 */
	protected $view;
	
	/**
	 * Plugin instance
	 *
	 * @var PluginAbstract
	 */
	protected $plugin;
	
	/**
	 * Validator instance
	 *
	 * @var Validator
	 */
	protected $validator;
	
	/**
	 * The string of Application environment.
	 * 
	 * @var string
	 */
	protected $appEnv;
		
	/**
	 * Validation rule
	 *
	 * @var array
	 */
	protected $validateRules = array();
	
	/**
	 * Session instance, it is keep value only in the same controller
	 *
	 * @var Session
	 */
	protected $_session;
	
	/**
	 * When processing that specifies the action ends, it becomes an imitation true besides
	 *
	 * @var boolean
	 */
	protected $dispatched = false;
	
	/**
	 * Whether execute validation or not.
	 *
	 * @var boolean
	 */
	protected $autoValidate = false;
	
	/**
	 * Cached values.
	 *
	 * @var array
	 */
	protected $_caches = array();
		
	/**
	 * Set the Request instance
	 *
	 * @param Request $request Request instance
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;		
	}
	
	/**
	 * Set the Response instance
	 *
	 * @param Response $response Response instance
	 * @return void
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;
	}
	
	/**
	 * Get the Request instance
	 *
	 * @return Request $request Request instance
	 */
	public function getRequest()
	{
		return $this->request;
	}
	
	/**
	 * Get the Response instance
	 *
	 * @return Response $response Response instance
	 */
	public function getResponse()
	{
		return $this->response;
	}
	
	/**
	 * Set view instance
	 *
	 * @param ViewAbstract $view ViewAbstract extended instnace
	 * @return void
	 */
	public function setView(ViewAbstract $view)
	{
		$this->view = $view;
	}
	
	/**
	 * Get view instance
	 *
	 * @return ViewAbstract
	 */
	public function getView()
	{
		return $this->view;
	}
	
	/**
	 * Set plugin instance
	 *
	 * @param PluginAbstract $view PluginAbstract extended instnace
	 * @return void
	 */
	public function setPlugin(PluginAbstract $plugin)
	{
		$this->plugin = $plugin;
	}
	
	/**
	 * Get plugin instance
	 *
	 * @return PluginAbstract
	 */
	public function getPlugin()
	{
		return $this->plugin;
	}
	
	/**
	 * Set whether execute validation or not.
	 *
	 * @param boolean $bool
	 * @return void
	 */
	public function setAutoValidate($bool)
	{
		$this->autoValidate = $bool;
	}
	
	/**
	 * Set valiator instance
	 *
	 * @param Validator $validator
	 * @return void
	 */
	public function setValidator(Validator $validator)
	{
		$this->validator = $validator;
	}
	
	/**
	 * Set the string of Application environment
	 * 
	 * @param string $appEnv
	 * @return void
	 */
	public function setAppEnv($appEnv)
	{
		$this->appEnv = $appEnv;
	}
	
	/**
	 * Get the string of Application environment
	 * 
	 * @return string
	 */
	public function getAppEnv()
	{
		return $this->appEnv;
	}
	
	/**
	 * Initialize validator
	 *
	 * @return void
	 */
	public function initValidator()
	{
		$ini = Ini::load('validate.ini', 'error_message');
		if ($ini !== false) {
			Validator::setDefaultErrorMessage($ini);
		}
		if (array_key_exists($this->action, $this->validateRules)) {
			$this->validator->setRules($this->validateRules[$this->action]);
		}
	}
	
	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initialize()
	{
		if ($this->session->exists('errors')) {
			$this->view->errors = $this->session->errors;
			$this->session->remove('errors');
		}		
		if ($this->autoValidate == true) {
			$this->vilidateRequest();		
			$this->session->back_action = $this->request->getAction();
		}
	}
	
	/**
	 * Validate requst if $autoValidate equals true
	 *
	 * @return void
	 */
	protected function vilidateRequest()
	{
		if (array_key_exists($this->action, $this->validateRules)) {
			$args = array_merge($this->post, $this->query);
			if (count($args) > 0) {
				$res = $this->validator->validate($args);
				if ($res == false) {
					$this->session->errors = $this->validator->getError();
					$this->redirect(sprintf('%s/%s', $this->controller, $this->session->back_action));
				}
			}
		}		
	}
		
	/**
	 * Returns whether the process of specific the action ends
	 *
	 * @return boolean
	 */
	public function isDispatched()
	{
		return $this->dispatched;
	}
	
	/**
	 * Create and return the instance of the model specified by the parameter
	 * 
	 * @param string $className
	 * @param string $alias
	 * @param string $subdir
	 * @return Model
	 */
	public function model($className, $alias = null, $subdir = null)
	{
		require_once 'core/model.php';
		$model = Loader::getModelInstance($className, $subdir);
		if ($alias != null) {
			$model->setAlias($alias);
		}
		return $model;
	}

	/**
	 * Create and return the instance of the service specified by the parameter
	 * 
	 * @param string $className
	 * @param string $subdir
	 * @return Service
	 */
	public function service($className, $subdir = null)
	{
		require_once 'core/service.php';
		$service = Loader::getServiceInstance($className, $subdir);
		return $service;
	}
			
	/**
	 * Redirect to other page of same domain
	 *
	 * @param string $path The Domain removed url path
	 * @return void
	 */
	public function redirect($path)
	{
		$url = $this->request->getBaseUrl() . '/' . ltrim($path, '/');
		$this->response->redirect($url);
	}
	
	/**
	 * Transfer the process to other action or controller
	 *
	 * @param string $action Action key you want to transfer
	 * @param string $controller Controller key you want to transfer
	 * @return void
	 */
	public function transfer($action, $controller = null)
	{
		if ($controller != null) {
			$this->view->setRenderingController($controller);
		}
		$this->view->setRenderingAction($action);
				
		$actionMethod = NameManager::convertActionToMethod($action);
		if ($controller == null) {
			$this->$actionMethod();
		} else {
			$subdir = '';
			$parts = explode('/', trim($controller, '/'));
			$partsCnt = count($parts);
			if ($partsCnt > 1) {
				$controller = $parts[$partsCnt - 1];
				unset($parts[$partsCnt - 1]);
				$subdir = implode('/', $parts);
			}
        	$className = NameManager::convertControllerToClass($controller);
			$ins = Loader::getControllerInstance($className, $subdir);
			$ins->setRequest($this->request);
			$ins->setView($this->view);
			$ins->$actionMethod();
		}
		$this->dispatched = true;
	}
		
	/**
	 * It is called before action method
	 *
	 * @return void
	 */
	public function preProcess()
	{	
	}
	
	/**
	 * It is called after action method
	 *
	 * @return void
	 */
	public function postProcess()
	{	
	}

	/**
	 * Overriding parent
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if ($name == 'session') {
			if ($this->_session === null) {
				$req = $this->request;
				Loader::load('Session', 'core');
				$ns = $req->getController();
				$subdir = str_replace('/', '_', $req->getControllerSubDirectory());
				if ($subdir != '') {
					$ns = $subdir . '_' . $ns;
				}
				$this->_session = new Session($ns);
			}
			return $this->_session;
		}
		if ($name == 'post') {
			if (!array_key_exists($name, $this->_caches)) {
				$this->_caches[$name] = $this->request->getPost();
			}
			return $this->_caches[$name];
		}
		if ($name == 'query') {
			if (!array_key_exists($name, $this->_caches)) {
				$this->_caches[$name] = $this->request->getQuery();
			}
			return $this->_caches[$name];
		}
		if ($name == 'params') {
			if (!array_key_exists($name, $this->_caches)) {
				$this->_caches[$name] = $this->request->getParams();
			}
			return $this->_caches[$name];
		}		
		if ($name == 'controller') {
			if (!array_key_exists($name, $this->_caches)) {
				$this->_caches[$name] = $this->request->getController();
			}
			return $this->_caches[$name];
		}
		if ($name == 'action') {
			if (!array_key_exists($name, $this->_caches)) {
				$this->_caches[$name] = $this->request->getAction();
			}
			return $this->_caches[$name];
		}
		
		return parent::__get($name);
	}
	
}