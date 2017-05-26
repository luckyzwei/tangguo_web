<?php
class listControllor extends Ttkvod_Controllor
{
	/**
	 * @var int 
	 */
	protected $mDefaultPagesize = 25;
	
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
		$year = trim($this->mRequest->year);
		$area = trim($this->mRequest->area);
		$order = trim($this->mRequest->order);
		$page = trim($this->mRequest->p);
		$pagesize = trim($this->mRequest->ps);
		$pinyin = trim($this->mRequest->pinyin);

		if (!Lamb_Utils::isInt($id)) {
			throw new Lamb_Exception("listid : \"$id\" is illegal!");
		}
		if (!array_key_exists($id,  $this->mSiteCfg['channels'])) {
			throw new Lamb_Exception("listid : \"$id\" is not found!");
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
		$publicCoulmn = 'id,name,mark,viewNum,actors,directors,point,vedioYear,pointNum,area,vedioPic,vedioType';
		$sql = "select $publicCoulmn from vedio where topType=:topType and status=1 ";
		$aPrepareSource = array(':topType' => array($id, PDO::PARAM_INT));
		
		if ($tag) {
			$sql = "select $publicCoulmn from vedio a,tag b,tagrelation c where topType=:topType and a.id=c.vedioid and c.tagid=b.tagid and b.tagname=:tagname and status=1 ";
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

		if($pinyin){
			$sql .= " and pinyin like :pinyin ";	
			$aPrepareSource[':pinyin'] = array($pinyin . "%", PDO::PARAM_STR);
		}
		
		if ($year) {
			if ($year != '其他') {
				$sql .= ' and vedioYear=:vedioYear';
				$aPrepareSource[':vedioYear'] = array($year, PDO::PARAM_STR);
			} else {
				$sql .= " and vedioYear not in ('" . implode("','", array_slice($currentSearchIndex['years'], 0, count($currentSearchIndex['years']) - 1)) . "')";
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
		$pageParam = array('order' => $order, 'tag' => $tag, 'year' => $year, 'area' => $area, 'id' => $id, 'pinyin' => $pinyin);
		$pageUrl = $this->mLinkRouter->router('list', $pageParam + array('p' => 0));
		$firstPageUrl = $this->mLinkRouter->router('list', $pageParam + array('p' => 1));
		$prevPageUrl = $this->mLinkRouter->router('list', $pageParam + array('p' => -1));
		$nextPageUrl = $this->mLinkRouter->router('list', $pageParam + array('p' => -2));
		$lastPageUrl = $this->mLinkRouter->router('list', $pageParam + array('p' => -3));
		Lamb_Registry::set('current_type_id', $id);
		
		include $this->load('vodlist');
	}
	
	/**
	 * @return array
	 */
	public function getSearchIndex()
	{
		static $years = array('2016','2015', '2014','2013','2012','2011','2010','2009','2008','2007','2006','2005','2004','2003','其他');
		static $areas = array('大陆', '香港', '台湾', '韩国', '日本', '泰国', '美国', '英国', '法国', '欧美','其他');
		return array(
				1 => array(
					'types' => array('动作','冒险','喜剧','爱情','科幻','恐怖','战争','灾难','犯罪','悬疑','奇幻','武侠','家庭','记录','剧情','伦理','高清', '微电影', '动画'),
					'areas' => array('大陆', '香港', '台湾', '韩国', '日本', '泰国', '美国', '英国', '法国', '欧美', '德国', '印度', '新加坡', '西班牙', '其他'),					'years' => $years
				),
				2 => array(
					'types' => array('剧情','偶像','情感','伦理','喜剧','古装','奇幻','犯罪','战争','动作','悬疑','武侠','TVB','家庭','警匪','历史'),
					'areas' => $areas,
					'years' => $years
				),
				3 => array(
					'types' => array('热门','交友','娱乐','选秀','时尚','小品','饮食','访谈','纪录','法制','军事','写真','资讯','戏曲','真人','音乐','篮球','足球','其他'),
					'areas' => $areas,
					'years' => $years
				),
				4 => array(
					'types' => array('新番','少儿','热血','搞笑','少女','萝莉','动作','机战','推理','竞技','魔幻','冒险','社会','校园','情感','剧情','剧场版'),
					'areas' => $areas,
					'years' => $years
				),
				5 => array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z')
			);
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