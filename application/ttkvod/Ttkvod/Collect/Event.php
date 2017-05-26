<?php
class Ttkvod_Collect_Event 
{
	protected $mIsItemFirstRun = true;
	
	protected $mSiteCfg;

	protected $mApp;
	
	public function __construct()
	{
		$cfg = Lamb_Registry::get(CONFIG);
		$this->mSiteCfg = $cfg;
		$this->mApp = Lamb_App::getGlobalApp();
	}
	
	public function onItemBeginEvent($rserve = null, $param)
	{
		$listdata = $param[1];
		$msg = "内容页：<b style='color:green'>{$listdata['url']}</b>采集开始<br/>";
		echo $msg;
		$this->writeLog($msg);
	}
	
	public function onItemEndEvent($rserve = null, $param)
	{ 
		$msg = '';
		$error = $param[1];
		$itemdata = $param[2];
		$listdata = $param[3];
		if ($error == Ttkvod_Collect_Interface::S_OK) {
			$msg = "影片：<b style='color:green'>{$itemdata['name']}</b> 采集成功<br/>";
			echo $msg;
			$ret = Ttkvod_Collect_Looper::writeToDB($itemdata, $ext);
			$msg .= $ret == 0 ? '插入失败' : ($ret == 1 ? '锁定' : ($ret == 2 ? '修改成功' : '插入成功'));
			$msg .= "\r\n ext : $ext";
			$msg .= "\r\n" . print_r($itemdata, true);
			if ($ret == 2) {
				Lamb_IO_File::putContents($this->mSiteCfg['video_sys_path'], $ext . ',', FILE_APPEND);
			} else if ($ret == 3 && $ext && $ext != $this->mSiteCfg['nopic_path']){
				$this->writeImgListItem($itemdata['vedioPic'], $ext);
			}
			if ($this->mIsItemFirstRun && $param[0]->getCurrentPage() == 1) {
				$this->mIsItemFirstRun = false;
				Lamb_IO_File::putContents($this->mSiteCfg['auto_temp_path'], $itemdata['updateDate']);
			}
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_NET_FAIL) {
			$msg = "内容页：<b style='color:red'>{$listdata['url']}</b> 由于<b>网络原因</b>采集失败<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_RULE_NOT_MATCH) {
			$msg = "内容页：<b style='color:red'>{$listdata['url']}</b> 由于<b>采集规则不匹配</b>采集失败<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_TYPE_FORBIN_COLLECT) {
			$msg = "内容页：<b style='color:red'>{$listdata['url']}</b> 由于<b>分类不匹配</b>采集失败<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_COLLECT_PLAYDATA_FAIL) {
			$msg = "内容页：<b style='color:red'>{$listdata['url']}</b> 由于<b>无法采集播放种子</b>采集失败<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_COLLECT_NAME_FAIL) {
			$msg = "内容页：<b style='color:red'>{$listdata['url']}</b> 由于<b>无法采集影片名称</b>采集失败<br/>";
			echo $msg;
		}
		$this->writeLog($msg);
	}
	
	public function onListEvent($event, $param)
	{
		$page = $param[1];
		$msg = '';
		if ($event == Ttkvod_Collect_Looper::EVENT_LIST_BEGIN) {
			$msg = "列表页第<b>$page</b>页采集开始<br/>";
		} else {
			$msg = "列表页第<b>$page</b>页采集结束";
		}
		echo $msg;
		$this->writeLog($msg);
	}
	
	public function onTaskEnd($rserve = null, $param)
	{
		$target = $param[0];
		$break = $param[1];
		$breakinfo = $param[2];
		$msg = '';
		$condition = $target->setOrGetCondition();
		switch ($break) {
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_PAGE_BREAK:
				$msg = "列表的当前{$breakinfo}，超过了指定的{$condition['val']}页，采集终止";
				break;
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_NUM_BREAK:
				$msg = "采集的当前数目{$breakinfo}，超过了指定的{$condition['val']}数目，采集终止";
				break;
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_LIST_NET_ERROR:
				$msg = "列表第{$breakinfo}页，由于网络原因无法获取数据，采集终止";
				break;
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_LIST_RULE_ERROR:
				$msg = "列表第{$breakinfo}页，由于采集规则不匹配，采集终止";
				break;		
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_DATE_BREAK:
				$msg = "当前影片的日期：" . date('Y-m-d H:i:s', $breakinfo) . "，超过了指定的日期：" . date('Y-m-d H:i:s', $condition['val']) . "，采集终止";
				break;
		}
		echo $msg;
		if (($newupdate = Lamb_IO_File::getContents($this->mSiteCfg['auto_temp_path'])) && Lamb_Utils::isInt($newupdate, true)) {
			Lamb_IO_File::putContents($this->mSiteCfg['auto_path'], $newupdate);
			Lamb_IO_File::putContents($this->mSiteCfg['auto_temp_path'], '');
		}
		$this->writeLog($msg);
		$this->videoSys();
		$this->sysImgList();		
	}
	
	public function sysImgList()
	{
		$path = $this->mSiteCfg['img_sys_list_path'];
		$strContent = trim(Lamb_IO_File::getContents($path));
		if ($strContent && $ret = Ttkvod_Http::quickPost($this->mSiteCfg['img_sys_url'], array('urls' => $strContent), 10, true, $status)) {
			Lamb_IO_File::putContents($path, '');
		}
	}

	public function writeImgListItem($srcurl, $targetpath)
	{
		if (!Lamb_Utils::isHttp($srcurl)) {
			$srcurl = 'http://' . $this->mApp->getRequest()->getHttpHost() . $srcurl;
		}
		Lamb_IO_File::putContents($this->mSiteCfg['img_sys_list_path'], $srcurl . '|$|' . $targetpath, FILE_APPEND);
		return $this;
	}
	
	public function videoSys()
	{
		$path = $this->mSiteCfg['video_sys_path'];
		if($strContent = trim(Lamb_IO_File::getContents($path))) {
		$client = new Ttkvod_OutServices_Client();
			$ret = $client->runFromRemote(array(
						'server_url' => $this->mSiteCfg['video_sys_url'],
						'key' => $this->mSiteCfg['client']['key'],
						'expire' => $this->mSiteCfg['client']['expire'],
						'clientid' => $this->mSiteCfg['client']['id'],
						'controllor' => 'index',
						'action' => 'server',
						'sync' => false,
					), 'clearNoticeCache', 'ids=' . $strContent, $isOk);	
			if ($isOk) {
				Lamb_IO_File::putContents($path, '');
			}
		}
	}
	
	public function writeLog($str)
	{
		$path = $this->mSiteCfg['log_path'] . date('Ymd') . '.txt';
		Lamb_IO_File::putContents($path, $str . "\r\n", FILE_APPEND);
	}
	
	public function loadAutoDate()
	{
		$val = Lamb_IO_File::getContents($this->mSiteCfg['auto_path']);
		if (!$val || !Lamb_Utils::isInt($val, true)) {
			$val = strtotime(date('Y-m-d'));
		}
		return $val;
	}
}