<?php
class Ttkvod_Collect_Hakuzy_Item extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ItemInterface
{
	/**
	 * @var array
	 */
	public static $sTypeMap = array(
		'����Ƭ'=>array('typeid'=>1,'type'=>'����'),
		'ϲ��Ƭ'=>array('typeid'=>1,'type'=>'ϲ��'),
		'����Ƭ'=>array('typeid'=>1,'type'=>'����'),
		'����Ƭ'=>array('typeid'=>4,'type'=>'����'),
		'�ֲ�Ƭ'=>array('typeid'=>1,'type'=>'�ֲ�'),
		'�ƻ�Ƭ'=>array('typeid'=>1,'type'=>'�ƻ�'),
		'ս��Ƭ'=>array('typeid'=>1,'type'=>'ս��'),
		'��¼Ƭ'=>array('typeid'=>3,'type'=>'��¼'),
		'����Ƭ'=>array('typeid'=>1,'type'=>'����'),
		'��½��'=>array('typeid'=>2,'type'=>'��½��'),
		'ŷ����'=>array('typeid'=>2,'type'=>'ŷ����'),
		'��̨��'=>array('typeid'=>2,'type'=>'��̨��'),
		'�պ���'=>array('typeid'=>2,'type'=>'�պ���'),
		'̩��' => array('typeid'=>2,'type'=>'̩��'),
		'��������'=>array('typeid'=>3,'type'=>'��������'),
		'����'=>array('typeid'=>3,'type'=>'����MV'),
		'���籭'=>array('typeid'=>3,'type'=>'����'),
		'720P����'=>array('typeid'=>1,'type'=>'����')
	);
	
	/**
	 * @var array
	 */
	public static $sPatt = array(
		'typeid' => '/<!--ӰƬ���Ϳ�ʼ����-->(.*?)<!--ӰƬ���ͽ�������-->/is',
		'playlist' => '/<!--�����б�ʼ����-->(.*?)<!--�����б��������-->/is',
		'playitemcontent' => '/<a>(.*?)<\/a>/is',
		'playitemname' => '/<!--�ּ�����ʼ(.*?)�ּ�������-->/is',
		'name' => '/<!--ӰƬ���ƿ�ʼ����-->(.*?)<!--ӰƬ���ƽ�������-->/is',
		'actor' => '/<!--ӰƬ��Ա��ʼ����-->(.*?)<!--ӰƬ��Ա��������-->/is',
		'director' => '/<!--ӰƬ���ݿ�ʼ����-->(.*?)<!--ӰƬ���ݽ�������-->/is',
		'area' => '/<!--ӰƬ������ʼ����-->(.*?)<!--ӰƬ������������-->/is',
		'updateDate' => '/<!--ӰƬ����ʱ�俪ʼ����-->(.*?)<!--ӰƬ����ʱ���������-->/is',
		'mark1' => '/<!--ӰƬ״̬��ʼ����-->(.*?)<!--ӰƬ״̬��������-->/is',
		'mark2' => '/<!--ӰƬ���꿪ʼ����-->(.*?)<!--ӰƬ�����������-->/is',
		'syDate' => '/<!--��ӳ���ڿ�ʼ����-->(.*?)<!--��ӳ���ڽ�������-->/is',
		'description' => '/<!--ӰƬ���ܿ�ʼ����-->(.*?)<!--ӰƬ���ܽ�������-->/is',
		'pic' => '/<!--ӰƬͼƬ��ʼ����-->(.*?)<!--ӰƬͼƬ��������-->/is',
	);
	
	public function __construct()
	{

	}
	
	/**
	 * @Ttkvod_Collect_ItemInterface implemtions
	 */	
	public function collect($url, $externals = null, &$error = null)
	{
		$nopic = $GLOBALS['aCfg']['nopic_path'];
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
			'topType' => ''
		);
		$patt = self::$sPatt;
		$temp = '';
		
		if (!Lamb_Utils::isHttp($url)) {
			$url = 'http://hakuzy.com' . $url;
		}
		
