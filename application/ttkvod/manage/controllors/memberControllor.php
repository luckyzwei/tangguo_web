<?php
class memberControllor extends Ttkvod_ManageControllor
{
	/**
	 * @var Ttkvod_Model_Video
	 */
	protected $mModel;
	
	public function __construct()
	{
		parent::__construct();
		$this->checkPurview();
		$this->mModel = new Ttkvod_Model_User;
	}
	
	public function getControllorName()
	{
		return 'member';
	}	

	public function adminAction()
	{
		if (!$this->checkIsAdmin()){
			$this->showMsg(array('msg' => '没有权限', 'url' => ''));
		}
		$page = trim($this->mRequest->p);
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		$sql = 'select id, name, lastip, lasttime, time, status, isdelete, isedit, isAdmin, islist from admin';
		
		$pageUrl = $this->mRouter->urlEx('member', 'admin') . '/p/';
		include $this->load('member_admin');
	}

	public function adminAjaxAction()
	{
		$id = $this->mRequest->id;
		$action = trim($this->mRequest->a);
		$val = trim($this->mRequest->v);
		$column = trim($this->mRequest->c);
		$ids = $this->mRequest->getPost('id');
		$name = trim($this->mRequest->name);
		$password = trim($this->mRequest->p);
		
		$admin = new Ttkvod_Model_Admin();
		if ($action == 'u') {
			$admin->update(array($column => $val, 'id' => $id));	
		} else if ($action == 'd') {
			if (is_array($ids) && count($ids) != 0) {
				foreach($ids as $itemId) {
					$admin->delete(array('id' => $itemId));
				}
			}	
			$this->showMsg(array('msg' => '删除成功', 'url' => $this->mRefer, 'level' => ''));
		} else if ($action == 'uu') {
			if ($admin->update(array('name' => $name, 'password' => md5($password), 'id' => $id), true)) {
				if ($id == $this->getWorkerId() && $this->checkIsAdmin()) {
					$_SESSION[$this->mSessionKeyUsername] = $name;
					$_SESSION[$this->mSessionKeyPassword] = md5($password);
				}
				$this->mResponse->eecho('succ');	
			}
			$this->mResponse->eecho('error');
		} else if ($action == 'add') {
			if ($admin->add(array('name' => $name, 'password' => md5($password), 'time' => time()))) {
				$this->mResponse->eecho('succ');	
			}
			$this->mResponse->eecho('error');
		}
	}

	public function logAction()
	{
		if (!$this->checkIsAdmin()){
			$this->showMsg(array('msg' => '没有权限', 'url' => ''));
		}
		$aid = trim($this->mRequest->aid);
		$keywords = trim($this->mRequest->keywords);
		$page = trim($this->mRequest->p);
		$query = trim($this->mRequest->query);
		$vid = trim($this->mRequest->vid);
		$stime = trim($this->mRequest->stime);
		$etime = trim($this->mRequest->etime);
		
		$this->getDate($stime, $etime);

		$sels = array_fill(0, 2, '');
		$selected = ' selected=selected';
		
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		$sql = 'select a.id, a.contents, a.time, b.name as adminname, moviename from record a, admin b where a.aid = b.id and a.aid = ' . $aid;
		
		if (Lamb_Utils::isInt($vid, true) || (Lamb_Utils::isInt($keywords, true) && $query == 'vid')) {
			if (Lamb_Utils::isInt($keywords, true)) {
				$vid = $keywords;
			}
			$sql .= ' and a.movieid = ' . $vid;
			$sels[0] = $selected;
		}
		if(!empty($keywords) && $query == 'name'){
			$sql .= ' and a.moviename like \'%' . $keywords .'%\'';
			$sels[1] = $selected;
		}
		
		$sql .= ' and a.time >= :stime and a.time <= :etime  order by time desc';

		$aPrepareSource = array(
			':stime' => array($stime, PDO::PARAM_INT),
			':etime' => array($etime, PDO::PARAM_INT)
		);
		
	
		$pageUrl = $this->mRouter->urlEx('member', 'log', array(
			'aid' => $aid,
			'keywords' => $keywords,
			'stime' => date('Y-m-d', $stime),
			'etime' => date('Y-m-d', $etime),
			'query' => $query
		)) . '/p/';
		include $this->load('member_log');
	}
	
