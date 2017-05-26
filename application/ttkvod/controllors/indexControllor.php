<?php
class indexControllor extends Ttkvod_Controllor
{
	public $webArr = array(
		'life' => 'minilife',
		'game' => 'minigame',
		'lady' => 'minilady',
		'new'  => 'mininew',
		'index' => 'mini_index',
		'text' => 'minitext',
		'img'  => 'miniimg',
		'shenqi' => 'minishenqi'
	);

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
			'year' => array(2016, 2015, 2014, 2013, 2012, 2011, 2010, 2009, 2008),
			'area' => array('大陆', '香港', '台湾', '韩国', '日本', '泰国', '欧美', '其他'),
			'movie' => array(
				'type' => array('动作','冒险','喜剧','爱情','科幻','恐怖','战争','犯罪', '悬疑','奇幻','武侠','剧情', '动画')
			),
			'tv' => array(
				'type' => array('剧情','古装','搞笑','悬疑','神话','偶像','科幻','言情','武侠','家庭','警匪','历史')
			),
			'show' => array(
				'type' => array('脱口秀','真人秀','选秀','美食','旅游','汽车','纪实','搞笑','少儿','娱乐','时尚','访谈','音乐')
			),
			'anime' => array(
				'type' => array('热血','搞笑','美少女','萝莉','机战','推理','竞技','冒险','社会','校园','剧情','其他')
			)
		);
		$listSqlTemplate = 'select id,name,mark,vedioPic from vedio where type=:tp and status=1 order by weekNum desc';
		$listSqlTemplateForArea = 'select id,name,mark,vedioPic from vedio where type=:tp and area=:area and status=1 order by updateDate desc';
		$listSqlTemplateForUpdate = 'select id,name,mark,vedioPic from vedio where type=:tp and status=1 order by updateDate desc';
		$listSqlTempalteForTag = 'select id,name,mark,vedioPic from vedio a,tag b,tagrelation c where type=:tp and a.id=c.vedioid and b.tagid=c.tagid and b.tagname=:tag and status=1 order by updateDate desc';

		$topPointSqlTemplate = 'select name,id,point from vedio where type=:tp and status=1 and point < 10 order by point desc,pointAll desc';
		include $this->load('index');
	}
	
}
