<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * FileReader
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
class FileReader extends CurryClass
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
	 * Whether to rewind file pointer or not when read file contents
	 * 
	 * @var boolean
	 */
	private $_autoRewind = false;
	
	/**
	 * constructor
	 *
	 * @param string $path Path of file
	 * @param boolean $autoRewind Whether to rewind file pointer or not when read file contents
	 * @return void
	 */
	public function __construct($path, $autoRewind = false)
	{
		$this->_path = $path;
		$this->_autoRewind = $autoRewind;
		$this->open();
	}
	
	/**
	 * Opens file
	 *
	 * @return boolean
	 */
	public function open()
	{
		if (@file_exists($this->_path) === false) {
			return false;
		}
		$this->_resource = @fopen($this->_path, "r");
		if ($this->_resource === false) {
			return false;
		}		
		return true;
	}
	
	/**
	 * Close file resouce
	 *
	 * @return boolean
	 */
	public function close()
	{
		if ($this->_resource == null) {
			return;
		}
		$res = fclose($this->_resource);
		if ($res) {
			$this->_resource = null;
		}
		return $res;
	}
	
	/**
	 * The position of the file pointer back to the top
	 * 
	 * @return boolean
	 */
	public function rewind()
	{
		$res = rewind($this->_resource);
		return $res;
	}
	
	/**
	 * Get character(s) from file and move pointer.
	 * 
	 * @param int $count
	 * @return string
	 * @throws Exception 
	 */
	public function char($count = 1)
	{
		if (null == $this->_resource) {
			throw new Exception('The file is not opened.');
		}
		$ret = "";
		for ($i = 0; $i < $count; $i++) {
			$s = fgetc($this->_resource);
		   if ($s === false) {
			   return false;
		   }
		   $ret .= $s;
		}
		if ($this->_autoRewind == true) {
			$this->rewind();
		}
		return $ret;
	}
	
	/**
	 * Get line text from file and move pointer.
	 * 
	 * @return string
	 * @throws Exception 
	 */
	public function line()
	{
		if (null == $this->_resource) {
			throw new Exception('The file is not opened.');
		}
		$line = fgets($this->_resource);
		$line = trim($line);
		if ($this->_autoRewind == true) {
			$this->rewind();
		}
		return $line;
	}
	
	/**
	 * Get all text from file.
	 * 
	 * @return string
	 * @throws Exception 
	 */
	public function contents()
	{
		if (null == $this->_resource) {
			throw new Exception('The file is not opened.');
		}
		$contents = stream_get_contents($this->_resource);
		if ($this->_autoRewind == true) {
			$this->rewind();
		}
		return $contents;
	}
	
	/**
	 * Get all text from file and get as lines array.
	 * 
	 * @return array
	 * @throws Exception 
	 */
	public function lines()
	{
		$s = $this->contents();
		$s = str_replace("\r", '', $s);
		$ar = explode("\n", $s);
		return $ar;
	}
	
	/**
	 * Get all text from file and get as csv array.
	 *
	 * @param string $delimiter
	 * @param string $quote
	 * @return array 
	 */
	public function csv($delimiter = ',', $quote = '')
	{
		$lines = $this->lines();
		$csvRows = array();
		foreach ($lines as $line) {
			if ('' == trim($line)) {
				continue;
			}
			$ar = explode($delimiter, $line);
			foreach ($ar as $key => $val) {
				$ar[$key] = trim($val, $quote);
			}
			$csvRows[] = $ar;
		}
		return $csvRows;
	}

}
