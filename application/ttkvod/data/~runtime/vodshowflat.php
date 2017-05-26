<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="gb2312">
	<meta name="shenma-site-verification" content="5f545f609549e1609ffc80987225d712_1486191571"> 
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit|ie-stand" />
	<title><?php echo $info['name'];?>���߲���, <?php echo $info['name'];?>���߹ۿ�, <?php echo $info['name'];?>������Կ�</title>
	<meta name="keywords" content="<?php echo $info['name'];?><?php echo $typename;?>��<?php echo $info['name'];?>���������棬<?php echo $info['name'];?>���߹ۿ�" />
	<meta name="description" content="<?php echo str_replace(array('<p>', '</p>', '">'), '', ($info['content'])); ?>" />
	
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
	
		<ol class="breadcrumb">
			��ǰλ�ã�
		  <li><a href="/">��ҳ</a></li>
		  <li><a href="<?php echo $this->mLinkRouter->router('list', array('id' => $info['type']))?>"><?php echo $typename;?></a></li>
		  <li class="active"><?php echo $info['name'];?></li>
		</ol>
		
		<div class="movie-info">
			<div class="movie-info-list row">
				<!-- ��Ƶ���� -->
				<div class="main-info col-md-8 col-xs-12">
					<div class="row">
					
						<div class="main-info-pic col-md-3">
							<img src="<?php echo Ttkvod_Utils::getImgPath($info['vedioPic']) ?>" alt="<?php echo $info['name'];?>"/>
						</div>
						
						<div class="col-md-6">
							<div class="main-title"><h1><?php echo $info['name'];?></h1></div>
							<div class="main-tags">
								<span>��ӳ���: </span><?php echo $info['vedioYear'];?> &nbsp;&nbsp;&nbsp;<span id="mark">״̬: </span><?php echo $info['mark'];?>
							</div>
							<div class="main-tags">
								<span>����:</span>
								<?php Ttkvod_Utils::randerTagHtml($directors, 0)?>
							</div>
							<div class="main-tags">
								<span>����:</span>
								<?php Ttkvod_Utils::randerTagHtml($actors, 0)?>
							</div>
							<div class="main-tags">
								<span>����:</span>
								<?php Ttkvod_Utils::randerTagHtml($tags, 0)?> 
							</div>
							<div class="main-tags">
								<span>����:</span><?php echo $info['area'];?>
							</div>
							<div class="main-comment">
								<span class="update-time">����:</span> <?php echo substr($info['date'], 0, 19); ?>
							</div>
							<div class="main-point">
								<span>�������֣�</span>
								<div class="main-starbox" id="point_stars">
									<a href="#" title="2�� ̫����" hidefocus="true" class="s10">1</a>
									<a href="#" title="4�� һ�������" hidefocus="true" class="s20">2</a>
									<a href="#" title="6�� ������Ŷ~" hidefocus="true" class="s30">3</a>
									<a href="#" title="8�� ֵ��һ��!" hidefocus="true" class="s40">4</a>
									<a href="#" title="10�� һ��Ҫ��!" hidefocus="true" class="s50">5</a>
								</div>
							</div>
							<div class="main-point">
								<span>���ۣ�</span><a href="#com-flag" id="btn-want-commented" hidefocus="true">��Ҫ����(<b id="comm-num">0</b>)</a>
							</div>
						</div>
						
						<div class="movie-point col-md-3">
							<div class="movie-point-info"><strong id="point_cont"><?php echo $points[0];?><em>.<?php echo $points[1];?></em></strong>��</div>
							<div>(<span id="point_num"><?php echo $info['pointNum'];?></span>������)</div>						
						</div>
					</div>
					
				</div>
				
				<!--��� -->
				<div class="movie-box-ad col-md-4 col-xs-12">
					<script src="<?php echo $this->mSiteCfg['site_root'];?>tangguo/ad2.js"></script>
				</div>
			</div>
			
			<div class="movie-desc">
				<div class="movie-desc-title"><h4>�������</h4></div>
				<p class="movie-desc-info">
					<?php
						$info['content'] = $this->filterContent($info['content']);
						$desclen = Lamb_Utils::mbLen($info['content']);
						$maxdesclen = 185;
						if ($desclen >= $maxdesclen) {
					?>
					<span><?php echo Lamb_Utils::mbSubstr($info['content'], 0, $maxdesclen)?>...</span>
					<span class="off"><?php echo $info['content'];?></span>
					<?php } else {?>
					<span><?php echo $info['content'];?></span>
					<?php }?>
					
					<?php
					if ($desclen >= $maxdesclen) { ?>
						<a href="#" hideFocus="true" id="btn_desc">չ��</a>	
					<?php } ?>
				</p>
			</div>
				
			<!-- �����б� -->
			<div class="movie-playlist">
	
				<div class="pl-inner">
					<div class="pl-inner">
						<script><?php echo $playData;?>;g_VideoName='<?php echo $this->addslashes($info['name']);?>'</script>	
						<div class="p-links clearfix" id="p-links">��������...</div>
					</div>
					<div class="p-t-menus" id="p-t-menus"></div>
				</div>
			</div>
		
			<!-- ���-->
			<div class="movie-ad"></div>

			<!--
				����ϲ��
			-->
			<div class="movie-likes">
				  <!-- nav -->
				  <ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#maybelikes" aria-controls="maybelikes" role="tab" data-toggle="tab">����ϲ��</a></li>
				  </ul>
				
				  <!-- Tab panes -->
				  <div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="maybelikes">
						<ul class="row m-likes-list clearfix">
							<?php foreach ($referLists as $item) {?>
							<li class="col-md-2 col-xs-6 col-xxs-3">
								<a title="<?php echo $item['name'];?>" router="static" href="<?php echo $this->mLinkRouter->router('item', array('id' => $item['id']))?>" class="pic">
								<img src="/themes/images/blank.gif" data-original="<?php echo Ttkvod_Utils::getImgPath($item['vedioPic'])?>" ></a>
								<p class="pic-txt"><a router="static" href="<?php echo $this->mLinkRouter->router('item', array('id' => $item['id']))?>"><?php echo $item['name'];?></a></p>
							</li>
							<?php }?>		
						</ul>
					</div>
				  </div>
			</div>
		
			<div class="movie-comment">
				<!-- Nav ���� -->
				  <ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#comment-new" aria-controls="comment-new" role="tab" data-toggle="tab">��������</a></li>
					<li role="presentation"><a href="#comment-hot" aria-controls="comment-hot" id="tab_comment_hot" role="tab" data-toggle="tab">��������</a></li>
				  </ul>
				
				  <!-- Tab panes -->
				  <div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="comment-new">
							<div class="movie-com-body" id="com_body">
								<div id="_warp"></div>
								<p class="w-more" id="loading">���ڼ���...</p>
							</div>
						</div>	
					
						<div role="tabpanel" class="tab-pane" id="comment-hot">
							<div class="movie-com-body" id="com_hot_body">
								<div id="_warp_hot"></div>
								<p class="w-more" id="loading_hot">���ڼ���...</p>
							</div>
						</div>
				  </div>
				  
				  <div class="com-sub-frm">
					<form method="post" id="comment_frm"><a name="com-flag"></a>
						<textarea name="com_cont"></textarea>
						<div class="c-f-bar">
							<input type="submit" class="c-f-submit" value="����" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="tips" class="tips"></div>
	<?php include $this->mView->load("bottom_f");?>
	
</body>

<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="//cdn.bootcss.com/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
<script src="/themes/layer/layer.js"></script>
<script src="/themes/js/config.js"></script>
<script src="/themes/js/index.js"></script>

<script>
	BC.action.index();
	BC.action.item(<?php echo $info['id'];?>, <?php echo $info['type'];?>, '<?php echo $info['name'];?>');
</script>
</html>