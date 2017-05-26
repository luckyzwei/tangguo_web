<?php
class Ttkvod_Proxy_360_Search extends Ttkvod_Proxy_360_Abstract implements Ttkvod_Proxy_SearchInterface
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getUrl($keywords, $page = 1)
	{
		return 'http://so.v.360.cn/index.php?kw=' . Lamb_App_Response::encodeURIComponent($keywords) . '&pageno=' .$page;
	}
	
	public function collect($url, $externals = null, &$error = null)
	{//exit($url);
		$ret = array();
		$app = Lamb_App::getGlobalApp();
		$charset = $app->getCharset();
		$patts = array(
			'top_item' => '/class="avatar".*?href="(.*?)".*?title="(.*?)".*?data-src="(.*?)".*?class="info">(.*?)<\/span>.*?category">(.*?)<\/span>.*?class="starring">(.*?)<\/p>.*?class="director">(.*?)<\/p>.*?area.*?<span>(.*?)<\/span>.*?years.*?<span>(.*?)<\/span>.*?type.*?<span>(.*?)<\/span>.*?class="intro">(.*?)<\/div>/is',
			
		);

		if (!($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;	
		}
		$html = iconv('utf-8', $charset . '//ignore', $html);
		
		if (!preg_match_all($patts['top_item'], $html, $result, PREG_SET_ORDER)){
			$error = self::E_RULE_NOT_MATCH;
			return $ret;		
		}	
		
		foreach($result as $item) {
			$type = trim($item[5]);
			$actor_name = '';
			$director_name = '';
			switch ($type){
				case '[电视剧]': $t_type = 'tv';break;
				case '[电影]': $t_type = 'movie';break;
				case '[动漫]': $t_type = 'comic';break;
				case '[综艺]': $t_type = 'variety';break;
			}
			if(preg_match_all('/<a.*?>(.*?)<\/a>/is', $item[6], $actors, PREG_SET_ORDER)){
				foreach($actors as $actor){
					$actor_name = $actor_name . $actor[1] . ' ';
				}
			}else{
				$actor_name = '未知';
			}
			if(preg_match_all('/<a.*?>(.*?)<\/a>/is', $item[7], $directors, PREG_SET_ORDER)){
				foreach($directors as $director){
					$director_name = $director_name . $director[1] . ' ';
				}
			}else{
				$director_name = '未知';
			}
			
			if('[电影]' == $type){
				preg_match('/<em>(.*?)<\/em>.*?<ins>(.*?)<\/ins>/is', $item[4], $time);
				$nums = $time[1] . ' ' .$time[2];
			}else{
				$nums = trim($item[4]);
			}

			$temp = array(
				'title' => $item[2],
				'img'   => trim($item[3]),
				'url'   => trim($item[1]),
				'type'  => $t_type,
				'nums'  => $nums,
				'score' => '',
				'actors' => $actor_name,
				'directors' => $director_name,
				'area'  => trim($item[8]),
				'year'  => trim($item[9]),
				'tags'   => trim($item[10]),
			);	
			

			$brief = trim(Ttkvod_Utils::filterHtmlTag($item[11]));
			$temp['describe'] = str_replace('显示详情&gt;&gt;', '', $brief);
			$temp['station'] = '';
			$ret[] = $temp;
		}
		
		return $ret;
	}
}