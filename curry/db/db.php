<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * Db
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    db
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Db extends CurryClass
{
	/**
	 * Information to connect to the database.
	 * 
	 * @var array
	 */
	protected static $_config;
	
	/**
	 * Keep database connection instances as array.
	 * 
	 * @var array
	 */
	protected static $_connections = array();
	
	/**
	 * Whether to use database connection instance as singleton pattern.
	 * If set to true, always the same ensuring that the instance via script.
	 * 
	 * @var boolean
	 */
	protected static $_isSingleton = true;
		
	/**
	 * History of executed sql sentence.
	 * 
	 * @var array
	 */
	protected static $_sqlHistory = array();
	
	/**
	 * Whether to write log of executed sql sentence.
	 * 
	 * @var string
	 */
	protected static $_logKey;
	
	/**
	 * Set information to connect to the database.
	 * 
	 * @param array $config
	 * @return void
	 */
	public static function setConfig($config)
	{
		if (!is_array($config) && !($config instanceof ArrayObject)) {			
			throw new ArgumentInvalidException(__METHOD__, $config, 1, 'array or ArrayObject');
		}
		self::$_config = $config;
	}
	
	/**
	 * Get information to connect to the database.
	 * 
	 * @param string $key
	 * @return string|array
	 */
	public static function getConfig($key = null)
	{
		$ret = null;
		if ($key != null) {
			$ret = self::$_config[$key];
		} else {
			$ret = self::$_config;
		}
		return $ret;
	}
	
	/**
	 * Set wheater to get database connection object as singleton instance or not
	 * 
	 * @param boolean $bool
	 * @return boolean
	 */
	public static function isSingleton($bool = null)
	{
		if (is_bool($bool)) {
			return self::setIsSingleton($bool);			
		} else {
			return self::$_isSingleton;
		}
	}
	
	/**
	 * Set wheater to get database connection object as singleton instance or not
	 * 
	 * @param boolean $bool
	 * @return void
	 */
	public static function setIsSingleton($bool)
	{
		self::$_isSingleton = $bool;
	}
	
	/**
	 * Set sql execution log output enabled
	 * 
	 * @param string $logKey
	 * @return void
	 */
	public static function enableLogging($logKey = 'default')
	{
		self::$_logKey = $logKey;
	}
		
	/**
	 * Get PDO instance as database connection object
	 * 
	 * @param string $connectionKey
	 * @return DbAdapterAbstract
	 */
	public static function factory($connectionKey = '0')
	{
		$connection = null;
		if (self::$_isSingleton) {
			if (!is_array(self::$_connections)) {
				self::$_connections = array();
			}
			if (!array_key_exists($connectionKey, self::$_connections) || !(self::$_connections[$connectionKey] instanceof PDO)) {
				$connection = self::_connect();
				self::$_connections[$connectionKey] = $connection;
			}
			$connection = self::$_connections[$connectionKey];
		} else {
			$connection = self::_connect();
			self::$_connections[$connectionKey] = $connection;
		}
		return $connection;
	}
	
	/**
	 * Close connection
	 * 
	 * @param string $connectionKey
	 * @return void
	 */
	public static function close($connectionKey = '0')
	{
		if (isset(self::$_connections[$connectionKey])) {
			self::$_connections[$connectionKey] = null;
			unset(self::$_connections[$connectionKey]);
		}
	}
	
	/**
	 * Add executed sql sentence to history
	 * 
	 * @param string $sql SQL sentence
	 * @param array $params Parameters for executed SQL
	 * @return void
	 */
	public static function addSqlHistory($sql, $params)
	{
		$sqlInfo = array('sql' => $sql, 'params' => $params);
		self::$_sqlHistory[] = $sqlInfo;		
		if (Loader::classExists('Logger') && self::$_logKey) {
			Logger::debug(json_encode($sqlInfo), self::$_logKey);
		}
	}
	
	/**
	 * Get executed SQL history as array 
	 * 
	 * @return array
	 */
	public static function getSqlHistory()
	{
		return self::$_sqlHistory;
	}
	
	/**
	 * Get SQL executed last time
	 * 
	 * @return array
	 */
	public static function getLastSql()
	{
		$idx = count(self::$_sqlHistory) - 1;
		if ($idx < 0) {
			return '';
		}
		return self::$_sqlHistory[$idx];
	}
	
	/**
	 * Create connection instance that extends PDO
	 * 
	 * @return DbAdapterAbstract
	 * @throws Exception
	 */
	protected static function _connect()
	{
		$snake = 'db_adapter_' . strtolower(self::$_config['type']);
		$adapterName = NameManager::toClass($snake);
		Loader::load($adapterName, 'db');
		if (!Loader::classExists($adapterName)) {
			throw new ClassUndefinedException($adapterName);	
		}
		$instance = new $adapterName(self::$_config);
		return $instance;
	}
	
}