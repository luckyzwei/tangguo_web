<?php
class Ttkvod_Collect_Looper extends Lamb_Event_Emiter
{
	const EVENT_LIST_BEGIN = 1;
	
	const EVENT_LIST_END = 2;
	
	const EVENT_ITEM_BEGIN = 4;
	
	const EVENT_ITEM_END = 8;
	
	const EVENT_TASK_END = 16;
	
	const EVENT_END_VALUE_PAGE_BREAK = 1;
	
	const EVENT_END_VALUE_LIST_NET_ERROR = 2;
	
	const EVENT_END_VALUE_LIST_RULE_ERROR = 4;
	
	const EVENT_END_VALUE_ITEM_NET_ERROR = 8;
	
	const EVENT_END_VALUE_ITEM_RULE_ERROR = 16;
	
	const EVENT_END_VALUE_NUM_BREAK = 32;
	
	const EVENT_END_VALUE_DATE_BREAK = 64;
	
	const CONDITION_NUM = 1;
	
	const CONDITION_PAGE =2;
	
	const CONDITION_DATE = 4;
	
	/**
	 * @var int 
	 */
	protected $mCurrentPage;
	
	/**
	 * @var Ttkvod_Collect_ListInterface
	 */
	protected $mList;
	
	/** 
	 * @var Ttkvod_Collect_ItemInterface
	 */
	protected $mItem;
	
	/**
	 * @var array = array('type' => CONDITION_NUM | CONDITION_PAGE | CONDITION_DATE, val=> mixed)
	 */
	protected $mCondition = array();
	
	public function __construct(array $condition = null, Ttkvod_Collect_ListInterface $list = null, Ttkvod_Collect_ItemInterface $item = null)
	{
		parent::__construct();
		if (null !== $condition) {
			$this->setOrGetCondition($condition);
		}
		if (null !== $list) {
			$this->setOrGetList($list);
		}
		if (null !== $item) {
			$this->setOrGetItem($item);
		}
	}
	
	/**
	 * @param Ttkvod_Collect_ListInterface $list
	 * @return Ttkvod_Collect_ListInterface | Ttkvod_Collect_Looper
	 */
	public function setOrGetList(Ttkvod_Collect_ListInterface $list = null)
	{
		if (null === $list) {
			return $this->mList;
		}
		$this->mList = $list;
		return $this;
	}
	
	/** 
	 * @param Ttkvod_Collect_ItemInterface $item
	 * @return Ttkvod_Collect_ListInterface | Ttkvod_Collect_Looper
	 */
	public function setOrGetItem(Ttkvod_Collect_ItemInterface $item = null)
	{
		if (null === $item) {
			return $this->mItem;
		}
		$this->mItem = $item;
		return $this;
	}
	
	/**
	 * @param array $condition
	 * @return array | Ttkvod_Collect_Looper
	 */
	public function setOrGetCondition(array $condition = null)
	{
		if (null === $condition) {
			return $this->mCondition;
		}
		$this->mCondition = $condition;
		return $this;
	}
	
	/** 
	 * @return int
	 */
	public function getCurrentPage()
	{
		return $this->mCurrentPage;
	}
	
