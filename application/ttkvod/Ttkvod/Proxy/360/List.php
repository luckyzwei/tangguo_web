<?php
class Ttkvod_Proxy_360_List extends Ttkvod_Proxy_360_Abstract implements Ttkvod_Proxy_ListInterface
{
	protected static $aCollectUrlsTemplate = array(
		1 => 'http://video.baidu.com/movie/?order=pubtime&pn={$page}',
		2 => 'http://video.baidu.com/tvplay/?order=pubtime&pn={$page}',
		3 => 'http://video.baidu.com/tvshow/?order=pubtime&pn={$page}',
		4 => 'http://video.baidu.com/comic/?order=pubtime&pn={$page}'
	);
	
	/**
	 * @Tmovie_Collect_ListInterface implemtions
	 */
	public function getUrl($page)
	{
		return str_replace('{$page}', $page, self::$aCollectUrlsTemplate[$this->mTypeid]);
	}
	
	/**
	 * @Tmovie_Collect_ListInterface implemtions
	 */	
	public function collect($url, $externals = null, &$error = null)
	{
		$ret = array();
		
		if (! ($content = Tmovie_Utils::fetchContentByUrlH($url)) ) {
			$error = self::E_NET_FAIL;
			return $ret;
		}
		
		$patt = '/<li class="video-item vi-114v.*?<a href="(.*?)&/is';	
		
		if (!preg_match_all($patt, $content, $result, PREG_SET_ORDER)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;		
		}
		
		foreach ($result as $item) {
			if (Lamb_Utils::isHttp($item[1])) {
				$ret[] = array('url' => $item[1]);
			}
		}

		return $ret;
	}	
}