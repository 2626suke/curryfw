<?php

/**
 * @see FormLayoutAbstract
 */
require_once 'html/form_layout_abstract.php';

/**
 * FormLayoutTable
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
class FormLayoutTable extends FormLayoutAbstract
{	
	/**
	 * Tag name of container that contains caption
	 *
	 * @var string 
	 */
	protected $_captionContainerTagName = 'td';
	
	/**
	 * Tag name of sub container that contains form element
	 *
	 * @var string 
	 */
	protected $_inputContainerTagName = 'td';
	
	/**
	 * Element of body container
	 * 
	 * @var HtmlElement 
	 */
	protected $_tbody;
		
	/**
	 * Add element of container that contains form element and caption
	 * 
	 * @param HtmlElement $inputContainer
	 * @param HtmlElement $captionContainer
	 */
	protected function addFormElementContainer(HtmlElement $inputContainer, HtmlElement $captionContainer)
	{
		if (!($this->_tbody instanceof HtmlElement)) {
			$this->_tbody = new HtmlElement('tbody');
			$this->addElement($this->_tbody);			
		}
		$tr = new HtmlElement('tr');
		$tr->addElement($captionContainer);
		$tr->addElement($inputContainer);
		$this->_tbody->addElement($tr);
	}
	
}
