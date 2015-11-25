<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * Model
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
class Model extends CurryClass
{
	/**
	 * PDO instance
	 *
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 * Alias ​​for a table in SQL statements
	 *
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Schema name of database.
	 * 
	 * @var string
	 */
	protected $schema;
	
	/**
	 * Default PDO instance
	 *
	 * @var PDO
	 */
	protected static $_defaultDb;
	
	/**
	 * Whether there are class actions in the case as the horse is not a class definition of table
	 *
	 * @var boolean
	 */
	protected static $_allowVirtual = true;
		
	/**
	 * Constructor
	 *
	 * @param string $tableName Table name to associate with class
	 * @param PDO $db PDO instance
	 * @return void
	 */
	public function __construct($tableName = null, $db = null)
	{
		if ($db == null) {
			if (self::$_defaultDb instanceof PDO) {
				$db = self::$_defaultDb;
			} else {
				$db = Db::factory();
			}
		}
		$this->db = $db;
		
		if ($tableName != null) {
			$this->name = $tableName;
		} else if ($this->name == null) {
			$this->setDefaultTableName();
		}
	}
	
	/**
	 * Set alias for a table in SQL statements
	 *
	 * @param string $alias Alias​for the table
	 * @return void
	 */
	public function setAlias($alias)
	{
		$this->alias = $alias;
	}
	
	/**
	 * Get alias for a table in SQL statements that is currently set
	 *
	 * @return string
	 */
	public function getAlias()
	{
		return $this->alias;
	}

	/**
	 * Set table name to associate with class
	 *
	 * @param string $name table name to associate with class
	 * @return void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Get table name to associate with class that is currently set
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set schema name
	 *
	 * @param string $schema
	 * @return void
	 */
	public function setSchema($schema)
	{
		$this->schema = $schema;
	}
	
	/**
	 * Get schema name
	 *
	 * @return string
	 */
	public function getSchema()
	{
		return $this->schema;
	}
	
	/**
	 * Set whether there are class actions in the case as the horse is not a class definition of table
	 *
	 * @param boolean $isAllow
	 * @return void
	 */
	public static function allowVirtual($isAllow)
	{
		self::$_allowVirtual = $isAllow;
	}
	
	/**
	 * Get whether there are class actions in the case as the horse is not a class definition of table
	 *
	 * @return boolean
	 */
	public static function isAllowVirtual()
	{
		return self::$_allowVirtual;
	}
	
	/**
	 * Set default PDO instance to be used case if no instance is particularly
	 *
	 * @param PDO $db
	 * @return void
	 */
	public static function setDefaultConnection($db)
	{
		self::$_defaultDb = $db;
	}
	
	/**
	 * Set PDO instance used in this instance
	 *
	 * @param PDO $db
	 * @return void
	 */
	public function setConnection($db)
	{
		$this->db = null;
		$this->db = $db;
	}

	/**
	 * Get PDO instance that is used in this instance
	 *
	 * @return PDO $db
	 */
	public function getConnection()
	{
		return $this->db;
	}
	
	/**
	 * Get instance of SqlSelect to build a select statement
	 *
	 * @return SqlSelect
	 * @throws Exception
	 */
	public function select()
	{
		if ($this->name == null) {
			throw new Exception('not specified table name.');
		}
		Loader::load('SqlSelect', 'db');
		$select = new SqlSelect($this->db, $this->name, $this->alias);
		if ($this->schema != null) {
			$select->setSchema($this->schema);
		}
		return $select;
	}
			
	/**
	 * Get instance of SqlUpdate to build a update statement
	 *
	 * @return SqlUpdate
	 * @throws Exception
	 */
	public function update()
	{
		if ($this->name == null) {
			throw new Exception('not specified table name.');
		}
		Loader::load('SqlUpdate', 'db');
		$upd = new SqlUpdate($this->db, $this->name, $this->alias);
		if ($this->schema != null) {
			$upd->setSchema($this->schema);
		}
		return $upd;
	}
			
	/**
	 * Get instance of SqlInsert to build a insert statement
	 *
	 * @return SqlInsert
	 * @throws Exception
	 */	
	public function insert()
	{
		if ($this->name == null) {
			throw new Exception('not specified table name.');
		}
		Loader::load('SqlInsert', 'db');
		$ins = new SqlInsert($this->db, $this->name);
		if ($this->schema != null) {
			$ins->setSchema($this->schema);
		}
		return $ins;
	}
			
