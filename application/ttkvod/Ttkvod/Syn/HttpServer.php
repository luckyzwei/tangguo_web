<?php
class Ttkvod_Syn_Http implements Ttkvod_Syn_Interface
{
	/**
	 * @var Ttkvod_Http
	 */
	protected $mHttp = null;
	
	/**
	 * @var boolean
	 */
	protected $mIsPost = false;
	
	/**
	 * @var string
	 */
	protected $mSavePath = '';
	
	/**
	 * @var string
	 */
	protected $mHttpPath = '';
	
	/**
	 * @var string
	 */
	protected $mLocalPathPrev = '';
	
	/**
	 * @var string
	 */
	protected $mHttpParamName = '';
	
	
	/**
	 * @param array $aOptions = array(
	 *				'url' => [string] 如果有URL键值，则不去查找host,port,path等参数
	 *				'host' => 
	 *				'port' =>
	 *				'path' =>
	 *				'ispost' => false
	 *				'savepath' =>,
	 *				'quickquit' => true,
	 *				'param_name' => http同步时参数名
	 *				'local_path_prev' => 'http://adasd | /path' 本地资源的URL前缀，如果该值为空，
	 *								则默认不进行任何操作，如果该值为一个http://完整链接，则在调用
	 *								write方法时候，当参数$identity不是一个以HTTP://开头的链接，将自动
	 *								在其前面加上local_path_prev，当local_path_prev不是以http://开头的
	 *								则自动将当前主机地址加上
	 *			)
	 * @throws Lamb_Exception
	 */
	public function __construct(array $aOptions)
	{	
		$options = array(
			'url' => '', 'host' => '', 'port' => '', 'path' => '', 'param_name' => '',
			'ispost' => false, 'savepath' => '', 'quickquit' => true, 'local_path_prev' => ''
		);
		Lamb_Utils::setOptions($options, $aOptions);
		if (isset($options['url']) && Lamb_Utils::isHttp($options['url'])) {
			$aParams = parse_url($options['url']);
			if (!isset($aParams['port'])) {
				$aParams['port'] = '80';
			}
			if (!isset($aParams['path'])) {
				$aParams['path'] = '/';
			}
			if (isset($aParams['query'])) {
				$aParams['path'] .= '?' . $aParams['query'];
			}
			$options['host'] = $aParams['host'];
			$options['port'] = $aParams['port'];
			$options['path'] = $aParams['path'];		
		}
		if (isset($options['host']) && isset($options['port']) && isset($options['path'])) {
			$this->mHttp = new Ttkvod_Http($options['host'], $options['port']);	
		} else {
			throw new Lamb_Exception('Inavild arguments for Ttkvod_Syn_Http::__construct,you must pass host,port,path');
		}
		$this->mHttpPath = $options['path'];
		$this->mIsPost = $options['ispost'];
		$this->mSavePath = $options['savepath'];
		$this->mHttp->bQuickQuit = $options['quickquit'];
		$this->mLocalPathPrev = $options['local_path_prev'];
		if (isset($options['local_path_prev']) && !Lamb_Utils::isHttp($options['local_path_prev'])) {
			$this->mLocalPathPrev = 'http://' . Lamb_App::getGlobalApp()->getRequest()->getHttpHost();
		}
		$this->mLocalPathPrev =  rtrim($this->mLocalPathPrev, '/');
		$this->mHttpParamName = $options['param_name'];
	}
	
	/**
	 * Ttkvod_Syn_Interface implemention
	 */
	public function write($identity, $strRemoteName = null, $path = null)
	{
		if (null === $path) {
			$path = $this->mSavePath;
		}
		$path = rtrim($path, '/');
		if (null === $strRemoteName) {
			$strRemoteName = basename($identity);
		}
		$identity = $this->getRealIdentity($identity);
		$param = $this->mHttpParamName . '=' . $identity . '$$' . $path . $strRemoteName;
		$ret = $this->mIsPost ? $this->mHttp->post($this->mHttpPath, $param) : $this->mHttp->get($this->mHttpPath, $param);
		if ($this->getStatus() != '200') {
			$ret = '';
		}
		return $ret;
	}
	
	/**
	 * 由于local_path_prev参数的影响，需要获取真正原始的URI
	 *
	 * @param string $identity
	 * @return string
	 */
	public function getRealIdentity($identity)
	{
		if (!Lamb_Utils::isHttp($identity)) {
			$identity = $this->mLocalPathPrev . $identity;
		}
		return $identity;		
	}
}