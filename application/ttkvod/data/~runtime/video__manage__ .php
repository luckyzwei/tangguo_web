<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<meta http-equiv="X-UA-Compatible" content="IE=edge charset=gbk" />
<link href="<?php echo $this->mThemePath;?>css/admin.css" rel="stylesheet" />
<link href="<?php echo $this->mThemePath;?>css/sub_page.css" rel="stylesheet" />
<link href="<?php echo $this->mThemePath;?>css/source_editor.css" rel="stylesheet" />
<script src="<?php echo $this->mRouter->urlEx('index', 'loadjsconfig')?>"></script>
<script src="<?php echo $this->mSiteRoot;?>api/lamb.js"></script>
<script src="<?php echo $this->mSiteRoot;?>api/global.js"></script>
<script src="<?php echo $this->mSiteRoot;?>api/base.js"></script>
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

var aType = [];
<?php foreach ($this->mSiteCfg['channels'] as $key => $item) {
	echo "aType[" . $key . "] = {id:" . $key . ",name:'" . $item['name'] . "'};";
}
?>

function showType(index)
{
	var sHtml = '<option value="">ѡ������</option>';
	for(var i=1,j=aType.length;i<j;i++)
	{
		sHtml += '<option value="'+aType[i].id+'"'+(index==aType[i].id?' selected="selected"':'')+'>'+aType[i].name+'</option>';
	}
	document.write(sHtml);
}

function showOneType(id)
{
	for(var i=1,j=aType.length;i<j;i++)
	{
		if(aType[i].id==id)
		{
			return aType[i].name;
		}
	}
}

function stort(obj,id,action,className)
{
	className	=	className || 'input_text';
	(new CDynamicAjax({
		'className'	:	className,
		'url'		:	function(value)
		{
			return Router.get('video', 'ajax', {'ajaxAction' : action, 'id' : id, 'value' : value, 'tt' : new Date().valueOf()});
		},
		'success'	:	function(s,l,c)
		{
			obj.innerHTML = s=='succ'?c:l;
		}
	})).create(obj);
}
function changeShow(index,value,id)
{
	var data = {'0' : 1, '1' : 2, '2' : 0};
	value = data[value];
	$B.ajax({
		'url'		:	Router.get('video', 'ajax', {'ajaxAction' : 'islock', 'id' : id, 'value' : value, 'tt' : new Date().valueOf()}),
		'success'	:	function(s)	
		{
			if(s == 'succ')	window.location.reload();
		}
	});
}
function changeShow2(index,value,id)
{
	var data = {'0' : 1, '1' : 0};
	value = data[value];
	$B.ajax({
		'url'		:	Router.get('video', 'ajax', {'ajaxAction' : 'status', 'id' : id, 'value' : value, 'tt' : new Date().valueOf()}),
		'success'	:	function(s)	
		{
			if(s == 'succ')	window.location.reload();
		}
	});
}
Ttk.action.videoIndex();
var videoNames = [];
</script>
<style> 
.input_text{
	width:30px;
}
.input_type{
	width:100%;
}
.btn_edit{
	display:inline-block;
	width:60px;
	height:24px;
	background:#6699FF;
	color:#fff;
	line-height:24px;
}
.btn_edit:hover{
	background:#f60;
}
</style>
</head>
<body>
<div id="rightTop" style="height:85px;">
	<p>��Ƶ�б�</p>
	<ul class="subnav">
		<li><span>�б�</span></li>
		<li><a href="<?php echo $this->mRouter->urlEx('video', 'add')?>" class="btn1">������Ƶ</a></li>
	</ul>
	<p style="clear:both;">
		<form name="search">
			������
			<input type="text" name="keywords" value="<?php echo $keywords;?>" size="50"/>
			<select name="query">
				<option value="vedioname" <?php echo $sels[0];?>>ָ��ӰƬ����</option>
				<option value="vedioid"<?php echo $sels[1];?>>ָ��ӰƬID</option>
				<option value="tag"<?php echo $sels[2];?>>ָ����ǩ</option>	
				<option value="area"<?php echo $sels[3];?>>ָ������</option>			
			</select>
			���ࣺ
			<select name="type">
				<script>showType(<?php echo $video_type;?>)</script>			
			</select>
			��ǣ�
			<select name="markType">
				<option value="" >ѡ��������</option>
				<option value="0" >δ����</option>
				<option value="1">��ͬ��</option>
				<option value="2">����Դ</option>				
			</select>
			<input type="submit" id="formbtn" class="formbtn" value="����"/>
			<input type="button" value="���ؿ���" id="tag" isDisplay="false"  class="formbtn"/>
		</form>	
		<script>Router.bindForm(true, 'video', '', document.search)</script>
	</p>
