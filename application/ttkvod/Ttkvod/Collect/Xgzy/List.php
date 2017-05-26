<?php
class Ttkvod_Collect_Xgzy_List extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ListInterface
{
	protected $mTypeid;
	
	public function __construct()
	{

	}
	
	public function setTypeid($typeid = null)
	{
		if (null == $typeid) {
			return $this->mTypeid;
		}
		
		$this->mTypeid = (int)$typeid;
		return $this;
	}
	
	/**
	 * @Ttkvod_Collect_ListInterface implemtions
	 */
	public function getUrl($page = 1)
	{
		return "http://www.myzyzy.com/?page={$page}";
	}

	/**
	 * @Ttkvod_Collect_ListInterface implemtions
	 */	
	public function collect($url, $externals = null, &$error = null)
	{
		$ret = array();
		
		if ( !($html = Ttkvod_Utils::fetchContentByUrlH($url))) {
			$error = self::E_NET_FAIL;
			return $ret;
		}
		
		if (!preg_match_all('/class="DianDian".*?href="(.*?)"/is', $html, $result, PREG_SET_ORDER)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;
		}
		
		foreach ($result as $item) {
			$ret[] = array(
						'url' => 'http://www.myzyzy.com' . trim($item[1]), 
						'externls' => array()
						);
		}
		
		$error = self::S_OK;
		return $ret;
	}
}