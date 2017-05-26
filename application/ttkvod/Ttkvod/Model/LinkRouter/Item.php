<?php
class Ttkvod_Model_LinkRouter_Item extends Ttkvod_Model_LinkRouter_StaticAbstract
{	
	public function __construct()
	{
		parent::__construct('item');
	}
	
	/**
	 * @param array $params = array('id' => int)
	 * Ttkvod_Model_LinkRouter_Interface implements
	 */	
	public function getStaticLink(array $params)
	{
		return $this->mStaticHost . '/html/' . $params['id'] . '.' . $this->mExtendtion;
	}
	
	/**
	 * Ttkvod_Model_LinkRouter_Interface implements
	 */	
	public function getDynamicLink(array $params)
	{
		$router = Lamb_App::getGlobalApp()->getRouter();
		if (!isset($params['action'])) {
			$params['action'] = '';
		}
		return $this->getHost() . '?' . $router->setRouterParamName() . '=' . $router->url(array(
																								$router->setControllorKey() => 'item',
																								$router->setActionKey() => $params['action'],
																								'id' => $params['id']
																							));
	}
}