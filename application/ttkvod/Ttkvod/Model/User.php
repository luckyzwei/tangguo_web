<?php
class Ttkvod_Model_User
{
	const E_USERNAME_EXISTS = -1;
	
	const E_EMAIL_EXISTS = -2;
	
	const E_EMAIL_ILLEGAL = - 3;
	
	const E_FAIL = 0;
	
	const S_OK = 1;
	
	const T_USERNAME = 1;
	
	const T_EMAIL = 2;
	
	const T_UID = 4;
	
	const T_IS_LOCK = 8;
	
	/**
	 * @var string
	 */
	protected $mHashCookieName = '__d__';
	
	//protected $cookie_username = 'cookie_username';
	
	//protected $cookie_password = 'cookie_password';
	
	protected $cookie_uid = '__e__';
	
	/**
	 * @var string
	 */
	protected $mUsernameCookieName = '__c__';
	
	/** 
	 * @var int
	 */
	protected $mCookieLifeTime;
	
	public function __construct()
	{
		$this->mCookieLifeTime = 30 * 24 * 3600;
	}
	
	/**
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @return int
	 */
	public function add( $username, $password, $email, &$salt = null)
	{
		if (!Lamb_Utils::isEmail($email)) {
			return self::E_EMAIL_IILEGAL;
		}
		if ($this->get($email, self::T_EMAIL)) {
			return self::E_EMAIL_EXISTS;
		}
		if ($this->get($username, self::T_USERNAME)) {
			return self::E_USERNAME_EXISTS;
		}
		$sql = 'insert into member ( username, registerTime, regip, salt, password, email) values
				( :username, :registerTime, :regip, :salt, :password, :email)';
		$salt = $this->createSalt();
		$password = md5(md5($password) . $salt);
		$aPrepareSource = array(
			':username' => array($username, PDO::PARAM_STR),
			':registerTime' => array(time(), PDO::PARAM_STR),
			':regip' => array(Ttkvod_Utils::getRealIp(), PDO::PARAM_STR),
			':salt' => array($salt, PDO::PARAM_STR),
			':password' => array($password, PDO::PARAM_STR),
			':email' => array($email, PDO::PARAM_STR)
		);
		$db = Lamb_App::getGlobalApp()->getDb();
		if (!$db->quickPrepare($sql, $aPrepareSource, true)) {
			return 0;
		}
		
		return $db->lastInsertId();
	}
	
	/**
	 * @param string $val
	 * @param int $type T_USERNAME | T_EMAIL
	 * @return boolean
	 */
	public function delete($val, $type)
	{
		$sql = 'delete from member where ';
		if ($type == self::T_USERNAME) {
			$sql .= 'username=?';
		} else {
			$sql .= 'email=?';
		}
		return Lamb_App::getGlobalApp()->getDb()->quickPrepare($sql, array(1 => array($val, PDO::PARAM_STR)), true);
	}
	
	/**
	 * @param array $updates
	 * @param string $val
	 * @param int $type T_USERNAME | T_EMAIL | T_UID
	 * @return int
	 */
	public function update(array $updates, $val, $type = self::T_USERNAME)
	{
		if(!($user = $this->get($val, $type, true))){
			return self::E_FAIL;
		}
		if (isset($updates['username']) && $this->get($updates['username'], self::T_USERNAME, false, $user['uid'])) {
			return self::E_USERNAME_EXISTS;
		}
		if (isset($updates['email']) && $this->get($updates['email'], self::T_EMAIL, false, $user['uid'])) {
			return self::E_EMAIL_EXISTS;
		}
		if (isset($updates['password'])) {
			$updates['password'] = md5(md5($updates['password']) . $user['salt']);
		}
		unset($updates['uid'], $updates['salt']);
		$table = new Lamb_Db_Table('member');
		return $table->setOrGetWhere('uid=' . $user['uid'])
			  		 ->set($updates)
			  		 ->execute();
	}
	
