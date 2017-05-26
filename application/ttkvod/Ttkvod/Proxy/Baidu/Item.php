<?php
class Ttkvod_Proxy_Baidu_Item extends Ttkvod_Proxy_Baidu_Abstract implements Ttkvod_Proxy_ItemInterface
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
		static $patts = array(
			'public' => array(
				'id' => '/id=(\d+)&page/is',
				'movieid' => '/movie\/(\d+).htm/is',
				'tvid' => '/tv\/(\d+).htm/is', 
				'sourceid' => '/[show|tv|movie|comic]\/(\d+).htm/is',
				'comicid' => '/comic\/(\d+).htm/is'
			)
		);
		$typeid = isset($this->mTypeid) ? $this->mTypeid : $this->getTypeidFromUrl($url);

		$aRet = array(
				'name' => '', 'cateid' => $typeid, 'pic' => '', 'actors' => '暂无', 
				'directors' => '暂无', 'showyear' => 0, 'area' => '暂无','type' => '暂无', 'typetag' => '暂无',
				'description' => '','url' => $url
			);
			
		if (!($content = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $aRet;
		}
		
		if (!preg_match($patts['public']['id'], $url, $result) && !preg_match($patts['public']['sourceid'], $url, $result)) {
			$error = self::E_RULE_NOT_MATCH;
			return $aRet;
		}	
		$id = $result[1];
		$error = $this->collectInfo($content, $aRet, $id);
		
		//$aRet['pic'] = Lamb_App::getGlobalApp()->getRouter()->urlEx('autoSearch', 'crackImg', array('url' => $aRet['pic'], 'refer' => 'http://www.baidu.com'));
		return $aRet;				
	}
	
	public function collectInfo($content, &$aRet, $id)
	{
		static $patts = array(
			'name' => '/title: \'(.*?)\'/is',
			'_name' => '/<div class="title-wrapper clearfix">[\r\s\n]*<h2>(.*?)<span class="update-info">/is',
			'showyear' => '/年代：<\/span>&nbsp;(\d+)/is',
			'actors' => '/演员：<\/span>(.*?)<\/li>/is',
			'oActors' => '/主演：<\/span>[\r\s\n]*(.*?)<\/li>/is',
			'directors' => '/导演：<\/span>[\r\s\n]*(.*?)<\/li>/is',
			'area' => '/地区：<\/span>(.*?)</is',
			'description' => '/<span class="plain-txt">(.*?)<\/span>/is',
			'description_bak' => '/<input type="hidden" value="(.*?)" name="longIntro"/is',
			'pic' => '/<div class="poster-sec".*?<img src="(.*?)"/is',
			'typetag' => '/<ul class="aside-highlight">(.*?)<\/ul>/is',
			'a_item' => '/<a.*?>(.*?)<\/a>/is'
		);
		
		if (!preg_match($patts['name'], $content, $result) || !$result[1]) {
			return self::E_RULE_NOT_MATCH;
		}
		
		$aRet['name'] = trim($result[1]);		
		
		foreach (array('showyear', 'area', 'pic') as $key) {
			if (!preg_match($patts[$key], $content, $result) || empty($result[1])) {
				continue;
			}
			$aRet[$key] = trim($result[1]);
		}
		if ($aRet['area']) {
			$aRet['area'] = explode('&nbsp;', $aRet['area']);
			$aRet['area'] = $aRet['area'][0];
		}
		
		if (!Lamb_Utils::isInt($aRet['showyear'], true)) {
			$aRet['showyear'] = 0;
		}
		
		foreach (array('actors', 'directors') as $key) {
			if (!preg_match($patts[$key], $content, $result) || empty($result[1])) {
				if (!preg_match($patts['oActors'], $content, $result)) {
					continue;
				}
			}
			if (!preg_match_all($patts['a_item'], $result[1], $acResult)) {
				$aRet[$key] = $result[1] != '' ? explode('&nbsp;', $result[1]) : '暂无';
				continue;
			} 
			$aRet[$key] = $this->a_array_unique($acResult[1]);
		}
		
		if (preg_match($patts['description_bak'], $content, $result) || preg_match($patts['description'], $content, $result)) {
			$aRet['description'] = trim($result[1]);
		}
		
		if (preg_match($patts['typetag'], $content, $result) && preg_match_all($patts['a_item'], $result[1], $result)) {
			foreach ($result[1] as $key => $val) {
				$result[1][$key] = Ttkvod_Utils::filterHtmlTag($val);
			}
			$aRet['typetag'] = $this->a_array_unique($result[1]);
		}

		unset($aRet);
		return self::S_OK;
	}
	
	//去除相同元素
	public function a_array_unique($array)
	{
		$out = array();
		foreach ($array as $key => $value) {
			if (!in_array($value, $out)){
			   $out[$key] = $value;
		    }
		}
		return $out;
	} 
	
}