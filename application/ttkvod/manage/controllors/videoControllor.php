<?php
class videoControllor extends Ttkvod_ManageControllor
{
	/**
	 * @var Ttkvod_Model_Video
	 */
	protected $mModel;
	
	public function __construct()
	{
		parent::__construct();
		if ($this->mDispatcher->setOrGetAction() != 'outerSys') {
			$this->checkPurview();
		}
		$this->mModel = new Ttkvod_Model_Video;
	}
	
	public function getControllorName()
	{
		return 'video';
	}

	public function outerSysAction()
	{
		$id = trim($this->mRequest->id);
		if (!Lamb_Utils::isInt($id, true)) {
			return;
		}
		$this->loadClientControllor('itemControllor', true)->deleteCacheVideoInfoById($id);
		Ttkvod_Model_Notice::clearCacheByVid($id);
		if ($cache = $this->mModel->getPlayDataCache($id)) {
			$cache->flush();
		}
		$this->createVideoHtmlById($id);
	}		
	
	public function indexAction()
	{
		if (!$this->checkWebPurview('islist')) {
			$this->mResponse->eecho('没有权限');
		}
		$page = trim($this->mRequest->p);
		$keywords = trim($this->mRequest->keywords);
		$query = trim($this->mRequest->query);
		$video_type = trim($this->mRequest->type);
		$order = trim($this->mRequest->order);
		$orval = trim($this->mRequest->orval);
		//标记的状态

		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		$column = 'id,name,type,viewNum,monthNum,point,mark,actors,directors,updateDate,stortId,weekNum,area,tag,status';
		
		$sql = "select $column from vedio where 1=1";
		$aPrepareSource = array();
			
		$sels = array_fill(0, 4, '');
		$SELECTED = 'selected="selected"';
		if ($keywords && $query) {
			switch ($query) {
				case 'vedioname':
					$sels[0] = $SELECTED;
					$sql .= ' and name like :name';
					$aPrepareSource[':name'] = array('%'. $keywords . '%', PDO::PARAM_STR);
					break;
				case 'vedioid':
					if (Lamb_Utils::isInt($keywords)) {
						$sels[1] = $SELECTED;
						$sql .= ' and id=:id';
						$aPrepareSource[':id'] = array($keywords, PDO::PARAM_INT);
					}
					break;
				case 'tag':
					$sql = "select $column from vedio a,tag b,tagrelation c where a.id=c.vedioid and b.tagid=c.tagid and b.tagname=:tagname";
					$sels[2] = $SELECTED;
					$aPrepareSource[':tagname'] = array($keywords, PDO::PARAM_STR);
					break;
				case 'area':
					$sel[3] = $SELECTED;
					$sql .= ' and area like :area';
					$aPrepareSource[':area'] = array('%' . $keywords . '%', PDO::PARAM_STR);
					break;
			}
		}
		
		if (Lamb_Utils::isInt($video_type, true)) {
			$sql .= ' and type=:type';
			$aPrepareSource[':type'] = array($video_type, PDO::PARAM_INT);
		}

		if ($orval == 1) {
			$order_val = 'desc';
			$ornewval = 0;
		} else {
			$order_val = 'asc';
			$ornewval = 1;
		}
		switch ($order) {
			case 'status':
				if ($order_val == 'desc') {
					$sql .= ' and status=1';
				} else {
					$sql .= ' and status=0';
				}
				break;
			case 'stortId':
				$sql .= " order by stortId $order_val";
				break;
			case 'viewNum':
				$sql .= " order by viewNum $order_val";
				break;
			case 'point':
				$sql .= " order by point $order_val";
				break;
			case 'weekNum':
				$sql .= " order by weekNum $order_val";
				break;
			case 'monthNum':
				$sql .= " order by monthNum $order_val";
				break;
			default:
				$sql .= ' order by updateDate desc';
				break;
		}
		
		$publicUrl = $this->mRouter->urlEx('video', '', array('keywords' => $keywords,'query' => $query, 'type' => $video_type)) . $this->mRouter->setUrlDelimiter();
		$pageUrl = $publicUrl . $this->mRouter->url(array('orval' => $orval, 'order' => $order)) . $this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		
		include $this->load('video');
	}

	public function addAction()
	{
		if (!$this->checkWebPurview('isedit')) {
			$this->mResponse->eecho('没有权限');
		}
		if ($this->mRequest->isPost()) {
			$data = $this->mRequest->getPost('data');
			$this->addUpdateCheck($data);
			$ret = $this->mModel->add($data);
			
			if ($ret > 0) {
				$this->createVideoHtmlById($ret);
				$this->showMsg(array('msg' => '添加成功', 'url' => $this->mRefer));
			} else if ($ret == -1) {
				$this->showMsg(array('msg' => "影片名{$data['name']}已经存在", 'url' => ''));
			} else {
				$this->showMsg(array('msg' => '未知错误', 'url' => ''));
			}
			
		} else {
			include $this->load('video_edit');
		}
	}

