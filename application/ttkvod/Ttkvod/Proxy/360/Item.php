<?php
class Ttkvod_Proxy_360_Item extends Ttkvod_Proxy_360_Abstract implements Ttkvod_Proxy_ItemInterface
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
		$typeid = isset($this->mTypeid) ? $this->mTypeid : $this->getTypeidFromUrl($url);
		$flags = array(1 => 'Movie', 2 => 'Tv', 3 => 'Show', 4 => 'Comic');
		$funcname = 'collectFor' . $flags[$typeid];
		
		$aRet = array(
				'name' => '', 'cateid' => $typeid, 'pic' => '', 'actors' => '暂无', 
				'directors' => '暂无', 'showyear' => 0, 'area' => '暂无', 'type' => '暂无', 'typetag' => '暂无',
				'description' => '','url' => $url
			);
		
		$error = $this->$funcname($url, $aRet);
		
		return $aRet;		
	}
	
	public function collectForTv($url, array &$aRet)
	{
		static $patts = array(
			'name' => '/<span id="film_name".*?>(.*?)<\/span>/is',
			'directors' => '/<i>导演：<\/i>(.*?)<\/p>/is',
			'actors' => '/<i class="gray">主演：<\/i>(.*?)<\/p>/is',
			'type' => '/<i>类型：<\/i>(.*?)<\/p>/is',
			'typetag' => '/<i class="gray">看点：<\/i>(.*?)<\/p>/is',
			'area' => '/<i>地区：<\/i>(.*?)<\/p>/is',
			'showyear' => '/<i>年代：<\/i>(.*?)<\/p>/is',
			'pic' => '/<div class="v-poster">.*?<img src="(.*?)"/is',
			'description' => '/<span class="text">(.*?)<\/span>/is',
			'description_bak' => '/<span class="text" id="full-intro".*?>(.*?)<\/span>/is',
			'a_item' => '/<a.*?>(.*?)</is',
			'tTag_item' => '/<i.*?>(.*?)<\/i>/is'
		);

		if (!($content = Ttkvod_Utils::fetchContentByUrlH($url))) {
			return self::E_NET_FAIL;
		}
		
		if (!Lamb_App::getGlobalApp()->isAppUTF8($charset)) {
			$content = iconv('utf-8', $charset . '//ignore', $content);
		}
		
		if (!preg_match($patts['name'], $content, $result) || !$result[1]) {
			return self::E_RULE_NOT_MATCH;
		}
		$aRet['name'] = trim($result[1]);
		
		if (preg_match($patts['actors'], $content, $result) && $result[1] && preg_match_all($patts['a_item'], $result[1], $result)) {
			$aRet['actors'] = $result[1];
			foreach ($aRet['actors'] as $k => $value) {
				if ($value == '赞美主演<em></em>' || $value == '赞美主演') {
					unset($aRet['actors'][$k]);
				}
			}
		}
		
		foreach (array('directors', 'type', 'area', 'showyear', 'pic') as $key) {
			if (preg_match($patts[$key], $content, $result) && !empty($result[1])) {
				$aRet[$key] = trim($result[1]);
			}
		}
		
		if (preg_match($patts['typetag'], $content, $result) && !empty($result[1])) {
				if (preg_match_all($patts['tTag_item'], $result[1], $result)) {
					$aRet['typetag'] = $result[1];
				}	
		}
		
		if ($aRet['type']) {
			$aRet['type'] = explode(' / ', $aRet['type']);
		}
		
		if ($aRet['directors']) {
			$aRet['directors'] = explode(' / ', $aRet['directors']);
		}		
		
		if (preg_match($patts['description_bak'], $content, $result)) {
			$aRet['description'] = preg_replace('/<a.*?>.*?<\/a>/is', '', trim($result[1]));
		} else if (preg_match($patts['description'], $content, $result)) {
			$aRet['description'] = trim($result[1]);
		}
		
		unset($aRet);
		return self::S_OK;	
	}
	
	public function collectForMovie($url, array &$aRet)
	{
		static $patts = array(
			'name' => '/<h1 class="title.*?>(.*?)<\/h1>/is',
			'directors' => '/<dt>导演：<\/dt><dd>(.*?)<\/dd>/is',
			'actors' => '/<dt>主演：<\/dt><dd>(.*?)<\/dd>/is',
			'typetag' => '/<dt>看点：<\/dt><dd>(.*?)<\/dd>/is',
			'area' => '/<dt>地区：<\/dt><dd>(.*?)<\/dd>/is',
			'showyear' => '/<dt>年代：<\/dt><dd>(.*?)<\/dd>/is',
			'pic' => '/<div class="pic".*?海报.*?<img src="(.*?)"/is',
			'description' => '/<p class="less">(.*?)<\/p>/is',
			'a_item' => '/<a.*?>(.*?)<\/a>/is'
		);
		
		if (!($content = Ttkvod_Utils::fetchContentByUrlH($url))) {
			return self::E_NET_FAIL;
		}
		
		if (!Lamb_App::getGlobalApp()->isAppUTF8($charset)) {
			$content = iconv('utf-8', $charset . '//ignore', $content);
		}
		
		if (!preg_match($patts['name'], $content, $result) || !$result[1]) {
			return self::E_RULE_NOT_MATCH;
		}
		$aRet['name'] = trim($result[1]);
		
		foreach (array('area', 'showyear', 'pic', 'description') as $key) {
			if (!preg_match($patts[$key], $content, $result) || empty($result[1])) {
				continue ;
			}
			$aRet[$key] = trim($result[1]);
		}
		
		if ($aRet['area']) {
			$aRet['area'] = explode('/', $aRet['area']);
			$aRet['area'] = trim($aRet['area'][0]);
		}
		
		if ($aRet['description']) {
			$aRet['description'] = preg_replace('/<a.*?>.*?<\/a>/is', '', $aRet['description']);
		}
		
		if (!Lamb_Utils::isInt($aRet['showyear'], true)) {
			$aRet['showyear'] = 0;
		}
		
		foreach (array('directors', 'actors', 'typetag') as $key) {
			if (!preg_match($patts[$key], $content, $result) || !preg_match_all($patts['a_item'], $result[1], $result)) {
				continue ;
			}
			$aRet[$key] = $result[1];
			foreach ($aRet[$key] as $k => $value) {
				if ($value == '赞美主演<em></em>' || $value == '赞美主演') {
					unset($aRet[$key][$k]);
				}
			}
		}	

		unset($aRet);
		return self::S_OK;
	}
	
	
	public function collectForShow($url, array &$aRet)
	{
		static $patts = array(
			'name' => '/<span id="film_name".*?>(.*?)<\/span>/is',
			'directors' => '/<em>播出：<\/em><span class="text">(.*?)<\/span>/is',
			'actors' => '/<em>主持人：<\/em><span class="text">(.*?)<\/span>/is',
			'type' => '/<em>类型：<\/em><span class="text">(.*?)<\/span>/is',
			'area' => '/<em>地区：<\/em><span class="text">(.*?)<\/span>/is',
			'showyear' => '/<em>年代：<\/em><span class="text">(.*?)<\/span>/is',
			'pic' => '/<div id="poster">.*?<img src="(.*?)"/is',
			'description' => '/<p class="text" id="part-intro">(.*?)<\/p>/is',
			'description_bak' => '/<p class="text" id="full-intro".*?>(.*?)<\/p>/is',
			'a_item' => '/<a.*?>(.*?)<\/a>/is'
		);
		if (!($content = Ttkvod_Utils::fetchContentByUrlH($url))) {
			return self::E_NET_FAIL;
		}
		
		if (!Lamb_App::getGlobalApp()->isAppUTF8($charset)) {
			$content = iconv('utf-8', $charset . '//ignore', $content);
		}
		
		if (!preg_match($patts['name'], $content, $result) || !$result[1]) {
			return self::E_RULE_NOT_MATCH;
		}
		$aRet['name'] = trim($result[1]);
		
		foreach (array('directors', 'type', 'area', 'showyear', 'pic', 'actors') as $key) {
			if (preg_match($patts[$key], $content, $result) && !empty($result[1])) {
				$aRet[$key] = trim($result[1]);
			}
		}

		foreach (array('type', 'actors', 'directors') as $key) {
			if (isset($aRet[$key])) {
				$aRet[$key] = explode(' / ', $aRet[$key]);
			}
		}

		if (preg_match($patts['description_bak'], $content, $result)) {
			$aRet['description'] = preg_replace('/<a.*?>.*?<\/a>/is', '', trim($result[1]));
		} else if (preg_match($patts['description'], $content, $result)) {
			$aRet['description'] = trim($result[1]);
		}

		unset($aRet, $webColumn, $pcColumn, $showPics);
		return self::S_OK;		
	}
	
	public function collectForComic($url, array &$aRet)
	{
		static $patts = array(
			'name' => '/<span id="film_name".*?>(.*?)<\/span>/is',
			'directors' => '/<em>导演：<\/em><span class="text">(.*?)<\/span>/is',
			'actors' => '/<em>主角：<\/em><span class="text">(.*?)<\/span>/is',
			'type' => '/<em>类型：<\/em><span class="text">(.*?)<\/span>/is',
			'area' => '/<em>地区：<\/em><span class="text">(.*?)<\/span>/is',
			'showyear' => '/<em>年代：<\/em><span class="text">(.*?)<\/span>/is',
			'pic' => '/<div id="poster">.*?<img src="(.*?)"/is',
			'description' => '/<p class="text" id="part-intro">(.*?)<\/p>/is',
			'description_bak' => '/<p class="text" id="full-intro".*?>(.*?)<\/p>/is',
			'a_item' => '/<a.*?>(.*?)<\/a>/is'
		);

		if (!($content = Ttkvod_Utils::fetchContentByUrlH($url))) {
			return self::E_NET_FAIL;
		}
		
		if (!Lamb_App::getGlobalApp()->isAppUTF8($charset)) {
			$content = iconv('utf-8', $charset . '//ignore', $content);
		}
		
		if (!preg_match($patts['name'], $content, $result) || !$result[1]) {
			return self::E_RULE_NOT_MATCH;
		}
		$aRet['name'] = trim($result[1]);
		
		foreach (array('directors', 'type', 'area', 'showyear', 'pic', 'actors') as $key) {
			if (preg_match($patts[$key], $content, $result) && !empty($result[1])) {
				$aRet[$key] = trim($result[1]);
			}
		}
		
		foreach (array('type', 'actors', 'directors') as $key) {
			if (isset($aRet[$key])) {
				$aRet[$key] = explode(' / ', $aRet[$key]);
			}
		}
		
		if (preg_match($patts['description_bak'], $content, $result)) {
			$aRet['description'] = preg_replace('/<a.*?>.*?<\/a>/is', '', trim($result[1]));
		} else if (preg_match($patts['description'], $content, $result)) {
			$aRet['description'] = trim($result[1]);
		}

		unset($aRet);
		return self::S_OK;				
	}
}