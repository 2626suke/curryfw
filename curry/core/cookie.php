<?php

class Cookie extends CurryClass
{
	private $_expire = 0;
	private $_path = null;
	
	public function setExpire($timestamp)
	{
		$this->_expire = $timestamp;
	}
	
	public function setExpireDays($days)
	{
		$this->_expire = $days * 24 * 60 * 60 + time();
	}
	
	public function setPath($path)
	{
		$this->_path = $path;
	}
	
	public function set($key, $value)
	{
		setcookie($key, $value, $this->_expire, $this->_path);
	}
	
	public function get($key)
	{
		return $_COOKIE[$key];
	}
	
	public function remove($key)
	{
		setcookie($key, '', time() - 3600, $this->_path);		
	}
	
}