<?php
class Ttkvod_Utils
{
	const FETCH_MODE_CURL = 1;
	
	const FETCH_MODE_HTTP = 2;
	
	const FETCH_MODE_FILE = 4;
	
	const TABLE_MOVIE_SOURCE = 1;
	
	const TABLE_MOVIE_CATE = 2;
	
	/**
	 * @param string $url
	 * @param int $connectTimeout
	 * @param int $type enum[FETCH_MODE_CURL,FETCH_MODE_HTTP,FETCH_MODE_FILE]
	 * @return string
	 */
	public static function fetchContentByUrl($url, $connectTimeout = 10, $type = self::FETCH_MODE_CURL)
	{
		switch($type) {
			case self::FETCH_MODE_HTTP:
				return self::fetchContentByUrlH($url, $connectTimeout);
			case self::FETCH_MODE_FILE:
				return file_get_contents($url);
			default:
				return self::fetchContentByUrlC($url, $connectTimeout);
		}
	}
	
	/**
	 * @param string $url
	 * @param int $connectTimeout
	 * @return string
	 */
	public static function fetchContentByUrlC($url, $connectTimeout = 10)
	{
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;		
	}
	
	/**
	 * @param string $url
	 * @param int $connectTimeout
	 * @return string
	 */
	public static function fetchContentByUrlH($url, $connectTimeout = 20)
	{
		$ret = Ttkvod_Http::quickGet($url, $connectTimeout, false, $status);
		if ($status != 200) {
			$ret = '';
		}
		return $ret;
	}
	
	/**
	 * @return string
	 */
	public static function getRealIp()
	{
		return Lamb_App::getGlobalApp()->getRequest()->getClientIp();
	}
	
	/**
	 * @param string $words
	 * @param int $wordsLen
	 * @return array
	 */
	public static function splitWords($words, $wordsLen = 200)
	{
		$ret = array();
		$words = Lamb_Utils::mbSubstr($words, 0, $wordsLen);
		$words = preg_replace('/,|\.|，|。|\.|;|\:|"|\'/is', '', $words);
		$cfg = Lamb_Registry::get(CONFIG);
		$cws = scws_new();
		$cws->set_charset(Lamb_App::getGlobalApp()->getCharset());
		$cws->set_rule($cfg['scws_rule_path']);
		$cws->set_dict($cfg['scws_dict_path']);
		unset($cfg);
		$cws->send_text($words);
		while ($temp = $cws->get_result()) {
			foreach ($temp as $temp1) {
				$ret[] = $temp1['word'];
			}
		}
		$cws->close();
		return $ret;
	}
	
	/**
	 * @param string $str
	 * @return string
	 */
	public static function encode($str)
	{
		return str_replace('=', 'c', str_replace('/', 'b', str_replace('+', 'a', trim($str))));
	}
	
	public static function encodeFullSearchStr($string, $len = 200)
	{
		$ret = '';
		foreach (Ttkvod_Utils::splitWords($string, $len) as $item) {
			$ret .= base64_encode(strtoupper($item)) . ' ';
		}
		
		return self::encode(trim($ret));
	}
	
	/**
	 * 获取真实的图片路径
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getImgPath($path, $url = '')
	{
		if (Lamb_Utils::isHttp($path)) {
			return $path;
		}
		$cfg = Lamb_Registry::get(CONFIG);
		return ($url ? $url : $cfg['img_host']) . $path;
	}
	
	/**
	 * @param string $mark
	 * @param string $title
	 * @return string
	 */
	public static function isHD($mark, $title)
	{
		if (strpos($mark, 'BD') !== false || strpos($mark, 'HD')!==false || strpos($mark, '高清')!==false || strpos($title, '[BD') !== false) {
			return '<s class="s_bd"></s>';
		}
		return '';	
	}
	
	/**
	 * urlRewrite
	 *
	 * @param string $model
	 * @param array $params
	 * @return string
	 */
	public static function UR($model, array $params)
	{
		return Ttkvod_Model_LinkRouter::getSingleInstance()->router($model, $params);
	}
	
	
	/**
	 * @param array $params
	 * @param string $action
	 * @param string $controllor
	 * @param boolean $encode
	 * @return string
	 */
	public static function U($params, $action = '', $controllor = '', $encode = true)
	{
		$router = Lamb_App::getGlobalApp()->getRouter();
		$params[$router->setControllorKey()] = $controllor;
		$params[$router->setActionKey()] = $action;
		return '/index.php?' . $router->setRouterParamName() . '=' . $router->url($params, $encode);
	}
	
