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
	 *				'url' => [string] �����URL��ֵ����ȥ����host,port,path�Ȳ���
	 *				'host' => 
	 *				'port' =>
	 *				'path' =>
	 *				'ispost' => false
	 *				'savepath' =>,
	 *				'quickquit' => true,
	 *				'param_name' => httpͬ��ʱ������
	 *				'local_path_prev' => 'http://adasd | /path' ������Դ��URLǰ׺�������ֵΪ�գ�
	 *								��Ĭ�ϲ������κβ����������ֵΪһ��http://�������ӣ����ڵ���
	 *								write����ʱ�򣬵�����$identity����һ����HTTP://��ͷ�����ӣ����Զ�
	 *								����ǰ�����local_path_prev����local_path_prev������http://��ͷ��
	 *								���Զ�����ǰ������ַ����
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
	 * ����local_path_prev������Ӱ�죬��Ҫ��ȡ����ԭʼ��URI
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