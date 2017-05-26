<?php
class Ttkvod_OutServices_Client
{
	/**
	 * @param array $remoteCfg array('server_url' => string, 'key' => string, 'ckey_len' => int, 
	 *			'expire' => int, 'clientid' => string, sync => boolean,
	 *			'controllor' => string, 'action' => string, post => false)
	 * @param string $method
	 * @param array | string $params
	 * @return 
	 */
	public function runFromRemote($remoteCfg, $method, $params = null, &$isOk = false)
	{
		$ret = '';
		$cfg = array(
			'server_url' => '',
			'key' => '',
			'ckey_len' => 7,
			'expire' => 60,
			'clientid' => '',
			'sync' => true,
			'controllor' => '',
			'action' => '',
			'post' => false
		);
		Lamb_Utils::setOptions($cfg, $remoteCfg);
		$router = Lamb_App::getGlobalApp()->getRouter();
		if ($params && is_array($params)) {
			$temp = '';
			foreach ($params as $key => $val) {
				$temp .= urlencode($key) . '=' . urlencode($val) . '&';
			}
			$params = $temp . 'method=' . $method;
		} else {
			$params = (string)$params;
			if ($params) {
				$params .= '&';
			}
			$params .= 'method=' . $method;
		}
		$param = array();
		$param['clientid'] = $cfg['clientid'];
		$param[$router->setControllorKey()] = $cfg['controllor'];
		$param[$router->setActionKey()] = $cfg['action'];
		if (!$cfg['post']) {
			$param['code'] =  Lamb_Utils::authcode($params, $cfg['key'], 'ENCODE', $cfg['expire'], $cfg['ckey_len']);
		} else {
			$_param = 'code=' . urlencode(Lamb_Utils::authcode($params, $cfg['key'], 'ENCODE', $cfg['expire'], $cfg['ckey_len']));
		}
		$url = $cfg['server_url'] . '?' . $router->setRouterParamName() . '=' . $router->url($param);		
		if (!$cfg['post']) { //get
			if ($cfg['sync']) {
				$ret = Ttkvod_Http::quickGet($url, 20, false, $status);
				if ($status == 200) {
					$isOk = true;
				}			
			} else {
				$isOk = Ttkvod_Http::quickGet($url, 20, true);
			}
		} else { //post
			if ($cfg['sync']) {
				$ret = Ttkvod_Http::quickPost($url, $_param, 20, false, $status);
				if ($status == 200) {
					$isOk = true;
				}			
			} else {
				$isOk = Ttkvod_Http::quickPost($url, $_param, 20, true);
			}			
		}
		return $ret;
	}
	
	/**
	 * @param string $method
	 * @param array $params
	 */
	public function runFromLocal($method, array $params = null, &$errorCode = null)
	{
		$server = new Ttkvod_OutServices_Server();
		return $server->runFromLocal($method, $params, $errorCode);
	}
}