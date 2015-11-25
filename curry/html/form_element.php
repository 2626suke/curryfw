<?php

/**
 * @see HtmlElement
 */
require_once 'html/html_element.php';

/**
 * FormElement
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
class FormElement extends HtmlElement
{	
	/**
	 * Element of parent form
	 *
	 * @var HtmlForm 
	 */
	protected $_parentForm;
	
	/**
	 * Caption of form item in case of using form layout
	 * 
	 * @var string 
	 */
	protected $_caption;
		
	/**
	 * Message of input error
	 *
	 * @var string 
	 */
	protected $_errorMessage;
	
	/**
	 * Parent element of caption node of form element
	 *
	 * @var HtmlElement 
	 */
	protected $_captionContainer;
	
	/**
	 * Parent element of form element
	 *
	 * @var HtmlElement 
	 */
	protected $_inputContainer;
	
	/**
	 * Element of error message
	 *
	 * @var HtmlElement 
	 */
	protected $_errorContainer;
	
	/**
	 * Parent layout
	 *
	 * @var FormLayoutAbstract
	 */
	protected $_parentLayout;
	
	/**
	 * Set parent layout
	 * 
	 * @param HtmlForm $form
	 * @return void
	 */
	public function setParentForm(HtmlForm $form)
	{
		$this->_parentForm = $form;
	}
	
	/**
	 * Set parent layout
	 * 
	 * @param FormLayoutAbstract $layout
	 * @return void
	 */
	public function setParentLayout(FormLayoutAbstract $layout)
	{
		$this->_parentLayout = $layout;
	}
	
	/**
	 * Set parent element of caption node of form element
	 * 
	 * @param HtmlElement $container
	 * @return void
	 */
	public function setCaptionContainer(HtmlElement $container)
	{
		$this->_captionContainer = $container;
		return $this;
	}
	
	/**
	 * Get parent element of caption node of form element
	 * 
	 * @return HtmlElement
	 * @return void
	 */
	public function getCaptionContainer()
	{
		return $this->_captionContainer;
	}
	
	/**
	 * Set element of error message
	 * 
	 * @param HtmlElement $container
	 * @return FormElement
	 */
	public function setErrorContainer(HtmlElement $container)
	{
		$this->_errorContainer = $container;
		return $this;
	}
	
	/**
	 * Set parent element of form element
	 * 
	 * @param HtmlElement $container
	 * @return FormElement
	 */
	public function setInputContainer(HtmlElement $container)
	{
		$this->_inputContainer = $container;
		return $this;
	}
	
	/**
	 * Get parent element of form element
	 * 
	 * @return HtmlElement
	 */
	public function getInputContainer()
	{
		return $this->_inputContainer;
	}
	
	/**
	 * Set attribute "disabled"
	 * 
	 * @param string|bool $disabled
	 * @return FormElement
	 */
	public function setDisabled($disabled = 'disabled')
	{
		if ($disabled === true || $disabled == 'true' || $disabled == 'disabled') {
			$this->setAttribute('disabled', 'disabled');
		}
		return $this;
	}
	
	/**
	 * Set Caption of form item in case of using form layout
	 * 
	 * @param string $caption
	 * @return FormElement
	 */
	public function setCaption($caption)
	{
		$this->_caption = $caption;
		
		if ($this->_captionContainer instanceof HtmlElement) {
			// rewrite caption node
			$displayCaption = $caption;
			if ($this->_parentLayout instanceof FormLayoutAbstract) {
				$displayCaption = sprintf($this->_parentLayout->getCaptionFormat(), $caption);
			}
			$this->_captionContainer->setText($displayCaption);
		}
			
		return $this;
	}
		
	/**
	 * Get caption of form item in case of using form layout
	 * 
	 * @return string
	 */
	public function getCaption()
	{
		return $this->_caption;
	}
	
	/**
	 * Set message of input error
	 *
	 * @param string $errorMessage
	 * @return HtmlElement
	 */
	public function setErrorMessage($errorMessage)
	{
		$this->_errorMessage = $errorMessage;
		if ($this->_parentLayout instanceof FormLayoutAbstract) {
			if ($errorMessage == '') {
				if ($this->_errorContainer instanceof HtmlElement) {
					$this->_parentLayout->removeErrorContainer($this, $this->_errorContainer);
				}
				$this->_errorContainer = null;
			} else {
				if (!($this->_errorContainer instanceof HtmlElement)) {
					$this->_errorContainer = $this->_parentLayout->addErrorContainer($this, $errorMessage);
				} else {
					$this->_errorContainer->setText($errorMessage);
				}
			}
		}
		return $this;
	}
	
	/**
	 * Get message of input error
	 * 
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->_errorMessage;
	}
				
	/**
	 * Set value of form element
	 * 
	 * @param string $value
	 * @return FormElement
	 */
	public function setValue($value)
	{
		if ($this->_tagName == 'textarea') {
			$this->setText($value);
		} else {
			$this->setAttribute('value', $value);
		}
		return $this;
	}	
	
}
