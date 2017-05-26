<?php
class searchControllor extends Ttkvod_Controllor
{
	/**
	 * @var int
	 */
	protected $mSearchResultLimitNum = 100;
	
	/**
	 * @var int 
	 */
	protected $mDefaultPagesize = 11;	
	
	public function getControllorName()
	{
		return 'search';
	}
	
	public function indexAction()
	{
		$keywords = trim($this->mRequest->q);
		$auto = trim($this->mRequest->auto);
		$typeid = trim($this->mRequest->typeid);
		$order = trim($this->mRequest->order);
		$page = trim($this->mRequest->p);
		$pagesize = trim($this->mRequest->ps);		
		
		if (empty($keywords)) {
			$this->mResponse->redirect($this->mLinkRouter->router('', array('id' => 'index')));
		}
		if (!Lamb_Utils::isInt($order, true)) {
			$order = 0;
		}
		if (!Lamb_Utils::isInt($page, true) || $page == 0) {
			$page = 1;
		}		
		if (!Lamb_Utils::isInt($pagesize, true)) {
			$pagesize = $this->mDefaultPagesize;
		}		
		
		$publicColumn = 'a.id as id,name,mark,viewNum,monthNum,weekNum,actors,directors,tag,vedioYear,point,monthPoint,pointAll,pointNum,area,vedioPic,type,updateDate,content';
		$aPrepareSource = array();
	
		switch ($auto) {
			case 'true':
				{
					$sql = "select $publicColumn from vedio where name=:name and status=1 ";
					$aPrepareSource[':name'] = array($keywords, PDO::PARAM_STR);
				}
				break;
			case 'tag':
				{
					$sql = "select $publicColumn from vedio a,tag b,tagrelation c,vedio_data d where a.id=c.vedioid and c.tagid=b.tagid and b.tagname=:tagname and a.id=d.id and a.status=1 ";
					$aPrepareSource[':tagname'] = array($keywords, PDO::PARAM_STR);
					if (!Lamb_Utils::isInt(trim($this->mRequest->order), true)) {
						$order = 1;
					}					
				}
				break;
			default :
			
				//$keywords = iconv('gbk', 'utf-8', $keywords);
				//$fulltext = Ttkvod_Utils::encodeFullSearchStr($keywords);

				/*
				$sql = "select $publicColumn from (select $publicColumn from vedio a, freetexttable(vedio, tagname, :fullsearch, 100) b
						where a.id=b.[KEY] and a.status=1 union all select $publicColumn from vedio a,tag b,tagrelation c where c.tagid=b.tagid and b.tagname=:tagname and a.status=1) a where 1=1";
				$aPrepareSource[':fullsearch'] = array($fulltext, PDO::PARAM_STR);
				$aPrepareSource[':tagname'] = array($keywords, PDO::PARAM_STR);
				*/
				
				$sql = "select $publicColumn from vedio a where status=1 and name like :name ";
				$aPrepareSource[':name'] = array('%' . $keywords . '%', PDO::PARAM_STR);
		}
		
		if (Lamb_Utils::isInt($typeid, true) && array_key_exists($typeid, $this->mSiteCfg['channels'])) {
			$sql .= ' and type=:typeid';
			$aPrepareSource[':typeid'] = array($typeid, PDO::PARAM_INT);
		} else {
			$typeid = '';
		}
		
		switch ($order) {
			case 1:
				$sql .= ' order by viewNum desc';
				break;
			case 2:
				$sql .= ' order by point desc,pointAll desc';
				break;
			case 3:
				$sql .= ' order by updateDate desc';
				break;
			case 4:
				$sql .= ' order by weekNum desc';
				break;
			case 5:
				$sql .= ' order by monthNum desc';
				break;
			case 6:
				$sql .= ' order by monthPoint desc';
				break;
			default:
				$order = 0;
		}
	
		//$hotkeywords = $this->mSiteCfg['hot_search_keywords']
		$params = array('id' => 'search', 'auto' => $auto, 'q' => $keywords, 'order' => $order);
		$pageUrlPrev = $this->mLinkRouter->router('', $params + array('typeid' => $typeid)) . $this->mRouter->setUrlDelimiter();
		$firstPageUrl = $pageUrlPrev . $this->mRouter->url(array('p' => 1));
		$prevPageUrl = $pageUrlPrev . $this->mRouter->url(array('p' => '#prevPage#'), false);
		$nextPageUrl = $pageUrlPrev . $this->mRouter->url(array('p' => '#nextPage#'), false);
		$lastPageUrl = $pageUrlPrev . $this->mRouter->url(array('p' => '#lastPage#'), false);
		$pageUrl = $pageUrlPrev . $this->mRouter->url(array('p' => '#page#'), false);
		
		include $this->load('search');
	}
	
	/*
	public function ajaxAction()
	{

		$keywords = trim($this->mRequest->q);
		if (!empty($keywords)) {
			$sql = "select top 6 id,name,topType,actors,point from vedio where status=1 and name like '" . Lamb_App::getGlobalApp()->getSqlHelper()->escapeBlur($keywords) . "%' order by weekNum desc";
			$cache = Ttkvod_Cache_Factory::getCache()->setIdentity(Lamb_Db_Select::getSqlIdentity($sql))
													 ->setCacheTime(24 * 3600);
			$select = new Lamb_Db_Select($sql);
			$data = $select->setOrGetCache($cache)->query()->toArray();
			for ($i = 0, $j = count($data); $i < $j; $i ++) {
				$data[$i]['name'] = Lamb_App_Response::encodeURIComponent($data[$i]['name']);
				$data[$i]['actors'] = Lamb_App_Response::encodeURIComponent($data[$i]['actors']);
			}
			header('Content-type:text/html;charset=gbk');
			$this->addAjaxDomainScript();
			$this->mResponse->eecho(json_encode($data));
		}
	}
	*/

	public function ajaxAction()
	{

		$keywords = trim($this->mRequest->q);
		if (!empty($keywords)) {
			$sql = "select top 8 id,name,topType,actors,point,vedioPic,directors,vedioType from vedio where status=1 and  name like '" . Lamb_App::getGlobalApp()->getSqlHelper()->escapeBlur($keywords) . "%' order by weekNum desc";
			$cache = Ttkvod_Cache_Factory::getCache()->setIdentity(Lamb_Db_Select::getSqlIdentity($sql))
													 ->setCacheTime(24 * 3600);
			$select = new Lamb_Db_Select($sql);
			$data = $select->setOrGetCache($cache)->query()->toArray();		
			
			for ($i = 0, $j = count($data); $i < $j; $i ++) {
				$data[$i]['name'] = Lamb_App_Response::encodeURIComponent($data[$i]['name']);
				$data[$i]['directors'] = Lamb_App_Response::encodeURIComponent($data[$i]['directors']);
				$data[$i]['vedioType'] = Lamb_App_Response::encodeURIComponent($data[$i]['vedioType']);
				$data[$i]['actors'] = Lamb_App_Response::encodeURIComponent($data[$i]['actors']);
				$data[$i]['vedioPic'] = Lamb_App_Response::encodeURIComponent(Ttkvod_Utils::getImgPath($data[$i]['vedioPic']));
			}
			
			$this->showResults(1, array('data' => $data) );
		}
		
		$this->showResults(1, null);
	}

}