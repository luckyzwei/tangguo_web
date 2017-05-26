<?php
class Ttkvod_Proxy_Yisou_Search extends Ttkvod_Proxy_Yisou_Abstract implements Ttkvod_Proxy_SearchInterface
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getUrl($keywords, $page = 1)
	{
		return 'http://www.yisou.com/?q=' . Lamb_App_Response::encodeURIComponent($keywords) . '&p=' .$page;
	}
	
	public function collect($url, $externals = null, &$error = null)
	{
		$ret = array();
		$app = Lamb_App::getGlobalApp();
		$charset = $app->getCharset();
		$patts = array(
			'top_item' => '/class="video-intro".*?href="(.*?)".*?alt="(.*?)".*?src="(.*?)".*?class="type">(.*?)<\/span>.*?class="video-meta">(.*?)<div/is',
			'tv_item' => '/<dl.*?>(.*?)<\/dl>/is',
						
		);

		if (!($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;		
		}
		$html = iconv('utf-8', $charset . '//ignore', $html);
			
		if (!preg_match_all($patts['top_item'], $html, $result, PREG_SET_ORDER)){
			$error = self::S_OK;
			$item = new Ttkvod_Proxy_Yisou_Item();
			$res = $item->collect($url, null, $error2);
			if ($error2 != self::S_OK) {
				$error = self::E_RULE_NOT_MATCH;
				return $ret;
			}
			foreach(array(
						'title' => 'name', 
						'img' => 'pic',
						'actors' => 'actors',
						'directors' => 'directors',
						'area' => 'area',
						'url' => 'url',
						'year' => 'showyear',
						'tags' => 'type',
						'describe' => 'description',
					) as $key => $value) {
				$ret[$key] = is_array($res[$value]) ? implode(' ' , $res[$value]) : $res[$value];
			}
			$ret['url'] = $app->getRouter()->decode($ret['url']);
			$ret['station'] = '';
			$ret['type'] = 0;
			$ret['nums'] = '';
			$ret['score'] = '';
			
			return array($ret);
		}			
		foreach($result as $item){
			$type = trim($item[4]);
			
			if ($type == '[影人]') {
				continue;
			}
			
			switch ($type){
				case '[电视剧]': $t_type = 'tv';break;
				case '[电影]': $t_type = 'movie';break;
				case '[动漫]': $t_type = 'comic';break;
				case '[综艺]': $t_type = 'variety';break;
			}

			$temp = array(
				'title' => $item[2],
				'img'   => trim($item[3]),
				'url'   => 'http://www.yisou.com' . $item[1],
				'type'  => $t_type
			);
		//Lamb_Debuger::debug($temp);
			if(!preg_match_all($patts['tv_item'], $item[5], $message, PREG_SET_ORDER)) {
				continue ;
			}
			if($type != '[综艺]'){
				$actor_name = '';
				$director_name = '';
				$tag_name = '';

				preg_match_all('/<dd.*?>(.*?)<\/dd>/is', $message[1][1], $dir_tags, PREG_SET_ORDER);
				preg_match_all('/<dd>(.*?)<\/dd>/is', $message[2][1], $year_area, PREG_SET_ORDER);
				preg_match_all('/<dd>(.*?)<a/is', $message[3][1], $brief, PREG_SET_ORDER);
				if(!preg_match_all('/<a.*?>(.*?)<\/a>/is', $message[0][1], $actors, PREG_SET_ORDER)){
					$actor_name = '未知';
				}else{
					foreach($actors as $actor){
						$actor_name = $actor_name . $actor[1] . ' ';
					}
				}
				if(!preg_match_all('/<a.*?>(.*?)<\/a>/is', $dir_tags[0][1], $directors, PREG_SET_ORDER)){
					$director_name = '未知';
					$tag_name = trim($dir_tags[1][1]);
				}else{
					preg_match_all('/<a.*?>(.*?)<\/a>/is', $dir_tags[1][1], $tags, PREG_SET_ORDER);
					foreach($directors as $director){
						$director_name = $director_name . $director[1] . ' ';
					}
					foreach($tags as $tag){
						$tag_name = $tag_name . $tag[1] . ' ';
					}
				}
				
				$temp['nums']  = '';
				$temp['score'] ='';
				$temp['actors'] = $actor_name;
				$temp['directors'] = $director_name; 
				$temp['area'] = trim($year_area[1][1]);
				$temp['year'] = trim($year_area[0][1]);
				$temp['tags']  = $tag_name;
				$temp['describe'] = preg_replace('/[\s]{2,}/','',$brief[0][1]);
				$temp['station'] = '';
				
			}else{
				$host_name = '';
				preg_match_all('/<dd>(.*?)<\/dd>/is', $message[1][1], $tag_station, PREG_SET_ORDER);
				
				if(!preg_match_all('/<a.*?>(.*?)<\/a>/is', $message[0][1], $hosts, PREG_SET_ORDER)){
					$host_name = '未知';
				}else{
					foreach($hosts as $host){
						$host_name = $host_name . $host[1] . ' ';
					}
				}
				if(!preg_match_all('/<dd>(.*?)<a/is', $message[2][1], $brief, PREG_SET_ORDER)){
					$brief = '未知';
				}else{
					$brief = preg_replace('/[\s]{2,}/','',$brief[0][1]);
				}
				$tag = trim($tag_station[0][1]);
				$station = trim($tag_station[1][1]);

				$temp['nums']  = '';
				$temp['score'] ='';
				$temp['actors'] = $host_name;
				$temp['directors'] = '';
				$temp['area'] = '';
				$temp['year'] = '';
				$temp['tags'] = $tag;
				$temp['describe'] = $brief;
				$temp['station'] = $station;
			
			}

			$ret[] = $temp;
		}		
		
		return $ret;
	}
}