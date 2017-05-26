<?php
class Ttkvod_HttpPageLooper extends Lamb_Looper_SqlPageStep
{
	const TASK_BEGIN = 1;
	
	const TASK_END = 2;
	
	const PER_TASK_BEGIN = 4;
	
	const PER_TASK_END = 8;
	
	/**
	 * @var int
	 */
	protected $mSleepSecond = 2;
	
	/**
	 * @var string
	 */
	protected $mUrlPrefix = '';
	
	/** 
	 * @var PHP callback
	 */
	protected $mMsgHandler = null;
	
	/**
	 * @param int $pagesize
	 */
	public function __construct($pagesize)
	{
		parent::__construct($pagesize);
	}
	
	/**
	 * @param int $time
	 * @return int | Ttkvod_HttpPageLooper
	 */
	public function setOrGetSleepSecond($time = null)
	{
		if (null === $time) {
			return $this->mSleepSecond;
		}
		$this->mSleepSecond = (int)$time;
		return $this;
	}
	
	/**
	 * @param string $prefix 
	 * @return string | Ttkvod_HttpPageLooper
	 */
	public function setOrGetUrlPrefix($prefix = null)
	{
		if (null === $prefix) {
			return $this->mUrlPrefix;
		}
		$this->mUrlPrefix = (string)$prefix;
		return $this;
	}
	
	/** 
	 * @param PHP callback $handler 0 - default
	 * @Ttkvod_HttpPageLooper
	 */
	public function setMsgHandler($handler = 0)
	{
		if (0 === $handler) {
			$handler = __CLASS__ . '::defaultHandler';
		}
		if (null !== $handler && is_callable($handler)) {
			$this->mMsgHandler = $handler;
		}
		return $this;
	}
	
	/**
	 * @return PHP callback
	 */
	public function getMsgHandler()
	{
		return $this->mMsgHandler;
	}
	
	/**
	 * @override
	 */
	public function run()
	{
		$pages = $this->getCount();
		$page = $this->setOrGetCurrentPage();
		if ($page > $pages) {
			return false;
		}
		$handler = $this->getMsgHandler();
		if ($handler) {
			call_user_func($handler, self::PER_TASK_BEGIN, $this);
		}
		
		$this->setOrGetCurrentPage($page);
		parent::run();
		
		if ($handler) {
			if ($page >= $pages) {
				call_user_func($handler, self::TASK_END, $this);
			} else {
				call_user_func($handler, self::PER_TASK_END, $this);
			}
		}
	}
	
	public static function defaultHandler($taskFlag, Ttkvod_HttpPageLooper $looper)
	{
		$res = Lamb_App::getGlobalApp()->getResponse();
		$router = Lamb_App::getGlobalApp()->getRouter();
		$pages = $looper->getCount();
		$page = $looper->setOrGetCurrentPage();
		$time = $looper->setOrGetSleepSecond();
		$urlprefix = $looper->setOrGetUrlPrefix() . $router->setUrlDelimiter() . 'page' . $router->setUrlDelimiter() . ($page + 1);
		
		switch ($taskFlag) {
			case self::TASK_END:
				$res->fecho("任务已经全部执行成功！");
				break;
			case self::PER_TASK_BEGIN:
				$res->fecho("共 <b>$pages</b> 页，当前第 <b style='color:red'>$page</b> 页<br/>");
				break;
			case self::PER_TASK_END:
				$res->fecho("当前页处理完毕，{$time}秒后跳转到下一页 <script>setTimeout(function(){location.href='$urlprefix'}, " . ($time * 1000) . ")</script>");
				break;
		}
	}
}