<?php
class Ttkvod_Proxy_Douban_Abstract extends Ttkvod_Proxy_Abstract
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
			case 'm':
				return 1;
			case 'dianshi':
			case 'tv':
				return 2;
			case 'zongyi':
			case 'va':
				return 3;
			case 'dongman':	
			case 'ct':
				return 4;
		}
		return -1;
	}
}