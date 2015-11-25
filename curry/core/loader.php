<?php

/**
 * @see NameManager
 */
require_once 'core/name_manager.php';

/**
 * @see PathManager
 */
require_once 'core/path_manager.php';

/**
 * Regist autoload function
 */
spl_autoload_register('Loader::autoload');

/**
 * Loader
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
class Loader
{
	/**
	 * Name of directory to search include file
	 *
	 * @var array 
	 */
	protected static $_autoloadDirs = array('core', 'exception', 'db');
	
	/**
	 * Load a file that defines the class of Curry
	 *
	 * @param string $className The name of the class to read
	 * @param string $dir Subdirectory that contains the file
	 * @param boolean $searchIncludePath
	 * @param boolean $throwException
	 * @return boolean
	 * @throws FileNotExistException
	 */
	public static function load($className, $dir = null, $searchIncludePath = true, $throwException = true)
	{
		if (self::classExists($className)) {
			return true;
		}
		$fileName = NameManager::toPhpFile($className);		
		if ($dir != null) {
			$fileName = $dir . '/' . $fileName;
		}
		$exists = false;
		if (self::fileExists($fileName, $searchIncludePath)) {
			$exists = true;
		}
		if ($exists == false) {
			if ($throwException == true) {
				self::load('FileNotExistException', 'exception');
				throw new FileNotExistException($fileName);
			}
			return false;
		}
		return require_once $fileName;
	}
		
	/**
	 * Load a file that defines the class of Curry and get instance
	 *
	 * @param string $className The name of the class to read
	 * @param string $dir Subdirectory that contains the file
	 * @return mixed
	 * @throws ClassUndefinedException
	 */
	public static function getInstance($className, $dir = null)
	{
		$res = self::load($className, $dir);
		if ($res == false) {
			return false;
		}
		if (!self::classExists($className)) {
			self::load('ClassUndefinedException', 'exception');
			throw new ClassUndefinedException($className);
		}
		$instance = new $className();
		return $instance;
	}
	
	/**
	 * Load a file that defines the controller class
	 *
	 * @param string $className The name of the controller class to read
	 * @param string $subdir Controller subdirectory that contains the file
	 * @return boolean
	 */
	public static function loadController($className, $subdir = '')
	{
		// verify the existence of a file
		$path = PathManager::getControllerDirectory();
		if ($subdir != '') {
			$path .= '/' . trim($subdir, '/');
		}
		$res = self::load($className, $path, false, false);
		return $res;
	}
	
	/**
	 * Load a file that defines the controller class and return that instance
	 *
	 * @param string $className The name of the controller class to get instance
	 * @param string $subdir Controller subdirectory that contains the file
	 * @return Controller 
	 * @throws ClassUndefinedException
	 */	
	public static function getControllerInstance($className, $subdir = '')
	{
		$res = self::loadController($className, $subdir);
		if ($res == false) {
			return false;
		}
		$instance = null;
		if ($subdir) {
			// if class name is added prefix.
			$splited = explode('/', trim($subdir, '/'));
			$splited[] = $className;
			$prefixedClassName = implode('_', $splited);
			$prefixedClassName = NameManager::toClass($prefixedClassName);
	        if (self::classExists($prefixedClassName)) {
        		$instance = new $prefixedClassName();
	        }
		}
		if ($instance === null) {
	        if (!self::classExists($className)) {
				self::load('ClassUndefinedException', 'exception');
				throw new ClassUndefinedException($className);
			}
	        $instance = new $className();
		}

        return $instance;
	}
		
	/**
	 * Load a file that defines model class
	 * 
	 * @param string $className The name of the controller class to read
	 * @param string $subdir
	 * @return boolean
	 */
	public static function loadModel($className, $subdir = '')
	{
		$path = PathManager::getModelDirectory();
		if ($subdir != '') {
			$path .= '/' . trim($subdir, '/');
		}
		$res = self::load($className, $path, false, false);
		return $res;
	}
		
	/**
	 * Load a file that defines model class and return that instance
	 * 
	 * @param string $className The name of the model class to get instance
	 * @param string $subdir
	 * @return Model
	 * @throws ClassUndefinedException
	 */
	public static function getModelInstance($className, $subdir = '')
	{
		$instance = false;
		
		$res = self::loadModel($className, $subdir);
		if ($res == false) {
			if (Model::isAllowVirtual()) {
				$tableName = NameManager::toTable($className);
				$instance = new Model($tableName);
			}
		} else {
			if (!self::classExists($className)) {
				self::load('ClassUndefinedException', 'exception');
				throw new ClassUndefinedException($className);
			}
			$instance = new $className();
		}
		
		return $instance;
	}
	
	/**
	 * Load a file that defines service class
	 * 
	 * @param string $className
	 * @param string $subdir
	 * @return boolean
	 */
	public static function loadService($className, $subdir = '')
	{
		$path = PathManager::getServiceDirectory();
		if ($subdir != '') {
			$path .= '/' . trim($subdir, '/');
		}
		$res = self::load($className, $path, false, false);
		return $res;
	}
	
	/**
	 * Load a file that defines the service class and return that instance
	 * 
	 * @param string $className The name of the service class to get instance
	 * @param string $subdir
	 * @return Service
	 * @throws FileNotExistException
	 * @throws ClassUndefinedException
	 */
	public static function getServiceInstance($className, $subdir = '')
	{		
		$res = self::loadService($className, $subdir);
		if ($res == false) {
			$fileName = NameManager::toPhpFile($className);
			self::load('FileNotExistException', 'exception');
			throw new FileNotExistException($fileName);
		}
		if (!self::classExists($className)) {
			self::load('ClassUndefinedException', 'exception');
			throw new ClassUndefinedException($className);
		}
		$instance = new $className();
		
		return $instance;
	}
		
	/**
	 * Load a file that defines the view script class
	 *
	 * @param string $className The name of the view script class to read
	 * @param string $subdir View script subdirectory that contains the file
	 * @return boolean
	 */
	public static function loadViewScript($className, $subdir = '')
	{
		// verify the existence of a file
		$path = PathManager::getViewScriptDirectory();
		if ($subdir != '') {
			$path .= '/' . trim($subdir, '/');
		}
		$res = self::load($className, $path, false, false);		
		return $res;
	}
	
	/**
	 * Load a file that defines the view script class and return that instance
	 *
	 * @param string $className The name of the view script class to get instance
	 * @param string $subdir View script subdirectory that contains the file
	 * @return boolean|ViewScript
	 * @throws ClassUndefinedException
	 */
	public static function getViewScriptInstance($className, $subdir = '')
	{
		$res = self::loadViewScript($className, $subdir);
		if ($res == false) {
			return false;
		}
		
		$instance = null;
		if ($subdir) {
			// if class name is added prefix.
			$splited = explode('/', trim($subdir, '/'));
			$splited[] = $className;
			$prefixedClassName = implode('_', $splited);
			$prefixedClassName = NameManager::toClass($prefixedClassName);
	        if (self::classExists($prefixedClassName)) {
        		$instance = new $prefixedClassName();
	        }
		}
		if ($instance === null) {
	        if (!self::classExists($className)) {
				self::load('ClassUndefinedException', 'exception');
				throw new ClassUndefinedException($className);
			}
	        $instance = new $className();
		}

        return $instance;
	}
		
	/**
	 * Load a file that defines the library class
	 * 
	 * @param string $className The name of the controller library to read
	 * @param string $subdir
	 * @return boolean
	 */
	public static function loadLibrary($className, $subdir = null)
	{
		$path = PathManager::getLibraryDirectory();
		if ($subdir != '') {
			$path .= '/' . trim($subdir, '/');
		}
		$res = self::load($className, $path, false, false);		
		return $res;
	}
	
	/**
	 * Load a file that defines the library class and return that instance
	 * 
	 * @param string $className The name of the library class to get instance
	 * @param string $subdir
	 * @return mixed
	 * @throws FileNotExistException
	 * @throws ClassUndefinedException
	 */
	public static function getLibraryInstance($className, $subdir = null)
	{		
		$res = self::loadLibrary($className, $subdir);
		if ($res == false) {
			$fileName = NameManager::toPhpFile($className);
			self::load('FileNotExistException', 'exception');
			throw new FileNotExistException($fileName);
		}
		if (!self::classExists($className)) {
			self::load('ClassUndefinedException', 'exception');
			throw new ClassUndefinedException($className);
		}
		$instance = new $className();
		
		return $instance;
	}

	/**
	 * Check whether the class is defined
	 * 
	 * @param string $className
	 * @return boolean
	 */
	public static function classExists($className)
	{		
		return class_exists($className, false);
	}
	
	/**
	 * Check whether the file exists
	 * 
	 * @param string $filePath
	 * @param boolean $searchIncludePath
	 * @return boolean
	 */
	public static function fileExists($filePath, $searchIncludePath = true)
	{
		if (file_exists($filePath)) {
			return true;
		}
		if ($searchIncludePath == true) {
			// search inside of this directory
			$fullPath = realpath(dirname(__FILE__)) . '/' . ltrim($filePath, '/');
			if (file_exists($fullPath)) {
				return true;
			}
			// search inside of include paths
			$incPaths = explode(PATH_SEPARATOR, get_include_path());
			foreach ($incPaths as $incPath) {
				$fullPath = rtrim($incPath, '/') . '/' . ltrim($filePath, '/');
				if (file_exists($fullPath)) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Add name of directory to search include file
	 * 
	 * @param string $directory
	 * @return void
	 */
	public static function addAutoloadDirectory($directory)
	{
		$dirs = $directory;
		if (!is_array($directory)) {
			$dirs = array($directory);
		}
		foreach ($dirs as $dir) {
			if (trim($dir) != '' && !in_array(trim($dir), self::$_autoloadDirs)) {
				self::$_autoloadDirs[] = $dir;
			}
		}
	}
	
	/**
	 * Include class file as autoload
	 * 
	 * @param string $className
	 * @return boolean
	 */
	public static function autoload($className)
	{
		$snake = NameManager::convertToSnake($className);
		$parts = explode('_', $snake);
		if (count($parts) == 0) {
			return false;
		}
		$fileName = NameManager::toPhpFile($className);
		$included = false;
		foreach (self::$_autoloadDirs as $dir) {
			$res = false;
			if (defined('CURRY_PATH')) {
				$res = @include_once rtrim(CURRY_PATH, '/') . '/' . $fileName;
			}
			if ($res == false) {
				$res = @include_once $dir . '/' . $fileName;
			}
			if ($res == true) {
				$included = true;
				break;
			}
		}
		if ($included == false) {
			$res = @include_once $fileName;
			if ($res == false) {
				return false;
			}
		}
		return true;
	}
}