	/**
	 * @param int $alreayCollectNum
	 * @param int $page
	 * @return int
	 */
	public function runForHttp(&$alreayCollectNum = 0, $page = 1)
	{
		if ($this->mCondition['type'] == self::CONDITION_PAGE && $page > $this->mCondition['val']) {
			$this->emit(self::EVENT_TASK_END, array($this, self::EVENT_END_VALUE_PAGE_BREAK, $page));
			return self::EVENT_END_VALUE_PAGE_BREAK;
		}
		
		if ($this->mCondition['type'] == self::CONDITION_NUM && $alreayCollectNum >= $this->mCondition['val']) {
			$this->emit(self::EVENT_TASK_END, array($this, self::EVENT_END_VALUE_NUM_BREAK, $alreayCollectNum));
			return self::EVENT_END_VALUE_NUM_BREAK;		
		}
		$this->mCurrentPage = $page;
		$listdata = $this->mList->collect($this->mList->getUrl($page), null, $error);
		
		if ($error != Ttkvod_Collect_Interface::S_OK) {
			$error = $error == Ttkvod_Collect_Interface::E_NET_FAIL ? self::EVENT_END_VALUE_LIST_NET_ERROR
							: self::EVENT_END_VALUE_LIST_RULE_ERROR;
			$this->emit(self::EVENT_TASK_END, array($this, $error, $page));
			return $error;
		}
		
		$endnum = count($listdata);
		$willBreakByNum = false;
		
		if ($this->mCondition['type'] == self::CONDITION_NUM && $alreayCollectNum + $endnum > $this->mCondition['val']) {
			$endnum = $this->mCondition['val'] - $alreayCollectNum;
			$willBreakByNum = true;
		}
		
		$this->emit(self::EVENT_LIST_BEGIN, array($this, $page));
	
		for ($i = 0; $i < $endnum; $i ++) {
		
			$data = $listdata[$i];

			if ($this->mCondition['type'] == self::CONDITION_DATE && 
				isset($data['datetime']) && Lamb_Utils::isInt($data['datetime'], true) && 
				$this->mCondition['val'] >= $data['datetime']) {
				$this->emit(self::EVENT_TASK_END, array($this, self::EVENT_END_VALUE_DATE_BREAK, $data['datetime']));
				return self::EVENT_END_VALUE_DATE_BREAK;
			}
			
			$this->emit(self::EVENT_ITEM_BEGIN, array($this, $data));
			$itemdata = $this->mItem->collect($data['url'], $data['externls'], $error);

			if ($error != Ttkvod_Collect_Interface::S_OK) {
				$this->emit(self::EVENT_ITEM_END, array($this, $error, $itemdata, null));
			} else if ($this->mCondition['type'] == self::CONDITION_DATE &&
						(!isset($data['datetime']) || !Lamb_Utils::isInt($data['datetime'], true)) &&
						$this->mCondition['val'] >= $itemdata['updateDate']){
				$this->emit(self::EVENT_ITEM_END, array($this, Ttkvod_Collect_Interface::S_OK, $itemdata, $data));				
				$this->emit(self::EVENT_TASK_END, array($this, self::EVENT_END_VALUE_DATE_BREAK, $itemdata['updateDate']));
				return self::EVENT_END_VALUE_DATE_BREAK;
			} else {
				$this->emit(self::EVENT_ITEM_END, array($this, Ttkvod_Collect_Interface::S_OK, $itemdata, $data));
			}
			$alreayCollectNum ++;
		}
		
		if ($willBreakByNum) {
			$this->emit(self::EVENT_TASK_END, array($this, self::EVENT_END_VALUE_NUM_BREAK, $alreayCollectNum));
			return self::EVENT_END_VALUE_NUM_BREAK;		
		}
		
		$this->emit(self::EVENT_LIST_END, array($this, $page));
		
		return 0;
	}
	
	public function runForLooper()
	{
	
	}
	
	/**
	 * @param array $data
	 * @return 0 - error 1 - lock 2 - update 3 - insert
	 */
	public static function writeToDB($data, &$ext = null)
	{
		static $model = null;
		
		if (null === $model) {
			$model = new Ttkvod_Model_Video;
		}
		$ret = 0;
		$nopic = $GLOBALS['aCfg']['nopic_path'];
		
		if ($olddata = $model->get($data['name'], Ttkvod_Model_Video::T_VIDEO_NAME, true)) {
			if ($olddata['isLock']) {
				$ret = 1;
			} else {
				$ret = $model->update($olddata['id'], Ttkvod_Model_Video::T_VID, 
					array(
						'vedioYear' => $data['vedioYear'],
						'area' => $data['area'],
						'mark' => $data['mark'],
						'isEnd' => $data['isEnd'],
						'updateDate' => $data['updateDate'],
						'syDate' => $data['syDate'],
						'playData' => $data['playData']
					)) ? 2 : 0;
				$ext = $olddata['id'];
			}
		} else {
			if (Lamb_Utils::isHttp($data['vedioPic'])) {
				$path =  Lamb_IO_File::getUniqueName($GLOBALS['aCfg']['pic_path'] . time() . rand(0,1000) . '.jpg');
				if ($content = Ttkvod_Utils::fetchContentByUrlH($data['vedioPic'], 10)) {
					Lamb_IO_File::putContents($path, $content);
					$data['vedioPic'] = '/' . str_replace(ROOT, '', $path);
				} else {
					$data['vedioPic'] = $nopic;
				}
			} else if ($data['vedioPic'] != $nopic) {
				$data['vedioPic'] = $nopic;
			}
			$ext = $data['vedioPic'];
			$ret = $model->add($data) > 0 ? 3 : 0;
		}
		
		return $ret;
	}
}