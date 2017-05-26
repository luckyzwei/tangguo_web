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
.none{display:none;}
.textarea_small{
	height:45px;width:800px;
}
.textarea_middle{
	height:90px;width:800px;
}
.textarea_big{
	height:120px;width:800px;
}
.tr_title{
	background:#ecf5ff;
	height:28px;
}
.tr_title b{
	color:#444;
}
body{
	color:#444;
}
</style>
</head>
<body>
<div class="info2">
	<form method="post" name="baseForm" action="<?php echo $this->mRouter->getCtrlActUrl()?>">
		<table cellpadding="5" width="100%;" class="edit_table">
			<tbody>
				<tr class="tr_title"><td colspan="2"><b>��վ����</b></td></tr>
				<tr>
					<td class="right">��վ���ƣ�</td>
					<td class="left"><input type="text" name="cfg[site_name]" value="<?php echo $this->mSiteCfg['site_name'];?>"/></td>
				</tr>
				<tr>
					<td class="right">��վ��Ŀ¼��</td>
					<td class="left"><input type="text" name="cfg[site_root]" value="<?php echo $this->mSiteCfg['site_root'];?>" /></td>
				</tr>
				<tr>
					<td class="right">cookie��Կ��</td>
					<td class="left"><input type="text" name="cfg[cookie_key]" value="<?php echo $this->mSiteCfg['cookie_key'];?>" /></td>
				</tr>				
				<tr>
					<td class="right">ͼƬ��ַ��</td>
					<td class="left"><input type="text" name="cfg[img_host]" size="60" value="<?php echo $this->mSiteCfg['img_host'];?>" /></td>
				</tr>
				<tr>
					<td class="right">�հ�ͼƬ·����</td>
					<td class="left"><input type="text" name="cfg[blank_img_path]" size="60" value="<?php echo $this->mSiteCfg['blank_img_path'];?>" /></td>
				</tr>												
				<tr>
					<td class="right">��վ�ؼ��֣�</td>
					<td class="left"><input type="text" name="cfg[site_keywords]" value="<?php echo $this->mSiteCfg['site_keywords'];?>" size="60"/></td>
				</tr>				
				<tr>
					<td class="right">��վ������</td>
					<td class="left"><textarea name="cfg[site_description]" class="textarea_small"><?php echo $this->mSiteCfg['site_description'];?></textarea></td>
				</tr>
				<tr>
					<td class="right">���������ʣ�</td>
					<td class="left"><textarea name="cfg[hot_search_keywords]" class="textarea_small"><?php echo implode(',', $this->mSiteCfg['hot_search_keywords'])?></textarea></td>
				</tr>
				<tr>
					<td class="right">����ѡ�</td>
					<td class="left"><textarea name="cfg[fix_chooses]" class="textarea_small"><?php echo $this->mSiteCfg['fix_chooses']?></textarea></td>
				</tr>	
				<tr>
					<td class="right">��������</td>
					<td class="left"><input type="text" name="cfg[domain]" value="<?php echo $this->mSiteCfg['domain'];?>"/></td>
				</tr>	
				<tr>
					<td class="right">ģ��·�ɵ�ַ��</td>
					<td class="left">��ģ�飺<input type="text" name="cfg[model_router_hostname][index]" value="<?php echo $this->mSiteCfg['model_router_hostname']['index'];?>"/>
					��Աģ�飺<input type="text" name="cfg[model_router_hostname][member]" value="<?php echo $this->mSiteCfg['model_router_hostname']['member'];?>"/>
					�б�ģ�飺<input type="text" name="cfg[model_router_hostname][list]" value="<?php echo $this->mSiteCfg['model_router_hostname']['list'];?>"/>
					����ģ�飺<input type="text" name="cfg[model_router_hostname][search]" value="<?php echo $this->mSiteCfg['model_router_hostname']['search'];?>"/>
					����ģ�飺<input type="text" name="cfg[model_router_hostname][item]" value="<?php echo $this->mSiteCfg['model_router_hostname']['item'];?>"/>
					��̬ģ�飺<input type="text" name="cfg[model_router_hostname][static]" value="<?php echo $this->mSiteCfg['model_router_hostname']['static'];?>"/></td>
				</tr>
				<tr>
					<td class="right">·�ɲ�����</td>
					<td>URL�ָ�����<input type="text" size="5" name="cfg[url_delimiter]" value="<?php echo $this->mSiteCfg['url_delimiter'];?>"/> 
					URL��������<input type="text" size="5" name="cfg[url_param_name]" value="<?php echo $this->mSiteCfg['url_param_name'];?>"/></td>
				</tr>	
				<tr>
					<td class="right">���Ӽ��ܣ�</td>
					<td><input type="checkbox" name="cfg[is_url_encode]" value="1" <?php if($this->mSiteCfg['is_url_encode'] == 1){echo ' checked="checked"';}?>/></td>
				</tr>	
				<tr>
					<td class="right">����֪ͨ�����</td>
					<td><input type="text" name="cfg[notice_interval_sec]" value="<?php echo $this->mSiteCfg['notice_interval_sec'];?>"/ size="3">��</td>
				</tr>
				<tr>
					<td class="right">����ͬ����</td>
					<td>
						�������ӣ�<input type="text" name="cfg[tv_sys][hntv]" value="<?php echo $this->mSiteCfg['tv_sys']['hntv'];?>" size="50"/> <br/>
						�������ӣ�<input type="text" name="cfg[tv_sys][jstv]" value="<?php echo $this->mSiteCfg['tv_sys']['jstv'];?>" size="50" /><br/>
						�������ӣ�<input type="text" name="cfg[tv_sys][bjtv]" value="<?php echo $this->mSiteCfg['tv_sys']['bjtv'];?>" size="50" /><br/>
						�������ӣ�<input type="text" name="cfg[tv_sys][dftv]" value="<?php echo $this->mSiteCfg['tv_sys']['dftv'];?>" size="50"/> ��ʽ��ID|ӰƬ����
					</td>
				</tr>	
				<tr>
					<td class="right">������ӳ��</td>
					<td><textarea class="textarea_big" name="cfg[will_show_movies]"><?php echo $this->mSiteCfg['will_show_movies'];?></textarea> ��ʽ��ӰƬ����|����</td>
				</tr>																											
				<tr class="tr_title"><td colspan="2"><b>��̬����</b></td></tr>				
				<tr>
					<td class="right">������̬��</td>
					<td class="left"><input type="checkbox" name="cfg[site_mode]" value="1" <?php if($this->mSiteCfg['site_mode'] == 1){echo ' checked="checked"';}?>/></td>
				</tr>
				<tr>
					<td class="right">�ļ���׺����</td>
					<td class="left"><input type="text" name="cfg[static_cfg][extendtion]" value="<?php echo $this->mSiteCfg['static_cfg']['extendtion'];?>" /></td>
				</tr>
				<tr>
					<td class="right">��̬�Զ�·�ɣ�</td>
					<td class="left"><input type="checkbox" value="1" name="cfg[enable_auto_router]" /><script>if (g_aCfg['enable_auto_router']){document.baseForm['cfg[enable_auto_router]'].checked=true}</script></td>
				</tr>				
				<tr>
					<td class="right">���ɷ�ʽ��</td>
					<td class="left"><select name="cfg[static_cfg][sync][type]" style="width:150px">
					<option value="2" <?php if ($this->mSiteCfg['static_cfg']['sync']['type'] == 2) {echo 'selected="selected"';}?>>���汾��</option><option value="3" <?php if ($this->mSiteCfg['static_cfg']['sync']['type'] == 3) {echo 'selected="selected"';}?>>HTTPͬ��</option><option value="4" <?php if ($this->mSiteCfg['static_cfg']['sync']['type'] == 4) {echo 'selected="selected"';}?>>FTPͬ��</option>
					</select></td>
				</tr>
				<tr syncfor='2,3,4' id="syncitem" name="syncitem" style="display:none">
					<td class="right">����·����</td>
					<td class="left"><input type="text" name="cfg[static_cfg][sync][save_path]" value="<?php echo $this->mSiteCfg['static_cfg']['sync']['save_path'];?>"/></td>
				</tr>
				<tr syncfor='3' id="syncitem" name="syncitem" style="display:none">
					<td class="right">�Ƿ�Post��</td>
					<td class="left"><input type="checkbox" name="cfg[static_cfg][sync][http_is_post]" value="1" <?php if($this->mSiteCfg['static_cfg']['sync']['http_is_post']){echo 'checked="checked"';}?></td>
				</tr>				
				<tr syncfor='3' id="syncitem" name="syncitem" style="display:none">
					<td class="right">HTTP��������</td>
					<td class="left"><input type="text" name="cfg[static_cfg][sync][http_param_name]" value="<?php echo $this->mSiteCfg['static_cfg']['sync']['http_param_name'];?>"/></td>				
				</tr>
				<tr syncfor='3' id="syncitem" name="syncitem" style="display:none">
					<td class="right">HTTP����URL��</td>
					<td class="left"><input type="text" name="cfg[static_cfg][sync][http_syn_url]" size="60" value="<?php echo $this->mSiteCfg['static_cfg']['sync']['http_syn_url'];?>"/></td>				
				</tr>
				<tr syncfor='4' id="syncitem" name="syncitem" style="display:none">
					<td class="right">FTP��ַ��</td>
					<td class="left"><input type="text" name="cfg[static_cfg][sync][ftp_host]" value="<?php echo $this->mSiteCfg['static_cfg']['sync']['ftp_host'];?>"/></td>				
				</tr>
				<tr syncfor='4' id="syncitem" name="syncitem" style="display:none">
					<td class="right">FTP�˿ڣ�</td>
					<td class="left"><input type="text" name="cfg[static_cfg][sync][ftp_port]" value="<?php echo $this->mSiteCfg['static_cfg']['sync']['ftp_port'];?>"/></td>				
				</tr>
				<tr syncfor='4' id="syncitem" name="syncitem" style="display:none">
					<td class="right">FTP�ʺţ�</td>
					<td class="left"><input type="text" name="cfg[static_cfg][sync][ftp_username]" value="<?php echo $this->mSiteCfg['static_cfg']['sync']['ftp_username'];?>"/></td>				
				</tr>
				<tr syncfor='4' id="syncitem" name="syncitem" style="display:none">
					<td class="right">FTP���룺</td>
					<td class="left"><input type="text" name="cfg[static_cfg][sync][ftp_password]" value="<?php echo $this->mSiteCfg['static_cfg']['sync']['ftp_password'];?>"/></td>				
				</tr>																										
				<tr class="tr_title"><td colspan="2"><b>��������</b></td></tr>
				<tr>
					<td class="left" colspan="2">��ҳ����<input type="text" name="cfg[comment][pagesize]" value="<?php echo $this->mSiteCfg['comment']['pagesize'];?>" size="2"/>
					��¥��ҳ����<input type="text" name="cfg[comment][cita_pagesize]" size="2" value="<?php echo $this->mSiteCfg['comment']['cita_pagesize'];?>"/>
					��¥�������<input type="text" size="2" name="cfg[comment][max_cita_count]" value="<?php echo $this->mSiteCfg['comment']['max_cita_count'];?>"/>
					��ʾ���ȣ�<input type="text" size="3" name="cfg[comment][max_display_contentlen]" value="<?php echo $this->mSiteCfg['comment']['max_display_contentlen'];?>"/>
					��󳤶ȣ�<input type="text" size="3" name="cfg[comment][max_content_len]" value="<?php echo $this->mSiteCfg['comment']['max_content_len'];?>"/>
					���ۼ��ʱ�䣺<input type="text" size="2" name="cfg[comment][submit_interval_mill]" value="<?php echo $this->mSiteCfg['comment']['submit_interval_mill'];?>"/>��
					֧�ּ��ʱ�䣺<input type="text" size="2" name="cfg[comment][support_interval_sec]" value="<?php echo $this->mSiteCfg['comment']['support_interval_sec'];?>"/>��
					</td>
				</tr>
				<tr>
					<td class="right">�ر�����ӰƬID��</td>
					<td class="left"><textarea class="textarea_small" name="cfg[comment][close_video_ids]"><?php echo $this->mSiteCfg['comment']['close_video_ids'];?></textarea></td>
				</tr>
				<tr>
					<td class="right">���˹ؼ��ʣ�</td>
					<td class="left"><textarea class="textarea_middle" name="cfg[comment][filter_words]"><?php echo $this->mSiteCfg['comment']['filter_words'];?></textarea></td>
				</tr>
				<tr>
					<td class="right">���Թؼ��ʣ�</td>
					<td class="left"><textarea class="textarea_middle" name="cfg[comment][forbin_words]"><?php echo $this->mSiteCfg['comment']['forbin_words'];?></textarea></td>
				</tr>
				<tr>
					<td class="right">����IP��</td>
					<td class="left"><textarea class="textarea_middle" name="cfg[comment][forbin_ips]"><?php echo $this->mSiteCfg['comment']['forbin_ips'];?></textarea></td>
				</tr>
				<tr class="tr_title"><td colspan="2"><b>��Ա����</b></td></tr>									
				<tr>
					<td class="right">���棺</td>
					<td class="left"><input type="text" name="cfg[member_notice]" value="<?php echo $this->mSiteCfg['member_notice'];?>" size="60"/></td>
				</tr>
				<tr>
					<td class="right">��ע��IP��</td>
					<td class="left"><textarea class="textarea_middle" name="cfg[register][forbin_ips]"><?php echo $this->mSiteCfg['register']['forbin_ips'];?></textarea></td>
				</tr>
				<tr>
					<td class="right">��ע��ؼ��ʣ�</td>
					<td class="left"><textarea class="textarea_middle" name="cfg[register][forbin_usernames]"><?php echo $this->mSiteCfg['register']['forbin_usernames'];?></textarea></td>
				</tr>
				<tr>
					<td class="right">��ע�����䣺</td>
					<td class="left"><textarea class="textarea_middle" name="cfg[register][forbin_email]"><?php echo $this->mSiteCfg['register']['forbin_email'];?></textarea></td>
				</tr>
				<tr>
					<td class="right">��ҳ�˵����ã�</td>
					<td class="left"><textarea class="textarea_middle" name="cfg[index_menu]"><?php echo $this->mSiteCfg['index_menu'];?></textarea>��ʽ����������|���ӵ�ַ|�Ƿ����´��ڴ� �磺Ӧ��|http://www.baidu.com|0</td>
				</tr>										
                <tr>
					<td colspan="2" style="padding-left:30px;"><input type="submit" onclick="return confirm('ȷ���޸���')" class="formbtn" value="ȷ���޸�" /></td>
                </tr>													
			</tbody>
		</table>
		<script>
			var key = 'syncfor', searchLists = document.getElementsByName('syncitem');
			selectLocalShow.main(<?php echo $this->mSiteCfg['static_cfg']['sync']['type'];?>, key, searchLists).bind(document.baseForm['cfg[static_cfg][sync][type]'], key, searchLists)
		</script>
	</form>
</div>
</body>
</html>