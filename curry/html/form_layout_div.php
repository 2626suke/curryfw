<?php

/**
 * @see FormLayoutAbstract
 */
require_once 'html/form_layout_abstract.php';

/**
 * FormLayoutDiv
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
class FormLayoutDiv extends FormLayoutAbstract
{	
	/**
	 * Tag name of container that contains caption
	 *
	 * @var string 
	 */
	protected $_captionContainerTagName = 'div';
	
	/**
	 * Tag name of sub container that contains form element
	 *
	 * @var string 
	 */
	protected $_inputContainerTagName = 'div';
	
	/**
	 * Add element of container that contains form element and caption
	 * 
	 * @param HtmlElement $inputContainer
	 * @param HtmlElement $captionContainer
	 */
	protected function addFormElementContainer(HtmlElement $inputContainer, HtmlElement $captionContainer)
	{
		$div = new HtmlElement('div');
		$div->addElement($captionContainer);
		$div->addElement($inputContainer);
		$this->addElement($div);
	}
	
}
