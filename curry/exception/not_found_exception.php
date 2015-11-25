<?php

/**
 * NotFoundException
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
class NotFoundException extends Exception
{
	/**
	 * Controller name
	 *
	 * @var string
	 */	
	protected $_controller;
	
	/**
	 * Action name
	 *
	 * @var string
	 */	
	protected $_action;
			
	/**
	 * Constructor
	 *
	 * @param string $controller Controller name 
	 * @param string $action Action name
	 * @return void
	 */
	public function __construct($controller, $action)
	{
		$this->_controller = $controller;
		$this->_action = $action;
		parent::__construct(sprintf('The path "%s/%s" is not found.', $controller, $action));
	}
	
	/**
	 * Get controller name
	 *
	 * @return string Controller name
	 */	
	public function getController()
	{
		return $this->_controller;
	}
	
	/**
	 * Get action name
	 *
	 * @return string Action name
	 */	
	public function getAction()
	{
		return $this->_action;
	}
}