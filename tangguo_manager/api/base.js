var msgBox = {//3700
	show : function(option)
	{
		var that = msgBox,
			_op	=	{
				'msg'	:	'',
				'type'	:	'succ',//succ,error,warn
				'atype'	:	'normal',//normal linner
				'millsec':	2000,
				'callback':	function(){},
				'manu_click' : true,
				'succ_anim' : true
			},
			domMsg	=	$A('msg_box'),
			className;
		if(that.bRun) return ;
		$F.extend(_op, option);
		if(_op.type=='error') { 
			className	=	'msgbox_layer_e';
		}
		else if(_op.type=='warn') {
			className	=	'msgbox_layer_w';
		}
		else {
			className	=	'msgbox_layer_s';		
		}
		if(domMsg	==	null)
		{
			domMsg		=	document.createElement('div');
			domMsg.id	=	'msg_box';
			domMsg.style.display	=	'none';
			domMsg.innerHTML	=	'<span class="msgbox_layer_c"><span class="'+className+'"></span><div>'+_op.msg+'</div><span class="msgbox_layer_end"></span></span>';
			document.body.appendChild(domMsg);
		}
		else{
			domMsg.getElementsByTagName('span')[1].className= className;
			domMsg.getElementsByTagName('div')[0].innerHTML = _op.msg;
		}
		if ($B.browser.ie_version == 6) {
			var rect = $D.pageRect(domMsg);
			domMsg.style.top = $D.getScrollTop() + parseInt($D.getClientHeight()/2) - 15 + 'px';
		}
		function close_msg()
		{
			that._timer && clearTimeout(that._timer);
			that.bRun	=	false;
			domMsg.style.display	=	'none';
			_op.callback();
			$E.removeEvent(domMsg,'click',close_msg);
		}
		if (_op.manu_click) {
			$E.addEvent(domMsg,'click',close_msg);		
		}
		function normal(){
			domMsg.style.display	=	'block';
			that.bRun	=	true;
			that._timer = window.setTimeout(function(){
				domMsg.style.display	=	'none';
				that.bRun	=	false;
				_op.callback();
			},_op.millsec);		
		}
		if (_op.type == 'succ' && _op.succ_anim) {
			_op.atype = 'liner';
			_op.millsec = 1000;
		}
		if(_op.atype == 'normal') {
			normal();
		}
		else {
			var nClientHeight	=	$D.getClientHeight(),
				nMoveLen		=	30,
				s				=	5,
				oa				=	new $B.extend.CAnimation($B.extend.CAnimation.CONST_TYPE.LINEAR,0,nMoveLen,s),
				nn				=	20,
				it				=	parseInt((nClientHeight-54)/2)+nMoveLen;
			if(nClientHeight>=108){
				$D.setFilter(domMsg,0);
				$D.style(domMsg,{'display':'block','position':'absolute','top': $D.getScrollTop()+it+'px'});
				oa.start(nn,function(i){
					domMsg.style.top	=	$D.getScrollTop()+parseInt(it-i) + 'px';
					$D.setFilter(domMsg,parseInt(i/nMoveLen*100));
					if(i>=nMoveLen){
						that.bRun	=	true;
						that._timer = window.setTimeout(close,_op.millsec);
					}
				});
				function close()
				{
					oa.start(nn,function(i){
						domMsg.style.top	=	parseInt(it-nMoveLen-i)+'px';
						$D.setFilter(domMsg,100-parseInt(i/nMoveLen*100));
						if(i>=nMoveLen){
							that.bRun	=	false;
							domMsg.removeAttribute('top');
							$D.style(domMsg,{'display':'none','position:':'fixed','_position':'absolute'});
							$D.setFilter(domMsg,100);
							_op.callback();
						}
					});
				}
			}
			else {
				normal();		
			}
		}
		return false;
	},
	'_isRun' : false,
	'_timer' : null
};

