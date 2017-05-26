<?php
/**
 * @author 1961299765
 */
class indexControllor extends Ttkvod_ManageControllor
{
	public $typeArr = array(
		1 => '电影',
		2 => '电视剧',
		3 => '综艺',
		4 => '动漫'
	);
	public $webArr = array(
		'life' => 'minilife',
		'game' => 'minigame',
		'lady' => 'minilady',
		'new'  => 'mininew',
		'index' => 'mini_index',
		'text' => 'minitext',
		'img'  => 'miniimg',
		'shenqi' => 'minishenqi'
	);

	public function indexAction()
	{
		$isAdmin = '';
		$this->checkPurview();
		$isAdmin = $this->checkIsAdmin() ? 'true' : 'false';
		include $this->load('index');
	}
	
	public function getControllorName()
	{
		return 'index';
	}		
	
	public function loginAction()
	{
		if ($this->mRequest->isPost()) {
			$username = trim($this->mRequest->getPost('username', ''));
			$password = trim($this->mRequest->getPost('password', ''));
			$safecode = trim($this->mRequest->getPost('safeCode', ''));
			
			if (empty($safecode) || strtolower($safecode) != strtolower($_SESSION['randval'])) {
				$this->showMsg(array('msg' => '验证码错误', 'url' => $this->mRefer));
			}
			if (empty($username) || empty($password) || !$this->isAccountCanLogin($username, md5($password))) {
				$this->showMsg(array('msg' => '帐号或密码错误', 'url' => $this->mRefer));
			}
			
			$_SESSION[$this->mSessionKeyUsername] = $username;
			$_SESSION[$this->mSessionKeyPassword] = md5($password);
			$admin = new Ttkvod_Model_Admin();
			$admin->update(array(
				'name' => $username,
				'lasttime' => time(),
				'lastip' => Lamb_App::getGlobalApp()->getRequest()->getClientIp()
			));
			$this->mResponse->setcookie(session_name(), session_id(), 7*24*3600);
			$this->mResponse->redirect($this->mRouter->urlEx('index', 'index'));
		} 
		include $this->load('index_login');
	}
	
	public function codeAction()
	{
		$c_check_code_image = new Ttkvod_CodeFile();
		$c_check_code_image ->OutCheckImage();	
	}

	public function miniConfigAction()
	{
		if ($this->mRequest->isPost()) {
			$data = $this->mRequest->getPost();
			$miniData = array();
			foreach ($data as $dataItem){
				foreach ($dataItem as $miniKey => $miniItem) {
					$miniData[$miniKey] = array();
					$keys = array_keys($miniItem);
					$len = count($miniItem[$keys[0]]);
					
					for ($i = 0; $i < $len; $i++) {
						$temp = array();
						foreach ($miniItem as $_key => $_item) {
							$temp[$_key] = $_item[$i];
						}
						$miniData[$miniKey][] = $temp;
					}
				}
			}
			$data = "<?php\nreturn " . var_export($miniData, true) . ';';
			Lamb_IO_File::putContents(DATA_PATH . 'config.mini.var.php', $data);
			$this->showMsg(array('msg' => '修改成功', 'url' => $this->mRefer, 'level' => ''));		 	
		} else {
			$miniData = include DATA_PATH . 'config.mini.var.php';
			include $this->load('index_mini');
		}
	}
	
	public function miniEditAction()
	{
		$type = trim($this->mRequest->t);
		$strHtml = $this->mRequest->getPost('h');
		if (!array_key_exists($type, $this->webArr)) {
			$type = 'lady';
		} 
		if ($strHtml != '') {
			$bool = file_put_contents($this->mTtkvodClientPath . '/mini/' . $this->webArr[$type] . '.html', $strHtml);
			Ttkvod_Utils::flushCDN(array('http://' . $this->mSiteCfg['cdn_host'] . '/mini/' . $this->webArr[$type] . '.html'));	
			$this->showMsg(array('msg' => '修改成功', 'url' => $this->mRefer, 'level' => ''));	
		}
		$html = file_get_contents($this->mTtkvodClientPath . '/mini/' . $this->webArr[$type] . '.html');
		$modeUrl = $this->mRouter->urlEx('index', 'miniEdit') . '/t/';
		$actionUrl = $modeUrl . $type;
		include $this->load('index_edit');
	}
	
