<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * LogLevel
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
class LogLevel extends CurryClass
{
	const DEBUG  = 1;
	const INFO   = 2;
	const WARN   = 3;
	const ERROR  = 4;
	const EXCEPT = 5;
	const NO_OUTPUT = 99;
}

/**
 * Logger
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
class Logger extends CurryClass
{
	/**
	 * File name of log file(s) which output log
	 *
	 * @var array
	 */	
	private static $_fileNames = array();
	
	/**
	 * Format of log line
	 *
	 * @var string
	 */	
	private static $_logFormat = '[%time%] [%level%] %message%';
	
	/**
	 * Format of time in log line
	 *
	 * @var string
	 */	
	private static $_timeFormat = 'Y-m-d H:i:s';
	
	/**
	 * Max generation of log files
	 *
	 * @var int
	 */	
	private static $_generation = 2;
	
	/**
	 * Max line count of log file
	 *
	 * @var int
	 */	
	private static $_maxLine = 100000;
	
	/**
	 * Min level of log to output
	 *
	 * @var int
	 */	
	private static $_outputLevel = 1;
	
	/**
	 * Texts of log lovel
	 *
	 * @var array
	 */	
	private static $_levelTexts = array(
		1 => 'DEBUG',
		2 => 'INFO ',
		3 => 'WARN ',
		4 => 'ERROR',
		5 => 'ERROR'
	);
	
	/**
	 * Set file name to output log
	 *
	 * @param string $fileName File name
	 * @param string $logKey Log key for file name
	 * @return void
	 */
	public static function setLogName($fileName, $logKey = 'default')
	{
		self::$_fileNames[$logKey] = $fileName;
	}
	
	/**
	 * Get file name to output log
	 *
	 * @param string $logKey Log key for file name
	 * @return string
	 */
	public static function getLogName($logKey = 'default')
	{
		return self::$_fileNames[$logKey];
	}
	
	/**
	 * Set format of log line
	 *
	 * @param string $format Format string of log line
	 * @return void
	 */
	public static function setLogFormat($format)
	{
		self::$_logFormat = $format;
	}
	
	/**
	 * Set format of time in log line
	 *
	 * @param string $format Format string of time in log line
	 * @return void
	 */
	public static function setTimeFormat($format)
	{
		self::$_timeFormat = $format;
	}
	
	/**
	 * Set max line count of log file
	 *
	 * @param int $lineCount
	 * @return void
	 */
	public static function setMaxLine($lineCount)
	{
		self::$_maxLine = $lineCount;
	}
	
	/**
	 * Set generation of log files
	 *
	 * @param int $generation
	 * @return void
	 */
	public static function setGeneration($generation)
	{
		self::$_generation = $generation;
	}

	/**
	 * Set directory path that outputs log file
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setDir($dir)
	{
		PathManager::setLogDirectory($dir);
	}
	
	/**
	 * Set min level of log to output
	 *
	 * @var int $level
	 * @return void
	 */	
	public static function setOutputLevel($level)
	{
		self::$_outputLevel = $level;
	}

	/**
	 * Set text of log lovel
	 * 
	 * @param int $logLevel
	 * @param string $text
	 * @return void
	 */
	public static function setLevelText($logLevel, $text)
	{
		self::$_levelTexts[$logLevel] = $text;
	}
	
	/**
	 * Output log
	 *
	 * @param string $message Log message
	 * @param string $logLevel Log level string
	 * @param string $logKey Log key for file name
	 * @return void
	 */
	public static function write($message, $logLevel = LogLevel::INFO, $logKey = 'default')
	{
		if (!array_key_exists($logKey, self::$_fileNames)) {
			return;
		}
		if (self::$_outputLevel > $logLevel) {
			return;
		}
				
		$dirPath = PathManager::getLogDirectory();
		$fileName = self::$_fileNames[$logKey];
		$filePath = $dirPath . '/' . $fileName;
        if (file_exists($filePath) && is_file($filePath) && !is_writable($filePath)) {
            return;
        }
        
        $level = 'UNKNOWN';
        if (array_key_exists($logLevel, self::$_levelTexts)) {
        	$level = self::$_levelTexts[$logLevel];
        }
		$logText = self::$_logFormat;
		$logText = str_replace('%time%', date(self::$_timeFormat), $logText);
		$logText = str_replace('%level%', $level, $logText);
		$logText = str_replace('%message%', $message, $logText);
		$logText .= "\n";
		
		$lineCount = 0;
		if (file_exists($filePath)) {
			$lineCount = count(file($filePath));
		}
		// exec rotate if line count of log file is over generation setting.
		if ($lineCount >= self::$_maxLine) {
			self::_rotate($fileName);
		}
		
		$r = @fopen($filePath, 'a');
		@fputs($r, $logText);
		@fclose($r);
	}

	/**
	 * Output log as information log
	 *
	 * @param string $message Log message
	 * @param string $logKey Log key for file name
	 * @return void
	 */
	public static function info($message, $logKey = 'default')
	{
        self::write($message, LogLevel::INFO, $logKey);
	}
	
	/**
	 * Output log as warning message
	 *
	 * @param string $message Log message
	 * @param string $logKey Log key for file name
	 * @return void
	 */
	public static function warn($message, $logKey = 'default')
	{
        self::write($message, LogLevel::WARN, $logKey);
	}
	
	/**
	 * Output log as error message
	 *
	 * @param string $message Log message
	 * @param string $logKey Log key for file name
	 * @return void
	 */
	public static function error($message, $logKey = 'default')
	{
        self::write($message, LogLevel::ERROR, $logKey);
	}
	
	/**
	 * Output log as debug message
	 *
	 * @param string $message Log message
	 * @param string $logKey Log key for file name
	 * @return void
	 */
	public static function debug($message, $logKey = 'default')
	{
        self::write($message, LogLevel::DEBUG, $logKey);
	}
	
	/**
	 * Output log as information of exception
	 * 
	 * @param Exception $e
	 * @param string $logKey Log key for file name
	 * @return void
	 */
	public static function except(Exception $e, $logKey = 'default')
	{
		if (self::$_outputLevel > LogLevel::EXCEPT) {
			return;
		}
		
		$message = sprintf(
			"FILE:%s LINE:%s %s\n%s",
			$e->getFile(),
			$e->getLine(),
			$e->getMessage(),
			$e->getTraceAsString()
			);
		
        self::write($message, LogLevel::EXCEPT, $logKey);
	}
	
	/**
	 * Execute log rotation
	 * 
	 * @param string $fileName
	 * @return void
	 */
	private static function _rotate($fileName)
	{
		$dirPath = PathManager::getLogDirectory();
		
		// get file list in log directory
    	$dir = dir($dirPath);
    	$generations = array();
    	while ($content = $dir->read()) {
    		$path = sprintf("%s/%s", $dirPath, $content);
    		if (!is_file($path)) {
    			// ignore directory
    			continue;
    		}
    		$info = pathinfo($path);
    		$name = $info['basename'];
    		if (!preg_match(sprintf('|^%s(\.[0-9]+)?$|', $fileName), $name)) {
    			// ignore not match log name
    			continue;	
    		}
    		$generation = trim(str_replace($fileName, '', $name), '.');
    		if ($generation == '') {
    			$generation = 0;
    		}
    		if ($generation >= self::$_generation - 1) {
    			// delete if rotate num is over generation setting
    			@unlink($path);
    		} else {
    			$generations[] = $generation;
    		}
    	}
    	// increment log extension as rotate num 
    	rsort($generations);
    	foreach ($generations as $gene) {
    		$oldName = sprintf('%s/%s.%s', $dirPath, $fileName, $gene);
    		$newName = sprintf('%s/%s.%s', $dirPath, $fileName, $gene + 1);
    		@rename($oldName, $newName);
    	}
    	$filePath = sprintf("%s/%s", $dirPath, $fileName);
    	@rename($filePath, $filePath . '.1');
    	$dir->close();
	}

}