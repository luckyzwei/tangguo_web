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
		$video_type = trim($this->mRequest->vedioType);
		$order = trim($this->mRequest->order);
		$orval = trim($this->mRequest->orval);
		
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		$column = 'id,name,topType,viewNum,monthNum,point,mark,vedioType,updateDate,isLock,stortId,weekNum,area,tag,status';
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
			$sql .= ' and topType=:toptype';
			$aPrepareSource[':toptype'] = array($video_type, PDO::PARAM_INT);
		}
		
		if ($orval == 1) {
			$order_val = 'desc';
			$ornewval = 0;
		} else {
			$order_val = 'asc';
			$ornewval = 1;
		}
		switch ($order) {
			case 'lock':
				if ($order_val == 'desc') {
					$sql .= ' and islock>0';
				} else {
					$sql .= ' and islock=0';
				}
				//$aPrepareSource[':islock'] = array($order_val == 'desc' ? 1 : 0, PDO::PARAM_INT);
				break;
			case 'status':
				if ($order_val == 'desc') {
					$sql .= ' and status=1';
				} else {
					$sql .= ' and status=0';
				}
				//$aPrepareSource[':islock'] = array($order_val == 'desc' ? 1 : 0, PDO::PARAM_INT);
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
		
		$publicUrl = $this->mRouter->urlEx('video', '', array('keywords' => $keywords,'query' => $query, 'vedioType' => $video_type)) . $this->mRouter->setUrlDelimiter();
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
			$rurl = $this->mRequest->getPost('rurl');

			$this->writeLog('视频编辑页面被修改' , $id);

			$this->addUpdateCheck($data);
			$ret = $this->mModel->update($id, Ttkvod_Model_Video::T_VID, $data);
			if ($ret == 1) {
				$this->loadClientControllor('itemControllor', true)->deleteCacheVideoInfoById($id);
				if ($data['updateDate'] > $aData['updateDate']) {
					Ttkvod_Model_Notice::clearCacheByVid($id);
					if ($cache = $this->mModel->getPlayDataCache($id)) {
						$cache->flush();
					}
				}
				$this->createVideoHtmlById($id);
				$this->showMsg(array('msg' => '修改成功', 'url' => empty($rurl) ? $this->mRefer : $rurl));
			} else if ($ret == '-1') {
				$this->showMsg(array('msg' => "影片名{$data['name']}已经存在", 'url' => ''));
			} else {
				$this->showMsg(array('msg' => '未知错误', 'url' => ''));
			}
			
		} else {
			$sql = 'select playData,content from vedio_data where id=?';
			$data = Lamb_App::getGlobalApp()->getDb()->quickPrepare($sql, array( 1 => array($id, PDO::PARAM_INT)))->toArray();
			if (count($data) <= 0) {
				$this->mResponse->eecho('illegal params');
			}
			$aData = $aData + $data[0];
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
		
		if (!isset($data['topType']) || !Lamb_Utils::isInt($data['topType'], true) || !isset($this->mSiteCfg['channels'])) {
			$this->showMsg(array('msg' => '影片类型不正确', 'url' => ''));
		}
		
		if (!isset($data['updateDate']) || ($temp = strtotime($data['updateDate'])) === false) {
			$this->showMsg(array('msg' => '更新时间格式不正确', 'url' => ''));
		}
		$data['updateDate'] = $temp;
		
		if (!isset($data['playData']) || empty($data['playData'])) {
			$this->showMsg(array('msg' => '播放种子不能为空', 'url' => ''));
		}
		
		if (!isset($data['syDate']) || ($temp = strtotime($data['syDate'])) === false) {
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
}