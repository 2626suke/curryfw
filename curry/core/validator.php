<?php

/**
 * Validator
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
class Validator extends CurryClass
{	
	/**
	 * Valitate rule setting
	 *
	 * @var array
	 */
	protected $_rules = array();
	
	/**
	 * The error message of the item used as an error is held as a result of an inspection. 
	 *
	 * @var array
	 */
	protected $_errors;
	
	/**
	 * Default error messages.
	 *
	 * @var array
	 */
	protected static $_messages = array(
		'required'      => 'Required.',
		'length'        => 'Length is out of range.',
		'length_range'  => 'Length is out of range.',
		'numeric'       => 'Must be numeric.',
		'number_string' => 'Must be only with the numeric character.',
		'alpha'         => 'Must be only with the alphabet character.',
		'alphanum'      => 'Must be only with the alphabet or numeric character.',
		'singlebyte'    => 'Must be only with the single byte character.',
		'regex'         => 'It is an illegal character.',
	);
	
	/**
	 * Set validate rule setting
	 *
	 * @param array $rules
	 * @return void
	 */
	public function setRules($rules)
	{
		if (!is_array($rules) && !($rules instanceof ArrayObject)) {
			throw new ArgumentInvalidException(__METHOD__, $rules, 1, 'array or ArrayObject');
		}
		$this->_rules = $rules;	
	}
	
	/**
	 * Get the error message of the item used as an error by validation.
	 * 
	 * @return array
	 */
	public function getError()
	{
		return $this->_errors;
	}
	
	/**
	 * Set default error messages
	 *
	 * @param array $messages
	 * @return void
	 */
	public static function setDefaultErrorMessage($messages)
	{
		if (!is_array($messages) && !($messages instanceof ArrayObject)) {
			throw new ArgumentInvalidException(__METHOD__, $messages, 1, 'array or ArrayObject');
		}
		foreach ($messages as $key => $message) {
			self::$_messages[$key] = $message;
		}
	}
	
	/**
	 * Execute validation for all of request values
	 *
	 * @param array $data Request data
	 * @return boolean
	 */
	public function validate($data)
	{
		foreach ($this->_rules as $item => $rules) {
			foreach ($rules as $ruleInfo) {
				$rule = $ruleInfo['rule'];
				$method = NameManager::convertToCamel($rule);
				if (!method_exists($this, $method)) {
					continue;
				}
				$message = '';
				if (isset($ruleInfo['error_message'])) {
					$message = $ruleInfo['error_message'];
				} else if (isset(self::$_messages[$rule])) {
					$message = self::$_messages[$rule];
				}
				if ($message == '') {
					throw new Exception('Error message of rule "' . $rule . '" is not specified.');
				}
				$res = $this->$method($data[$item], $ruleInfo);
				if ($res == false) {
					foreach ($ruleInfo as $key => $val) {
						$message = str_replace('%' . $key . '%', $val, $message);
					}
					$this->_errors[$item] = $message;
					break;
				}				
			}
		}
		if ($this->_errors) {
			return false;	
		}
		return true;
	}
	
	/**
	 * Execute validation of value required for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function required($value, $options)
	{		
		if (trim($value) == '') {
			return false;
		}		
		return true;
	}
	
	/**
	 * Execute validation of value max length for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function length($value, $options)
	{	
		if ($value == '') {
			return true;
		}
		$len = 0;
		if (array_key_exists('encoding', $options)) {
			$len = mb_strlen($value, $options['encoding']);
		} else {
			$len = mb_strlen($value);
		}
		if (array_key_exists('max', $options) && $len > $options['max']) {
			return false;
		}
		if (array_key_exists('min', $options) && $len < $options['min']) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Execute validation of range of value length for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function lengthRange($value, $options)
	{
		return $this->length($value, $options);
	}
	
	/**
	 * Execute validation of numeric for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function numeric($value, $options)
	{
		if ($value == '') {
			return true;
		}
		if (!is_numeric($value)) {
			return false;
		}
		return true;
	}
	
	/**
	 * Execute validation of number string for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function numberString($value, $options)
	{
		if ($value == '') {
			return true;
		}
		if (!preg_match('/^[0-9]+$/', $value)) {
			return false;
		}
		return true;
	}
	
	/**
	 * Execute validation of alphabet for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function alpha($value, $options)
	{
		if ($value == '') {
			return true;
		}
		if (!preg_match('/^[a-zA-Z]+$/', $value)) {
			return false;
		}
		return true;
	}
	
	/**
	 * Execute validation of alphabet or number string for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function alphaNum($value, $options)
	{
		if ($value == '') {
			return true;
		}
		if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
			return false;
		}
		return true;
	}
	
	/**
	 * Execute validation of singlebyte for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function singlebyte($value, $options)
	{
		if ($value == '') {
			return true;
		}
		$len = strlen($value);
		if (array_key_exists('encoding', $options)) {
			$mblen = mb_strlen($value, $options['encoding']);
		} else {
			$mblen = mb_strlen($value);
		}
		if ($len != $mblen) {
			return false;
		}
		return true;
	}
	
	/**
	 * Execute validation of reguler expression for a request value
	 *
	 * @param mixed $value
	 * @param array $options
	 * @return boolean
	 */
	public function regex($value, $options)
	{
		if (!preg_match($options['pattern'], $value)) {
			return false;
		}
		return true;
	}
	
}