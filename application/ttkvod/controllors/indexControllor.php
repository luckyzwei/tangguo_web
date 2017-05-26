<?php
class indexControllor extends Ttkvod_Controllor
{
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

	public function getControllorName()
	{
		return 'index';
	}
	
	public function indexAction()
	{
		$this->mCacheTime = $this->mRequest->ct;
		if (!Lamb_Utils::isInt($this->mCacheTime)) {
			$this->mCacheTime = 0;
		}	
		$searchIndex = array(
			'year' => array(2016, 2015, 2014, 2013, 2012, 2011, 2010, 2009, 2008),
			'area' => array('��½', '���', '̨��', '����', '�ձ�', '̩��', 'ŷ��', '����'),
			'movie' => array(
				'type' => array('����','ð��','ϲ��','����','�ƻ�','�ֲ�','ս��','����', '����','���','����','����', '����')
			),
			'tv' => array(
				'type' => array('����','��װ','��Ц','����','��','ż��','�ƻ�','����','����','��ͥ','����','��ʷ')
			),
			'show' => array(
				'type' => array('�ѿ���','������','ѡ��','��ʳ','����','����','��ʵ','��Ц','�ٶ�','����','ʱ��','��̸','����')
			),
			'anime' => array(
				'type' => array('��Ѫ','��Ц','����Ů','����','��ս','����','����','ð��','���','У԰','����','����')
			)
		);
		$listSqlTemplate = 'select id,name,mark,vedioPic from vedio where type=:tp and status=1 order by weekNum desc';
		$listSqlTemplateForArea = 'select id,name,mark,vedioPic from vedio where type=:tp and area=:area and status=1 order by updateDate desc';
		$listSqlTemplateForUpdate = 'select id,name,mark,vedioPic from vedio where type=:tp and status=1 order by updateDate desc';
		$listSqlTempalteForTag = 'select id,name,mark,vedioPic from vedio a,tag b,tagrelation c where type=:tp and a.id=c.vedioid and b.tagid=c.tagid and b.tagname=:tag and status=1 order by updateDate desc';

		$topPointSqlTemplate = 'select name,id,point from vedio where type=:tp and status=1 and point < 10 order by point desc,pointAll desc';
		include $this->load('index');
	}
	
}
