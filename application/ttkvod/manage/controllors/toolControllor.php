<?php
class toolControllor extends Ttkvod_ManageControllor
{
	public function getControllorName()
	{
		return 'toolContorllor';
	}
	
	public function indexAction()
	{
		$ctr = $this->loadClientControllor('listControllor', true);
		$searchIndex = $ctr->getSearchIndex();
		unset($ctr);
		$this->mApp->setViewPath($this->mViewPath);
		$types = array(
			1 => $searchIndex[1]['types'],
			2 => $searchIndex[2]['types'],
			3 => $searchIndex[3]['types'],
			4 => $searchIndex[4]['types']
		);
		
		$opaction = trim($this->mRequest->opac);
		$action = trim($this->mRequest->ac);
		$id = trim($this->mRequest->id);
		$pagesize = trim($this->mRequest->psi);
		$intervalSecond = trim($this->mRequest->is);
		$limit = trim($this->mRequest->limit);
		$sdate = trim($this->mRequest->sd);
		$edate = trim($this->mRequest->ed);
		$tagid = trim($this->mRequest->tagid);
		
		if ($opaction == 'run') {
			
			if (!Lamb_Utils::isInt($intervalSecond, true)) {
				$intervalSecond = 2;
			}
		
			if (!Lamb_Utils::isInt($pagesize, true)) {
				$pagesize = 100;
			}			
			$urlparam = array('is' => $intervalSecond, 'psi' => $pagesize, 'limit' => $limit );
			switch ($action) {
				case 'index':					
					break;
				case 'top':
					break;
				case 'item':
					if (!empty($sdate) && $sdate != '0' && strtotime($sdate) === false) {
						$this->showMsg(array('msg' => '更新起始时间格式不正确'));
					} else if ($sdate != '0') {
						$urlparam['sd'] = $sdate;
					} else if ($sdate == '0') {
						$urlparam['sd'] = '0';
					}
	
					if (!empty($edate) && $edate != '0' && strtotime($edate) === false) {
						$this->showMsg(array('msg' => '更新结束时间格式不正确'));
					} else if ($edate != '0') {
						$urlparam['ed'] = $edate;
					} else if ($edate == '0') {
						$urlparam['ed'] = '0';
					}
					$urlparam['id'] = $id;
					break;
				case 'list':
					if (!Lamb_Utils::isInt($id, true) || !array_key_exists($id, $this->mSiteCfg['channels'])) {
						$this->showMsg(array('msg' => '请选择影片分类'));
					}
					if (Lamb_Utils::isInt($tagid, true) && !array_key_exists($tagid, $types[$id])) {
						$this->showMsg(array('msg' => '请选择小分类'));
					}
					$urlparam['id'] = $id;
					$urlparam['tagid'] = $tagid;
					break;
				case 'listtask':
					if (Lamb_Utils::isInt($id, true) && array_key_exists($id, $this->mSiteCfg['channels'])) {
						$action = 'listtaskbyid';
						$urlparam['id'] = $id;
					}
					break;
				case 'todayalltask':
					$this->mResponse->redirect($this->getClientUrl('looper', 'createhtml'));
					break;
				default:
					if (empty($action)) {
						$this->showMsg(array('msg' => '请选择生成选项'));
					}				
					break;
			}
			$urlparam['ac'] = $action;
			$url = $this->getClientUrl('looper', 'createhtml', $urlparam);

			$this->mResponse->redirect($url);
		} else {
			include $this->load('tool_createstatic');
		}
	}
}