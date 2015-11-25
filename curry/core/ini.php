<?php

/**
 * Ini
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
class Ini
{	
	/**
	 * Whether to return as object or not
	 * 
	 * @var boolean
	 */
	protected static $_isReturnObject = true;
	
	/**
	 * Set whether to return as object or not
	 *
	 * @param boolean isReturnObject
	 * @return void
	 */
	public static function isReturnObject($bool)
	{
		self::$_isReturnObject = $bool;
	}
	
	/**
	 * Read ini file and return values as array
	 *
	 * @param string $fileName The name of the ini file to read
	 * @param string $section If the section name you want to get only certain sections
	 * @return ArrayObject|array
	 */
	public static function load($fileName, $section = null)
	{
		$ret = false;
		$path = PathManager::getConfigDirectory() . DIRECTORY_SEPARATOR . $fileName;
		if (file_exists($path)) {
			$iniArray = parse_ini_file($path, true);
			if ($section != null) {
				if (isset($iniArray[$section])) {
					$iniArray = $iniArray[$section];
					$ret = self::_levelize($iniArray);
				}
			} else {
				$ret = array();
				foreach ($iniArray as $sec => $values) {
					$ret[$sec] = self::_levelize($values);					
				}
			}
			if (is_array($ret) && self::$_isReturnObject == true) {
				$ret = self::_toArrayObject($ret);
			}
		}
		return $ret;
	}
	
	/**
	 * Set path to the directory that contains ini files
	 *
	 * @param string $dir Path to the directory that contains ini files
	 * @return void
	 */
	public static function setDir($dir)
	{
		PathManager::setConfigDirectory($dir);		
	}
	
	/**
	 * Levelize an array recursively
	 *
	 * @param array $iniArray
	 * @return array
	 */
	private static function _levelize($iniArray)
	{
		$ret = array();
		$prev = array();
		foreach ($iniArray as $key => $value) {
			if (strpos($key, '.') !== false) {
				$keyParts = explode('.', $key);
				$levelized = array();
				for ($i = count($keyParts) - 1; $i >= 0; $i--) {
					$keyPart = $keyParts[$i];
					if ($i == count($keyParts) - 1) {
						$levelized[$keyPart] = $value;
					} else {
						$tmp = array();
						$tmp[$keyPart] = $levelized;
						$levelized = $tmp;
					}
				}
				$ret = array_merge_recursive($prev, $levelized);
			} else {
				$tmp = array();
				$tmp[$key] = $value;
				$ret = array_merge_recursive($prev, $tmp);
			}
			$prev = $ret;
		}
		
		return $ret;
	}
	
	/**
	 * Convert array to ArrayObject
	 * 
	 * @param array $array
	 * @return ArrayObject
	 */
	private static function _toArrayObject($array)
	{
		$ret = $array;
		foreach ($ret as $key => $val) {
			if (is_array($val)) {
				$ret[$key] = self::_toArrayObject($val);
			}
		}
		$ret = new ArrayObject($ret, ArrayObject::ARRAY_AS_PROPS);
		return $ret;
	}
}