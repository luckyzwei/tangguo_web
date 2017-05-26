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
		$msg = "����ҳ��<b style='color:green'>{$listdata['url']}</b>�ɼ���ʼ<br/>";
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
			$msg = "ӰƬ��<b style='color:green'>{$itemdata['name']}</b> �ɼ��ɹ�<br/>";
			echo $msg;
			$ret = Ttkvod_Collect_Looper::writeToDB($itemdata, $ext);
			$msg .= $ret == 0 ? '����ʧ��' : ($ret == 1 ? '����' : ($ret == 2 ? '�޸ĳɹ�' : '����ɹ�'));
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
			$msg = "����ҳ��<b style='color:red'>{$listdata['url']}</b> ����<b>����ԭ��</b>�ɼ�ʧ��<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_RULE_NOT_MATCH) {
			$msg = "����ҳ��<b style='color:red'>{$listdata['url']}</b> ����<b>�ɼ�����ƥ��</b>�ɼ�ʧ��<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_TYPE_FORBIN_COLLECT) {
			$msg = "����ҳ��<b style='color:red'>{$listdata['url']}</b> ����<b>���಻ƥ��</b>�ɼ�ʧ��<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_COLLECT_PLAYDATA_FAIL) {
			$msg = "����ҳ��<b style='color:red'>{$listdata['url']}</b> ����<b>�޷��ɼ���������</b>�ɼ�ʧ��<br/>";
			echo $msg;
		} else if ($error == Ttkvod_Collect_Hakuzy_Item::E_COLLECT_NAME_FAIL) {
			$msg = "����ҳ��<b style='color:red'>{$listdata['url']}</b> ����<b>�޷��ɼ�ӰƬ����</b>�ɼ�ʧ��<br/>";
			echo $msg;
		}
		$this->writeLog($msg);
	}
	
	public function onListEvent($event, $param)
	{
		$page = $param[1];
		$msg = '';
		if ($event == Ttkvod_Collect_Looper::EVENT_LIST_BEGIN) {
			$msg = "�б�ҳ��<b>$page</b>ҳ�ɼ���ʼ<br/>";
		} else {
			$msg = "�б�ҳ��<b>$page</b>ҳ�ɼ�����";
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
				$msg = "�б�ĵ�ǰ{$breakinfo}��������ָ����{$condition['val']}ҳ���ɼ���ֹ";
				break;
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_NUM_BREAK:
				$msg = "�ɼ��ĵ�ǰ��Ŀ{$breakinfo}��������ָ����{$condition['val']}��Ŀ���ɼ���ֹ";
				break;
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_LIST_NET_ERROR:
				$msg = "�б��{$breakinfo}ҳ����������ԭ���޷���ȡ���ݣ��ɼ���ֹ";
				break;
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_LIST_RULE_ERROR:
				$msg = "�б��{$breakinfo}ҳ�����ڲɼ�����ƥ�䣬�ɼ���ֹ";
				break;		
			case Ttkvod_Collect_Looper::EVENT_END_VALUE_DATE_BREAK:
				$msg = "��ǰӰƬ�����ڣ�" . date('Y-m-d H:i:s', $breakinfo) . "��������ָ�������ڣ�" . date('Y-m-d H:i:s', $condition['val']) . "���ɼ���ֹ";
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