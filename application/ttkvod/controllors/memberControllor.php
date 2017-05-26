<?php
class memberControllor extends Ttkvod_Controllor
{
	const S_UPDATE_SUCC = 5;
	
	const S_REGISTER_SUCC = 4;
	
	const S_IMPORT_SUCC = 3;
	
	const S_OLD_USER_LOGIN = 2;
	
	const S_LOGIN_SUCC = 1;
	
	const E_HTTP_SUBMIT_ILLEGAL = -1;
	
	const E_RANDKEY_TIMEOUT = -2;
	
	const E_LOGIN_INFO_INCOMPLETE = -3;
	
	const E_USERNAME_PASSWORD_ILLEGAL = -4;
	
	const E_USER_IS_LOCK = -5;
	
	const E_IMPORT_ILLEGAL = -6;
	
	const E_EMAIL_ILLEGAL = -7;
	
	const E_USER_IS_NOT_EXIST = -8;
	
	const E_EMAIL_IS_EXISTS = -9;
	
	const E_REGISTER_INFO_INCOMPLETE = -10;
	
	const E_TWO_PASSWORD_ERROR = -11;
	
	const E_USERNAME_IS_EXISTS = -12;
	
	const E_USERNAME_TOO_LONG = -13;
	
	const E_EMAIL_TOO_LONG = -14;
	
	const E_USERNAME_TOO_SHORT = -15;
	
	const E_EMAIL_TOO_SHORT = -16;
	
	const E_CLIENT_IP_IS_FORBIN = -17;
	
	const E_CLIENT_USERNAME_IS_FORBIN = -18;
	
	const E_CLIENT_EMAIL_IS_FORBIN = -19;
	
	const E_NO_LOGIN = -20;
	
	const E_UPDATE_EXISTS = -21;
	
	const E_UPDATE_FULL = -22;
	
	protected $mModel;
	
	protected $mRightTemplate;
	
	protected $mUserId;
	
	protected $mUsername;
	
	public function __construct()
	{
		parent::__construct();
		$this->mModel =  new Ttkvod_Model_User;
	}
	
	public function getControllorName()
	{
		return 'member';
	}	
	
	public function indexAction()
	{
		$this->checkPurview();
		include $this->load('member_index');		
	}
	
	public function loginAction()
	{
		$this->addAjaxDomainScript();
		if ($this->mRequest->isPost()) {
			$randkey = $this->mRequest->getPost('randkey', '');
			$username = $this->mRequest->getPost('username', '');
			$password = $this->mRequest->getPost('password', '');
			
			if (empty($username) || empty($password) || strlen($username) >= 100 ) {
				$this->showResults($this->clientJsMsgHandler(self::E_LOGIN_INFO_INCOMPLETE));
			}
			if (!Lamb_Utils::authcode($randkey, $this->mSiteCfg['form_rank_key'], 'DECODE', $this->mSiteCfg['form_rank_expire'])) {
				$this->showResults($this->clientJsMsgHandler(self::E_RANDKEY_TIMEOUT));
			}
			$usermodel = new Ttkvod_Model_User;
			$ret = $usermodel->login($password, $username, Ttkvod_Model_User::T_USERNAME, $userinfo);
			if ($ret <= 0) {
				$this->showResults($this->clientJsMsgHandler(self::E_USERNAME_PASSWORD_ILLEGAL));
			}
			if (!$userinfo['status']) {
				$this->showResults($this->clientJsMsgHandler(self::E_USER_IS_LOCK));
			}
			if ($ret == 2) {
				$this->showResults($this->clientJsMsgHandler(self::S_OLD_USER_LOGIN));
			}
			$usermodel->serialize($userinfo)
					  ->updateLoginInfo($username);
			$this->showResults($this->clientJsMsgHandler(self::S_LOGIN_SUCC));
		}
		$this->showResults($this->clientJsMsgHandler(self::E_HTTP_SUBMIT_ILLEGAL));
	}

