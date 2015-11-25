<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * HtmlElement
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
class HtmlElement extends CurryClass
{
	/**
	 * Tag name of the element
	 * 
	 * @var string
	 */
	protected $_tagName;
		
	/**
	 * Child elements
	 * 
	 * @var array 
	 */
	protected $_nodes = array();
	
	/**
	 * Default attributes of the element
	 * 
	 * @var array 
	 */
	protected $_defaultAttributes = array();
	
	/**
	 * Attributes of the element
	 * 
	 * @var array 
	 */
	protected $_attributes = array();
		
	/**
	 * Class attribute values of the element
	 * 
	 * @var array 
	 */
	protected $_classes = array();
	
	/**
	 * Style attribute properties of the element
	 * 
	 * @var array 
	 */
	protected $_styles = array();
	
	/**
	 * Whether the element is empty tag or not.
	 * 
	 * @var boolean
	 */
	protected $_isEmptyTag = false;
	
	/**
	 * Whether insert return code around inner nodes
	 * 
	 * @var boolean
	 */
	protected $_isReturnInner = null;
	
	/**
	 * List of empty tag
	 *
	 * @var array 
	 */
	protected static $_emptyTags = array('br', 'input', 'img', 'link', 'meta', 'col');
		
	/**
	 * List of tag in which the line feed code is forbidden 
	 *
	 * @var array 
	 */
	protected static $_returnInnerNgTags = array('a', 'textarea', 'label', 'strong', 'em', 'object');
	
	/**
	 * Indent chars for human readable
	 *
	 * @var string 
	 */
	protected static $_indent = "  ";
	
	/**
	 * constructor
	 * 
	 * @param string $tagName
	 * @return void
	 */
	public function __construct($tagName = null)
	{
		if ($tagName != null) {
			$this->setTagName($tagName);
		}
	}
	
	/**
	 * __call
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		if (substr($name, 0, 3) == 'set') {
			// call attribute setter
			$attrName = strtolower(substr($name, 3));
			$this->setAttribute($attrName, $arguments[0]);
			return $this;
		} else if (substr($name, 0, 3) == 'get') {
			// call attribute getter
			$attrName = strtolower(substr($name, 3));
			$value = $this->getAttribute($attrName);
			return $value;
		}
	}
	
	/**
	 * Create and return HtmlElement instance.
	 * 
	 * @param string $tagName
	 * @return HtmlElement
	 */
	public static function create($tagName = null)
	{
		$className = get_called_class();
		$instance = new $className($tagName);
		return $instance;
	}
	
	/**
	 * Set indent char for human readable
	 *
	 * @param string $char
	 * @param int $length
	 * @return void
	 */
	public static function setIndentChar($char = " ", $length = 2)
	{
		if ($char === false) {
			self::$_indent = '';
		} else {
			self::$_indent = str_repeat($char, $length);
		}
	}
	
	/**
	 * Create and return a clone of this instance.
	 * 
	 * @return HtmlElement
	 */
	public function getClone()
	{
		$clone = clone $this;
		return $clone;
	}	
	
	/**
	 * Set tag name of the element
	 * 
	 * @param string $tagName
	 * @return HtmlElement
	 */
	public function setTagName($tagName)
	{
		$this->_tagName = $tagName;
		if (in_array($tagName, self::$_emptyTags)) {
			$this->_isEmptyTag = true;
		}
		if (in_array($tagName, self::$_returnInnerNgTags)) {
			$this->_isReturnInner = false;
		}
		return $this;
	}
	
	/**
	 * Get tag name of the element
	 * 
	 * @return string
	 */
	public function getTagName()
	{
		return $this->_tagName;
	}
	
	/**
	 * Clear class attribute values
	 * 
	 * @return HtmlElement
	 */
	public function clearClass()
	{
		$this->_classes = array();
		return $this;
	}
	
	/**
	 * Add class attribute value
	 * 
	 * @param string $class
	 * @return HtmlElement
	 */
	public function addClass($class)
	{
		$this->_classes[] = $class;
		return $this;
	}
	
