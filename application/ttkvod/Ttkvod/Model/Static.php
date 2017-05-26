<?php
class Ttkvod_Model_Static
{
	const T_INDEX = 1;
	
	const T_TOP = 2;
	
	const T_ITEM = 4;
	
	const T_LIST = 8;
		
	/**
	 * @var array
	 */
	protected $mCfg;
	
	protected $mLinkRouter;
	
	protected $mDispatcher;
	
	protected $mApp;
	
	protected $mRequest;
	
	/**
	 * @var array
	 */
	protected $mItemLooperParam = array();
	
	/**
	 * @var array
	 */
	protected $mListLooperParam = array();

	/**
	 * @var array
	 */	
	protected $mItemCDNUrls = array();
	
	/**
	 * @var array
	 */	
	protected $mListCDNUrls = array();	
	
	/**
	 * @var string
	 */
	protected $mCDNHost;
	
	/**
	 * @var string
	 */
	protected $mTaskUrl = '';
	
	/**
	 * @var int
	 */
	protected $mCurrentTask;
	
	/**
	 * @var array
	 */
	protected $mTasks = array();
	
	public function __construct()
	{
		$this->mCfg = Lamb_Registry::get(CONFIG);
		$this->mLinkRouter = Ttkvod_Model_LinkRouter::getSingleInstance();
		$this->mLinkRouter->setModelLinks('item', new Ttkvod_Model_LinkRouter_Item)
						  ->setModelLinks('list', new Ttkvod_Model_LinkRouter_List)
						  ->setModelLinks('', new Ttkvod_Model_LinkRouter_Default);	
		$this->mApp = Lamb_App::getGlobalApp();
		$this->mDispatcher = $this->mApp->getDispatcher();
		$this->mRequest = $this->mApp->getRequest();	
		$this->mCDNHost = 'http://' . $this->mCfg['cdn_host'] . '/';
	}
	
	/**
	 * @param int $time	
	 * @return Ttkvod_Model_Static
	 */
	public function createIndex($time = 2)
	{
		$path = $this->router('', array('id' => 'index'));
		$this->core($this->loadControllor('index', 'index'), 'index', $path);
		//Ttkvod_Utils::flushCDN(array($this->mCDNHost . $path . '?3.0.0.0'));

		$this->endEcho(self::T_INDEX, $time);
		return $this;
	}

	/**
	 * @param int $time
	 * @return Ttkvod_Model_Static
	 */	
	public function createTop($time = 2)
	{
		$action = 'top';	
		$controllor = $this->loadControllor('index', $action);
		$cdnUrls = array();
		//ob_end_clean();
		$router_path =  $this->router('', array('id' => 'top'));
		$this->core($controllor, $action, $router_path);
		$cdnUrls[] = $this->mCDNHost . $router_path;
		for ($vid = 1; $vid <= 4; $vid ++) {
			$router_path = $this->router('', array('vid' => $vid, 'id' => 'top'));
			$this->core($controllor, $action, $router_path, array('vid' => $vid));
			$cdnUrls[] = $this->mCDNHost . $router_path;
		}
		Ttkvod_Utils::flushCDN($cdnUrls);
		$this->endEcho(self::T_TOP, $time);
		return $this;
	}
	
	/**
	 * @return Ttkvod_Model_Static
	 */	
	public function createItem($id)
	{
		$action = 'index';
		$controllor = $this->loadControllor('item', $action);
		//ob_end_clean();
		$router_path = $this->router('item', array('id' => $id));
		$this->core($controllor, $action, $router_path, array('id' => $id));
		$this->mItemCDNUrls[] = $this->mCDNHost . $router_path;
		return $this;
	}
	
	/**
	 * @return Ttkvod_Model_Static
	 */	
	public function createList($id, $p = null, $tag = null)
	{
		$action = 'index';
		$controllor = $this->loadControllor('list', $action);
		//ob_end_clean();
		$param = array('id' => $id);
		if (null !== $p) {
			$param['p'] = $p;
		}
		if (null !== $tag) {
			$param['tag'] = $tag;
		}
		$router_path = $this->router('list', $param);
		//Lamb_Debuger::debug($router_path); //vodlist-1/p-1.html
		
		if ($p==1) {
			$router_path_index = preg_replace('/vod-show-(\d+)\/p-1\.html/i', 'vod-show-${1}/index.html', $router_path);
			$this->core($controllor, $action, $router_path_index, $param);
		}
		$this->core($controllor, $action, $router_path, $param);
		$this->mListCDNUrls[] = $this->mCDNHost . $router_path;
		return $this;
	}
	
