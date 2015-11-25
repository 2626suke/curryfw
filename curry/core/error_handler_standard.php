<?php

/**
 * ErrorHandlerStandard
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
class ErrorHandlerStandard extends CurryClass
{
	/**
	 * Instance of Request.
	 * 
	 * @var Request
	 */
	protected $request;
	
	/**
	 * Instance of Response.
	 * 
	 * @var Response
	 */
	protected $response;
	
	/**
	 * Instance of View.
	 * 
	 * @var ViewStandard or ViewSmarty
	 */
	protected $view;
	
	/**
	 * Instance of Exception.
	 * 
	 * @var Exception or an inherited it
	 */
	protected $exception;
	
	/**
	 * Log key.
	 * 
	 * @var string
	 */
	protected static $_logKey = 'default';
	
	/**
	 * Constructor
	 *
	 * @var Exception $exception
	 */
	public function __construct($exception = null)
	{
		if ($exception instanceof Exception) {
			$this->setException($exception);
		}
	}
		
	/**
	 * Process when basic exception occurs 
	 *
	 * @return void
	 */
	public function error()
	{
		if ($this->view instanceof ViewAbstract) {
			$dir = PathManager::getViewTemplateDirectory();
			$ext = NameManager::getTemplateExtension();
			$templateName = $this->view->getErrorTemplate();
			$exists = file_exists(sprintf('%s/error/%s.%s', $dir, $templateName, $ext));
			if ($exists == false) {
				$templateName = 'error';
				$exists = file_exists(sprintf('%s/error/%s.%s', $dir, $templateName, $ext));
			}
			if ($exists) {
				$this->view->setTemplate($templateName, 'error');
				$this->view->file = $this->exception->getFile();
				$this->view->line = $this->exception->getLine();
				$this->view->message = $this->exception->getMessage();
				$this->view->trace = $this->exception->getTraceAsString();
				$this->view->render();
				return;
			}
		}
		$message = sprintf('%s(%s)<br />%s<br /><br />%s',
			$this->exception->getFile(),
			$this->exception->getLine(),
			$this->exception->getMessage(),
			nl2br($this->exception->getTraceAsString())
		);
		echo $message;
	}
	
	/**
	 * Process when not found exception occurs 
	 *
	 * @return void
	 */
	public function notFound()
	{
		$dir = PathManager::getViewTemplateDirectory();
		$ext = NameManager::getTemplateExtension();
		$exists = file_exists(sprintf('%s/error/not_found.%s', $dir, $ext));
		if ($this->view instanceof ViewAbstract && $exists) {
			$this->view->setTemplate('not_found', 'error');
			$this->view->enableLayout(false);
			$this->view->render();
		} else {
			echo '404 Not Found';
		}
	}
	
	/**
	 * Process when PDO exception occurs 
	 *
	 * @return void
	 */
	public function pdo()
	{
		$this->error();
	}
	
	/**
	 * Redirect to other page of same domain
	 *
	 * @param string $path The Domain removed url path
	 * @return void
	 */
	public function redirect($path)
	{
		$url = $this->request->getBaseUrl() . '/' . ltrim($path, '/');
		$this->response->redirect($url);
	}
	
	/**
	 * Enable logging
	 *
	 * @param string $logKey Log key related to log file path
	 * @return void
	 */
	public static function enableLogging($logKey = 'default')
	{
		self::$_logKey = $logKey;
	}

	/**
	 * Set the request instance
	 *
	 * @param Request $request Request instance
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}
	
	/**
	 * Set the Response instance
	 *
	 * @param Response $response Response instance
	 * @return void
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;
	}
	
	/**
	 * Set the view instance
	 *
	 * @param ViewAbstract $view ViewAbstract extended instnace
	 * @return void
	 */
	public function setView(ViewAbstract $view)
	{
		$this->view = $view;
	}
	
	/**
	 * Set the exception instance
	 *
	 * @param Exception $exception
	 * @return void
	 */
	public function setException($exception)
	{
		$this->exception = $exception;
		if (Loader::classExists('Logger') && self::$_logKey) {
			Logger::except($exception, self::$_logKey);
		}
	}
	
	
}