	/**
	 * whether exists value of class attribute specified by parameter
	 * 
	 * @param type $class
	 * @return boolean
	 */
	public function hasClass($class)
	{
		$exists = false;
		if (in_array($class, $this->_classes)) {
			$exists = true;
		}
		return $exists;
	}
	
	/**
	 * Get value of class attribute
	 * 
	 * @return string
	 */
	public function getClass()
	{
		$ret = null;
		if (is_array($this->_classes)) {
			$ret = trim(implode(' ', $this->_classes));
		}
		return $ret;
	}
		
	/**
	 * Remove class attribute value
	 * 
	 * @param string $class
	 * @return HtmlElement
	 */
	public function removeClass($class)
	{
		foreach ($this->_classes as $key => $val) {
			if ($val == $class) {
				unset($this->_classes[$key]);
				$this->_classes = array_merge($this->_classes);
				break;
			}
		}
		return $this;
	}
	
	/**
	 * Set css by property name and value
	 * 
	 * @param string  $cssProperty
	 * @param string $cssValue
	 * @return HtmlElement
	 */
	public function addStyle($cssProperty, $cssValue)
	{
		$this->_styles[$cssProperty] = $cssValue;
		return $this;
	}
	
	/**
	 * Set plural css by array
	 * 
	 * @param array $styles
	 * @return HtmlElement
	 */
	public function addStyles($styles)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $styles);
		
		foreach ($styles as $cssProperty => $cssValue) {
			$this->addStyle($cssProperty, $cssValue);
		}
		return $this;
	}
	
	/**
	 * Get value of style attribute
	 * 
	 * @return string
	 */
	public function getStyle()
	{
		$style = null;
		if (is_array($this->_styles) && count($this->_styles) > 0) {
			if (isset($this->_attributes['style'])) {
				$style = rtrim($this->_attributes['style'], ';') . ';';
			}
			$styles = array();
			foreach ($this->_styles as $property => $value) {
				$styles[] = $property . ':' . $value;
			}
			$style .= trim(implode(';', $styles));
		}
		return $style;
	}
	
	/**
	 * Add child nodes
	 * 
	 * @param array $nodes
	 * @return HtmlElement
	 */
	public function addNodes($nodes)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $nodes);
		
		foreach ($nodes as $node) {
			$this->addNode($node);
		}
		return $this;
	}
	
	/**
	 * Add a child node
	 * 
	 * @param mixed $node
	 * @return HtmlElement
	 */
	public function addNode($node)
	{
		if ($node instanceof HtmlElement) {
			$this->addElement($node);
		} else {
			$this->addText($node);
		}
		return $this;
	}
	
	/**
	 * Insert child node before target node
	 * 
	 * @param mixed $node
	 * @param mixed $target
	 * @return HtmlElement
	 */
	public function insertNode($node, $target)
	{
		$inserted = false;
		foreach ($this->getNodes() as $index => $elem) {
			$isTarget = false;
			if ($target instanceof HtmlElement) {
				if ($target === $elem) {
					$isTarget = true;
				}
			} else {
				if ($target == $index) {
					$isTarget = true;
				}								
			}
			if ($isTarget == true) {
				$inserted = true;
				array_splice($this->_nodes, $index, 0, $node);
				if ($node instanceof HtmlElement && !is_bool($this->_isReturnInner)) {
					$this->setIsReturnInner(true);
				}
				break;
			}
		}
		if ($inserted == false) {
			$this->addNode($node);
		}
		return $this;
	}
	
	/**
	 * Get all child nodes
	 * 
	 * @return array
	 */
	public function getNodes()
	{
		return $this->_nodes;
	}
	
	/**
	 * Get a child element by specifying index
	 * 
	 * @param type $index
	 * @return HtmlElement
	 */
	public function getNode($index)
	{
		$node = null;
		if (isset($this->_nodes[$index])) {
			$node = $this->_nodes[$index];
		}
		return $node;
	}
	
	/**
	 * Add child element
	 * 
	 * @param HtmlElement $element
	 * @return HtmlElement
	 */
	public function addElement(HtmlElement $element)
	{
		$this->_nodes[] = $element;
		if (!is_bool($this->_isReturnInner)) {
			$this->setIsReturnInner(true);
		}
		return $this;
	}
	
	/**
	 * Insert child element before target node
	 * 
	 * @param HtmlElement $element
	 * @param mixed $target
	 * @return HtmlElement
	 */
	public function insertElement(HtmlElement $element, $target)
	{
		$this->insertNode($element, $target);
		return $this;
	}
		
	/**
	 * Add child element
	 * 
	 * @param HtmlElement $element
	 * @return HtmlElement
	 */
	public function addElements($elements)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $elements);
		
		foreach ($elements as $element) {
			$this->addElement($element);
		}
		return $this;
	}
	
	/**
	 * Get a child element by specifying index
	 * 
	 * @param int $index
	 * @return HtmlElement
	 */
	public function getElement($index)
	{
		$node = $this->getNode($index);
		if (!($node instanceof HtmlElement)) {
			return null;
		}
		return $node;
	}
		
	/**
	 * Get a child element by specifying value of id attribute
	 * 
	 * @param string $id
	 * @return HtmlElement
	 */
	public function getElementById($id)
	{
		$ret = null;
		foreach ($this->getElements() as $elem) {
			if ($elem->getId() == $id) {
				$ret = $elem;
				break;
			}
		}
		return $ret;
	}
	
	/**
	 * Get child elementd by specifying tag name
	 * 
	 * @param string $tagName
	 * @return array
	 */
	public function getElementsByTagName($tagName)
	{
		$ret = array();
		foreach ($this->getElements() as $elem) {
			if ($elem->getTagName() == $tagName) {
				$ret[] = $elem;
			}
		}
		return $ret;
	}
	
	/**
	 * Get child elementd by specifying value of class attribute
	 * 
	 * @param string $class
	 * @return array
	 */
	public function getElementsByClass($class)
	{
		$ret = array();
		foreach ($this->getElements() as $elem) {
			if ($elem->hasClass($class)) {
				$ret[] = $elem;
			}
		}
		return $ret;
	}
	
	/**
	 * Get all child elements
	 * 
	 * @return array
	 */
	public function getElements()
	{
		$ret = array();
		foreach ($this->getNodes() as $node) {
			if ($node instanceof HtmlElement) {
				$ret[] = $node;
			}
		}
		return $ret;
	}
	
	/**
	 * Remove child element
	 * 
	 * @param HtmlElement $element
	 * @return HtmlElement
	 */
	public function removeElement(HtmlElement $element)
	{
		$nodes = $this->getNodes();
		$this->clearNodes();
		foreach ($nodes as $node) {
			if ($node !== $element) {
				$this->addNode($node);
			}
		}
		return $this;
	}
	
	/**
	 * Add text node
	 * 
	 * @param string $text
	 * @return HtmlElement
	 */
	public function addText($text)
	{
		if (is_object($text) || is_array($text)) {
			throw new ArgumentInvalidException(__METHOD__, $text, 1, "string");
		}
		$this->_nodes[] = $text;
		return $this;
	}
		
	/**
	 * Set text node
	 * 
	 * @param string $text
	 * @return HtmlElement
	 */
	public function setText($text)
	{
		$this->clearNodes();
		return $this->addText($text);
	}
		
	/**
	 * Clear child elements
	 * 
	 * @return void
	 */
	public function clearNodes()
	{
		$this->_nodes = array();
	}
	
	/**
	 * Set an attribute
	 * 
	 * @param string $name
	 * @param string $value
	 * @return HtmlElement
	 */
	public function setAttribute($name, $value)
	{
		if ($name == 'class') {
			$this->clearClass();
			$this->addClass($value);
		} else {
			$this->_attributes[$name] = $value;
		}
		return $this;
	}
	
	/**
	 * Set plural attributes by array
	 * 
	 * @param array $attributes
	 * @return HtmlElement
	 */
	public function setAttributes($attributes)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $attributes);
		
		foreach ($attributes as $name => $value) {
			$this->setAttribute($name, $value);
		}
		return $this;
	}
	
	/**
	 * Get an attribute value
	 * 
	 * @param string $name
	 * @return string
	 */
	public function getAttribute($name)
	{
		$ret = null;
		if (isset($this->_attributes[$name])) {
			$ret = $this->_attributes[$name];
		}
		return $ret;
	}
	
	/**
	 * Get plural attributes as array
	 * 
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}
		
	/**
	 * Set whether be empty tag
	 * 
	 * @param boolean $isEmpty
	 * @return HtmlElement
	 */
	public function setIsEmptyTag($isEmpty)
	{
		$this->_isEmptyTag = $isEmpty;
		return $this;
	}
	
	/**
	 * Set whether be empty tag
	 * 
	 * @return boolean
	 */
	public function isEmptyTag()
	{
		return $this->_isEmptyTag;
	}
	
	/**
	 * Add to the list of empty tag
	 * 
	 * @param string|array $tagName
	 * @return void
	 */
	public static function addEmptyTags($tagName)
	{
		$tags = $tagName;
		if (!is_array($tagName)) {
			$tags = array($tagName);
		}
		foreach ($tags as $tag) {
			if (!in_array($tag, self::$_emptyTags)) {
				self::$_emptyTags[] = $tag;
			}
		}
	}
			
	/**
	 * Set whether insert return code around inner nodes
	 * 
	 * @param bool $returnInner
	 * @return HtmlElement
	 */
	public function setIsReturnInner($returnInner)
	{
		$this->_isReturnInner = $returnInner;
		return $this;
	}
	/**
	 * Set whether to insert return code element's inner 
	 * 
	 * @param bool $returnInner
	 * @return HtmlElement
	 */
	public function isReturnInner()
	{
		return $this->_isReturnInner;
	}
	
	/**
	 * Add to the list of tag in which the line feed code is forbidden 
	 * 
	 * @param string $tagName
	 * @return void
	 */
	public static function addReturnInnerNgTags($tagName)
	{
		$tags = $tagName;
		if (!is_array($tagName)) {
			$tags = array($tagName);
		}
		foreach ($tags as $tag) {
			if (!in_array($tag, self::$_returnInnerNgTags)) {
				self::$_returnInnerNgTags[] = $tag;
			}
		}
	}
	
	/**
	 * Get the HTML of this element's inner
	 * 
	 * @params int $level
	 * @return string
	 */
	public function getInnerHtml($level = 0)
	{
		$returnInner = false;
		if (is_bool($this->_isReturnInner)) {
			$returnInner = $this->_isReturnInner;
		}		
		if ($returnInner == false) {
			$level = false;
		} else {
			if (is_numeric($level)) {
				$level++;
			}
		}
		$lines = array();
		if (is_array($this->_nodes)) {
			foreach ($this->_nodes as $elem) {
				if ($elem instanceof HtmlElement) {
					$lines[] = $elem->_getHtml($level);
				} else {
					$lines[] = $this->_line($elem, $level);
				}
			}
		}
		$returnCode = '';
		if ($returnInner == true && is_numeric($level))  {
			$returnCode = "\n";
		}
		$html = implode($returnCode, $lines);
		return $html;
	}
	
	/**y
	 * Build and get HTML
	 * 
	 * @params int $level
	 * @return string
	 */
	public function getHtml($level = 0)
	{
		return $this->_getHtml($level) . "\n";
	}
	
	/**y
	 * Build and get HTML
	 * 
	 * @params int $level
	 * @return string
	 */
	protected function _getHtml($level = 0)
	{
		$returnInner = false;
		if (is_bool($this->_isReturnInner)) {
			$returnInner = $this->_isReturnInner;
		}
		// get attributes
		$attrs = $this->_defaultAttributes;
		foreach ($this->_attributes as $key => $value) {
			$attrs[$key] = $value;
		}
		
		$style = $this->getStyle();
		if ($style != '') {
			$attrs['style'] = $style;
		}
		$class = $this->getClass();
		if ($class != '') {
			$attrs['class'] = $class;
		}
		
		$lines = array();
				
		// start tag
		$line  = '<';
		$line .= $this->_tagName;
		if (isset($attrs['id'])) {
			$line .= sprintf(' id="%s"', $attrs['id']);
			unset($attrs['id']);
			$attrs = array_merge($attrs);
		}
		if (isset($attrs['class'])) {
			$line .= sprintf(' class="%s"', $attrs['class']);
			unset($attrs['class']);
		}
		if (is_array($attrs)) {
			foreach ($attrs as $key => $val) {
				if (is_array($val) || is_object($val)) {
					$type = gettype($val);
					if ($type == 'object') {
						$type = 'instance of ' . get_class($val);
					}
					throw new Exception('Attribute value must be string, but ' . $type . ' given to "' . $key .  '".');
				}
				$line .= sprintf(' %s="%s"', $key, $val);
			}
		}
		if ($this->_isEmptyTag == true) {
			$line .= ' />';
			$lines[] = $this->_line($line, $level);
		} else {
			$line .= '>';
			$lines[] = $this->_line($line, $level);
			// child nodes
			if (is_array($this->_nodes) && count($this->_nodes) > 0) {
				$line = $this->getInnerHtml($level);
				$lines[] = $line;
			}			
			// end tag
			$line = '</' . $this->_tagName . '>';
			if ($returnInner == false) {
				$lines[] = $line;
			} else {
				$lines[] = $this->_line($line, $level);
			}
		}
		$returnCode = '';
		if ($returnInner == true && is_numeric($level))  {
			$returnCode = "\n";
		}
		$html = implode($returnCode, $lines);
		return $html;
	}
	
	/**
	 * Create tag line, add indent according to a level to head of line 
	 * 
	 * @param string $line
	 * @param int $level
	 * @return string
	 */
	private function _line($line, $level)
	{
		$ret = $line;
		if (is_numeric($level)) {
			$ret = str_repeat(self::$_indent, $level) . $line;
		}
		return $ret;
	}
	
	/**
	 * Build HTML and echo it
	 * 
	 * @return void
	 */
	public function render()
	{
		echo $this->getHtml();
	}
	
	/**
	 * Create and add child elements by tag settings array
	 * 
	 * @param array $tagSettings
	 * @return void
	 */
	public function addFromArray($tagSettings)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $tagSettings);
		
		foreach ($tagSettings as $node) {
			if (is_array($node)) {
				$subElem = self::_createElement($node);
				$this->addElement($subElem);
			} else {
				$this->addNode($node);
			}
		}
		return $this;
	}
	
	/**
	 * Create element and child elements by tag settings array
	 * 
	 * @param array $tagSetting
	 * @return HtmlElement
	 */
	public static function fromArray($tagSetting)
	{
		$elem = self::_createElement($tagSetting);
		return $elem;
	}
	
	/**
	 * Create element by tag setting array
	 * 
	 * @param array $setting
	 * @return HtmlElement
	 */
	protected static function _createElement($setting)
	{		
		if (!isset($setting['tag_name'])) {
			throw new Exception('Specification of "tag_name" is required, but not specified.');
		}
		$elem = new HtmlElement($setting['tag_name']);
		if (isset($setting['attributes'])) {
			if (!is_array($setting['attributes'])) {
				throw new Exception('Specification of "attributes" in element setting must be an array, ' . gettype($setting['attributes']) . ' given.');
			}
			$elem->setAttributes($setting['attributes']);
		}
		if (isset($setting['is_empty'])) {
			$elem->setIsEmptyTag($setting['is_empty']);
		}
		if (isset($setting['nodes'])) {
			if ($elem->isEmptyTag()) {
				throw new Exception('Empty tag cannot contain node.');
			}
			if (!is_array($setting['nodes'])) {
				throw new Exception('Specification of "nodes" in element setting must be an array, ' . gettype($setting['nodes']) . ' given.');
			}
			foreach ($setting['nodes'] as $node) {
				if (is_array($node)) {
					$subElem = self::_createElement($node);
					$elem->addElement($subElem);
				} else {
					$elem->addText($node);
				}
			}
		}
		if (isset($setting['text'])) {
			if ($elem->isEmptyTag()) {
				throw new Exception('Empty tag cannot contain node.');
			}
			if (is_array($setting['text']) || is_object($setting['text'])) {
				throw new Exception('Specification of "text" in element setting must be string, ' . gettype($setting['text']) . ' given.');
			}
			$elem->setText($setting['text']);
		}
		return $elem;
	}
	
}
