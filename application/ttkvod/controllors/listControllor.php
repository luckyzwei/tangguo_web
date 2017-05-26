<?php
class listControllor extends Ttkvod_Controllor
{
	/**
	 * @var int 
	 */
	protected $mDefaultPagesize = 24;
	
	public function getControllorName()
	{
		return 'list';
	}	
	
	public function indexAction()
	{

		if (!Lamb_Utils::isInt($this->mCacheTime)) {
			$this->mCacheTime = 0;
		}	
		$id = trim($this->mRequest->id);
		$tag = trim($this->mRequest->tag);
		$area = trim($this->mRequest->area);
		$order = trim($this->mRequest->order);
		$page = trim($this->mRequest->p);
		$pagesize = trim($this->mRequest->ps);

		if (!Lamb_Utils::isInt($id)) {
			throw new Lamb_Exception("error!");
		}
		if (!array_key_exists($id,  $this->mSiteCfg['channels'])) {
			throw new Lamb_Exception("error!");
		}
		if (!Lamb_Utils::isInt($page, true) || $page == 0) {
			$page = 1;
		}
		if (!Lamb_Utils::isInt($order, true)) {
			$order = 0;
		}
		if (!Lamb_Utils::isInt($pagesize, true)) {
			$pagesize = $this->mDefaultPagesize;
		}
		$topTypeInfos =  $this->mSiteCfg['channels'][$id];
		$searchIndex = $this->getSearchIndex();
		$currentSearchIndex = $searchIndex[$id];
		$publicCoulmn = 'id,name,mark,actors,directors,point,vedioYear,pointNum,area,vedioPic,type';
		$sql = "select $publicCoulmn from vedio where type=:type and status=1 ";
		$aPrepareSource = array(':type' => array($id, PDO::PARAM_INT));
		
		if ($tag) {
			$tag = $tag == '萝莉' ?  'LOLI' : $tag;
			$sql = "select $publicCoulmn from vedio a,tag b,tagrelation c where type=:type and a.id=c.vedioid and c.tagid=b.tagid and b.tagname=:tagname and status=1 ";
			$aPrepareSource[':tagname'] = array($tag, PDO::PARAM_STR);
		}
		
		if ($area) {
			if ($area != '其他') {
				if ($area == '欧美') {
					$sql .= " and area in('欧美', '美国', '英国', '法国', '德国')";
				} else {
					$sql .= ' and area=:area';
					$aPrepareSource[':area'] = array($area, PDO::PARAM_STR);
				}
			} else {
				$sql .= " and area not in ('" . implode("','", array_slice($currentSearchIndex['areas'], 0, count($currentSearchIndex['areas']) - 1)) . "')";
			}
		}
		
		switch ($order) {
			case 1:
				$sql .= ' order by viewNum desc';
				break;
			case 2:
				$sql .= ' and point<10 order by point desc,pointAll desc';
				break;
			case 3:
				$sql .= ' order by weeknum desc';
				break;
			case 4:
				$sql .= ' order by monthnum desc';
				break;
			case 5:
				$sql .= ' order by monthPoint desc';
				break;
			default:
				$sql .= ' order by updateDate desc';
				$order = 0;
		}
		
		$topSqlTemplate = 'select name,id,point,vedioPic from vedio where type=:tp and status=1 order by monthPoint desc';
		$pageParam = array('order' => $order, 'tag' => $tag, 'area' => $area, 'id' => $id);
		$pageUrl = $this->mLinkRouter->router('list', $pageParam + array('p' => 0));
		$firstPageUrl = $this->mLinkRouter->router('list', $pageParam + array('p' => 1));
		$prevPageUrl  = $this->mLinkRouter->router('list', $pageParam + array('p' => -1));
		$nextPageUrl  = $this->mLinkRouter->router('list', $pageParam + array('p' => -2));
		$lastPageUrl  = $this->mLinkRouter->router('list', $pageParam + array('p' => -3));
		Lamb_Registry::set('current_type_id', $id);
		
		include $this->load('vodlist');
	}
	
	/**
	 * @param float $point
	 * @return string
	 */
	public static function showPoint($point)
	{
		$point = (string)$point;
		$points = explode('.', $point);
		if (empty($points[0])) {
			$points[0] = '0';
		}
		return "{$points[0]}<sub>.{$points[1]}</sub>";
	}
	
	/**
	 * @return int
	 */
	public function getListPagesize()
	{
		return $this->mDefaultPagesize;
	}
}