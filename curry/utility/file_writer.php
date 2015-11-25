<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * FileWriter
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    utility
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class FileWriter extends CurryClass
{	
	/**
	 * Path of file
	 *
	 * @var string
	 */
	private $_path;
	
	/**
	 * Resource of file 
	 *
	 * @var resource
	 */
	private $_resource;
	
	/**
	 * constructor
	 *
	 * @param string $path Path of file
	 * @param boolean $append If you want to open in append mode if true, otherwise false
	 * @return void
	 */
	public function __construct($path, $append = true)
	{
		$this->_path = $path;
		$this->open($append);
	}
	
	/**
	 * Opens file
	 *
	 * @param boolean $append If you want to open in append mode if true, otherwise false
	 * @return boolean
	 */
	public function open($append = true)
	{
		$mode = 'a';
		if ($append == false) {
			$mode = 'w';
		}
		$this->_resource = @fopen($this->_path, $mode);
		if ($this->_resource === false) {
			return false;
		}
		flock($this->_resource, LOCK_EX);
		
		return true;
	}	
	
	/**
	 * Close file resouce
	 *
	 * @return boolean
	 */
	public function close()
	{
		if ($this->_resource == false) {
			return;
		}
		$res = fclose($this->_resource);
		if ($res) {
			$this->_resource = null;
		}
		return $res;
	}
	
	/**
	 * Write text to file
	 *
	 * @param string $text Text to be written
	 * @return boolean
	 */
	public function write($text)
	{
		if ($this->_resource == false) {
			throw new Exception('The file is not opened.');
		}
		$res = fputs($this->_resource, $text);
		return $res;
	}
	
	/**
	 * Write text and return code to file
	 *
	 * @param string $text Text to be written
	 * @return boolean
	 */
	public function writeLine($text)
	{
		$res = $this->write($text . "\n");
		return $res;
	}
	
	/**
	 * Write text lines to file
	 *
	 * @param array $lines Texts to be written
	 * @return boolean
	 */
	public function writeLines(array $lines)
	{
		$text = implode("\n", $lines) . "\n";
		$res = $this->write($text);
		return $res;
	}
	
	/**
	 * Write array data as csv text to file
	 * 
	 * @param array $csvLines Array data to be written
	 * @param string $delimiter
	 * @param string $quote
	 * @return boolean
	 */
	public function writeCsv(array $csvLines, $delimiter = ',', $quote = '')
	{		
		$lines = array();
		foreach ($csvLines as $line) {
			$row = $line;
	    	if (!is_array($line)) {
				$row = array($line);
			}
			foreach ($row as $key => $val) {
				$row[$key] = $quote . $val . $quote;
			}
			$lines[] = implode($delimiter, $row);
		}
		$text = implode("\n", $lines) . "\n";
		$res = $this->write($text);
		return $res;
	}

}
