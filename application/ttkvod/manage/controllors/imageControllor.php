<?php
class imageControllor extends Ttkvod_ManageControllor
{
	private $model = null;
	public function getControllorName()
	{
		return 'imageContorllor';
	}
	
	public function __construct()
	{
		$this->model = new Ttkvod_Model_Video();
		parent::__construct();
	}
	
	public function indexAction()
	{
		$page = trim($this->mRequest->p);
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		$sql = "select id,name,vedioPic,directors,area,updateDate from vedio where picModify = 0 order by id desc";
		
		$pageUrl = $this->mRouter->urlEx('image', 'index') . $this->mRouter->setUrlDelimiter() . 'p' . $this->mRouter->setUrlDelimiter();
		include $this->load('image');
	}
	
	public function ajaxAction()
	{
		$name = trim($this->mRequest->name);
		$name = iconv('gbk', 'utf-8', $name);
		$data = array();
		$url = "http://so.mianbao.com/vod-search-kw-{$name}-ajax-ajax";
		if(!$html = Ttkvod_Utils::fetchContentByUrlH($url)){
			exit('internet busy');
		}
	
		$patt = array(
			'video_url' => '/class="play-img".*?href="(.*?)".*?src="(.*?)".*?alt="(.*?)"/is'
		);
		$html = json_decode($html,true);
		if (!preg_match_all($patt['video_url'], $html['ajaxtxt'], $html, PREG_SET_ORDER)) {
			exit('not match');
		}
		
		$data = array();
		foreach($html as $key => $item){
			$data[$key]['url']  = trim($item[1]);
			$data[$key]['img']  = trim($item[2]);
			$data[$key]['name'] = trim($item[3]);
		}
		
		echo json_encode($data); 
	}
	
	public function picLoadAction()
	{
		$id = trim($this->mRequest->id);
		$picLoad = trim($this->mRequest->picLoad);
		
		if($id == '' || $picLoad == ''){
			echo 0;
			return;
		}
		
		if($this->model->update($id, Ttkvod_Model_Video::T_VID, array('vedioPic' => $picLoad, 'picModify' => 1, 'picLoad' => 1))){
			echo 1;
			return;
		}
		
		echo 0;
	}
	
	public function ajaxImgAction()
	{
		$imgUrl = trim($this->mRequest->imgUrl);
		
		$patt = array(
			'img' => '/<img width="225" height="300" src="(.*?)"/is'
		);
		
		if(!Lamb_Utils::isHttp($imgUrl)){
			echo 0;
			exit;
		}
		
		if(!$html = Ttkvod_Utils::fetchContentByUrlH($imgUrl)){
			echo 0;
			exit;
		}
		
		if (!preg_match($patt['img'], $html, $html)) {
			echo 0;
			exit;
		}
		
		echo trim($html[1]);
	}
	
	/*
	public function getImageAction()
	{
		$site = trim($this->mRequest->site);
		$name = trim($this->mRequest->name);
		$data = $this->model->get($name, Ttkvod_Model_Video::T_VIDEO_NAME, true);
		$vedioPic = $data['vedioPic'];
		
		if($vedioPic == ''){
			exit('name not found');
		}
		
		$patt = array(
			'img' => '/<img width="225" height="300" src="(.*?)"/is'
		);
		
		if(!Lamb_Utils::isHttp($site)){
			exit('url error');
		}

		if(!$html = Ttkvod_Utils::fetchContentByUrlH($site)){
			exit('error');
		}
		
		if (!preg_match($patt['img'], $html, $html)) {
			exit('error2');
		}
		
		$imgUrl = trim($html[1]);
		if (Lamb_Utils::isHttp($imgUrl) && ($bin = file_get_contents($imgUrl))) {
			file_put_contents(substr($vedioPic,1), $bin);
			if($this->model->update($name, Ttkvod_Model_Video::T_VIDEO_NAME, array('picModify' => 1))){
				echo 1;
				return;
			}
		}
		echo 0;
	}
	*/

}