	public function login2Action()
	{
		if ($this->mRequest->isPost()) {
			$username = $this->mRequest->getPost('username', '');
			$password = $this->mRequest->getPost('password', '');
			$isDetail = $this->mRequest->getPost('isDetail', '');
			
			if (empty($username) || empty($password) || strlen($username) >= 50 ) {
				$this->showResults(self::E_LOGIN_INFO_INCOMPLETE, null, '登录信息有误');
			}
			
			$usermodel = new Ttkvod_Model_User;
			$ret = $usermodel->login($password, $username, Ttkvod_Model_User::T_USERNAME, $userinfo);
			
			if ($ret <= 0) {
				$this->showResults(self::E_USERNAME_PASSWORD_ILLEGAL, null, '用户名或密码错误');
			}
			
			if (!$userinfo['status']) {
				$this->showResults(self::E_USER_IS_LOCK, null, '用户被锁定');
			}
			
			$usermodel->serialize($userinfo)
					  ->updateLoginInfo($username);
			$this->showResults(self::S_LOGIN_SUCC, array('uid' => $userinfo['uid'], 'isDetail' => $isDetail));
		}
		
		$this->showResults(0);
	}
	
	public function registerAction()
	{
		$this->addAjaxDomainScript();
		if ($this->mRequest->isPost()) {
			$username = $this->mRequest->getPost('username', '');
			$password = $this->mRequest->getPost('password', '');
			$password2 = $this->mRequest->getPost('password2', '');
			$email = $this->mRequest->getPost('email', '');
			$randkey = $this->mRequest->getPost('randkey', '');
			
			if (empty($username) || empty($password) || empty($password2) || empty($email)) {
				$this->showResults($this->clientJsMsgHandler(self::E_REGISTER_INFO_INCOMPLETE));
			}
			if (!Lamb_Utils::authcode($randkey, $this->mSiteCfg['form_rank_key'], 'DECODE', $this->mSiteCfg['form_rank_expire'])) {
				$this->showResults($this->clientJsMsgHandler(self::E_RANDKEY_TIMEOUT));
			}			
			$namelen = strlen($username);
			$emaillen = strlen($email);
			if ($password != $password2) {
				$this->showResults($this->clientJsMsgHandler(self::E_TWO_PASSWORD_ERROR));
			}
			if ($namelen > 200) {
				$this->showResults($this->clientJsMsgHandler(self::E_USERNAME_TOO_LONG));
			}
			if ($emaillen > 200) {
				$this->showResults($this->clientJsMsgHandler(self::E_EMAIL_TOO_LONG));
			}
			if ($namelen < 4 ) {
				$this->showResults($this->clientJsMsgHandler(self::E_USERNAME_TOO_SHORT));
			}
			if ($emaillen < 6) {
				$this->showResults($this->clientJsMsgHandler(self::E_EMAIL_TOO_SHORT));
			}
			if (!Lamb_Utils::isEmail($email)) {
				$this->showResults($this->clientJsMsgHandler(self::E_EMAIL_ILLEGAL));
			}
			if ($this->isClientInForbinIps()) {
				$this->showResults($this->clientJsMsgHandler(self::E_CLIENT_IP_IS_FORBIN));
			}
			if ($this->isClientInForbinInfo($username)) {
				$this->showResults($this->clientJsMsgHandler(self::E_CLIENT_USERNAME_IS_FORBIN));
			}
			if ($this->isClientInForbinInfo($email, 2)) {
				$this->showResults($this->clientJsMsgHandler(self::E_CLIENT_EMAIL_IS_FORBIN));
			}
			
			$userapi = new Ttkvod_UserApi;
			$ret = $userApi->addUser(array(
				'username' => $username,
				'nickname' => $username,
				'salt' => $salt,
				'password' => $password,
				'regip' => Ttkvod_Utils::getRealIp()
			), true);
			
			if ($ret['s'] < 0) {
				if ($ret['s'] == -3) {
					$this->showResults($this->clientJsMsgHandler(self::E_USERNAME_IS_EXISTS));
				} else if ($ret['s'] == -4) {
					$this->showResults($this->clientJsMsgHandler(self::E_HTTP_SUBMIT_ILLEGAL));
				} else if ($ret['s'] == -5) {
					$this->showResults($this->clientJsMsgHandler(self::E_HTTP_SUBMIT_ILLEGAL));
				}
			}
			$ret = $ret['d']['uid'];
		
			$usermodel = $this->mModel;
			$ret = $usermodel->add($ret, $username, $password, $email, $salt);
			if ($ret == Ttkvod_Model_User::E_USERNAME_EXISTS) {
				$this->showResults($this->clientJsMsgHandler(self::E_USERNAME_IS_EXISTS));
			}
			if ($ret == Ttkvod_Model_User::E_EMAIL_EXISTS) {
				$this->showResults($this->clientJsMsgHandler(self::E_EMAIL_IS_EXISTS));
			}
			if ($ret > 0) {
				
				$usermodel->serialize(array(
								'uid' => $ret,
								'username' => $username,
								'email' => $email,
								'password' => md5(md5($password) . $salt),
								'loginTime' => date('Y-m-d H:i:s'),
								'loginip' => Ttkvod_Utils::getRealIp()
							));
				$this->showResults($this->clientJsMsgHandler(self::S_REGISTER_SUCC));
			}
			
		}
		$this->showResults($this->clientJsMsgHandler(self::E_HTTP_SUBMIT_ILLEGAL));
	}

