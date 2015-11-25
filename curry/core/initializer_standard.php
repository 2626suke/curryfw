<?php

/**
 * InitializerStandard
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
class InitializerStandard extends CurryClass
{
	/**
	 * Dispatcher instance
	 *
	 * @var Dispatcher
	 */
	protected $dispatcher;
	
	/**
	 * Router instance
	 *
	 * @var Router
	 */
	protected $router;
	
	/**
	 * Text that meaning the application environment
	 *
	 * @var string
	 */
	protected $_appEnv;
	
	/**
	 * Constructor
	 * 
	 * @param string $appEnv
	 * @return void
	 */
	public function __construct($appEnv = null)
	{
		$this->_appEnv = $appEnv;
	}
		
	/**
	 * Execute initialize
	 *
	 * @return void
	 */
	public function initialize()
	{	
	}
	
	/**
	 * Set the Dispatcher instance
	 *
	 * @param Dispatcher $dispatcher Dispatcher instance
	 * @return void
	 */
	public function setDispatcher(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * Set the Router instance
	 *
	 * @param Router $router Router instance
	 * @return void
	 */
	public function setRouter(Router $router)
	{
		$this->router = $router;
	}
	
	/**
	 * Initialize environment setting
	 */
	public function initEnv()
	{
		if ($this->_appEnv != null) {			
			// Set default error template.
			ViewAbstract::setDefaultErrorTemplate($this->_appEnv);			
			// call initialize environment method.
			$envInitMethod = NameManager::toMethod('init_' . $this->_appEnv);
			if (method_exists($this, $envInitMethod)) {
				$this->$envInitMethod();
			}
		}
	}
		
	/**
	 * apply curry.ini settings
	 * 
	 * @return boolean
	 */
	public function applyConfig()
	{
		Loader::load('Ini', 'core');
		Loader::load('Db', 'db');
		
		$ini = false;
		if ($this->_appEnv == null) {
			// Default section is "product"
			$ini = Ini::load('database.ini', 'product');
		} else {
			$ini = Ini::load('database.ini', $this->_appEnv);
		}
		if ($ini === false) {
			// For the compatibility of a old version.
			$ini = Ini::load('database.ini', 'connection');
		}
		if ($ini !== false) {
			Db::setConfig($ini);
		}
		
		$ini = Ini::load('curry.ini');
		if ($ini === false) {
			return false;
		}
		
		if (array_key_exists('dispatch', $ini)) {
			$values = $ini['dispatch'];
			$key = 'plugin_enabled';
			if (array_key_exists($key, $values)) {
				if (is_numeric($values[$key]) && $values[$key] == 1) {
					$this->dispatcher->enablePlugin(true);
				}
			}
			$key = 'is_send_404';
			if (array_key_exists($key, $values)) {
				if (is_numeric($values[$key]) && $values[$key] != 1) {
					$this->dispatcher->isSend404(false);
				}
			}
			$key = 'sub_controller_enabled';
			if (array_key_exists($key, $values)) {
				if (is_numeric($values[$key]) && $values[$key] == 1) {
					$this->router->enableSubController(true);
				}
			}
			$key = 'is_rewrite';
			if (array_key_exists($key, $values)) {
				if (is_numeric($values[$key]) && $values[$key] != 1) {
					$this->router->isRewrite(false);
				}
			}
			$key = 'default_controller';
			if (array_key_exists($key, $values)) {
				$this->router->setDefaultController($values[$key]);
			}
			$key = 'default_action';
			if (array_key_exists($key, $values)) {
				$this->router->setDefaultAction($values[$key]);
			}
			$key = 'controller_query_key';
			if (array_key_exists($key, $values)) {
				$this->router->setControllerQueryKey($values[$key]);
			}
			$key = 'action_query_key';
			if (array_key_exists($key, $values)) {
				$this->router->setActionQueryKey($values[$key]);
			}
			$key = 'controller_suffix';
			if (array_key_exists($key, $values)) {
				NameManager::setControllerSuffix($values[$key]);
			}
			$key = 'action_suffix';
			if (array_key_exists($key, $values)) {
				NameManager::setActionSuffix($values[$key]);
			}
		}
		if (array_key_exists('view', $ini)) {
			$values = $ini['view'];
			$key = 'class_name';
			if (array_key_exists($key, $values)) {
				$this->dispatcher->setViewClass($values[$key]);
			}
			$key = 'layout_enabled';
			if (array_key_exists($key, $values)) {
				if (is_numeric($values[$key]) && $values[$key] != 1) {
					ViewAbstract::setDefaultLayoutEnabled(false);
				}
			}
			$key = 'template_extension';
			if (array_key_exists($key, $values)) {
				NameManager::setTemplateExtension($values[$key]);
			}
		}
		if (array_key_exists('request', $ini)) {
			$values = $ini['request'];
			$key = 'auto_trim';
			if (array_key_exists($key, $values)) {
				if (is_numeric($values[$key]) && $values[$key] == 1) {
					Request::setAutoTrim(true);
				}
			}
		}
		if (array_key_exists('database', $ini)) {
			$values = $ini['database'];
			$key = 'is_singleton';
			if (array_key_exists($key, $values)) {
				if (is_numeric($values[$key]) && $values[$key] == 0) {
					Db::setIsSingleton(false);
				}
			}
		}
		
		if (array_key_exists('logger', $ini)) {
			Loader::load('Logger', 'utility');
			$values = $ini['logger'];
			$key = 'system_log';
			if (array_key_exists($key, $values)) {
				Logger::setLogName($values[$key]);
			}
			$key = 'query_log';
			if (array_key_exists($key, $values)) {
				Logger::setLogName($values[$key], 'query');
				Db::enableLogging('query');
			}
			$key = 'output_level';
			if (array_key_exists($key, $values)) {
				$val = strtolower(trim($values[$key]));
				if (is_numeric($val) == false) {
					$levels = array(
						'debug'  => LogLevel::DEBUG,
						'info'   => LogLevel::INFO,
						'warn'   => LogLevel::WARN,
						'error'  => LogLevel::ERROR,
						'except' => LogLevel::EXCEPT
					);
					if (array_key_exists($val, $levels)) {
						$val = $levels[$val];
					} else {
						$val = LogLevel::NO_OUTPUT;
					}
				}
				Logger::setOutputLevel($val, 'query');
			}
			$key = 'max_line';
			if (array_key_exists($key, $values)) {
				Logger::setMaxLine($values[$key]);
			}
			$key = 'max_generation';
			if (array_key_exists($key, $values)) {
				Logger::setGeneration($values[$key]);
			}
		}
		if (array_key_exists('loader', $ini)) {
			$values = $ini['loader'];
			$key = 'autoload_dirs';
			if (array_key_exists($key, $values)) {
				$dirs = explode(',', $values[$key]);
				Loader::addAutoloadDirectory($dirs);
			}
		
		}
		return true;
	}
	
}