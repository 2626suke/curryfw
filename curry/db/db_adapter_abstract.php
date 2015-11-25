<?php

/**
 * DbAdapterAbstract
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
abstract class DbAdapterAbstract extends PDO
{
	/**
	 * Database driver name
	 * 
	 * @var string 
	 */
	protected $_driver;
	
	/**
	 * Keys required for dsn
	 * 
	 * @var array 
	 */
	protected $_dsnKeys = array('host', 'dbname', 'port');
	
	/**
	 * connection settings
	 * 
	 * @var array 
	 */
	protected $_config = array();
	
	/**
	 * Default port when it is not specified
	 * 
	 * @var int 
	 */
	protected $_defaultPort;
	
	/**
	 * Default charset when it is not specified
	 * 
	 * @var string 
	 */
	protected $_defaultCharset = 'utf8';
	
	/**
	 * Delimiter of dsn keys
	 * 
	 * @var string 
	 */
	protected $_dsnDelimiter = ';';
	
	/**
	 * Open database connection
	 * 
	 * @param type $config
	 * @return DbAdapterAbstract
	 */
	public function __construct($config)
	{		
		$this->setConfig($config);
		$this->preConnect();
		$dsn = $this->createDsn();
        parent::__construct($dsn, $this->_config['user'], $this->_config['password']);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->postConnect();
	}
	
	/**
	 * Create dsn text
	 * 
	 * @return string
	 * @throws Exception
	 */
	protected function createDsn()
	{
		$parts  = array();
		foreach ($this->_dsnKeys as $key) {
			if (!array_key_exists($key, $this->_config)) {
				throw new Exception('Dsn config "' . $key . '" is not specified.');
			}
			$parts[] = $key . '=' . $this->_config[$key];
		}
		$dsn = $this->_driver . ':' . implode($this->_dsnDelimiter, $parts);
		return $dsn;
	}
	
	/**
	 * Set configs for database connection
	 * 
	 * @param array $config
	 * @return void
	 */
	public function setConfig($config)
	{
		if (!isset($config['port']) || $config['port'] == '') {
			$config['port'] = $this->_defaultPort;
		}
		if (!isset($config['charset']) || $config['charset'] == '') {
			$config['charset'] = $this->_defaultCharset;
		}
		$this->_config = $config;
	}
	
	/**
	 * Get sql sentence which deletes all the rows 
	 * 
	 * @param string $tableNaame
	 * @return string
	 */
	public function getTruncateSql($tableNaame)
	{
		$sql = 'TRUNCATE TABLE ' . $tableNaame;
		return $sql;
	}
	
	/**
	 * Processing before connection 
	 * 
	 * @return void
	 */
	protected function preConnect()
	{		
	}
	
	/**
	 * Processing after connection 
	 * 
	 * @return void
	 */
	protected function postConnect()
	{		
	}
	
}
