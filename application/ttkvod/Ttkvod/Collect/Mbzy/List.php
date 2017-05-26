<?php
class Ttkvod_Collect_Mbzy_List extends Ttkvod_Collect_Abstract implements Ttkvod_Collect_ListInterface
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
		return "http://www.mbzy.cc/list/?0-{$page}.html";
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
		
		if (!preg_match_all('/<tr><td colspan="5".*?align="left"><a.*?\[(.*?)\].*?href="(.*?)".*?<tr><td colspan="5" bgcolor="#999999"><\/td><\/tr>/is', $html, $result, PREG_SET_ORDER)) {
			$error = self::E_RULE_NOT_MATCH;
			return $ret;
		}
		
		foreach ($result as $item) {
			if(trim($item[1]) == 'Ô¤¸æÆ¬'){
				continue;
			}
			$ret[] = array(
				'url' => 'http://www.mbzy.cc' . trim($item[2]), 
				'externls' => array(
						'flag' => trim($item[1])
					)
				);
		}
		
		$error = self::S_OK;
		return $ret;
	}
}