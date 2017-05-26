<?php
class Ttkvod_Model_LinkRouter_Default extends Ttkvod_Model_LinkRouter_StaticAbstract
{
	public function __construct()
	{
		$cfg = Lamb_Registry::get(CONFIG);
		parent::__construct('index');
	}
	
	/** 
	 * @param array $params = array(
	 * 		'id' => 'index' | 'top'
	 *		....
	 * )
	 * Ttkvod_Model_LinkRouter_Interface implemention
	 */
	public function getStaticLink(array $params)
	{
		switch ($params['id']) {
		case 'index':
			return $this->mStaticHost . 'index.' . $this->mExtendtion;
		case 'search':
		case 'member':
			return $this->getDynamicLink($params);
		}
		return $ret;
	}

	/** 
	 * @param array $params = array(
	 * 		'id' => 'index' | 'top'
	 *		....
	 * )
	 * Ttkvod_Model_LinkRouter_Interface implemention
	 */	
	public function getDynamicLink(array $params)
	{
		$id = $params['id'];
		$host = $this->getHost();
		if ($id == 'index') {
			return $host;
		}
		$router = Lamb_App::getGlobalApp()->getRouter();
		if ($id == 'search' || $id == 'member') {
			$cfg = Lamb_Registry::get(CONFIG);
			$host = $cfg['model_router_hostname'][$id];
			$params[$router->setControllorKey()] = $id;
			$params[$router->setActionKey()] = '';
			if (isset($params['action'])) {
				$params[$router->setActionKey()] = $params['action'];
				unset($params['action']);
			}
		} else {
			$params[$router->setControllorKey()] = '';
			$params[$router->setActionKey()] = $id;		
		}
		unset($params['id']);	
		return $host . '/index.php?' . $router->setRouterParamName() . '=' . $router->url($params);;	
	}
}