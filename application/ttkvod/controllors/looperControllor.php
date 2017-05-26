<?php
class looperControllor extends Ttkvod_Controllor
{
	public function __construct()
	{
		parent::__construct();
		set_time_limit(0);
	}
	
	public function getControllorName()
	{
		return 'index';
	}	
	
	public function createhtmlAction()
	{
		$action = trim($this->mRequest->ac);
		$intervalSecond = trim($this->mRequest->is);
		$page = trim($this->mRequest->p);
		$pagesize = trim($this->mRequest->psi);
		$id = trim($this->mRequest->id);
		$limit = trim($this->mRequest->limit);
		$tagid = trim($this->mRequest->tagid);
		$sdate = trim($this->mRequest->sd);
		$edate = trim($this->mRequest->ed);
		$taskurl = trim($this->mRequest->turl);
		
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		if (!Lamb_Utils::isInt($intervalSecond, true)) {
			$intervalSecond = 2;
		}
		
		if (!Lamb_Utils::isInt($pagesize, true)) {
			$pagesize = 100;
		}
	
		$model = new Ttkvod_Model_Static();
		if ($taskurl) {
			$model->setTaskUrl($taskurl);
		}		
		switch ($action) {
			case 'item':
			case 'list':
				$condition = array();
				
				if (Lamb_Utils::isInt($id, true) && isset($this->mSiteCfg['channels'][$id])) {
					$condition['id'] = $id;
				} else if ($action == 'list'){
					$this->mResponse->eecho("id : {$id} illegal");
				}
				
				if (Lamb_Utils::isInt($limit, true)) {
					$condition['limit'] = $limit;
				}
				
				if ($action == 'list') {
					if (Lamb_Utils::isInt($tagid, true)) {
						$condition['tag'] = $tagid;
					}
					$count = $model->createListLooper($condition);
					$handler = array($model, 'listLooperCallback');
					$model->setCurrentTaskType(Ttkvod_Model_Static::T_LIST);
				} else {
					if ($sdate == '0' && $edate == '0') {
						$h = (int)date('h');
						if ( $h < 4 ) {
							$condition['updateDate'] = array(strtotime(date('Y-m-d', time() - 24 * 3600)), strtotime(date('Y-m-d 23:59:59')));
						} else {
							$condition['updateDate'] = array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59')));
						}
					} else {
						if (strtotime($sdate) !== false) {
							if (strtotime($edate) !== false) {
								$condition['updateDate'] = array(strtotime($sdate), strtotime($edate));
							} else {
								$condition['updateDate'] = array(strtotime($sdate));
							}
						} else if (strtotime($edate) !== false){
							$condition['updateDate'] = array(0, strtotime($edate));
						}
					}
					$count = $model->createItemLooper($condition);
					$handler = array($model, 'itemLooperCallback');
					$model->setCurrentTaskType(Ttkvod_Model_Static::T_ITEM);
				}
				$obj = new Ttkvod_HttpPageLooper($pagesize);
				$obj->setCount(ceil($count / 100))
					->setOrGetSleepSecond($intervalSecond)
					->setHandler($handler)
					->setMsgHandler(array($model, 'looperHandler'))
					->setOrGetCurrentPage($page)
					->run();
				break;
			case 'index':
				$model->createIndex($intervalSecond);
				break;
			case 'top':
				$model->createTop($intervalSecond);
				break;
			case 'listtask':
			case 'listtaskbyid':
				$urlParam = array('ac' => $action, 'psi' => $pagesize, 'is' => $intervalSecond);
				Lamb_Loader::loadClass('listControllor', $this->mSiteCfg['controllor_path']);
				$ctr = new listControllor();
				$searchIndexs = $ctr->getSearchIndex();
				
				if (!Lamb_Utils::isInt($id, true)) {
					if ($action == 'listtaskbyid') {
						$this->mResponse->eecho("error");
					}
					$id = 1;
				}
				
				$urlid = $id;
				if (!array_key_exists($id, $this->mSiteCfg['channels'])) {
					$this->mResponse->eecho("error");
				}
				
				$types = $searchIndexs[$id]['types'];
				if (!Lamb_Utils::isInt($tagid, true)) {
					$urlParam['tagid'] = 0;
				} else {					
					if ($tagid >= count($types)) {
						if ($action == 'listtaskbyid') {
							$this->mResponse->eecho("任务执行完毕！");
						}
						if ($id >= 4) {
							$this->mResponse->eecho("任务执行完毕！<script>window.open('','_self');window.opener=null;window.close()</script>");
						} else {
							$urlid ++;
							$urlParam['tagid'] = '';
							$tagid = '';
						}
					} else {
						$urlParam['tagid'] = $tagid + 1;
					}
				}
				
				$urlParam['id'] = $urlid;
				$urlParam['limit'] = $limit;
				
				$taskurl = $this->mRouter->urlEx($this->mDispatcher->setOrGetControllor(), $this->mDispatcher->setOrGetAction(), $urlParam);
				$url = $this->mRouter->urlEx($this->mDispatcher->setOrGetControllor(), $this->mDispatcher->setOrGetAction(), array(
								'ac' => 'list', 'limit' => $limit, 'tagid' => $tagid, 'psi' => $pagesize, 'is' => intervalSecond,
								'id' => $id, 'turl' => $taskurl
							));
				$this->mResponse->redirect($url);
				break;
			default:
				if ($action == 'daylistall') {
					$url = 'index.php?s=looper/createhtml/ac/listtask';
				} else {
					$url = 'index.php?s=looper/createhtml/ac/listtask/limit/5'; //list url
				}
				$url = 'index.php?s=looper/createhtml/ac/item/sd/0/ed/0/turl/' . $this->mRouter->encode($this->mRouter->encode($this->mRouter->encode($url))); //item url
				//$url = '?s=looper/createhtml/ac/top/turl/' . $this->mRouter->encode($this->mRouter->encode($url)); //top
				$url = 'index.php?s=looper/createhtml/ac/index/' . $this->mRouter->url(array('turl' => $url)); //$index
				$this->mResponse->redirect($url);
				break;
		}
	}
	
	public function customLooper($coutsql, $handler, $pagesize = 100)
	{
		$page = trim($this->mRequest->page);
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		$obj = new Ttkvod_HttpPageLooper($pagesize);
		$obj->setCountBySql($coutsql)
			->setHandler($handler)
			->setMsgHandler()
			->setOrGetUrlPrefix($this->mRouter->getCtrlActUrl())
			->setOrGetCurrentPage($page)
			->run();	
	}
}