	/**
	 * @param array $condition = array(
	 *		'id' => int,
	 *		'updateDate' => (array(0) >=, array(0, 1) >=0 && <=1 ),
	 *		'limit' => int
	 *	)
	 * @return int
	 */
	public function createItemLooper(array $condition = array())
	{
		$sql = 'select id,name from vedio where 1=1';
		
		if (isset($condition['id']) && Lamb_Utils::isInt($condition['id'], true)) {
			$sql .= ' and toptype=' . $condition['id'];
		}
		
		if (isset($condition['updateDate'])) {
			$updateDate = $condition['updateDate'];
			if (is_array($updateDate)) {
				if (count($updateDate) == 1) {
					$sql .= ' and updateDate>=' . $updateDate[0];
				} else if (count($updateDate) == 2) {
					$sql .= ' and updateDate>=' . $updateDate[0] . ' and updateDate<=' . $updateDate[1];
				}
			}
		}
		$this->mItemLooperParam['sql'] = $sql;
		if (isset($condition['limit']) && Lamb_Utils::isInt($condition['limit'], true)) {
			$this->mItemLooperParam['limit'] = $condition['limit'];
			$sql = $this->mApp->getSqlHelper()->getLimitSql($sql, $condition['limit']);
		}
		$num = $this->mApp->getDb()->getNumData($sql);		
		if ($num == 0) {
			return 0;
		}
		return $num;
	}
	
	/**
	 * @param array $condition = array(
	 *		'id' => int, 'tag' => int, 'limit' => int
	 * )
	 * @return int	 
	 */
	public function createListLooper(array $condition = array())
	{
		$ctr = $this->mDispatcher->loadControllor('listControllor', true);
		$listPagesize = $ctr->getListPagesize();
		$searchIndexs = $ctr->getSearchIndex();
		$aPrepareSource = array();
		unset($ctr);
		if (!array_key_exists($condition['id'], $this->mCfg['channels'])) {
			return 0;
		}
		
		$types = $searchIndexs[$condition['id']]['types'];
		$sql = 'select count(*) as num from vedio where topType =:topType';
		$aPrepareSource[':topType'] = array($condition['id'], PDO::PARAM_INT);
		
		if (isset($condition['tag']) && Lamb_Utils::isInt($condition['tag']) && array_key_exists($condition['tag'], $types)) {
			$sql = 'select count(*) as num from vedio a,tag b,tagrelation c where b.tagid=c.tagid and c.vedioid=a.id and b.tagname=:tagname and topType=:topType';
			$aPrepareSource[':tagname'] = array($types[$condition['tag']], PDO::PARAM_STR);
			$this->mListLooperParam['tag'] = $types[$condition['tag']];
		}
		$num = $this->mApp->getDb()->getPrepareRowCount($sql, $aPrepareSource);
		if ($num  == 0) {
			return 0;
		}
		$num = ceil($num / $listPagesize);	
		
		$this->mListLooperParam['num'] = $num;
		if (isset($condition['limit']) && Lamb_Utils::isInt($condition['limit'], true)) {
			$num = min($num, $condition['limit']);
			$this->mListLooperParam['limit'] = $condition['limit'];
		}	
		$this->mListLooperParam['id'] = $condition['id'];
		return $num;
	}
	
	/**
	 * @param Ttkvod_Controllor $controllor
	 * @param string $action
	 * @param array $param
	 * @return void
	 */
	public function core($controllor, $action, $router_path, array $param = array())
	{
		
		$method = ($action ? $action : 'index') . 'Action';
		if (method_exists($controllor, $method)) {
			$param['ct'] = -1;
			$this->mRequest->setUserParams($param);
			ob_start();		
			$controllor->$method();
			$buffer = ob_get_contents();
			ob_end_clean();
			$this->putContent($router_path, $buffer);
		}
	}
	
	/**
	 * @param string $controllor
	 * @param string $action
	 * @return Ttkvod_Controllor
	 */
	public function loadControllor($controllor, $action)
	{
		$class = $controllor . 'Controllor';
		Lamb_Loader::loadClass($class, $this->mCfg['controllor_path']);
		$this->mDispatcher->setOrGetControllor($controllor)->setOrGetAction($action);
		return new $class;
	}
	
	/**
	 * @param string $path
	 * @param string $content
	 * @return void
	 */
	public function putContent($path, $content)
	{
		$path = rtrim($this->mCfg['static_cfg']['sync']['save_path'], '\\/') . '/' . $path;
		Lamb_IO_File::mkdir(dirname($path));
		Lamb_IO_File::putContents($path, $content);
	}
	
	/**
	 * @param string $model
	 * @param array  $param
	 * @return string
	 */
	public function router($model, array $param)
	{
		return  $this->mLinkRouter->router($model, $param, Ttkvod_Model_LinkRouter_Interface::T_MODE_STATIC_PATH);
	}
	
