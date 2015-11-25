<?php

/**
 * @see HtmlElement
 */
require_once 'html/html_element.php';

/**
 * FormLayoutAbstract
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
abstract class FormLayoutAbstract extends HtmlElement
{
	/**
	 * Element of parent form
	 * 
	 * @var HtmlForm 
	 */
	protected $_parentForm;
				
	/**
	 * Tag name of container that contains caption
	 *
	 * @var string 
	 */
	protected $_captionContainerTagName;
	
	/**
	 * Tag name of sub container that contains form element
	 *
	 * @var string 
	 */
	protected $_inputContainerTagName;
		
	/**
	 * Tag name of error message for form element that occurs input error
	 *
	 * @var string 
	 */
	protected $_errorTagName = 'span';
	
	/**
	 * Class attribute value of container element that contains caption text for form element
	 * 
	 * @var string 
	 */
	protected $_captionContainerClass = 'caption';
	
	/**
	 * Class attribute value of container element that contains form element
	 *
	 * @var string 
	 */
	protected $_inputContainerClass = 'input';
		
	/**
	 * Default class attribute value of error message for form element that occurs input error
	 *
	 * @var string 
	 */
	protected $_errorClass = 'error';
	
	/**
	 * Format of the caption of form item
	 * 
	 * @var string 
	 */
	protected $_captionFormat = '%s';
	
	/**
	 * Constructor
	 * 
	 * @param HtmlForm $form
	 * @return void
	 */
	public function __construct(HtmlForm $form)
	{
		$this->_parentForm = $form;
		$tagName = $this->_tagName;
		if ($this->_tagName == '') {
			// decide tag name from tail of class name
			require_once 'core/name_manager.php';
			$parts = NameManager::split(get_class($this));
			$tagName = array_pop($parts);
		}
		parent::__construct($tagName);
	}
	
	/**
	 * Element of parent form 
	 * 
	 * @return HtmlForm
	 */
	public function getParentForm()
	{
		return $this->_parentForm;
	}
	
	/**
	 * Get form elements as array from layout element
	 * 
	 * @return array
	 */
	public function getFormElements()
	{
		$ret = $this->_getFormElements($this);
		return $ret;
	}
	
	/**
	 * Get form elements as array from specified container element recursively
	 * 
	 * @param HtmlElement $container
	 * @return array
	 */
	protected function _getFormElements(HtmlElement $container)
	{
		$ret = array();
		foreach ($container->getNodes() as $elem) {
			if ($elem instanceof FormElement) {
				$ret[] = $elem;
			} else if ($elem instanceof FormLayoutAbstract) {
				$ret = array_merge($ret, $elem->getFormElements());
			} else if ($elem instanceof HtmlElement) {
				$ret = array_merge($ret, $this->_getFormElements($elem));
			}
		}
		return $ret;
	}
	
	
	/**
	 * Set tag name of sub container that contains form element
	 * 
	 * @param string $tagName
	 * @return FormLayoutAbstract
	 */
	public function setCaptionContainerTagName($tagName)
	{
		$this->_captionContainerTagName = $tagName;
		return $this;
	}
	
	/**
	 * Set tag name of sub container that contains form element
	 * 
	 * @param string $tagName
	 * @return FormLayoutAbstract
	 */
	public function setInputContainerTagName($tagName)
	{
		$this->_inputContainerTagName = $tagName;
		return $this;
	}
	
	/**
	 * Set tag name of container of error message
	 * 
	 * @param string $tagName
	 * @return FormLayoutAbstract
	 */
	public function setErrorTagName($tagName)
	{
		$this->_errorTagName = $tagName;
		return $this;
	}
	
	/**
	 * Set class attribute of container element for caption
	 * 
	 * @param string $class
	 * @return FormLayoutAbstract
	 */
	public function setCaptionContainerClass($class)
	{
		$this->_captionContainerClass = $class;
		return $this;
	}
	
	/**
	 * Set class attribute of container element for form element
	 * 
	 * @param string $class
	 * @return FormLayoutAbstract
	 */
	public function setInputContainerClass($class)
	{
		$this->_inputContainerClass = $class;
		return $this;
	}
	
	/**
	 * Set default class attribute value for error message
	 * 
	 * @param string $class
	 * @return FormLayoutAbstract
	 */
	public function setErrorClass($class)
	{
		$this->_parentForm->setErrorClass($class);
		return $this;
	}
			
	/**
	 * Set format of the caption of form item
	 * 
	 * @param string $format
	 * @return FormLayoutAbstract
	 */
	public function setCaptionFormat($format)
	{
		$this->_captionFormat = $format;
		return $this;
	}
	
	/**
	 * Get format of the caption of form item
	 * 
	 * @return string
	 */
	public function getCaptionFormat()
	{
		return $this->_captionFormat;
	}
	
	/**
	 * Create and add element of input tag whose type is text
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addTextbox($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createTextbox($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
		
	/**
	 * Create and add element of textarea tag 
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addTextarea($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createTextarea($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of input tag whose type is password
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addPassword($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createPassword($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of input tag whose type is hidden
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addHidden($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createHidden($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of input tag whose type is file
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addFile($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createFile($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
		
	/**
	 * Create and return element of input tag whose type is image
	 * 
	 * @param string $name
	 * @param string $value
	 * @param string $src
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addImage($name = null, $value = null, $src = null, $attributes = null)
	{
		$elem = $this->_parentForm->createImage($name, $value, $src, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
		
	/**
	 * Create and add element of input tag whose type is submit
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addSubmit($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createSubmit($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of input tag whose type is reset
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addReset($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createReset($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of input tag whose type is button
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addButton($name = null, $value = null, $attributes = null)
	{
		$elem = $this->_parentForm->createButton($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of select tag and that options
	 * 
	 * @param string $name
	 * @param array $options
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function addSelect($name = null, $options = null, $defaultValue = null, $attributes = null)
	{
		$elem = $this->_parentForm->createSelect($name, $options, $defaultValue, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of radio tags
	 * 
	 * @param string $name
	 * @param array $values
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function addRadio($name = null, $values = null, $defaultValue = null, $attributes = null)
	{
		$elem = $this->_parentForm->createRadio($name, $values, $defaultValue, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add element of input tag whose type is checkbox
	 * 
	 * @param string $name
	 * @param string $value
	 * @param string $text
	 * @param array $attributes
	 * @return FormElement
	 */
	public function addCheckbox($name = null, $value = null, $text = null, $attributes = null)
	{
		$elem = $this->_parentForm->createCheckbox($name, $value, $text, $attributes);
		$this->addFormElement($elem);
		return $elem->getElement(0);
	}
	
	/**
	 * Create and add element of input tags whose type are checkbox
	 * 
	 * @param string $name
	 * @param array $values
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function addCheckboxes($name = null, $values = null, $defaultValue = null, $attributes = null)
	{
		$elem = $this->_parentForm->createCheckboxes($name, $values, $defaultValue, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Add form element to layout element
	 * 
	 * @param FormElement $formElement
	 * @return FormLayoutAbstract
	 */
	public function addFormElement(FormElement $formElement)
	{
		$formElement->setParentLayout($this);
		$captionContainer = $this->_createCaptionContainer($formElement);
		$inputContainer = $this->_createInputContainer($formElement);
		$errorContainer = null;
		if ($formElement->getErrorMessage() != null && $this->_errorTagName != null) {
			$errorContainer = new HtmlElement($this->_errorTagName);
			$errorContainer->addClass($this->_errorClass);
			$errorContainer->setText($formElement->getErrorMessage());
			$inputContainer->addElement($errorContainer);
		}
		$this->addFormElementContainer($inputContainer, $captionContainer, $errorContainer);
		return $this;
	}
	
	/**
	 * Add element of container that contains form element and caption
	 * 
	 * @param HtmlElement $inputContainer
	 * @param HtmlElement $captionContainer
	 */
	abstract protected function addFormElementContainer(HtmlElement $inputContainer, HtmlElement $captionContainer);
		
	/**
	 * Create and add container tag of error message to layout
	 * 
	 * @param FormElement $formElement
	 * @param string $errorMessage
	 * @return HtmlElement
	 */
	public function addErrorContainer(FormElement $formElement, $errorMessage)
	{
		$errorContainer = new HtmlElement($this->_errorTagName);
		$errorContainer->setClass($this->_errorClass);
		$errorContainer->setText($errorMessage);
		$inputContainer = $formElement->getInputContainer();
		$inputContainer->addElement($errorContainer);
		return $errorContainer;
	}
	
	/**
	 * Remove container of error message from layout
	 * 
	 * @param FormElement $formElement
	 * @param HtmlElement $errorContainer
	 */
	public function removeErrorContainer(FormElement $formElement, HtmlElement $errorContainer)
	{
		$inputContainer = $formElement->getInputContainer();
		$inputContainer->removeElement($errorContainer);
	}
	
	/**
	 * Create element of container that contains caption texts or tags
	 * @param FormElement $formElement
	 * @return HtmlElement
	 */
	protected function _createCaptionContainer(FormElement $formElement)
	{
		// create container element
		$containerElement = new HtmlElement($this->_captionContainerTagName);
		if ($this->_captionContainerClass) {
			$containerElement->addClass($this->_captionContainerClass);
		}
		$formElement->setCaptionContainer($containerElement);
		
		// set caption
		if ($formElement->getCaption() != null) {
			$caption = sprintf($this->_captionFormat, $formElement->getCaption());
			$containerElement->setText($caption);
		}
						
		return $containerElement;
	}
	
	/**
	 * Create element of container that contains form element
	 * 
	 * @param FormElement $formElement
	 * @return HtmlElement
	 */
	protected function _createInputContainer(FormElement $formElement)
	{
		// create container element
		$containerElement = new HtmlElement($this->_inputContainerTagName);
		if ($this->_inputContainerClass) {
			$containerElement->addClass($this->_inputContainerClass);
		}
		$formElement->setInputContainer($containerElement);
				
		// set form element to container
		$containerElement->addElement($formElement);
				
		return $containerElement;
	}
	
}