	/**
	 * Get instance of SqlDelete to build a delete statement
	 *
	 * @return SqlDelete
	 * @throws Exception
	 */
	public function delete()
	{
		if ($this->name == null) {
			throw new Exception('not specified table name.');
		}
		Loader::load('SqlDelete', 'db');
		$del = new SqlDelete($this->db, $this->name);
		if ($this->schema != null) {
			$del->setSchema($this->schema);
		}
		return $del;
	}
	
	/**
	 * Create table name by this class name and set
	 *
	 * @return void
	 */
    protected function setDefaultTableName()
    {
        $className = get_class($this);
		$this->name = NameManager::toTable($className);
    }
	
	/**
	 * Create table name by this class name and set
	 *
	 * @param string $tableName If not specified, use the table name for this class
	 * @return array
	 */
	public function getColumnsInfo($tableName = null)
	{
		$dbms = Db::getConfig('type');
		$ret = null;
		switch ($dbms) {
			case 'mysql':
				$ret = $this->getColumnInfoMysql($tableName);
				break;
			case 'pgsql':
				$ret = $this->getColumnInfoPgsql($tableName);
				break;
		}
		return $ret;
	}
	
	/**
	 * For MySQL.
	 * Create table name by this class name and set.
	 *
	 * @param string $tableName If not specified, use the table name for this class
	 * @return void
	 */
	protected function getColumnInfoMysql($tableName = null)
	{
		if ($tableName == null) {
			$tableName = $this->name;
		}
		$sql ='SELECT * FROM ' . $tableName . ' WHERE 1 = 0';
		$stmt = $this->db->query($sql);
		
		$columns = array();
		for ($i = 0; $i < $stmt->columnCount(); $i++) {
			$columnInfo = $stmt->getColumnMeta($i);
			$colName = $columnInfo['name'];
			$tmp = array();
			$tmp['index'] = $i;
			$tmp['name'] = $colName;
			$tmp['length'] = $columnInfo['len'];
			$tmp['is_string'] = true;
			if ($columnInfo['native_type'] == 'LONG') {
				$tmp['is_string'] = false;
			}
			$tmp['primary_key'] = false;
			if (in_array('primary_key', $columnInfo['flags'])) {
				$tmp['primary_key'] = true;
			}
			$tmp['not_null'] = false;
			if (in_array('not_null', $columnInfo['flags'])) {
				$tmp['not_null'] = true;
			}
			$columns[$colName] = $tmp;
		}
		return $columns;		
	}
	
	/**
	 * For PostgreSQL.
	 * Create table name by this class name and set.
	 *
	 * @param string $tableName If not specified, use the table name for this class
	 * @return array
	 */
	protected function getColumnInfoPgsql($tableName = null)
	{
		if ($tableName == null) {
			$tableName = $this->name;
		}
		$sql = "
			SELECT
			  ATT.attname AS name,
			  CASE 
			    WHEN ATT.attlen < 0 THEN atttypmod - 4
			    ELSE attlen
			  END AS length,
			  CASE 
			    WHEN
			      TYP.typname LIKE 'int%' OR
			      TYP.typname LIKE 'float%' OR
			      TYP.typname LIKE 'serial%' OR
			      TYP.typname = 'decimal'
			    THEN 0
			    ELSE 1
			  END AS is_string,
			  CAST(att.attnotnull AS int) AS not_null,
			  CASE
			    WHEN
			     CON.contype = 'p' THEN 1
			    ELSE 0
			  END AS primary_key
			FROM
			  pg_stat_user_tables STAT
			INNER JOIN
			  pg_attribute AS ATT
			ON
			  ATT.attrelid = STAT.relid  AND
			  ATT.attnum > 0
			INNER JOIN
			  pg_type AS TYP
			ON
			  TYP.oid = ATT.atttypid
			LEFT JOIN
			  pg_constraint AS CON
			ON
			  CON.conrelid = ATT.attrelid AND
			   ATT.attnum = ANY(CON.conkey)
			WHERE
			  STAT.schemaname = 'public' AND
			  STAT.relname = '" .$tableName . "' 
			ORDER BY
			  ATT.attnum
			";
		$stmt = $this->db->query($sql);
		$rows = $stmt->fetchAll();
		
		$columns = array();
		$i = 0;
		foreach ($rows as $row) {
			$tmp = array();
			$tmp['index'] = $i;
			$colName = $row['name'];
			$tmp['name'] = $colName;
			$tmp['length'] = $row['length'];
			// 文字列型かどうか
			$tmp['is_string'] = (boolean)$row['is_string'];
			// 主キーかどうか
			$tmp['primary_key'] = (boolean)$row['primary_key'];
			// null不可かどうか
			$tmp['not_null'] = (boolean)$row['not_null'];
			
			$columns[$colName] = $tmp;
			$i++;
		}
		return $columns;
	}
	
