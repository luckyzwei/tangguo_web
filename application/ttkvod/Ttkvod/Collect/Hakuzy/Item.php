<?php
class Ttkvod_Collect_Hakuzy_Item extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ItemInterface
{
	/**
	 * @var array
	 */
	public static $sTypeMap = array(
		'动作片'=>array('typeid'=>1,'type'=>'动作'),
		'喜剧片'=>array('typeid'=>1,'type'=>'喜剧'),
		'爱情片'=>array('typeid'=>1,'type'=>'爱情'),
		'动漫片'=>array('typeid'=>4,'type'=>'动漫'),
		'恐怖片'=>array('typeid'=>1,'type'=>'恐怖'),
		'科幻片'=>array('typeid'=>1,'type'=>'科幻'),
		'战争片'=>array('typeid'=>1,'type'=>'战争'),
		'纪录片'=>array('typeid'=>3,'type'=>'记录'),
		'剧情片'=>array('typeid'=>1,'type'=>'剧情'),
		'大陆剧'=>array('typeid'=>2,'type'=>'大陆剧'),
		'欧美剧'=>array('typeid'=>2,'type'=>'欧美剧'),
		'港台剧'=>array('typeid'=>2,'type'=>'港台剧'),
		'日韩剧'=>array('typeid'=>2,'type'=>'日韩剧'),
		'泰剧' => array('typeid'=>2,'type'=>'泰剧'),
		'综艺其它'=>array('typeid'=>3,'type'=>'综艺其它'),
		'音乐'=>array('typeid'=>3,'type'=>'音乐MV'),
		'世界杯'=>array('typeid'=>3,'type'=>'体育'),
		'720P高清'=>array('typeid'=>1,'type'=>'高清')
	);
	
	/**
	 * @var array
	 */
	public static $sPatt = array(
		'typeid' => '/<!--影片类型开始代码-->(.*?)<!--影片类型结束代码-->/is',
		'playlist' => '/<!--播放列表开始代码-->(.*?)<!--播放列表结束代码-->/is',
		'playitemcontent' => '/<a>(.*?)<\/a>/is',
		'playitemname' => '/<!--分集名开始(.*?)分集名结束-->/is',
		'name' => '/<!--影片名称开始代码-->(.*?)<!--影片名称结束代码-->/is',
		'actor' => '/<!--影片演员开始代码-->(.*?)<!--影片演员结束代码-->/is',
		'director' => '/<!--影片导演开始代码-->(.*?)<!--影片导演结束代码-->/is',
		'area' => '/<!--影片地区开始代码-->(.*?)<!--影片地区结束代码-->/is',
		'updateDate' => '/<!--影片更新时间开始代码-->(.*?)<!--影片更新时间结束代码-->/is',
		'mark1' => '/<!--影片状态开始代码-->(.*?)<!--影片状态结束代码-->/is',
		'mark2' => '/<!--影片副标开始代码-->(.*?)<!--影片副标结束代码-->/is',
		'syDate' => '/<!--上映日期开始代码-->(.*?)<!--上映日期结束代码-->/is',
		'description' => '/<!--影片介绍开始代码-->(.*?)<!--影片介绍结束代码-->/is',
		'pic' => '/<!--影片图片开始代码-->(.*?)<!--影片图片结束代码-->/is',
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
			if ($ret['topType'] == 1) {// 如果是电影
				for($i=0, $j=count($aResult[1]); $i<$j; $i++) {
					if ($aResult1[1][$i]) {
						if (strpos($aResult1[1][$i], '版') === false && strpos($aResult1[1][$i], '集') === false) {
							$aResult[1][$i] =  trim($aResult[1][$i]).'[$]'.$aResult1[1][$i].'版';
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
			else {//如果是综艺
				for($i=0, $j=count($aResult[1]); $i<$j; $i++) {
					if ($aResult1[1][$i]) {
						$aResult[1][$i] = trim($aResult[1][$i]).'[$]'.( preg_match('/^\d{8,8}$/is', $aResult1[1][$i]) && ($s = strtotime($aResult1[1][$i])) ?
								date('Y-m-d期', $s) : $aResult1[1][$i]);
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
			$ret['actors'] = !trim($aResult[1]) || $aResult[1]=='未填写' ?' 不详' : trim($aResult[1]);
		}
		
		//directors	
		if (preg_match($patt['director'], $html,$aResult)) {
			$ret['directors'] = !trim($aResult[1]) || $aResult[1]=='未填写' ?' 不详' : trim($aResult[1]);
		}			
		
		//area		
		if (preg_match($patt['area'], $html, $aResult)) {
			$ret['area'] = $aResult[1] == '未填写'? '不详' : trim($aResult[1]);
		}
		if ($ret['area'] == '其它') {
			$ret['area'] = '其他';
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
						$ret['mark'] = preg_match('/^\d{8,8}$/is',$aResult[1]) ? '全集' : '更新至'.$aResult[1].'集';
					} else {
						$ret['mark']= trim($aResult[1]);
					}
				} else {
					$ret['mark'] = trim($aResult[1]);
				}
			} else {
				$ret['mark'] = '完结';
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
			$ret['mark'] = 'BD高清';
		}
		
		//syDate
		if (preg_match($patt['syDate'], $html, $aResult)) {
			$aResult[1] = trim($aResult[1]);
			$ret['syDate'] = empty($aResult[1]) || $aResult[1]=='未填写'?'不详' : $aResult[1];			
			if (strtotime($ret['syDate']) === false) {
				$ret['vedioYear'] = '不详';
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