	/**
	 * @param stirng $password
	 * @param string $val
	 * @param int $type T_USERNAME | T_EMAIL(reserve)
	 * @return int 0 - not found 1 - new found 2 - old found
	 */
	public function login($password, $val, $type = self::T_USERNAME, &$ret)
	{
		if ($ret = $this->get($val, $type, true)) {
			if (md5(md5($password) . $ret['salt']) == $ret['password']) {
				return 1;
			} else {
				return -1;
			}
		} 

		return 0;
	}
	
	/**
	 * @return boolean
	 */
	public function isLogin(&$info = null)
	{
		@session_start();
		if (isset($_SESSION['_IS_LOGIN_']) && $_SESSION['_IS_LOGIN_']) {
			return true;
		}
		$cfg = Lamb_Registry::get(CONFIG);
		$cookie_key = $cfg['cookie_key'];
		$req = Lamb_App::getGlobalApp()->getRequest();
		$username = Lamb_App_Response::decodeURIComponent($req->getCookie($this->mUsernameCookieName, ''));
		$hash = Lamb_Utils::authcode($req->getCookie($this->mHashCookieName, ''), $cookie_key);
		
		
		
		if (empty($username) || empty($hash)) {
			return false;
		}
		
		$arr = explode('|', $hash);
		$password = $arr[0];
		$uid = $arr[1];

		if (!Lamb_Utils::isInt($uid, true) || empty($password) || !($info = $this->get($uid, self::T_UID, true))
			|| $info['password'] != $password || $info['username'] != $username) {
			return false;
		}
		
		$_SESSION['_IS_LOGIN_'] = true;
		$_SESSION['_USERNAME_'] = $info['username'];
		$_SESSION['_UID_'] = $info['uid'];
		$_SESSION['_LLOGINTIME'] = $info['loginTime'] ? $info['loginTime'] : '';
		$_SESSION['_LLOGINIP'] = $info['loginip'];
		
		return true;
	}
	
	/**
	 * @param array $userinfo
	 * @return Ttkvod_Model_User
	 */
	public function serialize(array $userinfo)
	{
		@session_start();
		$cfg = Lamb_Registry::get(CONFIG);
		$res = Lamb_App::getGlobalApp()->getResponse();
		$domain = $cfg['domain'];
		$cookie_key = $cfg['cookie_key'];
		unset($cfg);

		$res->setcookie($this->mUsernameCookieName, Lamb_App_Response::encodeURIComponent($userinfo['username']), 
						$this->mCookieLifeTime, $domain);
		
		$res->setcookie($this->mHashCookieName, Lamb_Utils::authcode($userinfo['password'] . '|' . $userinfo['uid'], $cookie_key, 'ENCODE'),
						$this->mCookieLifeTime, $domain);
						
		$res->setcookie($this->cookie_uid, $userinfo['uid'], $this->mCookieLifeTime, $domain);
									
						
		$_SESSION['_IS_LOGIN_'] = true;
		$_SESSION['_USERNAME_'] = $userinfo['username'];
		$_SESSION['_UID_'] = $userinfo['uid'];
		$_SESSION['_LLOGINTIME'] = $userinfo['loginTime'] ? $userinfo['loginTime'] : '';
		$_SESSION['_LLOGINIP'] = $userinfo['loginip'];
		return $this;
	}
	
	/**
	 * @return void
	 */
	public function loginout()
	{
		@session_start();
		$cfg = Lamb_Registry::get(CONFIG);
		$res = Lamb_App::getGlobalApp()->getResponse();
		$domain = $cfg['domain'];
		$res->setcookie($this->mUsernameCookieName, '', -1, $domain);	
		$res->setcookie($this->mHashCookieName, '', -1, $domain);
		$res->setcookie($this->cookie_uid, '', -1, $domain);	
		
		unset($_SESSION['_IS_LOGIN_'],$_SESSION['_USERNAME_'],$_SESSION['_UID_'],$_SESSION['_LLOGINTIME'],$_SESSION['_LLOGINIP']);	
	}

	/**
	 * @param string $val
	 * @param int $type T_USERNAME | T_EMAIL | T_UID
	 * @return Ttkvod_Model_User
	 */	
	public function updateLoginInfo($val, $type = self::T_USERNAME)
	{
		$this->update(array('loginTime' => time(), 'loginip' => Ttkvod_Utils::getRealIp()), $val, $type);
		return $this;
	}
	
