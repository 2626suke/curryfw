<?php

/**
 * @see FormLayoutAbstract
 */
require_once 'html/form_layout_abstract.php';

/**
 * FormLayoutDl
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
class FormLayoutDl extends FormLayoutAbstract
{
	/**
	 * Tag name of container that contains caption
	 *
	 * @var string 
	 */
	protected $_captionContainerTagName = 'dt';
	
	/**
	 * Tag name of sub container that contains form element
	 *
	 * @var string 
	 */
	protected $_inputContainerTagName = 'dd';
	
	/**
	 * Add element of container that contains form element and caption
	 * 
	 * @param HtmlElement $inputContainer
	 * @param HtmlElement $captionContainer
	 */
	protected function addFormElementContainer(HtmlElement $inputContainer, HtmlElement $captionContainer)
	{
		$this->addElement($captionContainer);
		$this->addElement($inputContainer);
	}
}
