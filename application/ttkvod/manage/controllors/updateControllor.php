<?php
class updateControllor extends Ttkvod_ManageControllor
{
	protected static $sSources = array(
		'360' => '', 'Yisou' => '', 'Baidu' => '', 'Douban' => ''
	);
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getControllorName()
	{
		return 'index';
	}	
	
	public function indexAction()
	{
		$q = trim($this->mRequest->q);
		$source = trim($this->mRequest->key);
		$page = trim($this->mRequest->p);
		$id = trim($this->mRequest->id);

		$isEmpty = $q == '';
		
		$page = $page == '' ? 1 : $page;
		
		if ($source == '' || $id == '') {
			return;
		}
		
		if (!array_key_exists($source, self::$sSources)) {
			return;
		}
		
		$itemName = 'Ttkvod_Proxy_'. $source . '_Item';
		$searchName = 'Ttkvod_Proxy_'. $source . '_Search';
		$sourceMode = new $searchName();
		$itemMode = new $itemName();
		//Lamb_Debuger::debug($searchName);
		$data = $sourceMode->collect($sourceMode->getUrl($q, $page), null, $error);
		$newdata = array();
		foreach ($data as $item) {
			$ret = $itemMode->collect($item['url'], null, $error);
			if ($error != Ttkvod_Proxy_Interface::S_OK) {
				continue;
			}
			
			foreach (array('actors', 'directors', 'typetag') as $key) {
				if (is_array($ret[$key])) {
					$ret[$key] = implode(' ', $ret[$key]);
				}
			}
			
			if (!$ret['type']) {
				$ret['type'] =  $item['tags'];
			} else if (is_array($ret['type'])){
				$ret['type'] = implode(' ', $ret['type']);
			}
			$newdata[] = $ret;
		}
		$data = $newdata;

		if (count($data) == 0) {
			$isEmpty = true;
		}		
		
		$firstUrl = $this->mRouter->urlEx($this->C, $this->A, array(
			'q' => $q,
			'key' => $source,
			'id' => $id,
			'p' => 1
		));
		$nextUrl = $this->mRouter->urlEx($this->C, $this->A, array(
			'q' => $q,
			'key' => $source,
			'id' => $id,
			'p' => $page + 1
		));
		$prevUrl = $this->mRouter->urlEx($this->C, $this->A, array(
			'q' => $q,
			'key' => $source,
			'id' => $id,
			'p' => $page != 1 ? $page - 1 : 1
		));

		include $this->load('update_index');
	}
	
	public function selectAction()
	{
		$q = trim($this->mRequest->url);
		$id = trim($this->mRequest->id);
		$source = trim($this->mRequest->key);
		$ext = trim($this->mRequest->ext);

		if ($source == '' || $id == '') {
			return;
		}
		$data = $this->getData($source, $q);
		if(!$data) {
			return;
		}
		
		if ($ext) {
			$data['type'] = $ext;
		}
		include $this->load('update_select');
	}
	
	public function quickAction()
	{
		static $keymap = array(
			'pic' => 'vedioPic',
			'type' => 'vedioType',
			'typetag' => 'tag',
			'showyear' => 'syDate',
			'description' => 'content'
		);
		$id   = trim($this->mRequest->id);
		$url  = trim($this->mRequest->url);
		$data = $this->mRequest->data;
		$source = trim($this->mRequest->key);
		$ext = trim($this->mRequest->ext);

		if (!Lamb_Utils::isInt($id, true)) {
			return ;
		}

		if (!is_array($data)) {	
			$_data = $this->getData($source, $url);
			
			if ($data != 'all') {
				$data = json_decode($data, true);
				
				if (!$data) {
					return false;
				}
				
				$temp = array();
				foreach ($data as $key => $val) {
					if (isset($_data[$key])) {
						$temp[$key] = $_data[$key];
					}
				}
				
				$data = $temp;
				unset($temp);
			} else {
				$data = $_data;
			}
		} else if (!is_array($data)) {
			return false;
		} else {
			foreach ($data as $key => $val) {
				$data[$key] = iconv('utf-8', 'gbk', $val);
			} 
		}
		
 		unset($data['name'], $data['cateid'], $data['url']);
		
		foreach ($keymap as $key => $newkey) {
			if (isset($data[$key])) {
				$data[$newkey] = $data[$key];
				unset($data[$key]);
			}
		}
		
		if ($ext) {
			$data['vedioType'] = $ext;
		}

		if (isset($data['vedioPic'])) {
			if (!Lamb_Utils::isHttp($data['vedioPic'])) {
				$data['vedioPic'] = 'http://' . $this->mRequest->getHttpHost() . '/' . $data['vedioPic'];
			}
			$newImgPath = Lamb_Utils::crc32FormatHex(Lamb_Utils::getRandString()) . '.jpg';
			$url = $this->mSiteCfg['img_url_group']['up'] . '/' . $this->mRouter->urlEx('index', 'imgsys', array('u' => "{$data['vedioPic']}[$]$newImgPath"));
			Ttkvod_Http::quickGet($url, 20, true);
			$data['vedioPic'] = '/upload/pic/' . $newImgPath;
		}
		
		$model = new Ttkvod_Model_Video;
		$model->update($id, Ttkvod_Model_Video::T_VID, $data) ? $this->mResponse->eecho('succ') : '';
	}
	
	public function getData($sourceName, $url)
	{
		if (!array_key_exists($sourceName, self::$sSources)) {
			return false;
		}
		$itemName = 'Ttkvod_Proxy_'. $sourceName . '_Item';
		$itemMode = new $itemName();
		$data = $itemMode->collect($url, null, $error);
		if ($error != Ttkvod_Proxy_Interface::S_OK) {
			return false;
		}
		
		foreach ( array('actors', 'directors', 'typetag', 'type') as $value) {
			if (is_array($data[$value])) {
				$data[$value] = implode(',' , $data[$value]);
			} 
		}
		return $data;
	}
	
	public function crackImgAction()
	{
		$url = trim($this->mRequest->url);
		$refer = trim($this->mRequest->refer);
		header('Content-type:image/jpeg');
		$data = Ttkvod_Http::request(array('url' => $url, 'refer' => $refer), $status);
		if ($status != 200) {
			return;
		}
		echo $data;
	}	
}
