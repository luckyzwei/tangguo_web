<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="gb2312">
	<meta name="shenma-site-verification" content="5f545f609549e1609ffc80987225d712_1486191571"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit|ie-stand" />
	<title><?php echo $topTypeInfos['name'];?>-<?php echo $this->mSiteCfg['site_name'];?></title>
	<meta name="keywords" content="<?php echo $topTypeInfos['name'];?>��Ƶ, <?php echo $topTypeInfos['name'];?>��Ƶ���а�, ���¸���<?php echo $topTypeInfos['name'];?>��Ƶ, <?php echo $topTypeInfos['name'];?>���а�, <?php echo $topTypeInfos['name'];?>��Ƶ������ѹۿ�" />
	<meta name="description" content="�ǹ�Ӱ�����µ�Ӱ���,��¼��Ӱ,��Ƶ��Դ��ȫ����վ,�ǹ�Ӱ����ӰƵ���ṩȫ�����ŵ�Ӱ��ѹۿ�,���������Ӱ���߹ۿ�,ͬ������ȫ����Ƶ��վ��ӳ��Ƭ,����ŷ����Ƭ,�պ���Ӱ,�����Ӱ��" />

	<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="/themes/css/index.css" rel="stylesheet">

	<!--[if lt IE 9]>
	  <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>
	
