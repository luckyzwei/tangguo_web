<?php
class Ttkvod_Collect_Xgzy_Item extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ItemInterface
{
	protected $mTypeid;
	/**
	 * @var array
	 */
	public static $sTypeMap = array(
		'动作'=>array('typeid'=>1,'type'=>'动作'),
		'剧情'=>array('typeid'=>1,'type'=>'剧情'),
		'喜剧'=>array('typeid'=>1,'type'=>'喜剧'),
		'爱情'=>array('typeid'=>1,'type'=>'爱情'),
		'恐怖'=>array('typeid'=>1,'type'=>'恐怖'),
		'科幻'=>array('typeid'=>1,'type'=>'科幻'),
		'战争'=>array('typeid'=>1,'type'=>'战争'),
				
		'国产'=>array('typeid'=>2,'type'=>'大陆剧'),
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
		'name' => '/影片名称: <\/strong>(.*?)</is',
		'vedioPic' => '/class="videoPic"><img src="(.*?)"/is',
		'vedioType' => '/影片分类: <\/strong>(.*?)</is',
		'mark' => '/连载状态: <\/strong>(.*?)</is',
		'area' => '/地区分类: <\/strong>(.*?)</is',
		'syDate' => '/上映年份: <\/strong>(.*?)</is',
		'directors' => '/影片导演: <\/strong>(.*?)</is',
		'actors' => '/影片主演: <\/strong>(.*?)</is',
		'content' => '/class="contentNR"><div class="movievod"><p><p>(.*?)<\/p>/is',
		'a_item' => '/分集开始(.*?)分集结束.*?value="(.*?)"/is'
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
		//$nopic = @$GLOBALS['aCfg']['nopic_path'];
		$nopic = '/upload/nopic.gif';
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
		
		$html = iconv('utf-8', 'gbk//ignore', $html);
		
		foreach (array('name', 'actors', 'mark', 'area', 'syDate', 'vedioType', 'content', 'vedioPic', 'directors') as $key) {
			if (preg_match($patt[$key], $html, $result)) {
				$ret[$key] = trim($result[1]);
			}
		}
		//Lamb_Debuger::debug($ret['actors']);
		if($ret['actors'] == '内详' || $ret['actors'] == ''){
			$ret['actors'] = '不详';
		}
		
		if($ret['vedioYear'] == ''){
			$ret['vedioYear'] = '不详';
		}
		
		if($ret['directors'] == '内详' || $ret['directors'] == ''){
			$ret['directors'] = '不详';
		}
		
		if($ret['content'] == '内详'){
			$ret['content'] = '暂无剧情介绍';
		}
		
		if (isset(self::$sTypeMap[$ret['vedioType']])) {
			$this->mTypeid = $ret['topType'] = self::$sTypeMap[$ret['vedioType']]['typeid'];
			$ret['vedioType'] = self::$sTypeMap[$ret['vedioType']]['type'];
		}
		
		if (!preg_match_all($patt['a_item'], $html, $result, PREG_SET_ORDER)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;		
		}
		
		$playdata = array();
		foreach ($result as $item) {
			$url = trim($item[2]);
			$flag = trim($item[1]);
			
			if ($this->mTypeid == 1) {
				$flag = '[$]' . strtoupper($flag);		
			}else if($this->mTypeid == 3) {
				$flag = ($time = strtotime($flag)) ? ('[$]' . date('Y-m-d', $time) . '期') : ('[$]' . $flag); 
			}else{
				$flag = '';
			}
						
			$playdata[] = $url . $flag;								
		}			
		$ret['playData'] = implode("\r\n", $playdata);			
		
		if ($this->mTypeid == 2 || $this->mTypeid == 4) {
			if(Lamb_Utils::isInt($ret['mark'], true)){
				$ret['mark'] = '更新至' . $ret['mark'] . '集';
			}
		}
		
		$ret['vedioYear'] = $ret['syDate'];
		$ret['vedioPic']  = (empty($ret['vedioPic']) || trim($ret['vedioPic']) == '/') ? $nopic : ('http://www.myzyzy.com' . trim($ret['vedioPic']));

		$error = self::S_OK;
		return $ret;
	}
}