<?php
class Ttkvod_Model_Video
{
	const T_VID = 1;
	
	const T_VIDEO_NAME = 2;
	
	const T_IS_LOCKCOLL = 4;
	
	protected $mListeners = array();
	
	public function __construct()
	{
	
	}
	
	/**
	 * @param int $event Ttkvod_Model_VideoListenerInterface::ON_BEFORE_UPDATE ...
	 * @param Ttkvod_Model_VideoListenerInterface
	 * @return Ttkvod_Model_Video
	 */
	public function addEventListener($event, Ttkvod_Model_VideoListenerInterface $listener)
	{
		$this->mListeners[$event][] = $listener;
		return $this;
	}
	
	/**
	 * @param int $event
	 * @param Ttkvod_Model_VideoListenerInterface $listener
	 * @return Ttkvod_Model_Video
	 */
	public function removeListener($event, Ttkvod_Model_VideoListenerInterface $listener)
	{
		if (isset($this->mListeners[$event]) && ($listeners = $this->mListeners[$event]) && ($index = array_search($listener, $listeners)) !== false) {
			unset($this->mListeners[$event][$index]);
		}
		return $this;
	}
	
	/**
	 * @param int $event
	 * @param array $videoInfo
	 * @return Ttkvod_Model_Video
	 */
	public function fireEvent($event, array $videoInfo)
	{
		if (isset($this->mListeners[$event])) {
			foreach ($this->mListeners[$event] as $listener) {
				$listener->on($event, $videoInfo);
			}
		}
		return $this;
	}
	
	/**
	 * @param string | int $val
	 * @param int $type T_VID | T_VIDEO_NAME
	 * @return null | int | array
	 */
	public function get($val, $type = self::T_VID, $isGetData = false, $includeVid = 0)
	{
		$sql = 'select ' . ($isGetData ? '*' : 'id') . ' from vedio where ';
		$aPrepareSource = array();
		if ($type & self::T_VID) {
			$sql .= 'id=?';
			$aPrepareSource[1] = array($val, PDO::PARAM_INT);
		} else if ($type & self::T_VIDEO_NAME) {
			$sql .= 'name=?';
			$aPrepareSource[1] = array($val, PDO::PARAM_STR);
		} else {
			return null;
		}
		if ($type & self::T_VID && $includeVid > 0) {
			$sql .= ' and id != :niid';
			$aPrepareSource[':niid'] = array($includeVid, PDO::PARAM_INT);
		}
		$ret = Lamb_App::getGlobalApp()->getDb()->getNumDataPrepare($sql, $aPrepareSource, true);
		if ($ret['num'] != 1) {
			return null;
		}
		return $isGetData ? $ret['data'] : $ret['data']['id'];
	}
	