	/**
	 * @param mixed  $val
	 * @param int $type (T_USERNAME | T_IS_LOCK) | (T_EMAIL | T_IS_LOCK)
	 * @param boolean $isGetAll
	 * @param int $notIncludeUid
	 * @return null | int (isGetAll = false) | array (isGetAll = true)
	 */
	public function get($val, $type = self::T_USERNAME, $isGetAll = false, $notIncludeUid = 0)
	{
		$type = (int)$type;
		$sql = 'select ' . ($isGetAll ? '*' : 'uid') . ' from member where ';
		$aPrepareSource = array();
		if ($type & self::T_USERNAME) {
			$sql .= 'username=:username';
			$aPrepareSource[':username'] = array($val, PDO::PARAM_STR);
		} else if ($type & self::T_EMAIL) {
			$sql .= 'email = :email';
			$aPrepareSource[':email'] = array($val, PDO::PARAM_STR);
		} else if ($type & self::T_UID) {
			$sql .= 'uid=:uid';
			$aPrepareSource[':uid'] = array($val, PDO::PARAM_INT);
		} else {
			return null;
		}
		if ($type & self::T_IS_LOCK) {
			$sql .= ' and status=:status';
			$aPrepareSource[':status'] = array(1, POD::PARAM_INT);
		}
		if (!($type & self::T_UID) && $notIncludeUid > 0) {
			$sql .= ' and uid != :niuid';
			$aPrepareSource['niuid'] = array($notIncludeUid, PDO::PARAM_INT);
		}
		$ret = Lamb_App::getGlobalApp()->getDb()->getNumDataPrepare($sql, $aPrepareSource, true);
		if ($ret['num'] != 1) {
			return null;
		}
		return $isGetAll ? $ret['data'] : $ret['data']['uid'];
	}

	/** 
	 * @param mixed $val
	 * @param int $type (T_USERNAME | T_UID) | T_IS_LOCK
	 * @return null | int (isGetAll = false) | array (isGetAll = true)
	 */
	public function getOld($val, $type, $isGetAll = false)
	{
		$type = (int)$type;
		$sql = 'select ' . ($isGetAll ? '*' : 'uid') . ' from member_bak where ';
		$aPrepareSource = array();
		if ($type & self::T_USERNAME) {
			$sql .= 'username=?';
			$aPrepareSource[1] = array($val, PDO::PARAM_STR);
		} else if ($type & self::T_UID) {
			$sql .= 'uid=?';
			$aPrepareSource[1] = array($val, PDO::PARAM_INT);
		} else {
			return null;
		}
		if ($type & self::T_IS_LOCK) {
			$sql .= ' and status=?';
			$aPrepareSource[2] = array(1, PDO::PARAM_INT);
		}
		$ret = Lamb_App::getGlobalApp()->getDb()->getNumDataPrepare($sql, $aPrepareSource, true);
		if ($ret['num'] != 1) {
			return null;
		}
		return $isGetAll ? $ret['data'] : $ret['data']['uid'];
	}
	
	/**
	 * @param string $username
	 * @param string $password
	 * @return null | array
	 */
	public function checkOld($username, $password)
	{
		$ret = $this->getOld($username, self::T_USERNAME, true);
		if (!$ret) {
			return null;
		}
		if (md5(md5($password) . $ret['salt']) != $ret['password']) {
			return null;
		}
		return $ret;
	}
	
	/**
	 * @param string $newemail
	 * @param string $val
	 * @param int $type T_USERNAME | T_UID
	 * @return int -1 not found -2 email illegal -3 email exists -4 username exists
	 */
	public function fromOldToNew($newemail, $val, $type = self::T_UID)
	{
		if (!($ret = $this->getOld($val, $type, true))) {
			return -1;
		}
		if (!Lamb_Utils::isEmail($newemail)) {
			return -2;
		}
		if ($this->get($newemail, self::T_EMAIL)) {
			return -3;
		}
		if ($this->get($ret['username'])) {
			return -4;
		}
		$ret['email'] = $newemail;
		$db = Lamb_App::getGlobalApp()->getDb();
		$db->begin();
		$db->exec('set identity_insert member on');
		$table = new Lamb_Db_Table('member', Lamb_Db_Table::INSERT_MODE);
		$table->set($ret)->execute();
		$db->exec('delete from member_bak where uid=' . $ret['uid']);
		return $db->end();
	}
	