var Ttk = {};
Ttk.config = {
	cookie : {
		qn : 'quicked',
		expired : 24 * 3600 * 30
 	}	
};
Ttk.lib = {
	textInput : function(opt)
	{
		var option = {
			eventSource : null,
			emitEventName : 'click',
			getOriValCallback : function(eventSource)
			{
				return eventSource.innerHTML;
			},
			editorProperty : {},
			finishEvent : 'blur',
			onfinish : function(){},
			onbind : function(){}
		};
		$F.extend(option, opt);
		if (!option.eventSource.length) {
			option.eventSource = [option.eventSource];
		}
		$F.each(option.eventSource, function(eventSource, index){
			option.onbind(eventSource, index, option);
			$E.addEvent(eventSource, option.emitEventName, function(event) {												
				if (eventSource.childNodes[0] && eventSource.childNodes[0].tagName && eventSource.childNodes[0].tagName == 'INPUT' && 
					eventSource.childNodes[0].getAttribute('tag') == 'dynamic_created') {
					return ;
				}
				var target = event.target,
					input = document.createElement('input'),
					orival = option.getOriValCallback(eventSource, index);
				input.type = 'text';
				input.value = orival;
				input.tag = 'dynamic_created';
				(function(obj, val){
					for (var pro in val) {
						if (val.hasOwnProperty(pro)) {
							if (typeof val[pro] == 'object') {
								arguments.callee(obj[pro], val[pro]);
							} else {
								obj[pro] = val[pro];
							}
						}
					}
				})(input, option.editorProperty);
				eventSource.innerHTML = '';
				if (typeof option.finishEvent == 'function' || option.finishEvent != option.emitEventName) {
					$E.addEvent(input, option.emitEventName, function(event){
							event.cancelBubble = true;
					});
				}
				if (typeof option.finishEvent == 'function') {
					option.finishEvent(eventSource, input, index);
				} else {
					$E.addEvent(input, option.finishEvent, function(event){
						var target = event.srcElement;						
						option.onfinish(target.value, eventSource, orival, index, option);
					});
				}
				eventSource.appendChild(input);
				input.focus();
			});
		});
	},
	dialog : $F.$Class({
			mIdkey : null,
			mOptions : null,
			mDialog : null,
			mDialogTitle : null,
			mDialogClose : null,
			mDialogCont : null,
			_mDialog : null,
			/**
			 * @param object option = {
			 *		'title' : string,
			 *		'html' : string,
			 *		'onBeforeClose' : function,
			 *		'onClose' : function,
			 *		'className' : string,
			 *		'opactiy' : int(only for modalless dialog)
			 *		'ismodel' : boolean,
			 *		'zindex' : int, f_zindex : int,
			 *		'isShow' : true
			 * }
			 */
			initialize : function(option)
			{
				var html = '', that = this;
				this.mIdkey = '_editdialog_' + new Date().valueOf();
				this.mOptions = {
					title : '', html : '', 
					onBeforeClose : function(){},
					onClose : function(){},
					className : '',
					opacity : 40,
					zindex : 999,
					f_zindex : 9999,
					ismodal : true,
					isShow : true
				};
				$F.extend(this.mOptions, option);
				html = '<div id="dialog'+ this.mIdkey + '" class="edit_dialog' + (this.mOptions.className ? (' ' + this.mOptions.className + '"') : '"') + '><div class="ed_title"><h4 class="ed_t_inner" id="title' + this.mIdkey + '">' + this.mOptions.title + '</h4><a href="javascript:;" class="ed_close" id="close' + this.mIdkey + '"></a></div><div class="ed_cont" id="cont' + this.mIdkey +'">' + this.mOptions.html + '</div></div>';
				this._mDialog = new $B.extend.CDialog({
					'html' : html,
					'opacity' : this.mOptions.opacity,
					'frontID' : 'front' + this.mIdkey,
					'zindex' : this.mOptions.zindex,
					'f_zindex' : this.mOptions.f_zindex,
					'onclose' : this.mOptions.onClose,
					'oncreate' : function(data, src, bg)
					{
						that.mDialog = $A('dialog' + that.mIdkey);
						that.mDialogTitle = $A('title' + that.mIdkey);
						that.mDialogClose = $A('close' + that.mIdkey);
						that.mDialogCont = $A('cont' + that.mIdkey);
						$E.addEvent(that.mDialogClose, 'click', function(){that.close()});
						$B.extend.drag({'drager' : that.mDialogTitle.parentNode, 'moveObject' : src})
					}
				});
				if (this.mOptions.isShow) {
					this.show(this.mOptions.ismodal);
				}
			},
			show : function(isModal)
			{
				if (!$A('front' + this.mIdkey)) {
					isModal = !!isModal;
					isModal ? this._mDialog.showModal() : this._mDialog.showModalLess(); 			
				}
			},
			setTitle : function(title)
			{
				if (!$A('front' + this.mIdkey)) {
					that.mDialogTitle.innerHTML = title;
				}
			},
			close : function()
			{
				this.mOptions.onBeforeClose();
				this._mDialog.close();
			}
	}),
	CONST : {
		type : {
			1 : '电影',
			2 : '电视剧',
			3 : '综艺',
			4 : '动漫'
		},
		language : {
			0 : '默认',
			1 : '国语',
			2 : '粤语'
		}
	},
	getPlatform : function(platform, source)
	{
		if (!platform || !$F.inArray(['pc', 'web', 'phone'], platform)) {
			platform = 'pc';
		}
		platform = 'is' + platform;
		source = source || g_SourceInfo;
		var ret = [];
		$F.each(source, function(it){
			if (it[platform] == 1) {
				ret.push(it);
			}
		});
		return ret;
	},
	generateSelect : function(option)
	{
		var opt = {
			name : '', id : '', currValue : null,
			className : '', platform : '', ret : true
		}, source, ret = '';
		$F.extend(opt, option);
		source = opt.type;
		
		ret = '<select ' + (opt.name ? ('name="' + opt.name + '" ') : '') + (opt.id ? ('id="' + opt.id + '" ') : '') + (opt.className ? ('class="' + opt.className + '"') : '') + '>';
		$F.each(source, function(name, key){
			ret += '<option value="' + key + '"' + (opt.currValue == key ? ' selected="selected"' : '') + '>' + name + '</option>';							 
		}, true);
		
		ret += '</select>';
		if (typeof opt.ret == 'object') {
			opt.ret.innerHTML = ret;
		} else {
			if (opt.ret) {
				return ret;
			}
			document.write(ret);
		}
	},
	showTag : function(key)
	{
		var mTagTitleWraper = $A('cont'),
			mTagContWraper  = $A('soc_form');	
		var collstitle = $D.getChildNodes(mTagTitleWraper),
			collscont = $D.getChildNodes(mTagContWraper),
			ret = false, index = null;
		if (typeof(key) == 'object') {
			$F.each(collstitle, function(it, i){
				if (it === key) {
					index = i;
					return false;
				}
			});
			if (index === null) {
				return false;
			}
		}
		if ($F.isInt(index, true) && collstitle.length > index) {
			$F.each(collstitle, function(it, i){
				$D.removeClass(it, 'selected');
				collscont[i].style.display = 'none';
			});
			$D.addClass(collstitle[index], 'selected');
			if (!collscont[index].src) {
				collscont[index].src = collscont[index]._src;
				if (!collscont[index].isloaded) {
					//this.mTagLoadding.style.display = 'block';
					$E.addIframeEvent(collscont[index], 'load', (function(owner, target){
						return function()
						{
							target.isloaded = 1;
						}
					})(this, collscont[index]));							
				}
			}
			collscont[index].style.display = '';
		}
		return ret;			
	},
	removeTags : function(key)
	{
		var mTagTitleWraper = $A('cont'),
			mTagContWraper  = $A('soc_form');
		var collstitle = $D.getChildNodes(mTagTitleWraper),
			collscont = $D.getChildNodes(mTagContWraper),
			ret = false, index = null;

		if (typeof(key) == 'object') {
			$F.each(collstitle, function(it, i){
				if (it === key) {
					index = i;
					return false;
				}
			});
			if (index === null) {		
				return false;
			}
		}
		if ($F.isInt(index, true) && collstitle.length > index) {
			mTagTitleWraper.removeChild(collstitle[index]);
			mTagContWraper.removeChild(collscont[index]);
		}
		collstitle = $D.getChildNodes(mTagTitleWraper);
		if (collstitle.length) {
			collstitle[collstitle.length - 1].fireEvent('onclick');
		}
		return ret;
	},
	addTags : function(tags)
	{

		var mTagTitleWraper = $A('cont'),
			mTagContWraper  = $A('soc_form');
		if ($F.isArray(tags)) {
			for (var i = 0; i < tags.length; i ++) {
				this.addTags(tags[i]);
			}
		} else {
			var tempa, tempem, tempframe;
			tempa = document.createElement('a');
			tempem = document.createElement('em');
			tempframe = document.createElement('iframe');
			tempem.innerHTML = '\u00d7';
			tempem.onclick = (function(owner) {
				return function(event)
				{
					if ($B.browser.ie) {
						window.event.cancelBubble = true;
					} else {
						event.preventDefault = true;
					}
					var target = $B.browser.ie ? window.event.srcElement : event.target;
					owner.removeTags(target.parentNode);
				};
			})(this);
			tempa.innerHTML = tags.name;
			tempa.appendChild(tempem);
			tempa.href = 'javascript:;';
			tempa.title = tags.name;
			tempa.keywords = encodeURIComponent(tags.param.q);
			tempa.key = encodeURIComponent(tags.param.key);
			tempa.onclick = (function(owner) {
				return function(event)
				{
					var target = $B.browser.ie ? window.event.srcElement : event.target;
					owner.showTag(target);
					target.blur();
				};
			})(this);
			if (tags.isshow) {
				tempa.className = 'selected';
			}
			mTagTitleWraper.appendChild(tempa);
			tempframe.width = '100%';
			tempframe.height = '100%';
			tempframe.marginHeight = 0;
			tempframe.marginWidth = 0;
			tempframe.frameBorder = 0;
			switch(tags.type)
			{
				case 1 : 
						tempframe._src = Router.get('update', 'index', tags.param);
						break;
				case 2 : 
						tempframe._src = Router.get('update', 'select', tags.param);
						break;
				case 3 : 
						tempframe._src = tags.url;
						break;
			}
			if (tags.isshow) {
				tempframe.src = tempframe._src;
				$E.addIframeEvent(tempframe, 'load', (function(owner, target){
					return function()
					{
						target.isloaded = 1;
					}
				})(this, tempframe));						
			} else {
				tempframe.style.display = 'none';
			}
			mTagContWraper.appendChild(tempframe);
		}
		if (tags.isshow) {
			this.showTag(tempa);
		}
		return this;
	},
	searchTag : function(key, keywords)
	{
		if (key) {
			key = encodeURIComponent(key);
		}
		if (keywords) {
			keywords = encodeURIComponent(keywords);
		}
		
		if (!key && !keywords) {
			return null;
		}
		var mTagTitleWraper = $A('cont'),
			mTagContWraper  = $A('soc_form');
		var ret = [], frames = $D.getChildNodes(mTagContWraper);
		
		$F.each($D.getChildNodes(mTagTitleWraper), function(it, i){
			if (key && keywords) {
				if (it.getAttribute('key') == key && it.getAttribute('keywords') == keywords) {
					ret = {title : it, frame : frames[i]};
					return false;
				}
			} else if (key) {
				if (it.getAttribute('key') == key) {
					ret.push({title : it, frame : frames[i]});
				}
			} else {
				if (it.getAttribute('keywords') == keywords) {
					ret.push({title : it, frame : frames[i]});
				}						
			}
		});
		return $F.isArray(ret) && !ret.length ? null : ret;
	},
	'getUrl' : function(c, a, p) {
		return 	Router.get(c, a, p);
	},
	'updateForm' : function(name, id)
	{
		var that = this,
			self = arguments.callee,
			source = {
			'360' : '360',
			'Baidu' : '百度',
			'Yisou' : '一搜',
			'Douban' : '豆瓣'
		};
		self.name = name;
		self.id = id;
		self.isRun = true;
		var autoDialog = new this.dialog({
			'className' : 'custom_auto_add_dialog',
			ismodal : false,
			'title' : '智能更新影片',
			'html' : '<form name="intell_option_frm"><ul class="intell_option_frm"><li class="title">选项：</li><li><input class="checkbox" value="1" type="checkbox" name="is_redirect"/><span>快速更新所有信息</span></li><li style="display:none;" id="lreme"><input class="checkbox" type="checkbox" value="0" name="is_reme"/><span>更新记住选项</span></li></ul></form><div class="soc_func"><form name="auto_add_source_form"><label>搜索词</label><input type="text" name="q" class="big_inputtext" style="width:400px;" value="' + name + '"><span>' + this.generateSelect({type : source, name : 'intelligent_source', className : 'select'}) + '</span><input type="submit" class="bd_btn" value="搜索"/><input type="button" name="add_all_auto_source" class="bd_btn" value="搜索全部"/></form></div><div id="cont" class="soc_tags"></div><div id="soc_form"></div>',
			'onClose' : function(){self.isRun = false}
		});
		
		$E.addEvent(document.intell_option_frm.is_redirect, 'click', function(){													  
			var domLReme = $A('lreme');	
			if (document.intell_option_frm.is_redirect.checked) {
				lreme.style.display	= 'block';
			} else {
				lreme.style.display	= 'none';	
				document.intell_option_frm.is_reme.checked = false;
			}
																		  
		});
		self.setKeywords = function(k)
		{
			this.mForm.q.value = k;
			$E.fireEvent(this.mForm, 'submit');
		};
		self.mForm  = document.auto_add_source_form;
		self.mForm.onsubmit = function(event)
		{
			var target, source, keywords, sourceName, param,
				sname = 'intelligent_source';
			if ($B.browser.ie) {
				target = window.event.srcElement;
				window.event.returnValue = false;
			} else {
				target = event.target;
				event.returnValue = false;
			}
			
			keywords = target.q.value;
			source = target[sname].options[target[sname].selectedIndex].value;
			sourceName = target[sname].options[target[sname].selectedIndex].innerHTML;
			param = {'q' : keywords, 'key' : source, 'sourceName' : sourceName, id : self.id};			
											 
			if ($F.isEmpty(keywords)) {
				return msgBox.show({type : 'warn', msg : '请输入搜索关键词'});
			}
			if ($F.isEmpty(source)) {
				return msgBox.show({type : 'warn', msg : '请选择资源'});
			}
			
			if (!that.searchTag(source, keywords)) {
				Ttk.lib.addTags({type : 1, name : sourceName, isshow : 1, param : param});
			} else {
				return msgBox.show({type : 'warn', msg : '该标签已经存在'});
			}
		};
		
		self.mForm.add_all_auto_source.onclick = function(event)
		{
			var target = $B.browser.ie ? window.event.srcElement : event.target,
				q = target.form.q.value, param;
			if (!$F.isEmpty(q)) {
				$F.each(target.form.intelligent_source.options, function(it){
					if (it.value && !that.searchTag(it.value, q)) {
						param = {q : q, key : it.value,  id : self.id};
						that.addTags({type : 1, name : it.innerHTML, isshow : 0, param : param});
					}
				});
			}
		};		
	},
	'intellSearch' : function(id)
	{
		$B.ready(function(){
			var domLoading = $A('loadding'), callbackKey = ['publicMovie', 'publicEdit'];
			var domResult = document.getElementById('itell_result') ;
			if (domResult == null) {
				return;
			}
			
			$F.each(domResult.getElementsByTagName('a'), function(dom) {	
				switch (dom.getAttribute('tag')) {
					case 'open' : 
						$E.addEvent(dom, 'click', function(event) {
							event.returnValue = false;
							var target = event.srcElement;
							while(target.tagName != 'A') {
								target = target.parentNode;
							}
							var name = target.getAttribute('_moviename');
							parent.Ttk.lib.addTags({type : 3, isshow : 1, name : name, param : {id : id, q : name, url : target.getAttribute('_url'), key : target.getAttribute('_source')}, url : target.getAttribute('_url')});
						});
						break;
					case 'collect':
						$E.addEvent(dom, 'click', function(event) {		
							event.returnValue = false;
							var target = event.srcElement;
							while(target.tagName != 'A') {
								target = target.parentNode;
							}
							
							domQuickUpdate = parent.document.intell_option_frm.is_redirect;	
							domQuickReme = parent.document.intell_option_frm.is_reme;	
							var name = target.getAttribute('_moviename');
							var param = {q : name, url : target.getAttribute('_url'), key : target.getAttribute('_source'), id : id, ext : target.getAttribute('_ext')};
							
							if (domQuickUpdate.checked) {
								if (domQuickReme.checked) {
									var checkRemes = $C.get('remeCheck'),
										remeData;
									if (checkRemes == '') {
										return msgBox.show({type : 'warn', msg : '未选中任何更新选项'});
									} else {
										eval('remeData='+ checkRemes);
										var tempData = {}, num = 0;
										$F.each(remeData, function(item, key){
											if (item == 'true') {
												tempData[key] = 1;
												num++;
											}						   
										}, true);
										if (num == 0) {
											return msgBox.show({type : 'warn', msg : '未选中任何更新选项'});	
										}
										param['data'] = $B.json.stringify(tempData);
									}
								} else {
									param['data'] = 'all';	
								}
								
								$B.ajax({
									'url' : parent.Ttk.lib.getUrl('update', 'quick', param),
									'type' : 'GET',
									'success' : function(s) {
										s == 'succ' ? msgBox.show({type : 'succ', msg : '更新成功'}) : msgBox.show({type : 'error', msg : '更新失败'});
									}
								});
								return;
							}

							var name = target.getAttribute('_moviename');
							var param = {q : name, url : target.getAttribute('_url'), key : target.getAttribute('_source'), id : id, ext : target.getAttribute('_ext')};
							parent.Ttk.lib.addTags({type : 2, isshow : 1, name : name, param : param});
						});
						break;
				}
			});
		});		
	}
};

Ttk.action = {};
Ttk.action.videoIndex = function()
{
	$B.ready(function(){
		$F.each(document.getElementsByName('btn_intupdate'), function(ele){
			$E.addEvent(ele, 'click', function(e){
				var target = e.target,
					name = target.getAttribute('dataname'),
					id = target.getAttribute('dataid');							   
				e.returnValue = false;
				if (Ttk.lib.updateForm.isRun === true) {
					Ttk.lib.updateForm.id = id;
					Ttk.lib.updateForm.setKeywords(name);
				} else {				
					Ttk.lib.updateForm(name, id);
				}
			});
		});
	});	
};