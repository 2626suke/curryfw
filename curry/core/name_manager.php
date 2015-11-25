<?php

/**
 * NameCase
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
class NameCase
{
	const PASCAL = 1;
	const CAMEL  = 2;
	const SNAKE  = 3;
}

/**
 * NameManager
 *
 * @category   Curry
 * @package    core
 * @copyright  Copyright (c) 2011 www.curryfw.net.
 * @license    MIT License
 */
class NameManager
{
	/**
	 * Suffix of controller class name
	 *
	 * @var string
	 */
	private static $_controllerSuffix = 'Controller';
		
	/**
	 * Suffix of view class name
	 *
	 * @var string
	 */
	private static $_viewSuffix = 'View';
	
	/**
	 * Suffix of action method name in controller class
	 *
	 * @var string
	 */
	private static $_actionSuffix  = '';
	
	/**
	 * Rest suffix of action method name
	 *
	 * @var array
	 */
	protected static $_restActionSuffix = array(
		'GET'    => '',
		'POST'   => 'post',
		'PUT'    => 'put',
		'DELETE' => 'delete'
	);
	
	/**
	 * Naming convention of class name
	 *
	 * @var int
	 */
	private static $_classCase     = NameCase::PASCAL;
	
	/**
	 * Naming convention of method name
	 *
	 * @var int
	 */
	private static $_methodCase    = NameCase::CAMEL;
	
	/**
	 * Naming convention of file name
	 *
	 * @var int
	 */
	private static $_fileCase      = NameCase::SNAKE;
	
	/**
	 * Naming convention of database table name
	 *
	 * @var int
	 */
	private static $_tableCase     = NameCase::SNAKE;
	
	/**
	 * Extension of php file
	 *
	 * @var string
	 */
	private static $_phpExtension  = 'php';
	
	/**
	 * Extension of view template file
	 *
	 * @var string
	 */
	private static $_tplExtension  = 'php';
	
	/**
	 * Set suffix of controller class name
	 *
	 * @param string $suffix
	 * @return void
	 */
	public static function setControllerSuffix($suffix)
	{
		self::$_controllerSuffix = $suffix;
	}
	
	/**
	 * Get suffix of controller class name
	 *
	 * @return string
	 */
	public static function getControllerSuffix()
	{
		return self::$_controllerSuffix;
	}
	
	/**
	 * Set suffix of action method name in controller class
	 *
	 * @param string $suffix
	 * @return void
	 */
	public static function setActionSuffix($suffix)
	{
		self::$_actionSuffix = $suffix;
	}

	/**
	 * Get suffix of action method name in controller class
	 *
	 * @return string
	 */
	public static function getActionSuffix()
	{
		return self::$_actionSuffix;
	}
	
	/**
	 * Set rest suffix of action method name
	 *
	 * @params array $suffixes
	 * @return void
	 */
	public static function setRestActionSuffix(array $suffixes)
	{
		foreach ($suffixes as $method => $suffix) {
			if (array_key_exists($method, self::$_restActionSuffix)) {
				self::$_restActionSuffix[$method] = $suffix;
			}
		}
	}
	
	/**
	 * Get rest suffix of action method name
	 *
	 * @params string $method
	 * @return string
	 */
	public static function getRestActionSuffix($method)
	{
		if (array_key_exists($method, self::$_restActionSuffix)) {
			return self::$_restActionSuffix[$method];
		}
	}
	
	/**
	 * Set naming convention of class name
	 *
	 * @param string $nameCase
	 * @return void
	 */
	public static function setClassCase($nameCase)
	{
		self::$_classCase = $nameCase;
	}

	/**
	 * Get naming convention of class name
	 *
	 * @return string
	 */
	public static function getClassCase()
	{
		return self::$_classCase;
	}
	
	/**
	 * Set naming convention of method name
	 *
	 * @param string $nameCase
	 * @return void
	 */
	public static function setMethodCase($nameCase)
	{
		self::$_methodCase = $nameCase;
	}
	
	/**
	 * Get naming convention of method name
	 *
	 * @return string
	 */
	public static function getMethodCase()
	{
		return self::$_methodCase;
	}
	
	/**
	 * Set naming convention of file name
	 *
	 * @param string $nameCase
	 * @return void
	 */
	public static function setFileCase($nameCase)
	{
		self::$_fileCase = $nameCase;
	}

	/**
	 * Get naming convention of file name
	 *
	 * @return string
	 */
	public static function getFileCase()
	{
		return self::$_fileCase;
	}
	
	/**
	 * Set naming convention of database table name
	 *
	 * @param string $nameCase
	 * @return void
	 */
	public static function setTableCase($nameCase)
	{
		self::$_tableCase = $nameCase;
	}
	
