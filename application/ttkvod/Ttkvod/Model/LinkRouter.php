<?php
class Ttkvod_Model_LinkRouter
{	
	/**
	 * @var array
	 */
	protected $mModelLinks = array();
	
	protected static $sInstance = null;
	
	public static function getSingleInstance()
	{
		if (null === self::$sInstance) {
			self::$sInstance = new self();
		}
		return self::$sInstance;
	}
	
	public function __construct()
	{
	
	}
	
	/**
	 * @param string $model
	 * @param Ttkvod_Model_LinkRouter_Interface $linkRouter
	 * @return Ttkvod_Model_LinkRouter
	 */
	public function setModelLinks($model, Ttkvod_Model_LinkRouter_Interface $linkRouter)
	{
		$this->mModelLinks[$model] = $linkRouter;
		return $this;
	}
	
	/**
	 * @param string $model
	 * @return boolean
	 */
	public function isModelExists($model)
	{
		return array_key_exists($model, $this->mModelLinks);
	}
	
	/**
	 * @return boolean
	 */
	public function clearModels()
	{
		$this->mModelLink = array();
		return $this;
	}
	
	/**
	 * @param string $model
	 * @return Ttkvod_Model
	 */
	public function getLinkRouter($model)
	{
		return $this->isModelExists($model) ? $this->mModelLinks[$model] : null;
	}
	
	/** 
	 * @param string $model
	 * @param array $params
	 * @return string
	 */
	public function router($model, array $params, $type = null)
	{
		$ret = '';
		if (null === $type) {
			$cfg = Lamb_Registry::get(CONFIG);
			$type = $cfg['site_mode'];
			unset($cfg);
		}
		
		if ($linkRouter = $this->getLinkRouter($model)) {
			switch($type) {
			case Ttkvod_Model_LinkRouter_Interface::T_MODE_STATIC:
				$ret = $linkRouter->getStaticLink($params);
				break;
			case Ttkvod_Model_LinkRouter_Interface::T_MODE_DYNAMIC:
				$ret = $linkRouter->getDynamicLink($params);
				break;
			case Ttkvod_Model_LinkRouter_Interface::T_MODE_HOSTNAME:
				$ret = $linkRouter->getHost();
				break;
			case Ttkvod_Model_LinkRouter_Interface::T_MODE_STATIC_PATH:
				$ret = $linkRouter->getStaticLinkPath($params);
				
			}
		}
	
		return $ret;
	}
}