<body>
	
	<?php include $this->mView->load("head_nav");?>
	<div class="container container-top60">
		
		<div class="row">
			<div class="col-md-12" >
				<ul class="category-top clearfix">
					<li><a hideFocus="true"  router='static'<?php if(empty($area)){?> class="current"<?php }?> href="<?php echo $this->mLinkRouter->router('list', array('tag' => $tag, 'id' => $id))?>">ȫ��</a></li>
						<?php foreach ($currentSearchIndex['areas'] as $item) { 
							$link = $this->mLinkRouter->router('list', array('order' => $order, 'tag' => $tag, 'area' => $item, 'id' => $id));
							$class = '';
							if ($item == $area) {
								$class = ' class="curt"';
							}
						?>
					<li><a hideFocus="true"  router='static' href='<?php echo $link;?>' <?php echo $class;?>><?php echo $item;?></a></li>
					<?php }?>					
				</ul>
				
				<div class="hot-tags"><i class="glyphicon glyphicon-tags"></i><h3>���ű�ǩ</h3></div>
				
				<ul class="category-middle clearfix">
					<li><a hideFocus="true"  router='static'<?php if(empty($tag)){?> class="current"<?php }?> href="<?php echo $this->mLinkRouter->router('list', array('order' => $order, 'area' => $area, 'id' => $id)) ?>">ȫ����ǩ</a></li>
						<?php foreach ($currentSearchIndex['types'] as $type) {
							$_type = $type;
							$class = '';
							if ($_type == $tag) {
								$class = ' class="current"';
							}
							
							
							$link = $this->mLinkRouter->router('list', array('tag' => $type, 'id' => $id));
							
							
						?>
						<li <?php echo $class;?>><a hideFocus="true"  router='static' title="<?php echo $type;?>" href="<?php echo $link;?>"><?php echo $type;?></a></li>
					<?php }?>
				</ul>
				
			</div>
			<div class="col-md-9">
				<div class="row row-nav-tabs">
					
					<ul class="nav nav-tabs col-md-12">
						<li  <?php if($order=='0'){?>class="active"<?php }?>>
							<a hideFocus="true"  href="<?php echo $this->mLinkRouter->router('list', array('id' => $id, 'order' => 0, 'area' => $area, 'tag' => $tag))?>" >��ʱ������</a>
							
						</li>
						<li <?php if($order=='3'){?>class="active"<?php }?>>
							<a hideFocus="true"  href="<?php echo $this->mLinkRouter->router('list', array('id' => $id, 'order' => 3, 'area' => $area, 'tag' => $tag))?>" >���ȶ�����</a>
						</li>
					</ul>
					
				</div>
				
				<div class="row">
					<div class="col-md-12">
						 <div role="tabpanel" class="tab-pane active" id="mv-update-time">
						 	<ul class="row tab-pane-ul">
							<!-- movie item -->
							<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$sql.'',
				'include_union' => null,
				'prepare_source' => $aPrepareSource,
				'is_page' => true,
				'page' => $page,
				'pagesize' => 24,
				'offset' => null,
				'limit' => null,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => 'list',
				'empty_str' => '<li class="l-m-none">�Բ�����δ�ҵ���Ƶ��Դ������ѡɸѡ</li>',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
								<li class="col-md-3 col-sm-4 col-xs-6 col-xxs-3">
									<a hideFocus="true"  class="middle-pic" target="_blank" title="\'.$item[\'name\'].\'" href="\'.(Ttkvod_Utils::UR(\'item\',  array(\'id\' => $item[\'id\']))).\'">
							  		<img class="img-responsive" src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							  			<span class="movie-mark"><strong>\'.(sprintf(\'%0.1f\', $item[\'point\'])).\'</strong>\'.$item[\'mark\'].\'</span>
							  		</a>
							  		<p class="middle-pic-text"><a hideFocus="true"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
							  		<p class="actor">
							  			\'.(Ttkvod_Utils::randerTagHtml($item[\'actors\'])).\'
							  		</p>
								</li>
							\');')
			))?>
							<!--/movie item -->
							</ul>
						 </div>
					</div>
				</div>
				
				<nav>
				  <ul class="pagination">
				  	<?php Lamb_View_Tag_Page::page(array(
			'page_num'		=>	9,
			'page_style'	=>	1,
			'listid'		=>	'list',
			'page_start_html'=>	'	
				  	<li><a hideFocus="true"  href="'.$firstPageUrl.'" title="��ҳ"><i class="glyphicon glyphicon-step-backward"></i></a></li>
					<li><a hideFocus="true"  href="'.$prevPageUrl.'" title="��һҳ ��ݼ���" aria-label="Previous"><i aria-hidden="true" class="glyphicon glyphicon-backward"></i></a></li>

					',
			'page_end_html'	=>	'

					<li><a hideFocus="true"  href="'.$nextPageUrl.'" title="��һҳ ��ݼ���" aria-label="Next"><i aria-hidden="true" class="glyphicon glyphicon-forward"></i></a></li>
					<li><a hideFocus="true"  href="'.$lastPageUrl.'" title="βҳ"><i class="glyphicon glyphicon-step-forward"></i></a></li>
					',
			'more_html'		=>	'',
			'focus_html'	=>	'<li class="active"><a hideFocus="true" >#page#</a></li>',
			'nofocus_html'	=>	'<li><a hideFocus="true"  href="'.$pageUrl.'">#page#</a></li>',
			'max_page_count' => 0,
			'page' => null,
			'pagesize' => null,
			'data_num' => null
		))?>	
				 </ul>
			   </nav>
			</div>
			
			<div class="col-md-3">
				<div class="category-right clearfix">
					<p class="hot">����</p>
					<ul class="hot-list">
	  	 				<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$topSqlTemplate.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array($id, PDO::PARAM_INT)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 10,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => -1,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 1,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					
						<li class="rand_#autoIndex#">
							
							<em>#autoIndex#</em>
							<span>
								<a hideFocus="true" router="static" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">
								\'.(Lamb_Utils::mbSubstr($item[\'name\'], 0, 14)).\'</a>
							</span>
							
							
								
							<strong>\'.(sprintf(\'%0.1f\', $item[\'point\'])).\'</strong>
							
						</li>									           						
					\');')
			))?>
						
					</ul>

				</div> 

			</div>
		</div>
	</div>
	<?php include $this->mView->load("bottom_f");?>
	
	<div id="tips" class="tips"></div>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
<script src="/themes/js/config.js"></script>
<script src="/themes/js/index.js"></script>
<script>
	BC.action.index();
</script>
</body>
</html>