	/**
	 * @param int $uid
	 * @return null | array
	 */
	public function getNetPlayList($uid)
	{
		$ret = null;
		if (!($user = $this->get($uid, self::T_UID, true))) {
			return $ret;
		}
		$ret = array();
		if ($playlist = $user['netplaylist']) {
			eval("\$ret=$playlist;");
			if (!$ret || !is_array($ret)) {
				$this->clearNetPlayList($uid);
				return $ret;
			}
		}
		return $ret;
	}
	
	/**
	 * @param int $uid
	 * @return int
	 */
	public function clearNetPlayList($uid)
	{
		return $this->update(array('netplaylist' => ''), $uid, self::T_UID);
	}
	
	/**
	 * @param int $vid
	 * @param int $uid
	 * @param string $name
	 * @return int 0 - error -1 - full > 0 succ
	 */
	public function addNetPlayList($vid, $uid, $name = null, $time = null)
	{
		$vid = (int)$vid;
		
		if ( null === ($playlist = $this->getNetPlayList($uid)) ) {
			return 0;
		}
		
		$cfg = Lamb_Registry::get(CONFIG);
		$limitnum =  $cfg['member_net_playlist_num'];
		unset($cfg);
		if ($this->isVidInPlayList($vid, $playlist) === false && count($playlist) >= $limitnum) {
				return -1;
		}
		
		if (null === $name) {
			$data = Lamb_App::getGlobalApp()->getDispatcher()->loadControllor('itemControllor', true)->getCacheVideoInfoById($vid);
			if (count($data) != 1) {
				return 0;
			}
			$name = $data[0]['name'];
			$vedioPic = $data[0]['vedioPic'];
			$topType = $data[0]['type'];
		}
		$queue = new Lamb_Queue_UniqueKey($limitnum);
		$playlist = $queue->setOrGetData($playlist)->setOrGetSearchUniqueKeyCallback(array($this, 'isVidInPlayList'))
						  ->push(array('n' => $name, 'i' => $vid, 'pic' => $vedioPic, 'type' => $topType, 't' => $time ? $time : strtotime(date('Y-m-d'))))
						  ->setOrGetData();	
						  
		
		if (count($playlist) > 100) {
			$playlist = array_slice($playlist, 0, 100);
		}
		
		Ttkvod_Cache_Factory::getCache()->setIdentity(__CLASS__. '_getPlayListCacheByUid_' . $uid)->flush();		
		$playlist =  preg_replace('/[\r\n\s]/is', '', var_export($playlist, true));

		return $this->update(array('netplaylist' => $playlist), $uid, self::T_UID);
	}
	
	/** 
	 * @param int $vid
	 * @param int $uid
	 * @return int
	 */
	public function deleteNetPlayList($vid, $uid)
	{
		$vid = (int)$vid;
		
		if ( null === ($playlist = $this->getNetPlayList($uid)) ) {
			return 0;
		}

		if (($index = $this->isVidInPlayList($vid, $playlist)) === false) {
			return 0;
		}
		
		Ttkvod_Cache_Factory::getCache()->setIdentity(__CLASS__. '_getPlayListCacheByUid_' . $uid)->flush();
		unset($playlist[$index]);
		$playlist =  preg_replace('/[\r\n\s]/is', '', var_export($playlist, true));	
		return $this->update(array('netplaylist' => $playlist), $uid, self::T_UID);		
	}
	
	/**
	 * @param array | int $vid
	 * @param array $data
	 * @return false - not found int > index
	 */
	public function isVidInPlayList($vid, $data)
	{
		$ret = false;
		$vid = is_array($vid) ? $vid['i'] : $vid;
		foreach ($data as $key => $val) {
			if ($vid == $val['i']) {
				$ret = $key;
				break;
			}
		}
		return $ret;
	}
	
