<?php

/**
 * FileNotExistException
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    eception
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class FileNotExistException extends Exception
{
	/**
	 * file name
	 *
	 * @var string
	 */	
	protected $_fileName;
				
	/**
	 * Constructor
	 *
	 * @param string $className Class name 
	 * @return void
	 */
	public function __construct($fileName)
	{
		$this->_fileName = $fileName;
		parent::__construct(sprintf('The file or directory "%s" does not exist.', $fileName));
	}
	
	/**
	 * Get class name
	 *
	 * @return string File name
	 */	
	public function getFileName()
	{
		return $this->_fileName;
	}
	
}