<?php

/**
 * @see SqlAbstract
 */
require_once 'db/sql_abstract.php';

/**
 * SqlUpdate
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
class SqlUpdate extends SqlAbstract
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
			if (func_num_args() > 1) {
				throw new Exception('Second argument can not be passed, when first argument is array.');
			}
			foreach ($fields as $col => $val) {
				$values[$col] = $val;
			}
		} else {
			if (func_num_args() == 1) {
				throw new Exception('Second must not be passed, when first argument is string.');
			}
			$values[$fields] = $value;
		}
		
		foreach ($values as $col => $val) {
			if ($val === null || (self::$_convertEmptyToNull == true && $val === '')) {
				$this->_parts['set'][] = sprintf('%s = NULL', $col);
			} else if ($val instanceof SqlSelect) {
				$subQuery = $val;
				$subQuery->forUpdateSub();
				$this->_parts['set'][] = sprintf('%s = (%s)', $col, $subQuery->getSql());
				$subParams = $subQuery->getParams();
				foreach ($subParams as $subCol => $subVal) {
					$this->_params[$subCol] = $subVal;
				}
			} else {
				$paramKey = $this->_createParamKey();
				$this->_parts['set'][] = sprintf('%s = :%s', $col, $paramKey);
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
		$set = implode(',', $this->_parts['set']);		
		$tableName = $this->_getTableFullName();
		if ($this->_alias) {
			$tableName .= ' AS ' . $this->_alias;
		}
		$sql  = sprintf("UPDATE %s SET %s", $tableName, $set);
		$sql .= $this->_createWhereSentence();
		
		return $sql;
	}

	/**
	 * Execute increment integer filed value by executing update sentence
	 *
	 * @return boolean
	 */	
	public function increment($fields)
	{
		if (!is_array($fields)) {
			$fields = array($fields);
		}
		$this->_parts['values'] = array();
		$conf = Db::getConfig();
		$sets = array();
		foreach ($fields as $fid) {
			if ($conf['type'] == 'mysql') {
				$sets[] = sprintf('%s = IFNULL(%s, 0) + 1', $fid, $fid); 
			} else if ($conf['type'] == 'pgsql') {
				$sets[] = sprintf('%s = COALESCE(%s, 0) + 1', $fid, $fid); 
			}
		}
		$set = implode(',', $sets);	
		$sql  = sprintf("UPDATE %s SET %s", $this->_tableName, $set);
		$sql .= $this->_createWhereSentence();
		$stmt = $this->_adapter->prepare($sql);
		if (is_array($this->_params) && count($this->_params) > 0) {
			foreach ($this->_params as $key => $val) {
				$stmt->bindValue(':' . $key, $val);
			}
		}
		$res = $stmt->execute();
		Db::addSqlHistory($sql, $this->_params);
		
		return $res;
	}
}