<?php
class Ttkvod_Proxy_Douban_Item extends Ttkvod_Proxy_Douban_Abstract implements Ttkvod_Proxy_ItemInterface
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @Ttkvod_Collect_ItemInterface implemtions
	 */	
	public function collect($url, $externals = null, &$error = null)
	{
		$ret = array(
			'name' => '', 'pic' => '',
			'directors' => array(), 'actors' => array(),
			'type' => array(), 'area' => '',
			'showyear' => '', 'description' => '',
			'typetag' => '', 'cateid' => 0,
			'url' => $url
		);
		
		static $patts = array(
			'name_pic' => '/<div id="mainpic".*?<img src="(.*?)".*?alt="(.*?)"/is',
			'directors' => '/导演<\/span>(.*?)<br\/>/is',
			'actors' => '/主演<\/span>(.*?)<br\/>/is',
			'type' => '/类型:<\/span>(.*?)<br\/>/is',
			'area' => '/地区:<\/span>(.*?)<br\/>/is',
			'showyear' => '/<span class="year">\((.*?)\)/is',
			'description' => '/<span class="all hidden">(.*?)<\/span>/is',
			'description_bak' => '/<span property="v:summary".*?>(.*?)<\/span>/is',
			'aitem' => '/<a.*?>(.*?)<\/a>/is',
			'sitem' => '/<span.*?>(.*?)<\/span>/is'
		);
		
		if (!($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;		
		}
		
		$html = iconv('utf-8', 'gbk//ignore', $html);	
		
		if (!preg_match($patts['name_pic'], $html, $result)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;
		}
		
		$ret['name'] = trim($result[2]);
		$ret['pic'] = trim($result[1]);
		
		foreach (array('actors', 'directors') as $key) {
			if (preg_match($patts[$key], $html, $result) && preg_match_all($patts['aitem'], $result[1], $result)) {
				$ret[$key] = $result[1];
			}
		}
		
		if (preg_match($patts['type'], $html, $result) && preg_match_all($patts['sitem'], $result[1], $result)) {
			$ret['type'] = $result[1];
		}
		
		foreach (array('area', 'showyear') as $key) {
			if (preg_match($patts[$key], $html, $result)) {
				$ret[$key] = trim($result[1]);
			}
		}
		
		if ($ret['area'] == '中国大陆') {
			$ret['area'] = '大陆';
		}
		
		if (preg_match($patts['description'], $html, $result) || preg_match($patts['description_bak'], $html, $result)) {
			$ret['description'] = trim($result[1]);
		}
 		
		$error = self::S_OK;
		return $ret;
	}
}