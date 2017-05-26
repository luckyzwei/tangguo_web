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
<script>
function changeShow(value,id, a, c)
{
	$B.ajax({
		'url'		:	Router.get('member', 'adminAjax', {'id' : id, 'v' : value == 1 ? 0 : 1, 'a' : a, 'c' : c}),
		'success'	:	function(s)	
		{
			window.location.reload();
		}
	});
}
</script>
<style>
.update, .addParent {
	width:400px;
	height:170px;
	position:absolute;
	left:50%;
	top:50%;
	background-color:#fff;
	margin-left:-200px;
	margin-top:-85px;
	border:1px solid #ccc;
	display:none;
}
.addParent{
	height:300px;
	margin-top:-150px;
	display:block;
}
.update h3, .addParent h3{
	line-height:30px;
	border-bottom:1px solid #ccc;
	background-color:#ecf5ff;
	height:30px;
}
.update h3 a, .addParent h3 a{
	float:right;
	margin-right:10px;
}
.update li, .addParent li{
	margin-top:10px;
}
.update label, .addParent label{
	width:80px;
	display:inline-block;
	text-align:right;
}
.update .text, .addParent .text{
	width:300px;
}
.update li span{
	float:left;
	margin-left:80px;
}
.sub{
	float:right;
	margin-right:15px;
}
.add{
	height:40px;
}
.add .formbtn{
	margin-top:10px;
	margin-left:20px;
}
#headMsg{
	display:block;
	float:left;
	font-size:14px;
	padding-left:5px;
}
</style>
</head>
<body>
	<div class="info2" style="color:#444">
		<div class="add"><input class="formbtn" type="button" id="addBtn" onclick="addUser()" value="添加" /></div>
		<table class="distinction" id="distinction">
		<thead>
			<tr>
				<th class="w70"><input id="selectAll" onclick="checkAll('id[]')" type="checkbox" /> <label for="selectAll">全选</label></th>
				<th width="5%">管理ID</th>
				<th width="10%">用户名</th>
				<th width="10%">管理员权限</th>
				<th width="5%">状态</th>
				<th width="5%">删除权限</th>
				<th width="5%">编辑权限</th>
				<th width="6%">浏览列表</th>
				<th width="15%">注册时间</th>
				<th width="15%">上次登录时间</th>
				<th width="14%">上次登录IP</th>
				<th width="6%">操作</th>
			</tr>
		</thead>
		<tbody>
			<form method="post" name="deleForm" action="<?php echo $this->mRouter->urlEx('member', 'adminAjax')?>/a/d">
				<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$sql.'',
				'include_union' => null,
				'prepare_source' => null,
				'is_page' => true,
				'page' => $page,
				'pagesize' => 30,
				'offset' => null,
				'limit' => null,
				'cache_callback' => null,
				'cache_time' => null,
				'cache_type' => null,
				'cache_id_suffix' => '',
				'is_empty_cache' => null,
				'id' => 'list',
				'empty_str' => '<tr><td colspan="12" style="padding:20px">对不起，暂无数据</td></tr>',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<tr id="tr" name="tr">
						<td><input type="checkbox" name="id[]" value="\'.$item[\'id\'].\'"/></td>
						<td name="item_#autoIndex#" id="item_#autoIndex#">\'.$item[\'id\'].\'</td>
						<td name="item_#autoIndex#" id="item_#autoIndex#">\'.$item[\'name\'].\'</td>
						<td><span id=\\\'cont_#autoIndex#\\\' onclick=\\\'changeShow(\'.$item[\'isAdmin\'].\',\'.$item[\'id\'].\', "u", "isAdmin")\\\'>\'.($item[\'isAdmin\'] ? \'<font class="js_ok">是</font\':\'<font class="js_error">否</font>\').\'</span></td>
						<td><span id=\\\'cont_#autoIndex#\\\' onclick=\\\'changeShow(\'.$item[\'status\'].\',\'.$item[\'id\'].\', "u", "status")\\\'>\'.($item[\'status\'] ? \'<font class="js_ok">正常</font\':\'<font class="js_error">锁定</font>\').\'</span></td>
						<td><span id=\\\'cont_#autoIndex#\\\' onclick=\\\'changeShow(\'.$item[\'isdelete\'].\',\'.$item[\'id\'].\', "u", "isdelete")\\\'>\'.($item[\'isdelete\'] ? \'<font class="js_ok">是</font\':\'<font class="js_error">否</font>\').\'</span></td>
						<td><span id=\\\'cont_#autoIndex#\\\' onclick=\\\'changeShow(\'.$item[\'isedit\'].\',\'.$item[\'id\'].\', "u", "isedit")\\\'>\'.($item[\'isedit\'] ? \'<font class="js_ok">是</font\':\'<font class="js_error">否</font>\').\'</span></td>
						<td><span id=\\\'cont_#autoIndex#\\\' onclick=\\\'changeShow(\'.$item[\'islist\'].\',\'.$item[\'id\'].\', "u", "islist")\\\'>\'.($item[\'islist\'] ? \'<font class="js_ok">是</font\':\'<font class="js_error">否</font>\').\'</span></td>
						<td>\'.(date(\'Y-m-d H:i:s\', $item[\'time\'])).\'</td>
						<td>\'.(date(\'Y-m-d H:i:s\', $item[\'lasttime\'])).\'</td>
						<td>\'.$item[\'lastip\'].\'</td>
						<td><a href="#" onclick="updateUser(\\\'\'.$item[\'name\'].\'\\\', \'.$item[\'id\'].\')">编辑</a> <a href="?s=member/log/aid/\'.$item[\'id\'].\'">查看操作记录</a></td>
					</tr>
				\');')
			))?>
			</form>
			<?php Lamb_View_Tag_Page::page(array(
			'page_num'		=>	9,
			'page_style'	=>	1,
			'listid'		=>	'list',
			'page_start_html'=>	'
				<tr><td colspan="12"><div id="pageDiv"><span class="msg">共#num#条数据 当前#currentPage#页</span><span class="page">
				<a href="'.$pageUrl.'1" class="nofocus">首页</a><a href="'.$pageUrl.'#prevPage#" class="nofocus">上一页</a>
				',
			'page_end_html'	=>	'
				<a href="'.$pageUrl.'#nextPage#" class="nofocus">下一页</a><a href="'.$pageUrl.'#lastPage#" class="nofocus">尾页</a>
				</span></div></td></tr>
			',
			'more_html'		=>	'',
			'focus_html'	=>	'<a href="'.$pageUrl.'#page#" class="focus">#page#</a>',
			'nofocus_html'	=>	'<a href="'.$pageUrl.'#page#" class="nofocus">#page#</a>',
			'max_page_count' => 0,
			'page' => null,
			'pagesize' => null,
			'data_num' => null
		))?>			
		</tbody>
		<tfoot>
			<tr class="tr_pt10">
				<td class="w70"><input id="selectAll" onclick="checkAll('id[]')" type="checkbox" /> <label for="selectAll">全选</label></td>
				<td colspan="9"><input class="formbtn" type="button" onclick="return (confirm('确定删除吗？')?document.deleForm.submit():false)" value="删除" /></td>
			</tr>
		</tfoot>		
	</table>
	</div>
	<div class="update" id="update">
		<h3><span id="headMsg">修改用户信息</span><a href="#" id="closeUpdate">关闭</a></h3>
		<form>
			<ul>
				<li><label>用户名：</label><input type="text" id="uName" class="text" /></li>
				<li><label>密码：</label><input type="text" id="uPassword1" class="text"/></li>
				<li><label>确认密码：</label><input type="text" id="uPassword2" class="text" /></li>
				<li><span id="updateMsg">只修改用户名不需输入密码</span><input type="button" value="确定" class="sub" id="updateBtn"/></li>
			</ul>
		</form>
	</div>