	public function checkLog($vid)
	{
		if (!$this->checkIsAdmin()) {
			return '';
		}
		$url = $this->mRouter->urlEx('member', 'log', array('aid' => $this->getWorkerId())) . '/vid/' . $vid;
		return "<a href='{$url}'>日志</a>";
	}
	
	public function checkUpdatePurview($vid)
	{
		if (!$this->checkWebPurview('isedit')) {
			return '没有修改权限';
		}
		$url = $this->mRouter->urlEx('video', 'update', array('id' => $vid));
		return "<a href='{$url}' class='btn_edit'>编辑</a>";
	}
	
	public function updateAction()
	{
		if (!$this->checkWebPurview('isedit')) {
			$this->mResponse->eecho('没有权限');
		}
		$id = trim($this->mRequest->id);

		if (!Lamb_Utils::isInt($id, true) || !($aData = $this->mModel->get($id, Ttkvod_Model_Video::T_VID, true))) {
			$this->mResponse->eecho('illegal params');
		}
		
		if ($this->mRequest->isPost()) {
			$data = $this->mRequest->getPost('data');
			
			$ret = array();
			foreach (explode("\r\n", $data['play_data']) as $key => $val) {
				$temp = explode('|', $val); 
				$ret[$key]['id'] = $temp[0]; 
				$ret[$key]['mid'] = $temp[1]; 
				$ret[$key]['play_data'] = $temp[2]; 
				$ret[$key]['num'] 	 = $temp[3]; 
				$ret[$key]['extra']  = $temp[4]; 
				$ret[$key]['source'] = $temp[5]; 
				$ret[$key]['time'] = time(); 
			}
			
			$data['play_data'] = $ret;
			$rurl = $this->mRequest->getPost('rurl');

			$this->addUpdateCheck($data);
			$ret = $this->mModel->update($id, Ttkvod_Model_Video::T_VID, $data);
			if ($ret == 1) {
				$this->loadClientControllor('itemControllor', true)->deleteCacheVideoInfoById($id);
				//给用户发送更新通知
				/*if ($data['updateDate'] > $aData['updateDate']) {
					Ttkvod_Model_Notice::clearCacheByVid($id);
					if ($cache = $this->mModel->getPlayDataCache($id)) {
						$cache->flush();
					}
				}*/
				//$this->createVideoHtmlById($id);
				$this->showMsg(array('msg' => '修改成功', 'url' => empty($rurl) ? $this->mRefer : $rurl));
			} else if ($ret == '-1') {
				$this->showMsg(array('msg' => "影片名{$data['name']}已经存在", 'url' => ''));
			} else {
				$this->showMsg(array('msg' => '未知错误', 'url' => ''));
			}
			
		} else {
			$sql = 'select * from vedio_data where mid=?';
			$data['play_data'] = Lamb_App::getGlobalApp()->getDb()->quickPrepare($sql, array( 1 => array($id, PDO::PARAM_INT)))->toArray();
		
			if (count($data) <= 0) {
				$this->mResponse->eecho('illegal params');
			}
			
			$play_str = '';
			foreach($data['play_data'] as $item) {
				$play_str .= $item['id'] . '|' . $item['mid'] . '|' . $item['play_data'] . '|' . $item['num'] . '|' . $item['extra'] . '|' . $item['source'] . "\r\n";
			}
			
			$data['play_data'] = $play_str;
			$aData = $aData + $data;
			include $this->load('video_edit');
		}
	}
	
	public function customaddAction()
	{
		if ($this->mRequest->isPost()) {
			$id = $this->mRequest->getPost('id');
			if (is_array($id)) {
				foreach ($id as $item) {
					Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from customAdd where id=?', array( 1 => array($item, PDO::PARAM_INT)), true);
				}
				$this->showMsg(array( 'msg' => '删除成功', 'url' => $this->mRefer, 'level' => ''));
			}
		}
		$page = trim($this->mRequest->p);
		$keywords = trim($this->mRequest->keywords);
		$query = trim($this->mRequest->query);
		$videoType = trim($this->mRequest->vedioType);
		$action = trim($this->mRequest->ac);
		$sql = 'select a.*,b.username as username from customAdd a,member b where a.uid=b.uid';
		$aPrepareSource = array();
		$sels = array_fill(0, 3, '');
		$SELECTED = 'selected="selected"';		
		
		if ($action == 'check') {
			$id = trim($this->mRequest->id);
			
			if (!Lamb_Utils::isInt($id, true)) {
				return ;
			}
			
			$aData = Lamb_App::getGlobalApp()->getDb()->getNumDataPrepare('select * from customAdd where id=?', array( 1 => array($id, PDO::PARAM_INT)), true);
			$aData = $aData['data'];

			include $this->load('video_edit');
		} else {		
			if (!Lamb_Utils::isInt($page, true)) {
				$page = 1;
			}
			
			if (!empty($keywords) && !empty($query)) {
				switch ($query) {
					case 'vedioname':
						$sels[0] = $SELECTED;
						$sql .= ' and name like :name';
						$aPrepareSource[':name'] = array('%' . $keywords . '%', PDO::PARAM_STR);
						break;
					case 'uid':
						$sels[1] = $SELECTED;
						$sql .= ' and a.uid=:uid';
						$aPrepareSource[':uid'] = array($keywords, PDO::PARAM_INT);
						break;
					case 'username':
						$sels[2] = $SELECTED;
						$sql .= ' and b.username=:username';
						$aPrepareSource[':username'] = array($keywords, PDO::PARAM_STR);
						break;
				}
			}
			
			if (Lamb_Utils::isInt($videoType, true)) {
				$sql .= ' and topType=:topType';
				$aPrepareSource[':topType'] = array($videoType, PDO::PARAM_INT);
			}
			
			$sql .= ' order by intdate desc';
			
			$pageUrl = $this->mRouter->urlEx('video', 'customadd', array('keywords' => $keywords, 'query' => $query, 'vedioType' => $videoType)) .
					$this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
			include $this->load('video_customadd');
		}
	}

