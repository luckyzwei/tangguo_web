<?php
abstract class Ttkvod_Controllor extends Lamb_Controllor_Abstract
{
	const PT_PAGE_URL = 0;
	
	const PT_NEXTPAGE_URL = 1;
	
	const PT_PREVPAGE_URL = 2;
	
	const PT_LASTPAGE_URL = 3;
	
	const PT_FIRSTPAGE_URL = 4;
	
	/**
	 * @var array
	 */
	protected $mSiteCfg;
	
	/**
	 * @var string
	 */
	protected $mRuntimeTemplate;
	
	/**
	 * @var string
	 */
	protected $mRuntimeViewPath;
	
	/**
	 * @var string
	 */
	protected $mRuntimeThemePath;
	
	/**
	 * @var string
	 */
	protected $mBlankImgPath; 
	
	/**
	 * @var string
	 */
	protected $mRuntimeThemeUrl;
	
	/**
	 * @var Ttkvod_Model_LinkRouter
	 */
	protected $mLinkRouter;
	
	/**
	 * @var callback
	 */
	protected $mCacheCallback;
	
	/** 
	 * @var int
	 */
	protected $mCacheTime ;
	
	/**
	 * ÿҳ��ȡ����������
	 */
	const MAX_PAGESIZE = 50;
	
	/**
	 * @var int
	 */
	protected $mCacheType;

	protected $mCacheSuffix;
	
	public function __construct()
	{
		parent::__construct();
		$t = trim($this->mRequest->tt);
		$this->mSiteCfg = Lamb_Registry::get(CONFIG);
		$this->mRuntimeTemplate = $this->mSiteCfg['template'];
		$this->mRuntimeThemePath = $this->mSiteCfg['theme_path'] . $this->mRuntimeTemplate . '/';;
		$this->mRuntimeViewPath = $this->mSiteCfg['view_path'] . $this->mRuntimeTemplate . '/';
		$this->mRuntimeThemeUrl = $this->mSiteCfg['site_root'] . substr($this->mRuntimeThemePath, strlen(ROOT));
		$this->mApp->setViewPath($this->mRuntimeViewPath);
		$this->mBlankImgPath = Lamb_Utils::isHttp($this->mSiteCfg['blank_img_path']) ? $this->mSiteCfg['blank_img_path']
									: $this->mSiteCfg['site_root'] . $this->mSiteCfg['blank_img_path'];
		$this->mLinkRouter = Ttkvod_Model_LinkRouter::getSingleInstance();
		$this->mLinkRouter->setModelLinks('item', new Ttkvod_Model_LinkRouter_Item)
						  ->setModelLinks('list', new Ttkvod_Model_LinkRouter_List)
						  ->setModelLinks('', new Ttkvod_Model_LinkRouter_Default);
		$this->mCacheCallback = 'Ttkvod_Cache_Factory::getCache';
		$this->mCacheTime = $this->mRequest->ct;
		if (!Lamb_Utils::isInt($this->mCacheTime)) {
			$this->mCacheTime = 0;
		}
		$this->mCacheType = Lamb_View_Tag_List::CACHE_HTML;
		$this->mRouter->setUrlDelimiter($this->mSiteCfg['url_delimiter'])
			 ->setRouterParamName($this->mSiteCfg['url_param_name']);
		$this->mCacheSuffix = $this->mSiteCfg['cache_cfg']['cache_suffix'];
	}
	
	/**
	 * @param string $filename
	 * @return string
	 */
	public function load($filename)
	{
		return $this->mView->load($filename, $this->mRuntimeTemplate);
	}
	
	/**
	 * @return int
	 */
	public function getRealCacheTime()
	{
		if ($this->mCacheTime < 0) {
			return 0;
		} else if ($this->mCacheTime == 0) {
			return $this->mSiteCfg['cache_cfg']['timeout'];
		} else {
			return $this->mCacheTime;
		}
	}
	
	/**
	 * @param int $type PT_PAGE_URL | PT_NEXTPAGE_URL | PT_PREVPAGE_URL
	 * @param string $pagePrev
	 * @return string
	 */
	public function getPageUrlTempalte($type, $pagePrev = '')
	{
		$ret = '';
		static $staticParams = array(
			self::PT_PAGE_URL => '#page#',
			self::PT_NEXTPAGE_URL => '#nextPage#',
			self::PT_PREVPAGE_URL => '#prevPage#',
			self::PT_LASTPAGE_URL => '#lastPage#',
			self::PT_FIRSTPAGE_URL => 1
		);
		if (isset($staticParams[$type])) {
			$ret = $this->mRouter->url(array('p' => $staticParams[$type]), false);
		}
		return $pagePrev . $this->mRouter->setUrlDelimiter() . $ret;
	}
	
