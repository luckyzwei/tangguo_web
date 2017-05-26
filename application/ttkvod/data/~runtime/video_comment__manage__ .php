<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=gbk" />
<link href="<?php echo $this->mThemePath;?>css/admin.css" rel="stylesheet" />
<link href="<?php echo $this->mThemePath;?>css/sub_page.css" rel="stylesheet" />
<script src="<?php echo $this->mSiteRoot;?>api/lamb.js"></script>
<script src="<?php echo $this->mRouter->urlEx('index', 'loadjsconfig')?>"></script>
<script src="<?php echo $this->mSiteRoot;?>api/global.js"></script>
<style> 
.input_text{
	width:30px;
}
ul,ul li{
	list-style:none;
}
.msg {
	margin-top:10px;
}
.msg li{
	margin-bottom:10px;
	margin-left:15px;
	margin-right:15px;
	border: 1px solid #ccc;
}
.msg li .title{
	height:25px;
	font-size:14px;
	background:#eee;
	line-height:25px;
	padding-left:15px;
	border-bottom:1px solid #ccc;
	color:#444;
	position:relative;
}
.date{
	font-size:12px;
	margin-left:10px;
	color:#999;
}
.msg li .content{
	line-height:22px;
	padding-left:15px;
	color:#666;
}
a.repeat{
	position:absolute;
	right:10px;
	top:2px;
	font-size:12px;
}
a.dele{
	position:absolute;
	right:70px;
	top:2px;
	font-size:12px;
}
a.show{
	position:absolute;
	right:90px;
	top:2px;
	font-size:12px;	
}
a.status{
	position:absolute;
	right:150px;
	top:2px;
	font-size:12px;	
}
.sNone{
	height:50px;
	line-height: 50px;
	text-indent:20px;
}
.updateDiago{
	width:400px;
	border:2px solid #ccc;
	background:#fff;
}
.updateDiago p{
	margin:auto 5px;
	color:#444;
}
</style>
</head>
<body>
<div id="rightTop" style="height:85px;">
	<p>评论管理</p>
	<ul class="subnav">
		<li><span>管理</span></li>
	</ul>
	<p style="clear:both;">
		<form name="search">
			指定影片ID：<input type="text" name="vid" value="<?php echo $vid;?>" size="10"/>
			内容：<input type"text" name="cont" value="<?php echo $content;?>"/>
			IP: <input type="text" name="ip" value="<?php echo $ip;?>" />
			用户名：<input type="text" name="name" value="<?php echo $username;?>"/>
			<input type="submit" class="formbtn" value="搜索"/>
		</form>	
		<script>Router.bindForm(true, 'video', 'comment', document.search)</script>
	</p>
</div>
<ul class="msg">
	<form name="deleform" action="<?php echo $this->mRouter->urlEx('video', 'commdele')?>" method="post">
	<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$sql.'',
				'include_union' => null,
				'prepare_source' => $aPrepareSource,
				'is_page' => true,
				'page' => $page,
				'pagesize' => 20,
				'offset' => null,
				'limit' => null,
				'cache_callback' => null,
				'cache_time' => null,
				'cache_type' => null,
				'cache_id_suffix' => '',
				'is_empty_cache' => null,
				'id' => 'list',
				'empty_str' => '<li class="sNone">对不起，暂无留言</li>',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
		<li>
		<div class="title"><input type="checkbox" name="id[]" value="\'.$item[\'id\'].\'|\'.$item[\'vedioid\'].\'"/>&nbsp;<a href="\'.(Ttkvod_Utils::U(array(\'name\' => $item[\'username\']), \'comment\', \'video\')).\'">\'.$item[\'username\'].\'</a><span class="date">\'.(date(\'Y-m-d H:i:s\',$item[\'time\'])).\'</span> <span class="date"><font style="color:#444">IP：</font>\'.$item[\'ip\'].\'</span><a class="dele" onclick="return confirm(\\\'确定删除吗？\\\')" href="\'.(Ttkvod_Utils::U(array(\'id\' => $item[\'id\'] . \'|\' . $item[\'vedioid\']), \'commdele\', \'video\')).\'">删除</a> <a class="repeat" href="\'.(Ttkvod_Utils::U(array(\'keywords\' => $item[\'vedioid\'], \'query\' => \'vedioid\'), \'\', \'video\')).\'">查看影片</a></div>
		<p class="content">\'.$item[\'content\'].\'</p>
		</li>
	\');')
			))?>
	</form>
		<?php Lamb_View_Tag_Page::page(array(
			'page_num'		=>	9,
			'page_style'	=>	1,
			'listid'		=>	'list',
			'page_start_html'=>	'
			<li style="border:none;"><div id="pageDiv"><span class="msg"><a href="javascript:checkAll(\'id[]\')">全
选</a> <a href="javascript:confirm(\'确定删除吗?\')?document.deleform.submit():void(0)">删除</a> 共#num#条数据 当前#currentPage#页</span><span class="page"><a href="'.$pageUrl.'1" class="nofocus">首页</a><a href="'.$pageUrl.'#prevPage#" class="nofocus">上一页</a>
				',
			'page_end_html'	=>	'
				<a href="'.$pageUrl.'#nextPage#" class="nofocus">下一页</a><a href="'.$pageUrl.'#lastPage#" class="nofocus">尾页</a>
				</span></span></div></li>
		',
			'more_html'		=>	'',
			'focus_html'	=>	'<a href="'.$pageUrl.'#page#" class="focus">#page#</a>',
			'nofocus_html'	=>	'<a href="'.$pageUrl.'#page#" class="nofocus">#page#</a>',
			'max_page_count' => 0,
			'page' => null,
			'pagesize' => null,
			'data_num' => null
		))?>
</ul>
<iframe name="opFrame" id="opFrame" height="0" width="0"></iframe>
</body>
</html>