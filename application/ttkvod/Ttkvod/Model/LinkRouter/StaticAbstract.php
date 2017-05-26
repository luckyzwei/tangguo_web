<?php
abstract class Ttkvod_Model_LinkRouter_StaticAbstract implements Ttkvod_Model_LinkRouter_Interface
{
	/**
	 * @var string
	 */
	protected $mHost;
	
	protected $mExtendtion;
	
	protected $mRouter;
	
	protected $mStaticHost;
	
	/**
	 * @param string $host
	 */
	public function __construct($hostDefaultKey, $host = null, $extendtion = null)
	{
		$cfg = Lamb_Registry::get(CONFIG);
		if (null === $host) {
			$host = $cfg['model_router_hostname'][$hostDefaultKey];
			
		} else {
			$host = (string)$host;
		}
		$this->mStaticHost = $cfg['model_router_hostname']['static'];
		if (null === $extendtion) {
			$extendtion = $cfg['static_cfg']['extendtion'];
		} else {
			$extendtion = (string)$extendtion;
		}		
		$this->mHost = $host;
		$this->mRouter = Lamb_App::getGlobalApp()->getRouter();
		$this->mExtendtion = $extendtion;
		unset($cfg);
	}
	
	/**
	 * Ttkvod_Model_LinkRouter_Interface implements
	 */
	public function getHost()
	{
		return $this->mHost;
	}	
	
	/**
	 * @param array $params = array('id' => int)
	 * Ttkvod_Model_LinkRouter_Interface implements
	 */		
	public function getStaticLinkPath(array $params)
	{
		$temp = $this->mStaticHost;
		$this->mStaticHost = '';
		$path = $this->getStaticLink($params);
		$this->mStaticHost = $temp;
		return $path;
	}	
}