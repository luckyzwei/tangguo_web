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
window.onload=function()
{
	var oTable=$A('distinction');
	var aTdObjItem=oTable.getElementsByTagName('tr');
 
	for (var i = 1,j = aTdObjItem.length-2 ;i < j;i++)
	{
		aTdObjItem[i].style.backgroundColor = ( i % 2 ) == 0? '#d9ebf5' : '#eee';
	}
}
function showEditDialog(index)
{
	var items = document.getElementsByName('item_' + index);
	var str='';
	str+='<div class="Diago">';
	str+='<form method="post" name="editform" target="opFrame" action="<?php echo $this->mRouter->urlEx('member', 'update')?>">';
	str+='<input type="hidden" name="uid" value="' + items[0].innerHTML + '"/><input type="hidden" name="ac" value="<?php echo $action;?>"/>';
	<?php if ($action != 'old') {?>
	str+='<p>�û�����<input type="text" name="username" value="'  + items[1].innerHTML + '"/></p>';
	str+='<p>�� &nbsp; �䣺<input type="text" name="email" value="' + items[2].innerHTML + '"/></p>'
	<?php } else {?>
	str+='<p>�û�����'  + items[1].innerHTML + '</p>';
	<?php }?>
	str+='<p>�� &nbsp; �룺<input type="password" name="password"/></p>';
	str+='<p><input type="submit" name="createSubmit" value="�޸�" class="formbtn"/> &nbsp; &nbsp; &nbsp; ';
	str+='<input type="button" class="formbtn" onclick="showEditDialog.close()" value="�ر�"/></p>';
	str+='</form>'
	str+='</div>';
	var dialog = new $B.extend.CDialog({
		'width' : 300,
		'html' : str
	});
	dialog.showModal();
	showEditDialog.close = function(){
		dialog.close();
	}
	return str;	
}
function importDialog(index)
{
	var items = document.getElementsByName('item_' + index);
	var str='';
	str+='<div class="Diago">';
	str+='<form method="post" name="editform" target="opFrame" action="<?php echo $this->mRouter->urlEx('member', 'import')?>">';
	str+='<input type="hidden" name="uid" value="' + items[0].innerHTML + '"/>';
	str+='<p>�û�����'  + items[1].innerHTML + '</p>';
	str+='<p>�� &nbsp; �䣺<input type="text" name="email" value=""/></p>'
	str+='<p><input type="submit" name="createSubmit" value="����" class="formbtn"/> &nbsp; &nbsp; &nbsp; ';
	str+='<input type="button" class="formbtn" onclick="importDialog.close()" value="�ر�"/></p>';
	str+='</form>'
	str+='</div>';
	var dialog = new $B.extend.CDialog({
		'width' : 300,
		'html' : str
	});
	dialog.showModal();
	importDialog.close = function(){
		dialog.close();
	}
	return str;		
}
function changeShow(index,value,id)
{
	$B.ajax({
		'url'		:	Router.get('member', 'ajax', {'ajaxAction' : 'newstatus', 'id' : id, 'value' : value == 1 ? 0 : 1, 'tt' : new Date().valueOf()}),
		'success'	:	function(s)	
		{
			if(s == 'succ')	window.location.reload();
		}
	});
}
</script>
</head>
<body>
<div id="rightTop" style="height:85px;">
	<p><?php if ($action == 'old') {?>�ɻ�Ա����<?php } else {?>��Ա����<?php }?></p>
	<ul class="subnav">
		<li><span>����</span></li>
		<li><a href="javascript:_global.cover.create(300,showCreateDiago())" class="btn1">������Ա</a></li>
	</ul>
	<p style="clear:both;">
		<form name="search">
			<input type="hidden" name="ac" value="<?php echo $action;?>"/>
			������
			<input type="text" name="keywords" value="<?php echo $keywords;?>" size="60"/>
			<select name="query">
				<option value="">ѡ��������ʽ</option>
				<option value="id" <?php echo $sels[0];?>>����ID����</option>
				<option value="username" <?php echo $sels[1];?>>�����û�������</option>
				<option value="email" <?php echo $sels[2];?>>����Email</option>
			</select>
			״̬��
			<select name="status">
				<option value="">ѡ��״̬</option>
				<option value="1" <?php if($status=='1'){?>selected="selected"<?php }?>>����</option>
				<option value="0" <?php if($status=='0'){?>selected="selected"<?php }?>>����</option>				
			</select>
			<input type="submit" class="formbtn" value="����"/>
		</form>	
		<script>Router.bindForm(true, 'member', '', document.search)</script>
	</p>
