<?php

/**
 * Service
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
class Service extends CurryClass
{
	/**
	 * Database connection instance
	 *
	 * @var DbAdapterAbstract
	 */
	protected $db;
	
	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->db = Db::factory();
	}
	
	/**
	 * 
	 * @param string $className
	 * @param string $alias
	 * @param string $subdir
	 * @return Model
	 */
	public function model($className, $alias = null, $subdir = null)
	{
		$model = Loader::getModelInstance($className, $subdir);
		if ($alias != null) {
			$model->setAlias($alias);
		}
		$model->setConnection($this->db);
		
		return $model;
	}
	
	/**
	 * This method is alias of the method "beginTransaction".
	 *
	 * @return void
	 */
	protected function begin()
	{
		$this->beginTransaction();
	}
	
	/**
	 * Begin the database transaction
	 *
	 * @return void
	 */
	protected function beginTransaction()
	{
		$this->db->beginTransaction();
	}
	
	/**
	 * Commit the database transaction
	 *
	 * @return void
	 */
	protected function commit()
	{
		$this->db->commit();
	}
	
	/**
	 * Rollback the database transaction
	 *
	 * @return void
	 */
	protected function rollback()
	{
		$this->db->rollback();
	}
	
}