	/**
	 * @param boolean $isItem
	 * @return Ttkvod_Model_Static
	 */
	public function flushCDN($isItem = true)
	{
		if ($isItem) {
			Ttkvod_Utils::flushCDN($this->mItemCDNUrls);
			$this->mItemCDNUrls = array();
		} else {
			Ttkvod_Utils::flushCDN($this->mListCDNUrls);
			$this->mListCDNUrls = array();		
		}
		
		return $this;
	}
	
	public function itemLooperCallback($page, $pagesize = 100)
	{
		$param = $this->mItemLooperParam;
		$db = $this->mApp->getDb();
		$sqlHelper = $this->mApp->getSqlHelper();
		if (isset($param['limit']) && Lamb_Utils::isInt($param['limit'], true)) {
			if($param['limit'] < $page  * $pagesize ) {
				$pagesize = $param['limit'] - ($page - 1) * $pagesize;
				if ($pagesize <= 0) {
					return ;
				}
			}
		}
		$sql = $sqlHelper->getLimitSql($param['sql'], $pagesize, ($page - 1) * $pagesize);
		foreach ($db->query($sql)->toArray() as $item) {
			$this->createItem($item['id']);
			$this->printInfo($item);
		}
		$this->flushCDN();
	}
	
	public function listLooperCallback($page, $pagesize = 100)
	{
		$param = $this->mListLooperParam;
		$start = ($page - 1) * $pagesize + 1;		
		$id = $param['id'];
		$end = $start + $pagesize;
		$end = min($end, $param['num'] + 1);
		if (isset($param['limit'])) {
			$end = min($end, $param['limit'] + 1);
		}
		$tag = isset($param['tag']) ? $param['tag'] : null;
		$data = array(
			'id' => $id, 'tag' => $tag	
		);
		for (; $start < $end; $start++) {
			$this->createList($id, $start, $tag);
			$data['p'] = $start;
			$this->printInfo($data, false);
		}
		$this->flushCDN(false);
	}
	
	/** 
	 * @param int $tasktype T_ITEM T_LIST
	 * @return Ttkvod_Model_Static
	 */
	public function setCurrentTaskType($tasktype)
	{
		$this->mCurrentTaskType = (int)$tasktype;
		return $this;
	}
	
	/**
	 * @param string $url
	 * @return Ttkvod_Model_Static
	 */
	public function setTaskUrl($url)
	{
		$this->mTaskUrl = (string)$url;
		return $this;
	}
	
	public function printInfo($data, $isItem = true)
	{
		//ob_start();
		if ($isItem) {
			echo "影片：<b style='color:green'>{$data['name']}</b>, ID : <b style='color:green'>{$data['id']}</b> 生成成功<br/>";
		} else {
			$param = array('id' => $data['id'], 'p' => $data['p']);
			$param['tag'] = $data['tag'] === null ? '' : $data['tag'];
			$path = $this->router('list', $param);
			$typename = $this->mCfg['channels'][$data['id']]['name'];
			echo "<b style='color:green'>$typename</b>， 分类：<b style='color:green'>" . ($param['tag'] ? $param['tag'] : '全部') ."</b>，路径：<b style='color:green'>{$path}</b>，生成成功<br/>";
		}
		//ob_end_flush();
	}
	
	/**
	 * @param int $intervalTimeout
	 */
	public function endEcho($type, $intervalTimeout = 2)
	{
		$info = array(
			self::T_INDEX => '首页生成完毕',
			self::T_TOP => '排行榜生成完毕',
			self::T_ITEM => '内容页生成完毕',
			self::T_LIST => '列表页生成完毕'
		);
		ob_start();
		echo $info[$type];
		if ($this->mTaskUrl) {
			echo ',' . $intervalTimeout . '秒后自动进入下一个任务。';
			echo "<script>location.href='{$this->mTaskUrl}'</script>";
		}
		ob_end_flush();
	}
	
	public function looperHandler($taskFlag, Ttkvod_HttpPageLooper $looper)
	{
		$pages = $looper->getCount();
		$page = $looper->setOrGetCurrentPage();
		$time = $looper->setOrGetSleepSecond();
		if ($page == 1) {
			$url = $this->mRequest->getRequestUri() . '/p/2';
		} else {
			$url = preg_replace('/\/p\/[^\/]*/is', '/p/' . ($page+1), $this->mRequest->getRequestUri());
		}
		ob_start();
		switch ($taskFlag) {
			case Ttkvod_HttpPageLooper::TASK_END:
				$this->endEcho($this->mCurrentTaskType, $time);
				break;
			case Ttkvod_HttpPageLooper::PER_TASK_BEGIN:
				echo "共 <b>$pages</b> 页，当前第 <b style='color:red'>$page</b> 页<br/>";
				break;
			case Ttkvod_HttpPageLooper::PER_TASK_END:
				echo "当前页处理完毕，{$time}秒后跳转到下一页 <script>setTimeout(function(){location.href='$url'}, " . ($time * 1000) . ")</script>";
				break;
		}
		ob_end_flush();		
	}
}