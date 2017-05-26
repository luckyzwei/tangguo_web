<?php
class collectControllor extends Ttkvod_Controllor
{
	private $list  = null;
	private $item  = null;
	private $model = null;
	private $mList = null;
	private $mItem = null;

	public function getControllorName()
	{
		return 'test';
	}	
	
	public function __construct()
	{
		parent::__construct();
		$this->model = new Ttkvod_Model_Video();
		$this->list  = new Ttkvod_Collect_Xgzy_List();
		$this->item  = new Ttkvod_Collect_Xgzy_Item();
		$this->mList = new Ttkvod_Collect_Mbzy_List();
		$this->mItem = new Ttkvod_Collect_Mbzy_Item();
	}
	
	public function xgzyAction()
	{
		set_time_limit(0) ;
		$page = trim($this->mRequest->p);
		$page = $page ? $page : 1;
		if($page > 83){
			echo '采集完毕';
			exit;
		}
		
		$url = $this->list->getUrl($page);
		$ret = $this->list->collect($url);	
		//Lamb_Debuger::debug($ret);

		echo "正在采集第{$page}页50部影片<br/>";
		
		foreach($ret as $item){
			$data = $this->item->collect($item['url']);
			if(empty($data['name'])){
				echo "资源采集失败！地址:{$item['url']}<br/>";
				continue;
			}
			if($this->model->update($data['name'], Ttkvod_Model_Video::T_VIDEO_NAME, array('playData' => $data['playData'], 'mark' => $data['mark'], 'content' => $data['content']))){
				echo "影片[<font color='green'>{$data['name']}</font>]修改成功<br/>";
			}else{
				echo "影片[<font color='green'>{$data['name']}</font>]" . ($this->model->add($data) ? "<font color='pink'>插入成功</font><br/>" : "<font color='red'>插入失败</font><br/>");		
			}
		}
		
		$url = $this->mRouter->urlEx($this->C, $this->A, array('p' => ++$page)); 
		$this->redirect($url);
	}
	
	public function mbzyAction()
	{
		set_time_limit(0) ;
		$page = trim($this->mRequest->p);
		$page = $page ? $page : 1;
		if($page > 50){
			echo '采集完毕';
			exit;
		}
		
		$url  = $this->mList->getUrl($page);
		$ret  = $this->mList->collect($url);	
		//Lamb_Debuger::debug($ret);
	
		if(empty($ret)){
			$this->redirect($this->mRouter->urlEx($this->C, $this->A, array('p' => ++$page)));
		}
		
		echo "正在采集第{$page}页50部影片<br/>";
		
		foreach($ret as $item){
			$data = $this->mItem->collect($item['url'], $item['externls']);
			if(empty($data['name'])){
				echo "资源采集失败！地址:{$item['url']}<br/>";
				continue;
			}
			
			if(empty($data['playData'])){
				echo "影片[<font color='red'>{$data['name']}</font>]资源不符合要求，跳过采集<br/>";
				continue;
			}
			
			if($data['area'] == ''){
				$data['area'] = '其他';
			}
			
			if($this->model->update($data['name'], Ttkvod_Model_Video::T_VIDEO_NAME, array('playData' => $data['playData'], 'mark' => $data['mark'], 'content' => $data['content']))){
				echo "影片[<font color='green'>{$data['name']}</font>]修改成功<br/>";
			}else{
				echo "影片[<font color='green'>{$data['name']}</font>]" . ($this->model->add($data) ? "<font color='pink'>插入成功</font><br/>" : "<font color='red'>插入失败</font><br/>");		
			}
		}
		
		$url  = $this->mRouter->urlEx($this->C, $this->A, array('p' => ++$page)); 
		$this->redirect($url);
	}

	public function redirect($url)
	{
		 echo "<script>
				 function redirect() 
				 {
					 window.location.replace('$url');
				 }
				 window.setTimeout('redirect();', 5000);
			 </script>";
	}

}