</div>
<div class="info2" style="color:#444">
	<table class="distinction" id="distinction">
		<thead>
			<tr>
				<th class="w70"><input id="selectAll" onclick="checkAll('id[]')" type="checkbox" /> <label for="selectAll">ȫѡ</label></th>
				<th width="80"  style="text-align:left;">ID</th>
				<th width="220" style="text-align:left;">��Ƶ����</th>
				<th>��ǩ</th>
				<th width="60"  style="text-align:left;">����</th>
				<th width="60" style="text-align:left;">��Ƶ����</th>
				<th width="50">״̬</th>
				<th width="60"><a href="<?php echo $publicUrl . $this->mRouter->url(array('orval' => $ornewval, 'order' => 'weekNum'))?>">������</a></th>
				<th width="60"><a href="<?php echo $publicUrl . $this->mRouter->url(array('orval' => $ornewval, 'order' => 'monthNum'))?>">������</a></th>
				<th width="60"><a href="<?php echo $publicUrl . $this->mRouter->url(array('orval' => $ornewval, 'order' => 'stortId'))?>">�Ƽ�ֵ</a></th>
				<th width="60"><a href="<?php echo $publicUrl . $this->mRouter->url(array('orval' => $ornewval, 'order' => 'viewNum'))?>">����</a></th>
				<th width="50"><a href="<?php echo $publicUrl . $this->mRouter->url(array('orval' => $ornewval, 'order' => 'point'))?>">����</a></th>
				<th width="140">����ʱ��</th>
			</tr>
		</thead>
		<tbody>
			<form method="post" name="deleForm" action="?s=video/delete">
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
				'empty_str' => '<tr><td colspan="15" style="padding:20px">�Բ�����������</td></tr>',
				'auto_index_prev' => 0,
				'db_callback' => null,
				'show_result_callback' => create_function('$item,$index','return str_replace("#autoIndex#",$index,\'
					<tr>
						<td><input type="checkbox" name="id[]" value="\'.$item[\'id\'].\'"/></td>
						<td style="text-align:left;">\'.$item[\'id\'].\'</td>
						<td style="text-align:left;"><a name="btn_name" id="btn_name" title="���ݣ�\'.$item[\'directors\'].\'����Ա��\'.$item[\'actors\'].\'" dataid="\'.$item[\'id\'].\'" href="\'.(videoControllor::UR(\'item\', array(\'id\' => $item[\'id\']), 1)).\'" target="_blank">\'.$item[\'name\'].\'</a><font style="color:red">[\'.$item[\'mark\'].\']</font> <a target="_blank" href="http://www.tangguoyy.com/html/item/\'.$item[\'id\'].\'.html"><font style="color:#ccc">HTML</font></a>
						</td>
						<td name="videoTag" id="videoTag" style="text-align:left;"  onclick="stort(this,\'.$item[\'id\'].\',\\\'tag\\\',\\\'input_type\\\')">\'.$item[\'tag\'].\'</td>
						<td style="text-align:left;"  onclick="stort(this,\'.$item[\'id\'].\',\\\'area\\\',\\\'input_type\\\')">\'.$item[\'area\'].\'</td>
						<td style="text-align:left;"><script>document.write(showOneType(\'.$item[\'type\'].\'))</script></td>
						<td id=\\\'cont2_#autoIndex#\\\' onclick=\\\'changeShow2(#autoIndex#,\'.$item[\'status\'].\',\'.$item[\'id\'].\')\\\'>\'.($item[\'status\'] == 1 ? \'<font color=#aaaaaa>��ʾ</font>\' : \'<font color=red>����</font>\').\'</td>
						<td onclick="stort(this,\'.$item[\'id\'].\',\\\'weekNum\\\')">\'.$item[\'weekNum\'].\'</td>
						<td>\'.$item[\'monthNum\'].\'</td>
						<td onclick="stort(this,\'.$item[\'id\'].\',\\\'stortId\\\')">\'.$item[\'stortId\'].\'</td>
						<td onclick="stort(this,\'.$item[\'id\'].\',\\\'viewNum\\\')">\'.$item[\'viewNum\'].\'</td>
						<td>\'.$item[\'point\'].\'</td>
						<td>\'.(date(\'Y-m-d H:i:s\', $item[\'updateDate\'])).\'</td>
						<td>
							\'.(Lamb_Utils::objectCall(\''.$this->mHash.'\', \'checkUpdatePurview\', array($item[\'id\']))).\'
						</td>
					</tr>
			\');')
			))?>	
			</form>
			<?php Lamb_View_Tag_Page::page(array(
			'page_num'		=>	9,
			'page_style'	=>	1,
			'listid'		=>	'list',
			'page_start_html'=>	'
				<tr><td colspan="15"><div id="pageDiv"><span class="msg">��#num#������ ��ǰ#currentPage#ҳ</span><span class="page">
				<a href="'.$pageUrl.'1" class="nofocus">��ҳ</a><a href="'.$pageUrl.'#prevPage#" class="nofocus">��һҳ</a>
				',
			'page_end_html'	=>	'
				<a href="'.$pageUrl.'#nextPage#" class="nofocus">��һҳ</a><a href="'.$pageUrl.'#lastPage#" class="nofocus">βҳ</a>
				</span><input type="text" size="2" value="#currentPage#" onblur="if(this.value && this.value!=#currentPage#){var s=location.href.replace(/\\/p\\/[^\\/]*/gi,\'\');location.href=s+\'/p/\'+this.value}"/></div></td></tr>
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
				<td colspan="14"> <input type="button" class="formbtn" onclick="return confirm('ȷ��Ҫ������')?(function(){document.deleForm.action='?s=video/create';document.deleForm.submit();})():false" value="����" />  <input class="formbtn" type="button" onclick="return (confirm('ȷ��ɾ����')?document.deleForm.submit():false)" value="ɾ��" /></td>
			</tr>
		</tfoot>
	</table>
</div>

<iframe name="opFrame" id="opFrame" height="0" width="0"></iframe>
</body>
<script>
$E.addEvent($A('tag'), 'click', function(event){
	if ($A('tag').getAttribute('isDisplay') == "false") {
		$A('listtag').style.display = 'none';
		var domTags = document.getElementsByName('videoTag');
		$F.each(domTags, function(item){
			item.style.display = 'none';
		});
		$A('tag').value = '��ʾ����';
		$A('tag').setAttribute('isDisplay', 'true');
	} else {
		$A('listtag').style.display = 'block';
		var domTags = document.getElementsByName('videoTag');
		$F.each(domTags, function(item){
			item.style.display = 'block';
		});
		$A('tag').value = '���ؿ���';
		$A('tag').setAttribute('isDisplay', 'false');
	}
	
});
</script>
</html>