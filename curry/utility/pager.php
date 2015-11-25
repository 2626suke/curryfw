<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * Pager
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    utility
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Pager extends CurryClass
{
	/**
	 * Instance of class "Request"
	 *
	 * @var Request
	 */	
	protected $_request;
	
	/**
	 * Base url of page
	 *
	 * @var string
	 */
	protected $_url;
		
	/**
	 * current page
	 *
	 * @var int
	 */
	protected $_current = 1;
	
	/**
	 * total data count
	 *
	 * @var int
	 */
	protected $_totalCount = 0;
	
	/**
	 * display count per page 
	 *
	 * @var int
	 */
	protected $_perPage = 10;
	
	/**
	 * Range of the page displayed on page navigation
	 *
	 * @var int
	 */
	protected $_displayRange;
	
	/**
	 * Page number of the top of the range of the page displayed on page navigation 
	 *
	 * @var int
	 */
	protected $_displayTopPage = 1;
	
	/**
	 * Page number of the last of the range of the page displayed on page navigation 
	 *
	 * @var int
	 */
	protected $_displayEndPage;
	
	/**
	 * total page count
	 *
	 * @var int
	 */
	protected $_pageCount;
	
	/**
	 * previous page number
	 *
	 * @var int
	 */
	protected $_previous;
	
	/**
	 * next page number
	 *
	 * @var int
	 */
	protected $_next;
	
	/**
	 * record offset
	 *
	 * @var int
	 */
	protected $_offset;
		
	/**
	 * Information of page navigation
	 *
	 * @var array
	 */
	protected $_pageInfo = array();
	
	/**
	 * The option of a display of page navigation 
	 *
	 * @var array
	 */
	protected $_outputOption = array(
		'frame_class' => 'pager',
		'frame_tag'   => 'div',
		'page_tag'    => 'span',
		'page_class'  => 'page',
		'page_style'  => '',
		'page_format' => '%page%',
		'prev_tag'    => 'span',
		'prev_class'  => 'page',
		'prev_style'  => '',
		'prev_text'   => '&lt;',
		'next_tag'    => 'span',
		'next_class'  => 'page',
		'next_style'  => '',
		'next_text'   => '&gt;',
		'top_tag'     => 'span',
		'top_class'   => 'page',
		'top_style'   => '',
		'top_text'    => '&lt;&lt;',
		'last_tag'    => 'span',
		'last_class'  => 'page',
		'last_style'  => '',
		'last_text'   => '&gt;&gt;',
	);
	
	/**
	 * Set Request instance
	 * 
	 * @param Request $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->_request = $request;
		if ($this->_url == null) {
			$url = $request->getPath() . '/?p=%page%';
			$this->setUrl($url);
		}
		$page = $request->getQuery('p');
		if (is_numeric($page)) {
			$this->setCurrentPage($page);
		}
	}
	
	/**
	 * Set ase url of page
	 *
	 * @param string $url
	 * @return void
	 */	
	public function setUrl($url)
	{
		$this->_url = $url;
	}
		
	/**
	 * Set total data count
	 *
	 * @param int $count
	 * @return void
	 */	
	public function setDataCount($count)
	{
		if (!is_numeric($count) || $count < 0) {
			return;
		}
		$this->_totalCount = $count;
	}
	
	/**
	 * Set display count per page
	 *
	 * @param int $count
	 * @return void
	 */	
	public function setCountPerPage($count)
	{
		if (!is_numeric($count) || $count < 1) {
			return;
		}
		$this->_perPage = $count;
	}	
	
	/**
	 * Set current page number
	 *
	 * @param int $page
	 * @return void
	 */	
	public function setCurrentPage($page)
	{
		if (!is_numeric($page) || $page < 1) {
			return;
		}
		$this->_current = $page;
	}
	
	/**
	 * Set range of the page displayed on page navigation
	 *
	 * @param int $range
	 * @return void
	 */	
	public function setDisplayRange($range)
	{
		if (!is_numeric($range)) {
			return;
		}
		$this->_displayRange = $range;
	}
	
	/**
	 * Set option of a display of page navigation 
	 *
	 * @param array $option
	 * @return void
	 */	
	public function setOutputOption($option)
	{
		if (!is_array($option)) {
			return;
		}
		foreach ($option as $key => $value) {
			if (array_key_exists($key, $this->_outputOption)) {
				$this->_outputOption[$key] = $value;
			}			
		}
	}
	
	/**
	 * Get the information of page navigation
	 *
	 * @return array
	 */	
	public function getPageInfo()
	{
		return $this->_pageInfo;
	}
	
	/**
	 * Get current page number
	 *
	 * @return int
	 */	
	public function getCurrentPage()
	{
		return $this->_current;
	}
	
	/**
	 * Get offset of data
	 *
	 * @return int
	 */	
	public function getDataOffset()
	{		
		$offset = ($this->_current - 1) * $this->_perPage;
		return $offset;
	}
	
	/**
	 * Build page information of page navigation
	 *
	 * @return void
	 */	
	public function paginate()
	{
		// calculate page count
		$this->_pageCount = ceil($this->_totalCount / $this->_perPage);
		if ($this->_pageCount == 0) {
			$this->_pageCount = 1;
		}
		
		// decide previous page
		if ($this->_current > 1) {
			$this->_previous = $this->_current - 1;
		}
		// decide next page
		if ($this->_current < $this->_pageCount) {
			$this->_next = $this->_current + 1;
		}
		
		// decide top and last page of range of display 
		$this->_displayEndPage = $this->_pageCount;
		if (is_numeric($this->_displayRange)) {
			$topPage = $this->_current - $this->_displayRange;
			if ($topPage > 1) {
				$this->_displayTopPage = $topPage;
			}
			$endPage = $this->_current + $this->_displayRange;
			if ($endPage < $this->_pageCount) {
				$this->_displayEndPage = $endPage;
			}
		}
		
		$info = array();
		
		// pages
		$pages = array();		
		for ($i = $this->_displayTopPage; $i <= $this->_displayEndPage; $i++) {
			$tmp = array();
			$tmp['page'] = $i;
			if ($i != $this->_current) {
				$tmp['url'] = str_replace('%page%', $i, $this->_url);
			}
			$pages[] = $tmp;
		}
		$info['pages'] = $pages;
	
		// previous page
		$tmp = array();
		if ($this->_current > 1) {
			$tmp['url'] = str_replace('%page%', $this->_previous, $this->_url);
		}
		$info['prev'] = $tmp;
		
		// next page
		$tmp = array();
		if ($this->_current < $this->_pageCount) {
			$tmp['url'] = str_replace('%page%', $this->_next, $this->_url);
		}
		$info['next'] = $tmp;
		
		// previous page
		$tmp = array();
		if ($this->_current > 1) {
			$tmp['url'] = str_replace('%page%', 1, $this->_url);
		}
		$info['top'] = $tmp;
		
		// next page
		$tmp = array();
		if ($this->_current < $this->_pageCount) {
			$tmp['url'] = str_replace('%page%', $this->_pageCount, $this->_url);
		}
		$info['last'] = $tmp;
		
		$this->_pageInfo = $info;
	}

	/**
	 * Get the HTML tag of page navigation
	 *
	 * @return string
	 */	
	public function getTag()
	{
		$tags = array();
		
		if ($this->_outputOption['top_text'] != '') {
			$page = $this->_pageInfo['top'];
			$url = '';
			if (isset($page['url']) && $page['url'] != '') {
				$url = $page['url'];
			}
			$tag = $this->_createTag(
				$this->_outputOption['top_tag'],
				$this->_outputOption['top_class'],
				$this->_outputOption['top_style'],
				$this->_outputOption['top_text'],
				$url
			);
			$tags[] = $tag;
		}
		if ($this->_outputOption['prev_text'] != '') {
			$page = $this->_pageInfo['prev'];
			$url = '';
			if (isset($page['url']) && $page['url'] != '') {
				$url = $page['url'];
			}
			$tag = $this->_createTag(
				$this->_outputOption['prev_tag'],
				$this->_outputOption['prev_class'],
				$this->_outputOption['prev_style'],
				$this->_outputOption['prev_text'],
				$url
			);
			$tags[] = $tag;
		}
		foreach ($this->_pageInfo['pages'] as $page) {
			$url = '';
			if (isset($page['url']) && $page['url'] != '') {
				$url = $page['url'];
			}
			$tag = $this->_createTag(
				$this->_outputOption['page_tag'],
				$this->_outputOption['page_class'],
				$this->_outputOption['page_style'],
				str_replace('%page%', $page['page'], $this->_outputOption['page_format']),
				$url
			);
			$tags[] = $tag;
		}
		if ($this->_outputOption['next_text'] != '') {
			$page = $this->_pageInfo['next'];
			$url = '';
			if (isset($page['url']) && $page['url'] != '') {
				$url = $page['url'];
			}
			$tag = $this->_createTag(
				$this->_outputOption['next_tag'],
				$this->_outputOption['next_class'],
				$this->_outputOption['next_style'],
				$this->_outputOption['next_text'],
				$url
			);
			$tags[] = $tag;
		}
		if ($this->_outputOption['last_text'] != '') {
			$page = $this->_pageInfo['last'];
			$url = '';
			if (isset($page['url']) && $page['url'] != '') {
				$url = $page['url'];
			}
			$tag = $this->_createTag(
				$this->_outputOption['last_tag'],
				$this->_outputOption['last_class'],
				$this->_outputOption['last_style'],
				$this->_outputOption['last_text'],
				$url
			);
			$tags[] = $tag;
		}
				
		$ret  = '<' . $this->_outputOption['frame_tag'];
		if ($this->_outputOption['frame_class'] != '') {
			$ret .= ' class="' . $this->_outputOption['frame_class'] . '"';
		}
		$ret .= '>' . "\n";
		$ret .= implode("\n", $tags) . "\n";
		$ret .= '</' . $this->_outputOption['frame_tag'] . '>' . "\n";
		
		return $ret;
	}
	
	/**
	 * Output the HTML tag of page navigation
	 *
	 * @return void
	 */	
	public function outputTag()
	{
		$tag = $this->getTag();
		echo $tag;
	}
	
	/**
	 * Create the tag of each page of page navigation
	 * 
	 * @param string $tag
	 * @param string $class
	 * @param string $style
	 * @param string $text
	 * @param string $url
	 * @return string
	 */
	private function _createTag($tag, $class, $style, $text, $url)
	{
		$ret = '';
		$ret .= '<' . $tag;
		if ($class != '') {
			$ret .= ' class="' . $class . '"';
		}
		if ($style != '') {
			$ret .= ' style="' . $style . '"';
		}
		$ret .= '>';
		if ($url != '') {
			$ret .= '<a href="' . $url . '">';
		}
		$ret .= $text;
		if ($url != '') {
			$ret .= '</a>';
		}
		$ret .= '</' . $tag . '>';
		return $ret;
	}
}