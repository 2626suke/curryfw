<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * Dir
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
class Dir extends CurryClass
{
	/**
	 * Path of the directory
	 *
	 * @var string
	 */
	private $_path;
	
	/**
	 * constructor
	 *
	 * @param string $path Path of the directory
	 * @return void
	 * @throws FileNotExistException
	 */
	public function __construct($path)
	{
		if (self::exists($path) == false) {
			throw new FileNotExistException($path);
		}
		$this->_path = $path;
	}
	
	/**
	 * Check whether a directory exists
	 *
	 * @param string $path
	 * @return boolean
	 */
	public static function exists($path)
	{
		if (@file_exists($path) == false) {
			return false;
		}
		return true;
	}
	
	/**
	 * Create a directory
	 *
	 * @param string $path
	 * @return Dir
	 * @throws Exception 
	 */
	public static function create($path)
	{
		if (self::exists($path)) {
			throw new Exception(sprintf('The file "%s" already exists.', $path));
		}
		if (@mkdir($path) === false) {
			throw new Exception(sprintf('To create directory "%s" failed.', $path));
		}
		$dir = new Dir($path);
		return $dir;
	}
	
	/**
	 * Get the path of current directory
	 * 
	 * @return string 
	 */
	public static function current()
	{
		return getcwd();
	}
	
	/**
	 * Change current directory
	 * 
	 * @param string $path
	 * @return boolean
	 * @throws FileNotExistException
	 * @throws Exception 
	 */
	public static function changeCurrent($path)
	{
		if (self::exists($path) === false) {
			throw new FileNotExistException($path);
		}
		if (is_dir($path) == false) {
			throw new Exception(sprintf('"%s" is not a directory.', $path));
		}
		if (@chdir($path) == false) {
			throw new Exception(sprintf('To change current directory to "%s" failed.', $path));
		}		
		return true;
	}

	/**
	 * Delete the directory and all contents
	 * 
	 * @param boolean $contentsToo
	 * @return boolean
	 * @throws FileNotExistException 
	 */
	public function delete($contentsToo = true)
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		if ($contentsToo == true) {
			$res = $this->_delete($this->_path);
		} else {
			$res = @unlink($this->_path);
		}
		if ($res == false) {
			return false;
		}
		return true;
	}
	
	/**
	 * Delete the directory and all contents recursively
	 * 
	 * @param string $path
	 * @return boolean 
	 */
	protected function _delete($path)
	{
		$dir = @dir($path);
		if ($dir) {
			$contents = array();
			while ($fileName = $dir->read()) {
				$contents[] = $fileName;
			}
			$dir->close();
			foreach ($contents as $fileName) {
				$contentsPath = sprintf('%s/%s', $path, $fileName);
				if (is_dir($contentsPath)) {
					if ($fileName == '.' || $fileName == '..') {
						continue;
					}
					$res = $this->_delete($contentsPath);
					if ($res == false) {
						return false;
					}
				} else {
					$res = @unlink($contentsPath);
				}
			}
		}
		$res = @rmdir($path);
		return $res;
	}
	
	/**
	 *　Copy directory and all contents
	 * 
	 * @param string $parentDir
	 * @param boolean $overwrite
	 * @return boolean
	 * @throws FileNotExistException
	 * @throws Exception 
	 */
	public function copy($parentDir, $overwrite = false)
	{
		if (self::exists($this->_path) == false) {
			throw new FileNotExistException($this->_path);
		}
		$newDir = rtrim($parentDir, '/') . '/' . $this->getName();
		if ($overwrite == false && self::exists($newDir)) {
			throw new Exception(sprintf('Destination directory "%s" already exists.', $newDir));
		}
		$res = $this->_copy($this->_path, $newDir);
		if ($res == false) {
			return false;
		}
		return true;		
	}
	
	/**
	 *　Copy directory and all contents recursively
	 * 
	 * @param string $sourcePath
	 * @param string $newPath
	 * @return boolean 
	 */
	protected function _copy($sourcePath, $newPath)
	{		
		if (self::exists($newPath) == false) {
			$oldMask = @umask(0);
			$res = self::create($newPath);
			if ($res == false) {
				return false;
			}
			@umask($oldMask);
		}		
		$dir = dir($sourcePath);
		if ($dir) {
			while ($contentName = $dir->read()) {
				if ($contentName == '.' || $contentName == '..') {
					continue;
				}
				$contentPath = sprintf('%s/%s', $sourcePath, $contentName);
				if (is_dir($contentPath)) {
					$res = $this->_copy($contentPath, rtrim($newPath, '/') . '/' . $contentName);
				} else {
					$res = @copy($contentPath, rtrim($newPath, '/') . '/' . $contentName);
				}
				if ($res == false) {
					return false;
				}
			}
			$dir->close();
		}
		return true;
	}
	
	/**
	 * Rename the directory
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
			throw new Exception(sprintf('To rename directory "%s" failed.', $this->_path));
		}
		return true;
	}   
	
	/**
	 * Move directory the whole containts
	 * 
	 * @param string $newParent
	 * @param string $newName
	 * @return boolean
	 * @throws FileNotExistException
	 * @throws Exception
	 */
	public function move($newParent, $newName = null)
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		$dirName = $this->getName($this->_path);
		if ($newName != null) {
			$dirName = $newName;
		}		
		$newPath = rtrim($newParent, "/") . "/" . $dirName;
		if (false == @rename($this->_path, $newPath)) {
			throw new Exception(sprintf('To move directory "%s" to "%s" failed.', $this->_path, $newDir));
		}
		return true;
	}
		
	/**
	 * Get directory path
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
	 * Get name of the directory
	 * 
	 * @return string 
	 */
	public function getName()
	{
		return basename($this->_path);
	}
   
	/**
	 * Get list of file and directory in the directory
	 * 
	 * @return array Contents list
	 * @throws FileNotExistException 
	 */
	public function getContents()
	{
		if (self::exists($this->_path) === false) {
			throw new FileNotExistException($this->_path);
		}
		$dir = dir($this->_path);
		$list = array();
		while (false !== ($content = $dir->read())) {
			if ('.' == $content || '..' == $content) {
				continue;
			}
			$list[] = $content;
		}
		$dir->close();
		return $list;
	}
	
	/**
	 * Get list of file in the directory
	 * 
	 * @return array File list
	 */
	public function getFiles()
	{
		$files = array();
		$contents = $this->getContents();
		foreach ($contents as $content) {
			$path = sprintf("%s/%s", $this->_path, $content);
			if (true == is_file($path)) {
				$files[] = $content;
			}
		}
		return $files;
	}
	
	/**
	 * Get list of directory in the directory
	 * 
	 * @return array Directory list
	 */
	public function getDirs()
	{
		$dirs = array();
		$contents = $this->getContents();
		foreach ($contents as $content) {
			$path = sprintf("%s/%s", $this->_path, $content);
			if (true == is_dir($path)) {
				$dirs[] = $content;
			}
		}
		return $dirs;
	}

	/**
	 * Change the permissions of the directory
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
			throw new Exception(sprintf('To change permissions to %s of the directory "%s" failed.', $mode, $this->_path));
		}
		return true;
	}
	
	/**
	 * Check the directory is readable
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
	 * Check the directory is writable
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
	
}
