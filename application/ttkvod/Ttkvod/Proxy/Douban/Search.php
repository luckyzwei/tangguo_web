<?php
class Ttkvod_Proxy_Douban_Search extends Ttkvod_Proxy_douban_Abstract implements Ttkvod_Proxy_SearchInterface
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getUrl($keywords, $page = 1)
	{
		return 'http://movie.douban.com/subject_search?search_text=' . Lamb_App_Response::encodeURIComponent($keywords);
	}
	
	public function collect($url, $externals = null, &$error = null)
	{
		$ret = array();
		$app = Lamb_App::getGlobalApp();
		$patt = '/<a class="nbg" href="(.*?)".*?title="(.*?)"\s*>.*?<img.*?src="(.*?)"/is';

		if (!($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;	
		}
		
		$html = iconv('utf-8', 'gbk//ignore', $html);
		
		if (!preg_match_all($patt, $html, $result, PREG_SET_ORDER)){
			$error = self::E_RULE_NOT_MATCH;
			return $ret;		
		}	
		
		foreach ($result as $item) {
			$ret[] = array(
				'type' => '',
				'url' => trim($item[1]),
				'name' => trim($item[2]),
				'img' => trim($item[3]),
				'tags' => ''
			);
		} 
		
		$error = self::S_OK;
		return $ret;
	}
}