	public function register2Action()
	{
		if ($this->mRequest->isPost()) {
			$username = $this->mRequest->getPost('username', '');
			$password = $this->mRequest->getPost('password', '');
			$email = $this->mRequest->getPost('email', '');
			
			if (empty($username) || empty($password) || empty($email)) {
				$this->showResults(self::E_REGISTER_INFO_INCOMPLETE, null, '注册信息有误');
			}
						
			$namelen = strlen($username);
			$emaillen = strlen($email);
			if ($namelen > 50) {
				$this->showResults(self::E_USERNAME_TOO_LONG, null, '用户名长度不能超过50');
			}
			if ($emaillen > 50) {
				$this->showResults(self::E_EMAIL_TOO_LONG, null, '邮箱长度不能超过50');
			}
			if ($namelen < 4 ) {
				$this->showResults(self::E_USERNAME_TOO_SHORT, null, '用户名长度不够');
			}
			if ($emaillen < 6) {
				$this->showResults(self::E_EMAIL_TOO_SHORT, null, '邮箱长度不够');
			}
			if (!Lamb_Utils::isEmail($email)) {
				$this->showResults(self::E_EMAIL_ILLEGAL, null, '邮箱格式错误');
			}
		
			$usermodel = $this->mModel;
			$ret = $usermodel->add($username, $password, $email, $salt);
			
			if ($ret == Ttkvod_Model_User::E_USERNAME_EXISTS) {
				$this->showResults(self::E_USERNAME_IS_EXISTS, null, '用户名已存在');
			}
			
			if ($ret == Ttkvod_Model_User::E_EMAIL_EXISTS) {
				$this->showResults(self::E_EMAIL_IS_EXISTS, null, '邮箱已存在');
			}
			
			if ($ret > 0) {
				$usermodel->serialize(array(
								'uid' => $ret,
								'username' => $username,
								'email' => $email,
								'password' => md5(md5($password) . $salt),
								'loginTime' => date('Y-m-d H:i:s'),
								'loginip' => Ttkvod_Utils::getRealIp()
							));
				$this->showResults(1);
			}
			
		}
		
		$this->showResults(0);
	}
	
	public function loginoutAction()
	{
		$this->mModel->loginout();
		$this->showResults(1);
	}
	
	public function testAction()
	{
		
		/*
		$userapi = new Ttkvod_UserApi;
		$ret = $userApi->addUser(array(
			'username' => '12345678',
			'nickname' => '12345678',
			'salt' => '123456',
			'password' => '123456',
			'regip' => '192.168.8.58'
		), true);
		print_r($ret);
		*/
	}
	
