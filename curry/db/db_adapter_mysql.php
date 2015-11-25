<?php

/**
 * @see DbAdapterAbstract
 */
require_once 'db/db_adapter_abstract.php';

/**
 * DbAdapterMysql
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
class DbAdapterMysql extends DbAdapterAbstract
{
	/**
	 * Database driver name
	 * 
	 * @var string 
	 */
	protected $_driver = 'mysql';
	
	/**
	 * Default port when it is not specified
	 * 
	 * @var int 
	 */
	protected $_defaultPort = 3306;
	
	/**
	 * Processing after connection 
	 * 
	 * @return void
	 */
	protected function postConnect()
	{	
		$this->query('SET NAMES ' . $this->_config['charset']);
	}
}
