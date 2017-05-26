<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<meta http-equiv="X-UA-Compatible" content="IE=edge charset=gbk" />
<link href="<?php echo $this->mThemePath;?>css/admin.css" rel="stylesheet" />
<base target="workspace" />
<title>�ǹ�Ӱ��_��̨����ϵͳ</title>
<script src="<?php echo $this->mSiteRoot;?>api/lamb.js"></script>
<script src="<?php echo $this->mRouter->urlEx('index', 'loadjsconfig')?>"></script>
<script src="<?php echo $this->mSiteRoot;?>api/global.js"></script>
<script>
function showMenu()
{
	var menuData=
	[
			{
				name	:		'ϵͳ����',
				url		:		'<?php echo $this->mRouter->urlEx('index', 'config')?>',
				subMenu	:
				[
				 	{
				 		name	:	'ϵͳ����',
				 		url		:	'<?php echo $this->mRouter->urlEx('index', 'config')?>'
				 	}
				]
			},
			{
				name	:		'��Ƶ����',
				url		:		'<?php echo $this->mRouter->urlEx('video', '')?>',
				subMenu	:
				[
				 	{
						name	:	'��Ƶ�б�',
						url		: 	'<?php echo $this->mRouter->urlEx('video', '')?>'
					},
					{
						name	:	'�����Ƶ',
						url	:	'<?php echo $this->mRouter->urlEx('video', 'add')?>'
					},
					{
						name	:	'���۹���',
						url		:	'<?php echo $this->mRouter->urlEx('video', 'comment')?>'
					},
					{
						name	:	'��������',
						url		:	'<?php echo $this->mRouter->urlEx('video', 'feedback')?>'
					}
				]
					
			},
			{
				name	:		'ϵͳ����',
				url		:		'<?php echo $this->mRouter->urlEx('tool', '')?>',
				subMenu	:
				[
					{
						name	:	'���ɾ�̬',
						url		:	'<?php echo $this->mRouter->urlEx('tool', '')?>'
					},
					{
						name	:	'����ǰ5ҳ�б�',
						url		:	'<?php echo $this->getClientUrl('looper', 'createhtml', array('ac' => 'listtask', 'limit' => 5))?>'
					}
				]
			},
			{
				name	:		'��Ա����',
				url		:		'<?php echo $this->mRouter->urlEx('member', '')?>',
				subMenu	:
				[
				 	{
						name	:	'��Ա����',
						url	:	'<?php echo $this->mRouter->urlEx('member', '')?>'
				 	}
					,
					{
						name	:	'�༭Ա����',
						isAdmin  :  <?php echo $isAdmin;?>,
						url		:	'<?php echo $this->mRouter->urlEx('member', 'admin')?>'
					}
				]
			}
	]

	this.showTopMenu=function()
	{
		for(var i=0,j=menuData.length;i<j;i++)
		{
			document.write('<li><a href="'+menuData[i].url+'" target="workspace" onclick="oMenu.showSubMenu('+i+')">'+menuData[i].name+'<\/a><\/li>');		
		}
	}

	this.showSubMenu=function(index)
	{
		var aSub=menuData[index].subMenu;
		var oContSub=$A('submenu');
		oContSub.innerHTML='';
		var o=document.createElement('li');
		o.className='title';
		o.innerHTML=menuData[index].name;
		oContSub.appendChild(o);	
		for(var i=0,j=aSub.length;i<j;i++)
		{
			if (aSub[i].isAdmin == false) {
				continue;
			}
			var o=document.createElement('li');
			var oa=document.createElement('a');
			oa.innerHTML=aSub[i].name;
			oa.setAttribute('href',aSub[i].url);
			o.appendChild(oa);
			oContSub.appendChild(o);
		}

		var oNav= $A('nav');
		var oNavChild=oNav.childNodes;
		for(var i=1,j=oNavChild.length;i<j;i++) oNavChild[i].getElementsByTagName('a')[0].className='';
		oNavChild[index+1].getElementsByTagName('a')[0].className='actived';
	}
}
var oMenu=new showMenu();
</script>
</head>
<body>
<div id="head">
	<img style="top:10px;" class="logo" src="<?php echo $this->mThemePath;?>images/logo.png" width="200px" height="50px">
    <div class="top">
    	���,<b><?php echo $_SESSION[$this->mSessionKeyUsername];?></b> &nbsp;&nbsp;<a href="<?php echo $this->mRouter->urlEx('index', 'loginout')?>" target="_self">[�˳�]</a> &nbsp; <a href="<?php echo $this->mRouter->urlEx('index', 'flushcache')?>" target="_self" class="menu-btn2">�������</a> <a href="http://<?php echo self::$sTtkvodClientHost?>" target="_blank" class="menu-btn2">ǰ̨��ҳ</a> <a href="<?php echo $this->mRouter->urlEx('index', 'createindex')?>" target="_self" class="menu-btn2">������ҳ</a>
    </div>
    <ul class="nav" id="nav"><script>oMenu.showTopMenu()</script></ul>
</div>
<div id="content" class="clearfix">
	<div id="left">
    	<ul id="submenu">
        	<li class="title">ϵͳ����</li>
           	<li><a href="<?php echo $this->mRouter->urlEx('index', 'config')?>">ϵͳ����</a></li>
        </ul>
    </div>
    <div id="right">
    	<iframe width="100%" id="workspace" frameborder="0" src="<?php echo $this->mRouter->urlEx('index', 'config')?>" name="workspace"></iframe>
	<script>
		(fresize=function(){
			$A("workspace").style.height=($D.getClientHeight()-100)+'px';
			$A("workspace").style.width=($D.doc().clientWidth-172)+'px';
		})();
		window.onresize = fresize; 
	</script>
    </div>
</div>
</body>
</html>