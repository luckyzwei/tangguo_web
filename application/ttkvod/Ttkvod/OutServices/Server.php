<?php
class Ttkvod_OutServices_Server
{
	const E_CLIENT_ID_ILLEGAL = -1;
	
	const E_DECODE_FIAL = -2;
	
	const E_METHOD_ILLEGAL = -3;
	
	const E_METHOD_PARAMS_ERROR = -4;
	
	const S_OK = 1;
	
	/**
	 * @var Ttkvod_OutServices_Method
	 */
	protected $mMethods;
	
	public function __construct()
	{
		$this->mMethods = new Ttkvod_OutServices_Method();
	}
	
	/**
	 * @param string $clientId
	 * @param string $encodeStr
	 * @return mixed
	 */
	public function runFromRemote($clientId, $encodeStr, &$errorCode = self::S_OK)
	{
		if (!($ret = $this->parseRemoteInfo($clientId, $encodeStr, $errorCode))) {
			return null;
		}
		return $this->runFromLocal($ret['method'], $ret['params'], $errorCode);
	}
	
	/**
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public function runFromLocal($method, array $params = null, &$errorCode = self::S_OK)
	{
		if (!$this->isMethodExists($method)) {
			return self::E_METHOD_ILLEGAL;
		}
		return $this->mMethods->$method($params, $errorCode);
	}
	
	/**
	 * @param string $method
	 * @return boolean
	 */
	public function isMethodExists($method)
	{
		return method_exists($this->mMethods, $method);
	}
	
	/**
	 * @param string $clientId
	 * @param string $encodeStr
	 * @param int & $errorCde
	 * @return array | null
	 */
	protected function parseRemoteInfo($clientId, $encodeStr, &$errorCode = self::S_OK)
	{
		$cfg = Lamb_Registry::get(CONFIG);
		if (!array_key_exists($clientId, $cfg['out_services_hash']) || !($setting = $cfg['out_services_hash'][$clientId])) {
			$errorCode = self::E_CLIENT_ID_ILLEGAL;
			return null;
		}
		if (!isset($setting['expire'])) {
			$setting['expire'] = 0;
		}
		if (!isset($setting['ckey_len'])) {
			$setting['ckey_len'] = 7;
		}
		if (!($decodeStr = Lamb_Utils::authcode($encodeStr, $setting['key'], 'DECODE', $setting['expire'], $setting['ckey_len']))) {
			$errorCode = self::E_DECODE_FAIL;
			return null;
		}
		parse_str($decodeStr, $params);
		if (!isset($params['method']) || !$this->isMethodExists($params['method'])) {
			$errorCode = self::E_METHOD_ILLEGAL;
			return null;
		}
		$method = $params['method'];
		unset($params['method']);
		return array(
			'method' => $method,
			'params' => $params
		);
	}
}