	public function wantAction()
	{
		$page = trim($this->mRequest->p);
		$keywords = trim($this->mRequest->keywords);
		$query = trim($this->mRequest->query);
		$action = trim($this->mRequest->ac);
		$aPrepareSource = array();
		$sels = array_fill(0, 3, '');
		$SELECTED = 'selected="selected"';	
		$sql = 'select w.id as id, w.vname as vname, w.uid as uid, w.date as date , w.ext as ext, w.url as url, w.notes as notes, m.username as uname from want w, member m where w.uid = m.uid';
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		if(isset($action) && $action == 'dele'){
			if ($this->mRequest->isPost()) {
				$ids = $this->mRequest->getPost('ids');
				foreach ($ids as $id) {
					if (Lamb_Utils::isInt($id, true)) {
						Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from want where id=?',array( 1 => array($id, PDO::PARAM_INT)));
					}	
				}
				$this->showMsg(array('msg' => '', 'url' => $this->mRefer, 'level' => '', 'fun' => 'void'));
			} 
		}
			
		if (!empty($keywords) && !empty($query)) {
			switch ($query) {
				case 'vedioname':
					$sels[0] = $SELECTED;
					$sql .= ' and vname like :vname';
					$aPrepareSource[':vname'] = array('%' . $keywords . '%', PDO::PARAM_STR);
					break;
				case 'uid':
					$sels[1] = $SELECTED;
					$sql .= ' and w.uid=:uid';
					$aPrepareSource[':uid'] = array($keywords, PDO::PARAM_INT);
					break;
				case 'username':
					$sels[2] = $SELECTED;
					$sql .= ' and m.username=:username';
					$aPrepareSource[':username'] = array($keywords, PDO::PARAM_STR);
					break;
			}
		}
				
		$sql .= ' order by date desc';
		
		$pageUrl = $this->mRouter->urlEx('video', 'want', array('keywords' => $keywords, 'query' => $query)) .
					$this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		include $this->load('video_want');
	}
	
