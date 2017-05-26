<?php
class Ttkvod_Collect_Hakuzy_List extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ListInterface
{
	/**
	 * @var string
	 */
	public static $sPatt = '/<!--一个影片开始代码-->(?:.*?)<!--影片链接开始代码-->(.*?)<!--影片链接结束代码-->(?:.*?)<!--影片类型开始代码-->(.*?)<!--影片类型结束代码-->(?:.*?)<!--上映日期开始代码-->(.*?)<!--上映日期结束代码-->(?:.*?)<!--一个影片结束代码-->/is';
	
	public function __construct()
	{
		
	}
	
	/**
	 * @Ttkvod_Collect_ListInterface implemtions
	 */
	public function getUrl($page)
	{
		return 'http://hakuzy.com/list/?0-' . $page .'.html';
	}

	/**
	 * @Ttkvod_Collect_ListInterface implemtions
	 */	
	public function collect($url, $externals = null, &$error = null)
	{
		$ret = array();
		
		if ( !($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;
		}

		if (!preg_match_all(self::$sPatt, $html, $aResult, PREG_SET_ORDER)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;
		}
		
		foreach ($aResult as $item) {
			$ret[] = array(
				'url' => '/' . ltrim($item[1], '/'),
				'datetime' => strtotime($item[3]) === false ? -1 : strtotime($item[3]),
				'externls' => null
			);
		}
		
		$error = self::S_OK;

		return $ret;
	}
}