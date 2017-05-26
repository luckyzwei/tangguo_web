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
		$this->mRightTemplate = 'member_index_main';
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
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_LOGIN_INFO_INCOMPLETE));
			}
			if (!Lamb_Utils::authcode($randkey, $this->mSiteCfg['form_rank_key'], 'DECODE', $this->mSiteCfg['form_rank_expire'])) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_RANDKEY_TIMEOUT));
			}
			$usermodel = new Ttkvod_Model_User;
			$ret = $usermodel->login($password, $username, Ttkvod_Model_User::T_USERNAME, $userinfo);
			if ($ret <= 0) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_USERNAME_PASSWORD_ILLEGAL));
			}
			if (!$userinfo['status']) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_USER_IS_LOCK));
			}
			if ($ret == 2) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::S_OLD_USER_LOGIN));
			}
			$usermodel->serialize($userinfo)
					  ->updateLoginInfo($username);
			$this->mResponse->eecho($this->clientJsMsgHandler(self::S_LOGIN_SUCC));
		}
		$this->mResponse->eecho($this->clientJsMsgHandler(self::E_HTTP_SUBMIT_ILLEGAL));
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
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_REGISTER_INFO_INCOMPLETE));
			}
			if (!Lamb_Utils::authcode($randkey, $this->mSiteCfg['form_rank_key'], 'DECODE', $this->mSiteCfg['form_rank_expire'])) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_RANDKEY_TIMEOUT));
			}			
			$namelen = strlen($username);
			$emaillen = strlen($email);
			if ($password != $password2) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_TWO_PASSWORD_ERROR));
			}
			if ($namelen > 200) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_USERNAME_TOO_LONG));
			}
			if ($emaillen > 200) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_EMAIL_TOO_LONG));
			}
			if ($namelen < 4 ) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_USERNAME_TOO_SHORT));
			}
			if ($emaillen < 6) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_EMAIL_TOO_SHORT));
			}
			if (!Lamb_Utils::isEmail($email)) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_EMAIL_ILLEGAL));
			}
			if ($this->isClientInForbinIps()) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_CLIENT_IP_IS_FORBIN));
			}
			if ($this->isClientInForbinInfo($username)) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_CLIENT_USERNAME_IS_FORBIN));
			}
			if ($this->isClientInForbinInfo($email, 2)) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_CLIENT_EMAIL_IS_FORBIN));
			}
			$usermodel = $this->mModel;
			$ret = $usermodel->add($username, $password, $email, $salt);
			if ($ret == Ttkvod_Model_User::E_USERNAME_EXISTS) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_USERNAME_IS_EXISTS));
			}
			if ($ret == Ttkvod_Model_User::E_EMAIL_EXISTS) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_EMAIL_IS_EXISTS));
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
				$this->mResponse->eecho($this->clientJsMsgHandler(self::S_REGISTER_SUCC));
			}
			
		}
		$this->mResponse->eecho($this->clientJsMsgHandler(self::E_HTTP_SUBMIT_ILLEGAL));
	}
	
	public function loginoutAction()
	{
		$isnotredirect = trim($this->mRequest->isnr);
		$this->mModel->loginout();
		if (!$isnotredirect) {
			$this->mResponse->redirect($this->mLinkRouter->router('', array('id' => 'index')));
		}
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
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_IMPORT_ILLEGAL));
			} 
			if (!Lamb_Utils::authcode($randkey, $this->mSiteCfg['form_rank_key'], 'DECODE', $this->mSiteCfg['form_rank_expire'])) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_RANDKEY_TIMEOUT));
			}
			if (empty($email) || !Lamb_Utils::isEmail($email)) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_EMAIL_ILLEGAL));
			}
			
			$usermodel = $this->mModel;
			if (!($user = $usermodel->checkOld($username, $password))) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_USER_IS_NOT_EXIST));
			}
			if (!$user['status']) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_USER_IS_LOCK));
			}
			if ($usermodel->get($email, Ttkvod_Model_User::T_EMAIL)) {
				$this->mResponse->eecho($this->clientJsMsgHandler(self::E_EMAIL_IS_EXISTS));
			}
			if ($usermodel->fromOldToNew($email, $username, Ttkvod_Model_User::T_USERNAME) > 0) {
				$user['email'] = $email;
				$usermodel->serialize($user)
					  	  ->updateLoginInfo($username);				
				$this->mResponse->eecho($this->clientJsMsgHandler(self::S_IMPORT_SUCC));
			}
		}
		$this->mResponse->eecho($this->clientJsMsgHandler(self::E_IMPORT_ILLEGAL));
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
		$sql = 'select a.vedioId as vedioId, a.date as date, name as vedioName from favorites a,vedio b where a.vedioId=b.id and a.userId = :uid order by date desc';
		$aPrepareSource = array(':uid' => array($this->mUserId, PDO::PARAM_INT));
		$pageUrlPrev = $this->mLinkRouter->router('', array('id' => 'member', 'action' => 'fav'));
		$firstPageUrl = $this->getPageUrlTempalte(self::PT_FIRSTPAGE_URL, $pageUrlPrev);
		$lastPageUrl = $this->getPageUrlTempalte(self::PT_LASTPAGE_URL, $pageUrlPrev);
		$pageUrl = $this->getPageUrlTempalte(self::PT_PAGE_URL, $pageUrlPrev);
		
		$this->mRightTemplate = 'member_index_fav';
		include $this->load('member_index');		
	}
	
	public function sendAction()
	{
		$this->checkPurview();
		$msg = trim($this->mRequest->msg);
		if ($this->mRequest->isPost() && ($aSendData = $this->mRequest->getPost('sendData', null, false))) {
				$this->addAjaxDomainScript();
				if (is_array($aSendData)) {
					if (strlen($aSendData['name']) <= 0) {
						$this->mResponse->eecho('<script>parent.showMsg("影片名称不能为空")</script>');
					}
					if (strlen($aSendData['name']) >= 300) {
						$this->mResponse->eecho('<script>parent.showMsg("影片名称长度不能超过300，一个汉字占2个长度")</script>');
					}
					if (!Lamb_Utils::isInt($aSendData['type'], true) || !in_array($aSendData['type'], array('1', '2', '3', '4'))) {
						$this->mResponse->eecho('<script>parent.showMsg("请选择影片分类")</script>');
					}
					if (strlen($aSendData['content']) <= 0) {
						$this->mResponse->eecho('<script>parent.showMsg("影片简介不能为空")</script>');
					}
					if (strlen($aSendData['name']) >= 1000) {
						$this->mResponse->eecho('<script>parent.showMsg("影片简介长度不能超过1000，一个汉字占2个长度")</script>');
					}
					if (strlen($aSendData['playData']) <= 0) {
						$this->mResponse->eecho('<script>parent.showMsg("影片种子不能为空")</script>');
					}				
					if (strlen($aSendData['playData']) >= 5000) {
						$this->mResponse->eecho('<script>parent.showMsg("影片种子长度不能超过5000，一个汉字占2个长度")</script>');
					}
					if (strlen($aSendData['actor']) >= 300) {
						$this->mResponse->eecho('<script>parent.showMsg("影片演员长度不能超过1000，一个汉字占2个长度")</script>');
					}		
					if (strlen($aSendData['dircetor']) >= 300) {
						$this->mResponse->eecho('<script>parent.showMsg("影片导演长度不能超过1000，一个汉字占2个长度")</script>');
					}
					if (strlen($aSendData['area']) >= 50) {
						$this->mResponse->eecho('<script>parent.showMsg("影片地区长度不能超过1000，一个汉字占2个长度")</script>');
					}
					if (strtolower($aSendData['pic']) == 'http://') {
						$aSendData['pic'] = '';
					}
					if (strlen($aSendData['pic']) >= 500) {
						$this->mResponse->eecho('<script>parent.showMsg("影片图片长度不能超过1000，一个汉字占2个长度")</script>');
					}	
					if (strlen($aSendData['pic']) >0 && !Lamb_Utils::isHttp($aSendData['pic']))	{
						$this->mResponse->eecho('<script>parent.showMsg("影片图片地址必须是远程地址")</script>');
					}
					$db = Lamb_App::getGlobalApp()->getDb();
					if ($db->getNumDataPrepare('select id from customAdd where name=? and uid=?', array(1 => array($aSendData['name'], PDO::PARAM_STR), 2 => array($this->mUserId, PDO::PARAM_INT))) > 0) {
						$this->mResponse->eecho('<script>parent.showMsg("您已经提交过此影片，请不要重复提交")</script>');
					}
					$strSql = 'insert into customAdd (name,actors,directors,vedioPic,intdate,playData,content,area,topType,uid) values (
								:name,:actors,:directors,:vedioPic,:intdate,:playData,:content,:area,:topType,:uid)';
					$aPrepareSource = array(
							':name' => array($aSendData['name'], PDO::PARAM_STR),
							':actors' => array($aSendData['actor'], PDO::PARAM_STR),
							':directors' => array($aSendData['dircetor'], PDO::PARAM_STR),
							':vedioPic' => array($aSendData['pic'], PDO::PARAM_STR),
							':intdate' => array(time(), PDO::PARAM_INT),
							':playData' => array($aSendData['playData'], PDO::PARAM_STR),
							':content' => array($aSendData['content'], PDO::PARAM_STR),
							':area' => array($aSendData['area'], PDO::PARAM_STR),
							':topType' => array($aSendData['type'], PDO::PARAM_INT),
							':uid' => array($this->mUserId, PDO::PARAM_INT)
						);	
					if ($db->quickPrepare($strSql, $aPrepareSource, true)) {
						$this->mResponse->eecho('<script>parent.location.href="' . $this->mLinkRouter->router('', array('id' => 'member', 'action' => 'send', 'msg' => 1)) . '"</script>');
					}																								
					else {
						$this->mResponse->eecho('<script>parent.showMsg("系统异常，请稍后重试")</script>');
					}										
				}
		} else {
			$this->mRightTemplate = 'member_index_send';
			include $this->load('member_index');	
		}
	}
	
	public function updateAction()
	{
		$this->addAjaxDomainScript();
		$vid = trim($this->mRequest->vid);
		
		if (!Lamb_Utils::isInt($vid, true)) {
			$this->mResponse->eecho(self::E_NO_LOGIN);
		}

		if (!$this->userCheck(true, $userinfo)) {
			$this->mResponse->eecho(self::E_NO_LOGIN);
		}
		
		$ret = Ttkvod_Model_Notice::limitAddNotice($userinfo['uid'], $vid);
		$this->mResponse->eecho($ret < 0 ? self::E_UPDATE_FULL : ($ret == 0 ? self::E_UPDATE_EXISTS : self::S_UPDATE_SUCC));
	}
	
	public function updatelistAction()
	{
		$this->checkPurview();
		$ac = trim($this->mRequest->ac);
		$vid = trim($this->mRequest->vid);
		$delsucc = false;
		
		if ($ac == 'del' && Lamb_Utils::isInt($vid, true)) {
			$delsucc = Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from notice where uid=? and vid=?',
						array( 1 => array($this->mUserId, PDO::PARAM_INT), 2 => array($vid, PDO::PARAM_INT)),
						true);
		}
		
		$newUpdateData = Ttkvod_Model_Notice::getUpdateVideoInfos($this->mUserId, $isHas);
		$newUpdateNum = count($newUpdateData);
		$this->mRightTemplate = 'member_updatelist';
		$sql = 'select id,name,time,mark from vedio a,notice b where id=vid and uid=?';
		$aPrepareSource = array( 1 => array($this->mUserId, PDO::PARAM_INT));
		include $this->load('member_index');	
	}
	
	public function getupinfoAction()
	{
		header('Content-type:text/html;charset=' . $this->mApp->getCharset());
		$this->addAjaxDomainScript();
		if (!$this->mModel->isLogin()) {
			$this->mResponse->eecho('error');
		}
		$newUpdateData = Ttkvod_Model_Notice::getUpdateVideoInfos($_SESSION['_UID_'], $isHas);
		if ($isHas) {
			/*foreach ($newUpdateData as $key => $val) {
				$newUpdateData[$key]['mark'] = Lamb_App_Response::encodeURIComponent($val['mark']);
				$newUpdateData[$key]['name'] = Lamb_App_Response::encodeURIComponent($val['name']);
			}
			$this->mResponse->eecho(json_encode($newUpdateData));*/
			$this->mResponse->eecho(count($newUpdateData));
		} else {
			$this->mResponse->eecho('error');
		}
	}
	
	public function playlistAction()
	{
		$action = trim($this->mRequest->ac);
		$vid = trim($this->mRequest->vid);
		header('Content-type:text/html;charset=' . $this->mApp->getCharset());
		$this->addAjaxDomainScript();
		if (!$this->mModel->isLogin()) {
			$this->mResponse->eecho('error');
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
					$this->mResponse->eecho($ret);					
				}
				break;
			case 'clear':
				if ($this->mModel->clearNetPlayList($uid)) {
					$this->getPlayListCacheByUid($uid)->flush();
					$this->mResponse->eecho(1);
				}
				break;
			case 'get':
				if ($str = $this->getPlayListCacheData($uid)) {
					$this->mResponse->eecho($str);
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
						$this->mResponse->eecho(1);
					}
				}
				break;
		}
		$this->mResponse->eecho('error');
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
		if ($playlist) {
			foreach ($playlist as $key => $item) {
				$playlist[$key]['n'] = Lamb_App_Response::encodeURIComponent($item['n']);
			}
			$data = json_encode($playlist);
		}
		$cache->write($data);
		return $data;
	}
	
	/**
	 * @param int $errorno
	 * @return string
	 */
	public function clientJsMsgHandler($errorno)
	{
		$action = $this->mDispatcher->setOrGetAction();
		return "<script>parent.msgHandler('$action', $errorno);</script>";
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