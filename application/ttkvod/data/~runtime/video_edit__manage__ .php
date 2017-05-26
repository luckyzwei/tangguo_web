<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<link href="<?php echo $this->mThemePath;?>css/admin.css" rel="stylesheet" />
<link href="<?php echo $this->mThemePath;?>css/sub_page.css" rel="stylesheet" />
<script src="<?php echo $this->mSiteRoot;?>api/lamb.js"></script>
<script src="<?php echo $this->mRouter->urlEx('index', 'loadjsconfig')?>"></script>
<script src="<?php echo $this->mSiteRoot;?>api/global.js"></script>
<script type="text/javascript" charset="utf-8"  src="/ueditor/editor_config.js"></script>
<script type="text/javascript" charset="utf-8"  src="/ueditor/editor_ui_all_min.js"></script>
<link rel="stylesheet" type="text/css" href="/ueditor/themes/default/ueditor.css"/>
<script>
imgUrl	=	'<?php echo $this->mSiteCfg['img_host'];?>';
var aType = [];
<?php foreach ($this->mSiteCfg['channels'] as $key => $item) {
	echo "aType[" . $key . "] = {id:" . $key . ",name:'" . $item['name'] . "'};";
}
?>
function showType(index)
{
	var sHtml = '';
	for(var i=1,j=aType.length;i<j;i++)
	{
		sHtml += '<option value="'+aType[i].id+'"'+(index==aType[i].id?' selected="selected"':'')+'>'+aType[i].name+'</option>';
	}
	document.write(sHtml);
}
</script>
<style>
.notice{
	text-align:left;
	text-indent:15px;
	font-weight:bold;
	color:#333;
	font-size:14px;
	border-bottom:#999 1px solid !important ;
}
.input{
	width:400px;
}
#content{
	width:80%;
}
.img{
	height:290px;
	width:250px;
}
</style>
</head>
<body>
<?php if ($this->mDispatcher->setOrGetAction() == 'update') {?>
<div id="rightTop">
	<p>视频编辑(ID：<?php echo $id;?>)</p>
	<ul class="subnav">
		<li><span>编辑</span></li>
	</ul>
</div>
<div class="info2" style="padding-top:5px;">
	<form method="post" name="updateForm" target="opFrame" action="<?php echo $this->mRouter->urlEx('video', 'update', array('id' => $id))?>">
		<input type="hidden" name="rurl" value="<?php echo $this->mRefer;?>"/>
		<table cellpadding="5" width="100%;" class="edit_table">
			<tbody>
			<tr>
				<td class="right">&nbsp;</td>
				<td class="left">
					<input type="submit" class="formbtn" value="修改" onclick="return confirm('确定修改吗？')"/>
					&nbsp; &nbsp; 
					<input type="button" class="formbtn" value="返回" onclick="history.go(-1)"/>
				</td>
			</tr>			
			<tr>
				<td class="right">影片名称：</td>
				<td class="left">
				<input type="text" class="input" value="<?php echo $aData['name'];?>" name="data[name]"/>
				</td>
			</tr>
			<tr>
				<td class="right">影片分类：</td>
				<td class="left">
					<select name="data[type]">
						<script>showType(<?php echo $aData['type'];?>)</script>
					</select>
				</td>
			</tr>			
			<tr>
				<td class="right">连载标题：</td><td class="left"><input type="text" name="data[mark]" class="input" value="<?php echo $aData['mark'];?>"/></td>
			</tr>
			<tr>
				<td class="right">影片导演：</td><td class="left">
				<input type="text" class="input" name="data[directors]" value="<?php echo $aData['directors'];?>"/>
				<span style="color:#333">多个用空格或逗号分割</span>
				</td>
			</tr>
			<tr>
				<td class="right">影片演员：</td><td class="left">
				<input type="text" class="input" name="data[actors]" value="<?php echo $aData['actors'];?>"/>
				<span style="color:#333">多个用空格或逗号分割</span>
				</td>
			</tr>
			<tr>
				<td class="right">影片类型：</td><td class="left">
				<input type="text"  class="input" name="data[type]" value="<?php echo $aData['type'];?>"/>
				<span style="color:#333">多个用空格或逗号分割</span>
				</td>
			</tr>			
			<tr>
				<td class="right">影片地区：</td><td class="left"><input type="text" name="data[area]" size="8" value="<?php echo $aData['area'];?>"/></td>
			</tr>
			<tr>
				<td class="right">上映日期：</td><td class="left"><input type="text" name="data[vedioYear]"  value="<?php echo $aData['vedioYear'];?>"/>
				</td>
			</tr>						
			<tr>
				<td class="right">人气：</td><td class="left"><input type="text" size="5" name="data[viewNum]" value="<?php echo $aData['viewNum'];?>"/></td>
			</tr>
			<tr>
				<td class="right">更新日期：</td><td class="left"><input type="text"  name="data[updateDate]" id="updateDate" value="<?php echo date('Y-m-d H:i:s', $aData['updateDate'])?>"/> <a href="javascript:void(0)" onclick="$A('updateDate').value='<?php echo date('Y-m-d H:i:s')?>'">获取当前时间</a></td>
			</tr>						
			<tr>
				<td class="right">播放种子：</td>
				
				<td class="left">
					<p>id |mid 资源ID |play_data 播放地址  |num 集数  |extra 标识 |source 播放源 |description</p>
					<textarea name="data[play_data]" style="width:80%;height:300px"><?php echo $aData['play_data'];?></textarea>
				</td>
			</tr>
			<tr>
				<td class="right">影片简介：</td>
				<td class="left"><textarea name="content" id="content"><?php echo $aData['content'];?></textarea><script>var editor = new baidu.editor.ui.Editor({'initialContent' : '', 'minFrameHeight' : 250,'textarea' : 'data[content]'}); editor.render('content');</script></td>
			</tr>	
			<tr>
				<td class="right">备注：</td>
				<td class="left"><input type="text" name="data[gemo]" size="60" value="<?php echo $aData['gemo'];?>"/></td>
			</tr>		
			<tr>
				<td class="right">影片图片：</td>
				<td class="left">
					<div id="img_cont" style="float:left;margin-bottom:10px;width:622px;height:350px;border:1px solid #ccc;overflow:hidden;">
						<div style="float:left;margin-right:10px;margin-bottom:20px;"><img src="<?php echo Ttkvod_Utils::getImgPath($aData['vedioPic']);?>" class="img" id="dx_img" /></div>
						<p style="clear:both"><input id="vedioPic" type="text" value="<?php echo $aData['vedioPic'];?>" class="input" name="data[vedioPic]"/>
						</p>
					</div>
				</td>
			</tr>
			<tr>
				<td class="right">远程下载图片：</td>
				<td class="left">
					<input class="input" id="site" style="width:600px" />
					<input type="button" id="formbtn" class="formbtn" value="下载" />
					<span id="u_status" style="color:red"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" class="formbtn" value="修改" onclick="return confirm('确定修改吗？')"/>
					&nbsp; &nbsp; 
					<input type="button" class="formbtn" value="返回" onclick="history.go(-1)"/>
				</td>
			</tr>			
			</tbody>
		</table>
	</form>
<script>
	
	$B.ready(function(){
		var formbtn = $A('formbtn');
		$E.addEvent(formbtn, 'click', function(){
			var site = $A('site').value;
			
			$B.ajax({
				'url' : 'http://localhost:805/?s=collect/upload/site/' + encodeURIComponent(encodeURIComponent(site)) + '/id/' + <?php echo $id;?> + '/tt/' + new Date().valueOf(),
				'type' : 'GET',
				'success' : function(ret) {
					if(ret.s > 0){
						$A('u_status').innerHTML = '上传成功';
						var dx_img = $A('dx_img');
						dx_img.src = ret.d.url + '?' + new Date().valueOf();
					}else{
						$A('u_status').innerHTML = ret.err_str;
					}
				}
			});
				
			/*	
			Ajax.script.get({
				'url'	  : 'http://localhost:805/?s=collect/upload/site/' + encodeURIComponent(encodeURIComponent(site)) + '/id/' + <?php echo $id;?> + '/tt/' + new Date().valueOf(),
				'success' :	function(ret){
					if(ret.s > 0){
						$A('u_status').innerHTML = '上传成功';
						var dx_img = $A('dx_img');
						dx_img.src = ret.d.url + '?' + new Date().valueOf();
					}else{
						$A('u_status').innerHTML = ret.err_str;
					}
				}
			});	*/	
		});
		
		function imgfresh_onclick()
		{
			var dom = $A('cn_img');
			dom.src = dom.src.split('?')[0] + '?' + new Date().valueOf();
		}
	
	});
	
</script>
<?php } else {?>
<div id="rightTop">
	<p>添加视频</p>
	<ul class="subnav">
		<li><span>添加</span></li>
	</ul>
</div>
<div class="info2" style="padding-top:5px;">
	<form method="post" name="addForm" target="opFrame" action="<?php echo $this->mRouter->urlEx('video', 'add')?>">
		<table cellpadding="5" width="100%;" class="edit_table">
			<tbody>
			<tr>
				<td class="right">&nbsp;</td>
				<td class="left">
					<input type="submit" class="formbtn" value="添加">
					&nbsp; &nbsp; 
					<input type="button" class="formbtn" value="返回" onclick="history.go(-1)"/>
				</td>
			</tr>			
			<tr>
				<td class="right">影片名称：</td>
				<td class="left">
				<input type="text" class="input" value="" name="data[name]"/>
				</td>
			</tr>
			<tr>
				<td class="right">影片分类：</td>
				<td class="left">
					<select name="data[type]">
						<script>showType()</script>
					</select>
				</td>
			</tr>			
			<tr>
				<td class="right">连载标题：</td><td class="left"><input type="text" name="data[mark]" class="input" value=""/></td>
			</tr>
			<tr>
				<td class="right">影片导演：</td><td class="left">
				<input type="text" class="input" name="data[directors]" value="不详"/>
				<span style="color:#333">多个用空格或逗号分割</span>
				</td>
			</tr>
			<tr>
				<td class="right">影片演员：</td><td class="left">
				<input type="text" class="input" name="data[actors]" value="不详"/>
				<span style="color:#333">多个用空格或逗号分割</span>
				</td>
			</tr>
			<tr>
				<td class="right">影片类型：</td><td class="left">
				<input type="text"  class="input" name="data[type]" value=""/>
				<span style="color:#333">多个用空格或逗号分割</span>
				</td>
			</tr>			
			<tr>
				<td class="right">影片地区：</td><td class="left"><input type="text" name="data[area]" size="8" value=""/></td>
			</tr>
			<tr>
				<td class="right">上映日期：</td><td class="left"><input type="text" name="data[vedioYear]"  value=""/>
				</td>
			</tr>						
			<tr>
				<td class="right">人气：</td><td class="left"><input type="text" size="5" name="data[viewNum]" value="0"/></td>
			</tr>
			<tr>
				<td class="right">更新日期：</td><td class="left"><input type="text"  name="data[updateDate]" value="<?php echo date('Y-m-d H:i:s')?>"/></td>
			</tr>					
			<tr>
				<td class="right">播放种子：</td>
				<td class="left">
				<textarea name="data[playData]" style="width:80%;height:300px"></textarea>
				</td>
			</tr>
			<tr>
				<td class="right">影片简介：</td>
				<td class="left"><textarea name="content" id="content"></textarea><script>var editor = new baidu.editor.ui.Editor({'initialContent' : '', 'minFrameHeight' : 250,'textarea' : 'data[content]'}); editor.render('content');</script></td>
			</tr>
			<tr>
				<td class="right">备注：</td>
				<td class="left"><input type="text" name="data[gemo]" size="60"/></td>
			</tr>
			<tr>
				<td class="right">图片地址：</td>
				<td class="left">
				<div style="float:left;margin-bottom:10px;width:622px;height:300px;border:1px solid #ccc;overflow:hidden;" id="img_cont" style="display:none">
					<img id="dx_img" src="" />
				</div>
				<p style="clear:both"><input type="text" class="input" name="data[vedioPic]" id="vedioPic"/></p>				
				</td>
			</tr>
			<tr>
				<td class="right">远程下载图片：</td>
				<td class="left">
					<input class="input" id="site" style="width:600px" />
					<input type="button" id="formbtn" class="formbtn" value="下载" />
					<span id="u_status" style="color:red"></span>
				</td>
			</tr>	
			<tr>
				<td colspan="2" align="center">
					<input type="submit" class="formbtn" value="添加">
					&nbsp; &nbsp; 
					<input type="button" class="formbtn" value="返回" onclick="history.go(-1)"/>
				</td>
			</tr>							
			</tbody>
		</table>
	</form>
	<script>
	$B.ready(function(){
		var formbtn = $A('formbtn');
		$E.addEvent(formbtn, 'click', function(){
			var site = $A('site').value;

			Ajax.script.get({
				'url'	  : 'http://localhost:805/?s=collect/upload/site/' + encodeURIComponent(encodeURIComponent(site)) + '/tt/' + new Date().valueOf(),
				'success' :	function(ret){
					if(ret.s > 0){
						$A('u_status').innerHTML = '上传成功';
						var dx_img = $A('dx_img');
						var vedioPic = $A('vedioPic');
						dx_img.src = ret.d.url;
						vedioPic.value = ret.d.dir;
					}else{
						$A('u_status').innerHTML = ret.err_str;
					}
				}
			});		
		});
	});
	</script>
<?php }?>
	<!--iframe src="?s=video/upload" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="250px;"></iframe-->
</div>
<iframe name="opFrame" id="opFrame" height="0" width="0" frameborder="0"></iframe>
</body>
</html>