	public function importAction()
	{
		$this->addAjaxDomainScript();
		if ($this->mRequest->isPost()) {
			$randkey = $this->mRequest->getPost('randkey', '');
			$username = $this->mRequest->getPost('old_username', '');
			$password = $this->mRequest->getPost('old_password', '');
			$email = $this->mRequest->getPost('email', '');
			
			if (empty($randkey) || empty($username) || empty($password)) {
				$this->showResults($this->clientJsMsgHandler(self::E_IMPORT_ILLEGAL));
			} 
			if (!Lamb_Utils::authcode($randkey, $this->mSiteCfg['form_rank_key'], 'DECODE', $this->mSiteCfg['form_rank_expire'])) {
				$this->showResults($this->clientJsMsgHandler(self::E_RANDKEY_TIMEOUT));
			}
			if (empty($email) || !Lamb_Utils::isEmail($email)) {
				$this->showResults($this->clientJsMsgHandler(self::E_EMAIL_ILLEGAL));
			}
			
			$usermodel = $this->mModel;
			if (!($user = $usermodel->checkOld($username, $password))) {
				$this->showResults($this->clientJsMsgHandler(self::E_USER_IS_NOT_EXIST));
			}
			if (!$user['status']) {
				$this->showResults($this->clientJsMsgHandler(self::E_USER_IS_LOCK));
			}
			if ($usermodel->get($email, Ttkvod_Model_User::T_EMAIL)) {
				$this->showResults($this->clientJsMsgHandler(self::E_EMAIL_IS_EXISTS));
			}
			if ($usermodel->fromOldToNew($email, $username, Ttkvod_Model_User::T_USERNAME) > 0) {
				$user['email'] = $email;
				$usermodel->serialize($user)
					  	  ->updateLoginInfo($username);				
				$this->showResults($this->clientJsMsgHandler(self::S_IMPORT_SUCC));
			}
		}
		$this->showResults($this->clientJsMsgHandler(self::E_IMPORT_ILLEGAL));
	}
	
	public function favAction()
	{
		$this->checkPurview();
		$msg = trim($this->mRequest->msg);
		$page = trim($this->mRequest->p);
		$vid = trim($this->mRequest->vid);
		
		if ($msg == 'delete' && Lamb_Utils::isInt($vid, true)) {
			Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from favorites where vedioId=? and userId=?',
					array(1 => array($vid, PDO::PARAM_INT), 2 => array($this->mUserId, PDO::PARAM_INT)));
		}
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		$sql = 'select a.vedioId as vedioId, a.date as date, name as vedioName,vedioPic,mark from favorites a,vedio b where a.vedioId=b.id and a.userId = :uid order by date desc';
		$aPrepareSource = array(':uid' => array($this->mUserId, PDO::PARAM_INT));
		$pageUrlPrev = $this->mLinkRouter->router('', array('id' => 'member', 'action' => 'fav'));
		$firstPageUrl = $this->getPageUrlTempalte(self::PT_FIRSTPAGE_URL, $pageUrlPrev);
		$lastPageUrl = $this->getPageUrlTempalte(self::PT_LASTPAGE_URL, $pageUrlPrev);
		$pageUrl = $this->getPageUrlTempalte(self::PT_PAGE_URL, $pageUrlPrev);
		