	/**
	 * @param string | array $tags
	 * @param int $id
	 * @param boolean $isecho
	 */
	public static function randerTag($tags, $id = 0, $isecho = true)
	{
		if (is_string($tags)) {
			$tagmodel = new Ttkvod_Model_Tag();
			$tags = $tagmodel->parse($tags);
			unset($tagmodel);
		}
		$ret = '';
		$param = array('id' => 'search', 'auto' => 'tag', 'order' => '');
		if ($id > 0) {
			$param['lid'] = $id;
		}
		foreach ($tags as $tag) {
			$param['q'] = $tag;
			$ret .= '<a router="search" href="' . self::UR('', $param) . "\">$tag</a> ";
		}
		if ($isecho) {
			echo $ret;
		} else {
			return $ret;
		}
	}
	
	public static function randerTagHtml($tags, $isecho = true)
	{
		if (is_string($tags)) {
			$tagmodel = new Ttkvod_Model_Tag();
			$tags = $tagmodel->parse($tags);
			unset($tagmodel);
		}
		
		$ret = '';
		foreach ($tags as $tag) {
			if ($tag == '') {
				continue;
			}
			$ret .= "<a router='search' target='_blank' href='/search-{$tag}.html'>{$tag}</a>";
		}
		
		if ($isecho) {
			return $ret;			
		} else {
			echo $ret;
		}
	}
	
	public static function flushCache()
	{
		$cfg = Lamb_Registry::get(CONFIG);
		if ($cfg['cache_cfg']['type'] & Ttkvod_Cache_Factory::CACHE_FILE) {
			$paths = array($cfg['cache_cfg']['db_path'], $cfg['cache_cfg']['html_path'], $cfg['cache_cfg']['comm_path'], $cfg['cache_cfg']['local_path']);
			foreach ($paths as $path) {
				Lamb_IO_File::delFileUnderDir($path);
			}
		} else {
			Ttkvod_Cache_Factory::getMemcached()->flushAll();
		}
	}
	
	public static function flushCDN2($aUrls)
	{
		$strUsername = 'flycache';
		$strPassword = 'cacheapi@123';
		$strHost = 'wscp.lxdns.com:8080';
		$strPath = '/wsCP/servlet/contReceiver';
		$url = implode(';', $aUrls);
		$md5 = md5($strUsername.$strPassword.$url);
		$param ="?username={$strUsername}&passwd={$md5}&url=" . urlencode($url);
		$objHttp = new Ttkvod_Http($strHost);
		$objHttp->timeout = 8;
		//$objHttp->post($strPath, $aParam);
		$objHttp->get($strPath . $param);
	}
	
	public static function flushCDN($aUrls)
	{
		$strUsername = 'ttkvod-cdn';
		$strPassword = '123@cdns';
		$strHost = 'r.chinacache.com';
		$strPath = '/content/refresh';
		$aParam = array(
			'username' => $strUsername,
			'password' => $strPassword,
			'task' => json_encode(array('urls' => $aUrls))
		);
		$objHttp = new Ttkvod_Http($strHost);
		$objHttp->timeout = 8;
		$objHttp->post($strPath, $aParam);
	}
	
	/**
	 * @param string $content
	 */
	public static function filterCollectContent(&$content)
	{
		$aPatt = array(
			'/<script.*?>.*?<\/script>/is',
			'/<iframe.*?>.*?<\/iframe>/is',
			'/<object.*?>.*?<\/object>/is',
			'/<a.*?>.*?<\/a>/is',
			'/style\=".*?"/is'
		);
		foreach ($aPatt as $item) {
			$content = preg_replace($item, '', $content);
		}	
		unset($content);
	}

	/**
	 * @param string $content
	 * @param string $replaceMent
	 * @return string
	 */
	public static function filterHtmlTag($content, $replaceMent = '')
	{
		return preg_replace('/(<(\/)?[^>]*>)/is', $replaceMent, $content);	
	}

	public static function utfToUnicode($str) 
	{
		$a = ord($str{0}) & 0x1f;
		$b = ord($str{1}) & 0x7f;
		$c = ord($str{2}) & 0x7f;
		$n = (64*$a+$b)*64+$c;
		return sprintf('%0x', $n);
	}	

	public static function showReply($state, $content) 
	{
		return $state == -1 ? "<p><span style='color:#ff6600'>回复：$content</span></p>" : "";
	}
	public static function createLink($state, $vid, $vname) 
	{
		if ($state == 1) {
			return '<a href=' . self::UR('item', array('id' => $vid)) . '>' . $vname . '</a>';
		}
		return "<b>$vname</b>";
	}	
}