	public function feedbackAction()
	{
		$page = trim($this->mRequest->p);
		$action = trim($this->mRequest->ac);
		$uid = trim($this->mRequest->uid);
		$id = trim($this->mRequest->id);
		$state = $this->mRequest->state;
		$reply = trim($this->mRequest->rp);
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		if ($action == 'sublist') {
			$sql = 'select * from feedback where 1=1';
			
			if (Lamb_Utils::isInt($uid, true)) {
				$sql .= ' and uid=:uid';
				$aPrepareSource[':uid'] = array($uid, PDO::PARAM_INT);
			}
		
			$pageUrl = $this->mRouter->urlEx('video', 'feedback', array('ac' => 'sublist', 'uid' => $uid)) . $this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		}else if ($action == 'dele') {
			if ($this->mRequest->isPost()) {
				$vid = $this->mRequest->getPost('vid');
				foreach ($vid as $id) {
					if (Lamb_Utils::isInt($id, true)) {
						Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from feedback where id=?',array( 1 => array($id, PDO::PARAM_INT)));
					}	
				}
				$this->showMsg(array('msg' => '', 'url' => $this->mRefer, 'level' => '', 'fun' => 'void'));
			}
			$id = trim($this->mRequest->id);
			if (Lamb_Utils::isInt($id, true)) {
				Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from feedback where id=?',array( 1 => array($id, PDO::PARAM_INT)));
				$this->showMsg(array('msg' => '', 'url' => $this->mRefer, 'level' => '', 'fun' => 'void'));
			}			
		}else if($action == 'ajax'){
			if (Lamb_Utils::isInt($id)) {
				if(Lamb_App::getGlobalApp()->getDb()->quickPrepare('update feedback set reply=?, isReply =1 where id=?',
					array( 1 => array($reply, PDO::PARAM_STR), 2 => array($id, PDO::PARAM_INT)))){
					$this->mResponse->eecho('succ');
				}
			}
		}else{
			$sql = 'select count(id) as num, sum(isReply) as r_num, uid, max(date) as date from feedback group by uid';
			$pageUrl = $this->mRouter->urlEx('video', 'feedback') . $this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		}
		include $this->load('video_feedback');
	}
	
	
	public function commentAction()
	{
		$vid = trim($this->mRequest->vid);
		$ip = trim($this->mRequest->ip);
		$content = trim($this->mRequest->cont);
		$username = trim($this->mRequest->name);
		$page = trim($this->mRequest->p);
		$sql = 'select * from comment where 1=1 ';
		$aPrepareSource = array();
		
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		if (Lamb_Utils::isInt($vid, true)) {
			$sql .= ' and vedioid=:vid';
			$aPrepareSource[':vid'] = array($vid, PDO::PARAM_INT);
		}
		
		if (!empty($ip)) {
			$sql .= ' and ip=:ip';
			$aPrepareSource[':ip'] = array($ip, PDO::PARAM_STR);
		}
		
		if (!empty($content)) {
			$sql .= ' and [content] like :cont';
			$aPrepareSource[':cont'] = array('%' . $content .  '%', PDO::PARAM_STR);
		}
		
		if (!empty($username)) {
			$sql .= ' and name=:name';
			$aPrepareSource[':name'] = array($username, PDO::PARAM_STR);
		}
		
		$sql .= ' order by time desc';
		$pageUrl = $this->mRouter->urlEx('video', 'comment', array('name' => $username, 'vid' => $vid, 'cont' => $content, 'ip' => $ip)) . $this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		
		include $this->load('video_comment');
	}
	
