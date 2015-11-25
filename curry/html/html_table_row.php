<?php

/**
 * @see HtmlElement
 */
require_once 'html/html_element.php';

/**
 * HtmlTableRow
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
class HtmlTableRow extends HtmlElement implements IteratorAggregate, ArrayAccess 
{
	/**
	 * Instance of element of the parent table
	 *
	 * @var HtmlTable 
	 */
	protected $_parentTable;
	
	/**
	 * Tag name of the element
	 * 
	 * @var string
	 */
	protected $_tagName = 'tr';
	
	/**
	 * Tag name of the element of cell
	 * 
	 * @var string
	 */
	protected $_cellTagName = 'td';
	
	/**
	 * Elements of cells
	 * 
	 * @var array 
	 */
	protected $_cells = array();
	
	/**
	 * constructor
	 * 
	 * @param HtmlTable $parentTable
	 * @return void
	 */
	public function __construct($parentTable = null)
	{
		if ($parentTable !== null && !($parentTable instanceof HtmlTable)) {
			throw new ArgumentInvalidException(__METHOD__, $parentTable, 1, 'HtmlTable instance');
		}
		if ($parentTable !== null) {
			$this->setParentTable($parentTable);
		}
	}
	
	/**
	 * Overrides magic method "__set".
	 * Calls method "setCellValue".
	 * 
	 * @param string $name
	 * @param string $value
	 * @return HtmlTableRow
	 */
	public function __set($name, $value)
	{
		return $this->setCellValue($name, $value);
	}
	
	/**
	 * Overrides magic method "__get".
	 * Calls method "getCellValue".
	 * 
	 * @param string $name
	 * @return string
	 */
	public function __get($name)
	{
		return $this->getCellValue($name);
	}
	
	/**
	 * Overrides parent method.
	 * Calls method "setCellValue".
	 * 
	 * @param string|int $offset
	 * @param string $value
	 * @return HtmlTableRow
	 */
    public function offsetSet($offset, $value)
	{
		return $this->setCellValue($offset, $value);
    }
	
	/**
	 * Check whether specified offset exists in cells
	 * 
	 * @param string|int $offset
	 * @return boolean
	 */
    public function offsetExists($offset)
	{
        return $this->existsCell($offset);
    }
	
	/**
	 * Unset cell if exists specified offset
	 * 
	 * @param string|int $offset
	 * @return HtmlTableRow
	 */
    public function offsetUnset($offset)
	{
        return $this->removeCell($offset);
    }
	
	/**
	 * Get element of cell if exists specified offset
	 * 
	 * @param string|int $offset
	 * @return HtmlElement
	 */
    public function offsetGet($offset)
	{
        return $this->getCell($offset);
    }
	
	/**
	 * Get iterator object of cell elements
	 * 
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
        return new ArrayIterator($this->_cells);
	}
	
	/**
	 * Set instance of element of the parent table
	 * 
	 * @param HtmlElement $parentTable
	 * @return HtmlTableRow
	 */
	public function setParentTable(HtmlElement $parentTable)
	{
		$this->_parentTable = $parentTable;
		return $this;
	}
	
	/**
	 * Set tag name of the element of cell
	 * 
	 * @param string $cellTagName
	 * @return HtmlTableRow
	 */
	public function setCellTagName($cellTagName)
	{
		$this->_cellTagName = $cellTagName;
		return $this;
	}
	
	/**
	 * Set cell value by specifying column key and value
	 * 
	 * @param string $columnKey
	 * @param string|HtmlElement $value
	 * @return HtmlTableRow
	 */
	public function setCellValue($columnKey, $value)
	{
		$cell = null;
		if ($this->existsCell($columnKey)) {
			$cell = $this->getCell($columnKey);
		} else {
			$cell = $this->addCell($columnKey);
		}
		$cell->setText($value);
		
		return  $this;
	}
	
	/**
	 * Add cell by specifying column key and value
	 * 
	 * @param string $columnKey
	 * @param string|HtmlElement $value
	 * @return HtmlElement
	 * @throws Exception
	 */
	public function addCell($columnKey, $value = null)
	{
		if ($this->existsCell($columnKey)) {
			throw new Exception('cell "' . $columnKey . '" already exists.');
		}
		$cell = new HtmlElement($this->_cellTagName);
		$cell->setIsReturnInner(false);		
		$attrs = $this->_parentTable->getColumnAttributes($columnKey);
		if ($attrs) {
			$cell->setAttributes($attrs);
		}
		if ($value !== null) {
			$nodes = $value;
			if (!is_array($value)) {
				$nodes = array($value);
			}
			$cell->addNodes($nodes);
		}
		$this->_addCellElement($columnKey, $cell);
		return $cell;
	}
	
	/**
	 * Get element of the cell by specifying column key
	 * 
	 * @param string $columnKey
	 * @return HtmlElement
	 */
	public function getCell($columnKey)
	{
		$cell = null;
		if ($this->existsCell($columnKey)) {
			$cell = $this->_cells[$columnKey];
		}
		return $cell;
	}
		
	/**
	 * Get cell value as string
	 * 
	 * @param string $columnKey
	 * @return string
	 */
	public function getCellValue($columnKey)
	{
		$ret = null;
		$td = $this->getCell($columnKey);
		if ($td instanceof HtmlElement) {
			$ret = $td->getInnerHtml();
		}
		return $ret;
	}
	
	/**
	 * Check whether exists cell by specifying column key
	 * 
	 * @param string $columnKey
	 * @return boolean
	 */
	public function existsCell($columnKey)
	{
		return isset($this->_cells[$columnKey]);
	}
	
	/**
	 * Remove cell element by specifying column key
	 * 
	 * @param string $columnKey
	 * @return HtmlTableRow
	 */
	public function removeCell($columnKey)
	{
		if (isset($this->_cells[$columnKey])) {
			unset($this->_cells[$columnKey]);
		}
		return  $this;		
	}
	
	/**
	 * Set attributes of cell elements
	 * 
	 * @param array $cellsAttributes
	 * @return HtmlTableRow
	 */
	public function setCellsAttributes($cellsAttributes)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $cellsAttributes);
		
		foreach ($cellsAttributes as $columnKey => $attributes) {
			$this->setCellAttributes($columnKey, $attributes);
		}
		return $this;
	}
	
	/**
	 * Set attributes of a cell element
	 * 
	 * @param string $columnKey
	 * @param array $attributes
	 * @return HtmlTableRow
	 */
	public function setCellAttributes($columnKey, $attributes)
	{
		$this->_checkArgumentIsArray(__METHOD__, 2, $attributes);
		
		$td = $this->getCell($columnKey);
		if ($td instanceof HtmlElement) {
			$td->setAttributes($attributes);
		}
		return $this;
	}
	
	/**
	 * Add cell element
	 * 
	 * @param string $columnKey
	 * @param HtmlElement $cellElement
	 */
	protected function _addCellElement($columnKey, HtmlElement $cellElement)
	{
		$this->_cells[$columnKey] = $cellElement;
		$this->addElement($cellElement);
	}
		
}
