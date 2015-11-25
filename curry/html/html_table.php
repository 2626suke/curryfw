<?php

/**
 * @see HtmlTableRow
 */
require_once 'html/html_table_row.php';

/**
 * HtmlTable
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
class HtmlTable extends HtmlElement implements IteratorAggregate
{
	/**
	 * Tag name of the element
	 * 
	 * @var string
	 */
	protected $_tagName = 'table';
	
	/**
	 * Element of header container
	 * 
	 * @var HtmlElement 
	 */
	protected $_thead;
	
	/**
	 * Element of body container
	 * 
	 * @var HtmlElement 
	 */
	protected $_tbody;
	
	/**
	 * Default attributes of the element of columns
	 * 
	 * @var array 
	 */
	protected $_defaultColumnsAttributes = array();
	
	/**
	 * Get iterator object of body row elements
	 * 
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
        return new ArrayIterator($this->_tbody->getElements());
	}
	
	/**
	 * Set default attributes of the elements of column
	 * 
	 * @param array $columnsAttributes
	 * @return HtmlTable
	 */
	public function setColumnsAttributes($columnsAttributes)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $columnsAttributes);
		
		foreach ($columnsAttributes as $columnKey => $attributes) {
			if (is_array($attributes)) {
				$this->setColumnAttributes($columnKey, $attributes);
			}
		}
		return $this;
	}
	
	/**
	 * Set default attributes of the element of a column
	 * 
	 * @param string $columnKey
	 * @param array $attributes
	 * @return HtmlTable
	 */
	public function setColumnAttributes($columnKey, $attributes)
	{
		$this->_checkArgumentIsArray(__METHOD__, 2, $attributes);
		
		foreach ($attributes as $name => $val) {
			$this->_defaultColumnsAttributes[$columnKey][$name] = $val;
		}
		return $this;
	}
	
	/**
	 * Get default attributes of the element of a column
	 * 
	 * @param strin $columnKey
	 * @return array
	 */
	public function getColumnAttributes($columnKey)
	{
		$ret = null;
		if (isset($this->_defaultColumnsAttributes[$columnKey])) {
			$ret = $this->_defaultColumnsAttributes[$columnKey];
		}
		return $ret;
	}
	
	/**
	 * Add body row element by specifying cells value, and return created row element
	 * 
	 * @param array $values
	 * @return HtmlTableRow
	 */
	public function addRow($values)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $values);
		
		$row = $this->createRow($values);
		$this->addRowElement($row);
		return $row;
	}
	
	/**
	 * Create and return body row element by specifying cells value
	 * 
	 * @param array $values
	 * @param bool $isHeader
	 * @return HtmlTableRow
	 */
	public function createRow($values = array(), $isHeader = false)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $values);
		
		$row = new HtmlTableRow($this);
		if ($isHeader == true) {
			$row->setCellTagName('th');
		}
		foreach ($values as $columnKey => $value) {
			$row->addCell($columnKey, $value);
		}
		return $row;
	}
	
	/**
	 * Add body row element
	 * 
	 * @param HtmlTableRow $row
	 * @return HtmlTable
	 */
	public function addRowElement(HtmlTableRow $row)
	{
		$this->_createRowContainer();
		$this->_tbody->addElement($row);
		return $this;
	}
	
	/**
	 * Add header row element by specifying cells value, and return created row element
	 * 
	 * @param array $values
	 * @return HtmlTableRow
	 */
	public function addHeader($values)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $values);
		
		$row = $this->createHeader($values);
		$this->addHeaderElement($row);
		return $row;
	}
	
	/**
	 * Create and return header row element by specifying cells value
	 * 
	 * @param array $values
	 * @return HtmlTableRow
	 */
	public function createHeader($values = array())
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $values);
		
		return $this->createRow($values, true);
	}	
	
	/**
	 * Add header row element
	 * 
	 * @param HtmlTableRow $row
	 * @return HtmlTable
	 */
	public function addHeaderElement(HtmlTableRow $row)
	{
		$this->_createHeaderContainer();
		$this->_thead->addElement($row);
		return $this;
	}
	
	/**
	 * Get element of body container
	 * 
	 * @return HtmlElement
	 */
	public function getRowsContainer()
	{
		return $this->_tbody;
	}
	
	/**
	 * Get element of header container
	 * 
	 * @return HtmlElement
	 */
	public function getHeaderContainer()
	{
		return $this->_thead;
	}
	
	/**
	 * Get element of body row
	 * 
	 * @return HtmlTableRow
	 */
	public function getRow($rowIndex)
	{
		$ret = null;
		if ($this->_tbody instanceof HtmlElement) {
			$ret = $this->_tbody->getElement($rowIndex);
		}
		return $ret;
	}
	
	/**
	 * Get all elements of body rows
	 * 
	 * @return array
	 */
	public function getRows()
	{
		$ret = null;
		if ($this->_tbody instanceof HtmlElement) {
			$ret = $this->_tbody->getElements();
		}
		return $ret;
	}
	
	/**
	 * Get element of header row
	 * 
	 * @param int $rowIndex
	 * @return HtmlTableRow
	 */
	public function getHeader($rowIndex)
	{
		$ret = null;
		if ($this->_thead instanceof HtmlElement) {
			$ret = $this->_thead->getElement($rowIndex);
		}
		return $ret;
	}
	
	/**
	 * Get all elements of header rows
	 * 
	 * @return array
	 */
	public function getHeaders()
	{
		$ret = null;
		if ($this->_thead instanceof HtmlElement) {
			$ret = $this->_thead->getElements();
		}
		return $ret;
	}
	
	/**
	 * Create elements of rows and columns element by two dimensional array
	 * 
	 * @param array $rows
	 * @return HtmlTable
	 */
	public function bindArray($rows)
	{
		$this->_checkArgumentIsArray(__METHOD__, 1, $rows);
		
		$this->_tbody = new HtmlElement('tbody');
		$this->addElement($this->_tbody);		
		foreach ($rows as $row) {
			$this->addRow($row);
		}
		return $this;
	}

	/**
	 * Create the element of body container if it does not exist 
	 * 
	 * @return void
	 */
	protected function _createRowContainer()
	{
		if ($this->_tbody == null) {
			$this->_tbody = new HtmlElement('tbody');
			$this->addElement($this->_tbody);			
		}		
	}
	
	/**
	 * Create the element of header container if it does not exist 
	 * 
	 * @return void
	 */
	protected function _createHeaderContainer()
	{
		if ($this->_thead == null) {
			$this->_thead = new HtmlElement('thead');
			$this->addElement($this->_thead);
		}		
	}
	
}
