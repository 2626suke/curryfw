<?php

/**
 * ClassUndefinedException
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
class ClassUndefinedException extends Exception
{
	/**
	 * Class name
	 *
	 * @var string
	 */	
	protected $_class;
				
	/**
	 * Constructor
	 *
	 * @param string $className Class name 
	 * @return void
	 */
	public function __construct($className)
	{
		$this->_class = $className;
		parent::__construct(sprintf('class "%s" is not defined.', $className));
	}
	
	/**
	 * Get class name
	 *
	 * @return string Class name
	 */	
	public function getClass()
	{
		return $this->_class;
	}
	
}