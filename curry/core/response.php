<?php

/**
 * Response
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    core
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Response extends CurryClass
{
	/**
	 * http status code of response
	 *
	 * @var int
	 */
	protected $_httpStatus = 200;
	
	/**
	 * headers for send
	 *
	 * @var array
	 */
	protected $_headers = array(); 
		
	/**
	 * response text
	 *
	 * @var string
	 */
	protected $_response = '';
	
	/**
	 * whether the response has already been output
	 *
	 * @var boolean
	 */
	protected $_isOutput = false;
	
	/**
	 * Set http status code of response
	 *
	 * @param int $status
	 * @return void
	 */	
	public function setHttpStatus($status = 200)
	{
		$this->_httpStatus = $status;
	}
	
	/**
	 * Add http response header for send
	 *
	 * @param string $header
	 * @return void
	 */	
	public function addHeader($header)
	{
		if ($header != '') {
			$this->_headers[] = trim($header);
		}
	}

	/**
	 * Add http response header for send
	 *
	 * @param array $headers
	 * @return void
	 */	
	public function addHeaders(array $headers)
	{
		foreach ($headers as $header) {
			if ($header != '') {
				$this->_headers[] = trim($header);
			}
		}
	}
	
	/**
	 * Send http response header now
	 *
	 * @param string $header
	 * @return void
	 */	
	public function sendHeader($header)
	{
		if (is_string($header) && $header != '') {
			header($header);
		}
	}
	
	/**
	 * Send http response headers now
	 *
	 * @param array $headers
	 * @return void
	 */	
	public function sendHeaders(array $headers)
	{
		foreach ($headers as $header) {
			$this->sendHeader($header);
		}
	}

	/**
	 * Send http response status code now
	 *
	 * @param int $status
	 * @return void
	 */	
	public function sendHttpStatus($status = null)
	{
		if ($status == null) {
			$status = $this->_httpStatus;
		}
		$serverProtocol = 'HTTP/1.1 ';
		if (isset($_SERVER['SERVER_PROTOCOL'])) {
			$serverProtocol = $_SERVER['SERVER_PROTOCOL'];
		}
		header($serverProtocol . ' ' . $status);
	}
	
	/**
	 * Send http status code and response headers now
	 *
	 * @return void
	 */	
	public function send()
	{
		if ($this->_httpStatus != 200) {
			$this->sendHttpStatus($this->_httpStatus);
		}
		if (is_array($this->_headers)) {
			$this->sendHeaders($this->_headers);
		}
		$this->output();
	}
		
	/**
	 * Redirect to other url
	 *
	 * @param string $url
	 * @param boolean $exit
	 * @return void
	 */	
	public function redirect($url, $exit = true)
	{
		header('Location: ' . $url);
		if ($exit) {
			exit;
		}
	}
	
	/**
	 * Download file
	 *
	 * @param string $filePath Path of source file
	 * @param string $downloadName Name of downloaded file
	 * @param boolean $exit
	 * @return boolean
	 */	
	public function download($filePath, $downloadName, $exit = true)
	{
		if (!file_exists($filePath)) {
			return false;
		}
		$this->sendHeaders(array(
			'Content-Disposition: inline; filename="' . $downloadName . '"',
			'Content-Length: ' . filesize($filePath),
			'Content-Type: application/octet-stream'
		));
		readfile($filePath);
		if ($exit) {
			exit;
		}
		return true;
	}
	
	/**
	 * Set response as plain text
	 *
	 * @param string $plainText
	 * @return void
	 */	
	public function plain($plainText)
	{
		$this->_response = $plainText;
	}
	
	/**
	 * Set response as json text from array
	 *
	 * @param array $array
	 * @return void
	 */	
	public function json($array)
	{
		$text = json_encode($array);
		$this->plain($text);
	}
	
	/**
	 * Set response as xml text from array
	 *
	 * @param array $array
	 * @return void
	 */	
	public function xml($array)
	{
		$doc = new DOMDocument();
		$root = $doc->createElement('response');
		$this->_createElementRecursive($doc, $root, $array);
		$doc->appendChild($root);
		$xml = $doc->saveXML();
		$this->plain($xml);
	}
	
	/**
	 * Create and append xml element recusive called by method "xml"
	 *
	 * @param DOMDocument $doc
	 * @param DOMElement $parentNode
	 * @param mixed $data
	 * @return void
	 */	
	private function _createElementRecursive($doc, $parentNode, $data)
	{
		foreach ($data as $key => $val) {
			$elem = $doc->createElement($key);
			if (is_array($val)) {
				$this->_createElementRecursive($doc, $elem, $val);
			} else {
				$elem->nodeValue = $val;
			}
			$parentNode->appendChild($elem);
		}
	}	
	
	/**
	 * Clear response
	 *
	 * @return void
	 */	
	public function clear()
	{
		$this->_response = '';
	}
	
	/**
	 * Output response
	 *
	 * @return void
	 */	
	public function output()
	{
		if ($this->_response == '') {
			return;
		}
		echo $this->_response;
		$this->_isOutput = true;
	}
	
	/**
	 * Get whether the response has already been output
	 *
	 * @return boolean
	 */	
	public function isOutput()
	{
		return $this->_isOutput;
	}
	
}