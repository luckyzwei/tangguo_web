<?php
class  Ttkvod_Proxy_Baidu_Abstract extends  Ttkvod_Proxy_Abstract
{
	/**
	 * @var array
	 */
	protected static $sTypeKeyMap = array(
				1 => 'movie',
				2 => 'tv',
				3 => 'show',
				4 => 'comic'
			);
			
	/**
	 * @var int
	 */
	protected $mTypeid;
	
	public function __construct()
	{
	
	}
	
	/**
	 * @param int $typeid
	 * @return Tmovie_Collect_Le123_Abstract
	 */
	public function setTypeid($typeid)
	{
		if ( (Lamb_Utils::isInt($typeid, true) && array_key_exists($typeid, self::$sTypeKeyMap)) ||
				($typeid = array_search($typeid, self::$sTypeKeyMap)) !== false ) {
			$this->mTypeid = (int)$typeid;
		}
		return $this;
	}
			
	/**
	 * @return string
	 * @throws Lamb_Exception
	 */
	protected function getTypeKey()
	{
		if (!isset($this->mTypeid) || !isset(self::$sTypeKeyMap[$this->mTypeid])) {
			throw new Lamb_Exception("Can't found type key ");
			return '';
		}
		return self::$sTypeKeyMap[$this->mTypeid];
	}

	/**
	 * @param string $url
	 * @return int
	 * @throws Lamb_Exception
	 */	
	public function getTypeidFromUrl($url)
	{
		$typeid = -1;
		$arr = explode('/', substr($url, 7));
		
		if (!isset($arr[1])) {
			throw new Lamb_Exception("Can't get typeid from url \"$url\"");
		}
		
		$arr = explode('_', $arr[1]);
		if (!isset($arr[0]) || ($typeid = array_search($arr[0], self::$sTypeKeyMap)) === false)	{
			throw new Lamb_Exception("Can't get typeid from url \"$url\"");
		}
		
		return $typeid;				
	}
}