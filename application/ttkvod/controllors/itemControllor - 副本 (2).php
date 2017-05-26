<?php
class itemControllor extends Ttkvod_Controllor
{
	const E_COMMENT_MAX_CITA = -5;

	const E_COMMENT_SUBMIT_UNKNOW_ERROR = -3;
	
	const E_COMMENT_CONTENT_LENGTH_TOO_LONG = -2;
	
	const E_COMMENT_CONTENT_EMPTY = -1;
	
	const E_COMMENT_NOT_LOGIN = 0;
	
	const S_COMMENT_SUBMIT_SUCC = 1;
	
	protected $mDbCallback = null;
	
	public function getControllorName()
	{
		return 'item';
	}	
	
	public function indexAction()
	{
		$this->mCacheTime = $this->mRequest->ct;
		if (!Lamb_Utils::isInt($this->mCacheTime)) {
			$this->mCacheTime = 0;
		}
		$id = trim($this->mRequest->id);
		$subTitleDelimiter = '[$]';
		
		if (!Lamb_Utils::isInt($id)) {
			throw new Lamb_Exception("vid : \"$id\" is illegal!");
		}
		$info = $this->getCacheVideoInfoById($id);
		if (count($info) != 1) {
			throw new Lamb_Exception("vid : \"$id\" is not found!");
		}
		$info = $info[0];
		$tagmodel = new Ttkvod_Model_Tag();
		$topTypeInfos =  $this->mSiteCfg['channels'][$info['topType']];
		$points = explode('.', $info['point']);
		$typename = $topTypeInfos['name'];
		$actors = $tagmodel->parse($info['actors']);
		$types = $tagmodel->parse($info['vedioType']);
		$directors = $tagmodel->parse($info['directors']);
		if ($this->mSiteCfg['is_url_encode']) {
			$playData = '';
			foreach (explode("\r\n", $info['playData']) as $val) {
				$subtitles = '';
				if (!empty($val)) {
					if ($pos = strpos($val, $subTitleDelimiter)) {
						$subtitles = substr($val, $pos);
						$val = substr($val, 0, $pos);
					}
					$playData .= Lamb_Utils::authcode(trim($val), $this->mSiteCfg['encode_key'], 'ENCODE') . $subtitles . '#';
				}
			}
			$playData = rtrim($playData, '#');
			$playData = 'g_PlayUrl=\'' . rtrim($playData, '#') . '\'';
		} else {
			$playData = str_replace("\r\n", '#', $info['playData']/*, $playDataLen*/);
			$this->toUnicodeNumber($playData, 1500);
			$jsstr = "g_PlayUrl='$playData';";
			$packer = new Ttkvod_JavaScriptPacker($jsstr);
			$jsstr = $packer->pack();
			$playData = $jsstr;
		}
		if ($this->mRuntimeTemplate == 'default') {
			$aItem = array();
			$referLists = $this->getReferList($info['name'], $id, $info['topType'], $this->mCacheTime);
		} else {
			$aItem = $referLists = array();
			
			foreach (array('vedioType', 'directors', 'actors') as $key) {
				$referLists[$key] = $this->getLikeSource(array('tagname' => $info[$key], 'id' => $id, 'topType' => $info['topType'], 'area' => $info['area']),
					'name,id,vedioPic', 7);
			}		
		}
		
		$db = $this->mApp->getDb();
		$mobileMovieId = $db->query("select mobile_movie_id from work_mark where pc_movie_id = $id and mark = 1")->toArray();
		if (count($mobileMovieId)) {
			$mobileMovieId = $mobileMovieId[0]['mobile_movie_id'];
		} else {
			$mobileMovieId = 0;
		}
		
		include $this->load($topTypeInfos['item_template']);
	}
	
