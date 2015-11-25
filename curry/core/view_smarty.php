<?php

/**
 * @see ViewAbstract
 */
require_once 'core/view_abstract.php';

/**
 * ViewSmarty
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
class ViewSmarty extends ViewAbstract
{
	/**
	 * Path of directory to output compiled template file
	 *
	 * @var string
	 */
	protected $_compileDir;
	
	/**
	 * Path of directory to output template cache file
	 *
	 * @var string
	 */
	protected $_cacheDir;
	
	/**
	 * Smarty instance
	 *
	 * @var Smarty
	 */
	protected $_smarty;
	
	/**
	 * Execute initial process,
	 *
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();
		
		$this->_smarty = new Smarty();
		$this->_smarty->error_reporting = E_ALL & ~E_NOTICE;
		NameManager::setTemplateExtension('tpl');
		$root = PathManager::getViewDirectory();
		$this->setCompileDirectory($root . '/compile');
		$this->setCacheDirectory($root . '/cache');		
	}

	/**
	 * Clear all assigned values
	 *
	 * @return void
	 */
	public function clearValues()
	{
		parent::clearValues();
        $this->_smarty->clearAllAssign();
	}
	
	/**
	 * Get Smarty instance
	 *
	 * @return Smarty
	 */
	public function getSmarty()
	{
		return $this->_smarty;
	}
	
	/**
	 * Set path of directory to output compiled template file
	 * 
	 * @param string $dir
	 * @return void
	 */
	public function setCompileDirectory($dir)
	{
		$this->_smarty->compile_dir = $dir;
	}

	/**
	 * Set path of directory to output template cache file
	 * 
	 * @param string $dir
	 * @return void
	 */
	public function setCacheDirectory($dir)
	{
		$this->_smarty->cache_dir = $dir;
	}
	
	/**
	 * Set whether output smarty error
	 * 
	 * @param string $errorLevel
	 * @return void
	 */
	public function setErrorRepoting($errorLevel)
	{
		$this->_smarty->error_reporting = $errorLevel;
	}
	
	/**
	 * Execute output html without using a layout template
	 *
	 * @return string
	 */
	protected function renderTemplate()
	{
		foreach ($this->_vars as $key => $val) {
			$this->_smarty->assign($key, $val);
		}	
	
		$templateDir = PathManager::getViewTemplateDirectory();
		$template = $this->_template . '.' . NameManager::getTemplateExtension();
		
		$this->_smarty->template_dir = $templateDir;
		$templatePath = $templateDir . '/' . $template;
		if (!file_exists($templatePath)) {
			throw new FileNotExistException($templatePath);
		}
		$rendered = $this->_smarty->fetch($template);
		return $rendered;
	}
	
	/**
	 * Execute output html using a layout template
	 *
	 * @return string
	 */
	protected function renderTemplateWithLayout()
	{		
		foreach ($this->_vars as $key => $val) {
			$this->_smarty->assign($key, $val);
		}
		
		$ext = NameManager::getTemplateExtension();
		
		$templateDir = PathManager::getViewTemplateDirectory();
		$template = $this->_template . '.' . $ext;
		$templatePath = $templateDir . '/' . $template;
		if (!file_exists($templatePath)) {
			throw new FileNotExistException($templatePath);
		}		
		
		$layoutDir = PathManager::getViewLayoutDirectory();
		$layout = $this->_layout . '.' . $ext;
		$layoutPath = $layoutDir . '/' . $layout;
		if (!file_exists($layoutPath)) {
			throw new FileNotExistException($layoutPath);
		}
		
		$this->_smarty->template_dir = $templateDir;		
		$contents = $this->_smarty->fetch($template);
		$this->_smarty->assign('inner_contents', $contents);
		
		$this->_smarty->template_dir = $layoutDir;
		$rendered = $this->_smarty->fetch($layout);
		return $rendered;
	}
		
}