	/**
	 * Return names of the table's primary key field
	 *
	 * @param string $tableName If not specified, use the table name for this class
	 * @return array
	 */
	public function getKeyColumns($tableName = null)
	{
		$columnsInfo = $this->getColumnsInfo($tableName);
		$keys = array();
		foreach ($columnsInfo as $col => $info) {
			if (true == $info['primary_key']) {
				$keys[] = $col;
			}
		}
		return $keys;
	}
	
	/**
	 * Execute select statement specify primary key field values
	 * and return single row information as array
	 *
	 * @return array $row Array of row information
	 */
	public function find()
	{
		$args = func_get_args();
		$keyCols = $this->getKeyColumns();
		
		if (count($args) != count($keyCols)) {
			return false;
		}
		
		$sel = $this->select();
		$i = 0;
		foreach ($keyCols as $col) {
			$sel->where($col, $args[$i]);
			$i++;
		}
		$row = $sel->fetchRow();
		
		return $row;
	}
	
	/**
	 * Execute a SELECT statement without where condition
	 * and return all rows information as array
	 *
	 * @param string $order
	 * @return array
	 */
	public function selectAll($order = null)
	{
		$sel = $this->select();
		if ($order) {
			$sel->order($order);
		}
		$rows = $sel->fetchAll();
		return $rows;
	}
	
	/**
	 * Overloading parent.
	 * 
	 * If it starts with the beginning of the parameter values ​​"selectBy",
	 * Execute SELECT statement and return rows inforamtion
	 * by specifying a value for the field was extracted from the subsequent part.
	 *
	 * If it starts with the beginning of the parameter values ​​"findBy",
	 * Execute SELECT statement and return single row information
	 * by specifying a value for the field was extracted from the subsequent part.
	 * 
	 * @return array
	 */	
	public function __call($name, $args)
	{
		$snake = NameManager::convertToSnake($name);
		if (substr($snake, 0, 10) == 'select_by_') {
			$methodField = substr($snake, 10);
			$rows = $this->_selectByMethodName($methodField, $args);
			return $rows;
		}
		if (substr($snake, 0, 8) == 'find_by_') {
			$methodField = substr($snake, 8);
			$row = $this->_findByMethodName($methodField, $args);
			return $row;
		}
		parent::__call($name, $args);
	}

	/**
	 * Execute a SELECT statement using WHERE condition
	 * created by the field names and values ​​specified in the parameter
	 * and return rows information as sql result.
	 *
	 * @param string  $name Field names that combined by "And"
	 * @param array  $args Values for fields
	 * @return array
	 */	
	private function _selectByMethodName($name, $args)
	{
		$columns = explode('_and_', $name);
		$i = 0;
		$sel = $this->select();
		foreach ($columns as $column) {
			$columnName = NameManager::toTable($column);
			$sel->where($columnName, $args[$i]);
			$i++;
		}
		if (count($args) > count($columns)) {
			$sel->order($args[$i]);
		}
		$rows = $sel->fetchAll();
		return $rows;
	}

	/**
	 * Execute a SELECT statement using WHERE condition
	 * created by the field names and values ​​specified in the parameter
	 * and return single row information as sql result.
	 *
	 * @param string $name Field names that combined by "And"
	 * @param array $args Values for fields
	 * @return array
	 */	
	private function _findByMethodName($name, $args)
	{
		$rows = $this->_selectByMethodName($name, $args);
		$row = false;
		if (is_array($rows) && count($rows) > 0) {
			$row = $rows[0];
		}
		return $row;
	}
}