<?php

/**
 * @see SqlAbstract
 */
require_once 'db/sql_abstract.php';

/**
 * SqlInsert
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
class SqlInsert extends SqlAbstract
{	
	/**
	 * Add values
	 *
	 * @param mixed $fields Field name or array of combination of field names and values
	 * @param mixed $value Value if $fields is string as fields name
	 * @return SqlAbstract $this Self instance
	 */
	public function values($fields, $value = null)
	{
		$values = array();
		if (is_array($fields)) {
			if ($value != null) {
				throw new Exception('Second argument can not be passed, when first argument is array.');
			}
			foreach ($fields as $col => $val) {
				$values[$col] = $val;
			}
		} else {
			$values[$fields] = $value;
		}
		
		foreach ($values as $col => $val) {
			$this->_parts['fields'][] = $col;
			if ($val === null || (self::$_convertEmptyToNull == true && $val === '')) {
				$this->_parts['values'][] = "NULL";
			} else if ($val instanceof SqlSelect) {
				$subQuery = $val;
				$this->_parts['values'][] = '(' . $subQuery->getSql() . ')';
				$subParams = $subQuery->getParams();
				foreach ($subParams as $subCol => $subVal) {
					$this->_params[$subCol] = $subVal;
				}
			} else {
				$paramKey = $this->_createParamKey();
				$this->_parts['values'][] = ':' . $paramKey;
				$this->_params[$paramKey] = $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * Create and return SQL sentence
	 *
	 * @return string SQL sentence
	 */
	public function getSql()
	{
		$fieldsStr = implode(',', $this->_parts['fields']);
		$valsStr = implode(',', $this->_parts['values']);		
		$tableName = $this->_getTableFullName();
		$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $tableName, $fieldsStr, $valsStr);
		
		return $sql;
	}
			
	/**
	 * Execute insert sentence.
	 * It fails, when conditions are not specified. 
	 *
	 * @return boolean
	 */	
	public function execute()
	{
		$res = parent::execute();
		if ($res === false) {
			return false;
		}
		$res = $this->_adapter->lastInsertId();		
		return $res;
	}
	

}