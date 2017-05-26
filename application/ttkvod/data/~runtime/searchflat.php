<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="gb2312">
	<meta name="shenma-site-verification" content="5f545f609549e1609ffc80987225d712_1486191571"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit|ie-stand" />
	<title><?php echo $keywords;?>-<?php echo $this->mSiteCfg['site_name'];?></title>
	<meta name="Keywords" content="<?php echo $keywords;?>-糖果影音全网搜索 搜全网 全网搜 " />
	<meta name="description" content="糖果影音视频搜索结果页 <?php echo $keywords;?>" />
	
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
		
		<div class="row row-margin">
			
			<div class="col-md-12" style="margin-bottom: 15px;">
				<ul class="category-top clearfix">
					<li><a hideFocus="true" router='static' href="<?php echo $this->mLinkRouter->router('', $params)?>" <?php if (!$typeid){?>class="curt"<?php }?>>全部</a>
					<li><a hideFocus="true" router='static' href="<?php echo $this->mLinkRouter->router('', $params + array('typeid' => 1))?>" <?php if ($typeid == '1'){?>class="curt"<?php }?>>电影</a></li>
					
					<li><a hideFocus="true" router='static' href="<?php echo $this->mLinkRouter->router('', $params + array('typeid' => 2))?>" <?php if ($typeid == '2'){?>class="curt"<?php }?>>电视剧</a></li>
					
					<li><a hideFocus="true" router='static' href="<?php echo $this->mLinkRouter->router('', $params + array('typeid' => 3))?>" <?php if ($typeid == '3'){?>class="curt"<?php }?>>动漫</a></li>
					
					<li><a hideFocus="true" router='static' href="<?php echo $this->mLinkRouter->router('', $params + array('typeid' => 4))?>" <?php if ($typeid == '4'){?>class="curt"<?php }?>>综艺</a></li>
					
					
									
				</ul>
			</div>
			
			
			<div class="col-md-12">
				<div class="row">
					
					<ul class="nav nav-tabs col-md-12">
						<li <?php if ($order=='0'){?>class="active"<?php }?>>
							<a href="<?php echo $this->mLinkRouter->router('', array('id' => 'search', 'auto' => 'tag', 'q' => $keywords, 'order' => 0))?>" >按时间排序</a>	
						</li>
						<li <?php if($order=='3'){?>class="active"<?php }?>>
							<a href="<?php echo $this->mLinkRouter->router('', array('id' => 'search', 'auto' => 'tag', 'q' => $keywords, 'order' => 3))?>" >按热度排序</a>
						</li>
					</ul>
					
				</div>
				
				<div class="row movie-info-list main-info">
					<div class="tab-content col-md-12">
						 <div role="tabpanel" class="tab-pane active" id="mv-update-time">
						 	<ul class="tab-pane-ul">
							<!-- movie item -->
							
							<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$sql.'',
				'include_union' => false,
				'prepare_source' => $aPrepareSource,
				'is_page' => true,
				'page' => $page,
				'pagesize' => $pagesize,
				'offset' => null,
				'limit' => null,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => 'list',
				'empty_str' => '<li class="none" style="display:block">对不起，未找到影片资源，请重选条件</li>',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
								<li class="row" style="margin-bottom: 20px;">
									<div class="col-md-2 col-xs-6">
										<a hideFocus="true" class="search-pic" target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'"  title="\'.$item[\'name\'].\'" router="static">
											<img class="img-responsive" src="themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" />
											<span class="movie-mark">\'.$item[\'mark\'].\'</span>
											<strong class="search-point">\'.(sprintf(\'%0.1f\', $item[\'point\'])).\'</strong>
										</a>
									</div>
									
									<div class="col-md-10 col-xs-12">
										<div class="main-title">
											<h4>\'.($item[\'type\'] == 1 ? \'电影\' : ($item[\'type\'] == 2 ? \'电视剧\' : ($item[\'type\'] == 3 ? \'动漫\' : \'综艺\'))).\' <a href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a><span>(\'.$item[\'vedioYear\'].\')</span></h4>
										</div>
										
										<div class="main-tags">
											<span>主演：</span>\'.(Ttkvod_Utils::randerTagHtml($item[\'actors\'])).\'
										</div>
										<div class="main-tags">
											<span id="m-tag">导演：</span>\'.(Ttkvod_Utils::randerTagHtml($item[\'directors\'])).\'
										</div>
										<div class="main-tags">
											<span>类型：</span>\'.(Ttkvod_Utils::randerTagHtml($item[\'tag\'])).\'
										</div>
										<div class="main-tags">
											<span>地区：</span>\'.$item[\'area\'].\'
										</div>
										
										<div class="m-desc">\'.(preg_replace(\'/(<(\\/)?[^>]*>)/is\', \'\', Lamb_Utils::mbSubstr($item[\'content\'], 0, 150))).\'...</div>
										
									</div>

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
				  	<li><a href="'.$firstPageUrl.'" title="首页"><i class="glyphicon glyphicon-step-backward"></i></a></li>
					<li><a href="'.$prevPageUrl.'" title="上一页 快捷键←" aria-label="Previous"><i aria-hidden="true" class="glyphicon glyphicon-backward"></i></a></li>

					',
			'page_end_html'	=>	'

					<li><a href="'.$nextPageUrl.'" title="下一页 快捷键→" aria-label="Next"><i aria-hidden="true" class="glyphicon glyphicon-forward"></i></a></li>
					<li><a href="'.$lastPageUrl.'" title="尾页"><i class="glyphicon glyphicon-step-forward"></i></a></li>
					',
			'more_html'		=>	'',
			'focus_html'	=>	'<li class="active"><a>#page#</a></li>',
			'nofocus_html'	=>	'<li><a href="'.$pageUrl.'">#page#</a></li>',
			'max_page_count' => 0,
			'page' => null,
			'pagesize' => null,
			'data_num' => null
		))?>	
						
				 </ul>
			   </nav>
			
			</div>

		</div>
		
	</div>
	<?php include $this->mView->load("bottom_f");?>
	
</body>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="//cdn.bootcss.com/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="/themes/js/config.js"></script>
<script src="/themes/js/index.js"></script>
<script>
	BC.action.index();
</script>
</html>