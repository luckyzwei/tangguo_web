<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="gb2312">
	<meta name="shenma-site-verification" content="5f545f609549e1609ffc80987225d712_1486191571"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit|ie-stand" />
	<title><?php echo $info['name'];?>在线播放,<?php echo $info['name'];?>在线观看, <?php echo $info['name'];?>哪里可以看</title>
	<meta name="keywords" content="<?php echo $info['name'];?><?php echo $typename;?>，<?php echo $info['name'];?>高清完整版，<?php echo $info['name'];?>在线观看" />
	<meta name="description" content="<?php echo str_replace('">', '', ($info['content'])); ?>" />
	
	<script type="text/javascript" src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript" src="/ckplayer/ckplayer.js" charset="utf-8"></script>
	<script type="text/javascript" src="/themes/js/encrypt.js" charset="utf-8"></script>
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
					<li><a hideFocus="true"  router='static'<?php if(empty($area)){?> class="current"<?php }?> href="<?php echo $this->mLinkRouter->router('list', array('id' => $id))?>">全部</a></li>
						<?php foreach ($currentSearchIndex['areas'] as $item) { 
							$link = $this->mLinkRouter->router('list', array('id' => $id, 'area' => $item));
							$class = '';
						?>
					<li><a hideFocus="true"  router='static' href='<?php echo $link;?>' <?php echo $class;?>><?php echo $item;?></a></li>
					<?php }?>					
				</ul>
				
				<div class="hot-tags"><i class="glyphicon glyphicon-tags"></i><h3>热门标签</h3></div>
				
				<ul class="category-middle clearfix">
						<?php foreach ($currentSearchIndex['types'] as $type) {
							$_type = $type;
							$class = '';
							$link = $this->mLinkRouter->router('list', array('id' => $id, 'tag' => $type));
						?>
						<li <?php echo $class;?>><a hideFocus="true"  router='static' title="<?php echo $type;?>" href="<?php echo $link;?>"><?php echo $type;?></a></li>
						<?php }?>
				</ul>
				
				<ol class="breadcrumb">
					当前位置：
				  <li><a href="/">首页</a></li>
				  <li><a href="<?php echo $this->mLinkRouter->router('list', array('id' => $info['type']))?>"><?php echo $typename;?></a></li>
				  <li class="active"><?php echo $info['name'];?></li>
				</ol>
				
			</div>
			
			<div class="col-md-11 col-md-offset-1">
				<div id="a1" class="third-player"></div>
			</div>
			
			<!-- 播放列表 -->
			<div class="col-md-12 movie-info">
				<div class="movie-playlist">
					<div class="pl-inner">
						<div class="pl-inner">
							<script><?php echo $playData;?>;g_VideoName='<?php echo $this->addslashes($info['name']);?>'</script>	
							<div class="p-links clearfix" id="p-links">正在载入...</div>
						</div>
						<div class="p-t-menus" id="p-t-menus"></div>
					</div>
				</div>
			</div>
			
			<!-- 广告-->
			<div class="movie-ad"></div>

			<!--
				猜你喜欢
			-->
			<div class="movie-likes">
				  <!-- nav -->
				  <ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#maybelikes" aria-controls="maybelikes" role="tab" data-toggle="tab">猜你喜欢</a></li>
				  </ul>
				
				  <!-- Tab panes -->
				  <div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="maybelikes">
						<ul class="row m-likes-list clearfix">
							<?php foreach ($referLists as $item) {?>
							<li class="col-md-2 col-xs-6 col-xxs-3">
								<a title="<?php echo $item['name'];?>" router="static" href="<?php echo $this->mLinkRouter->router('item', array('id' => $item['id']))?>" class="pic">
								<img src="themes/images/blank.gif" data-original="<?php echo Ttkvod_Utils::getImgPath($item['vedioPic'])?>" ></a>
								<p class="pic-txt"><a router="static" href="<?php echo $this->mLinkRouter->router('item', array('id' => $item['id']))?>"><?php echo $item['name'];?></a></p>
							</li>
							<?php }?>		
						</ul>
					</div>
				  </div>
			</div>
			
			<div class="movie-comment">
				<!-- Nav 评论 -->
				  <ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#comment-new" aria-controls="comment-new" role="tab" data-toggle="tab">最新评论</a></li>
					<li role="presentation"><a href="#comment-hot" aria-controls="comment-hot" id="tab_comment_hot" role="tab" data-toggle="tab">最热评论</a></li>
				  </ul>
				
				  <!-- Tab panes -->
				  <div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="comment-new">
							<div class="movie-com-body" id="com_body">
								<div id="_warp"></div>
								<p class="w-more" id="loading">正在加载...</p>
							</div>
						</div>	
					
						<div role="tabpanel" class="tab-pane" id="comment-hot">
							<div class="movie-com-body" id="com_hot_body">
								<div id="_warp_hot"></div>
								<p class="w-more" id="loading_hot">正在加载...</p>
							</div>
						</div>
				  </div>
				  
				  <div class="com-sub-frm">
					<form method="post" id="comment_frm"><a name="com-flag"></a>
						<textarea name="com_cont"></textarea>
						<div class="c-f-bar">
							<input type="submit" class="c-f-submit" value="发表" />
						</div>
					</form>
				</div>
			</div>
	
		</div>
	</div>
	<?php include $this->mView->load("bottom_f");?>
	
	<div id="tips" class="tips"></div>

<script src="//cdn.bootcss.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
<script src="/themes/js/config.js"></script>
<script src="/themes/js/index.js"></script>
<script>
	BC.action.index();
	BC.action.item(<?php echo $info['id'];?>, <?php echo $info['type'];?>, '<?php echo $info['name'];?>', 0);
	BC.action.playItem(<?php echo $info['id'];?>, <?php echo $info['type'];?>, '<?php echo $info['name'];?>');
	BC.action.player(<?php echo $info['id'];?>, <?php echo $num;?>);	
</script>
</body>
</html>