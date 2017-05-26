<?php
class Ttkvod_Proxy_Baidu_Search extends Ttkvod_Proxy_Baidu_Abstract implements Ttkvod_Proxy_SearchInterface
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getUrl($keywords, $page = 1)
	{
		return 'http://v.baidu.com/v?word=' . $keywords;
	}
	
	public function collect($url, $externals = null, &$error = null)
	{
		$ret = array();

		$patts = array(
			'top_item' => '/class="poster-sec">(.*?)class="tag-wrapper">/is',
			'top_item_play' => '/src="(.*?)"(.*?)class="arrow"><\/span>(.*?)<\/span>.*?class="desc-wrapper">(.*?)<\/p>/is',
			
			'tv_item' => '/class="update-info">(.*?)<\/span>.*?href="(.*?)".*?color=#c60a00>(.*?)<\/span>/is',
			'movie_item' => '/href="(.*?)".*?color=#c60a00>(.*?)<\/span>.*?style="color:#999;">&nbsp;(.*?)<\/span>/is',
			'variety_item' => '/href="(.*?)".*?color=#c60a00>(.*?)<\/span>.*?class="update-info">(.*?)<\/span>/is',
			
			'item_list' => '/class="sinfo(.*?)<\/div>/is',
			'item_actors' => '/class="plaintxt onel ">(.*?)<\/span>/is',
			'description' => '/class="vdetail plaintxt">(.*?)<\/span>/is'
		);

		if (!$html = Ttkvod_Utils::fetchContentByUrlH($url)) {
			return $ret;
		}

		if (!Lamb_App::getGlobalApp()->isAppGBK($charset)) {
			$html = iconv('gbk', $charset . '//ignore', $html);
		}	

		if(!preg_match_all($patts['top_item'], $html, $result, PREG_SET_ORDER)){
			$error = self::E_NET_FAIL;
			return $ret;
		}
	
		foreach($result as $item){
			if(!preg_match_all($patts['top_item_play'], $item[1], $result, PREG_SET_ORDER)){
				continue;
			}			

			if(!preg_match_all($patts['item_list'], $result[0][4], $messageList, PREG_SET_ORDER)){
				continue;
			}
			
			if(!preg_match($patts['description'], $result[0][4], $description)){
				continue;
			}	

			$type = trim($result[0][3]);
			switch ($type){
				case '电视剧': $type = 'tv';break;
				case '电影': $type = 'movie';break;
				case '动漫': $type = 'comic';break;
				case '综艺': $type = 'variety';break;
			}		
			
			if('tv' == $type || 'movie' == $type){
				if(!preg_match_all($patts['item_actors'], $messageList[0][1], $arctors, PREG_SET_ORDER)){
					continue;
				}
			}
			if('variety' != $type){
				if(!preg_match_all($patts['item_actors'], $messageList[1][1], $directors, PREG_SET_ORDER)){
					continue;
				}
			}

			if(!preg_match_all($patts['item_actors'], $messageList[2][1], $areas, PREG_SET_ORDER)){
				continue;
			}
		
			if('tv' == $type || 'comic' == $type){
				if(!preg_match($patts['tv_item'], $result[0][2], $message)){
					continue;
				}
				if(!preg_match_all($patts['item_actors'], $messageList[4][1], $tags, PREG_SET_ORDER)){
					continue;
				}
				$nums = trim(Ttkvod_Utils::filterHtmlTag($message[1]));
				$url  = trim($message[2]);
				$title = preg_replace('/\s*/', '', Ttkvod_Utils::filterHtmlTag($message[3]));		
			}else if('movie' == $type){
				if(!preg_match($patts['movie_item'], $result[0][2], $message)){
					continue;
				}
				if(!preg_match_all($patts['item_actors'], $messageList[3][1], $tags, PREG_SET_ORDER)){
					continue;
				}
				$nums = trim(Ttkvod_Utils::filterHtmlTag($message[3]));
				$url  = trim($message[1]);
				$title = preg_replace('/\s*/', '', Ttkvod_Utils::filterHtmlTag($message[2]));
			}else if('variety' == $type){
				if(!preg_match($patts['variety_item'], $result[0][2], $message)){
					continue;
				}
				if(!preg_match_all($patts['item_actors'], $messageList[3][1], $tags, PREG_SET_ORDER)){
					continue;
				}
				if(!preg_match_all($patts['item_actors'], $messageList[1][1], $comperes, PREG_SET_ORDER)){
					continue;
				}
				if(!preg_match($patts['item_actors'], $messageList[4][1], $station)){
					continue;
				}

				$nums = trim(Ttkvod_Utils::filterHtmlTag($message[3]));
				$url  = trim($message[1]);
				$title = preg_replace('/\s*/', '', Ttkvod_Utils::filterHtmlTag($message[2]));
			} else {
				continue;
			}
			
			$actorsName = '';
			$directorsName = '';
			$areasName = '';
			$tagsName = '';
			$compereName = '';
			$stationName  = '';

			if('tv' == $type || 'movie' == $type){
				foreach($arctors as $arctor){
					$actorsName = $actorsName . ' ' . $arctor[1];
				}
				
			}

			if('tv' == $type || 'movie' == $type || 'comic' == $type){
				foreach($directors as $director){
					$directorsName = $directorsName . ' ' . $director[1];
				}
			}

			foreach($areas as $area){
				$areasName = $areasName . ' ' . $area[1];
			}
			foreach($tags as $tag){
				$tagsName = $tagsName . ' ' . $tag[1];
			}
			
			if('variety' == $type){
				$stationName = $station[1];
				foreach($comperes as $compere){
					$compereName = $compereName . ' ' . $compere[1];
				}
			}

			$temp = array(
				'title'		=> $title, 
				'img'		=> Lamb_App::getGlobalApp()->getRouter()->urlEx('update', 'crackImg', array('url' => $result[0][1], 'refer' => 'http://www.baidu.com')), 
				'url'		=> $url, 
				'type'		=> $type, 
				'nums'		=> $nums,
				'compere'	=> $compereName,
				'actors'	=> $actorsName,
				'directors' => $directorsName,
				'area'		=> $areasName,
				'tags'		=> $tagsName,
				'describe'	=> $description[1],
				'station'	=> $stationName,
				'score' => '',
				'year' => ''
			);

			$ret[] = $temp;
		}

		$error = self::S_OK;
		return $ret;		
	}
}