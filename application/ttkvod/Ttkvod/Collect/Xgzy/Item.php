<?php
class Ttkvod_Collect_Xgzy_Item extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ItemInterface
{
	protected $mTypeid;
	/**
	 * @var array
	 */
	public static $sTypeMap = array(
		'����'=>array('typeid'=>1,'type'=>'����'),
		'����'=>array('typeid'=>1,'type'=>'����'),
		'ϲ��'=>array('typeid'=>1,'type'=>'ϲ��'),
		'����'=>array('typeid'=>1,'type'=>'����'),
		'�ֲ�'=>array('typeid'=>1,'type'=>'�ֲ�'),
		'�ƻ�'=>array('typeid'=>1,'type'=>'�ƻ�'),
		'ս��'=>array('typeid'=>1,'type'=>'ս��'),
				
		'����'=>array('typeid'=>2,'type'=>'��½��'),
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
		'name' => '/ӰƬ����: <\/strong>(.*?)</is',
		'vedioPic' => '/class="videoPic"><img src="(.*?)"/is',
		'vedioType' => '/ӰƬ����: <\/strong>(.*?)</is',
		'mark' => '/����״̬: <\/strong>(.*?)</is',
		'area' => '/��������: <\/strong>(.*?)</is',
		'syDate' => '/��ӳ���: <\/strong>(.*?)</is',
		'directors' => '/ӰƬ����: <\/strong>(.*?)</is',
		'actors' => '/ӰƬ����: <\/strong>(.*?)</is',
		'content' => '/class="contentNR"><div class="movievod"><p><p>(.*?)<\/p>/is',
		'a_item' => '/�ּ���ʼ(.*?)�ּ�����.*?value="(.*?)"/is'
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
		
		$html = iconv('utf-8', 'gbk//ignore', $html);
		
		foreach (array('name', 'actors', 'mark', 'area', 'syDate', 'vedioType', 'content', 'vedioPic', 'directors') as $key) {
			if (preg_match($patt[$key], $html, $result)) {
				$ret[$key] = trim($result[1]);
			}
		}
		//Lamb_Debuger::debug($ret['actors']);
		if($ret['actors'] == '����' || $ret['actors'] == ''){
			$ret['actors'] = '����';
		}
		
		if($ret['vedioYear'] == ''){
			$ret['vedioYear'] = '����';
		}
		
		if($ret['directors'] == '����' || $ret['directors'] == ''){
			$ret['directors'] = '����';
		}
		
		if($ret['content'] == '����'){
			$ret['content'] = '���޾������';
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
				$flag = ($time = strtotime($flag)) ? ('[$]' . date('Y-m-d', $time) . '��') : ('[$]' . $flag); 
			}else{
				$flag = '';
			}
						
			$playdata[] = $url . $flag;								
		}			
		$ret['playData'] = implode("\r\n", $playdata);			
		
		if ($this->mTypeid == 2 || $this->mTypeid == 4) {
			if(Lamb_Utils::isInt($ret['mark'], true)){
				$ret['mark'] = '������' . $ret['mark'] . '��';
			}
		}
		
		$ret['vedioYear'] = $ret['syDate'];
		$ret['vedioPic']  = (empty($ret['vedioPic']) || trim($ret['vedioPic']) == '/') ? $nopic : ('http://www.myzyzy.com' . trim($ret['vedioPic']));

		$error = self::S_OK;
		return $ret;
	}
}