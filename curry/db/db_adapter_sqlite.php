<?php

/**
 * @see DbAdapterAbstract
 */
require_once 'db/db_adapter_abstract.php';

/**
 * DbAdapterSqlite
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
class DbAdapterSqlite extends DbAdapterAbstract
{
	/**
	 * Database driver name
	 * 
	 * @var string 
	 */
	protected $_driver = 'sqlite';
		
	/**
	 * Data source file name
	 * 
	 * @var string 
	 */
	protected $_defaultSourceName = 'data.sqlite';
	
	/**
	 * Processing before connection 
	 * 
	 * @return void
	 */
	protected function preConnect()
	{
		$this->_config['user'] = null;
		$this->_config['password'] = null;
	}
	
	/**
	 * Create dsn text
	 * 
	 * @return string
	 * @throws Exception
	 */
	protected function createDsn()
	{
		$dsn = $this->_driver . ':' . $this->_config['source_directory'] . '/' . $this->_config['source_file'];
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
		if (!isset($config['source_directory']) || $config['source_directory'] == '') {
			$sourceDir = PathManager::getDataDirectory() . '/' . $this->_driver;
			if (!file_exists($sourceDir)) {
				if (@mkdir($sourceDir) == false) {
					throw new Exception('Failed to create data source directory "' . $sourceDir . '". Please check permission of data directory');
				}
			}
			$config['source_directory'] = $sourceDir;
		}
		if (!isset($config['source_file']) || $config['source_file'] == '') {
			$config['source_file'] = $this->_defaultSourceName;
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
		$sql = sprintf('DELETE FROM %s WHERE 1 = 1', $tableNaame);
		return $sql;
	}
}
