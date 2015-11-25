<?php

/**
 * @see CurryClass
 */
require_once 'core/curry_class.php';

/**
 * Crypter
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
class Crypter extends CurryClass
{
	/**
	 * Length of salt for encryption 
	 *
	 * @var int
	 */
	protected $_saltLength = 5;
	
	/**
	 * The number of times of encryption by sha1
	 *
	 * @var int
	 */
	protected $_iterationCount = 10;
	
	/**
	 * Set iteration count when auth failed
	 * 
	 * @param int $count
	 */
	public function setIterationCount($count)
	{
		$this->_iterationCount = $count;
	}
	
	/**
	 * Set salt length for crypt
	 * 
	 * @param int $length
	 */
	public function setSaltLength($length)
	{
		$this->_saltLength = $length;
	}
	
	/**
	 * Execute encrypt
	 *
	 * @param string $text source text
	 * @return string Crypted text
	 */
	public function encrypt($text)
	{
		$salt = $this->createSalt();
		$crypted = $this->_crypt($text, $salt);		
		return $crypted;
	}
	
	/**
	 * Compare whether two characters that plain and crypted are the same
	 *
	 * @param string $plain Plain text
	 * @param string $compare Crypted text
	 * @return string Crypted text
	 */
	public function compare($plain, $compare)
	{
		$salt = substr($compare, 0, $this->_saltLength);
		$crypted = $this->_crypt($plain, $salt);
		if ($crypted != $compare) {
			return false;
		}
		return true;
	}
		
	/**
	 * Create salt
	 *
	 * @return string Salt text
	 */
	public function createSalt()
	{
		$salt = md5(uniqid(rand(), true));
		$salt = substr($salt, 0, $this->_saltLength);
		return $salt;
	}
	
	/**
	 * Execute encrypt with salt
	 *
	 * @param string $text
	 * @param string $salt
	 * @return string Crypted text
	 */
	protected function _crypt($text, $salt)
	{
		$crypted = $salt . $text;
		for ($i = 0; $i < $this->_iterationCount; $i++) {
			$crypted = sha1($crypted);
		}
		$crypted = $salt . $crypted;

		return $crypted;
	}
	
}