		$this->mRightTemplate = 'member_index_fav';
		include $this->load('member_index');		
	}
	
	public function historyAction()
	{
		$action = trim($this->mRequest->ac);
		$mid = trim($this->mRequest->mid);
	
		if (!$this->mModel->isLogin()) {
			$this->showResults(-1);
		}
		
		$uid = $_SESSION['_UID_'];
		
		switch ($action) {
			case 'add':
			case 'dele':
				if (Lamb_Utils::isInt($mid, true)) {
					$ret = $action == 'dele' ? $this->mModel->deleteNetPlayList($mid, $uid) : $this->mModel->addNetPlayList($mid, $uid);
					if ($ret > 0) {
						$this->getPlayListCacheByUid($uid)->flush();
					}
					$this->showResults(1);				
				}
				break;
			case 'clear':
				if ($this->mModel->clearNetPlayList($uid)) {
					$this->getPlayListCacheByUid($uid)->flush();
					$this->showResults(1);
				}
				break;
			case 'get':
				if ($data = $this->getPlayListCacheData($uid)) {
					$this->showResults(1, $data);
				}
				break;
		}
		
		$this->showResults(0);
	}

	public function playlist2Action()
	{
		$action = trim($this->mRequest->ac);
		$vid = trim($this->mRequest->vid);
		$callback = trim($this->mRequest->c);
		$msgOpt = array('mode' => 2, 'callback' => $callback);	
		
		if (!$this->mModel->isLogin()) {
			$this->noticeClient(-1, $msgOpt);
		}
		
		$uid = $_SESSION['_UID_'];
		
		switch ($action) {
			case 'add':
			case 'dele':
				if (Lamb_Utils::isInt($vid, true)) {
					$ret = $action == 'dele' ? $this->mModel->deleteNetPlayList($vid, $uid) : $this->mModel->addNetPlayList($vid, $uid);
					if ($ret > 0) {
						$this->getPlayListCacheByUid($uid)->flush();
					}
					$this->noticeClient($ret, $msgOpt);				
				}
				break;
			case 'clear':
				if ($this->mModel->clearNetPlayList($uid)) {
					$this->getPlayListCacheByUid($uid)->flush();
					$this->noticeClient(1, $msgOpt);
				}
				break;
			case 'get':
				if ($str = $this->getPlayListCacheData($uid)) {
					$this->noticeClient($str, $msgOpt);
				}
				break;
			case 'import':
				$infos = trim($this->mRequest->info);
				$succ = false;
				if (!empty($infos)) {
					foreach (explode(',', $infos) as $info) {
						$arr = explode('|', $info);
						if (count($arr) == 2 && Lamb_Utils::isInt($arr[0], true) && Lamb_Utils::isInt($arr[1], true)) {
							if ($this->mModel->addNetPlayList($arr[0], $uid, null, $arr[1]) > 0) {
								$succ = true;
							}
						}
					}
					if ($succ) {
						$this->getPlayListCacheByUid($uid)->flush();
						$this->noticeClient(1, $msgOpt);
					}
				}
				break;
		}
		$this->noticeClient(0, $msgOpt);
	}

	


	public function delAction()
	{
		$id = trim($this->mRequest->i);
		if (!Lamb_Utils::isInt($id)) {
			return ;
		}
		$db = Lamb_App::getGlobalApp()->getDb();
		if ($db->exec("delete from want where id = $id")) {
			$this->mResponse->redirect($this->mLinkRouter->router('', array('id' => 'member', 'action' => 'want')));
		}
	}



	/**
	 * @param int $uid
	 * @return Lamb_Cache_Interface
	 */
	public function getPlayListCacheByUid($uid)
	{
		return Ttkvod_Cache_Factory::getCache()->setIdentity(__CLASS__. '_getPlayListCacheByUid_' . $uid)->setCacheTime(24 * 3600);
	}
	
	/**
	 * @param int $uid
	 * @return string
	 */
	public function getPlayListCacheData($uid)
	{
		$cache = $this->getPlayListCacheByUid($uid);
		if ($cache->isCached()) {
			return $cache->read();
		}
		
		$data = '';
		$playlist = $this->mModel->getNetPlayList($uid);
		
		$result = array();
		
		if ($playlist) {

			foreach ($playlist as $key => $item) {
				$item['n'] = Lamb_App_Response::encodeURIComponent($item['n']);
				$result[$item['t']][] =  $item;
			}

			//$data = json_encode($result);
		}

		$cache->write($result);
		
		return $result;
	}
	
	/**
	 * @param string $ip
	 * @return boolean
	 */
	public function isClientInForbinIps($ip = null)
	{
		if (null === $ip) {
			$ip = Ttkvod_Utils::getRealIp();
		}
		return strpos($this->mSiteCfg['register']['forbin_ips'], $ip) !== false;
	}

	/**
	 * @param string $ip
	 * @param int $type 1 - username 2 - email
	 * @return boolean
	 */
	public function isClientInForbinInfo($info, $type = 1) 	
	{
		$forbins = explode(',', $this->mSiteCfg['register'][ $type == 1 ? 'forbin_usernames' : 'forbin_email']);
		foreach ($forbins as $item) {
			if (strpos($info, $item) !== false) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return memberControllor
	 */
	public function checkPurview()
	{
		if (!$this->mModel->isLogin()) {
			$this->mResponse->redirect($this->mLinkRouter->router('', array('id' => 'index')));
		}	
		$this->mUserId = $_SESSION['_UID_'];
		$this->mUsername = $_SESSION['_USERNAME_'];
	}
}