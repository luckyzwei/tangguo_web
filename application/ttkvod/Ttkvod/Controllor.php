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
	 * 每页获取最大的数据数
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
	 * @param array $opt = array('end' => boolean, 'mode' => int 1-直接输出 2-callback 3-script)
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
	 * 带错误信息的输出
	 *
	 * @param int $code 错误码
	 * @param array $data 输出的内容
	 * @param string $errorString 错误信息，如果为空，当$code=0,-1,-2则会输出固定的错误信息，如果不为空，则会先从配置文件error_strings找出对应的映射，
	 * 如果找不到映射，则直接将该值输出
	 */
	public function showResults($code, array $data = null, $errorString = '')
	{
		static $fixedErrorStr = array(
			'0' => '服务器繁忙，请稍后再试',
			'-1' => '您还没有登录',
			'-2' => '登录过期，请重新登录'
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
		static $areas = array('大陆', '香港', '台湾', '韩国', '日本', '泰国', '美国', '英国', '法国', '欧美','其他');
		return array(
				1 => array(
					'types' => array('动作','冒险','喜剧','爱情','科幻','恐怖','战争','犯罪', '悬疑','奇幻','武侠','剧情', '动画'),
					'areas' => array('大陆', '香港', '台湾', '韩国', '日本', '泰国', '美国', '英国', '法国', '欧美', '德国', '印度', '新加坡', '西班牙', '其他')
				),
				2 => array(
					'types' => array('剧情','古装','搞笑','悬疑','神话','偶像','科幻','言情','武侠','家庭','警匪','历史'),
					'areas' => $areas
				),
				3 => array(
					'types' => array('热血','搞笑','美少女','萝莉','机战','推理','竞技','冒险','社会','校园','剧情','其他'),
					'areas' => array('大陆', '香港', '台湾', '韩国', '日本', '泰国', '美国', '英国', '法国', '欧美','其他')
				),
				4 => array(
					'types' => array('脱口秀','真人秀','选秀','美食','旅游','汽车','纪实','搞笑','少儿','娱乐','时尚','访谈','音乐'),
					'areas' => $areas
				)
			);
	}
	
	public function d($str)
	{
		Lamb_Debuger::debug($str);
	}
		
}