	/**
	 * @param int $min
	 * @param int $max
	 * @return string
	 */
	protected function createSalt($min = 5, $max = 10)
	{
		$ret = '';
		if ($min > $max) {
			$max = $min;
		}
		$key = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$len = rand($min, $max);
		$salt_len = strlen($key) - 1;
		for ($i = 1; $i <= $len; $i ++) {
			$ret .= $key{rand(0, $salt_len)};
		}
		return $ret;
	}
	
	/**
	 * @param int & $allCount
	 * @param int & $succnum
	 * @return boolean
	 */
	public static function translteDatetimeToInt($page, $pagesize = 100)
	{
		$app = Lamb_App::getGlobalApp();
		$db = $app->getDb();
		$sqlHelper = $app->getSqlHelper();
		$res = $app->getResponse();
		$aPrepareSource = array();
		$sql_up = 'update member set _loginTime=:loginTime,_registerTime=:registerTime where uid=:uid';
		$sql = $sqlHelper->getPageSql('select username,uid,registerTime,loginTime from member', $pagesize, $page);
		foreach ($db->query($sql) as $item) {
			$item['registerTime'] = strtotime($item['registerTime']);
			$item['loginTime'] = strtotime($item['loginTime']);
			$aPrepareSource[':uid'] = array($item['uid'], PDO::PARAM_INT);
			$aPrepareSource[':loginTime'] = array($item['loginTime'], PDO::PARAM_INT);
			$aPrepareSource[':registerTime']  = array($item['registerTime'], PDO::PARAM_INT);
			$msg = "uid : {$item['uid']}, username : {$item['username']}";
			if ($db->quickPrepare($sql_up, $aPrepareSource, true)){
				$res->fecho("$msg <b style='color:green'>修改成功</b><br/>");
			} else {
				$res->fecho("$msg <b style='color:red'>修改失败</b><br/>");
			}
		}
		return true;
	}
	
	public static function separatorRepeatUsers($page, $pagesize = 100)
	{
		$app = Lamb_App::getGlobalApp();
		$db = $app->getDb();
		$res = $app->getResponse();
		$sqlHelper = $app->getSqlHelper();
		$table = new Lamb_Db_Table('member_bak', Lamb_Db_Table::INSERT_MODE);
		static $sql = 'select * from member where email=:email';
		static $aPrepareSource = array();
		foreach($db->query($sqlHelper->getPageSql('select email from member group by email having count(uid) > 1', $pagesize, $page))->toArray() as $item) {
			$aPrepareSource[':email'] = array($item['email'], PDO::PARAM_STR);
			foreach ($db->quickPrepare($sql, $aPrepareSource) as $item2) {
				$msg = "uid : {$item2['uid']}, username : {$item2['username']}, email : {$item2['email']}";
				if ($table->set($item2)->execute()) {
					$res->fecho("$msg <b style='color:green'>添加成功</b><br/>");
				} else {
					$res->fecho("$msg <b style='color:red'>添加失败</b><br/>");
				}
			}
		}
		return true;
	}
	
	public static function cleanRepeatUsers($page, $pagesize = 100)
	{
		$app = Lamb_App::getGlobalApp();
		$db = $app->getDb();
		$sqlHelper = $app->getSqlHelper();
		$res = $app->getResponse();
		static $aPrepareSource = array();
		static $sql = 'delete from member where uid=:uid';
		foreach ($db->query($sqlHelper->getPageSql('select uid,username from member_bak', $pagesize, $page)) as $item) {
			$msg = "uid : {$item['uid']}, username : {$item['username']}";
			$aPrepareSource[':uid'] = array($item['uid'], PDO::PARAM_INT);
			if ($db->quickPrepare($sql, $aPrepareSource, true)) {
				$res->fecho("$msg <b style='color:green'>删除成功</b><br/>");
			} else {
				$res->fecho("$msg <b style='color:red'>删除失败</b><br/>");
			}
		}
		return true;
	}
}