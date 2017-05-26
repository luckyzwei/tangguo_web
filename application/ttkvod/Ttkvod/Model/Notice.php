<?php
class Ttkvod_Model_Notice
{
	/**
	 * @var int
	 */
	protected $mUid;
	
	protected $mCache;
	
	protected static $mCacheTime = 86400;
	
	/**
	 * @param int $uid
	 */
	public function __construct($uid)
	{
		$this->mUid = $uid;
	}
	
	/**
	 * @param int $uid
	 * @return Ttkvod_Model_Notice
	 */
	public function setUid($uid)
	{
		$this->mUid = (int)$uid;
	}
	
	/**
	 * @param int $vid
	 * @param int $time
	 * @return int
	 */
	public function add($vid, $time)
	{
		return self::addNotice($this->mUid, $vid, $time);
	}

	/**
	 * @param int $vid
	 * @return int
	 */	
	public function remove($vid)
	{
		return self::removeNotice($this->mUid, $vid);
	}
	
	/**
	 * @param int $vid
	 * @return int 0 <= not found >0 time
	 */	
	public function isExistsItem($vid)
	{
		return self::isExistsNoticeItem($this->mUid, $vid);
	}
	
	/**
	 * @param int 
	 * @return boolean
	 */
	public function isCountFull($limit = null)
	{
		return self::isNoticeCountFull($this->mUid, $limit);
	}
	
	/**
	 * @param int $vid
	 * @param int $limit = null use default config
	 * @return int -1 full 0 - error > 0 succ
	 */	
	public function limitAdd($vid, $limit = null)
	{
		return self::limitAddNotice($this->mUid, $vid, $time, $limit);
	}
	
	/**
	 * @return array
	 */
	public function getUpdateVidInfos()
	{
		return self::getUpdateVideoInfos($this->mUid);
	}
	
	/**
	 * @param int $cachetime
	 */
	public static function setCacheTime($cachetime)
	{
		self::$mCacheTime = $cachetime;
	}
	
	/**
	 * @param int $uid
	 * @return Lamb_Cache_Interface
	 */
	public static function getCache($uid)
	{
		return Ttkvod_Cache_Factory::getCache()->setCacheTime(self::$mCacheTime)->setIdentity(__CLASS__ . '_getcache_byuid_' . $uid);
	}
	
	/**
	 * @param int $vid
	 */
	public static function clearCacheByVid($vid)
	{
		$recordset = self::getDb()->quickPrepare('select uid from notice where vid=?',
						array( 1 => array($vid, PDO::PARAM_INT)));
		foreach ($recordset as $item) {
			self::getCache($item['uid'])->flush();
		}
		$recordset = null;
	}
	
	/**
	 * @param int $uid
	 * @param int $vid
	 * @param int $limit = null use default config
	 * @return int -1 full 0 - error > 0 succ
	 */
	public static function limitAddNotice($uid, $vid, $limit = null)
	{
		if (null === $limit) {
			$cfg = Lamb_Registry::get(CONFIG);
			$limit = $cfg['member_notice_num'];
			unset($cfg);
		}
		$limit = (int)$limit;
		
		if (self::isNoticeCountFull($uid, $limit)) {
			return -1;
		}
		
		return self::addNotice($uid, $vid);
	}
	
	/**
	 * @param int $uid
	 * @param int $vid
	 * @return int
	 */
	public static function addNotice($uid, $vid)
	{
		$videoModel = new Ttkvod_Model_Video;
		if (!($data = $videoModel->get($vid, Ttkvod_Model_Video::T_VID, true))) {
			return 0;
		}

		return self::getDb()->quickPrepare(
				'insert into notice (uid,vid,time) values (?,?,?)',
				array( 1 => array($uid, PDO::PARAM_INT), 2 => array($vid, PDO::PARAM_INT), 3 => array($data['updateDate'], PDO::PARAM_INT))
				, true);
	}
	
	/**
	 * @param int $uid
	 * @param int $vid
	 * @return int 0 - not found > 0 time
	 */
	public static function isExistsNoticeItem($uid, $vid)
	{
		$data = self::getDb()->getNumDataPrepare(
				'select time from notice where uid=? and vid=?', 
				array( 1 => array($uid, PDO::PARAM_INT), 2 => array($vid, PDO::PARAM_INT)), 
				true);
		return $data['num'] != 1 ? 0 : $data['data']['time'];
	}
	
	/**
	 * @param int $uid
	 * @param int $limit = null use default config
	 * @return boolean - true full - false not full
	 */
	public static function isNoticeCountFull($uid, $limit = null)
	{
		if (null === $limit) {
			$cfg = Lamb_Registry::get(CONFIG);
			$limit = $cfg['member_notice_num'];
			unset($cfg);
		}
		$limit = (int)$limit;
		
		return self::getNoticeCount($uid) >= $limit;
	}
	
	/**
	 * @param int $uid
	 * @parma int $vid
	 * @return int
	 */
	public static function removeNotice($uid, $vid)
	{
		return self::getDb()->quickPrepare(
				'delete from notice where uid=? and vid=?',
				array( 1 => array($uid, PDO::PARAM_INT), 2 => array($vid, PDO::PARAM_INT))
				, true);
	}
	
	/**
	 * @param int $uid
	 * @return int
	 */
	public static function getNoticeCount($uid)
	{
		return self::getDb()->getPrepareRowCount(
						'select count(vid) as num from notice where uid=?',
						array( 1 => array($uid, PDO::PARAM_INT)));
	}
	
	/**
	 * @param int $uid
	 * @return array
	 */
	public static function getUpdateVideoInfos($uid, &$isHasNew = true)
	{
		$cache = self::getCache($uid);
		if ($cache->isCached()) {
			$data = unserialize($cache->read());
			$isHasNew = false;
		} else {
			$sql = 'select id,name,mark,updateDate,vedioPic from vedio a,notice b where id=vid and uid=? and updateDate > time';
			$db = Lamb_App::getGlobalApp()->getDb();
			$data = $db->quickPrepare($sql, array( 1 => array($uid, PDO::PARAM_INT)))->toArray();
			$sql_up = 'update notice set time=? where uid=? and vid=?';
			$aPrepareSource = array();

			foreach ($data as $item) {
				$aPrepareSource[1] = array($item['updateDate'], PDO::PARAM_INT);
				$aPrepareSource[2] = array($uid, PDO::PARAM_INT);
				$aPrepareSource[3] = array($item['id'], PDO::PARAM_INT);
				$db->quickPrepare($sql_up, $aPrepareSource, true);
			}
			$cache->write(serialize($data));
			$isHasNew = true;
		}
		unset($isHasNew);
		return $data;
	}
	
	/**
	 * @return Lamb_Db_Abstract
	 */
	public static function getDb()
	{
		return Lamb_App::getGlobalApp()->getDb();
	}
}