<?php
abstract class Tmovie_Collect_AbstractEvent 
{
	protected $mIsItemFirstRun = true;
	
	protected $mSiteCfg;
	
	protected $mApp;
	
	public function __construct()
	{
		$this->mApp = Lamb_App::getGlobalApp();
		$this->mSiteCfg = $this->mApp->getCfg();
		$this->mSiteCfg = $this->mSiteCfg['collect'];
	}
	
	public function onItemBeginEvent($rserve = null, $param)
	{
		$listdata = $param[1];
		$msg = "����ҳ��<b style='color:green'>{$listdata['url']}</b>�ɼ���ʼ<br/>";
		echo $msg;
		$this->writeLog($msg);
	}
	
	public function onListEvent($event, $param)
	{
		$page = $param[1];
		$msg = '';
		if ($event == Tmovie_Collect_Looper::EVENT_LIST_BEGIN) {
			$msg = "�б�ҳ��<b>$page</b>ҳ�ɼ���ʼ<br/>";
		} else {
			$msg = "�б�ҳ��<b>$page</b>ҳ�ɼ�����";
		}
		echo $msg;
		$this->writeLog($msg);
	}
	
	public function writeImgListItem($targetpath, $src = null)
	{
		$cfg = $this->mSiteCfg;
		if ($src && !Lamb_Utils::isHttp($src)) {
			if (!($host =  $cfg['img_host'])) {
				$host = 'http://' . $this->mApp->getRequest()->getHttpHost();
			}
			$host .= '/' . str_replace(ROOT, '', $cfg['pic_path']);
			$src = $host . $src;
		} 
		
		if ($src) {
			$targetpath .= "[$]{$src}";
		}
		
		Lamb_IO_File::putContents($cfg['img_sys_path'], $targetpath .  "\r\n", FILE_APPEND);
		return $this;
	}
	
	public function writeVideoSysItem($items)
	{
		Lamb_IO_File::putContents($this->mSiteCfg['video_sys_path'], $items . ',', FILE_APPEND);
		return $this;
	}
	
	public function videoSys()
	{
		$cfg = $this->mSiteCfg;
		$path = $cfg['video_sys_path'];
		if($strContent = trim(Lamb_IO_File::getContents($path))) {
			$client = new Tmovie_OutServices_Client();
			$ret = $client->runFromRemote(array(
						'server_url' => $cfg['video_sys_url'],
						'key' => $cfg['client']['key'],
						'expire' => $cfg['client']['expire'],
						'clientid' => $cfg['client']['id'],
						'controllor' => 'index',
						'action' => 'server',
						'sync' => false,
					), 'clearNoticeCache', 'ids=' . $strContent, $isOk);	
			if ($isOk) {
				Lamb_IO_File::putContents($path, '');
			}
		}
		return $this;
	}
	
	public function writeLog($str)
	{return ;
		$path = $this->mSiteCfg['log_path'] . date('Ymd') . '.txt';
		Lamb_IO_File::putContents($path, $str . "\r\n", FILE_APPEND);
		return $this;
	}
	
	
	/**
	 * @param array $tasks = array( array(name, src), ....);
	 * @return boolean
	 */
	public function downImg(array $tasks, $refer = '')
	{
		$ret = false;
		$cfg = $this->mSiteCfg;
		$imgSys = array();
		
		if ($data = trim(Lamb_IO_File::getContents($cfg['down_imgs_path']))) {
			$tasks += explode("\r\n", $data);
			Lamb_IO_File::putContents($cfg['down_imgs_path'], '');
		}
		
		foreach ($tasks as $key => $task) {
			if (($pos = strpos($task, '[$]')) !== false) {
				$filename = substr($task, 0, $pos);
				$url = substr($task, $pos + 3);
				
				if (($data = Tmovie_Http::request(array('url' => $url, 'refer' => $refer), $status)) && $status == 200) {
					if (Lamb_IO_File::putContents($cfg['pic_path'] . $filename, $data)) {
						$imgSys[] = $task;
						unset($tasks[$key]);
					}
				}
			}
		}
		
		if (count($tasks) > 0) {
			Lamb_IO_File::putContents($cfg['down_imgs_path'], implode("\r\n", $tasks) . "\r\n");
		}

		if (count($imgSys) > 0) {
			$ret = true;
			$this->writeImgListItem(implode("\r\n", $imgSys));
			$this->imgSys();
		}		
		
		return $ret;
	}
	
	public  function imgSys()
	{return;
		$ret = false;
		$cfg = $this->mSiteCfg;
		$path = $cfg['img_sys_list_path'];
		$strContent = trim(Lamb_IO_File::getContents($path));
		if ($strContent && $ret = Tmovie_Http::quickPost($cfg['img_sys_url'], array('urls' => $strContent), 10, true, $status)) {
			Lamb_IO_File::putContents($path, '');
			$ret = true;
		}
		return $ret;
	}
	
	public function onTaskEnd($rserve = null, $param)
	{
		$target = $param[0];
		$break = $param[1];
		$breakinfo = $param[2];
		$msg = '';
		$condition = $target->setOrGetCondition();
		switch ($break) {
			case Tmovie_Collect_Looper::EVENT_END_VALUE_PAGE_BREAK:
				$msg = "�б�ĵ�ǰ{$breakinfo}��������ָ����{$condition['val']}ҳ���ɼ���ֹ";
				break;
			case Tmovie_Collect_Looper::EVENT_END_VALUE_NUM_BREAK:
				$msg = "�ɼ��ĵ�ǰ��Ŀ{$breakinfo}��������ָ����{$condition['val']}��Ŀ���ɼ���ֹ";
				break;
			case Tmovie_Collect_Looper::EVENT_END_VALUE_LIST_NET_ERROR:
				$msg = "�б��{$breakinfo}ҳ����������ԭ���޷���ȡ���ݣ��ɼ���ֹ";
				break;
			case Tmovie_Collect_Looper::EVENT_END_VALUE_LIST_RULE_ERROR:
				$msg = "�б��{$breakinfo}ҳ�����ڲɼ�����ƥ�䣬�ɼ���ֹ";
				break;		
			case Tmovie_Collect_Looper::EVENT_END_VALUE_DATE_BREAK:
				$msg = "��ǰӰƬ�����ڣ�" . date('Y-m-d H:i:s', $breakinfo) . "��������ָ�������ڣ�" . date('Y-m-d H:i:s', $condition['val']) . "���ɼ���ֹ";
				break;
		}
		echo $msg;
		$this->writeLog($msg);				
	}	
	
	abstract public function onItemEndEvent($rserve = null, $param);
}