<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * BasicAuth
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
class BasicAuth extends CurryClass
{
	/**
	 * user id that is inputed by user
	 *
	 * @var string
	 */
	protected $_inputedUserId;
	
	/**
	 * password that is inputed by user
	 *
	 * @var string
	 */
	protected $_inputedPassword;
	
	/**
	 * Wheater retry or not when authorization is failed
	 *
	 * @var boolean
	 */
	protected $_retry = true;
	
	/**
	 * Messages
	 *
	 * @var array
	 */
	protected $_messages = array(
		'prompt' => 'Please Enter Your Password',
		'denied' => 'Access Denied.',
		'required' => 'Authorization Required.'
	);
	
	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$request = new Request();
		$this->_inputedUserId = $request->getServer('PHP_AUTH_USER');
		$this->_inputedPassword = $request->getServer('PHP_AUTH_PW');
	}

	/**
	 * Set the message displayed at the time of cancellation 
	 *
	 * @param string $message
	 * @return void
	 */
	public function setRequiedMessage($message)
	{
		$this->_messages['required'] = $message;
	}
	
	/**
	 * Set the message displayed at the time of failure 
	 *
	 * @param string $message
	 * @return void
	 */
	public function setDeniedMessage($message)
	{
		$this->_messages['denied'] = $message;
	}
	
	/**
	 * Set the message to which an input is urged 
	 *
	 * @param string $message
	 * @return void
	 */
	public function setPromptMessage($message)
	{
		$this->_messages['prompt'] = $message;
	}
	
	/**
	 * Set the all messages
	 *
	 * @param array $messages
	 * @return void
	 */
	public function setMessages($messages)
	{
		if (!is_array($messages) && !($messages instanceof ArrayObject)) {
			throw new ArgumentInvalidException(__METHOD__, $messages, 1, 'array or ArrayObject');
		}
		foreach ($messages as $key => $message) {
			if (array_key_exists($key, $this->_messages)) {
				$this->_messages[$key] = $message;
			}
		}
	}
	
	/**
	 * Set wheater retry or not when authorization is failed
	 *
	 * @param boolean $enabled
	 * @return void
	 */
	public function setRetryEnabled($enabled)
	{
		$this->_retry = $enabled;
	}
	
	/**
	 * Execute authorization by plain password
	 *
	 * @param string $userId
	 * @param string $password
	 * @return boolean
	 */
	public function plain($userId, $password)
	{
		if ($this->_inputedUserId == null) {
			self::_showInput();
		}
		if ($userId != $this->_inputedUserId || $password != $this->_inputedPassword) {
			self::_failed();
		}
		return true;
	}
	
	/**
	 * Execute authorization by crypted password
	 *
	 * @param string $userId
	 * @param string $password
	 * @return boolean
	 */
	public function crypt($userId, $password)
	{
		if ($this->_inputedUserId == null) {
			self::_showInput();
		}		
		if ($userId != $this->_inputedUserId) {
			self::_failed();
		}
		
		$crypter = Loader::getInstance('Crypter', 'utility');
		$match = $crypter->compare($this->_inputedPassword, $password);
		if ($match == false) {
			self::_failed();
		}
		return true;
	}	
	
	/**
	 * Execute authorization by the authorization file "auth.ini"
	 *
	 * @param boolean $crypt
	 * @return boolean
	 */
	public function file($crypt = false)
	{
		if ($this->_inputedUserId == null) {
			self::_showInput();
		}
		
		$conf = Ini::load('auth.ini', 'basic_auth');
		if ($conf === false) {
			self::_deny();
		}
		foreach ($conf as $userId => $password) {
			$res = false;
			if ($crypt) {
				$res = $this->crypt($userId, $password);
			} else {
				$res = $this->plain($userId, $password);
			}
			if ($res === true) {
				break;
			}
		}
		return true;
	}
	
	/**
	 * Process when authorization failed
	 *
	 * @return void
	 */
	protected function _failed()
	{
		if (true == $this->_retry) { 
			self::_showInput();
		} else {
			self::_deny();
		}		
	}

	/**
	 * Display the authorization dialog
	 *
	 * @return void
	 */
	protected function _showInput()
	{
		$response = new Response();
		$response->addHeader('WWW-Authenticate: Basic realm="' . $this->_messages['prompt'] . '"');
		$response->addHeader('HTTP/1.0 401 Unauthorized');
		$response->send();
		echo $this->_messages['required'];
		exit;		
	}
	
	/**
	 * Output dinied message
	 *
	 * @return void
	 */
	protected function _deny()
	{
		echo $this->_messages['denied'];
		exit;
	}
	
	
}