	public function getDate(&$sDate, &$eDate)
	{
		
		
		$sDate = strtotime($sDate);
		$eDate   = strtotime($eDate);
		if (!$sDate || !$eDate) {
			$sDate = strtotime(date('Y-m-d 00:00:00', time())) - 24 * 3600 * 6;
			$eDate   = strtotime(date('Y-m-d 23:59:59', time()));
		}  else if($eDate){
			$eDate   = $eDate + 24 * 3600 - 1;
		}
		unset($sDate);
		unset($eDate);
	}			
	
	public function indexAction()
	{
		$page =trim($this->mRequest->p);
		$keywords = trim($this->mRequest->keywords);
		$query = trim($this->mRequest->query);
		$status = trim($this->mRequest->status);
		$action = trim($this->mRequest->ac);
		if ($action == 'old') {
			$sql = 'select * from member_bak where 1 = 1';
		} else {
			$sql = 'select * from member where 1 = 1';
		}
		$aPrepareSource = array();
		$sels = array_fill(0, 3, '');
		$SELECTED = ' selected="selected"';
		
		if (!Lamb_Utils::isInt($page)) {
			$page = 1;
		}
		
		if (!empty($keywords) && !empty($query)) {
			switch ($query) {
				case 'id':
					if (Lamb_Utils::isInt($keywords, true)) {
						$sql .= ' and uid=:id';
						$sels[0] = $SELECTED;
						$aPrepareSource[':id'] = array($keywords, PDO::PARAM_INT);
					}
					break;
				case 'username':
					$sql .= ' and username=:username';
					$sels[1] = $SELECTED;
					$aPrepareSource[':username'] = array($keywords, PDO::PARAM_STR);
					break;
				case 'email':
					$sql .= ' and email=:email';
					$sels[2] = $SELECTED;
					$aPrepareSource[':email'] = array($keywords, PDO::PARAM_STR);
					break;					
			}
		}
		
		if (Lamb_Utils::isInt($status)) {
			$sql .= ' and status=:status';
			if ($status != 1) {
				$status = 0;
			}
			$aPrepareSource[':status'] = array($status, PDO::PARAM_INT);
		}
		
		$sql .= ' order by registerTime desc';
		
		$pageUrl = $this->mRouter->urlEx('member', '', array('keywords' => $keywords,'query' => $query, 'status' => $status, 'ac' => $action)) . $this->mRouter->setUrlDelimiter() 
			. 'p' . $this->mRouter->setUrlDelimiter();

		include $this->load('member');
	}
	
	
	public function updateAction()
	{
		$action = trim($this->mRequest->ac);
		
		if ($this->mRequest->isPost()) {
			$username = $this->mRequest->getPost('username');
			$password = $this->mRequest->getPost('password');
			$email = $this->mRequest->getPost('email');
			$uid = $this->mRequest->getPost('uid');
			
			if (!Lamb_Utils::isInt($uid)) {
				$this->showMsg(array('msg' => '非法操作', 'url' => ''));
			}
			
			$data = array();
			
			if ($action == 'old') {
				if (empty($password)) {
					$this->showMsg(array('msg' => '请填写密码', 'url' => ''));
				}
				$data['password'] = $password;
				
				$user = $this->mModel->getOld($uid, Ttkvod_Model_User::T_UID, true);
				
				if (!$user) {
					$this->showMsg(array('msg' => '该用户不存在', 'url' => ''));
				}
				
				$newpassword = md5(md5($password) . $user['salt']);
				
				if (Lamb_App::getGlobalApp()->getDb()->quickPrepare('update member_bak set password=? where uid=?', 
						array( 1 => array($newpassword, PDO::PARAM_STR), 2 => array($uid, PDO::PARAM_INT)), true)) {
					$this->showMsg(array('msg' => '修改成功', 'url' => $this->mRefer));
				} else {
					$this->showMsg(array('msg' => '未知错误', 'url' => ''));
				}
				
			} else {
				if (empty($email) || empty($username)) {
					$this->showMsg(array('msg' => '邮箱和用户名都不能为空', 'url' => ''));
				}
				
				if (!Lamb_Utils::isEmail($email)) {
					$this->showMsg(array('msg' => '邮箱格式不正确', 'url' => ''));
				}
				$data['email'] = $email;
				$data['username'] = $username;
				
				if (!empty($password)) {
					$data['password'] = $password;
				}

				$ret = $this->mModel->update($data, $uid, Ttkvod_Model_User::T_UID);
				
				$msg = array('url' => '');
				if ($ret > 0) {
					$msg['msg'] = '修改成功';
					$msg['url'] = $this->mRefer;
				} else if ($ret == 0) {
					$msg['msg'] = '未知错误';
				} else if ($ret == -1) {
					$msg['msg'] = $username . ' 该用户名已经存在';
				} else if ($ret == -2) {
					$msg['msg'] = $email . ' 该邮箱已经存在';
				} 
				$this->showMsg($msg);				
			} 
		}
	}
	