	/**
	 * @param mixed $val
	 * @param int $type T_VID | T_VIDEO_NAME
	 * @param array $videoinfo
	 * @return int 1 - succ 0 - not found -1 videoname exits
	 */
	public function update($val, $type, array $videoInfo)
	{
		$type = (int)$type;
		if (!($ret = $this->get($val, $type, true))) {
			return 0;
		}
		unset($videoInfo['id']);
		if (isset($videoInfo['name']) && $ret['name'] != $videoInfo['name']) {
			$videoInfo['pinyin'] = Lamb_Utils::pinyin($videoInfo['name']);
			if ($this->get($videoInfo['name'], self::T_VIDEO_NAME, false, $ret['name'])) {
				return -1;
			}
			$videoInfo['tagname'] = self::encodeFullSearchStr($videoInfo['name']);
		}
		$this->fireEvent(Ttkvod_Model_VideoListenerInterface::ON_BEFORE_UPDATE, $videoInfo);
		$videoDataInfo = array();
		if (isset($videoInfo['play_data'])) {
			$videoDataInfo['play_data'] = $videoInfo['play_data'];
			unset($videoInfo['play_data']);
		}

		if (isset($videoInfo['tag']) && !isset($videoInfo['tagcode'])) {
			$videoInfo['tagcode'] = self::encodeTag($videoInfo['tag']); 
		}

		$videoTable = new Lamb_Db_Table('vedio');
		$videoDataTable = new Lamb_Db_Table('vedio_data');
		$db = Lamb_App::getGlobalApp()->getDb();
		$tagmodle = new Ttkvod_Model_Tag();
		if (isset($videoInfo['directors']) && $videoInfo['directors'] != $ret['directors']) {
			$tagmodle->compareTag($videoInfo['directors'], $ret['directors'], $ret['id']);
		}
		if (isset($videoInfo['actors']) && $videoInfo['actors'] != $ret['actors'])	{
			$tagmodle->compareTag($videoInfo['actors'], $ret['actors'], $ret['id']);
		}
		if (isset($videoInfo['vedioType']) && $videoInfo['vedioType'] != $ret['vedioType'])	{
			$tagmodle->compareTag($videoInfo['vedioType'], $ret['vedioType'], $ret['id']);
		}
		

		$db->begin();
		if (isset($videoDataInfo['play_data']) && count($videoDataInfo['play_data']) > 0) {
			foreach($videoDataInfo['play_data'] as $item) {
				$id = $item['id'];
				unset($item['id']);
				$videoDataTable->setOrGetWhere('id=' . $id)
							   ->set($item)
							   ->execute();
			}
		}
		$videoTable->setOrGetWhere('id=' . $ret['id'])
				   ->set($videoInfo)
				   ->execute();
				   
		if($db->end()) {
			$temp = $videoInfo + $videoDataInfo;
			$this->fireEvent(Ttkvod_Model_VideoListenerInterface::ON_AFTER_UPDATE, $temp);
			return 1;
		} 
		return 0;
	}
	
	/**
	 * @param array $videoInfo
	 * @return int > o new uid 0 - unknown err -1 name exists
	 */
	public function add(array $videoInfo)
	{
		unset($videoInfo['id']);
		if ($this->get($videoInfo['name'], self::T_VIDEO_NAME)) {
			return -1;
		}
		$this->fireEvent(Ttkvod_Model_VideoListenerInterface::ON_BEFORE_INSERT, $videoInfo);
		$videoInfo['tagname'] = self::encodeFullSearchStr($videoInfo['name']);
		$videoInfo['pinyin'] = Lamb_Utils::pinyin($videoInfo['name']);
		$videoDataInfo['play_data'] = $videoInfo['play_data'];
		unset($videoInfo['play_data']);

		if (isset($videoInfo['tag']) && !isset($videoInfo['tagcode'])) {
			$videoInfo['tagcode'] = self::encodeTag($videoInfo['tag']); 
		}

		$videoTable = new Lamb_Db_Table('vedio', Lamb_Db_Table::INSERT_MODE);
		$videoDataTable = new Lamb_Db_Table('vedio_Data', Lamb_Db_Table::INSERT_MODE);
		$db = Lamb_App::getGlobalApp()->getDb();
		$tagmodel = new Ttkvod_Model_Tag();	
		$db->begin();
		$videoTable->set($videoInfo)
				   ->execute();
		$vid = $db->lastInsertId();
		$videoDataInfo['id'] = $vid;
		$videoDataTable->set($videoDataInfo)
					   ->execute();
		if ($db->end()) {
			$temp = $videoInfo + $videoDataInfo;
			$this->fireEvent(Ttkvod_Model_VideoListenerInterface::ON_AFTER_INSERT, $temp);
			$tagmodel->handle($videoInfo['directors'], $vid);
			$tagmodel->handle($videoInfo['actors'], $vid);
			$tagmodel->handle($videoInfo['vedioType'], $vid);				
			return $vid;
		}
		return 0;
	}
	
