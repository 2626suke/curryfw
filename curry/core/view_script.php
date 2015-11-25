<?php

/**
 * ViewScript
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
class ViewScript extends CurryClass
{	
	/**
	 * View instance
	 *
	 * @var ViewAbstract extended instnace
	 */
	protected $view;
			
	/**
	 * Request instance, contains request informations
	 *
	 * @var Request
	 */
	protected $request;
	
	/**
	 * Set view instance
	 *
	 * @param ViewAbstract $view ViewAbstract extended instnace
	 * @return void
	 */
	public function setView(ViewAbstract $view)
	{
		$this->view = $view;
	}
	
	/**
	 * Set the Request instance
	 *
	 * @param Request $request Request instance
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;		
	}	
	
	/**
	 * Alias of method "getVar"
	 *
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name)
	{
		return $this->getVar($name);
	}
	
	/**
	 * Alias of method "setVar"
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->setVar($name, $value);
	}
	
	/**
	 * Get view var
	 * 
	 * @param string $name
	 * @return mivxed
	 */
	public function getVar($name = null)
	{
		return $this->view->get($name);
	}
	
	/**
	 * Set view var
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function setVar($name, $value)
	{
		$this->view->set($name, $value);
	}
}