	public function configAction()
	{
		$this->checkPurview();
		if ($this->mRequest->isPost()) {
			$cfg = $this->mRequest->getPost('cfg', null, false);
			if (!isset($cfg['site_mode']) || $cfg['site_mode'] != 1) {
				$cfg['site_mode'] = 2;
			}

			$cfg['static_cfg']['sync']['type'] = (int)$cfg['static_cfg']['sync']['type'];
			
			if ($cfg['static_cfg']['sync']['type'] == 4) {
				$cfg['static_cfg']['sync']['ftp_port'] = (int)$cfg['static_cfg']['sync']['ftp_port'];
			}
			
			$cfg['comment']['pagesize'] = (int)$cfg['comment']['pagesize'];
			$cfg['comment']['cita_pagesize'] = (int)$cfg['comment']['cita_pagesize'];
			$cfg['comment']['max_cita_count'] = (int)$cfg['comment']['max_cita_count'];
			$cfg['comment']['max_display_contentlen'] = (int)$cfg['comment']['max_display_contentlen'];
			$cfg['comment']['max_content_len'] = (int)$cfg['comment']['max_content_len'];
			$cfg['comment']['submit_interval_mill'] = (int)$cfg['comment']['submit_interval_mill'];
			$cfg['comment']['support_interval_sec'] = (int)$cfg['comment']['support_interval_sec'];
			$cfg['static_cfg']['sync']['http_is_post'] = $cfg['static_cfg']['sync']['http_is_post'] ? 1 : 0;
			$cfg['hot_search_keywords'] = preg_split('/,|\s/i', $cfg['hot_search_keywords']);
			
			if (!isset($cfg['is_url_encode']) || $cfg['is_url_encode'] != 1) {
				$cfg['is_url_encode'] = 0;
			}
			
			if (!isset($cfg['notice_interval_sec']) || !Lamb_Utils::isInt($cfg['notice_interval_sec'], true)) {
				$cfg['notice_interval_sec'] = 0;
			}
			$cfg['notice_interval_sec'] = (int)$cfg['notice_interval_sec'];

			$adMenu = array();
			foreach (explode("\r\n", $cfg['index_menu']) as $menuItem) {
				if (!$menuItem) {continue;}
				$menuItem = explode("|", $menuItem);
				$adMenu[] = array(
					'name' => iconv('gbk', 'utf-8', $menuItem[0]),
					'link' => $menuItem[1],
					'new_window' => isset($menuItem[2]) ? $menuItem[2] : 0
				);
			}
			
			$jsConfig = 'var g_aCfg = ' . json_encode(array(
				'comment' => array(
					'pagesize' => $cfg['comment']['pagesize'],
					'cita_pagesize' => $cfg['comment']['cita_pagesize'],
					'max_cita_count' => $cfg['comment']['max_cita_count'],
					'max_display_contentlen' => $cfg['comment']['max_display_contentlen'],
					'max_content_len' => $cfg['comment']['max_content_len'],
					'submit_interval_mill' => $cfg['comment']['submit_interval_mill'],
					'support_interval_sec' => $cfg['comment']['support_interval_sec'],
					'close_video_ids' => $cfg['comment']['close_video_ids']	
				),
				'router' => array(
					'host_map' => $cfg['model_router_hostname'],
					'url_delimiter' => $cfg['url_delimiter'],
					'url_param_name' => $cfg['url_param_name']
				),
				'static_file_extendtion' => $cfg['static_cfg']['extendtion'],
				'enable_auto_router' => isset($cfg['enable_auto_router']) && $cfg['enable_auto_router'] ? 1 : 0,
				'is_url_encode' => $cfg['is_url_encode'],
				'img_host' => $cfg['img_host'],
				'blank_img_path' => $cfg['site_root'] . $cfg['blank_img_path'],
				'notice_interval_sec' => $cfg['notice_interval_sec'],
				'site_mode' => $cfg['site_mode'],
				'domain' => $cfg['domain'],
				'ad_menu' => $adMenu
			));
			
			unset($cfg['enable_auto_router']);
			
			$data = "<?php\nreturn " . var_export($cfg, true) . ';';
			Lamb_IO_File::putContents(DATA_PATH . 'config.var.php', $data);
			Lamb_IO_File::putContents($this->mJsConfigPath, $jsConfig);
			Ttkvod_Utils::flushCDN(array('http://cdn.ttkvod.com/api/config.js'));
			$client = new Ttkvod_OutServices_Client();
			foreach (array(
					array('item', 'http://cache1.ttkvod.com:8089/index.php'), 
					array('item', 'http://member2.ttkvod.com:8089/index.php'), 
					array('search', 'http://42.121.193.165:8089/index.php'),
					array('search', 'http://121.199.32.222:8089/index.php')
					) as $info) {
				$client->runFromRemote(array(
							'server_url' => $info[1],
							'key' => $this->mSiteCfg['out_services_hash']['ttkitem']['key'],
							'expire' => $this->mSiteCfg['out_services_hash']['ttkitem']['expire'],
							'clientid' => $this->mSiteCfg['out_services_hash']['ttkitem']['id'],
							'controllor' => $info[0],
							'action' => 'server',
							'sync' => false,
							'post' => true
						), 'sysConfig', 'data=' . urlencode($data), $isOk);
			}	
			$this->showMsg(array('msg' => '修改成功', 'url' => $this->mRefer, 'level' => ''));
		} else {
			include $this->load('index_config');
		}
	}
	
	public function loginoutAction()
	{
		unset($_SESSION[$this->mSessionKeyUsername], $_SESSION[$this->mSessionKeyPassword]);
		$this->showMsg(array('msg' => '退出成功', 'url' => $this->mRouter->urlEx('index', 'login')));
	}
	
	public function flushcacheAction()
	{
		Ttkvod_Utils::flushCache();
		$this->showMsg(array('msg' => '缓存清除成功', 'url' => $this->mRouter->urlEx('index', 'index')));
	}
	
	public function loadjsconfigAction()
	{
		header('Content-Type:application/x-javascript');
		$this->mResponse->eecho(file_get_contents($this->mJsConfigPath));
	}
	
	public function createindexAction()
	{
		file_get_contents($this->getClientUrl('looper', 'createhtml', array('ac' => 'index')));
		$this->showMsg(array('msg' => '生成成功', 'url' => $this->mRouter->urlEx('index', 'index')));
	}
}