	/**
	 * @param mixed $val
	 * @param int $type T_VID | T_VIDEO_NAME
	 * @return boolean
	 */
	public function delete($val, $type = self::T_VID)
	{
		if (!$this->get($val, $type)) {
			return false;
		}
		$param = array($val, $type);
		$this->fireEvent(Ttkvod_Model_VideoListenerInterface::ON_BEFORE_DELETE, $param);
		$aPrepareSource = array(1 => array($val, PDO::PARAM_INT));
		if (Lamb_App::getGlobalApp()->getDb()->quickPrepare('delete from vedio where id=?', $aPrepareSource, true)) {
			$this->fireEvent(Ttkvod_Model_VideoListenerInterface::ON_AFTER_DELETE, $param);
			return true;
		}
		return false;
	}
	
	/**
	 * @param boolean $reset
	 * @return Ttkvod_Model_Video
	 */
	public function viewNumHandler($id, $reset = 0) 
	{return;
		$db = Lamb_App::getGlobalApp()->getDb();
		if ($reset == 1) {
			$db->exec('update vedio set weekNum=0');
		} else if ($reset == 2) {
			$db->exec('update vedio set monthNum=0,monthPoint=0');
		} else {
			$db->quickPrepare('update vedio set weekNum=weekNum+1, viewNum=viewNum+1, monthNum=monthNum+1 where id = ?', 
										array( 1 => array($id, PDO::PARAM_INT)), true);
		}							
		return $this;		
	}
	
	/**
	 * @param string $name
	 * @param boolean $encode
	 * @return string
	 */
	public function getPlayDataByName($name, $encode = true)
	{
		$cache = $this->getPlayDataCache($name, self::T_VIDEO_NAME);
		if ($cache->isCached()) {
			return $cache->read();
		}
		$ret = '';
		$sql = 'select * from vedio_data a,vedio b where a.id=b.id and name=?';
		$aPrepareSource = array( 1 => array($name, PDO::PARAM_STR));
		$db = Lamb_App::getGlobalApp()->getDb();
		$data = $db->getNumDataPrepare($sql, $aPrepareSource, true);
		if ($data['num'] == 1) {
			$ret = $data['data']['play_data'];
			$listindex = 0;
			$subTitleDelimiter = '[$]';
			$result = array();
			foreach (explode("\r\n", $ret) as $item) {
				$subtitle = '';
				if (!empty($item)) {
					if ($pos = strpos($item, $subTitleDelimiter)) {
						$subtitle = substr($item, $pos + strlen($subTitleDelimiter));
						$item = substr($item, 0, $pos);
						if ($subtitle && substr($subtitle, 0, 1) == '_' && ($temp = substr($subtitle, 1)) && Lamb_Utils::isInt($temp, true)) {
							$temp = (int)$temp;
							$subtitle = '第' . ($temp >= 10 ? '' : '0') . $temp . '集';
							$listindex = $temp - 1;
						}
					}
					if (!$subtitle) {
						$subtitle = '第' . ($listindex + 1 >= 10 ?  '' : '0') . ($listindex + 1) . '集';
					}
					$listindex++;
					$result[] = trim($item) . $subTitleDelimiter . $subtitle;
				}
			}
			$ret = implode("\r\n", $result);
			if ($encode) {
				$cfg = Lamb_Registry::get(CONFIG);
				$ret = Lamb_Utils::authcode($ret, $cfg['encode_key'], 'ENCODE');
			}
		}
		$cache->write($ret);
		return $ret;
	}
	
