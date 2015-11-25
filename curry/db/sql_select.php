<?php

/**
 * @see SqlAbstract
 */
require_once 'db/sql_abstract.php';

/**
 * SqlSelect
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
class SqlSelect extends SqlAbstract
{
	/**
	 * Whether this instance is for subquery or not
	 *
	 * @var boolean
	 */
	protected $_forUpdateSub = false;
	
	/**
	 * Field name to the key of the query result array
	 *
	 * @var string
	 */
	protected $_resultKeyField;
	
	/**
	 * Set field name to the key of the query result array
	 *
	 * @var string
	 */	
	public function setResultKeyField($fieldName)
	{
		$this->_resultKeyField = $fieldName;
	}
	
	/**
	 * Add SELECT fields
	 *
	 * @param mixed $fields Field name(s)
	 * @return SqlSelect
	 */
	public function fields($fields)
	{
		if (is_array($fields)) {
			foreach ($fields as $field) {
				$this->_parts['fields'][] = $field;				
			}
		} else {
			$this->_parts['fields'][] = $fields;
		}
		return $this;
	}
	
	/**
	 * Specify the table that expected table name or SqlSelect instance as sub query
	 * 
	 * @param string $from
	 * @return SqlSelect
	 */
	public function from($from)
	{
		$this->_parts['from'] = $from;
		return $this;
	}
	
	/**
	 * Add ORDER BY fields
	 * 
	 * @param string $orderBy
	 * @return SqlSelect
	 */
	public function order($orderBy)
	{
		if (is_array($orderBy)) {
			foreach ($orderBy as $field) {
				$this->_parts['order'][] = $field;				
			}
		} else {
			$this->_parts['order'][] = $orderBy;
		}
		return $this;
	}
	
	/**
	 * Add GROUP BY fields
	 * 
	 * @param string $groupBy
	 * @return SqlSelect
	 */
	public function group($groupBy)
	{
		if (is_array($groupBy)) {
			foreach ($groupBy as $field) {
				$this->_parts['group'][] = $field;				
			}
		} else {
			$this->_parts['group'][] = $groupBy;
		}
		return $this;
	}
	
	/**
	 * Set max row count of query result
	 *
	 * @param int $limit Max row count of query result
	 * @return SqlSelect
	 */
	public function limit($limit)
	{
		if (!is_numeric($limit)) {
			throw new Exception('limit is must be numeric. but passed is not.');
		}
		$this->_parts['limit'] = $limit;
		return $this;
	}
	
	/**
	 * Set whether use distinct sentence or not
	 *
	 * @param bool $isDistinct
	 * @return SqlSelect
	 */
	public function distinct($isDistinct = true)
	{
		$this->_parts['distinct'] = $isDistinct;
		return $this;
	}
	
	/**
	 * 
	 * @param int $offset
	 * @return SqlSelect
	 * @throws Exception
	 */
	public function offset($offset)
	{
		if (!is_numeric($offset)) {
			throw new Exception('offset is must be numeric. but passed is not.');
		}
		$this->_parts['offset'] = $offset;
		return $this;
	}
	
	/**
	 * Alias of "joinInner" 
	 *
	 * @param string $table Table name or instastance of class that extends "Model"
	 * @param string|array $on Join condition(s)
	 * @return SqlSelect
	 */
	public function join($table, $on)
	{
		return $this->joinInner($table, $on);
	}
	
	/**
	 * Add INNER JOIN table and on condition(s)
	 *
	 * @param string $table Table name or instastance of class that extends "Model"
	 * @param string|array $on Join condition(s)
	 * @return SqlSelect
	 */
	public function joinInner($table, $on)
	{
		$this->_join($table, $on, 'INNER');
		return $this;
	}
	
	/**
	 * Add LEFT JOIN table and on condition(s)
	 *
	 * @param string $table Table name or instastance of class that extends "Model"
	 * @param string|array $on Join condition(s)
	 * @return SqlSelect
	 */
	public function joinLeft($table, $on)
	{
		$this->_join($table, $on, 'LEFT');
		return $this;
	}
	
	/**
	 * Add RIGHT JOIN table and on condition(s)
	 *
	 * @param string $table Table name or instastance of class that extends "Model"
	 * @param string|array $on Join condition(s)
	 * @return SqlSelect
	 */
	public function joinRight($table, $on)
	{
		$this->_join($table, $on, 'RIGHT');
		return $this;
	}
		
	/**
	 * Add CROSS JOIN table
	 *
	 * @param string $table Table name or instastance of class that extends "Model"
	 * @param string|array $on Join condition(s)
	 * @return SqlSelect
	 */
	public function joinCross($table, $on = null)
	{
		$this->_join($table, $on, 'CROSS');
		return $this;
	}
	
	/**
	 * Set for update
	 *
	 * @return SqlSelect
	 */
	public function forUpdateSub()
	{
		$this->_forUpdateSub = true;
		return $this;
	}	
		
	/**
	 * Add join table and on condition(s)
	 *
	 * @param string $table Table name or instastance of class that extends "Model"
	 * @param string|array $on Join condition(s)
	 * @param string $joinType Join type, "LEFT" or "RIGHT" or "INNER"
	 * @return SqlSelect
	 */
	protected function _join($table, $on, $joinType)
	{
		if ($table instanceof Model) {
			$tableName = $table->getName();
			if ($table->getAlias() != '') {
				$tableName .= ' AS ' . $table->getAlias();
			}
			$table = $tableName;
		}
		if ($table instanceof SqlSelect) {
			if ($table->getAlias() == null) {
				throw new Exception('You have to specify an alias for a subquery');
			}
		}
		$tmp['table'] = $table;
		
		if (is_array($on)) {
			$on = implode(' AND ', $on);
		}
		$tmp['on'] = $on;
		
		$tmp['type'] = $joinType;
		
		$this->_parts['join'][] = $tmp;
	}

	
	/**
	 * Add HAVING conditions
	 *
	 * @param string $having condition sentence
	 * @return SqlSelect
	 */
	public function having($having)
	{
		$this->_setHaving(' AND ', $having);
		return $this;
	}

	/**
	 * Add HAVING condition combined as OR
	 *
	 * @param string $having condition sentence
	 * @return SqlAbstract $this Self instance
	 */
	public function orHaving($having)
	{
		$this->_setHaving(' OR ', $having);
		return $this;
	}
	
	/**
	 * Add UNION.
	 * Parameter expects select sentense or SqlSelect instance.
	 * 
	 * @param string|SqlSelect $select
	 * @return SqlSelect
	 */
	public function union($select)
	{
		$union['select'] = $select;
		$union['all'] = false;		
		$this->_parts['union'][] = $union;
		return $this;
	}
	
	/**
	 * Add UNION ALL.
	 * Parameter expects select sentense or SqlSelect instance.
	 * 
	 * @param string|SqlSelect $select
	 * @return SqlSelect
	 */
	public function unionAll($select)
	{
		$union['select'] = $select;
		$union['all'] = true;		
		$this->_parts['union'][] = $union;
		return $this;
	}
	
	/**
	 * Add HAVING condition
	 *
	 * @param string $operation "AND" or "OR" as Combine type
	 * @param string $having
	 * @return void
	 */
	protected function _setHaving($operation, $having)
	{
		$this->_parts['having'][] = array(
			'operation' => $operation,
			'having' => $having
		);
	}
	
	/**
	 * Create and return HAVING sentence
	 *
	 * @return string WHERE sentence
	 */	
	protected function _createHavingSentence()
	{
		$ret = '';
		if (isset($this->_parts['having']) && $this->_parts['having']) {
			$having = '';
			for ($i = 0; $i < count($this->_parts['having']); $i++) {
				if ($i > 0) {
					$having .= sprintf(' %s ', $this->_parts['having'][$i]['operation']);
				}
				$having .= sprintf('(%s)', $this->_parts['having'][$i]['having']);
			}
			$ret = ' HAVING ' . $having;
		}
		return $ret;
	}
	
	/**
	 * Create and return SQL sentence
	 *
	 * @return string SQL sentence
	 */
	public function getSql()
	{
		$fields = '*';
		if (isset($this->_parts['fields']) && is_array($this->_parts['fields'])) {
			$fields = implode(',', $this->_parts['fields']);
		}
		$tableName = $this->_getTableFullName();
		$from = $tableName;
		if (isset($this->_parts['from']) && $this->_parts['from']) {
			if ($this->_parts['from'] instanceof SqlSelect) {
				$subQuery = $this->_parts['from']->getSql();
				$subAlias = $this->_parts['from']->getAlias();
				$subParams = $this->_parts['from']->getParams();
				foreach ($subParams as $subCol => $subVal) {
					$this->_params[$subCol] = $subVal;
				}
				$from = sprintf('(%s) AS %s', $subQuery, $subAlias);
			} else {
				$from = $this->_parts['from'];
			}
		} else {
			if ($this->_forUpdateSub) {
				$from = sprintf('(SELECT * FROM %s)', $tableName);
			}
			if ($this->_alias != null) {
				$from .= ' AS ' . $this->_alias;
			}
		}
		$distinct = '';
		if (isset($this->_parts['distinct']) && $this->_parts['distinct'] === true) {
			$distinct = ' DISTINCT';
		}
		$sql = sprintf('SELECT%s %s FROM %s', $distinct, $fields, $from);
		if (isset($this->_parts['join']) && $this->_parts['join']) {
			foreach ($this->_parts['join'] as $i => $join) {
				if ($join['table'] instanceof SqlSelect) {
					$subQuery = $join['table']->getSql();
					$subAlias = $join['table']->getAlias();
					$subParams = $join['table']->getParams();
					foreach ($subParams as $subCol => $subVal) {
						$this->_params[$subCol] = $subVal;
					}
					$sql .= sprintf(' %s JOIN (%s) AS %s', $join['type'], $subQuery, $subAlias);
				} else {
					$sql .= sprintf(' %s JOIN %s', $join['type'], $join['table']);
				}
				if ($join['on'] != null) {
					$sql .= ' ON ' . $join['on'];
				}
			}
		}
		
		$sql .= $this->_createWhereSentence();
		
		if (isset($this->_parts['group']) && $this->_parts['group']) {
			$sql .= ' GROUP BY ' . implode(',', $this->_parts['group']);
		}
		
		$sql .= $this->_createHavingSentence();
		
		if (isset($this->_parts['order']) && $this->_parts['order']) {
			$sql .= ' ORDER BY ' . implode(',', $this->_parts['order']);
		}
		if (isset($this->_parts['limit']) && is_numeric($this->_parts['limit'])) {
			$sql .= ' LIMIT ' . $this->_parts['limit'];
			if (isset($this->_parts['offset']) && is_numeric($this->_parts['offset'])) {
				$sql .= ' OFFSET ' . $this->_parts['offset'];
			}
		}
		
		if (isset($this->_parts['union']) && is_array($this->_parts['union'])) {
			foreach ($this->_parts['union'] as $union) {
				$unionSql = '';
				if ($union['select'] instanceof SqlSelect) {
					$unionSql = $union['select']->getSql();
					$unionParams = $union['select']->getParams();
					foreach ($unionParams as $key => $val) {
						$this->_params[$key] = $val;
					}
				} else {
					$unionSql = $union['select'];
				}
				$all = '';
				if ($union['all'] == true) {
					$all = 'ALL';
				}
				$sql .= sprintf(' UNION %s %s', $all, $unionSql);
			}
		}
				
		return $sql;
	}
	
	/**
	 * Execute query and return all result rows
	 *
	 * @return array Rows as query result
	 */	
	public function fetchAll()
	{
		parent::execute();
		$rows = $this->_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($rows === false) {
			return false;
		}
		
		$ret = array();
		if ($this->_resultKeyField == null) {
			$ret = $rows;
		} else {
			foreach ($rows as $row) {
				$key = $row[$this->_resultKeyField];
				$ret[$key] = $row;
			}
		}
		return $ret;
	}
	
	/**
	 * Execute query and return result first one row
	 *
	 * @return array A Row as query result
	 */	
	public function fetchRow()
	{
		$this->_parts['limit'] = 1;
		$rows = $this->fetchAll();
		if ($rows === false || count($rows) == 0) {
			return false;
		}
		$row = $rows[0];
		
		return $row;
	}
	
	/**
	 * Execute query and return scalar value of specified column in result first one row
	 *
	 * @param string $fieldName 
	 * @return string Scalar value 
	 */	
	public function fetchScalar($fieldName)
	{
		$row = $this->fetchRow();
		if ($row === false || array_key_exists($fieldName, $row) === false) {
			return false;
		}
		$val = $row[$fieldName];
		
		return $val;
	}
	
	/**
	 * Execute query and return max value of specified column
	 *
	 * @param string $fieldName 
	 * @return string Max value of specified column
	 */	
	public function fetchMax($fieldName)
	{
		$this->_parts['fields'] = array('MAX(' . $fieldName .') AS ' . $fieldName);
		$val = $this->fetchScalar($fieldName);
		return $val;
	}
	
	/**
	 * Execute query and return min value of specified column
	 *
	 * @param string $fieldName 
	 * @return string Min value of specified column
	 */	
	public function fetchMin($fieldName)
	{
		$this->_parts['fields'] = array('MIN(' . $fieldName .') AS ' . $fieldName);
		$val = $this->fetchScalar($fieldName);
		return $val;
	}
	
	/**
	 * Execute query and return row count
	 *
	 * @return string Row count
	 */	
	public function fetchCount()
	{
		$this->_parts['fields'] = array('COUNT(*) AS cnt');
		$val = $this->fetchScalar('cnt');
		return $val;
	}
	
	/**
	 * Execute query and return values sum of specified column
	 
	 * @param string $fieldName 
	 * @return string Sum of values
	 */	
	public function fetchSum($fieldName)
	{
		$this->_parts['fields'] = array('SUM(' . $fieldName .') AS sum');
		$val = $this->fetchScalar('sum');
		return $val;
	}
	
}