		if (! ($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;
		}
		
		//topType
		if (!preg_match($patt['typeid'], $html, $aResult)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;
		}
		$temp = trim($aResult[1]);
		if (!array_key_exists($temp, self::$sTypeMap)) {
			$error = self::E_TYPE_FORBIN_COLLECT;
			return $ret;
		}
		$ret['topType'] = self::$sTypeMap[$temp]['typeid'];
		$ret['vedioType'] = self::$sTypeMap[$temp]['type'];
		
		//playData
		if (!preg_match($patt['playlist'], $html, $aResult)) {
			Lamb_Debuger::debug($html);
			$error = self::E_COLLECT_PLAYDATA_FAIL;
			return $ret;
		}
		if (!preg_match_all($patt['playitemcontent'], $aResult[1], $aResult, PREG_PATTERN_ORDER) || empty($aResult[1])) {
			$error = self::E_COLLECT_PLAYDATA_FAIL;
			return $ret;
		}
		if (($ret['topType'] ==1 || $ret['topType'] == 3) && preg_match_all($patt['playitemname'], $html, $aResult1, PREG_PATTERN_ORDER)) {
			if ($ret['topType'] == 1) {// ����ǵ�Ӱ
				for($i=0, $j=count($aResult[1]); $i<$j; $i++) {
					if ($aResult1[1][$i]) {
						if (strpos($aResult1[1][$i], '��') === false && strpos($aResult1[1][$i], '��') === false) {
							$aResult[1][$i] =  trim($aResult[1][$i]).'[$]'.$aResult1[1][$i].'��';
						}
						else {
							$aResult[1][$i] =  trim($aResult[1][$i]).'[$]'.$aResult1[1][$i];
						}
					}
					else {
						$aResult[1][$i] =  trim($aResult[1][$i]);
					}
				}
			}
			else {//���������
				for($i=0, $j=count($aResult[1]); $i<$j; $i++) {
					if ($aResult1[1][$i]) {
						$aResult[1][$i] = trim($aResult[1][$i]).'[$]'.( preg_match('/^\d{8,8}$/is', $aResult1[1][$i]) && ($s = strtotime($aResult1[1][$i])) ?
								date('Y-m-d��', $s) : $aResult1[1][$i]);
					}
					else {
						$aResult[1][$i] =  trim($aResult[1][$i]);
					}
				}
			}
		}
		else {
			for($i=0, $j=count($aResult[1]); $i<$j; $i++) {
				$aResult[1][$i] = trim($aResult[1][$i]);
			}	
		}
		$ret['playData'] = trim(implode("\r\n", $aResult[1]));		
		
		//name
		if (!preg_match($patt['name'], $html, $aResult) || strpos($aResult[1], 'QMV') !== false) {
			$error = E_COLLECT_NAME_FAIL;
			return $ret;
		}
		$ret['name'] = trim($aResult[1]);
		
		//actor
		if (preg_match($patt['actor'], $html,$aResult)) {
			$ret['actors'] = !trim($aResult[1]) || $aResult[1]=='δ��д' ?' ����' : trim($aResult[1]);
		}
		
		//directors	
		if (preg_match($patt['director'], $html,$aResult)) {
			$ret['directors'] = !trim($aResult[1]) || $aResult[1]=='δ��д' ?' ����' : trim($aResult[1]);
		}			
		
		//area		
		if (preg_match($patt['area'], $html, $aResult)) {
			$ret['area'] = $aResult[1] == 'δ��д'? '����' : trim($aResult[1]);
		}
		if ($ret['area'] == '����') {
			$ret['area'] = '����';
		}
		
		//updateDate
		if (preg_match($patt['updateDate'], $html, $aResult))
		{
			$ret['updateDate'] = strtotime(empty($aResult[1]) ? date('Y-m-d H:i:s', mktime()) : trim($aResult[1]));
			$ret['updateDate'] = $ret['updateDate'] === false ? time() : $ret['updateDate'];
		}					
		
		//mark
		if(preg_match($patt['mark1'], $html, $aResult)) {
			if ($aResult[1] != '0') {
				if($ret['topType'] != 3) {
					if (is_numeric($aResult[1])) {
						$ret['mark'] = preg_match('/^\d{8,8}$/is',$aResult[1]) ? 'ȫ��' : '������'.$aResult[1].'��';
					} else {
						$ret['mark']= trim($aResult[1]);
					}
				} else {
					$ret['mark'] = trim($aResult[1]);
				}
			} else {
				$ret['mark'] = '���';
				$ret['isEnd'] = 1;
			}
		}		
		if($ret['topType'] ==1 && preg_match($patt['mark2'], $html, $aResult)) {
			$ret['mark'] = trim($aResult[1]);
		}
		if (strpos($ret['mark'], 'QMV') !== false) {
			$error = E_COLLECT_NAME_FAIL;
			return $ret;
		}
		if(empty($ret['mark']) || $ret['mark'] == 'BD') {
			$ret['mark'] = 'BD����';
		}
		
		//syDate
		if (preg_match($patt['syDate'], $html, $aResult)) {
			$aResult[1] = trim($aResult[1]);
			$ret['syDate'] = empty($aResult[1]) || $aResult[1]=='δ��д'?'����' : $aResult[1];			
			if (strtotime($ret['syDate']) === false) {
				$ret['vedioYear'] = '����';
			} else {
				$ret['vedioYear'] = date('Y', strtotime($ret['syDate']));
			}
		}		
				
		//description
		if (preg_match($patt['description'], $html, $aResult)) {
			$ret['content'] = trim($aResult[1]);
			Ttkvod_Utils::filterCollectContent($ret['content']);
		}
		
		//vedioPic
		if (preg_match($patt['pic'] ,$html, $aResult)) {
			$aResult[1] = trim($aResult[1]);
			if (Lamb_Utils::isHttp($aResult[1])) {
				$ret['vedioPic'] = $aResult[1];
			}
			
		}				
				
		$error = self::S_OK;
		return $ret;
	}
}	