<?php

/**
 * @see FormElement
 */
require_once 'html/form_element.php';

/**
 * FormElementSet
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
class FormElementSet extends FormElement
{
	/**
	 * Value of "name" attribute
	 *
	 * @var string 
	 */
	protected $_name;
	
	/**
	 * Default value
	 *
	 * @var string|array 
	 */
	protected $_defaultValue;
	
	/**
	 * Set default value
	 * 
	 * @param string|array $value
	 * @return FormElementSet
	 */
	public function setValue($value)
	{
		$this->_defaultValue = $value;
		return $this;
	}
	
	/**
	 * Set value of name attribute
	 * 
	 * @param string $name
	 * @return FormElementSet
	 */
	public function setName($name)
	{
		$this->_name = $name;
		if ($this->_tagName == 'select') {
			$this->setAttribute('name', $name);
		} else {
			$formElems = $this->_searchFormElement($this);
			foreach ($formElems as $elem) {
				$elem->setName($name);
			}
		}
		return $this;
	}
	
	/**
	 * Get value of name attribute
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Set value of "multiple" attribute
	 * 
	 * @param string|bool $multiple
	 * @return FormElementSet
	 */
	public function setMultiple($multiple = 'multiple')
	{
		if ($this->_tagName == 'select') {
			if ($multiple === true || $multiple == 'true' || $multiple == 'multiple') {
				$this->setAttribute('multiple', 'multiple');
			}
		}
		return $this;
	}
	
	/**
	 * Add child element
	 * 
	 * @param HtmlElement $element
	 * @return HtmlElement
	 */
	public function addElement(HtmlElement $element)
	{
		if ($this->_name !== null) {
			$formElems = $this->_searchFormElement($element);
			foreach ($formElems as $formElem) {
				$formElem->setName($this->_name);
			}
		}		
		return parent::addElement($element);
	}
	
	/**
	 * Build and get HTML
	 * 
	 * @param int $level
	 * @return string
	 */
	protected function _getHtml($level = 0)
	{
		$this->_setElementsDefault();
		return parent::_getHtml($level);
	}
	
	/**
	 * Set attribute to set status of default selected
	 * 
	 * @return void
	 */
	protected function _setElementsDefault()
	{
		if ($this->_defaultValue === null) {
			return;
		}
		$formElems = $this->_searchFormElement($this);
		foreach ($formElems as $formElem) {
			$values = $this->_defaultValue;
			if (!is_array($this->_defaultValue)) {
				$values = array($this->_defaultValue);
			}
			if (in_array($formElem->getValue(), $values)) {
				$defValKeyword = 'checked';
				if ($formElem->getTagName() == 'option') {
					$defValKeyword = 'selected';
				}
				$formElem->setAttribute($defValKeyword, $defValKeyword);	
			}
		}		
	}
	
	/**
	 * Search and get FormElement instances under specified element
	 * 
	 * @param HtmlElement $element
	 * @return array
	 */
	protected function _searchFormElement(HtmlElement $element)
	{
		$formElems = array();
		$elems = $element->getNodes();
		foreach ($elems as $elem) {
			if ($elem instanceof FormElement) {
				$formElems[] = $elem;
			} else if ($elem instanceof HtmlElement) {
				$result = $this->_searchFormElement($elem);
				$formElems = array_merge($formElems, $result);
			}
		}
		return $formElems;
	}
}
