<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=gbk" />
<link href="<?php echo $this->mThemePath;?>css/admin.css" rel="stylesheet" />
<link href="<?php echo $this->mThemePath;?>css/sub_page.css" rel="stylesheet" />
<script src="<?php echo $this->mRouter->urlEx('index', 'loadjsconfig')?>"></script>
<script src="<?php echo $this->mSiteRoot;?>api/lamb.js"></script>
<script src="<?php echo $this->mSiteRoot;?>api/global.js"></script>
<style>
ul li{
	list-style:none;
}
.case{
	border-top:1px solid #fff;
	background:#f0f7ff;
	padding:10px 0px 10px 10px;
}
.case li{
	padding:5px 0px;
	color:#333;
	vertical-align:middle;
}
.case li span{
	font-weight:bold;
	margin-right:5px;
}
.split{
	border-bottom:1px solid #ccc;
	padding-bottom:10px !important;
	margin-bottom:8px;
	margin-right:10px;
}
.content{
	height:500px;
	background:#fcfdff;
	border:3px solid #d3e9f8;
	border-left:none;
}
.hide_choice{
	margin:5px 0 0;
}
</style>
<script>
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
var types = [];
<?php foreach ($types as $id => $type) {
	echo 'types[' . $id . '] = [];';
	foreach ($type as $key => $item) {
?>	
types[<?php echo $id;?>][<?php echo $key;?>] = '<?php echo $item;?>';
<?php }}?>
function showtagid(sel)
{
	var val = sel.options[sel.selectedIndex].value,
		str = '';
	var type = types[val];
	document.sqlForm.tagid.innerHTML = '';
	var option = document.createElement('option');
	option.value = '';
	option.innerHTML = 'Ĭ��';
	document.sqlForm.tagid.appendChild(option);
	if ($F.isInt(val, true)) {
		for (var i = 0; i < type.length; i ++) {
			option = document.createElement('option');
			option.value = i;
			option.innerHTML = type[i];
			document.sqlForm.tagid.appendChild(option);
		}
	}
}
function updatedate_onclick(obj)
{
	if (obj.checked) {
		document.sqlForm.sd.value = 0;
		document.sqlForm.ed.value = 0;
	} else {
		document.sqlForm.sd.value = '';
		document.sqlForm.ed.value = '';	
	}
}
function ac_onchange(obj)
{
	var val = obj.options[obj.selectedIndex].value,
		hide_choices = document.getElementsByName('hide_choice');
	if (val == 'item') {
		hide_choices[0].style.display = '';
	} else {
		hide_choices[0].style.display = 'none';
	}
	if (val == 'list') {
		hide_choices[1].style.display = '';
	} else {
		hide_choices[1].style.display = 'none';
	}	
}
function form_onclick()
{
	var url = '/index.php?s=tool//' + Router.bindFormCore(document.sqlForm);
	$A('content_1').src = url;
}
</script>
</head>
<body>
<ul class="case">
	<li><span>��������</span>
	<form name="sqlForm" method="get">
	<input type="hidden" name="opac" value="run"/>
	����ѡ�<select name="ac" onchange="ac_onchange(this)">
	<option value="index">��ҳ</option>
	<option value="item">����ҳ</option>
	<option value="listtask">�б�ҳ</option>
	<option value="list">�����������б�ҳ</option>
	<option value="todayalltask">��������</option>
	</select> ӰƬ���ࣺ<select name="id" onchange="showtagid(this)"><script>showType()</script></select>
	ÿҳ������Ŀ�� <input type="text" name="psi" size="5" value="100"/>  ����ʱ�� <input type="text" name="is" size="5" value="2"/> ��
	ֻ����ǰ��<input type="text" name="limit" value="" size="5"/> ��  <input onclick="form_onclick()" type="button" value="��ʼ����" class="formbtn"/>
	<div class="hide_choice" style="display:none" id="hide_choice" name="hide_choice">
	����ʱ�䣺�� <input type="text" name="sd" value=""/> �� <input type="text" name="ed" value=""/> <input type="checkbox" onclick="updatedate_onclick(this)"/><span>����</span>
	</div>
	<div class="hide_choice" style="display:none" id="hide_choice" name="hide_choice">
	С���ࣺ<select name="tagid"><option value="">Ĭ��</option></select>
	</div>	
	</form>
	</li>
</ul>
<div class="content">
<iframe width="100%" height="100%" frameBorder=0 marginheight="0" name="opFrame" marginwidth="0" id="content_1" src="about:blank"></iframe>
</div>
</body>
</html>