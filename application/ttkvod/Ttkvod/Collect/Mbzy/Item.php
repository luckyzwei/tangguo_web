<?php
class Ttkvod_Collect_Mbzy_Item extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ItemInterface
{
	protected $mTypeid;
	/**
	 * @var array
	 */
	public static $sTypeMap = array(
		'动作片'=>array('typeid'=>1,'type'=>'动作'),
		'剧情片'=>array('typeid'=>1,'type'=>'剧情'),
		'喜剧片'=>array('typeid'=>1,'type'=>'喜剧'),
		'爱情片'=>array('typeid'=>1,'type'=>'爱情'),
		'恐怖片'=>array('typeid'=>1,'type'=>'恐怖'),
		'科幻片'=>array('typeid'=>1,'type'=>'科幻'),
		'战争片'=>array('typeid'=>1,'type'=>'战争'),
		'纪录片'=>array('typeid'=>1,'type'=>'记录'),
		'微电影'=>array('typeid'=>1,'type'=>'微电影'),
		
		'国产剧'=>array('typeid'=>2,'type'=>'大陆剧'),
		'港剧'  =>array('typeid'=>2,'type'=>'港剧'),
		'韩剧'	=>array('typeid'=>2,'type'=>'韩剧'),
		'美剧'	=>array('typeid'=>2,'type'=>'美剧'),
		'台剧'	=>array('typeid'=>2,'type'=>'台剧'),
		'日剧'	=>array('typeid'=>2,'type'=>'日剧'),
		'泰剧' 	=>array('typeid'=>2,'type'=>'泰剧'),
		'海外' 	=>array('typeid'=>2,'type'=>'海外'),
		
		'综艺'	=>array('typeid'=>3,'type'=>'综艺'),
		'动漫'  =>array('typeid'=>4,'type'=>'动漫')
	);
	
	/**
	 * @var array
	 */
	public static $sPatt = array(
		'name' => '/影片名称开始代码-->(.*?)</is',
		'vedioPic' => '/valign="top">[\s\r\n]*<img src="(.*?)"/is',
		'vedioType' => '/影片类型开始代码-->(.*?)</is',
		'mark' => '/影片状态开始代码-->(.*?)</is',
		'remark' => '/影片备注开始代码-->(.*?)</', 
		'area' => '/影片地区开始代码-->(.*?)</is',
		'syDate' => '/上映日期开始代码-->(.*?)</is',
		'directors' => '/影片名称开始代码--><a.*?>(.*?)<\/a>/is',
		'actors' => '/影片演员开始代码-->(.*?)</is',
		'content' => '/影片介绍开始代码-->(.*?)</is',
		'pd_area' => '/来源:西瓜(.*?)<\/table>/is',
		'a_item' => '/<li><input.*?value=\'(.*?)\'.*?<\/li><!--分集开始(.*?)分集结束/is'
	);
	
	public function __construct()
	{

	}
	
	public function setTypeid($typeid = null)
	{
		if (null === $typeid) {
			return $this->mTypeid;
		}
		$this->mTypeid = (int)$typeid;
		return $this;
	}
	
	/**
	 * @Ttkvod_Collect_ItemInterface implemtions
	 */	
	public function collect($url, $externals = null, &$error = null)
	{
		$nopic = @$GLOBALS['aCfg']['nopic_path'];
		$ret = array(
			'name' => '',
			'actors' => '不详',
			'directors' => '不详',
			'vedioType' => '',
			'area' => '其他',
			'updateDate' => time(),
			'mark' => '',
			'vedioYear' => '不详',
			'content' => '',
			'playData' => '',
			'syDate' => '',
			'vedioPic' => $nopic,
			'isEnd' => 0,
			'topType' => $this->mTypeid
		);
		$patt = self::$sPatt;
		
		if (!Lamb_Utils::isHttp($url)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;
		}
		
		if (! ($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;
		}
		
		foreach (array('name', 'actors', 'mark', 'area', 'syDate', 'vedioType', 'content', 'vedioPic', 'directors') as $key) {
			if (preg_match($patt[$key], $html, $result)) {
				$ret[$key] = trim($result[1]);
			}
		}
		
		if(preg_match($patt['remark'], $html, $result)){
			$movie_mark = trim($result[1]);
		}
		
		Ttkvod_Utils::filterCollectContent($ret['content']);
		
		if($ret['actors'] == '未填写' || $ret['actors'] == ''){
			$ret['actors'] = '不详';
		}
		
		if($ret['directors'] == '未知' || $ret['directors'] == ''){
			$ret['directors'] = '不详';
		}
		
		if($ret['content'] == ''){
			$ret['content'] = '暂无剧情介绍';
		}
		
		if (isset(self::$sTypeMap[$ret['vedioType']])) {
			$this->mTypeid = $ret['topType'] = self::$sTypeMap[$ret['vedioType']]['typeid'];
			$ret['vedioType'] = self::$sTypeMap[$ret['vedioType']]['type'];
		}
		
		if (!preg_match($patt['pd_area'], $html, $result)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;		
		}
		$result = trim($result[1]);
		
		if (!preg_match_all($patt['a_item'], $result, $result, PREG_SET_ORDER)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;		
		}
		
		$playdata = array();
		foreach ($result as $item) {
			$url = trim($item[1]);
			$flag = trim($item[2]);
			if($url == ''){
				continue;
			}
			
			if ($this->mTypeid == 1) {
				$flag = '[$]' . $flag;		
			}else if($this->mTypeid == 3) {
				$flag = ($time = strtotime($flag)) ? ('[$]' . date('Y-m-d', $time) . '期') : ('[$]' . $flag); 
			}else {
				$flag = '';
			}
						
			$playdata[] = $url . $flag;								
		}			
		$ret['playData'] = implode("\r\n", $playdata);			
		
		if ($this->mTypeid == 2 || $this->mTypeid == 4) {
			if(Lamb_Utils::isInt($externals['flag'], true) && $externals['flag']< 10000){
				$ret['mark'] = '更新至' . $externals['flag'] . '集';
			}else{
				$ret['mark'] = $movie_mark;
			}
		}
		
		if($this->mTypeid == 1){
			$ret['mark'] = $movie_mark;
		}
		
		if ($this->mTypeid == 3){
			$ret['mark'] = $externals['flag'];
		}
		
		if (empty($ret['vedioPic'])) {
			$ret['vedioPic'] = $nopic;
		} 
		
		$ret['vedioYear'] = $ret['syDate'];

		$error = self::S_OK;
		return $ret;
	}
}