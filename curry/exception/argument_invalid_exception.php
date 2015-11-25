<?php

/**
 * ArgumentInvalidException
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    eception
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ArgumentInvalidException extends Exception
{
	/**
	 * Constructor
	 * 
	 * @param string $method
	 * @param mixed $argument
	 * @param int $argumentNo
	 * @param string $needed
	 * @return void
	 */
	public function __construct($method, $argument, $argumentNo, $needed = null)
	{
		$neededMsg = '';
		if ($needed != null) {
			$neededMsg = ' must be an ' . $needed;
		}
		$givenType = gettype($argument);
		if ($givenType == 'object') {
			$givenType = get_class($argument);
		}
		parent::__construct(sprintf('Argument %s passed to %s%s, %s given.', $argumentNo, $method, $neededMsg, $givenType));
	}
	
}