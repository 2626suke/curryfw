<?php

/**
 * @see FormLayoutAbstract
 */
require_once 'html/form_layout_abstract.php';

/**
 * FormLayoutStandard
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    html
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class FormLayoutStandard extends FormLayoutAbstract
{
	/**
	 * Tag name of item container
	 *
	 * @var string 
	 */
	protected $_containerTagName = 'div';
	
	/**
	 * Value of class attribute of item container
	 *
	 * @var string 
	 */
	protected $_containerClass = 'container';
	
	/**
	 * Tag name of container that contains caption
	 *
	 * @var string 
	 */
	protected $_captionContainerTagName = 'span';
	
	/**
	 * Tag name of sub container that contains form element
	 *
	 * @var string 
	 */
	protected $_inputContainerTagName = 'span';
	
	/**
	 * Set tag name of container element of sub container
	 * 
	 * @param string $tagName
	 * @return void
	 */
	public function setContainerTagName($tagName)
	{
		$this->_containerTagName = $tagName;
	}
	
	/**
	 * Set default class attribute value for container element
	 * 
	 * @param string $class
	 * @return void
	 */
	public function setContainerClass($class)
	{
		$this->_containerClass = $class;
	}
	
	/**
	 * Add element of container that contains form element and caption
	 * 
	 * @param HtmlElement $inputContainer
	 * @param HtmlElement $captionContainer
	 */
	protected function addFormElementContainer(HtmlElement $inputContainer, HtmlElement $captionContainer)
	{
		$container = new HtmlElement($this->_containerTagName);
		$container->setClass($this->_containerClass);
		$container->addElement($captionContainer);
		$container->addElement($inputContainer);
		$this->_parentForm->addElement($container);
	}
	
}