	/**
	 * @param boolean $isCheckStatus
	 * @param array & $userinfo
	 * @return int 0 - not found > 0 uid
	 */
	public function userCheck($isCheckStatus = true, &$userinfo = null)
	{
		$usermodel = new Ttkvod_Model_User;
		if (!$usermodel->isLogin($userinfo)) {
			return 0;
		}
		$userinfo = array(
			'uid' => $_SESSION['_UID_'],
			'username' => $_SESSION['_USERNAME_']
		);
		$uid = $userinfo['uid'];
		unset($userinfo);
		return $uid;
	}

	public function addAjaxDomainScript($model = null)
	{
		if (null === $model) {
			$model = $this->getControllorName();
		}
		
		if ($this->mSiteCfg['domain'] && $this->mSiteCfg['model_router_hostname'][$model] != '/') {
			echo "<script>document.domain='{$this->mSiteCfg['domain']}'</script>";
		}
	}

	/**
	 * @param string $msg
	 * @param array $opt = array('end' => boolean, 'mode' => int 1-ֱ����� 2-callback 3-script)
	 * @return void
	 */
	public function noticeClient($msg, array $opt = array())
	{
		$option = array(
			'end' => 1,
			'mode' => 1,
			'callback' => ''
		);
		Lamb_Utils::setOptions($option, $opt);
		
		if ($option['mode'] == 2 && $option['callback']) {
			$msg = "{$option['callback']}($msg)";
		}
		
		if ($option['mode'] == 3) {
			$msg = "<script>{$option['callback']}($msg)</script>";
		}
		
		if ($option['end']) {
			$this->mResponse->eecho($msg);
		} else {
			echo $msg;
		}
	}
	
	/**
	 * ��������Ϣ�����
	 *
	 * @param int $code ������
	 * @param array $data ���������
	 * @param string $errorString ������Ϣ�����Ϊ�գ���$code=0,-1,-2�������̶��Ĵ�����Ϣ�������Ϊ�գ�����ȴ������ļ�error_strings�ҳ���Ӧ��ӳ�䣬
	 * ����Ҳ���ӳ�䣬��ֱ�ӽ���ֵ���
	 */
	public function showResults($code, array $data = null, $errorString = '')
	{
		static $fixedErrorStr = array(
			'0' => '��������æ�����Ժ�����',
			'-1' => '����û�е�¼',
			'-2' => '��¼���ڣ������µ�¼'
		);
		
		$ret = array('s' => $code);
		
		if ($data) {
			$ret['d'] = $data;
		}
		
		if (!$errorString && isset($fixedErrorStr[$code])) {
			$errorString = $fixedErrorStr[$code];
		}
		
		$ret['err_str'] = iconv('gbk', 'utf-8', $errorString);
		
		$ret = json_encode($ret);
		$this->mResponse->eecho($ret);	
	}
	
	/**
	 * @return array
	 */
	public function getSearchIndex()
	{
		static $areas = array('��½', '���', '̨��', '����', '�ձ�', '̩��', '����', 'Ӣ��', '����', 'ŷ��','����');
		return array(
				1 => array(
					'types' => array('����','ð��','ϲ��','����','�ƻ�','�ֲ�','ս��','����', '����','���','����','����', '����'),
					'areas' => array('��½', '���', '̨��', '����', '�ձ�', '̩��', '����', 'Ӣ��', '����', 'ŷ��', '�¹�', 'ӡ��', '�¼���', '������', '����')
				),
				2 => array(
					'types' => array('����','��װ','��Ц','����','��','ż��','�ƻ�','����','����','��ͥ','����','��ʷ'),
					'areas' => $areas
				),
				3 => array(
					'types' => array('��Ѫ','��Ц','����Ů','����','��ս','����','����','ð��','���','У԰','����','����'),
					'areas' => array('��½', '���', '̨��', '����', '�ձ�', '̩��', '����', 'Ӣ��', '����', 'ŷ��','����')
				),
				4 => array(
					'types' => array('�ѿ���','������','ѡ��','��ʳ','����','����','��ʵ','��Ц','�ٶ�','����','ʱ��','��̸','����'),
					'areas' => $areas
				)
			);
	}
	
	public function d($str)
	{
		Lamb_Debuger::debug($str);
	}
		
}