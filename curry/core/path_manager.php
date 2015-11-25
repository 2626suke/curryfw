<?php

/**
 * PathManager
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
class PathManager
{
	/**
	 * Path of curry framework directory.
	 * 
	 * @var string
	 */
	private static $_frameworkDir;
	
	/**
	 * Path of system root directory.
	 * 
	 * @var string
	 */
	private static $_systemDir;
	
	/**
	 * Path of application root directory.
	 * 
	 * @var string
	 */
	private static $_appDir;
	
	/**
	 * Path of controller directory.
	 * 
	 * @var string
	 */
	private static $_controllerDir;
	
	/**
	 * Path of model directory.
	 * 
	 * @var string
	 */
	private static $_modelDir;
	
	/**
	 * Path of service directory.
	 * 
	 * @var string
	 */
	private static $_serviceDir;
	
	/**
	 * Path of view root directory.
	 * 
	 * @var string
	 */
	private static $_viewDir;
	
	/**
	 * Path of view template directory.
	 * 
	 * @var string
	 */
	private static $_viewTemplateDir;
	
	/**
	 * Path of view layout template directory.
	 * 
	 * @var string
	 */
	private static $_viewLayoutDir;
	
	/**
	 * Path of view script directory.
	 * 
	 * @var string
	 */
	private static $_viewScriptDir;
	
	/**
	 * Path of config files directory.
	 * 
	 * @var string
	 */
	private static $_configDir;
	
	/**
	 * Path of directory to output log.
	 * 
	 * @var string
	 */
	private static $_logDir;
	
	/**
	 * Path of document root directory.
	 * 
	 * @var string
	 */
	private static $_htdocsDir;
	
	/**
	 * Path of library directory.
	 * 
	 * @var string
	 */
	private static $_libDir;
	
	/**
	 * Path of data directory.
	 * 
	 * @var string
	 */
	private static $_dataDir;
	
	/**
	 * Set path of curry framework root directory
	 *
	 * @param string $path
	 * @return void
	 */
	public static function setFrameworkRoot($path)
	{
		self::$_frameworkDir = rtrim($path, '/');
	}
	
	/**
	 * Set path of system root directory and another directory
	 * application, config, output log, document root, library at the same time set
	 *
	 * @param string $path
	 * @return void
	 */
	public static function setSystemRoot($path)
	{
		self::$_systemDir = rtrim($path, '/');
		self::setAppDirectory(self::$_systemDir . '/app');
		self::setConfigDirectory(self::$_systemDir . '/configs');
		self::setLogDirectory(self::$_systemDir . '/logs');
		self::setHtdocsDirectory(self::$_systemDir . '/htdocs');
		self::setLibraryDirectory(self::$_systemDir . '/library');
		self::setDataDirectory(self::$_systemDir . '/data');
	}
	
	/**
	 * Set path of application root directory and another directory
	 * controller, model, view, service at the same time set
	 *
	 * @param string $path
	 * @return void
	 */
	public static function setAppDirectory($path)
	{
		self::$_appDir = rtrim($path, '/');
		self::setControllerDirectory(self::$_appDir . '/controllers');
		self::setModelDirectory(self::$_appDir . '/models');
		self::setViewDirectory(self::$_appDir . '/views');
		self::setServiceDirectory(self::$_appDir . '/services');
	}
	
	/**
	 * Set path of controller directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setControllerDirectory($dir)
	{
		self::$_controllerDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of model directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setModelDirectory($dir)
	{
		self::$_modelDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of service directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setServiceDirectory($dir)
	{
		self::$_serviceDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of view directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setViewDirectory($dir)
	{
		self::$_viewDir = rtrim($dir, '/');
		self::setViewTemplateDirectory(self::$_viewDir . '/templates');
		self::setViewLayoutDirectory(self::$_viewDir . '/layouts');
		self::setViewScriptDirectory(self::$_viewDir . '/scripts');
	}
	
	/**
	 * Set path of view template directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setViewTemplateDirectory($dir)
	{
		self::$_viewTemplateDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of view layout template directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setViewLayoutDirectory($dir)
	{
		self::$_viewLayoutDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of view layout script directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setViewScriptDirectory($dir)
	{
		self::$_viewScriptDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of config file directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setConfigDirectory($dir)
	{
		self::$_configDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of directory to output log
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setLogDirectory($dir)
	{
		self::$_logDir = rtrim($dir, '/');
	}
		
	/**
	 * Set path of document root directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setHtdocsDirectory($dir)
	{
		self::$_htdocsDir = rtrim($dir, '/');
	}
	
	/**
	 * Set path of library directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setLibraryDirectory($dir)
	{
		self::$_libDir = rtrim($dir, '/');
	}

	/**
	 * Set path of data directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setDataDirectory($dir)
	{
		self::$_dataDir = rtrim($dir, '/');
	}
	
	/**
	 * Get path of curry framework root directory
	 *
	 * @return string
	 */
	public static function getFrameworkRoot()
	{
		return self::$_frameworkDir;		
	}
	
	/**
	 * Get path of system root directory
	 *
	 * @return string
	 */
	public static function getSystemRoot()
	{
		return self::$_systemDir;		
	}
	
	/**
	 * Get path of application root directory
	 *
	 * @return string
	 */
	public static function getAppDirectory()
	{
		return self::$_appDir;
	}
	
	/**
	 * Get path of controller directory
	 *
	 * @return string
	 */
	public static function getControllerDirectory()
	{
		return self::$_controllerDir;
	}
	
	/**
	 * Get path of model directory
	 *
	 * @return string
	 */
	public static function getModelDirectory()
	{
		return self::$_modelDir;
	}
	
	/**
	 * Get path of service directory
	 *
	 * @return string
	 */
	public static function getServiceDirectory()
	{
		return self::$_serviceDir;
	}

	/**
	 * Get path of view root directory
	 *
	 * @return string
	 */
	public static function getViewDirectory()
	{
		return self::$_viewDir;
	}

	/**
	 * Get path of view template directory
	 *
	 * @return string
	 */
	public static function getViewTemplateDirectory()
	{
		return self::$_viewTemplateDir;
	}
	
	/**
	 * Get path of view script directory
	 *
	 * @return string
	 */
	public static function getViewScriptDirectory()
	{
		return self::$_viewScriptDir;
	}
	
	/**
	 * Get path of view layout template directory
	 *
	 * @return string
	 */
	public static function getViewLayoutDirectory()
	{
		return self::$_viewLayoutDir;
	}
	
	/**
	 * Get path of config file directory
	 *
	 * @return string
	 */
	public static function getConfigDirectory()
	{
		return self::$_configDir;
	}
	
	/**
	 * Get path of directory to output log
	 *
	 * @return string
	 */
	public static function getLogDirectory()
	{
		return self::$_logDir;
	}
	
	/**
	 * Get path of document root directory
	 *
	 * @return string
	 */
	public static function getHtdocsDirectory()
	{
		return self::$_htdocsDir;
	}
	
	/**
	 * Get path of library directory
	 *
	 * @return string
	 */
	public static function getLibraryDirectory()
	{
		return self::$_libDir;
	}

	/**
	 * Get path of data directory
	 *
	 * @return string
	 */
	public static function getDataDirectory()
	{
		return self::$_dataDir;
	}
}