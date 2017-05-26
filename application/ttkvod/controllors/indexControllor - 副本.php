<?php
class indexControllor extends Ttkvod_Controllor
{
	public function getControllorName()
	{
		return 'index';
	}
	
	public function indexAction()
	{
		$this->mCacheTime = $this->mRequest->ct;
		if (!Lamb_Utils::isInt($this->mCacheTime)) {
			$this->mCacheTime = 0;
		}	
		$searchIndex = array(
			'year' => array(2012, 2011, 2010, 2009, 2008, 2007, 2006, 2005, 2004, 2003, 2002, 2001, 2000),
			'area' => array('大陆', '香港', '台湾', '韩国', '日本', '泰国', '欧美', '其他'),
			'movie' => array(
				'type' => array('动作', '冒险', '爱情', '喜剧', '科幻', '恐怖', '战争', '灾难', '犯罪', '悬疑',
					'奇幻', '武侠', '家庭', '记录', '剧情', '伦理', '高清'),
				
				'stars' => array('周星驰', '章子怡', '周润发', '成 龙', '甄子丹', '刘德华', '梁朝伟', 
					'刘青云', '姜 文', '李连杰', '古天乐', '谢霆锋', '范冰冰', '葛 优')
			),
			'tv' => array(
				'type' => array('剧情', '偶像', '情感', '伦理', '喜剧', '古装', '奇幻', '犯罪', '战争', '悬疑', 
					'武侠', 'TVB', '动作', '其他'),
				'stars' => array('林心如', '胡  歌', '杨  幂', '殷  桃', '孙红雷', '唐国强', '陈道明', '王宝强',
					'海  清', '张国立', '陈宝国', '李幼斌', '黄晓明', '闫  妮', '霍思燕', '陈乔恩', '杨丞琳', '冯绍峰', '姚  晨')
			),
			'show' => array(
				'type' => array('热门', '交友', '娱乐', '选秀', '时尚', '小品', '饮食', '访谈', '纪录', '法制', 
					'军事', '写真', '资讯', '戏曲', '真人', '脱口', '音乐', '篮球', '足球', '其他')
			),
			'anime' => array(
				'type' => array('新番', '少儿', '热血', '搞笑', '少女', '萝莉', '动作', '机战', '推理', '竞技',
					'魔幻', '冒险', '社会', '校园', '情感', '剧情', '剧场版')
			)
		);
		$listSqlTemplate = 'select id,name,mark,vedioPic from vedio where toptype=:tp order by weekNum desc';
		$listSqlTemplateForArea = 'select id,name,mark,vedioPic from vedio where toptype=:tp and area=:area order by weekNum desc';
		$listSqlTemplateForUpdate = 'select id,name,mark,vedioPic from vedio where toptype=:tp order by updateDate desc';
		$listSqlTempalteForTag = 'select id,name,mark,vedioPic from vedio a,tag b,tagrelation c where topType=:tp and a.id=c.vedioid and b.tagid=c.tagid and b.tagname=:tag order by weekNum desc';
		$topSqlTemplate = 'select name,id,point from vedio where toptype=:tp order by monthNum desc';
		include $this->load('index');
	}
	
	public function topAction()
	{
		$id = trim($this->mRequest->vid);
		$isall = !Lamb_Utils::isInt($id, true) || !array_key_exists($id, $this->mSiteCfg['channels']);
		$sql = 'select name,id,point,vedioType,mark,vedioPic,pointNum,syDate,area,actors from vedio where topType=:tp order by weekNum desc';
		$sqlitem = 'select name,id,point,vedioType,mark,vedioPic,pointNum,syDate,area,actors from vedio where toptype=:tp order by weekNum desc';
		include $this->load('top');
	}
	
	public function dialogAction()
	{
		$param = trim($this->mRequest->param);
		$old_username = trim($this->mRequest->oun);
		$old_password = trim($this->mRequest->ops);
		$id = trim($this->mRequest->id);
		
		if (!$param) {
			exit;
		}
		
		$form_rand_key = Lamb_Utils::authcode(time(), $this->mSiteCfg['form_rank_key'], 'ENCODE', $this->mSiteCfg['form_rank_expire']);
		include $this->load('dialog');
	}
	
	public function newAction()
	{
		include $this->load('new');	
	}
	
	public function serverAction()
	{
		$clientid = trim($this->mRequest->clientid);
		$encode = trim($this->mRequest->code);
		$server = new Ttkvod_OutServices_Server();
		$server->runFromRemote($clientid, $encode, $errorno);
	}
	
	public function imgsysAction()
	{
		//Ttkvod_Utils::flushCDN(array('http://cdn.ttkvod.com/themes/default/css/detail.css'));
		$ipSources = explode(',', $this->mSiteCfg['comment']['forbin_ips']);
		$ip = '111.72.29.2';
		$ret = true;
		foreach ($ipSources as $_ip) {
			if (strpos($ip, $_ip) !== false) {
				$ret = false;
				break;
			}
		}
		Lamb_Debuger::debug($ret);		
	}
	
	public function uplistAction()
	{
		$ret = '';
		$version = @$_SERVER['HTTP_TTKVOD_CUSTOM_HTTP_HEADER'];
		$name = $this->mRequest->getGet('n');
		if (!empty($name) && $version == '3.0') {
			$model = new Ttkvod_Model_Video();
			$ret = $model->getPlayDataByName($name);
		}
		header('Content-Length:' . strlen($ret));
		$this->mResponse->eecho($ret);
	}
	

	public function getRandomMemberUrl($url)
	{
		$randomHosts = array('http://member1.ttkvod.com', 'http://member2.ttkvod.com', 'http://member3.ttkvod.com', 'http://member4.ttkvod.com', 'http://member5.ttkvod.com');
		if (!Lamb_Utils::isHttp($url)) {
			return $url;
		}
		$index = time() % (count($randomHosts) + 1);
		
		if ($index == 0) {
			return $url;
		}
		
		$index --;
		
		$url = substr($url, 7);
		if (($pos = strpos($url, '/')) === false) {
			return 'http://' . $url;
		}
		return $randomHosts[$index] . substr($url, $pos);	
	}
}
/**
 * @param float $point
 * @return string
 */
function getTopHeaderCss($point)
{
	if ($point<=0) {
		return 'star0';
	}
	if ($point>0 && $point<=2) {
		return 'star1';
	}
	if ($point>2 && $point<=5) {
		return 'star2';
	}
	if ($point>6 && $point<=8) {
		return 'star3';
	}
	if ($point>8 && $point<=9) {
		return 'star4';
	}
	return 'star5';	
}

function splitPoint($point)
{
	$arr = explode('.', $point);
	if (empty($arr[0])) {
		$arr[0]	=	'0';
	}
	return '<strong>' . $arr[0] . '.</strong>' . $arr[1];
}

function getTypeName($id)
{
	$cfg = Lamb_Registry::get(CONFIG);
	return $cfg['channels'][$id]['name'];
}

function randerTag($tag)
{
	static $model = null;
	if (null === $model) {
		$model = new Ttkvod_Model_Tag;
	}
	return '<span>' . implode('</span><span>', $model->parse($tag)) . '</span>';
}