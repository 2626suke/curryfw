<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * File
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
class File extends CurryClass
{
	/**
	 * Path of the file
	 *
	 * @var string
	 */
	private $_path;
	
	/**
	 * constructor
	 *
	 * @param string $path Path of the file
	 * @return void
	 */
	public function __construct($path)
	{
		if (self::exists($path) == false) {
			throw new FileNotExistException($this->_path);
		}
		$this->_path = $path;
	}
	
	/**
	 * Check whether a file exists
	 *
	 * @param string $path
	 * @return boolean
	 */
	public static function exists($path)
	{
		$ret = false;
		if (@file_exists($path) && is_file($path)) {
			$ret = true;
		}
		return $ret;
	}
	
	/**
	 * Create a file
	 *
	 * @param string $path
	 * @return File
	 * @throws Exception 
	 */
	public static function create($path)
	{
		if (self::exists($path)) {
			throw new Exception(sprintf('The file "%s" already exists.', $path));
		}
		if (@touch($path) === false) {
			throw new Exception(sprintf('To create file "%s" failed.', $path));
		}
		$file = new File($path);
		return $file;
	}
	
	/**
	 * Delete the file
	 * 
	 * @return boolean
	 * @throws Exception 
	 */
	public function delete()
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		if (@unlink($this->_path) === false) {
			throw new Exception(sprintf('To delete file "%s" failed.', $this->_path));
		}
		return true;
	}
	
	/**
	 * Copy the file
	 * 
	 * @param string $newPath
	 * @param boolean $overwrite
	 * @return boolean
	 * @throws FileNotExistException
	 * @throws Exception 
	 */
	public function copy($newPath, $overwrite = false)
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		if ($overwrite == false && self::exists($newPath)) {
			throw new Exception(sprintf('Destination file "%s" already exists.', $newPath));
		}
		return @copy($this->_path, $newPath);
	}
	
	/**
	 * Rename the file
	 *
	 * @param string $newName
	 * @return boolean
	 * @throws FileNotExistException
	 * @throws Exception 
	 */
	public function rename($newName)
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		$newPath = $this->getParentPath() . "/" . $newName;
		if (false == @rename($this->_path, $newPath)) {
			throw new Exception(sprintf('To rename file "%s" failed.', $this->_path));
		}
		return true;
	}   
	
	/**
	 * Move file to other directory
	 * 
	 * @param type $newDir Destination directory path
	 * @param type $newName Filename if you want to file a different name in the destination
	 * @return boolean
	 * @throws FileNotExistException
	 * @throws Exception 
	 */
	public function move($newDir, $newName = null)
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		$fileName = $this->getName($this->_path);
		if ($newName != null) {
			$fileName = $newName;
		}		
		$newPath = rtrim($newDir, "/") . "/" . $fileName;
		if (false == @rename($this->_path, $newPath)) {
			throw new Exception(sprintf('To move file "%s" to "%s" failed.', $this->_path, $newDir));
		}
		return true;
	}
	
	/**
	 * Get file path
	 * 
	 * @return string 
	 */
	public function getPath()
	{
	   	return $this->_path;
	}
	
	/**
	 * Get path of the parent directory
	 * 
	 * @return string 
	 */
	public function getParentPath()
	{
		return dirname($this->_path);
	}
	
	/**
	 * Get name of the file
	 * 
	 * @return string 
	 */
	public function getName()
	{
	   	return basename($this->_path);
	}
		
	/**
	 * Get extension of the file
	 * 
	 * @return string 
	 */
	public function getExtension()
	{
		$pathInfo = $this->getPathInfo();
		return $pathInfo['extension'];
	}
	
	/**
	 * Get path infomation
	 * 
	 * @return array 
	 */
	public function getPathInfo()
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		return pathinfo($this->_path);
	}
	
	/**
	 * Get size of the file
	 * 
	 * @return int File size
	 * @throws Exception 
	 */
	public function getSize()
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		return filesize($this->_path);
	}
	
	/**
	 * Change the permissions of the file
	 * 
	 * @param string $mode
	 * @return boolean
	 * @throws FileNotExistException
	 * @throws Exception
	 */
	public function chmod($mode)
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		if (@chmod($this->_path, $mode) == false) {
			throw new Exception(sprintf('To change permissions to %s of the file "%s" failed.', $mode, $this->_path));
		}
		return true;
	}
	
	/**
	 * Check the file is readable
	 * 
	 * @return boolean
	 * @throws FileNotExistException
	 */
	public function isReadable()
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		$res = is_readable($this->_path);
		return $res;
	}
	
	/**
	 * Check the file is writable
	 * 
	 * @return boolean
	 * @throws FileNotExistException
	 */
	public function isWritable()
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		$res = is_writable($this->_path);
		return $res;
	}
	
	/**
	 * Get the instance of class "FileReader" of the file
	 * 
	 * @return FileReader 
	 * @throws FileNotExistException
	 */
	public function getReader()
	{
		if (!Loader::classExists('FileReader')) {
			Loader::load('FileReader', 'utility');
		}
		$reader = new FileReader($this->_path);
		return $reader;
	}

	/**
	 * Get the instance of class "FileWriter" of the file
	 * 
	 * @param boolean $append
	 * @return FileWriter
	 */
	public function getWriter($append = false)
	{
		if (!Loader::classExists('FileWriter')) {
			Loader::load('FileWriter', 'utility');
		}
		$writer = new FileWriter($this->_path);
		return $writer;
	}
	
}
