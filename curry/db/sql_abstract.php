<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * @see Db
 */
require_once 'db/db.php';

/**
 * SqlAbstract
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
abstract class SqlAbstract extends CurryClass
{	
	/**
	 * DbAdapterAbstract instance
	 *
	 * @var DbAdapterAbstract
	 */	
	protected $_adapter;
	
	/**
	 * Name of database table related with this class as candidate for processing
	 *
	 * @var string
	 */
	protected $_tableName;
	
	/**
	 * Alias of table name in the inside of an SQL sentence
	 *
	 * @var string
	 */
	protected $_alias;
	
	/**
	 * Schema name of database.
	 * 
	 * @var string
	 */
	protected $_schema;
	
	/**
	 * Parts of sql conditions
	 *
	 * @var array
	 */
	protected $_parts = array();
		
	/**
	 * Parameters for place holder
	 *
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * PDO statement instance
	 *
	 * @var PDOStatement
	 */
	protected $_statement;
	
	/**
	 * Whether to create prepared statement or not, when you run sql repeatedly
	 *
	 * @var boolean
	 */
	protected static $_preparing = true;
	
	/**
	 * Whether convert empty string to null 
	 *
	 * @var boolean
	 */
	protected static $_convertEmptyToNull = false;
	
	/**
	 * Instance number
	 *
	 * @var int
	 */
	protected $_instanceNo;
	
	/**
	 * Instance number to give to next create instance
	 *
	 * @var int
	 */
	protected static $_newInstanceNo = 0;
	
	/**
	 * Constructor
	 *
	 * @param DbAdapterAbstract $adapter Database adapter instance
	 * @param string $tableName Table name 
	 * @param string $alias Alias of table name in the inside of an SQL sentence
	 * @return void
	 */
	public function __construct($adapter, $tableName, $alias = null)
	{
		$this->_adapter = $adapter;
		$this->_tableName = $tableName;
		$this->_alias = $alias;
		$this->_instanceNo = self::$_newInstanceNo;
		self::$_newInstanceNo++;
	}
	
	/**
	 * Set whether convert empty string to null 
	 * 
	 * @param boolean $isConvert
	 * @return void
	 */
	public static function setIsConvertEmptyToNull($isConvert)
	{
		self::$_convertEmptyToNull = $isConvert;
	}
	
	/**
	 * Create and return SQL sentence
	 *
	 * @return string
	 */
	abstract public function getSql();
	
	/**
	 * Set alias of table name
	 *
	 * @param string $alias
	 * @return SqlAbstract $this Self instance
	 */
	public function setAlias($alias)
	{
		$this->_alias = $alias;
		return $this;
	}
	
	/**
	 * Get alias of table name
	 *
	 * @return string
	 */
	public function getAlias()
	{
		return $this->_alias;
	}
	
	/**
	 * Set schema name
	 *
	 * @param string $schema	 * @return void

	 */
	public function setSchema($schema)
	{
		$this->_schema = $schema;
	}
	
	/**
	 * Get schema name
	 *
	 * @return string
	 */
	public function getSchema()
	{
		return $this->_schema;
	}
	
	/**
	 * Set whether to create prepared statement or not, when you run sql repeatedly
	 *
	 * @param boolean $enabled
	 * @return void
	 */
	public static function enablePreparing($enabled)
	{
		self::$_preparing = $enabled;
	}
		
	/**
	 * Get parameters
	 *
	 * @return array
	 */
	public function getParams()
	{
		return $this->_params;
	}
	
	/**
	 * Add WHERE condition of equality combined as AND
	 *
	 * @param mixed $where
	 * @param mixed $param
	 * @return SqlAbstract $this Self instance
	 */
	public function where($where, $param = null)
	{
		if (func_num_args() == 1) {
			$this->_setWhereDirect(false, $where);
		} else {
			if (is_array($where)) { 
				throw new Exception('Second argument can not be passed, when first argument is array.');
			} else if (is_array($param)) {
				$this->_setWhereDirect(false, $where, $param);
			} else {
				$this->_setWhere($where, $param, false, '=');
			}
		}
		return $this;
	}

	/**
	 * Add WHERE condition of equality combined as OR
	 *
	 * @param mixed $where
	 * @param mixed $param
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhere($where, $param = null)
	{
		if (func_num_args() == 1) {
			$this->_setWhereDirect(true, $where);
		} else {
			if (is_array($where)) { 
				throw new Exception('Second argument can not be passed, when first argument is array.');
			} else if (is_array($param)) {
				$this->_setWhereDirect(true, $where, $param);
			} else {
				$this->_setWhere($where, $param, true, '=');
			}
		}
		return $this;
	}
	
	/**
	 * Add WHERE condition of inequality combined as AND
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function whereNot($column, $value)
	{
		$this->_setWhere($column, $value, false, '<>');
		return $this;
	}
	
	/**
	 * Add WHERE condition of inequality combined as OR
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereNot($column, $value)
	{
		$this->_setWhere($column, $value, true, '<>');
		return $this;
	}
	
	/**
	 * Add WHERE condition of greater combined as AND
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function whereGt($column, $value)
	{
		$this->_setWhere($column, $value, false, '>');
		return $this;
	}
	
	/**
	 * Add WHERE condition of less than combined as AND
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function whereLt($column, $value)
	{
		$this->_setWhere($column, $value, false, '<');
		return $this;
	}
	
	/**
	 * Add WHERE condition of greater or equal combined as AND
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function whereGe($column, $value)
	{
		$this->_setWhere($column, $value, false, '>=');
		return $this;
	}
	
	/**
	 * Add WHERE condition of less than or equal combined as AND
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function whereLe($column, $value)
	{
		$this->_setWhere($column, $value, false, '<=');
		return $this;
	}
		
	/**
	 * Add WHERE condition of greater combined as OR
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereGt($column, $value)
	{
		$this->_setWhere($column, $value, true, '>');
		return $this;
	}
	
	/**
	 * Add WHERE condition of less than combined as OR
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereLt($column, $value)
	{
		$this->_setWhere($column, $value, true, '<');
		return $this;
	}
	
	/**
	 * Add WHERE condition of greater or equal combined as OR
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereGe($column, $value)
	{
		$this->_setWhere($column, $value, true, '>=');
		return $this;
	}
	
	/**
	 * Add WHERE condition of less than or equal combined as OR
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereLe($column, $value)
	{
		$this->_setWhere($column, $value, true, '<=');
		return $this;
	}
	
	/**
	 * Add WHERE ambiguous condition of equality combined as AND
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function whereLike($column, $value)
	{
		$this->_setWhere($column, $value, false, 'LIKE', true);
		return $this;
	}

	/**
	 * Add WHERE ambiguous condition of equality combined as OR
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereLike($column, $value)
	{
		$this->_setWhere($column, $value, true, 'LIKE', true);
		return $this;
	}

	/**
	 * Add WHERE ambiguous condition of inequality combined as AND
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function whereNotLike($column, $value)
	{
		$this->_setWhere($column, $value, false, 'NOT LIKE', true);
		return $this;
	}

	/**
	 * Add WHERE ambiguous condition of inequality combined as OR
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereNotLike($column, $value)
	{
		$this->_setWhere($column, $value, true, 'NOT LIKE', true);
		return $this;
	}
	
	/**
	 * Add WHERE IN condition of equality combined as AND
	 *
	 * @param string $column
	 * @param array $values
	 * @return SqlAbstract $this Self instance
	 */
	public function whereIn($column, array $values)
	{
		$this->_setWhereIn($column, $values, false, false);
		return $this;
	}
	
	/**
	 * Add WHERE IN condition of equality combined as OR
	 *
	 * @param string $column
	 * @param array $values
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereIn($column, array $values)
	{
		$this->_setWhereIn($column, $values, true, false);
		return $this;
	}
	
	/**
	 * Add WHERE IN condition of inequality combined as AND
	 *
	 * @param string $column
	 * @param array $values
	 * @return SqlAbstract $this Self instance
	 */
	public function whereNotIn($column, array $values)
	{
		$this->_setWhereIn($column, $values, false, true);
		return $this;
	}
	
	/**
	 * Add WHERE IN condition of inequality combined as OR
	 *
	 * @param string $column
	 * @param array $values
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereNotIn($column, array $values)
	{
		$this->_setWhereIn($column, $values, true, true);
		return $this;
	}
	
	/**
	 * Add WHERE range condition combined as AND
	 *
	 * @param string $column
	 * @param mixed $minValue
	 * @param mixed $maxValue
	 * @return SqlAbstract $this Self instance
	 */
	public function whereBetween($column, $minValue, $maxValue)
	{
		$this->_setWhereBetween($column, $minValue, $maxValue, false, false);
		return $this;		
	}
	
	/**
	 * Add WHERE range condition combined as OR
	 *
	 * @param string $column
	 * @param mixed $minValue
	 * @param mixed $maxValue
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereBetween($column, $minValue, $maxValue)
	{
		$this->_setWhereBetween($column, $minValue, $maxValue, true, false);
		return $this;		
	}
	
	/**
	 * Add WHERE out of range condition combined as AND
	 *
	 * @param string $column
	 * @param mixed $minValue
	 * @param mixed $maxValue
	 * @return SqlAbstract $this Self instance
	 */
	public function whereNotBetween($column, $minValue, $maxValue)
	{
		$this->_setWhereBetween($column, $minValue, $maxValue, false, true);
		return $this;		
	}
	
	/**
	 * Add WHERE out of range condition combined as OR
	 *
	 * @param string $column
	 * @param mixed $minValue
	 * @param mixed $maxValue
	 * @return SqlAbstract $this Self instance
	 */
	public function orWhereNotBetween($column, $minValue, $maxValue)
	{
		$this->_setWhereBetween($column, $minValue, $maxValue, true, true);
		return $this;		
	}

	/**
	 * add WHERE condition of equality compared as expression 
	 * 
	 * @param string $column
	 * @param string $expression
	 * @return SqlAbstract
	 */
	public function whereExpr($column, $expression)
	{
		$this->_setWhereExpr($column, $expression);
		return $this;
	}
	
	/**
	 * add WHERE condition of inequality compared as expression 
	 * 
	 * @param string $column
	 * @param string $expression
	 * @return SqlAbstract
	 */
	public function whereNotExpr($column, $expression)
	{
		$this->_setWhereExpr($column, $expression, false, '<>');
		return $this;
	}
	
	/**
	 * add WHERE condition of equality compared as expression combined as OR
	 * 
	 * @param string $column
	 * @param string $expression
	 * @return SqlAbstract
	 */
	public function orWhereExpr($column, $expression)
	{
		$this->_setWhereExpr($column, $expression, true);
		return $this;
	}
	
	/**
	 * add WHERE condition of inequality compared as expression combined as OR
	 * 
	 * @param string $column
	 * @param string $expression
	 * @return SqlAbstract
	 */
	public function orWhereNotExpr($column, $expression)
	{
		$this->_setWhereExpr($column, $expression, true, '<>');
		return $this;
	}
	
	/**
	 * Add parametets
	 * 
	 * @param mixed $param
	 * @param mixed $value
	 * @return SqlAbstract
	 * @throws Exception
	 */
	public function params($param, $value = null)
	{
		if (func_num_args() == 1) {
			if (is_array($param) == false) {
				throw new Exception('if second parameter is not passed, first parameter must be array');
			}
			foreach ($param as $key => $val) {
				$this->_params[$key] = $val;
			}			
		} else {		
			if (is_array($param) == true) {
				throw new Exception('if second parameter is passed, first parameter must be string');
			}
			$this->_params[$param] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Execute sql.
	 *
	 * @return boolean
	 */
	public function execute()
	{
		$this->_createStatement();
		$res = $this->_statement->execute();
		$this->clearConditions();	
		return $res;
	}
	
	/**
	 * Clear sql conditions
	 *
	 * @return void
	 */
	public function clearConditions()
	{
		$this->_parts = array();
		$this->_params = array();
	}
	
	/**
	 * Get pdo statement instance
	 *
	 * @return PDOStatement
	 */
	public function getStatement()
	{
		return $this->_statement;
	}
		
	/**
	 * Create pdo statement instance using conditions 
	 *
	 * @return void
	 */
	protected function _createStatement()
	{
		$sql = $this->getSql();
		if (self::$_preparing == false || !($this->_statement instanceof PDOStatement)) {
			$this->_statement = $this->_adapter->prepare($sql);
		} else {
			if ($this->_statement->queryString != $sql) {
				$this->_statement = $this->_adapter->prepare($sql);
			}
		}
		if (is_array($this->_params) && count($this->_params) > 0) {
			foreach ($this->_params as $key => $val) {
				$this->_statement->bindValue(':' . $key, $val);
			}
		}
		Db::addSqlHistory($sql, $this->_params);
	}
	
	/**
	 * Create parameter key
	 *
	 * @return string
	 */
	protected function _createParamKey()
	{
		$paramKey = 'i' . $this->_instanceNo .  '_p' . count($this->_params);
		return $paramKey;
	}

	/**
	 * Add WHERE condition
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @param boolean $or
	 * @param string $comparison
	 * @return void
	 */
	protected function _setWhere($column, $value, $or = false, $comparison = '=')
	{
		if ($value === null) {
			$not = false;
			if ($comparison == '<>') {
				$not = true;
			}
			$this->_setWhereNull($column, $or, $not);
		} else {
			$operation = 'AND';
			if ($or == true) {
				$operation = 'OR';
			}
			$col = trim($column);
			$paramKey = $this->_createParamKey();
			$this->_parts['where'][] = array(
				'operation' => $operation,
				'where' => sprintf('%s %s :%s', $col, $comparison, $paramKey)
			);
			$this->_params[$paramKey] = $value;
		}
	}
	
	/**
	 * Add WHERE IN condition
	 *
	 * @param string $column
	 * @param array $values
	 * @param boolean $or
	 * @param boolean $not
	 * @return void
	 */		
	protected function _setWhereIn($column, array $values, $or = false, $not = false)
	{
		$holders = array();
		foreach ($values as $val) {
			$paramKey = $this->_createParamKey();
			$holders[] = ':' . $paramKey;
			$this->_params[$paramKey] = $val;
		}
		$operation = 'AND';
		if ($or == true) {
			$operation = 'OR';
		}
		$comparison = 'IN';
		if ($not == true) {
			$comparison = 'NOT IN';
		}
		$this->_parts['where'][] = array(
			'operation' => $operation,
			'where' => sprintf('%s %s (%s)', $column, $comparison, implode(',', $holders))
		);		
	}
	
	/**
	 * Add WHERE BETWEEN condition
	 * 
	 * @param string $column
	 * @param mixed $minValue
	 * @param mixed $maxValue
	 * @param boolean $or
	 * @param boolean $not
	 * @return void
	 */
	protected function _setWhereBetween($column, $minValue, $maxValue, $or = false, $not = false)
	{		
		$paramKey = $this->_createParamKey();
		$minHolder = ':' . $paramKey;
		$this->_params[$paramKey] = $minValue;
		
		$paramKey = $this->_createParamKey();
		$maxHolder = ':' . $paramKey;
		$this->_params[$paramKey] = $maxValue;
		
		$operation = 'AND';
		if ($or == true) {
			$operation = 'OR';
		}
		$comparison = 'BETWEEN';
		if ($not == true) {
			$comparison = 'NOT BETWEEN';
		}
		$this->_parts['where'][] = array(
			'operation' => $operation,
			'where' => sprintf('%s %s %s AND %s', $column, $comparison, $minHolder, $maxHolder)
		);		
	}
	
	/**
	 * Set WHERE condition that value is null
	 * 
	 * @param string $column
	 * @param boolean $or
	 * @param boolean $not
	 * @return void
	 */
	protected function _setWhereNull($column, $or = false, $not = false)
	{
		$operation = 'AND';
		if ($or == true) {
			$operation = 'OR';
		}
		$comparison = 'IS';
		if ($not == true) {
			$comparison .= ' NOT';
		}
		$this->_parts['where'][] = array(
			'operation' => $operation,
			'where' => sprintf('%s %s NULL', $column, $comparison)
		);
	}
	
	/**
	 * Set WHERE condition which value is expression
	 * 
	 * @param string $column
	 * @param string $expression
	 * @param boolean $or
	 * @param string $comparison
	 */
	protected function _setWhereExpr($column, $expression, $or = false, $comparison = '=')
	{
		$operation = 'AND';
		if ($or == true) {
			$operation = 'OR';
		}
		$this->_parts['where'][] = array(
			'operation' => $operation,
			'where' => sprintf('%s %s %s', $column, $comparison, $expression)
		);
	}
	
	/**
	 * Add WHERE condition
	 *
	 * @param boolean $or
	 * @param mixed $where
	 * @param mixed $param
	 * @return void
	 */		
	protected function _setWhereDirect($or, $where, $param = null)
	{
		$operation = 'AND';
		if ($or == true) {
			$operation = 'OR';
		}
		if (is_array($where)) {
			foreach ($where as $key => $val) {
				$col = trim($key);
				$paramKey = $this->_createParamKey();
				if ($val === null) {
					$this->_parts['where'][] = array(
						'operation' => $operation,
						'where' => sprintf('%s IS NULL', $col)
					);					
				} else {
					$this->_parts['where'][] = array(
						'operation' => $operation,
						'where' => sprintf('%s = :%s', $col, $paramKey)
					);
					$this->_params[$paramKey] = $val;
				}
			}
		} else {
			$this->_parts['where'][] = array(
				'operation' => $operation,
				'where' => $where
			);
			if (is_array($param)) {
				if (is_array($param)) {
					foreach ($param as $key => $val) {
						$this->_params[$key] = $val;
					}
				}
			}
		}
	}
	
	/**
	 * Create and return WHERE sentence
	 *
	 * @return string WHERE sentence
	 */	
	protected function _createWhereSentence()
	{
		$ret = '';
		if (isset($this->_parts['where']) && $this->_parts['where']) {
			$where = '';
			for ($i = 0; $i < count($this->_parts['where']); $i++) {
				if ($i > 0) {
					$where .= sprintf(' %s ', $this->_parts['where'][$i]['operation']);
				}
				$where .= sprintf('(%s)', $this->_parts['where'][$i]['where']);
			}
			$ret = ' WHERE ' . $where;
		}
		return $ret;
	}
	
	/**
	 * Get table name with schema name, if schema name is specified
	 *
	 * @return string table name with schema name
	 */	
	protected function _getTableFullName()
	{
		$tableName = $this->_tableName;
		if ($this->_schema != '') {
			$tableName = $this->_schema . '.' . $tableName;
		}
		return $tableName;
	}
	
}