</body>
<script>
var domTrs = document.getElementsByName('tr');
$F.each(domTrs, function(item, index){
	if (index % 2 == 0) {
		item.style.background = "#d9ebf5";
	} else {
		item.style.background = "#eeeeee";
	}
});
$E.addEvent($A('closeUpdate'), 'click', function(){
	$A('update').style.display = 'none';
});
function addUser()
{
	$A('update').style.display = 'block';
	$A('updateMsg').style.display = 'none';
	$A('headMsg').innerHTML = '添加用户';
	$A('uName').value = '';
	
	$A('updateBtn').onclick = function()
	{
		var passValue1 = $A('uPassword1').value,
			passValue2 = $A('uPassword2').value,
			param = {};
			
		if ($F.trim($A('uName').value) == '') {
			return alert('用户名不能为空');
		}
		if ($F.trim(passValue1) == '' || $F.trim(passValue2) == '') {
			return alert('密码不能为空');
		}
		if ($F.trim(passValue1) != $F.trim(passValue2)) {
			return alert('2次密码不一致');
		}
		
		$B.ajax({
			'url'		:	Router.get('member', 'adminAjax', param = {'name' : $A('uName').value, 'a' : 'add', 'p' : passValue1}),
			'success'	:	function(s)	
			{
				if (s == 'succ') {
					alert('添加成功');
					window.location.reload();
				} else {
					alert('添加失败或者用户名重复');
				}
			}
		});
	}
}
function updateUser(name, id)
{
	$A('update').style.display = 'block';
	$A('updateMsg').style.display = 'block';
	$A('uName').value = name;
	$A('headMsg').innerHTML = '修改用户信息';
	$A('updateBtn').onclick = function()
	{
		var passValue1 = $A('uPassword1').value,
			passValue2 = $A('uPassword2').value,
			param = {};
		if ($F.trim($A('uName').value) == '') {
			return alert('用户名不能为空');
		}
		if ($F.trim(passValue1) == '' || $F.trim(passValue2) == '') {
			param = {
				'id' : id,
				'name' : $A('uName').value,
				'a' : 'uu'
			};
		}
		if ($F.trim(passValue1) != $F.trim(passValue2)) {
			return alert('2次密码不一致');
		} else {
			param = {
				'id' : id,
				'name' : $A('uName').value,
				'a' : 'uu',
				'p' : passValue1
			};
		}	 
		
		$B.ajax({
			'url'		:	Router.get('member', 'adminAjax', param),
			'success'	:	function(s)	
			{
				if (s == 'succ') {
					alert('修改成功');
					window.location.reload();
				} else {
					alert('修改失败或者用户名重复');
				}
			}
		});
	}
}
</script>
</html>