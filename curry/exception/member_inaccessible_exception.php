<?php

/**
 * MemberInaccessibleException
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
class MemberInaccessibleException extends Exception
{

	/**
	 * Class name
	 *
	 * @var string
	 */	
	protected $_class;
	
	/**
	 * Member name
	 *
	 * @var string
	 */	
	protected $_member;
			
	/**
	 * Constructor
	 *
	 * @param string $className Class name 
	 * @param string $memberName Member name
	 * @return void
	 */
	public function __construct($className, $memberName)
	{
		$this->_class = $className;
		$this->_member = $memberName;
		parent::__construct(sprintf('"%s::%s" is inaccessible.', $className, $memberName));
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
	
	/**
	 * Get member name
	 *
	 * @return string Member name
	 */	
	public function getMember()
	{
		return $this->_member;
	}
}