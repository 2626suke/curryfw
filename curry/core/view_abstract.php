<?php

/**
 * ViewAbstract
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
abstract class ViewAbstract extends CurryClass
{
	/**
	 * Request instance, contains request informations
	 *
	 * @var Request
	 */
	protected $_request;
	
	/**
	 * Rendering controller
	 *
	 * @var string
	 */
	protected $_renderingController;
	
	/**
	 * Rendering action
	 *
	 * @var string
	 */
	protected $_renderingAction;
	
	/**
	 * Values assigned for template
	 *
	 * @var array
	 */
	protected $_vars = array();
	
	/**
	 * title of page
	 *
	 * @var string 
	 */
	protected $_title;
	
	/**
	 * values of meta tag 
	 *
	 * @var array 
	 */
	protected $_metas = array();
		
	/**
	 * Name of layout template
	 *
	 * @var string
	 */
	protected $_layout = 'default';
	
	/**
	 * Name of template
	 *
	 * @var string
	 */
	protected $_template;
	
	/**
	 * Default name of error template
	 *
	 * @var string 
	 */
	protected static $_defaultErrorTemplate = 'error';
	
	/**
	 * Name of error template
	 *
	 * @var string
	 */
	protected $_errorTemplate;
	
	/**
	 * Paths of additional Javascript file
	 *
	 * @var array
	 */
	protected $_jsFiles = array();
	
	/**
	 * Buffer of paths of additional preferred Javascript file
	 *
	 * @var array
	 */
	protected $_jsFilesPreferred = array();
	
	/**
	 * Paths of additional css file
	 *
	 * @var array
	 */
	protected $_cssFiles = array();
	
	/**
	 * paths of additional preferred css file
	 *
	 * @var array
	 */
	protected $_cssFilesPreferred = array();
	
	/**
	 * Whether output using a template.
	 * 
	 * @var boolean
	 */
	protected $_templateEnabled = true;
	
	/**
	 * Whether output using template with layout.
	 * 
	 * @var boolean
	 */
	protected $_layoutEnabled = true;
	
	/**
	 * Default setting of whether output using template with layout.
	 * 
	 * @var boolean
	 */
	protected static $_defaultLayoutEnabled = true;
	
	/**
	 * Whether render html.
	 * 
	 * @var boolean
	 */
	protected $_renderingEnabled = true;
	
	/**
	 * Encoding of output template
	 * 
	 * @var string 
	 */
	protected $_outputEncoding;
	
	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->enableLayout(self::$_defaultLayoutEnabled);
		$this->setErrorTemplate(self::$_defaultErrorTemplate);
	}
	
	/**
	 * Overriding parent.
	 * Alias of method "set".
	 *
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public function __set($key, $val)
	{
		$this->set($key, $val);
	}
	
	/**
	 * Overriding parent.
	 * Alias of method "get".
	 *
	 * @param string $key
	 * @return mixed 
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Set value for template
	 *
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public function set($key, $val)
	{
		$this->_vars[$key] = $val;
	}
	
	/**
	 * Get value for template
	 *
	 * @param string $key
	 * @return mixed 
	 */
	public function get($key = null)
	{
		$ret = null;
		if ($key == null) {
			$ret = $this->_vars;
		} else if (isset($this->_vars[$key])) {
			$ret = $this->_vars[$key];
		}		
		return $ret;
	}
		
	/**
	 * Set path of template directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public function setTemplateDirectory($dir)
	{
		PathManager::setViewTemplateDirectory($dir);
	}
			
	/**
	 * Set path of layout template directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public function setLayoutDirectory($dir)
	{
		PathManager::setViewLayoutDirectory($dir);
	}
	
	/**
	 * Set whether render html.
	 * 
	 * @param boolean $enabled
	 * @return void
	 */
	public function enableRendering($enabled)
	{
		$this->_renderingEnabled = $enabled;
	}
	
	/**
	 * Get whether render html.
	 * 
	 * @return boolean
	 */
	public function getRenderingEnabled()
	{
		return $this->_renderingEnabled;  
	}

	/**
	 * Set layout enabled.
	 * 
	 * @param boolean $enabled
	 * @return void
	 */
	public static function setDefaultLayoutEnabled($enabled)
	{
		self::$_defaultLayoutEnabled = $enabled;
	}
	
	/**
	 * Set whether output using a template.
	 * 
	 * @param boolean $enabled
	 * @return void
	 */
	public function enableTemplate($enabled)
	{
		$this->_templateEnabled = $enabled;
	}
	
	/**
	 * Set whether output using template with layout.
	 * 
	 * @param boolean $enabled
	 * @return void
	 */
	public function enableLayout($enabled)
	{
		$this->_layoutEnabled = $enabled;
	}
	
	/**
	 * Set request instance
	 *
	 * @param Request $request Request instance
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->_request = $request;
	}
	
	/**
	 * Set view template use for rendering.
	 *
	 * @param string $action Action name as template file name
	 * @param string $controller Controller name as template directory name
	 * @return void
	 */
	public function setTemplate($action, $controller = null)
	{
		$req = $this->_request;
		if ($controller == null) {
			$controller = $req->getController();
			if ($req->getControllerSubDirectory()) {
				$controller = $req->getControllerSubDirectory() . '/' . $controller;
			}
		}
		$ext = NameManager::getTemplateExtension();
		$action = preg_replace('/\.' . $ext . '$/', '', $action);
		$this->_template = sprintf('%s/%s', $controller, $action);
	}
	
	/**
	 * Execute initial process,
	 *
	 * @return void
	 */
	public function initialize()
	{
		$req = $this->_request;		
		if ($req->isXmlHttp()) {
			$this->enableLayout(false);
		}
		$controller = $req->getController();
		$subdir = $req->getControllerSubDirectory();
		if ($subdir != '') {
			$controller = $subdir . '/' . $controller;
		}
		$this->_renderingController = $controller;
		$this->_renderingAction = $req->getAction();
	}
	
	/**
	 * Set controller for rendering.
	 *
	 * @param string $controller Controller name as template directory name
	 * @return void
	 */
	public function setRenderingController($controller)
	{
		$this->_renderingController = $controller;
	}
	
	/**
	 * Set action for rendering.
	 *
	 * @param string $action Action name as template file name
	 * @return void
	 */
	public function setRenderingAction($action)
	{
		$this->_renderingAction = $action;
	}
	
	/**
	 * Set default view template use for rendering on error.
	 * 
	 * @param string $tamplateName
	 * @return void
	 */
	public static function setDefaultErrorTemplate($tamplateName)
	{
		self::$_defaultErrorTemplate = $tamplateName;
	}
	
	/**
	 * Set view template use for rendering on error.
	 *
	 * @param string $tamplateName Template file name
	 * @return void
	 */
	public function setErrorTemplate($tamplateName)
	{
		$this->_errorTemplate = $tamplateName;
	}
	
	/**
	 * Get view template use for rendering on error.
	 *
	 * @return string Template file name
	 */
	public function getErrorTemplate()
	{
		return $this->_errorTemplate;
	}
	
	/**
	 * Get whether exists view template.
	 *
	 * @return boolean
	 */
	public function existsTemplate()
	{
		$ext = NameManager::getTemplateExtension();
		$templateDir = PathManager::getViewTemplateDirectory();
		$template = $this->_template . '.' . $ext;
		$templatePath = $templateDir . '/' . $template;
		$exists = file_exists($templatePath);
		return $exists;		
	}
		
	/**
	 * Set layout template
	 *
	 * @param string $layout Layout template name
	 * @return void
	 */
	public function setLayout($layout)
	{
		$ext = NameManager::getTemplateExtension();
		$layout = preg_replace('/\.' . $ext . '$/', '', $layout);
		$this->_layout = $layout;
	}
	
	/**
	 * Set encoding of output template
	 * 
	 * @param string $encoding
	 * @return void
	 */
	public function setOutputEncoding($encoding)
	{
		$this->_outputEncoding = $encoding;
	}
	
	/**
	 * Add javascript file name to read
	 *
	 * @param string $fileName Javascript file name
	 * @param string $key Array key, if you want specify by key in template
	 * @param boolean $isPreferred
	 * @return void
	 */
	protected function _addJs($fileName, $key = null, $isPreferred = false)
	{
		$fileName = trim($fileName, '/');
		$fileName = preg_replace('|.js$|', '', $fileName) . '.js';
		if (!file_exists(sprintf('%s/js/%s', PathManager::getHtdocsDirectory(), $fileName))) {
			return;
		}
		if (in_array($fileName, $this->_jsFiles)) {
			return;
		}
		if ($isPreferred == true) {
			if ($key == null) {
				$this->_jsFilesPreferred[] = $fileName;
			} else {
				$this->_jsFilesPreferred[$key] = $fileName;
			}			
		} else {
			if ($key == null) {
				$this->_jsFiles[] = $fileName;
			} else {
				$this->_jsFiles[$key] = $fileName;
			}
		}
	}
		
	/**
	 * Add javascript file name to read
	 *
	 * @param string $fileName Javascript file name
	 * @param string $key Array key, if you want specify by key in template
	 * @return void
	 */
	public function addJs($fileName, $key = null)
	{
		return $this->_addJs($fileName, $key);
	}
	
	/**
	 * Add javascript file name to read to preferentially
	 *
	 * @param string $fileName Javascript file name
	 * @param string $key Array key, if you want specify by key in template
	 * @return void
	 */
	public function addPreferredJs($fileName, $key = null)
	{
		return $this->_addJs($fileName, $key, true);
	}
	
	/**
	 * Add stylesheet file name to read
	 *
	 * @param string $fileName Stylesheet file name
	 * @param string $key Array key, if you want specify by key in template
	 * @param boolean $isPreferred
	 * @return void
	 */
	protected function _addCss($fileName, $key = null, $isPreferred = false)
	{
		$fileName = trim($fileName, '/');
		$fileName = preg_replace('|.css$|', '', $fileName) . '.css';
		if (!file_exists(sprintf('%s/css/%s', PathManager::getHtdocsDirectory(), $fileName))) {
			return;
		}
		if (in_array($fileName, $this->_cssFiles) || in_array($fileName, $this->_cssFilesPreferred)) {
			return;
		}
		if ($isPreferred == true) {
			if ($key == null) {
				$this->_cssFilesPreferred[] = $fileName;
			} else {
				$this->_cssFilesPreferred[$key] = $fileName;
			}			
		} else {
			if ($key == null) {
				$this->_cssFiles[] = $fileName;
			} else {
				$this->_cssFiles[$key] = $fileName;
			}
		}
	}
	
	/**
	 * Add stylesheet file name to read
	 *
	 * @param string $fileName Stylesheet file name
	 * @param string $key Array key, if you want specify by key in template
	 * @return void
	 */
	public function addCss($fileName, $key = null)
	{
		return $this->_addCss($fileName, $key);
	}
	
	/**
	 * Add stylesheet file name to read preferentially
	 *
	 * @param string $fileName Stylesheet file name
	 * @param string $key Array key, if you want specify by key in template
	 * @return void
	 */
	public function addPreferredCss($fileName, $key = null)
	{
		return $this->_addCss($fileName, $key, true);
	}
	
	/**
	 * Set value of tag in which a name has an attribute which is "http-equiv" 
	 * 
	 * @param string $httpEquivValue Value of "name" attribute
	 * @param string $contentValue Value of "content" attribute
	 * @return void
	 */
	public function setMetaHttpEquiv($httpEquivValue, $contentValue)
	{
		$this->_metas['http-equiv'][$httpEquivValue] = $contentValue;
	}
	
	/**
	 * Set value of tag in which a name has an attribute which is "name" 
	 * 
	 * @param string $nameValue Value of "name" attribute
	 * @param string $contentValue Value of "content" attribute
	 * @return void
	 */
	public function setMetaName($nameValue, $contentValue)
	{
		$this->_metas['name'][$nameValue] = $contentValue;
	}
	
	/**
	 * Set page keywords
	 * 
	 * @param string|array $keywords
	 * @return void
	 */
	public function setMetaNameKeywords($keywords)
	{
		if (is_array($keywords)) {
			$keywords = implode(',', $keywords);
		}
		$this->setMetaName('keywords', $keywords);		
	}
	
	/**
	 * Set page description
	 * 
	 * @param string $description
	 * @return void
	 */
	public function setMetaNameDescription($description)
	{
		$this->setMetaName('description', $description);		
	}
	
	/**
	 * Set title of page
	 * 
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->_title = $title;
	}
	
	/**
	 * Clear all assigned values
	 *
	 * @return void
	 */
	public function clearValues()
	{
		$this->_vars = array();
	}
	
	/**
	 * Set basic vars to template
	 * 
	 * @return void
	 */
	public function setBasicVars()
	{
		$req = $this->_request;		
		$requestInfo['base_path']  = rtrim('/' . trim($req->getBasePath(), '/'), '/');
		$requestInfo['base_url'] = $req->getBaseUrl();
		$requestInfo['controller'] = $req->getController();
		$requestInfo['action'] = $req->getAction(); 
		$this->set('request', $requestInfo);
		
		$rendering['controller'] = $this->_renderingController;
		$rendering['action'] = $this->_renderingAction;
		$this->set('rendering', $rendering);
		
		$this->addPreferredCss('common');
		$this->addPreferredCss($this->_renderingController);
		$cssFiles = array_merge($this->_cssFilesPreferred, $this->_cssFiles);
		$this->set('stylesheets', $cssFiles);
		
		$this->addPreferredJs('common');
		$this->addPreferredJs($this->_renderingController);
		$jsFiles = array_merge($this->_jsFilesPreferred, $this->_jsFiles);
		$this->set('javascripts', $jsFiles);
		
		$this->set('page_title', $this->_title);
		$this->set('metas', $this->_metas);
	}
	
	/**
	 * Execute output html
	 *
	 * @return string
	 */
	public function render()
	{
		if ($this->_renderingEnabled == false || $this->_templateEnabled == false) {
			return;
		}
		if ($this->_template == null) {
			$this->setTemplate($this->_renderingAction, $this->_renderingController);
		}
		$rendered = $this->getRendered();
		echo $rendered;
	}
	
	/**
	 * Get output text which should be outputted is acquired. 
	 * 
	 * @return string
	 */
	public function getRendered()
	{
		$this->setBasicVars();
		$wasNull = false;
		if ($this->_template == null) {
			$wasNull = true;
			$this->setTemplate($this->_renderingAction, $this->_renderingController);
		}
		$rendered = null;
		if ($this->_layoutEnabled) {
			$rendered = $this->renderTemplateWithLayout();
		} else {
			$rendered = $this->renderTemplate();
		}
		if ($wasNull == true) {
			$this->_template = null;
		}
		if ($rendered != '' && $this->_outputEncoding != null) {
			$currentEnc = mb_detect_encoding($rendered);
			if ($currentEnc != $this->_outputEncoding) {
				$rendered = mb_convert_encoding($rendered, $this->_outputEncoding, $currentEnc);
			}
		}
		return $rendered;
	}
		
	/**
	 * Execute output html using a layout template
	 *
	 * @return void
	 */
	abstract protected function renderTemplate();
	
	/**
	 * Execute output html without using a layout template
	 *
	 * @return void
	 */
	abstract protected function renderTemplateWithLayout();
		
}