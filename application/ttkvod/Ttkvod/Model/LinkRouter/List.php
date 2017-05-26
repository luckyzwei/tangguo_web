<?php
class Ttkvod_Model_LinkRouter_List extends Ttkvod_Model_LinkRouter_StaticAbstract
{
	private $cfg = null;
	public function __construct()
	{
		parent::__construct('list');
		$this->cfg = Lamb_Registry::get(CONFIG);
	}

	/**
	 * @param array $params = array('id' => int)
	 * Ttkvod_Model_LinkRouter_Interface implements
	 */	
	public function getStaticLink(array $params)
	{
		$id = $params['id'];
		unset($params['id']);
		if (count($params) == 0) {
			return $this->mStaticHost . '/vod-show-' . $id;
		}
		
		if (!@$params['area'] && !@$params['order']){
			if (!isset($params['p'])) {
				$params['p'] = 1;
			}
			
			if (!isset($params['tag']) || empty($params['tag'])) {
				$params['tag'] = 'p';
			} else {
				$listControllor = Lamb_App::getGlobalApp()->getDispatcher()->loadControllor('listControllor', true);
				$searchIndex = $listControllor->getSearchIndex();
				$currData = $searchIndex[$id];
				if (($index = array_search($params['tag'], $currData['types'])) === false) {
					$params['id'] = $id;
					return $this->getDynamicLink($params);						
				}
				//  /vod-show-1/1-1.html
				//$params['tag'] = $index;
			}
			if ($params['p'] <= 0) {
				$pageReplaceMent = '#page#';
				if ($params['p'] == -1) {
					$pageReplaceMent = '#prevPage#';
				} else if ($params['p'] == -2) {
					$pageReplaceMent = '#nextPage#';
				} else if ($params['p'] == -3) {
					$pageReplaceMent = '#lastPage#';
				}
				
				//Lamb_Debuger::debug($params);
				
				return $this->mStaticHost . '/vod-show-' . $id . '/tag-' . $params['tag'] . '-' . $pageReplaceMent . '.' . $this->mExtendtion;				
			} else {
				return $this->mStaticHost . '/vod-show-' . $id . '/tag-' . $params['tag'] . '-' . $params['p'] . '.' . $this->mExtendtion;
			}
		} else {
			
			$params['id'] = $id;
			return $this->getDynamicLink($params);			
		}
	}

	/**
	 * Ttkvod_Model_LinkRouter_Interface implements
	 */	
	public function getDynamicLink(array $params)
	{
		$router = Lamb_App::getGlobalApp()->getRouter();
		//Lamb_Debuger::debug($router);
		$params[$router->setControllorKey()] = 'list';
		$params[$router->setActionKey()] = '';
		if (isset($params['p']) && $params['p'] <= 0) {
			$pageReplaceMent = '#page#';
			if ($params['p'] == -1) {
				$pageReplaceMent = '#prevPage#';
			} else if ($params['p'] == -2) {
				$pageReplaceMent = '#nextPage#';
			} else if ($params['p'] == -3) {
				$pageReplaceMent = '#lastPage#';
			}
			unset($params['p']);
		
			//return $this->getHost() . '?' . $router->setRouterParamName() . '=' . $router->url($params) . $router->setUrlDelimiter() . $router->url(array('p' => $pageReplaceMent), false);
			
			if ($this->cfg['site_mode'] == 1) {
				return $this->getHost() . '/' . $router->url2($params) . $router->setUrlDelimiter() . $router->url2(array('p' => $pageReplaceMent), false) . '.' . $this->mExtendtion;
			} else {
				return $this->getHost() . '?' . $router->setRouterParamName() . '=' . $router->url($params) . $router->setUrlDelimiter() . $router->url(array('p' => $pageReplaceMent), false);
			}
			
		} else {
			
			if ($this->cfg['site_mode'] == 1) {
				return $this->getHost() . '/' . $router->url2($params) . '.' . $this->mExtendtion;
			} else {
				return $this->getHost() . '?' . $router->setRouterParamName() . '=' . $router->url($params);
			}
		}
	}
}