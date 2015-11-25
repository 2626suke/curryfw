<?php

/**
 * Session
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
class Session extends CurryClass
{
	/**
	 * Namespace of session instance
	 *
	 * @var string
	 */
	protected $_namespace = '_default';
	
	/**
	 * Whether the session has begun or not
	 *
	 * @var boolean
	 */
	protected static $_isStarted = false;
	
	/**
	 * whether a session is closed or not after writing
	 *
	 * @var boolean
	 */
	protected static $_isCloseEndWrite = false;
	
	/**
	 * Constructor
	 *
	 * @param string $namespace
	 * @return void
	 */
	public function __construct($namespace = '_default')
	{
		self::start();
		if ($namespace != null) {
			$this->_namespace = $namespace;
			if (!isset($_SESSION[$this->_namespace])) {
				$_SESSION[$this->_namespace] = array();
			}
		}
	}
	
	/**
	 * Get value of session
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key = null)
	{
		self::start();
		$ret = null;
		if ($key === null) {
			$ret = $_SESSION[$this->_namespace];
		} else {
			if ($this->exists($key)) {
				$ret = $_SESSION[$this->_namespace][$key];
			}
		}
		return $ret;
	}
	
	/**
	 * Set value of session
	 *
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public function set($key, $val)
	{
		self::start();
		$_SESSION[$this->_namespace][$key] = $val;
		if (self::$_isCloseEndWrite == true) {
			self::closeWrite();
		}
	}
		
	/**
	 * Overriding parent.
	 * Alias of method "set".
	 *
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public function __set($key, $val)
	{
		$this->set($key, $val);
	}
	
	/**
	 * Overriding parent.
	 * Alias of method "get".
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key = null)
	{
		return $this->get($key);
	}
	
	/**
	 * Check wheater session has key or not
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key)
	{
		if (!isset($_SESSION[$this->_namespace][$key])) {
			return false;
		}
		return true;
	}
	
	/**
	 * Remove session value
	 *
	 * @param string $key
	 * @return void
	 */
	public function remove($key)
	{
		if ($this->exists($key) == false) {
			return;
		}
		unset($_SESSION[$this->_namespace][$key]);
	}
	
	/**
	 * Clear session values of this instance
	 *
	 * @return void
	 */
	public function clear()
	{
		self::start();
		unset($_SESSION[$this->_namespace]);
	}
	
	/**
	 * Start session
	 *
	 * @return void
	 */
	public static function start()
	{
		if (self::$_isStarted == false) {
			session_start();
			self::$_isStarted = true;
		}
	}
	
	/**
	 * Set whether a session is closed or not after writing,
	 * 
	 * @param type $isClose
	 * @return void
	 */
	public static function isCloseEndWrite($isClose)
	{
		self::setIsCloseEndWrite($isClose);
	}
	
	/**
	 * Set whether a session is closed or not after writing,
	 *
	 * @param type $isClose
	 * @return void
	 */
	public static function setIsCloseEndWrite($isClose)
	{
		self::$_isCloseEndWrite = $isClose;
	}
	
	
	/**
	 * Close session write
	 *
	 * @return void
	 */
	public static function closeWrite()
	{
		session_write_close();
		self::$_isStarted = false;
	}
	
}