	public function dynamicAction()
	{
		$id = trim($this->mRequest->id);
		$pagesize = trim($this->mRequest->ps);
		
		if (!Lamb_Utils::isInt($id, true) || !($videoInfo = $this->getCacheVideoInfoById($id)) || count($videoInfo) != 1) {
			return false;
		}

		$videoInfo = $videoInfo[0];
		$week = (int)date('w');
		$month = (int)date('d');
		$writeStatus = (int)Lamb_IO_File::getContents($this->mSiteCfg['week_lock_path']);
		if ($writeStatus > 3 || $writeStatus < 0) {
			$writeStatus = 0;
		}
		$model = new Ttkvod_Model_Video;
		$gemo = $videoInfo['gemo'];
		
		if ($week == 1 && !($writeStatus & 1) ) {//如果周一，并且还没有复位周人气
			$model->viewNumHandler($id, 1);
			$writeStatus = $writeStatus & 2 | 1;
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 2 | 1);
		} else if ($week != 1 && ($writeStatus & 1)) { //如果不是周一，则修改成未复位标志
			$writeStatus = $writeStatus & 2;
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 2);
		}
		
		if ($month == 1 && !($writeStatus & 2) ) {
			$model->viewNumHandler($id, 2);
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 1 | 2);
		} else if ($month != 1 && ($writeStatus & 2)) {
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 1);
		}
		$model->viewNumHandler($id);
							
		if (!$gemo) {
			$gemo = Lamb_IO_File::getContents($this->mSiteCfg['item_text_ad_path']);
		}
		
		header('Content-type:text/html;charset=gbk');
		$ret = $this->getCommentNum($id) . '|' . $videoInfo['point'] . '|' . $videoInfo['pointNum'] . '|' . Lamb_App_Response::encodeURIComponent($gemo);
		$ret = strlen($ret) . '|' . $ret;
		$this->addAjaxDomainScript();
		echo $ret;
		$this->commentAction($pagesize, true);
	}

	public function dynamic2Action()
	{
		$id = trim($this->mRequest->id);
		$pagesize = trim($this->mRequest->ps);
		$callback = trim($this->mRequest->c);		
		
		if (!Lamb_Utils::isInt($id, true) || !($videoInfo = $this->getCacheVideoInfoById($id)) || count($videoInfo) != 1) {
			return false;
		}

		
		$videoInfo = $videoInfo[0];
		$week = (int)date('w');
		$month = (int)date('d');
		$writeStatus = (int)Lamb_IO_File::getContents($this->mSiteCfg['week_lock_path']);
		if ($writeStatus > 3 || $writeStatus < 0) {
			$writeStatus = 0;
		}
		$model = new Ttkvod_Model_Video;
		$gemo = $videoInfo['gemo'];
		if ($week == 1 && !($writeStatus & 1) ) {//如果周一，并且还没有复位周人气
			$model->viewNumHandler($id, 1);
			$writeStatus = $writeStatus & 2 | 1;
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 2 | 1);
		} else if ($week != 1 && ($writeStatus & 1)) { //如果不是周一，则修改成未复位标志
			$writeStatus = $writeStatus & 2;
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 2);
		}
		
		if ($month == 1 && !($writeStatus & 2) ) {
			$model->viewNumHandler($id, 2);
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 1 | 2);
		} else if ($month != 1 && ($writeStatus & 2)) {
			Lamb_IO_File::putContents($this->mSiteCfg['week_lock_path'], $writeStatus & 1);
		}
		
		$model->viewNumHandler($id);
							
		if (!$gemo) {
			$gemo = Lamb_IO_File::getContents($this->mSiteCfg['item_text_ad_path']);
		}

		$videoInfo['point'] = sprintf('%0.1f', $videoInfo['point']);
		$gemo = Lamb_App_Response::encodeURIComponent($gemo);
		$param = ",{p:'{$videoInfo['point']}', pn:'{$videoInfo['pointNum']}', g:'{$gemo}'}";
		$param = $this->comment2Action($pagesize, true) . $param;		
		$this->noticeClient($param, array('mode' => 2, 'callback' => $callback));
	}
	
	public function commentAction($pagesize = null, $isCalled = false)
	{
		if (!$isCalled) {
			header('Content-type:text/html;charset=' . $this->mApp->getCharset());
			$this->addAjaxDomainScript();
		}
		$videoModel = new Ttkvod_Model_Video;
		$id = trim($this->mRequest->id);
		$page = trim($this->mRequest->p);
		$orderType = trim($this->mRequest->ot);
		if (!$pagesize) {
			$pagesize = trim($this->mRequest->ps);
		}		
		
		if (!Lamb_Utils::isInt($id, true) || in_array($id, explode(',', $this->mSiteCfg['comment']['close_video_ids'])) || !$videoModel->get($id)) {
			$this->mResponse->eecho('error');
		}
		if (!Lamb_Utils::isInt($pagesize, true) || $pagesize > 50) {
			$pagesize = $this->mSiteCfg['comment']['pagesize'];
		}
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		if (!Lamb_Utils::isInt($orderType, true) || $orderType > 1) {
			$orderType = 0;
		} 
		
		$cache = $this->getCommentCache($id, $orderType, $pagesize, $page, $sql);
		if ($cache->isCached()) {
			$this->mResponse->eecho($cache->read());
		}
		if ($orderType != 1) {
			$sql = Lamb_App::getGlobalApp()->getSqlHelper()->getPageSql($sql, $pagesize, $page);
		}
		
		$comment = new Ttkvod_BuildComment();
		$ret = $comment->getComment($sql, $id, true, $this->getCommentNum($id));
		$cache->write($ret);
		$this->mResponse->eecho($ret);
	}

	public function comment2Action($pagesize = null, $isCalled = false)
	{
		$videoModel = new Ttkvod_Model_Video;
		$id = trim($this->mRequest->id);
		$page = trim($this->mRequest->p);
		$callback = trim($this->mRequest->c);
		$orderType = trim($this->mRequest->ot);
		if (!$pagesize) {
			$pagesize = trim($this->mRequest->ps);
		}		
		
		if (!Lamb_Utils::isInt($id, true) || in_array($id, explode(',', $this->mSiteCfg['comment']['close_video_ids'])) || !$videoModel->get($id)) {
			return 'null';
		}
		if (!Lamb_Utils::isInt($pagesize, true) || $pagesize > 50) {
			$pagesize = $this->mSiteCfg['comment']['pagesize'];
		}
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		if (!Lamb_Utils::isInt($orderType, true) || $orderType > 1) {
			$orderType = 0;
		} 
		
		$cache = $this->getCommentCache($id, $orderType, $pagesize, $page, $sql);
		if ($cache->isCached()) {
			$ret = $cache->read();
		} else {
			if ($orderType != 1) {
				$sql = Lamb_App::getGlobalApp()->getSqlHelper()->getPageSql($sql, $pagesize, $page);
			}
			$comment = new Ttkvod_BuildComment();
			$ret = $comment->getComment($sql, $id, true, $this->getCommentNum($id));
			$cache->write($ret);
		}

		if ($isCalled) {
			return $ret ? $ret : 'null';
		} else {
			$this->noticeClient($ret, array('mode' => 2, 'callback' => $callback));
		}
	}
	
	public function commsbtAction()
	{
		$this->addAjaxDomainScript();
		if ($this->mRequest->isPost()) {
			$vid = trim($this->mRequest->id);
			$content = $this->mRequest->getPost('content', '');
			$commid = $this->mRequest->getPost('commid', '');
			$ip = Ttkvod_Utils::getRealIp();
			
			if (!Lamb_Utils::isInt($vid, true) || in_array($vid, explode(',', $this->mSiteCfg['comment']['close_video_ids']))
					|| !($videoInfo = $this->getCacheVideoInfoById($vid))|| 
			 		count($videoInfo) != 1 || !$this->accessIp($ip, explode(',', $this->mSiteCfg['comment']['forbin_ips'])) ) {
				$this->mResponse->eecho(self::E_COMMENT_SUBMIT_UNKNOW_ERROR);
			}
			
			$contlen = strlen($content);
			
			if ($contlen < 1) {
				$this->mResponse->eecho(self::E_COMMENT_CONTENT_EMPTY);
			}			
			if ($contlen > $this->mSiteCfg['comment']['max_content_len']) {
				$this->mResponse->eecho(self::E_COMMENT_CONTENT_LENGTH_TOO_LONG);
			}		
			if (!$this->userCheck(true, $userinfo)) {
				$this->mResponse->eecho(self::E_COMMENT_NOT_LOGIN);
			}
			if ($this->accessCommentContent($userinfo['username']) || $this->accessCommentContent($content)) {
				$this->mResponse->eecho(self::E_COMMENT_SUBMIT_UNKNOW_ERROR);
			}

			$isIllegal = false;
			if (preg_match('/\d{6,10}/is', preg_replace('/[\s\r\n·`]*/is', '', $content))) {
				$sbData = $this->mApp->getDb()->getNumDataPrepare('select id,vid from sb where ip=:ip', array(':ip' => array($ip, PDO::PARAM_STR)), true);
				if ($sbData['num']) {
					if ($sbData['data']['id']) {
						$this->mApp->getDb()->quickPrepare('delete from comment where vedioid=? and id=?',
						array( 
							1 => array($sbData['data']['vid'], PDO::PARAM_INT), 
							2 => array($sbData['data']['id'], PDO::PARAM_INT)
						), 
						true);
						$this->getCommentCache($sbData['data']['vid']);
						$this->mApp->getDb()->quickPrepare('update sb set id=0,vid=0 where ip=:ip', 
							array(':ip' => array($ip, PDO::PARAM_STR)), true);
					}
					$this->mResponse->eecho(self::S_COMMENT_SUBMIT_SUCC);
				} else {
					$isIllegal = true;
				}			
			}			

			$this->filterCommentContent($userinfo['username'])
				 ->filterCommentContent($content);
			$relas = '';
			if (Lamb_Utils::isInt($commid, true)) {
				$data = $this->mApp->getDb()->getNumDataPrepare('select relas from comment where vedioid=:parentid and id=:id',
							array(':parentid' => array($vid, PDO::PARAM_INT), ':id' => array($commid, PDO::PARAM_INT)), true);
				if ($data['num'] == 1) {
					$relas = trim($data['data']['relas']);
					$relas = $relas . ($relas && strlen($relas)>0 ? ',' : '') . $commid;
				}
				if (count(explode(',', $relas)) + 1> $this->mSiteCfg['comment']['max_cita_count']) {
					$this->mResponse->eecho(self::E_COMMENT_MAX_CITA);
				}					
			}
			$db = $this->mApp->getDb();
			$maxid = $db->getNumDataPrepare('select max(id) as id from comment where vedioid=?', 
							array(1 => array($vid, PDO::PARAM_INT)), true);
			$maxid = $maxid['num'] < 1 ? 1 : $maxid['data']['id'] + 1;
			$sql = 'insert into comment (vedioid,relas,name,ip,content,time,id) values (?,?,?,?,?,?,?)';
			$aPrepareSource = array(
				1 => array($vid, PDO::PARAM_INT),
				2 => array($relas, PDO::PARAM_STR),
				3 => array($userinfo['username'], PDO::PARAM_STR),
				4 => array($ip, PDO::PARAM_STR),
				5 => array($content, PDO::PARAM_STR),
				6 => array(time(), PDO::PARAM_INT),
				7 => array($maxid, PDO::PARAM_INT)
			);
			
			if ($db->quickPrepare($sql, $aPrepareSource, true))	{
				if ($isIllegal) {
					$this->mApp->getDb()->quickPrepare('insert into sb (ip,id,vid) values (:ip,:id,:vid)', 
					array(
						':ip' => array($ip, PDO::PARAM_STR), 
						':id' => array($maxid, PDO::PARAM_INT),
						':vid' => array($vid, PDO::PARAM_INT)
					)
					);				
				}
				$this->getCommentCache($vid);
				$this->mResponse->eecho(self::S_COMMENT_SUBMIT_SUCC);
			}		
		}
		
		$this->mResponse->eecho(self::E_COMMENT_SUBMIT_UNKNOW_ERROR);
	}
	
	public function supportAction()
	{
		$this->addAjaxDomainScript();
		$id = trim($this->mRequest->id);
		$commid = trim($this->mRequest->commid);
		if (Lamb_Utils::isInt($id, true) && Lamb_Utils::isInt($commid, true) && count($this->getCacheVideoInfoById($id)) == 1) {
			if ($this->mApp->getDb()->quickPrepare('update comment set hit=hit+1 where vedioid=:parentid and id=:id',
				array(':parentid' => array($id, PDO::PARAM_INT), ':id' => array($commid, PDO::PARAM_INT)), true)) {
				$this->getCommentCache($id);
				$this->mResponse->eecho(self::S_COMMENT_SUBMIT_SUCC);			
			}
		}
	}

	public function support2Action()
	{
		$id = trim($this->mRequest->id);
		$commid = trim($this->mRequest->commid);
		$callback = trim($this->mRequest->c);
		$isDown = trim($this->mRequest->isd);
		if (Lamb_Utils::isInt($id, true) && Lamb_Utils::isInt($commid, true) && count($this->getCacheVideoInfoById($id)) == 1) {
			$column = $isDown ? 'down=down+1' : 'hit=hit+1';
			if ($this->mApp->getDb()->quickPrepare("update comment set {$column} where vedioid=:parentid and id=:id",
				array(':parentid' => array($id, PDO::PARAM_INT), ':id' => array($commid, PDO::PARAM_INT)), true)) {
				$this->getCommentCache($id);
				$this->noticeClient(self::S_COMMENT_SUBMIT_SUCC, array('mode' => 2, 'callback' => $callback));				
			}
		}
		$this->noticeClient(0, array('mode' => 2, 'callback' => $callback));
	}
	
	public function favAction()
	{
		$this->addAjaxDomainScript();
		$id = trim($this->mRequest->id);
		if (Lamb_Utils::isInt($id) && count($this->getCacheVideoInfoById($id)) == 1) {
			if (!($uid = $this->userCheck())) {
				$this->mResponse->eecho(2);
			}
			$db = $this->mApp->getDb();
			if ($db->getNumDataPrepare('select vedioId from favorites where userId=? and vedioId=?',
					array(1 => array($uid, PDO::PARAM_INT), 2 => array($id, PDO::PARAM_INT))) > 0) {
				$this->mResponse->eecho(3);	
			}
			if ($db->quickPrepare('insert into favorites (userId, vedioId) values (?,?)', 
					array(1 => array($uid, PDO::PARAM_INT), 2 => array($id, PDO::PARAM_INT)), true)) {
				$this->mResponse->eecho(1);	
			}
		}
	}

	public function fav2Action()
	{
		$id = trim($this->mRequest->id);
		$callback = trim($this->mRequest->c);
		$msgop = array('mode' => 2, 'callback' => $callback);
		
		if (Lamb_Utils::isInt($id) && count($this->getCacheVideoInfoById($id)) == 1) {
			if (!($uid = $this->userCheck())) {
				$this->noticeClient(2, $msgop);
			}
			$db = $this->mApp->getDb();
			if ($db->getNumDataPrepare('select vedioId from favorites where userId=? and vedioId=?',
					array(1 => array($uid, PDO::PARAM_INT), 2 => array($id, PDO::PARAM_INT))) > 0) {
				$this->noticeClient(3, $msgop);
			}
			if ($db->quickPrepare('insert into favorites (userId, vedioId) values (?,?)', 
					array(1 => array($uid, PDO::PARAM_INT), 2 => array($id, PDO::PARAM_INT)), true)) {
				$this->noticeClient(1, $msgop);
			}
		}
		$this->noticeClient(0, $msgop);
	}
	
	public function pointAction()
	{
		$this->addAjaxDomainScript();
		$id = trim($this->mRequest->id);
		$point = trim($this->mRequest->point);
		
		if (Lamb_Utils::isInt($id) && count($info = $this->getCacheVideoInfoById($id)) == 1) {
			$info = $info[0];
			if ($this->mRequest->getCookie('isPointed_' . $id, '')) {
				$this->mResponse->eecho('isPointed');	
			}
			if (!Lamb_Utils::isNumber($point, true)) {
				$this->mResponse->eecho('error');
			}
			$point = max(1, min(10, (int)($point)));
			$pointnum = $info['pointNum'];
			$pointall = $info['pointAll'];
			$pointall += $point;
			$pointnum ++;
			$newpoint = sprintf('%0.1f', $pointall/$pointnum);
			$aPrepareSource = array(1=>array($newpoint, PDO::PARAM_INT), 2=>array($pointall, PDO::PARAM_INT), 3=>array($pointnum, PDO::PARAM_INT), 4=>array($id, PDO::PARAM_INT));
			$this->mApp->getDb()->quickPrepare('update vedio set point=?,pointAll=?,pointNum=? where id=?', $aPrepareSource, true);
			$this->mResponse->setcookie('isPointed_'.$id, 'true', 24*3600*30);
			$this->mResponse->eecho($newpoint);
		}
	}

	public function point2Action()
	{
		$id = trim($this->mRequest->id);
		$point = trim($this->mRequest->point);
		$callback = trim($this->mRequest->c);
		$msgop = array('mode' => 2, 'callback' => $callback);
		
		if (Lamb_Utils::isInt($id) && count($info = $this->getCacheVideoInfoById($id)) == 1) {
			$info = $info[0];
			if ($this->mRequest->getCookie('isPointed_' . $id, '')) {
				$this->noticeClient("'isPointed'", $msgop);
			}
			if (!Lamb_Utils::isNumber($point, true)) {
				$this->noticeClient("'error'", $msgop);
			}
			$point = max(1, min(10, (int)($point)));
			$pointnum = $info['pointNum'];
			$pointall = $info['pointAll'];
			$monthPoint = $info['monthPoint'];
			$pointall += $point;
			$monthPoint += $point;
			$pointnum ++;
			$newpoint = sprintf('%0.1f', $pointall/$pointnum);
			$aPrepareSource = array(1=>array($newpoint, PDO::PARAM_INT), 2=>array($pointall, PDO::PARAM_INT), 3=>array($pointnum, PDO::PARAM_INT), 4=>array($monthPoint, PDO::PARAM_INT), 5=>array($id, PDO::PARAM_INT));
			$this->mApp->getDb()->quickPrepare('update vedio set point=?,pointAll=?,pointNum=?, monthPoint=? where id=?', $aPrepareSource, true);
			$this->mResponse->setcookie('isPointed_'.$id, 'true', 24*3600*30);
			$this->noticeClient($newpoint, $msgop);
		}
		$this->noticeClient("'error'", $msgop);
	}
	
	public function fixAction()
	{
		$this->addAjaxDomainScript();
		$id = trim($this->mRequest->id);
		
		if ($this->mRequest->isPost()) {
			$proids = $this->mRequest->getPost('problems', '', false);
			$reslutions = $this->mRequest->getPost('reslutions', '');
			$contacts = $this->mRequest->getPost('contact', '');
			$problems = array();
			$isOk = false;
			
			if (is_array($proids)) {
				$aProblemsInfo = explode(',' ,$this->mSiteCfg['fix_chooses']);
				foreach ($proids as $proid) {
					if (array_key_exists($proid, $aProblemsInfo)) {
						$isOk = true;
						$problems[] = $aProblemsInfo[$proid];
					}
				}
			}
			
			$problems = implode('|', $problems);
			if (strlen($problems) < 1000 && strlen($reslutions) < 1000) {
				if (!$isOk && empty($reslutions)) {
					$this->mResponse->eecho("<script>parent.showResult('总得写点什么吧~~')</script>");
				}
				if (empty($contacts)) {
					$this->mResponse->eecho("<script>parent.showResult('请填写联系方式，方便我们联系您')</script>");
				}
				if (strlen($contacts) > 50) {
					$this->mResponse->eecho("<script>parent.showResult('联系方式长度不能超过50')</script>");
				}
				$aPrepareSource = array(
						1 => array($id, PDO::PARAM_INT),
						2 => array(time(), PDO::PARAM_INT),
						3 => array($reslutions, PDO::PARAM_STR),
						4 => array($problems, PDO::PARAM_STR),
						5 => array($contacts, PDO::PARAM_STR)
					);
				if ($this->mApp->getDb()->quickPrepare('insert into fixinfo (videoid,intdate,reslutions,problems,contact) values (?,?,?,?,?)', $aPrepareSource, true)) {
					$this->mResponse->eecho("<script>parent.location.href += '/msg/1'</script>");
				}
			}
		} 
		
		$this->mResponse->eecho("<script>parent.showResult('系统异常，请稍后重试')</script>");
	}

	public function fix2Action()
	{
		$this->addAjaxDomainScript();
		$id = trim($this->mRequest->id);
		$callback = trim($this->mRequest->c);
		$msgop = array('mode' => 2, 'callback' => $callback);
		
		if ($this->mRequest->isPost()) {
			$proids = $this->mRequest->getPost('problems', '', false);
			$reslutions = $this->mRequest->getPost('reslutions', '');
			$contacts = $this->mRequest->getPost('contact', '');
			$problems = array();
			$isOk = false;
			
			if (!is_array($proids)) {
				$proids = explode(',', $proids);
			}
			
			if (is_array($proids)) {
				$aProblemsInfo = explode(',' ,$this->mSiteCfg['fix_chooses']);
				foreach ($proids as $proid) {
					if (array_key_exists($proid, $aProblemsInfo)) {
						$isOk = true;
						$problems[] = $aProblemsInfo[$proid];
					}
				}
			}
			
			$problems = implode('|', $problems);
			if (strlen($problems) < 1000 && strlen($reslutions) < 1000) {
				if (!$isOk && empty($reslutions)) {
					$this->noticeClient(-1, $msgop);
				}
				if (empty($contacts)) {
					$this->noticeClient(-2, $msgop);
				}
				if (strlen($contacts) > 50) {
					$this->noticeClient(-3, $msgop);
				}
				$aPrepareSource = array(
						1 => array($id, PDO::PARAM_INT),
						2 => array(time(), PDO::PARAM_INT),
						3 => array($reslutions, PDO::PARAM_STR),
						4 => array($problems, PDO::PARAM_STR),
						5 => array($contacts, PDO::PARAM_STR)
					);
				if ($this->mApp->getDb()->quickPrepare('insert into fixinfo (videoid,intdate,reslutions,problems,contact) values (?,?,?,?,?)', $aPrepareSource, true)) {
					$this->noticeClient(1, $msgop);
				}
			}
		} 
		
		$this->noticeClient(0, $msgop);
	}	

	/**
	 * @param int $id
	 * @return Lamb_Db_RecordSet_Interface
	 */
	public function getCacheVideoInfoById($id)
	{
		$select = new Lamb_Db_Select('select a.*, b.content as content,b.playData as playData from vedio a, vedio_data b where a.id=b.id and a.id= ?');
		$select->setOrGetCache($this->getVideoInfoCache($id));
		return $select->query(array( 1 => array($id, PDO::PARAM_INT)))->toArray();
	}
	
	/**
	 * @param int $id
	 * @return boolean
	 */
	public function deleteCacheVideoInfoById($id)
	{
		return $this->getVideoInfoCache($id)->flush();
	}
	
	/**
	 * @param int $id
	 * @return Lamb_Cache_Interface
	 */
	public function getVideoInfoCache($id)
	{
		return Ttkvod_Cache_Factory::getCache()->setIdentity(__CLASS__. '_getVideoInfoCache_' . $id)->setCacheTime($this->getRealCacheTime());
	}
	
	/**
	 * @param int $vid
	 * @return int
	 */
	public function getCommentNum($vid)
	{
		$cache = $this->getCommentNumCache($vid, $sql, $aPrepareSource);
		$select = new Lamb_Db_Select($sql);
		$data = $select->setOrGetCache($cache)->query($aPrepareSource)->toArray();
		return $data[0]['num'];
	}	
	
	/**
	 * @param int $vid
	 * @param string & $sql
	 * @param array & $aPrepareSource
	 * @return Lamb_Cache_Interface
	 */
	public function getCommentNumCache($vid, &$sql = null, &$aPrepareSource = null)
	{
		$sql = 'select count(id) as num from comment where vedioid=:id';
		$aPrepareSource[':id'] = array($vid, PDO::PARAM_INT);
		$identity = Lamb_Db_Select::getSqlIdentity($sql, null, null, $aPrepareSource);
		unset($sql, $aPrepareSource);
		return Ttkvod_Cache_Factory::getCache()->setIdentity($identity)->setCacheTime(1800);
	}
	
	/**
	 * @param int $vid
	 * @param int $orderType
	 * @param int $pagesize
	 * @param int $page
	 * @param string & $sql
	 * @param array & $aPrepareSource
	 * @return Lamb_Cache_Interface
	 */
	public function getCommentCache($vid, $orderType = null, $pagesize = null, $page = null, &$sql = null)
	{
		$sql1 = "select top $pagesize vedioid,id,time,name,ip,content,hit,relas,down from comment where vedioid=$vid and hit>0 order by hit desc,time desc";
		$sql2 = "select vedioid,id,time,name,ip,content,hit,relas,down from comment where vedioid=$vid order by time desc";
		$cache = Ttkvod_Cache_Factory::getCache(Ttkvod_Cache_Factory::CACHE_LOCAL_CACHE);
		
		if (null === $orderType) {
			$cache->clear(array('sql_key' => Lamb_Utils::crc32FormatHex($sql1)))
				  ->clear(array('sql_key' => Lamb_Utils::crc32FormatHex($sql2)));
			$this->getCommentNumCache($vid)->flush();
		} else {
			$sql = $orderType == 1 ? $sql1 : $sql2;
			$cache->setOrGetTableKey('comment')
				  ->setOrGetPagesize($pagesize)
				  ->setOrGetSqlKey($sql)
				  ->setIdentity(Lamb_Db_Select::getSqlIdentity($sql, $page, $pagesize));			
		}
		unset($sql, $aPrepareSource);
		return $cache;
	}
	
	/**
	 * @param  string $string
	 * @return string
	 */
	public function filterContent($string)
	{
		$patts = array(
			'/<a.*?>.*?<\/a>/is',
			'/<script.*?>.*?<\/script>/is',
			'/<object.*?>.*?<\/object>/is',
			'/<iframe.*?>.*?<\/iframe>/is',
			'/<img.*?>/is',
			'/<\/?span.*?>/is',
			'/<link.*?>/is',
			'/<\/?p.*?>/is'
		);
		foreach ($patts as $patt) {
			$string = preg_replace($patt, '', $string);
		}
		return $string;	
	}
	
	/**
	 * @param string $strKey
	 * @param int $nItemId
	 * @param int $nTopType
	 * @param int $nCacheTime
	 * @param int $nLimit
	 * @return array
	 */
	public function getReferList($strKey, $nItemID, $nTopType, $nCacheTime = 0, $nLimit = 8)
	{
		if ($nCacheTime == 0) {
			$nCacheTime = $this->mSiteCfg['cache_cfg']['timeout'];
		}
		$aRet	= array();
		$cache = Ttkvod_Cache_Factory::getCache();
		$cache->setIdentity(__CLASS__ . '_refersearch_' . Lamb_Utils::crc32FormatHex($strKey))
			  ->setCacheTime($nCacheTime);
		if ($cache->isCached() && ($aRet = unserialize($cache->read()))) {
			return $aRet;
		}
		
		$strKey = Ttkvod_Model_Video::encodeFullSearchStr($strKey);
		$nNewLimit = $nLimit + 1;
		$i = 0;
		$db = Lamb_App::getGlobalApp()->getDb();
		$strSql	=	"select id,name,vedioPic from vedio a ,freetexttable
(vedio, tagname, :fullsearch, $nNewLimit) b where a.topType=:topType and a.id=b.[KEY] and b.[RANK]>0 and a.status=1 ";
	
		if ( ($objIterator = $db->quickPrepare($strSql, array(':fullsearch' => array($strKey, PDO::PARAM_STR), ':topType' => array($nTopType, PDO::PARAM_INT))) ) ){
			foreach ($objIterator as $aItem) {
				if ($aItem['id'] != $nItemID) {
					$aRet[] = $aItem;
					$i ++ ;
				}
				if ($i >= $nLimit) {
					if ($nCacheTime > 0) {
						$cache->write(serialize($aRet));
					}
					return $aRet;
				}
			}
			$nLimit = $nLimit - $i;
		}
		$strSqlBak = 'select top '. (++$nLimit) .' id,name,mark,vedioPic from vedio with(NOLOCK) where topType=:topType and status=1 order by weekNum 
desc';
		if ( ($objIterator = $db->quickPrepare($strSqlBak, array(':topType' => array($nTopType, PDO::PARAM_INT)))) ){
			$i = 0;
			foreach ($objIterator as $aItem) {
				if ($aItem['id'] != $nItemID) {
					$aRet[] = $aItem;
					$i ++ ;
				}
				if ($i >= $nLimit-1) break;
			}
		}
		if ($nCacheTime > 0) {
			$cache->write(serialize($aRet));
		}		
		return $aRet;
	}	
	
	public function filterCommentContent(&$strContent)
	{
		$strContent = str_replace("\r\n", '<br/>', htmlentities($strContent, ENT_COMPAT, 'gb2312'));
		$aContent = explode(',', $this->mSiteCfg['comment']['filter_words']);
		foreach ($aContent as $item) {
			$strContent = str_replace($item, str_repeat('*', Lamb_Utils::mbLen($item)), $strContent);
		}
		unset($strContent);
		return $this;
	}
	
	public function accessCommentContent($content)
	{
		$bRet = false;
		$content = preg_replace('/[\s\r\n]*/is', '', strtolower($content));
		$aContent = explode(',', $this->mSiteCfg['comment']['forbin_words']);
		foreach ($aContent as $item) 
		{
			if ($item && strpos($content, $item) !== false ) {
				$bRet = true;
				break;
			}
		}
		return $bRet;
	}

	public function accessIp($ip, array $ipSources)
	{
		$ret = true;
		foreach ($ipSources as $_ip) {
			if (strpos($ip, $_ip) !== false) {
				$ret = false;
				break;
			}
		}
		return $ret;
	}	

	public static function toUnicodeNumber(&$strPlayData, $nMax)
	{
		$nLen = strlen($strPlayData);
		$strTemp =  '';
		if ($nLen <= $nMax) {
			for ($i = 0; $i < $nLen; $i ++) {

				if (ord($strPlayData{$i}) >= 128) {
					$str = $strPlayData{$i}.$strPlayData{$i+1};
					$str = '\\u'.@Ttkvod_Utils::utfToUnicode(iconv('gbk', 'utf-8', $str));
					$i++;
				}
				else {
					$str = '\\u00'.sprintf('%0x', ord($strPlayData{$i}));
				}
				$strTemp .= $str;
			}
		}
		else {
			$strTemp = $strPlayData;
		}
		$strPlayData = $strTemp;
		unset($strPlayData);
	}
	
	public function addslashes($str)
	{
		//$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('\'', '\\\'', $str);
		return $str;
	}

	/**
	 * @param array $info = array(
	 *		'tagname' => string, 'id' => int, 'topType' => int, 'area' => string
	 *	)
	 * @param string $column
	 * @param int $resultNum
	 * @return array
	 */
	public function getLikeSource(array $info, $column = 'name,id', $resultNum = 5, $nCacheTime = 0)
	{
		$returnValue = array();
		if ($nCacheTime == 0) {
			$nCacheTime = $this->mSiteCfg['cache_cfg']['timeout'];
		}
		
		$cache = Ttkvod_Cache_Factory::getCache();
		$cache->setIdentity(__CLASS__ . '_maybelike_' . $column . Lamb_Utils::crc32FormatHex(print_r($info, true)))
			  ->setCacheTime($nCacheTime);
		$cache->flush();	  
		if ($cache->isCached() && ($returnValue = unserialize($cache->read()))) {
			return $returnValue;
		}	
		
		$pdo = $this->mApp->getDb();
		$sqlhelper = $this->mApp->getSqlHelper();	
		$tagmodel = new Ttkvod_Model_Tag;
		
		$sql = "select {$column} from vedio a, tag b, tagrelation c where b.tagname = :tagname and a.status=1 and b.tagid = c.tagid
						and c.vedioid = a.id and area=:area and topType = :type and id != :id order by weekNum desc";	
		$sql = $sqlhelper->getPrePareLimitSql($sql);		
			
		if ( $info['tagname'] == '不详') {
		
			$sql = "select {$column} from vedio where area = :area and a.status=1 and id != :id and topType = :type order by weekNum desc";
			$sql = $sqlhelper->getPrePareLimitSql($sql);
			
			$aPrepareSource = array(
				':g_limit' => array($resultNum, PDO::PARAM_INT),
				':area' => array($info['area'], PDO::PARAM_STR),
				':id' => array($info['id'], PDO::PARAM_INT),
				':g_offset' => array(0, PDO::PARAM_INT),
				':type' => array($info['topType'], PDO::PARAM_INT)
			);
					
			return $pdo->quickPrepare($sql, $aPrepareSource, false)->fetchAll();
		}
				
		$tagNames = $tagmodel->parse($info['tagname']);
		
		foreach ($tagNames as $tagItem) {
			$aPrepareSource = array(
				':tagname' => array($tagItem, PDO::PARAM_STR),
				':area' => array($info['area'], PDO::PARAM_STR),
				':id' => array($info['id'], PDO::PARAM_INT),
				':type' => array($info['topType'], PDO::PARAM_INT),
				':g_limit' => array($resultNum + 1, PDO::PARAM_INT),
				':g_offset' => array(0, PDO::PARAM_INT)
			);
			
			$queryResult = $pdo->quickPrepare($sql, $aPrepareSource, false)->fetchAll();
			
			foreach ($queryResult as $resultItem) {
				$returnValue[] = $resultItem;
				if (count($returnValue) >= $resultNum) {								
					break;
				}
			}
			
			if (count($returnValue) >= $resultNum) {
				break;
			}
		}	
		if ($nCacheTime > 0) {
			$cache->write(serialize($returnValue));
		}					
		return $returnValue;
	}	



	//---------------------------------------------//
	
	/**
	 * @author jude
	 * @method get 
	 * 评论列表
	 * 
	 * req_data : 
	 *		mid : int 影片id
	 * 		page : int 分页页数 默认 1
	 *		pagesize : int 默认 10
	 *
	 * res_data :
	 * 		s : 
	 * 			
	 * 		d : {
	 *			'data' : [
	 *				{'id' : 1, 'uid' : ..., 'floor_msg' : 
	 *					[
	 *						'id' : 2, 'uid' : ...
	 *					]
	 *				}
	 *			]
	 *		}
	 */
	public function listAction()
	{
		$id = trim($this->mRequest->maxid);
		$mid = trim($this->mRequest->mid);
		$hot = trim($this->mRequest->hot);
		$pagesize = trim($this->mRequest->pagesize);
		
		if (!Lamb_Utils::isInt($id, true)) {
			$id = 0;
		}
		
		if (!Lamb_Utils::isInt($hot, true)) {
			$hot = 0;
		}
		
		if (!Lamb_Utils::isInt($mid, true)) {
			$this->showResults(-2, null, '影片不存在');
		}
		
		if (!Lamb_Utils::isInt($pagesize,true)) {
			$pagesize = 10;
		}	
		$pagesize = min(max($pagesize, 1), self::MAX_PAGESIZE);	
		

		$smt = Lamb_App::getGlobalApp()->getDb()->quickPrepare('exec getMovieComment :mid,:commid,:pagesize, :hot', array(
			':mid' => array($mid, PDO::PARAM_INT),
			':commid' => array($id, PDO::PARAM_INT),
			':pagesize' => array($pagesize, PDO::PARAM_INT),
			':hot' => array($hot, PDO::PARAM_INT)
		));
		
		$aData = $smt->toArray();
		
		if (!$aData){
			$this->showResults(0);
		}
		
		foreach($aData as $key => $val) {
			$aData[$key]['msg'] = iconv('gbk', 'utf-8', $val['msg']);
		}
		
		$smt->nextRowset();	
		$bData = $smt->toArray();
		
		$smt = null;
		if (!count($bData)) { //没有出现评论盖楼
			$this->showResults(1, $aData);
		}
		
		foreach($bData as $key => $val) {
			$bData[$key]['msg'] = iconv('gbk', 'utf-8', $val['msg']);
		}
		$aData = $this->combind($aData, $bData);
		//$this->d($aData);
		$this->showResults(1, $aData);
	}
	
	
	/**
	 * @author jude
	 * @method post 
	 * 发表评论
	 * 
	 * req_data : 
	 * 		
	 *		mid : int 影片id
	 * res_data :
	 * 		s : 
	 *			 1-成功
	 *			-1-未登录
	 *			-3-评论的影片不存在 
	 *			-4-评论不能为空
	 *			-5-评论长度在2000个字以内
	 *			-6-评论盖楼已达上限	
	 * 			
	 * 		d : null
	 */
	public function addAction()
	{
		//$uid = 1;//$this->userCheck();
		
		if (!$this->mRequest->isPost()) {
			$this->showResults(0);
		}
		
		$uid = trim($this->mRequest->getPost('uid'));
		$mid = trim($this->mRequest->getPost('mid'));
		$commid = trim($this->mRequest->getPost('commid'));
		$msg = trim($this->mRequest->getPost('msg'));
		
		if (!Lamb_Utils::isInt($mid, true)) {
			$this->showResults(-3, null, '评论的影片不存在');
		}
		
		if (!Lamb_Utils::isInt($commid, true)) {
			$commid = 0;
		}
		
		if ($msg == '') {
			$this->showResults(-4, null, '评论不能为空');
		}
		
		if (strlen($msg) > 2000 ) {
			$this->showResults(-5, null, '评论长度在2000个字以内');
		}
		
		/*
		if ($this->accessCommentContent($msg)){
			$this->showResults(-7, null, '评论中含有非法词汇');
		}*/
		
		
		$smt = Lamb_App::getGlobalApp()->getDb()->prepare('exec :ret=addComment :uid,:nickname,:mid,:commid,:msg,:time,:ip');
		$smt->bindValue(':uid', $uid, PDO::PARAM_INT);
		$smt->bindValue(':nickname', 'JJAY', PDO::PARAM_STR, 200);
		$smt->bindValue(':mid', $mid, PDO::PARAM_INT);
		$smt->bindValue(':commid', $commid, PDO::PARAM_INT);
		$smt->bindValue(':msg', iconv('utf-8', 'gbk',$msg), PDO::PARAM_STR, 2000);
		$smt->bindValue(':time', time(), PDO::PARAM_INT);
		$smt->bindValue(':ip', $this->mRequest->getClientIp(), PDO::PARAM_STR, 20);
		$smt->bindParam(':ret', $ret, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 2);
		if (!$smt->execute()) {
			$this->showResults(0);
		}
		
		if ($ret == -1) {
			$this->showResults(-3, null, '评论的影片不存在');
		} else if ($ret == -2) {
			$this->showResults(-6, null, '评论盖楼已达上限');
		}
		
		$res = $smt->toArray(); 
		$touid  = $res[0]['touid']; 
		$commid = $res[0]['commid'];
		
		$this->showResults(1, $res[0]);
	}
	
	
	/**
	 * @author jude
	 * @method get 
	 * 评论点赞
	 * 
	 * req_data : 
	 * 		
	 *		id : int 评论id
	 * res_data :
	 * 		s : 
	 *			 1-成功
	 *			-1-未登录
	 *			-3-评论不存在 
	 */
	public function praiseAction()
	{
		$commid = trim($this->mRequest->commid);
		$isDown = trim($this->mRequest->isDown);
		if (Lamb_Utils::isInt($commid, true)) {
			$column = $isDown ? 'down=down+1' : 'up=up+1';
			
			$this->mApp->getDb()->quickPrepare("update comment set {$column} where id=:id", array(':id' => array($commid, PDO::PARAM_INT)), true);
			
			$this->showResults(1);				
		}
		
		$this->showResults(0);
	}
	
	
	public function combind(array $aData, array $bData)
	{
		$aNewData = array();
		$id = 0;
		foreach($bData as $key => $val) {
			$id = $val['comm_id'];
			unset($val['comm_id']);
			$aNewData[$id][] =  $val;
		}
		
		for ($i=0, $j = count($aData); $i < $j; $i++) {
			$id = $aData[$i]['commid'];
			if (array_key_exists($id, $aNewData)) {
				$aData[$i]['floor_msg'] = $aNewData[$id];		
			} else {
				$aData[$i]['floor_msg'] = array();
			}
		}

		return $aData;
	}


	
}