	/**
	 * Get naming convention of table name name
	 *
	 * @return string
	 */
	public static function getTableCase()
	{
		return self::$_tableCase;
	}	
	
	/**
	 * Set extension of php file
	 *
	 * @param string $extension
	 * @return void
	 */
	public static function setPhpExtension($extension)
	{
		self::$_phpExtension = $extension;
	}
	
	/**
	 * Get extension of php file
	 *
	 * @return string
	 */
	public static function getPhpExtension()
	{
		return self::$_phpExtension;
	}

	/**
	 * Set extension of view template file
	 *
	 * @param string $extension
	 * @return void
	 */
	public static function setTemplateExtension($extension)
	{
		self::$_tplExtension = $extension;
	}
	
	/**
	 * Get extension of view template file
	 *
	 * @return string
	 */
	public static function getTemplateExtension()
	{
		return self::$_tplExtension;
	}

	/**
	 * Convert a string to naming convention of class
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toClass($str)
	{
		return self::convert($str, self::$_classCase);
	}
	
	/**
	 * Convert a string to naming convention of method
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toMethod($str)
	{
		return self::convert($str, self::$_methodCase);
	}
	
	/**
	 * Convert a string to naming convention of database table
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toTable($str)
	{
		return self::convert($str, self::$_tableCase);
	}
	
	/**
	 * Convert a string to naming convention of file
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toFile($str)
	{
		return self::convert($str, self::$_fileCase);
	}
		
	/**
	 * Convert a string to naming convention of file and add extension of php
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toPhpFile($str)
	{
		return self::toFile($str) . '.' . self::$_phpExtension;
	}
	
	/**
	 * Convert contoller key to controller class name
	 *
	 * @param string $controller
	 * @return string
	 */
	public static function convertControllerToClass($controller)
	{
		return self::toClass($controller . '_' . self::$_controllerSuffix);
	}
	
	/**
	 * Convert contoller key to controller class name
	 *
	 * @param string $controller
	 * @return string
	 */
	public static function convertControllerToViewClass($controller)
	{
		return self::toClass($controller . '_' . self::$_viewSuffix);
	}
	
	/** 
	 * Convert action key to action method name
	 * 
	 * @param string $action
	 * @param string $method
	 * @return string
	 */
	public static function convertActionToMethod($action, $method = null)
	{
		$restSuffix = '';
		if ($method != null) {
			if (array_key_exists($method, self::$_restActionSuffix)) {
				$restSuffix = '_' . self::$_restActionSuffix[$method];
			}
		}
		return self::toMethod($action . $restSuffix . '_' . self::$_actionSuffix);
	}
	
	/**
	 * Convert a string to pascal case
	 *
	 * @param string $name
	 * @return string
	 */
	public static function convertToPascal($name)
	{
		return self::convert($name, NameCase::PASCAL);
	}

	/**
	 * Convert a string to camel case
	 *
	 * @param string $name
	 * @return string
	 */
	public static function convertToCamel($name)
	{
		return self::convert($name, NameCase::CAMEL);
	}
	
	/**
	 * Convert a string to snake case
	 *
	 * @param string $name
	 * @return string
	 */
	public static function convertToSnake($name)
	{
		return self::convert($name, NameCase::SNAKE);
	}
	
	/**
	 * Convert a string to case that specified by parameter
	 *
	 * @param string $name
	 * @param int $nameCase Naming convention
	 * @return string
	 */
	public static function convert($name, $nameCase)
	{
		$name = trim($name);
        $len = strlen($name);
        
		$words = array();
		$wordIndex = 0;
		for ($i = 0; $i < $len; $i++) {
            $char = substr($name, $i, 1);
            $lower = strtolower($char);
			if (isset($words[$wordIndex]) && $words[$wordIndex] != '') {
				if ($char == '_' || $char != $lower) {
					$wordIndex++;
				}
			}
			if ($char == '_' && $i > 0) {
				continue;
			}
			if (!isset($words[$wordIndex])) {
            	$words[$wordIndex] = '';
            }
			$words[$wordIndex] .= $lower;
		}
		
		$ret = '';
		if ($nameCase == NameCase::SNAKE) {
			$ret = implode('_', $words);
		} else {
			foreach ($words as $idx => $word) {
				if ($nameCase == NameCase::PASCAL || $idx > 0) {
					$ret .= ucfirst($word);
				} else {
					$ret .= $word;
				}
			}
		}
		
		return $ret;
	}	
	
	/**
	 * Split by pause of word
	 * 
	 * @param string $name
	 * @return array
	 */
	public static function split($name)
	{
		$snake = self::convertToSnake($name);
		$parts = explode('_', $snake);
		return $parts;
	}
}