	public function commdeleAction($id = null)
	{
		$isSingle = false;
		if (null === $id) {
			$id = $this->mRequest->id;
			$isSingle = true;
		}
		if (is_array($id)) {
			foreach ($id as $item) {
				if (null !== $item) {
					$this->commdeleAction($item);
				}
			}
			$this->showMsg(array('msg' => '删除成功', 'url' => $this->mRefer, 'level' => ''));
		} else if (strpos($id, '|')) {
			$ret = explode('|', $id);
			
			if (count($ret == 2) && Lamb_Utils::isInt($ret[0], true) && Lamb_Utils::isInt($ret[1], true)) {
				if (Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from comment where vedioid=? and id=?', 
						array( 1 => array($ret[1], PDO::PARAM_INT), 2 => array($ret[0], PDO::PARAM_INT)), true)) {
						$this->loadClientControllor('itemControllor', true)->getCommentCache($ret[1]);	
				}
			}
			
			if ($isSingle) {
				$this->showMsg(array('msg' => '删除成功', 'url' => $this->mRefer, 'level' => ''));
			}
		}
	}
	
	public function fixedAction()
	{
		$action = trim($this->mRequest->ac);
		$keywords = trim($this->mRequest->keywords);
		$query = trim($this->mRequest->query);
		$videoType = trim($this->mRequest->vedioType);
		$page = trim($this->mRequest->p);
		$ortype = trim($this->mRequest->ot);
		$vid = @trim($this->mRequest->vid);
		
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		$aPrepareSource = array();
		$sels = array_fill(0, 2, '');
		$SELECTED = 'selected="selected"';		
		
		if ($action == 'sublist') {
			$sql = 'select videoid,a.id as id,intdate,reslutions,problems,isFinish,contact, b.name as name from fixinfo a,vedio b where a.videoid=b.id';
			
			if (Lamb_Utils::isInt($vid, true)) {
				$sql .= ' and a.videoid=:vid';
				$aPrepareSource[':vid'] = array($vid, PDO::PARAM_INT);
			}
			
			if (!empty($keywords)) {
				$sql .= ' and problems like :prob or reslutions :prob';
				$aPrepareSource[':prob'] = array('%' . $keywords . '%', PDO::PARAM_STR);
			}
			
			if ($query == 1) {
				$sels[0] = $SELECTED;
				$sql .= ' and isfinish=:isfinish';
				$aPrepareSource[':isfinish'] = array(1, PDO::PARAM_INT);
			} else if ($query == 2) {
				$sels[1] = $SELECTED;
				$sql .= ' and isfinish=:isfinish';
				$aPrepareSource[':isfinish'] = array(0, PDO::PARAM_INT);
			}

			$pageUrl = $this->mRouter->urlEx('video', 'fixed', array('ac' => 'sublist', 'keywords' => $keywords, 'query' => $query,
				'vid' => $vid)) . $this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		} else if ($action == 'dele') {
			if ($this->mRequest->isPost()) {
				$vid = $this->mRequest->getPost('vid');
				foreach ($vid as $id) {
					if (Lamb_Utils::isInt($id, true)) {
						Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from fixinfo where id=?',array( 1 => array($id, PDO::PARAM_INT)));
					}	
				}
				$this->showMsg(array('msg' => '', 'url' => $this->mRefer, 'level' => '', 'fun' => 'void'));
			} else {
				if (Lamb_Utils::isInt($vid, true) && Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from fixinfo where id=?',
					array( 1 => array($vid, PDO::PARAM_INT)))) {
					$this->showMsg(array('msg' => '', 'url' => $this->mRefer, 'level' => '', 'fun' => 'void'));
				}
			}
		} else {
			$sql = 'select id,name,intdate,topType,num from vedio a,(select videoid,max(intdate) as intdate,count(id) as num from fixinfo group by videoid) b where id=videoid ';		
			
			if (!empty($keywords) && !empty($query)) {
				switch ($query) {
					case 'vedioname':
						$sels[0] = $SELECTED;
						$sql .= ' and name=:name';
						$aPrepareSource[':name'] = array($keywords, PDO::PARAM_STR);
						break;
					case 'vid':
						if (Lamb_Utils::isInt($keywords, true)) {
							$sels[1] = $SELECTED;
							$sql .= ' and id=:id';
							$aPrepareSource[':id'] = array($keywords, PDO::PARAM_INT);
						}
						break;
				}
			}
			
			if (Lamb_Utils::isInt($videoType)) {
				$sql .= ' and topType=:toptype';
				$aPrepareSource[':toptype'] = array($videoType, PDO::PARAM_INT);
			}
			
			if ($ortype == '1') {
				$sql .= ' order by num desc';
			} else {
				$sql .= ' order by intdate desc';
			}
			
			$publicUrl = $this->mRouter->urlEx('video', 'fixed', array('keywords' => $keywords, 'query' => $query,
				'vedioType' => $videoType)) . $this->mRouter->setUrlDelimiter();
			$pageUrl = $publicUrl . $this->mRouter->url(array('ot' => $ortype)) . $this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		}
		include $this->load('video_fixed');
	}

	public function ajaxAction()
	{
		if (!$this->checkWebPurview('isedit')) {
			$this->mResponse->eecho('error');
		}
		$subaction = trim($this->mRequest->ajaxAction);
		$value = trim($this->mRequest->value);
		$id = trim($this->mRequest->id);

		$logArr = array(
			'tag' => '看点',
			'area' => '地区',
			'type' => '类型',
			'islock' => '锁定',
			'weekNum' => '周人气',
			'stortId' => '推荐值',
			'viewNum' => '人气',
			'status' => '状态'
		);
		$val = $value;
		if($subaction == 'islock') {
			$val = $value == 1 ? '是' : '否'; 
		}
		$this->writeLog('' . $logArr[$subaction] . '项修改成 ' . $val, $id);
		
		switch ($subaction) {
			case 'type':
			case 'area':
			case 'tag':
				if($subaction == 'type') {
					$subaction = 'vedioType';
				}
				if (Lamb_Utils::isInt($id, true) && $this->mModel->update($id, Ttkvod_Model_Video::T_VID, array($subaction => $value)) == 1) {
					$this->loadClientControllor('itemControllor', true)->deleteCacheVideoInfoById($id);
					$this->createVideoHtmlById($id);
					$this->mResponse->eecho('succ');
				}
				break;
			case 'status':
			case 'islock':
			case 'weekNum':
			case 'stortId':
			case 'viewNum':
				if (Lamb_Utils::isInt($id) && $this->mModel->update($id, Ttkvod_Model_Video::T_VID, array($subaction => $value)) == 1) {
					$this->mResponse->eecho('succ');
				}
				break;
			case 'fixfin':
				if (Lamb_Utils::isInt($id)) {
					Lamb_App::getGlobalApp()->getDb()->quickPrepare('update fixinfo set isFinish=? where id=?',
						array( 1 => array($value == 1 ? 0 : 1, PDO::PARAM_INT), 2 => array($id, PDO::PARAM_INT)));
				}
				$this->showMsg(array('msg' => '', 'url' => $this->mRefer, 'level' => '', 'fun' => 'void'));
				break;
			case 'custchk':
				if (Lamb_Utils::isInt($id) && Lamb_Utils::isInt($value)) {
					if (Lamb_App::getGlobalApp()->getDb()->quickPrepare('update customAdd set isOp=1 where id=?',
						array( 1 => array($id, PDO::PARAM_INT)))) {
						$this->mResponse->eecho('succ');
					}
				}
				break;
		}
	}
	
	public function createAction()
	{
		if ($this->mRequest->isPost()) {
			$ids = $this->mRequest->getPost('id', null, false);
			foreach ($ids as $id) {
				$this->createVideoHtmlById($id);
			}
			$this->showMsg(array('msg' => '生成成功', 'url' => $this->mRefer, 'level' => ''));			
		}
	}
	
	public function deleteAction()
	{
		if (!$this->checkWebPurview('isdelete')) {
			$this->showMsg(array('msg' => '没有权限', 'url' => $this->mRefer, 'level' => ''));
		}
		if ($this->mRequest->isPost()) {
			$ids = $this->mRequest->getPost('id', null, false);
			$controllor = $this->loadClientControllor('itemControllor', true);
			foreach ($ids as $id) {
				if ($this->mModel->delete($id)) {
					$controllor->deleteCacheVideoInfoById($id);
				}
			}
			$this->showMsg(array('msg' => '删除成功', 'url' => $this->mRefer, 'level' => ''));
		}
	}
	
	public function uploadAction()
	{
		include $this->load('video_upload');
	}
	
	public function addUpdateCheck(&$data)
	{
		if (!isset($data['name']) || empty($data['name'])) {
			$this->showMsg(array('msg' => '请填写名称', 'url' => ''));
		}
		
		if (!isset($data['type']) || !Lamb_Utils::isInt($data['type'], true) || !isset($this->mSiteCfg['channels'])) {
			$this->showMsg(array('msg' => '影片类型不正确', 'url' => ''));
		}
		
		if (!isset($data['updateDate']) || ($temp = strtotime($data['updateDate'])) === false) {
			$this->showMsg(array('msg' => '更新时间格式不正确', 'url' => ''));
		}
		$data['updateDate'] = $temp;
		
		if (!isset($data['play_data']) || empty($data['play_data'])) {
			$this->showMsg(array('msg' => '播放种子不能为空', 'url' => ''));
		}
		
		if (!isset($data['vedioYear']) || ($temp = strtotime($data['vedioYear'])) === false) {
			$this->showMsg(array('msg' => '上映日期格式不正确', 'url' => ''));
		}
		$data['vedioYear'] = date('Y', $temp);
		
		if (!isset($data['viewNum'])) {
			$data['viewNum'] = 0;
		} else {
			$data['viewNum'] = (int)$data['viewNum'];
		}
		
		if (!isset($data['vedioPic']) || empty($data['vedioPic'])) {
			$data['vedioPic'] = $this->mSiteCfg['site_root'] . 'upload/nopic.gif';
		}	
		unset($data);
	}
	
	/**
	 * @param int $vid
	 * @return videoControllor
	 */
	public function createVideoHtmlById($vid)
	{
		static $model = null;
		if (null == $model) {
			$model = new Ttkvod_Model_Static;
		} 
		$model->createItem($vid)
			  ->flushCDN();
		return $this;
	}
	
	
	public function updateWantAction()
	{
		$id = trim($this->mRequest->i);
		$vname = trim($this->mRequest->v);
		$ext = trim($this->mRequest->e);
	
		if(Lamb_Utils::isHttp($ext)){
			$ext = "<a href=\"$ext\">$vname</a>";
		}
				
		if (Lamb_App::getGlobalApp()->getDb()->quickPrepare('update want set ext=:ext where id=:id',
			array(':ext' => array($ext, PDO::PARAM_STR), ':id' => array($id, PDO::PARAM_INT)))) {
			$this->mResponse->eecho('succ');
		}
	}
	
	/**
	 *	标记影片
	 *	$id int 影片id
	 *	$mobile_movie_id int 移动端影片id
	 *	$mark int 标记 1-移动端有， 2-移动端无
	 *
	 */
	public function markMovieAction()
	{	
		$id = trim($this->mRequest->id);
		$mark = trim($this->mRequest->mark);
		$mobile_movie_id = trim($this->mRequest->mobile_movie_id);
		
		if(!Lamb_Utils::isInt($id, true)){
			$this->mResponse->eecho('id_err');
		}
		
		if($mark != 1 && $mark != 2){
			$this->mResponse->eecho('mark_err');
		}
		
		if ($mark == 1 && !$mobile_movie_id) {
			$this->mResponse->eecho('mid_err');
		}
		
		if (!$mobile_movie_id) {
			$mobile_movie_id = 0;
		}
		
		$db = Lamb_App::getGlobalApp()->getDb();
		
		$sql = "select pc_movie_id,mobile_movie_id from work_mark where pc_movie_id =$id";
		$ret = $db->query($sql)->toArray();
		$time = time();
		
		if ($ret) {
			$res = $db->query("update work_mark set mark=$mark,time=$time,mobile_movie_id=$mobile_movie_id where pc_movie_id=$id");
		}else {
			$res = $db->query("insert into work_mark(pc_movie_id, mobile_movie_id, mark, time) values($id, $mobile_movie_id, $mark, $time)");
		}
		
		$db->query("update vedio set work_mark=$mark where id = $id");
		
		if ($res) {
			echo 1;die;
		}else {
			echo -1;die;
		}
	}
	
	public function getTtkDb()
	{
		//$dsn = 'sqlsrv:Server=127.0.0.1,1533;Database=ttk';
		$dsn = 'sqlsrv:Server=121.40.218.51,1543;Database=ttk';
		$user = 'm_ttkvod';
		$password = 's_a_d_m_y_s_9185~';

		try {
			$objInstance = new PDO($dsn, $user, $password);
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}

		return $objInstance;
	}
	
	/**
	 * 将影片基本信息加载到移动端
	 *	$id int 当前影片id
	 *
	 */
	public function pcToMobileAction() 
	{
		header("content-Type: text/html; charset=Utf-8"); 
		$map = array('name','actors','directors','area','mark','tag','year');
		
		$id = trim($this->mRequest->id);
		
		if(!Lamb_Utils::isInt($id, true)){
			$this->mResponse->eecho('id_err');
		}
	
		$ret = Lamb_App::getGlobalApp()->getDb()->query("select * from vedio where id = $id")->toArray();
		
		$desc = Lamb_App::getGlobalApp()->getDb()->query("select content from vedio_data where id = $id")->toArray();
		
		if (!$ret) {
			$this->mResponse->eecho('id_err');
		}
		$ret = $ret[0];
		
		$movie = array();
		$movie['type'] = $ret['topType'];
		
		if ($ret['topType'] == 3) {
			$movie['type'] = 4;
		}else if($ret['topType'] == 4) {
			$movie['type'] = 3;
		}
		
		preg_match('#\d+#', $ret['mark'], $preg_ret);
		
		if ($preg_ret) {
			$ret['mark'] = $preg_ret[0];
		}else if ($ret['mark'] == '完结') {
			$vedio_data = Lamb_App::getGlobalApp()->getDb()->query("select playData from vedio_data where id = $id")->toArray();

			if ($vedio_data) {
				$vedio_data = explode("\r", $vedio_data[0]['playData']);
				$ret['mark'] = count($vedio_data);
			}
			
		}
		
		$movie['mark'] = $ret['mark'] ? $ret['mark'] : '';
		
		$movie['name'] = $ret['name'];
		$movie['pic'] = 'http://imga.ttkvod.com'.$ret['vedioPic'];
		//$movie['year'] = $ret['vedioYear'] ? ($ret['vedioYear'] == '不详' ? 0 : $ret['vedioYear']) : 0;
		$movie['year'] = $ret['vedioYear'] ? $ret['vedioYear'] : 0;
		$movie['actors'] = $ret['actors'] ? $ret['actors'] : '未知';
		$movie['directors'] = $ret['directors'] ? $ret['directors'] : '未知';
		$movie['area'] = $ret['area'] ? $ret['area'] : '未知';
		$movie['is_end'] = $ret['isEnd'] ? $ret['isEnd'] : 0;
		$movie['update_time'] = time();
		$movie['tag'] = $ret['vedioType'];
		$movie['status'] = 0;
		$movie['pinyin'] =  Lamb_Utils::pinyin($ret['name']);
		$movie['search_code'] =  Ttkvod_Utils::encodeFullSearchStr($ret['name']);
		
		if ($desc) {
			$movie['description'] =  strip_tags(mb_convert_encoding($desc[0]['content'], "UTF-8","GBK"));
			$movie['description'] = str_replace("'", '"', $movie['description']);
			//$movie['actors'] = str_replace("'", '', $movie['actors']);
		}else{
			$movie['description'] = '';
		}
		//$movie['year'] = mb_convert_encoding($movie['year'], "UTF-8","GBK");
		
		foreach ($map as $v) {
			$movie[$v] = mb_convert_encoding($movie[$v], "UTF-8","GBK");
		}
		
		if (!is_numeric($movie['year'])) {
			$movie['year'] = 0;
		}
		
		$db = $this->getTtkDb();

		/*
		print_r("insert into movie(type,name,pic,year,actors,directors,area,mark,is_end,update_time,tag,status,pinyin,search_code,description) values({$movie['type']},'{$movie['name']}','{$movie['pic']}',{$movie['year']},'{$movie['actors']}','{$movie['directors']}','{$movie['area']}','{$movie['mark']}',{$movie['is_end']},{$movie['update_time']},'{$movie['tag']}',{$movie['status']},'{$movie['pinyin']}','{$movie['search_code']}','{$movie['description']}')");die;
		
		if(!$db->exec("insert into movie(type,name,pic,year,actors,directors,area,mark,is_end,update_time,tag,status,pinyin,search_code,description) values({$movie['type']},'{$movie['name']}','{$movie['pic']}',{$movie['year']},'{$movie['actors']}','{$movie['directors']}','{$movie['area']}','{$movie['mark']}',{$movie['is_end']},{$movie['update_time']},'{$movie['tag']}',{$movie['status']},'{$movie['pinyin']}','{$movie['search_code']}','{$movie['description']}')")) {
			$this->mResponse->eecho('error');
		}
		*/
		
		$sth= $db->prepare("insert into movie(type,name,pic,year,actors,directors,area,mark,is_end,update_time,tag,status,pinyin,search_code,description) values(:type,:name,:pic,:year,:actors,:directors,:area,:mark,:is_end,:update_time,:tag,:status,:pinyin,:search_code,:description)");
		$sth->bindParam(':type', $movie['type'], PDO::PARAM_INT);
		$sth->bindParam(':name', $movie['name'], PDO::PARAM_STR);
		$sth->bindParam(':pic', $movie['pic'], PDO::PARAM_STR);
		$sth->bindParam(':year', $movie['year'], PDO::PARAM_INT);
		$sth->bindParam(':actors', $movie['actors'], PDO::PARAM_STR);
		$sth->bindParam(':directors', $movie['directors'], PDO::PARAM_STR);
		$sth->bindParam(':area', $movie['area'], PDO::PARAM_STR);
		$sth->bindParam(':mark', $movie['mark'], PDO::PARAM_STR);
		$sth->bindParam(':is_end', $movie['is_end'], PDO::PARAM_INT);
		$sth->bindParam(':update_time', $movie['update_time'], PDO::PARAM_INT);
		$sth->bindParam(':tag', $movie['tag'], PDO::PARAM_STR);
		$sth->bindParam(':status', $movie['status'], PDO::PARAM_INT);
		$sth->bindParam(':pinyin', $movie['pinyin'], PDO::PARAM_STR);
		$sth->bindParam(':search_code', $movie['search_code'], PDO::PARAM_STR);
		$sth->bindParam(':description', $movie['description'], PDO::PARAM_STR);
		
		if (!$sth->execute()) {
			$this->mResponse->eecho('error');
		}
		
		$mid = $db->lastInsertId();

		//规范的tag值集
		$allTag = explode(' ', $movie['tag']);
			
		if ($allTag) {
			foreach ($allTag as $k => $v) {
				$allTag[$k] = trim($v);
			}
			
			$tag = str_replace(' ', "','", $movie['tag']);
			$tag = "'" . $tag. "'";

			//检查原来是否存在对应的tag
			$tags = $db->query("select tagid,tagname from tag where tagname in ({$tag})")->fetchAll();
			
			//有tag已经存在tag表中
			if ($tags) {
				$temp = array();
				$tempId = array();
				foreach ($tags as $v) {
					$temp[] = $v['tagname'];
					$tempId[] = $v['tagid'];
				}
				
				//获得不在tag表中的新tag
				$ret = array_diff($allTag, $temp); 
				
				if ($ret) {
					foreach ($ret as $v){
						$db->exec("insert into tag(tagname) values('{$v}')");
						$id = $db->lastInsertId();
						$db->exec("insert into tagrelation(tagid,mid) values($id, $mid)");
					}
				}
				
				foreach ($tempId as $v) {
					$db->exec("insert into tagrelation(tagid,mid) values($v, $mid)");
				}
			}else { //无tag存在tag表中(将所有新的tag插入tag表，并添加到tagrelation)
				foreach ($allTag as $v) {
					$db->exec("insert into tag(tagname) values('{$v}') ");
					$id = $db->lastInsertId();
					$db->exec("insert into tagrelation(tagid,mid) values($id, $mid)");
				}
			}
		}
		
		if ($mid) {
			echo $mid;die;
		}else{
			echo 'err';die;
		}
	}
	
	public function getMarkNum($data)
	{
		$num = explode("\r", $data);
		$num = count($num);
		return $num;
	}
	
	/**
	 *添加加密资源
	 *
	 */
	public function addsecretAction()
	{	
		$url = trim($this->mRequest->url);
		$sourceName = trim($this->mRequest->name);

		preg_match("/sohu|56|pptv|letv|iqiyi|youku|bilibili|kankan|m1905|tudou|qq|hunantv|cntv|baofeng|ysdq|tucao|qianmo|dyued|bddisk|diediao|Funshion|acfun|ledisk/i", $sourceName, $r);
		
		if (!$url){
			echo 'url err';die;
		}
		
		if (!$r){
			echo 'name err!'; die;
		}
		$url = $sourceName.'$'.$url;

		echo 'thls://'.Lamb_Utils::authcode(trim($url), $this->mSiteCfg['encode_key'], 'ENCODE');
	}
	/**
	 *添加加密资源
	 *
	 */
	public function testAction()
	{	die;
		$url = trim($this->mRequest->url);
		
		$code= Lamb_Utils::authcode(trim($url), $this->mSiteCfg['encode_key'], 'ENCODE');
		//$code = '03a61378aq/aqdprPyROkGx+hMWJnEMh7Xsemao9Hk';
		echo Lamb_Utils::authcode('0db99d5pys9Tzg4ZUAzidQGMIaEfUJEnyYjMMhf6fkxOKABd9ODkLoRZTqgzZrq9dfcoxF2dZANm5GyDgoWroSfF5aIQlCwPh99VHU', $this->mSiteCfg['encode_key']);
	}
}