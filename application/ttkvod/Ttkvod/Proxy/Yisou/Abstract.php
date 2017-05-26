<?php
class Ttkvod_Proxy_Yisou_Abstract extends Ttkvod_Proxy_Abstract
{
	protected $mTypeid;
	
	public function __construct()
	{
	}
	
	/**
	 * @param string $url
	 * @return int
	 */
	public function getTypeidFromUrl($url)
	{
		if (Lamb_Utils::isHttp($url)) {
			$url = substr($url, 7);
		}
		$flag = explode('/', $url);
		$flag = $flag[1];
		
		switch ($flag) {
			case 'dianying':
				return 1;
			case 'tv':
				return 2;
			case 'zongyi':
				return 3;
			case 'dongman':	
				return 4;
		}
		return -1;
	}
	
	/**
	 * @param string $content
	 * @return int
	 */
	public function getTypeidFromContent($content)
	{
		static $patt = '/<a.*?class="active".*?>(.*?)<\/a>/is';
		static $map = array('电影' => 1, '电视剧' => 2, '综艺' => 3, '动漫' => 4);
		$ret = -1;
		if (preg_match($patt, $content, $result)) {
			$result[1] = trim($result[1]);
			if (isset($map[$result[1]])) {
				$ret = $map[$result[1]];
			}
		}
		return $ret;
	}
}