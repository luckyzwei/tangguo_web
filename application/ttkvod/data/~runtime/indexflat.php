<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="gb2312">
	<meta name="shenma-site-verification" content="5f545f609549e1609ffc80987225d712_1486191571"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit|ie-stand" />
	<title><?php echo $this->mSiteCfg['site_name'];?>-2017最新电影，海量高清视频在线免费观看，手机高清电影在线免费播放，高清免费电影，最新免费电影，限制级大片</title>
	<meta name="keywords" content="<?php echo $this->mSiteCfg['site_keywords'];?>" />
	<meta name="description" content="<?php echo $this->mSiteCfg['site_description'];?>" />
	
	<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="/themes/css/index.css" rel="stylesheet">

	<!--[if lt IE 9]>
	  <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	  <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<![endif]-->
</head>
<body>
<?php include $this->mView->load("head_nav");?>
<div class="container container-top60">
	
	<div class="row movie-hot">
	  <div class="col-xs-12">
		<p class="hot">热播推荐</p>
	  </div>
	   <!-- recommend item -->
	  <?php Lamb_View_Tag_List::main(array(
				'sql' => 'select id,name,vedioPic,mark from vedio order by stortId desc offset 0 rows fetch next 6 rows only;',
				'include_union' => null,
				'prepare_source' => null,
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => null,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => -1,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
	  <div class="col-md-2 col-sm-4 col-xs-6 col-xxs-3">
			<a router="static"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				<img src="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'" />	<span class="movie-mark">\'.$item[\'mark\'].\'</span>
			</a>
			<p class="pic-text"><a router="static" target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
	  </div>
	  \');')
			))?>
	</div>
	
	<div class="row movie-hot" style="margin-bottom: 20px">
		<!-- recommend item -->
	  <?php Lamb_View_Tag_List::main(array(
				'sql' => 'select id,name,vedioPic,mark from vedio order by stortId desc offset 6 rows fetch next 6 rows only;',
				'include_union' => null,
				'prepare_source' => null,
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => null,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => -1,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
	  <div class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
			<a router="static"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				<img src="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'" />	<span class="movie-mark">\'.$item[\'mark\'].\'</span>
			</a>
			<p class="pic-text"><a router="static" target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
	  </div>
	  \');')
			))?>
	</div>
	
	<div class="movie-ad-btm">
		<script src="<?php echo $this->mSiteCfg['site_root'];?>tangguo/ad0.js"></script>
	</div>
	
	<!-- 电影 -->
	<div class="row">
	  <h4 class="col-xs-4"><a hideFocus="true" href="<?php echo $this->mLinkRouter->router('list', array('id' => 1))?>">电影</a></h4>
	  <!-- 电影 tabs -->
	  <ul class="nav nav-tabs col-xs-8" role="tablist">
		<li role="presentation" class="active"><a hideFocus="true" href="#mv-hot" aria-controls="mv-hot" role="tab" data-toggle="tab">最热</a></li>
		<li role="presentation"><a hideFocus="true" href="#mv-new" aria-controls="mv-new" role="tab" data-toggle="tab">最新</a></li>
		<li role="presentation"><a hideFocus="true" href="#mv-love" aria-controls="mv-love" role="tab" data-toggle="tab">爱情</a></li>
		<li role="presentation"><a hideFocus="true" href="#mv-action" aria-controls="mv-action" role="tab" data-toggle="tab">动作</a></li>
		<li role="presentation"><a hideFocus="true" href="#mv-funny" aria-controls="mv-funny" role="tab" data-toggle="tab">喜剧</a></li>
		<li role="presentation"><a hideFocus="true" href="#mv-terror" aria-controls="mv-terror" role="tab" data-toggle="tab">恐怖</a></li>
		<li role="presentation"><a hideFocus="true" href="#mv-science" aria-controls="mv-science" role="tab" data-toggle="tab">科幻</a></li>
		<li role="presentation"><a hideFocus="true" href="#mv-ethic" aria-controls="mv-ethic" role="tab" data-toggle="tab">战争</a></li>
	  </ul>
	</div>	
	
	<div class="row">	
		<!-- 电影 panes -->
		<div class="tab-content col-xs-12">
			<div role="tabpanel" class="tab-pane active" id="mv-hot">
				<ul class="row tab-ul-list">
				<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplate.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => -1,
				'cache_type' => null,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
				\');')
			))?>
				</ul>			    	
			</div>
			<div role="tabpanel" class="tab-pane" id="mv-new">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForUpdate.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
						<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
							<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
								<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
								<span class="movie-mark">\'.$item[\'mark\'].\'</span>
							</a>
							<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
						</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="mv-love">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT), ":tag" => array("爱情", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="mv-action">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT), ":tag" => array("动作", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true" class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="mv-funny">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT), ":tag" => array("喜剧", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="mv-terror">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT), ":tag" => array("恐怖", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true" class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="mv-science">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT), ":tag" => array("科幻", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="mv-ethic">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(1, PDO::PARAM_INT), ":tag" => array("战争", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			
			<div class="movie-ad-btm">
				<script src="<?php echo $this->mSiteCfg['site_root'];?>tangguo/ad0.js"></script>
			</div>		
			
		</div>
		
		<!-- 电影 panes -->
	</div>
	
	<div class="row">
	  <h4 class="col-xs-4"><a hideFocus="true"href="<?php echo $this->mLinkRouter->router('list', array('id' => 2))?>">电视剧</a></h4>
	  <!-- 电视剧 tabs -->
	  <ul class="nav nav-tabs col-xs-8" role="tablist">
	    <li role="presentation" class="active"><a hideFocus="true"href="#tv-hot" aria-controls="tv-hot" role="tab" data-toggle="tab">最热</a></li>
	    <li role="presentation"><a hideFocus="true" href="#tv-new" aria-controls="tv-new" role="tab" data-toggle="tab">最新</a></li>
	    <li role="presentation"><a hideFocus="true" href="#inland-tv" aria-controls="inland-tv" role="tab" data-toggle="tab">内地</a></li>
	    <li role="presentation"><a hideFocus="true" href="#hong-tv" aria-controls="hong-tv" role="tab" data-toggle="tab">港剧</a></li>
	    <li role="presentation"><a hideFocus="true" href="#tai-tv" aria-controls="tai-tv" role="tab" data-toggle="tab">台剧</a></li>
	    <li role="presentation"><a hideFocus="true" href="#han-tv" aria-controls="han-tv" role="tab" data-toggle="tab">韩剧</a></li>
	    <li role="presentation"><a hideFocus="true" href="#tai-mv" aria-controls="tai-mv" role="tab" data-toggle="tab">泰剧</a></li>
	    <li role="presentation"><a hideFocus="true" href="#amer-tv" aria-controls="amer-tv" role="tab" data-toggle="tab">美剧</a></li>
	  </ul>

    </div>
	
		
	<div class="row">	
		<!-- 电视剧 panes -->
		<div class="tab-content col-xs-12">

		    <div role="tabpanel" class="tab-pane active" id="tv-hot">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplate.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => -1,
				'cache_type' => null,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'" />	
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>   
					\');')
			))?>			
				</ul>			    	
		    </div>
		    <div role="tabpanel" class="tab-pane" id="tv-new">
		    	<ul class="row tab-ul-list">
		    		<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForUpdate.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'		
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'" />	
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					
					\');')
			))?>	
				</ul>
		    </div>
		    <div role="tabpanel" class="tab-pane" id="inland-tv">
		    	<ul class="row tab-ul-list">
		    		<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT), ":area" => array("大陆", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>	
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>	
				</ul>
		    </div>
		    <div role="tabpanel" class="tab-pane" id="hong-tv">
		    	<ul class="row tab-ul-list">
		    		<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT), ":area" => array("香港", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>	
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
		    </div>
		    <div role="tabpanel" class="tab-pane" id="tai-tv">
		    	<ul class="row tab-ul-list">
		    		<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT), ":area" => array("台湾", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>	
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
		    </div>
		    <div role="tabpanel" class="tab-pane" id="han-tv">
		    	<ul class="row tab-ul-list">
		    		<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT), ":area" => array("韩国", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'" />	
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
		    </div>
		    <div role="tabpanel" class="tab-pane" id="tai-mv">
		    	<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT), ":area" => array("泰国", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" \'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
		    </div>
		    <div role="tabpanel" class="tab-pane" id="amer-tv">
		    	<ul class="row tab-ul-list">
		    		<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(2, PDO::PARAM_INT), ":area" => array("美国", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" \'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
				  			<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
				  			<span class="movie-mark">\'.$item[\'mark\'].\'</span>
				  		</a>
				  		<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>	
				</ul>
		    </div>
		    <div class="movie-ad-btm">
				<script src="<?php echo $this->mSiteCfg['site_root'];?>tangguo/ad0.js"></script>
			</div>	
		  <!--电视剧 panes -->
		</div>
	</div>
	
	<!-- 动漫 -->
		
	<div class="row">
	  <h4 class="col-xs-4"><a hideFocus="true" href="<?php echo $this->mLinkRouter->router('list', array('id' => 3))?>">动漫</a></h4>
	  <!-- 动漫 tabs -->
	  <ul class="nav nav-tabs col-xs-8" role="tablist">
		<li role="presentation" class="active"><a hideFocus="true" href="#comic-hot" aria-controls="comic-hot" role="tab" data-toggle="tab">最热</a></li>
		<li role="presentation"><a hideFocus="true" href="#comic-new" aria-controls="comic-new" role="tab" data-toggle="tab">搞笑</a></li>
		<li role="presentation"><a hideFocus="true" href="#comic-inland" aria-controls="comic-inland" role="tab" data-toggle="tab">国产</a></li>
		<li role="presentation"><a hideFocus="true" href="#comic-jpan" aria-controls="comic-jpan" role="tab" data-toggle="tab">日韩</a></li>
		<li role="presentation"><a hideFocus="true" href="#comic-amer" aria-controls="comic-amer" role="tab" data-toggle="tab">美国</a></li>
		<li role="presentation"><a hideFocus="true" href="#comic-child" aria-controls="comic-child" role="tab" data-toggle="tab">热血</a></li>
		<li role="presentation"><a hideFocus="true" href="#comic-grid" aria-controls="comic-grid" role="tab" data-toggle="tab">少女</a></li>
		<li role="presentation"><a hideFocus="true" href="#comic-machine" aria-controls="comic-machine" role="tab" data-toggle="tab">机战</a></li>
	  </ul>

	</div>
	
	<div class="row">	
		<!-- 动漫 panes -->
		<div class="tab-content col-xs-12">
			<div role="tabpanel" class="tab-pane active" id="comic-hot">
				<ul class="row tab-ul-list">
				<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplate.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => -1,
				'cache_type' => null,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
				\');')
			))?>	
				</ul>			    	
			</div>
			<div role="tabpanel" class="tab-pane" id="comic-new">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT), ":tag" => array("搞笑", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'		
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="comic-inland">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT), ":area" => array("大陆", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'		
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="comic-jpan">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT), ":area" => array("日本", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'		
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="comic-amer">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplateForArea.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT), ":area" => array("美国", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'		
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="comic-child">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT), ":tag" => array("热血", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="comic-grid">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT), ":tag" => array("美少女", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="comic-machine">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(3, PDO::PARAM_INT), ":tag" => array("机战", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			
			<div class="movie-ad-btm">
				<script src="<?php echo $this->mSiteCfg['site_root'];?>tangguo/ad0.js"></script>
			</div>		
			
		  </div>
		<!-- 动漫 panes -->
	</div>
	<!-- 动漫 -->
	
	<!-- 综艺  -->
	
	<div class="row">
	  <h4 class="col-xs-4"><a hideFocus="true" href="<?php echo $this->mLinkRouter->router('list', array('id' => 4))?>">综艺</a></h4>
	  <!-- 综艺 tabs -->
	  <ul class="nav nav-tabs col-xs-8" role="tablist">
		<li role="presentation" class="active"><a hideFocus="true" href="#art-hot" aria-controls="art-hot" role="tab" data-toggle="tab">最热</a></li>
		<li role="presentation"><a hideFocus="true" href="#art-new" aria-controls="art-new" role="tab" data-toggle="tab">搞笑</a></li>
		<li role="presentation"><a hideFocus="true" href="#art-person" aria-controls="art-person" role="tab" data-toggle="tab">真人秀</a></li>
		<li role="presentation"><a hideFocus="true" href="#art-funny" aria-controls="art-funny" role="tab" data-toggle="tab">脱口秀</a></li>
		<li role="presentation"><a hideFocus="true" href="#art-friend" aria-controls="art-friend" role="tab" data-toggle="tab">美食</a></li>
		<li role="presentation"><a hideFocus="true" href="#art-record" aria-controls="art-record" role="tab" data-toggle="tab">旅游</a></li>
		<li role="presentation"><a hideFocus="true" href="#art-football" aria-controls="art-football" role="tab" data-toggle="tab">访谈</a></li>
		<li role="presentation"><a hideFocus="true"href="#art-basket" aria-controls="art-basket" role="tab" data-toggle="tab">纪实</a></li>
	  </ul>
	</div>
		
	<div class="row">	
		<!-- 综艺 panes -->
		<div class="tab-content col-xs-12">
			<div role="tabpanel" class="tab-pane active" id="art-hot">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTemplate.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => -1,
				'cache_type' => null,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'		
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>	
				</ul>			    	
			</div>
			<div role="tabpanel" class="tab-pane" id="art-new">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT), ":tag" => array("搞笑", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif"  data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="art-person">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT), ":tag" => array("真人秀", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="art-funny">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT), ":tag" => array("脱口秀", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'	
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="art-friend">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT), ":tag" => array("美食", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="art-record">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT), ":tag" => array("旅游", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="art-football">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT), ":tag" => array("访谈", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank"  href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane" id="art-basket">
				<ul class="row tab-ul-list">
					<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$listSqlTempalteForTag.'',
				'include_union' => null,
				'prepare_source' => array(":tp" => array(4, PDO::PARAM_INT), ":tag" => array("纪实", PDO::PARAM_STR)),
				'is_page' => false,
				'page' => null,
				'pagesize' => null,
				'offset' => 0,
				'limit' => 12,
				'cache_callback' => $this->mCacheCallback,
				'cache_time' => $this->mCacheTime,
				'cache_type' => $this->mCacheType,
				'cache_id_suffix' => '',
				'is_empty_cache' => false,
				'id' => null,
				'empty_str' => '',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<li class="col-md-2 col-sm-4 col-xs-6  col-xxs-3">
						<a hideFocus="true"  class="pic"  target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'" title="\'.$item[\'name\'].\'">
							<img class="img-responsive"  src="/themes/images/blank.gif" data-original="\'.(Ttkvod_Utils::getImgPath($item[\'vedioPic\'])).\'" alt="\'.$item[\'name\'].\'"/>
							<span class="movie-mark">\'.$item[\'mark\'].\'</span>
						</a>
						<p class="small-pic-text"><a hideFocus="true" target="_blank" href="\'.(Ttkvod_Utils::UR(\'item\', array(\'id\' => $item[\'id\']))).\'">\'.$item[\'name\'].\'</a></p>
					</li>
					\');')
			))?>
				</ul>
			</div>
			
			<div class="movie-ad-btm">
				<script src="<?php echo $this->mSiteCfg['site_root'];?>tangguo/ad0.js"></script>
			</div>	
			
		  </div>
		 <!-- 综艺 panes -->
	</div>
	
	<!-- 综艺 -->

</div>
<?php include $this->mView->load("bottom_f");?>

<div id="tips" class="tips"></div>

<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="//cdn.bootcss.com/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
<script src="<?php echo $this->mSiteCfg['site_root'];?>themes/js/config.js"></script>
<script src="<?php echo $this->mSiteCfg['site_root'];?>themes/js/index.js"></script>
<script>
	BC.action.index();
</script>
</body>
</html>
			
				