	public function deleteAction()
	{
		$action = trim($this->mRequest->ac);
		$table = $action != 'old' ? 'member' : 'member_bak';
		
		if ($this->mRequest->isPost()) {
			$id = $this->mRequest->getPost('id');
			$db = Lamb_App::getGlobalApp()->getDb();
			
			foreach ($id as $item) {
				$db->quickPrepare('delete from ' . $table . ' where uid=?', array( 1 => array($item, PDO::PARAM_INT)), true);
			}
			$this->showMsg(array('msg' => '删除成功', 'url' => $this->mRefer, 'level' => ''));
		}
	}
	
	public function ajaxAction()
	{
		$action = trim($this->mRequest->ajaxAction);
		$id = trim($this->mRequest->id);
		$value = trim($this->mRequest->value);
		
		switch ($action) {
			case 'newstatus':
				if (Lamb_Utils::isInt($id) && in_array($value, array('0', '1'))) {
					if (Lamb_App::getGlobalApp()->getDb()->quickPrepare('update member set status=? where uid=?', 
							array( 1 => array($value, PDO::PARAM_INT), 2 => array($id, PDO::PARAM_INT)), true)) {
						$this->mResponse->eecho('succ');
					}
				}
				break;
		}
	}
	
	public function importAction()
	{	
		$uid = trim($this->mRequest->uid);
		$email = trim($this->mRequest->email);
		if ($this->mRequest->isPost()) {
		
			if (!Lamb_Utils::isInt($uid)) {
				$this->showMsg(array('msg' => '非法操作', 'url' => ''));
			}
			
			if (empty($email) || !Lamb_Utils::isEmail($email)) {
				$this->showMsg(array('msg' => '邮箱格式不正确', 'url' => ''));
			}
			
			$ret = $this->mModel->fromOldToNew($email, $uid);
			if ($ret !== true) {
				switch ($ret) {
					case -1:
						$this->showMsg(array('msg' => '用户不存在', 'url' => ''));
						break;
					case -2:
						$this->showMsg(array('msg' => '邮箱格式不正确', 'url' => ''));
						break;
					case -3:
						$this->showMsg(array('msg' => $email . ' 已被注册', 'url' => ''));
						break;
					case -4:
						$this->showMsg(array('msg' => '该帐号已经存在，请重新注册', 'url' => ''));
						break;
				}
			} else {
				$this->showMsg(array('msg' => '导入成功', 'url' => $this->mRefer));
			}
		}
	}
}