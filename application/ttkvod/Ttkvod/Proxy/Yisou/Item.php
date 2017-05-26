<?php
class Ttkvod_Proxy_Yisou_Item extends Ttkvod_Proxy_Yisou_Abstract implements Ttkvod_Proxy_ItemInterface
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
				'name' => '/<h1 itemprop="name"><a.*?>(.*?)<\//is',
				'default_name' => '/<h1 itemprop="name">(.*?)<\//is',
				'pic' => '/<img itemprop="image".*?src="(.*?)".*?\/>/is',
				'description' => '/<p class="brief">(.*?)<\/p>/is',
				'description_bak' => '/<p class="detail ks-hidden">(.*?)<\/p>/is',
				'showyear' => '/<dt>年份：<\/dt>[\s\r\n]*<dd>(.*?)<\/dd>/is',
				'default_showyear' => '/<dt>年份：<\/dt>[\s\r\n]*<dd itemprop="copyrightYear">(.*?)<\/dd>/is',
				'a_item' => '/<a.*?>(.*?)<\/a>/is',
				'type' => '/<dt>类型：<\/dt>.*?<dd class="inline">[\r\s\n]*(.*?)[\r\s\n]*<\/dd>/is'
			),
			'm_t_c' => array(
				'directors' => '/<dt>导演：<\/dt>[\s\r\n]*<dd itemprop="director">(.*?)<\/dd>/is',
				'default_directors' => '/<dt>导演：<\/dt>[\s\r\n]*<dd class="inline">(.*?)<\/dd>/is',
				'actors' => '/<dt>主演：<\/dt>[\s\r\n]*<dd itemprop="actors">(.*?)<\/dd>/is',
				'default_actors' => '/<dt>主演：<\/dt>[\s\r\n]*<dd class="inline">(.*?)<\/dd>/is',
				'area' => '/<dt>地区：<\/dt>[\s\r\n]*<dd>(.*?)<\/dd>/is',
				'default_area' => '/<dt>地区：<\/dt>[\s\r\n]*<dd class="inline">(.*?)<\/dd>/is'
			)
		);
		static $flags = array(1 => 'Movie', 2 => 'Tv', 3 => 'Show', 4 => 'Comic', 0 => 'Movie');		
		$aRet = array(
			'name' => '', 'cateid' => '', 'pic' => '', 'actors' => '暂无', 
			'directors' => '暂无', 'showyear' => 0, 'area' => '暂无', 'type' => '暂无', 'typetag' => '暂无',
			'description' => '', 'url' => Lamb_App::getGlobalApp()->getRouter()->encode($url)
		);
					
		if (!($content = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $aRet;
		}

		if (!Lamb_App::getGlobalApp()->isAppUTF8($charset)) {
			$content = iconv('utf-8', $charset . '//ignore', $content);
		}
		
		if (($typeid = $this->getTypeidFromContent($content)) == -1) {
			$typeid = 0;
		}
		$aRet['cateid'] = $typeid;
		if ((!preg_match($patts['public']['name'], $content, $result) && !preg_match($patts['public']['default_name'], $content, $result)) || !$result[1] ) {
			$error = self::E_RULE_NOT_MATCH;
			return $aRet;
		}

		$aRet['name'] = trim($result[1]);	
		
		if (preg_match($patts['public']['type'], $content, $result)) {
			$aRet['type'] = explode('/', $result[1]);
		} 
		
		foreach (array('showyear', 'pic', 'default_showyear') as $key) {
			if (!preg_match($patts['public'][$key], $content, $result) || empty($result[1])) {
				continue ;
			}
			$key = str_replace('default_', '', $key);
			$aRet[$key] = trim($result[1]);
		}
		
		if (preg_match($patts['public']['description_bak'], $content, $result) || preg_match($patts['public']['description'], $content, $result)) {
			$aRet['description'] = preg_replace('/(?:<a.*?>.*?<\/a>)|(?:<br.*?>)/is', '', trim($result[1]));
		}	

		if ($typeid != 3) {
			if ((preg_match($patts['m_t_c']['area'], $content, $result) || preg_match($patts['m_t_c']['default_area'], $content, $result)) && !empty($result[1])) {
				$aRet['area'] = trim($result[1]);		
			}
		
			foreach (array('directors', 'actors', 'default_actors', 'default_directors') as $key) {
				if (!preg_match($patts['m_t_c'][$key], $content, $result) || !preg_match_all($patts['public']['a_item'], $result[1], $result)) {
					continue ;
				}
				$key = str_replace('default_', '', $key);
				$aRet[$key] = $result[1];
			}			
		}		
		
		$funcname = 'collectFor' . $flags[$typeid];	
		$error = $this->$funcname($content, $aRet);
		
		$aRet['area'] = explode('/', $aRet['area']);
		$aRet['area'] = trim($aRet['area'][0]);

		if (in_array($aRet['area'], array('中国大陆', '内地'))) {
			$aRet['area'] = '大陆';
		}
		
		if (!Lamb_Utils::isInt($aRet['showyear'], true)) {
			if (($time = strtotime($aRet['showyear'])) !== false) {
				$aRet['showyear'] = date('Y', $time);
			} else {
				$aRet['showyear'] = 0;
			}
		}
		
		$error = self::S_OK;
		return $aRet;		
	}
	
	public function collectForMovie($content, array &$aRet)
	{
		static $patts = array(
			'typetag' => '/<dt>看点：<\/dt>[\s\r\n]*<dd itemprop="keywords">(.*?)<\/dd>/is',
			'a_item' => '/<a.*?>[\r\s\n]*(.*?)[\r\s\n]*<\/a>/is'
		);

		if (preg_match($patts['typetag'], $content, $result) && !empty($result[1])) {
			if (preg_match_all($patts['a_item'], $result[1], $typetags )) {
				$aRet['typetag'] = $typetags[1];
			}
		}

		unset($aRet);
		return self::S_OK;
	}
	
	public function collectForTv($content, array &$aRet)
	{
		$this->collectForMovie($content,$aRet);
		unset($aRet);
		return self::S_OK;	
	}
	
	public function collectForComic($content, array &$aRet)
	{
		static $patts = array(
			'typetag' => '/<dt>看点：<\/dt>[\s\r\n]*<dd itemprop="keywords">(.*?)<\/dd>/is',
			'a_item' => '/<a.*?>[\r\s\n]*(.*?)[\r\s\n]*<\/a>/is',
			'showyear' => '/<dt>年份：<\/dt>[\s\r\n]* <dd class="inline">[\r\s\n]*(.*?)[\r\s\n]*<\/dd>/is'
		);

		if (preg_match($patts['typetag'], $content, $result) && !empty($result[1])) {
			if (preg_match_all($patts['a_item'], $result[1], $typetags )) {
				$aRet['typetag'] = $typetags[1];
			}
		}
		if (preg_match($patts['showyear'], $content, $result) && !empty($result[1])) {
			$aRet['showyear'] = $result[1];
		}

		unset($aRet);
		return self::S_OK;
	}
	
	public function collectForShow($content, array &$aRet)
	{
		static $patts = array(
			'typetag' => '/<dt>类型：<\/dt>[\s\r\n]*<dd>(.*?)<\/dd>/is',
			'directors' => '/<dt>播出：<\/dt>[\s\r\n]*<dd>(.*?)<\/dd>/is',
			'actors' => '/<dt>主持：<\/dt>[\s\r\n]*<dd class="inline">(.*?)<\/dd>/is',
			'pic' => '/<img itemprop="image".*?src="(.*?)".*?\/>/is',
			'a_item' => '/<a.*?>(.*?)<\/a>/is'
		);

		if (preg_match($patts['typetag'], $content, $result) && !empty($result[1])) {
			$aRet['typetag'] = preg_split('/\s*\/\s*/is', trim($result[1]));	
		}
		
		if (preg_match($patts['directors'], $content, $result) && !empty($result[1])) {
			$aRet['directors'] = trim($result[1]);	
		}	
		
		if (preg_match($patts['pic'], $content, $result) && !empty($result[1])) {
			$aRet['pic'] = trim($result[1]);	
		}	
		
		if (preg_match($patts['actors'], $content, $result) && !empty($result[1]) &&
			preg_match_all($patts['a_item'], $result[1], $result)) {
			$aRet['actors'] = $result[1];
		}

		unset($aRet, $webColumn, $pcColumn, $showPics);
		return self::S_OK;		
	}
	
}