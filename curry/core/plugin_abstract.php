<?php

/**
 * PluginAbstract
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
abstract class PluginAbstract extends CurryClass
{
	/**
	 * Instance of controller class that routing by url
	 *
	 * @var Controller
	 */
	protected $_controllerInstance;
	
	/**
	 * Set instance of controller class
	 *
	 * @param Controller $controllerInstance
	 * @return void
	 */
	public function setControllerInstance(Controller $controllerInstance)
	{
		$controllerInstance->setPlugin($this);
		$this->_controllerInstance = $controllerInstance;
	}
	
	/**
	 * It is called before preProcess of controller class
	 *
	 * @return void
	 */
	public function preProcess()
	{
	}
	
	/**
	 * It is called after postProcess of controller class
	 *
	 * @return void
	 */
	public function postProcess()
	{
	}
	
	/**
	 * Create and return the instance of the model specified by the parameter
	 * 
	 * @param string $className Class name of model
	 * @param string $alias Alias in SQL
	 * @param PDO $db PDO instance
	 * @return Model
	 */
	public function model($className, $alias = null, $db = null)
	{
		return $this->_controllerInstance->model($className, $alias, $db);
	}
	
	/**
	 * Redirect to other page of same domain
	 *
	 * @param string $path The Domain removed url path
	 * @return void
	 */
	public function redirect($path)
	{
		$this->_controllerInstance->redirect($path);
	}
		
	/**
	 * Overriding parent
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		// View
		if ($name == 'view') {
			return $this->_controllerInstance->getView();
		}
		// Request
		if ($name == 'request') {
			return $this->_controllerInstance->getRequest();
		}
		// Response
		if ($name == 'response') {
			return $this->_controllerInstance->getResponse();
		}
		// POST
		if ($name == 'post') {
			return $this->_controllerInstance->getRequest()->getPost();
		}
		// GET
		if ($name == 'query') {
			return $this->_controllerInstance->getRequest()->getQuery();
		}
		// URL parameters
		if ($name == 'params') {
			return $this->_controllerInstance->getRequest()->getParams();
		}
		// application env
		if ($name == 'appEnv') {
			return $this->_controllerInstance->getAppEnv();
		}
		parent::__get($name);
	}
	
}