	/**
	 * @param int | string $mixed
	 * @param int $type T_VID T_VIDEO_NAME
	 * @return null | Ttkvod_Cache_Interface
	 */
	public function getPlayDataCache($mixed, $type = self::T_VID)
	{
		$ret = null;
		if ($type == self::T_VID) {
			$data = $this->get($mixed, self::T_VID, true);
			if (!$data) {
				return $ret;
			}
			$mixed = $data['name'];
		}
		return Ttkvod_Cache_Factory::getCache()->setCacheTime(24 * 3600)->setIdentity(__CLASS__ . '_getpalydata_byname_' . Lamb_Utils::crc32FormatHex($mixed));
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	public static function encodeFullSearchStr($string, $len = 200)
	{
		$ret = '';
		foreach (Ttkvod_Utils::splitWords($string, $len) as $item) {
			$ret .= base64_encode(strtoupper($item)) . ' ';
		}
		return self::encode(trim($ret));
	}

	/**
	 * @param int & $allCount
	 * @param int & $succnum
	 * @return boolean
	 */
	public static function translteDatetimeToInt($page, $pagesize = 100)
	{
		$db = Lamb_App::getGlobalApp()->getDb();
		$sqlHelper = Lamb_App::getGlobalApp()->getSqlHelper();
		$res = Lamb_App::getGlobalApp()->getResponse();
		$sql_up = 'update vedio set updateDate=:updateDate where id=:id';
		$sql = $sqlHelper->getPageSql('select name,id,updateDate from vedio', $pagesize, $page);
		$aPrepareSource = array();
		
		foreach ($db->query($sql) as $item) {
			$item['updateDate'] = strtotime($item['updateDate']);
			$aPrepareSource[':id'] = array($item['id'], PDO::PARAM_INT);
			$aPrepareSource[':updateDate'] = array($item['updateDate'], PDO::PARAM_INT);
			$msg = "id : {$item['id']}, name : {$item['name']}";
			if ($db->quickPrepare($sql_up, $aPrepareSource, true)){
				$res->fecho("$msg <b style='color:green'>执行成功</b><br/>");
			} else {
				$res->fecho("$msg <b style='color:red'>执行失败</b><br/>");
			}
		}
		return true;
	}	
	
	public static function generateNewSearchTag($page, $pagesize)
	{
		$app = Lamb_App::getGlobalApp();
		$sql = $app->getSqlHelper()->getPageSql('select id,name from vedio', $pagesize, $page);
		$db = $app->getDb();
		$res = $app->getResponse();
		$sql_up = 'update vedio set tagname=:tagname where id=:id';
		$aPrepareSource = array();
		foreach ($db->query($sql) as $item) {
			$aPrepareSource[':id'] = array($item['id'], PDO::PARAM_INT);
			$aPrepareSource[':tagname'] = array(self::encodeFullSearchStr($item['name']), PDO::PARAM_STR);
			$msg = "id : {$item['id']}, name : {$item['name']}";
			if ($db->quickPrepare($sql_up, $aPrepareSource, true)) {
				$res->fecho("$msg <b style='color:green'>执行成功</b><br/>");
			} else {
				$res->fecho("$msg <b style='color:red'>执行失败</b><br/>");
			}
		}
		$objRecordSet = null;
		return true;
	}
	
	public static function repaireTag($page, $pagesize = 100)
	{
		$app = Lamb_App::getGlobalApp();
		$db = $app->getDb();
		$sqlHelper = $app->getSqlHelper();
		$res = $app->getResponse();
		$tagmodel = new Ttkvod_Model_Tag();
		$sql = $sqlHelper->getPageSql('select id,type,directors,actors,name from vedio', $pagesize, $page);
		foreach ($db->query($sql)->toArray() as $item) {
			$tagmodel->handle($item['type'], $item['id']);
			$tagmodel->handle($item['directors'], $item['id']);
			$tagmodel->handle($item['actors'], $item['id']);
			$res->fecho("id : {$item['id']}, name : {$item['name']} 修复成功<br/>");
		}	
	}

	public static function encodeTag($tag)
	{
		$tagmodel = new Ttkvod_Model_Tag;
		$ret = array();
		foreach($tagmodel->parse($tag) as $item) {
			$ret[] = base64_encode(strtoupper($item));
		}
		return self::encode(implode(' ', $ret));
	}
	
	/**
	 * @param string $str
	 * @return string
	 */
	protected static function encode($str)
	{
		return str_replace('=', 'c', str_replace('/', 'b', str_replace('+', 'a', trim($str))));
	}
}