</div>
<div class="info2" style="color:#444">
	<table class="distinction" id="distinction">
		<thead>
			<tr>
				<th class="w70"><input id="selectAll" onclick="checkAll('id[]')" type="checkbox" /> <label for="selectAll">ȫѡ</label></th>
				<th width="5%">UID</th>
				<th width="10%">�û���</th>
				<th width="10%">Email</th>
				<th width="5%">״̬</th>
				<th width="15%">ע��ʱ��</th>
				<th width="15%">ע��IP</th>
				<th width="15%">�ϴε�¼ʱ��</th>
				<th width="15%">�ϴε�¼IP</th>
				<th width="5%">����</th>
			</tr>
		</thead>
		<tbody>
			<form method="post" name="deleForm" action="<?php echo $this->mRouter->urlEx('member', 'delete', array('ac' => $action))?>">
			<?php if ($action == 'old') {?>
			<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$sql.'',
				'include_union' => null,
				'prepare_source' => $aPrepareSource,
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
				'empty_str' => '<tr><td colspan="12" style="padding:20px">�Բ�����������</td></tr>',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<tr>
						<td><input type="checkbox" name="id[]" value="\'.$item[\'uid\'].\'"/></td>
						<td name="item_#autoIndex#" id="item_#autoIndex#">\'.$item[\'uid\'].\'</td>
						<td name="item_#autoIndex#" id="item_#autoIndex#">\'.$item[\'username\'].\'</td>
						<td>\'.$item[\'email\'].\'</td>
						<td><span id=\\\'cont_#autoIndex#\\\' onclick=\\\'changeShow(#autoIndex#,\'.$item[\'status\'].\',\'.$item[\'uid\'].\')\\\'>\'.($item[\'status\'] ? \'<font class="js_ok">����</font\':\'<font class="js_error">����</font>\').\'</span></td>
						<td>\'.(date(\'Y-m-d H:i:s\', $item[\'registerTime\'])).\'</td>
						<td>\'.$item[\'regip\'].\'</td>
						<td>\'.(date(\'Y-m-d H:i:s\', $item[\'loginTime\'])).\'</td>
						<td>\'.$item[\'loginip\'].\'</td>
						<td><a href="javascript:void(0)" onclick="importDialog(#autoIndex#)">����</a> <a href="javascript:void(0)" onclick="showEditDialog(#autoIndex#)">�༭</a></td>
					</tr>
			\');')
			))?>			
			<?php } else {?>
			<?php Lamb_View_Tag_List::main(array(
				'sql' => ''.$sql.'',
				'include_union' => null,
				'prepare_source' => $aPrepareSource,
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
				'empty_str' => '<tr><td colspan="12" style="padding:20px">�Բ�����������</td></tr>',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<tr>
						<td><input type="checkbox" name="id[]" value="\'.$item[\'uid\'].\'"/></td>
						<td name="item_#autoIndex#" id="item_#autoIndex#">\'.$item[\'uid\'].\'</td>
						<td name="item_#autoIndex#" id="item_#autoIndex#">\'.$item[\'username\'].\'</td>
						<td name="item_#autoIndex#" id="item_#autoIndex#">\'.$item[\'email\'].\'</td>
						<td><span id=\\\'cont_#autoIndex#\\\' onclick=\\\'changeShow(#autoIndex#,\'.$item[\'status\'].\',\'.$item[\'uid\'].\')\\\'>\'.($item[\'status\'] ? \'<font class="js_ok">����</font\':\'<font class="js_error">����</font>\').\'</span></td>
						<td>\'.(date(\'Y-m-d H:i:s\', $item[\'registerTime\'])).\'</td>
						<td>\'.$item[\'regip\'].\'</td>
						<td>\'.($item[\'loginTime\'] ? date(\'Y-m-d H:i:s\', $item[\'loginTime\']) : \'\').\'</td>
						<td>\'.$item[\'loginip\'].\'</td>
						<td><a href="javascript:void(0)" onclick="showEditDialog(#autoIndex#)">�༭</a></td>
					</tr>
			\');')
			))?>
			<?php }?>
			</form>
			<?php Lamb_View_Tag_Page::page(array(
			'page_num'		=>	9,
			'page_style'	=>	1,
			'listid'		=>	'list',
			'page_start_html'=>	'
				<tr><td colspan="10"><div id="pageDiv"><span class="msg">��#num#������ ��ǰ#currentPage#ҳ</span><span class="page">
				<a href="'.$pageUrl.'1" class="nofocus">��ҳ</a><a href="'.$pageUrl.'#prevPage#" class="nofocus">��һҳ</a>
				',
			'page_end_html'	=>	'
				<a href="'.$pageUrl.'#nextPage#" class="nofocus">��һҳ</a><a href="'.$pageUrl.'#lastPage#" class="nofocus">βҳ</a>
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
				<td class="w70"><input id="selectAll" onclick="checkAll('id[]')" type="checkbox" /> <label for="selectAll">ȫѡ</label></td>
				<td colspan="9"><input class="formbtn" type="button" onclick="return (confirm('ȷ��ɾ����')?document.deleForm.submit():false)" value="ɾ��" /></td>
			</tr>
		</tfoot>		
	</table>
</div>
<iframe name="opFrame" id="opFrame" height="0" width="0"></iframe>
</body>
</html>