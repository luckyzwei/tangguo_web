<?php
class Ttkvod_Collect_Mbzy_Item extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ItemInterface
{
	protected $mTypeid;
	/**
	 * @var array
	 */
	public static $sTypeMap = array(
		'����Ƭ'=>array('typeid'=>1,'type'=>'����'),
		'����Ƭ'=>array('typeid'=>1,'type'=>'����'),
		'ϲ��Ƭ'=>array('typeid'=>1,'type'=>'ϲ��'),
		'����Ƭ'=>array('typeid'=>1,'type'=>'����'),
		'�ֲ�Ƭ'=>array('typeid'=>1,'type'=>'�ֲ�'),
		'�ƻ�Ƭ'=>array('typeid'=>1,'type'=>'�ƻ�'),
		'ս��Ƭ'=>array('typeid'=>1,'type'=>'ս��'),
		'��¼Ƭ'=>array('typeid'=>1,'type'=>'��¼'),
		'΢��Ӱ'=>array('typeid'=>1,'type'=>'΢��Ӱ'),
		
		'������'=>array('typeid'=>2,'type'=>'��½��'),
		'�۾�'  =>array('typeid'=>2,'type'=>'�۾�'),
		'����'	=>array('typeid'=>2,'type'=>'����'),
		'����'	=>array('typeid'=>2,'type'=>'����'),
		'̨��'	=>array('typeid'=>2,'type'=>'̨��'),
		'�վ�'	=>array('typeid'=>2,'type'=>'�վ�'),
		'̩��' 	=>array('typeid'=>2,'type'=>'̩��'),
		'����' 	=>array('typeid'=>2,'type'=>'����'),
		
		'����'	=>array('typeid'=>3,'type'=>'����'),
		'����'  =>array('typeid'=>4,'type'=>'����')
	);
	
	/**
	 * @var array
	 */
	public static $sPatt = array(
		'name' => '/ӰƬ���ƿ�ʼ����-->(.*?)</is',
		'vedioPic' => '/valign="top">[\s\r\n]*<img src="(.*?)"/is',
		'vedioType' => '/ӰƬ���Ϳ�ʼ����-->(.*?)</is',
		'mark' => '/ӰƬ״̬��ʼ����-->(.*?)</is',
		'remark' => '/ӰƬ��ע��ʼ����-->(.*?)</', 
		'area' => '/ӰƬ������ʼ����-->(.*?)</is',
		'syDate' => '/��ӳ���ڿ�ʼ����-->(.*?)</is',
		'directors' => '/ӰƬ���ƿ�ʼ����--><a.*?>(.*?)<\/a>/is',
		'actors' => '/ӰƬ��Ա��ʼ����-->(.*?)</is',
		'content' => '/ӰƬ���ܿ�ʼ����-->(.*?)</is',
		'pd_area' => '/��Դ:����(.*?)<\/table>/is',
		'a_item' => '/<li><input.*?value=\'(.*?)\'.*?<\/li><!--�ּ���ʼ(.*?)�ּ�����/is'
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
			'actors' => '����',
			'directors' => '����',
			'vedioType' => '',
			'area' => '����',
			'updateDate' => time(),
			'mark' => '',
			'vedioYear' => '����',
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
		
		if($ret['actors'] == 'δ��д' || $ret['actors'] == ''){
			$ret['actors'] = '����';
		}
		
		if($ret['directors'] == 'δ֪' || $ret['directors'] == ''){
			$ret['directors'] = '����';
		}
		
		if($ret['content'] == ''){
			$ret['content'] = '���޾������';
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
				$flag = ($time = strtotime($flag)) ? ('[$]' . date('Y-m-d', $time) . '��') : ('[$]' . $flag); 
			}else {
				$flag = '';
			}
						
			$playdata[] = $url . $flag;								
		}			
		$ret['playData'] = implode("\r\n", $playdata);			
		
		if ($this->mTypeid == 2 || $this->mTypeid == 4) {
			if(Lamb_Utils::isInt($externals['flag'], true) && $externals['flag']< 10000){
				$ret['mark'] = '������' . $externals['flag'] . '��';
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