<?php

/**
 * @see FormElement
 */
require_once 'html/form_element.php';

/**
 * @see FormElementSet
 */
require_once 'html/form_element_set.php';

/**
 * @see FormLayoutStandard
 */
require_once 'html/form_layout_standard.php';

/**
 * HtmlForm
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
class HtmlForm extends HtmlElement
{
	/**
	 * Tag name of the element
	 * 
	 * @var string
	 */
	protected $_tagName = 'form';
	
	/**
	 * Captions for form elements
	 * 
	 * @var array 
	 */
	protected $_captions = array();
	
	/**
	 * Values for form elements
	 * 
	 * @var array 
	 */
	protected $_formValues = array();
	
	/**
	 * errors for form elements
	 * 
	 * @var array 
	 */
	protected $_errors = array();
	
	/**
	 * Default attributes of the element
	 * 
	 * @var array 
	 */
	protected $_defaultAttributes = array(
		'method' => 'post'
	);
	
	/**
	 * Whether be available auto layout
	 *
	 * @var boolean
	 */
	protected $_isAutoLayout = true;
	
	/**
	 * Instance of standard layout
	 *
	 * @var FormLayoutStandard 
	 */
	protected $_stdLayout;	
	
	/**
	 * Element of inner container
	 * 
	 * @var HtmlElement 
	 */
	protected $_innerContainer;
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->_stdLayout = new FormLayoutStandard($this);
		parent::__construct();
	}
	
	/**
	 * Set whether be available auto layout
	 * 
	 * @param boolean $isAutoLayout
	 */
	public function setIsAutoLayout($isAutoLayout)
	{
		$this->_isAutoLayout = $isAutoLayout;
		return $this;
	}
	
	/**
	 * Get whether be available auto layout
	 * 
	 * @return boolean
	 */
	public function isAutoLayout()
	{
		return $this->_isAutoLayout;
	}
	
	/**
	 * Set tag name of container element of sub container
	 * 
	 * @param string $tagName
	 * @return HtmlForm
	 */
	public function setContainerTagName($tagName)
	{
		$this->_stdLayout->setContainerTagName($tagName);
		return $this;
	}
	
	/**
	 * Set tag name of sub container that contains form element
	 * 
	 * @param string $tagName
	 * @return HtmlForm
	 */
	public function setCaptionContainerTagName($tagName)
	{
		$this->_stdLayout->setCaptionContainerTagName($tagName);
		return $this;
	}
	
	/**
	 * Set tag name of sub container that contains form element
	 * 
	 * @param string $tagName
	 * @return HtmlForm
	 */
	public function setInputContainerTagName($tagName)
	{
		$this->_stdLayout->setInputContainerTagName($tagName);
		return $this;
	}
	
	/**
	 * Set tag name of sub container that contains error message
	 * 
	 * @param string $tagName
	 * @return HtmlForm
	 */
	public function setErrorTagName($tagName)
	{
		$this->_stdLayout->setErrorTagName($tagName);
		return $this;
	}
	
	/**
	 * Set default class attribute value for container element
	 * 
	 * @param string $class
	 * @return HtmlForm
	 */
	public function setContainerClass($class)
	{
		$this->_stdLayout->setContainerClass($class);
		return $this;
	}
	
	/**
	 * Set default class attribute value for caption container element
	 * 
	 * @param string $class
	 * @return HtmlForm
	 */
	public function setCaptionContainerClass($class)
	{
		$this->_stdLayout->setCaptionContainerClass($class);
		return $this;
	}
	
	/**
	 * Set default class attribute value for input container element
	 * 
	 * @param string $class
	 * @return HtmlForm
	 */
	public function setInputContainerClass($class)
	{
		$this->_stdLayout->setInputContainerClass($class);
		return $this;
	}
	
	/**
	 * Set default class attribute value for error message
	 * 
	 * @param string $class
	 * @return HtmlForm
	 */
	public function setErrorClass($class)
	{
		$this->_stdLayout->setErrorClass($class);
		return $this;
	}
		
	/**
	 * Set element of inner container
	 * 
	 * @param HtmlElement $element
	 */
	public function setInnerContainer(HtmlElement $element)
	{
		$this->_innerContainer = $element;
		return $this;
	}
	
	/**
	 * Create and return form layout element
	 * 
	 * @param string $layoutTagName
	 * @return FormLayoutAbstract
	 * @throws FileNotExistException
	 */
	public function createLayout($layoutTagName)
	{
		$layoutClassName= NameManager::toClass('form_layout_' . trim(strtolower($layoutTagName)));
		if (!Loader::classExists($layoutClassName)) {
			$result = Loader::load($layoutClassName, 'html', true, false);
			if ($result == false) {
				$result = Loader::loadLibrary($layoutClassName);
				if ($result == false) {
					$fileName = NameManager::toPhpFile($layoutClassName);
					require_once 'exception/file_not_exist_exception.php';
					throw new FileNotExistException($fileName);
				}
			}
		}
		$layoutInstance = new $layoutClassName($this);
		return $layoutInstance;
	}
	
	/**
	 * Create and add form layout element
	 * 
	 * @param string $layoutTagName
	 * @return FormLayoutAbstract
	 */
	public function addLayout($layoutTagName)
	{
		$layoutInstance = $this->createLayout($layoutTagName);
		$this->addElement($layoutInstance);
		return $layoutInstance;
	}
	
	/**
	 * Add child element
	 * 
	 * @param HtmlElement $element
	 * @return HtmlElement
	 */
	public function addElement(HtmlElement $element)
	{
		if ($element instanceof FormLayoutAbstract && !($element instanceof FormLayoutStandard)) {
			$this->setIsAutoLayout(false);
		}
		return parent::addElement($element);
	}
	
	/**
	 * Create element of form input tag
	 * 
	 * @param string $tagName
	 * @return FormElement
	 */
	public function createFormElement($tagName)
	{
		$elem = new FormElement($tagName);
		$elem->setParentForm($this);
		return $elem;		
	}
	
	/**
	 * Create element set of form input tag
	 * 
	 * @param string $tagName
	 * @return FormElementSet
	 */
	public function createFormElementSet($tagName)
	{
		$elem = new FormElementSet($tagName);
		$elem->setParentForm($this);
		return $elem;		
	}
	
	/**
	 * Create and return element of input tag
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createInput($name = null, $value = null, $attributes = null)
	{
		if (!is_array($attributes)) {
			$attributes = array();
		}
		if ($name !== null) {
			$attributes['name'] = $name;
		}
		if ($value !== null) {
			$attributes['value'] = $value;
		}
		$elem = $this->createFormElement('input');
		$elem->setAttributes($attributes);
		return $elem;
	}
	
	/**
	 * Add form element to form
	 * 
	 * @param FormElement $formElement
	 * @return HtmlForm
	 */
	public function addFormElement(FormElement $formElement)
	{
		if ($this->_isAutoLayout == false) {
			$this->addElement($formElement);
		} else {
			$this->_stdLayout->addFormElement($formElement);
		}		
		return $this;
	}
	
	/**
	 * Create and return element of input tag whose type is text
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createTextbox($name = null, $value = null, $attributes = null)
	{
		$attributes['type'] = 'text';
		$elem = $this->createInput($name, $value, $attributes);
		return $elem;		
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
		$elem = $this->createTextbox($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of textarea tag 
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createTextarea($name = null, $value = null, $attributes = null)
	{
		$attributes['name'] = $name;
		$elem = $this->createFormElement('textarea');
		$elem->setIsReturnInner(false);
		$elem->setAttributes($attributes);
		if ($value !== null) {
			$elem->addNode($value);
		}
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
		$elem = $this->createTextarea($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tag whose type is password
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createPassword($name = null, $value = null, $attributes = null)
	{
		$attributes['type'] = 'password';
		$elem = $this->createInput($name, $value, $attributes);
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
		$elem = $this->createPassword($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tag whose type is hidden
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createHidden($name = null, $value = null, $attributes = null)
	{
		$attributes['type'] = 'hidden';
		$elem = $this->createInput($name, $value, $attributes);
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
		$elem = $this->createHidden($name, $value, $attributes);
		$this->addElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tag whose type is file
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createFile($name = null, $value = null, $attributes = null)
	{
		$attributes['type'] = 'file';
		$elem = $this->createInput($name, $value, $attributes);
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
		$elem = $this->createFile($name, $value, $attributes);
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
	public function createImage($name = null, $value = null, $src = null, $attributes = null)
	{
		$attributes['type'] = 'image';
		$attributes['src'] = $src;
		$elem = $this->createInput($name, $value, $attributes);
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
		$elem = $this->createImage($name, $value, $src, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tag whose type is submit
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createSubmit($name = null, $value = null, $attributes = null)
	{
		$attributes['type'] = 'submit';
		$elem = $this->createInput($name, $value, $attributes);
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
		$elem = $this->createSubmit($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tag whose type is reset
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createReset($name = null, $value = null, $attributes = null)
	{
		$attributes['type'] = 'reset';
		$elem = $this->createInput($name, $value, $attributes);
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
		$elem = $this->createReset($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tag whose type is button
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createButton($name = null, $value = null, $attributes = null)
	{
		$attributes['type'] = 'button';
		$elem = $this->createInput($name, $value, $attributes);
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
		$elem = $this->createButton($name, $value, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tag whose type is checkbox
	 * 
	 * @param string $name
	 * @param string $value
	 * @param string $text
	 * @param array $attributes
	 * @return FormElement
	 */
	public function createCheckbox($name = null, $value = null, $text = null, $attributes = null)
	{
		$attributes['type'] = 'checkbox';
		if ($value !== null) {
			$attributes['value'] = $value;
		}
		$checkbox = $this->createInput($name, $value, $attributes);
		$label = new HtmlElement('label');
		$label->setIsReturnInner(false);
		$label->addElement($checkbox);
		if ($text !== null) {
			$label->addNode($text);			
		}
		return $label;
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
		$elem = $this->createCheckbox($name, $value, $text, $attributes);
		$this->addFormElement($elem);
		return $elem->getElement(0);
	}
	
	/**
	 * Create and return element of select tag and that options
	 * 
	 * @param string $name
	 * @param array $list
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function createSelect($name = null, $list = array(), $defaultValue = null, $attributes = null)
	{
		$this->_checkArgumentIsArray(__METHOD__, 2, $list);
		
		$select = $this->createFormElementSet('select');
		if (is_array($list)) {
			foreach ($list as $value => $text) {
				$opt = new FormElement('option');
				$opt->setIsReturnInner(false);
				$opt->setValue($value)->addNode($text);
				if ($attributes !== null) {
					$opt->setAttributes($attributes);
				}
				$select->addElement($opt);
			}
		}
		$select->setName($name);
		if ($defaultValue !== null) {
			$select->setValue($defaultValue);
		}
		return $select;
	}
	
	/**
	 * Create and add element of select tag and that options
	 * 
	 * @param string $name
	 * @param array $list
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function addSelect($name = null, $list = null, $defaultValue = null, $attributes = null)
	{		
		$elem = $this->createSelect($name, $list, $defaultValue, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of radio tags
	 * 
	 * @param string $name
	 * @param array $list
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function createRadio($name = null, $list = array(), $defaultValue = null, $attributes = null)
	{
		$this->_checkArgumentIsArray(__METHOD__, 2, $list);
		
		$frame = $this->createFormElementSet('span');
		if (!is_array($attributes)) {
			$attributes = array();
		}
		$attributes['type'] = 'radio';
		$frame->setName($name);
		if (is_array($list)) {
			foreach ($list as $value => $text) {
				$radio = new FormElement('input');
				$radio->setIsReturnInner(false);
				$radio->setAttributes($attributes);
				$radio->setValue($value);
				$label = new HtmlElement('label');
				$label->setIsReturnInner(false);
				$label->addElement($radio);
				$label->addNode($text);
				$frame->addElement($label);
			}
		}
		if ($defaultValue !== null) {
			$frame->setValue($defaultValue);
		}
		return $frame;
	}
	
	/**
	 * Create and add element of radio tags
	 * 
	 * @param string $name
	 * @param array $list
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function addRadio($name = null, $list = array(), $defaultValue = null, $attributes = null)
	{		
		$elem = $this->createRadio($name, $list, $defaultValue, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and return element of input tags whose type are checkbox
	 * 
	 * @param string $name
	 * @param array $list
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function createCheckboxes($name = null, $list = array(), $defaultValue = null, $attributes = null)
	{
		$this->_checkArgumentIsArray(__METHOD__, 2, $list);
		
		$frame = $this->createFormElementSet('span');
		if (!is_array($attributes)) {
			$attributes = array();
		}
		$attributes['type'] = 'checkbox';
		if (is_array($list)) {
			foreach ($list as $value => $text) {
				$checkbox = new FormElement('input');
				$checkbox->setIsReturnInner(false);
				$checkbox->setAttributes($attributes);
				$checkbox->setValue($value);
				$label = new HtmlElement('label');
				$label->setIsReturnInner(false);
				$label->addElement($checkbox);
				$label->addNode($text);
				$frame->addElement($label);
			}
		}
		$frame->setName($name);
		if ($defaultValue !== null) {
			$frame->setValue($defaultValue);
		}
		return $frame;
	}
	
	/**
	 * Create and add element of input tags whose type are checkbox
	 * 
	 * @param string $name
	 * @param array $list
	 * @param string $defaultValue
	 * @param array $attributes
	 * @return FormElementSet
	 */
	public function addCheckboxes($name = null, $list = array(), $defaultValue = null, $attributes = null)
	{
		$elem = $this->createCheckboxes($name, $list, $defaultValue, $attributes);
		$this->addFormElement($elem);
		return $elem;
	}
	
	/**
	 * Create and add elements to form from array
	 * 
	 * @param array $elementSettings
	 */
	public function buildFromArray($elementSettings)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $elementSettings);
		
		// search layout element from child nodes
		$searchedLayout = null;
		foreach ($this->getNodes() as $elem) {
			if ($elem instanceof FormLayoutAbstract) {
				$searchedLayout = $elem;
				break;
			}
		}		
		// create form elements
		foreach ($elementSettings as $setting) {
			$type = strtolower($setting['type']);
			unset($setting['type']);
			$method = 'create' . ucfirst($type);
			$formElem = null;
			if ($type == 'select' || $type == 'radio' || $type == 'checkboxes') {
				$formElem = $this->$method(null, $setting['list']);
				unset($setting['list']);
			} else {
				$formElem = $this->$method();
			}
			foreach ($setting as $key => $val) {
				if ($key == 'layout'  || $key == 'container') {
					continue;
				}
				if ($key == 'caption') {
					// set caption
					$formElem->setCaption($val);
				} else {
					// call setter method
					$method = NameManager::toMethod('set_' . strtolower($key));
					$formElem->$method($val);
				}
			}
			
			// create layout element and add form element to there
			$layout = null;
			if (isset($setting['layout'])) {
				if ($setting['layout'] instanceof FormLayoutAbstract) {
					$layout = $setting['layout'];
				} else if ($setting['layout'] === true && $searchedLayout instanceof FormLayoutAbstract) {
					$layout = $searchedLayout;
				}
			}
			
			// add child node of form
			if ($layout instanceof FormLayoutAbstract) {
				$layout->addFormElement($formElem);
			} else {
				if (isset($setting['container']) && $setting['container'] instanceof HtmlElement) {
					$container = $setting['container'];
					$container->addElement($formElem);
					$this->addElement($container);
				} else {
					$this->addFormElement($formElem);
				}
			}
		}
	}
	
	/**
	 * Bind caption for form element
	 * 
	 * @param string $name
	 * @param string $caption
	 * @return HtmlForm
	 */
	public function bindCaption($name, $caption)
	{
		$this->_captions[$name] = $caption;
		return $this;
	}
	
	/**
	 * Bind captions for elements 
	 * 
	 * @param array $captions
	 * @return HtmlForm
	 */
	public function bindCaptions($captions)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $captions);		
		foreach ($captions as $name => $caption) {
			$this->bindCaption($name, $caption);
		}
		return $this;
	}
	
	/**
	 * Bind value for form element
	 * 
	 * @param string $name
	 * @param string $value
	 * @return HtmlForm
	 */
	public function bindValue($name, $value)
	{
		$this->_formValues[$name] = $value;
		return $this;
	}
	
	/**
	 * Bind values for elements 
	 * 
	 * @param array $values
	 * @return HtmlForm
	 */
	public function bindValues($values)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $values);		
		foreach ($values as $name => $value) {
			$this->bindValue($name, $value);
		}
		return $this;
	}
	
	/**
	 * Bind error message for form element
	 * 
	 * @param string $name
	 * @param string $value
	 * @return HtmlForm
	 */
	public function bindError($name, $value)
	{
		$this->_errors[$name] = $value;
		return $this;
	}
	
	/**
	 * Bind error messages for elements 
	 * 
	 * @param array $errorMessages
	 * @return HtmlForm
	 */
	public function bindErrors($errorMessages)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $errorMessages);		
		foreach ($errorMessages as $name => $value) {
			$this->bindError($name, $value);
		}
		return $this;
	}
	
	/**
	 * Get form elements as array from form
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
	 * Build and get HTMLv
	 * 
	 * @param int $level
	 * @return type
	 */
	protected function _getHtml($level = 0)
	{
		foreach ($this->getFormElements() as $formElem) {
			// set form values
			$name = $formElem->getName();
			if (isset($this->_formValues[$name])) {
				$formElem->setValue($this->_formValues[$name]);
			}
			// set input errors
			if (isset($this->_errors[$name])) {
				$formElem->setErrorMessage($this->_errors[$name]);
			}
			// set captions
			if (isset($this->_captions[$name])) {
				$formElem->setCaption($this->_captions[$name]);
			}
		}		
		if ($this->_innerContainer instanceof HtmlElement) {
			$elements = $this->getElements();
			$this->clearNodes();
			$this->_innerContainer->addElements($elements);
			$this->addElement($this->_innerContainer);
		}
		return parent::_getHtml($level);
	}
	
}
