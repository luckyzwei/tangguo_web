
var BC = {
	pub : function()
	{
		var that = arguments.callee;
		for (var k in that.components) {
			if (that.components.hasOwnProperty(k) && typeof(that.components[k]) == 'function') {
				that.components[k]();
			}
		}
	}
};

/**
 * 工具
 */
BC.utils =
{
	'regexpQuote' : function(str, delimiter/*=''*/)
	{
		return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
	},
	
	'replaceEx' : function(str, search, replaceMent, bIgnore/*=false*/)
	{
		return (str + '').replace(new RegExp(BC.utils.regexpQuote(search), bIgnore ? 'g' : 'gi'), replaceMent);
	},
	
	'extend' : function(targetClass, sourceClass, bReserveOwnProperty/*=false*/)
	{
		if (bReserveOwnProperty) {
			for (var s in sourceClass) {
				if (!targetClass.hasOwnProperty(s)) {
					targetClass[s] = sourceClass[s];
				}
			}
		}
		else {
			for (var s in sourceClass) {
				targetClass[s] = sourceClass[s];
			}
		}
	}
};

Array.prototype.IndexOf = function(i) {
	return this[i];
}
/**
 * 公用函数
 */
BC.lib = { 
	/**
	 * 是否静态模式
	 */
	'isStaticModel' : function()
	{
		return g_aCfg.site_mode == 1;
	},
	
	/**
	 * 是否正常播放
	 */
	'isNnormalModel' : function()
	{
		return g_aCfg.play_mode == 1;
	},
	
	/**
	 * @param string model
	 * @param map _opt = {random : 1 | 0, cache : 1 | 0, add : 1 | 0}
	 */
	'getModelHost' : function(model, _opt)
	{
		var host_map = g_aCfg.router.host_map,
			random_host_map = g_aCfg.router.random_host_map,
			opt = {random : 0, cache : 1, add : 1},
			map, self = arguments.callee, ret;
		_opt = _opt || {};
		BC.utils.extend(opt, _opt);
		
		if (!opt.random || !random_host_map[model]) {
			return host_map[model] || '/';
		}
		
		if (opt.cache) {
			if (!self.cache) {
				self.cache = {};
			} else if (self.cache[model]) {
				return self.cache[model];	
			}
		}
		
		if (!BC.lib.isArray(random_host_map[model])) {
			if (random_host_map[model]) {
				random_host_map[model] = random_host_map[model].split(',');
			} else {
				random_host_map[model] = [];
			}
		}
		
		map = Array.apply(null, random_host_map[model]); 
		opt.add && map.push(host_map[model]);
		ret = map[new Date().valueOf() % map.length];
		
		if (opt.cache) {
			self.cache[model] = ret;
		}
		return ret || '/';
	},
	
	'isInt' : function(num, ext)
	{
		ext = ext || false;
        return (ext ? /^\d+$/gi : /^-?\d+$/gi).test(num + "");
	},
	
	'isPhone' : function(phone)
	{
		var reg = /^1([38]\d|4[57]|5[0-35-9]|7[06-8]|8[89])\d{8}$/;
		if (reg.test(phone)) {
		    return true;
		} else {
			return false;
		}
	},
	
	'isNumber' : function (num, bPostive/*=false*/)
	{
		return (bPostive ? (/^((\d+\.\d+)|(\d+))$/gi) : (/^-?((\d+\.\d+)|(\d+))$/gi)).test(num + '');
	},
	
	'isArray' : function(a) {
        return a && a.constructor === Array;
    },
	
	/**
	 * 解析时间（用于评论）
	 */
	'parseTime' : function(iTime)
	{
		var oDate = new Date(),
			iCurrent = parseInt(oDate.valueOf() / 1000),
			iSeconds = iCurrent - iTime,
			sReturn = '';
		
		if(iSeconds <60) {
			sReturn = '刚刚';
		} else if (iSeconds<3600) {
			sReturn = Math.ceil(iSeconds / 60) + '分钟前';
		} else if (iSeconds < 3600 * 24) {
			sReturn = Math.ceil(iSeconds / 3600)+'小时前';
		} else if(iSeconds < 3600 * 24 * 7) {
			sReturn = Math.ceil(iSeconds / (3600 * 24)) + '天前';
		} else {
			sReturn =  new Date(parseInt(iTime) * 1000).toLocaleString().replace(/\//g, '-').substr(0,9);
		}
		return sReturn ;
	},
	
	
	/**
	 * 解析日期（用户个人中心播放记录）
	 */
	'parseDate' : function(iTime)
	{
		var oDate = new Date(),
			iCurrent = parseInt(oDate.valueOf() / 1000);
			
		var t = iCurrent - iTime;
		
		if (t < 86400) {
			return '今天';
		} else if (t > 86400 && t < 172800 ) {
			return '昨天';
		}
		
		return new Date(parseInt(iTime) * 1000).toLocaleString().replace(/\//g, '-').substr(0,9);
	},
	
	/**
	 * 模仿PHP的date()函数
	 * strtotime('Y-m-d H:i:s');
	 * @param format 只支持 'Y-m-d H:i:s','Y-m-d','H:i:s' 三种调用方式
	 * @param time 为空时，取当前时间
	 * @return 日期格式化的字符串
	 */
	'date' : function(format, time)
	{
	    var _temp = (time != null) ? new Date(time*1000) : new Date();
	    var _return = '';
	
	    if(/Y-m-d/.test(format)){
	        var _day = [_temp.getFullYear(),addzero(1 + _temp.getMonth()),addzero(_temp.getDate())];
	        _return = _day.join('-');
	    }
	    if(/H:i:s/.test(format)){
	        var _time = [addzero(_temp.getHours()),addzero(_temp.getMinutes()),addzero(_temp.getSeconds())];
	        _return += ' ' +_time.join(':');
	    }
	    
   		return _return;
    	
    	function addzero(i){
	        if(i<=9){
	            return '0' + i;
	        }else{
	            return i;
	        }
    	}
	},
	
	
	/**
	 * 模仿PHP的strtotime()函数
	 * strtotime('2012-07-27 12:43:43') OR strtotime('2012-07-27')
	 * @return 时间戳
	 */
	'strtotime' : function(str)
	{
	    var _arr = str.split(' ');
	    var _day = _arr[0].split('-');
	    _arr[1] = (_arr[1] == null) ? '0:0:0' :_arr[1];
	    var _time = _arr[1].split(':');
	    for (var i = _day.length - 1; i >= 0; i--) {
	        _day[i] = isNaN(parseInt(_day[i])) ? 0 :parseInt(_day[i]);
	    };
	    for (var i = _time.length - 1; i >= 0; i--) {
	        _time[i] = isNaN(parseInt(_time[i])) ? 0 :parseInt(_time[i]);
	    };
	    var _temp = new Date(_day[0],_day[1]-1,_day[2],_time[0],_time[1],_time[2]);
	    return _temp.getTime() / 1000;
	},

	/**
	 * 判断字符串是否为空
	 */
	'isEmpty' : function(a) {
        return (a + "").length <= 0;
   	},
	
	'url' : {
		"delimiter" : "\/",
		"param_name" : "s",
		
		'encode_map' : {'/' : '~@:@~'},
		'encode' : function (str)
		{
			for (var repl in this.encode_map) {
				str = BC.utils.replaceEx(str, repl, this.encode_map[repl]);	
				
			}
			return encodeURIComponent(str);
		},
		'decode' : function (str)
		{
			str = decodeURIComponent(str);
			for (var repl in this.encode_map) {
				str = BC.utils.replaceEx(str, this.encode_map[repl], repl);
			}
			return str;
		},
		/**
		 * 获取url
		 */
		'get' : function(controllor, action, params)
		{
			controllor = controllor || '';
			action = action || '';
			params = params || {};
			var params = this.make(params);
			return '/index.php?' + this.param_name + '=' + controllor + this.delimiter + 
					action + (params ? (this.delimiter + params) : '');
		},
		
		/**
		 * 拼接参数
		 */
		'make' : function(params)
		{
			var ret = [];
			for (var name in params) {
				ret.push(this.encode(name));
				ret.push(this.encode(params[name]));
			}
			return ret.join(this.delimiter);
		},
		
		'getItemLink' : function(id)
		{
			if (BC.lib.isStaticModel()) {
				return BC.lib.getModelHost('static') + 'html/' + id + '.' + g_aCfg.static_file_extendtion;
			} else {
				return this.get('item', '', {'id' : id});
			}
		}
	},
	
	'trim' : function(a) {
        return (a + "").replace(/(^\s*)|(\s*$)/gi, "");
    },
        
	/**
	 * 提示消息
	 */
	'tips' : function(text, _css)
	{
		var text = text || '系统繁忙，请稍后再试！';
		var tip = $('#tips');
		tip.html(text);
		if (_css) {
			tip.css(_css);
		}
		tip.fadeIn(800).fadeOut(500);
	}
};

BC.model = {
	
	/**
	 * 相关操作
	 */
	'BC' : {
		/**
		 * 内容评论
		 * param object
		 * callback 评论完毕回调函数
		 */
		'comment' : function(param, callback)
		{
			$.ajax({
				type: 'POST',
				url : BC.lib.url.get('item', 'reply'), 
				data: param,
				success: function(data){
					callback(data);
				}
			});
		},
		
		/**
		 * 内容页电影评分
		 * param object
		 * callback 评论完毕回调函数
		 */
		'pointer' : function(id, point, callback)
		{
			$.ajax({
				url : BC.lib.url.get('item', 'point', {'id' : id, 'point' : point}), 
				success : function(data) {
					data = JSON.parse(data);
					callback(data);
				}
			});
		}
		
	},
	/**
	 * 评论
	 */
	'comment' : {
			
		'mid' : 0,
		
		/**
		 * 创建评论列表
		 */
		'createCommentList' : function()
		{
			var warp =  $('<div></div>');
			warp.attr('class', 'w-comment-list');
			
			return warp;
		},
		
		/**
		 * 创建最底楼评论人信息
		 */
		'createHead' : function(head)
		{
			var comment_item = $('<div class="comment-top clearfix"></div>');
	
			var _p_name = $('<p class="w-item-nickname pull-left"></p>');
			var _p_date = $('<p class="w-item-date pull-right"></p>');
			
			_p_name.html(head.username);
			_p_date.html(BC.lib.parseTime(head.time));
						
			comment_item.append(_p_name);
			comment_item.append(_p_date);
	
			return comment_item;
		},
		
		
		/**
		 * 创建盖楼内容
		 */
		'createContent' : function(ret)
		{
			
			var item_warp = $('<div class="w-item-warp clearfix"></div>');
			
			if (!ret ||ret.length == 0) {
				return item_warp;
			}
	
			$(ret.floor_msg).each(function(i, obj){
				var itemCitation = $('<div></div>');
				if (i == 0) {
					itemCitation.attr('class', 'w-item-citation-first');
				} else {
					itemCitation.attr('class', 'w-item-citation');
				}
				
				itemCitation.html('<h4 class="clearfix"><span>' + obj.username +'</span><em>' + (i+1) + '</em></h4>'
					+ '<p class="w-item-content">' + obj.msg + '</p>');
				
				item_warp.append(itemCitation);
			});					        		        
			
			var _citation = $('<p class="w-item-con-self"></p>');
			_citation.html(ret.msg);
			_citation.attr('commid', ret.commid);
		
			var dom_area = $('<div class="reply-textarea clearfix" commid="'+ ret.commid +'" uid="' + ret.uid + '"></div>');
			var _do = $('<div class="w-item-do pull-right" commid="'+ ret.commid +'"></div>');
			
			
			var dom_rep = $('<a href="#" class="comment-reply">回复</a>');
			var dom_down = $('<a href="#" class="comment-down">踩(<span>'+ ret.down+'</span>)</a>');
			var dom_up = $('<a href="#" class="comment-up">顶(<span>'+ ret.up +'</span>)</a>')
					
			_do.append(dom_rep);
			_do.append(dom_down);
			_do.append(dom_up);
			item_warp.append(_citation);
			item_warp.append(dom_area);
			item_warp.append(_do);
			
			return item_warp;
		},
		
		/**
		 * 请求分页数据
		 */
		'get' : function(_url, callback)
		{
			$.ajax({
				url :  _url, 
				success : function(data) {
					data = JSON.parse(data);
					callback(data);
				}
			});
		},
		
		/**
		 * 评论踩
		 * @params commid 评论id
		 * @params isDown 是顶还是踩 默认踩
		 * @callback 回调显示点赞状态
		 */
		'praise' : function(commid, isDown, callback)
		{
			$.ajax({
				url : BC.lib.url.get('item', 'praise', {'commid' : commid, 'isDown' : isDown}),
				success : function(data) {
					data = JSON.parse(data);
					callback(data);
				}
			});
		},
		
		/**
		 * 评论回复 dom 对象创建
		 */
		'comment_reply' : function()
		{
			$('.comment-reply').each(function(i, obj){
				$(obj).on('click', function(e){
					
					var r = BC.model.user._isLogin();
					if (!r) {
						return false;
					}
					
					e.preventDefault();
					$('.reply-textarea').each(function(j,o){
						var o = $(o);
						o.html('');
						o.attr('style', 'margin-bottom:0px');
					});
					var that = $(e.target);
					var part = that.parent();
					part = $(part).parent();
					var reply_textarea = part.find('.reply-textarea');
					if (reply_textarea.find('textarea').length == 0) {
						reply_textarea.attr('style', 'margin-bottom: 20px;');
						var dom_text_area = $('<textarea></textarea>');
						var dom_btm_sub = $('<span>提交</span>');
						var dom_r = $(reply_textarea[0]);
						dom_r.append(dom_text_area);
						dom_r.append(dom_btm_sub);
					
						dom_btm_sub.on('click', function(_e){
							var _that = $(_e.target).parent();
							var commid =  _that.attr('commid');
							var COOKIE_NAME = 'comment_reply_mid' + commid;
							
							if ($.cookie(COOKIE_NAME) == commid) {
								BC.lib.tips('歇息一会再来评论吧！');
								return false;
							} else {
								$.cookie(COOKIE_NAME, commid , { path: '/', expires: 300 }); 
							}
							
							var area = _that.find('textarea')[0];
							area = $(area); 
							if (area.val() == '') {
								BC.lib.tips('总该说些什么吧！');
								return false;
							}
							
							if (area.val().length > 2000) {
								BC.lib.tips('回复内容不能超过2000个汉字！');
								return false;
							}
							
							BC.model.comment.add(area.val(), commid, function(data){
								if(data.s == 1) {
									BC.model.comment.reload();
								} else {
									BC.lib.tips();
								}
							});
						});
					}
				});
			});
		},
		
		/**
		 * 评论踩 dom 对象创建
		 */
		'comment_down' : function()
		{
			$('.comment-down').each(function(i, obj){
				$(obj).on('click', function(e){
					e.preventDefault();
					var that = $(e.target);
					if (parseInt(that.html()) >= 0) {
						that = that.parent();
					}
					that = that.parent();
					var commid = that.attr('commid');
					var COOKIE_NAME = 'commid_' + commid; 
					if ($.cookie(COOKIE_NAME) == commid) {
						BC.lib.tips('您已经踩过了！');
						return false;
					} else {
						$.cookie(COOKIE_NAME, commid , { path: '/', expires: 86400 }); 
					}
					
					BC.model.comment.praise(commid, 1, function(data){
						if (data.s == 1) {
							var _span  = $(that.find('span')[0]);
							var oldNum = _span.html();
							_span.html(parseInt(oldNum) + 1);
						}
					});
				});
			});
		},
		
		/*
		 * 评论点赞 dom 创建
		 */
		'comment_zan' : function()
		{
			$('.comment-up').on('click', function(e){
				e.preventDefault();
				var that = $(e.target);
				if (parseInt(that.html()) >= 0) {
					that = that.parent();
				}
				that = that.parent();
				var commid = that.attr('commid');
				var COOKIE_NAME = 'commid_up_' + commid; 
				if ($.cookie(COOKIE_NAME) == commid) {
					BC.lib.tips('您已经点过赞了！');
					return false;
				} else {
					$.cookie(COOKIE_NAME, commid , { path: '/', expires: 86400 }); 
				}
				
				BC.model.comment.praise(commid, 0, function(data){
					if (data.s == 1) {
						var _span  = $(that.find('span')[1]);
						var oldNum = _span.html();
						_span.html(parseInt(oldNum) + 1);
					} else {
						BC.lib.tips();
					}
				});
			});
		},
		
		/**
		 * 发表评论或回复
		 * @params uid 用户id
		 * @params msg 评论内容
		 * @params commid 回复评论id
		 * @callback 回调
		 */
		'add' : function(msg, commid, callback)
		{
			$.ajax({
				type: 'POST',
				url : BC.lib.url.get('item', 'add'),
				data: {'msg' : msg, 'commid' : commid, 'mid' : BC.model.comment.mid},
				success : function(data) {
					data = JSON.parse(data);
					callback(data);
				}
			});
		},
		
		/**
		 * 列表显示
		 * @param url
		 */
		'show' : function(url, callback, is_hot)
		{
			callback = callback || function(){};
			
			var _warp ;
			if (is_hot) {
				_warp = $('#_warp_hot');
			} else {
				_warp = $('#_warp');
			}
			
			BC.model.comment.get(url, function(data){
				if (is_hot) {
					_warp.html('');
				}
				
				if (data.s == 0) {
					var more =  $('<p></p>');
					more.attr('id', 'nomore');
					more.attr('class', 'w-more');
					more.html('没有评论了！');
					_warp.append(more);

					callback(0, _warp);						
					return false;
				}
					
				$(data.d).each(function(i,obj){
					
					var commentList = BC.model.comment.createCommentList();
					var head = BC.model.comment.createHead(obj);
					commentList.append(head);
					var warp = BC.model.comment.createContent(obj);
					commentList.append(warp);
					_warp.append(commentList);
					
				});
				
				callback(1, _warp);
			});
			
		},
		
		
		/**
		 * 重新渲染评论列表
		 */
		'reload' : function()
		{
			$("#_warp").html('');
			$("#more_comment").remove();
			BC.model.comment.index(BC.model.comment.mid);
		},
	
		/**
		 * 更多..
		 */
		'createMore' : function(id)
		{
			var id = id || 'more_comment';
			var more = $('<p></p>');
			more.attr('class', 'w-more');
			more.attr('id', id);
			more.html('查看更多');
			
			return more;
		},
		
		/**
		 * 最新评论列表入口
		 */
		'index' : function(mid)
		{
			var mid = mid || 74795;
			var com_body = $('#com_body');
			var loading = $('#loading');
			var _url  = BC.lib.url.get('item', 'list', {'mid' : mid, 'maxid' : 0});
			loading.show();
			
			BC.model.comment.mid = mid;
			BC.model.comment.show(_url, function(_more, _warp){
				loading.hide();
				com_body.append(_warp);
				
				if (_more == 1) {
					var more = BC.model.comment.createMore();
					com_body.append(more);
				}
				
				/*评论回复*/
				BC.model.comment.comment_reply();
				
				/*评论点踩*/
				BC.model.comment.comment_down();
				
				/*评论点赞*/
				BC.model.comment.comment_zan();
				
				/**
				 * 分页加载更多
				 */
				more && more.on('click', function(){
					var commentItemList = $('.w-item-con-self');
					var commid = $(commentItemList[commentItemList.length-1]).attr('commid'); //取最后一条记录的id，用来分
					BC.model.comment.show(BC.lib.url.get('item', 'list', {'mid' : mid, 'maxid' : commid}), function(__more, __warp){
						if (__more == 0) {
							more.remove();
						}
						_warp.append(__warp);
						
						BC.model.comment.comment_reply();
						
						BC.model.comment.comment_down();
						
						BC.model.comment.comment_zan();
					},0);
				});				
			}, 0);
			
			
			$('#comment_frm').on('submit', function(e){
				e.preventDefault();
				var r = BC.model.user._isLogin();
				if (!r) {
					return false;
				}
				var cont = $(e.target.com_cont);
				var msg = cont.val();
				if (msg == '') {
					BC.lib.tips('总该说些什么吧！');
					return false;
				}
				if (msg.length > 2000) {
					BC.lib.tips('评论不能超过2000个字！');
					return false;
				}
				
				BC.model.comment.add(msg, 0, function(data){
					if (data.s > 0) {
						cont.val('');
						BC.model.comment.reload();
					} else {
						BC.lib.tips();
					}
				});
			});
		},
		
		/**
		 * 最热评论列表入口
		 */
		'index_hot' : function(mid) {
			var mid = mid || 74795;
			var com_body = $('#com_hot_body');
			var loading = $('#loading_hot');
			var _url  = BC.lib.url.get('item', 'list', {'mid' : mid, 'maxid' : 0, 'hot' : 1});
			loading.show();				
			BC.model.comment.mid = mid;	
			loading.show();
			BC.model.comment.mid = mid;
			BC.model.comment.show(_url, function(_more, _warp){
				loading.hide();
				com_body.append(_warp);
				
				/*评论回复*/
				BC.model.comment.comment_reply();
				
				/*评论点踩*/
				BC.model.comment.comment_down();
				
				/*评论点赞*/
				BC.model.comment.comment_zan();	
			},1);
		}
	}
};


BC.model.user = {
	
	req : {
		login : function(param, callback)
		{
			$.ajax({
				type: 'POST',
				url : BC.lib.url.get('member', 'login2'),
				data : param,
				success : function(data){
					data = JSON.parse(data);
					callback(data);
				}
			});
			
		},
		
		reg : function(param, callback)
		{
			$.ajax({
				type : 'POST',
				url : BC.lib.url.get('member', 'register2'),
				data : param,
				success : function(data){
					data = JSON.parse(data);
					callback(data);
				}
			});
		}
	},
	
	login : function()
	{
		var COOKIE_USERNAME = '__c__';
		var COOKIE_UID = '__e__';
		
		var uname = $.cookie(COOKIE_USERNAME);
		var data_target = $('#login_name').parent();
		
		BC.model.user._isLogin(0);
		
		$('#bc_login').on('submit', function(e){
			e.preventDefault();
			if (e.target.username.value == '' || e.target.username.value.length > 50) {
				e.target.username.style.border = '1px solid red';
				return false;
			}
			e.target.username.style.border = '1px solid #ccc';
			
			if (e.target.password.value == '' || e.target.password.value.length > 50) {
				e.target.password.style.border = '1px solid red';
				return false;
			}
			
			e.target.password.style.border = '1px solid #ccc';
			
			BC.model.user.req.login($(e.target).serialize(), function(data){
				if (data.s == 1) {
					var name = $('#login_name');
					name.addClass('blue');
					name.html('');
					$('#myModal').modal('hide');
					$('#user_name').html('');
					$('#info_username').html(uname);
					$('#login_pannel').html('<a>用户'+ uname +'</a>');
					$(data_target).removeAttr('data-target');
					
					BC.model.user.showMenu();
					BC.lib.tips('登录成功！');
					/*if (!data.d.isDetail) {
						window.location.href = g_aCfg.router.host_map.member;												
					}*/
					return true;
				}
				
				if (data.s < 0) {
					BC.lib.tips(data.err_str, {'top' : '33px'});				
				}
			});
			
		});
		
		$('#login_name').on('click', function(){
			if (BC.model.user.isLogin()) {
				window.location.href = g_aCfg.router.host_map.member;
			}
		});
		
		$('#btn_register').on('click', function(){
			$('#myModal').modal('hide');
			$('#registerModal').modal('show');
		});
		
		$('#register_return').on('click', function(){
			$('#registerModal').modal('hide');
			$('#myModal').modal('toggle');
		});
		
		if (BC.model.user.isLogin()) {
			BC.model.user.showMenu();
		}
		
		$('#loginout').on('click', function(){
			$.ajax({
				type: 'get',
				url : BC.lib.url.get('member', 'loginout'),
				success : function(data) {
					$('#login_name').removeClass('blue');
					$('#user_name').html('登录');
					$('#user_info').remove();
					BC.lib.tips('退出成功!');
				}
			});
		});
		
		$('#bc_register').on('submit', function(e){
			e.preventDefault();
			
			if (e.target.username.value == '' || e.target.username.value.length > 50) {
				e.target.username.style.border = '1px solid red';
				return false;
			}
			
			e.target.username.style.border = '1px solid #ccc';
			
			if (e.target.password.value == '' || e.target.password.value.length > 50) {
				e.target.password.style.border = '1px solid red';
				return false;
			}
			
			e.target.password.style.border = '1px solid #ccc';
			
			if (e.target.repassword.value == '' || e.target.repassword.value.length > 50) {
				e.target.repassword.style.border = '1px solid red';
				return false;
			}
			
			e.target.repassword.style.border = '1px solid #ccc';
			
			if (e.target.email.value == '' || e.target.email.value.length > 50 ) {
				e.target.email.style.border = '1px solid red';
				return false;
			}
			
			e.target.email.style.border = '1px solid #ccc';
			
			if (!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(e.target.email.value)) {
				BC.lib.tips('邮箱地址不正确！', {'top' : '150px'});
				return false;
			}
			
			if (e.target.password.value != e.target.repassword.value ) {
				BC.lib.tips('两次密码不一致！', {'top' : '150px'});
				return false;
			}
			
			BC.model.user.req.reg($(e.target).serialize(), function(data){
				if (data.s == 1) {
					var data_target = $('#login_name').parent();

					$('#login_name').addClass('blue');
					$('#registerModal').modal('hide');
					$('#login_pannel').html('<a>用户'+ e.target.username.value +'</a>');
					data_target.removeAttr('data-target');
					return true;
				}
				
				if (data.s <= 0) {
					BC.lib.tips(data.err_str, {'top' : '100px'});				
				}
			});
			
		});
		
	},
	
	isLogin : function(ret)
	{
		var username = $.cookie('__c__'),
			r = false;
			
		ret = ret || {};
		try {
			n = decodeURIComponent(n);
		} catch (e) {
			n = '';
		}
			
		if (username) {
			r = true;
			ret.username = username
			//$('#login_name').parent().removeAttr('data-target');
			$('#info_username').html(username);
			$('#login_name').addClass('blue');
			$('#user_name').html('');
		}
			
		return r;
	},
	
	'_isLogin': function(flag = 1) {
		var ret = {};
		var r = BC.model.user.isLogin(ret);
		if (!r && flag) {
			BC.model.user.showLogin();
			return false;
		}

		return true;
	},
	
	showLogin : function()
	{
		$('#myModal').modal('show');			
	},
	
	showMenu : function()
	{
		if (BC.model.user.isLogin()) {
			$('#login_a').hover(function(){
				$("#user_info").show();
			},function(){
				$("#user_info").hide();
			});
		}
	}
	
}

/**
 * 公共执行函数
 */
BC.pub.components = {
	
	/**
	 * 回顶部
	 */
	'backtop' : function()
	{
		$(window).scroll(function() {
			var scroTop = $(window).scrollTop();
			var bodyHeight = $(window).height();
			if (scroTop >= bodyHeight) {
				$(".backtop").css('display', 'block');
			} else {
				$(".backtop").css('display', 'none');
			}
		});
	},
	
	/**
	 * 搜索
	 */
	'search' : function()
	{	
		var domSearchForm = document.search_form,
			domSearchInput = domSearchForm.keywords,
			smartSearchList = $('#s-s-list'),
			typemap = {'1' : '电影', '2' : '电视剧', '3' : '动漫', '4' : '综艺'},
			cache = {}, lastKeywords = '';
			
		$(domSearchForm).on('submit', function(e){
			e.preventDefault();
			var val = BC.lib.trim(domSearchInput.value);
			if (!BC.lib.isEmpty(val)) {
				location.href = BC.lib.url.get('search', '', {'q' : val});
			}
		});
		
		//refer search list
		var cacheDomOver = null;
		var generateContent = function(data)
		{
			
			var ul = $('<ul></ul>'), li;
			
			cacheDomOver = null;
			lastKeywords && (cache[lastKeywords] = data);
			
			smartSearchList.html('');
			smartSearchList.show();
			
			$(data.d.data).each(function(i,it){	
				var link = BC.lib.url.getItemLink(it.id),
					name = 	decodeURIComponent(it.name),
					actors = decodeURIComponent(it.actors),
					directors = decodeURIComponent(it.directors),
					type = decodeURIComponent(it.vedioType),	
					pic = decodeURIComponent(it.vedioPic),
					html = '<a href="' + link + '" class="s-m-name ">' + name + '</a>';
					
				it.point = (it.point + '').split('.');
				it.point = (it.point[0] ? it.point[0] : 0) + '.' + it.point[1];
				
				html += '<div class="s-l-info"><a href="' + link + '" class="s-l-image"><img src="' + pic + '"/></a>';
				html += '<dl class="s-desc"><dd class="s-d-name">' + name + '</dd>';
				html += "<dd>主演：<span>" + actors + "</span></dd>";
				html += "<dd>类型：" + "<span>" + type + "</span>";
				html += "<dd>导演：" + "<span>" + directors + "</span>";
				html += "<dd>分类：" + "<span>" + typemap[it.topType] + "</span>";
				html += "<dd>评分：<strong class='pi'>" + it.point + "</strong></dd></dl></div>";
				
				li = $('<li></li>');
				var className = 'clearfix' + (i == 0 ? ' cur' : '');
				li.addClass(className);	
				li.html(html)
				
				li.q = name;
				li.i = i;
				!cacheDomOver && (cacheDomOver = li);
				
				li.on('mouseover', function(e){
					var target = e.target,
						a, div;
					while (target.tagName != 'LI') {
						target = target.parentNode;
					}
					
					if (cacheDomOver) {
						$(cacheDomOver).removeClass('cur');
					}
					
					cacheDomOver = target;
					$(cacheDomOver).addClass('cur');
					$(domSearchInput).val(target.q);
				});
				
				$(li.find('div').get(0)).on('click', function(e){
					location.href = link;
				});
				
				ul.append(li);
				
			});
			
			smartSearchList.append(ul);
			
		};
			
		$(domSearchInput).on('keyup', function(e){
			e.preventDefault();
			var val = BC.lib.trim(domSearchInput.value),
				keycode = e.keyCode;
			
			if (keycode == 13) {
				return $(domSearchForm).trigger('submit')
			}
			
			if (BC.lib.isEmpty(val)) {
				smartSearchList.hide();
				return ;
			}
			
			if (keycode == 38 || keycode == 40) {
				if (cacheDomOver) {
					var index = cacheDomOver.i,
						childs = cacheDomOver.parentNode.childNodes;
					keycode == 38 ? index -- : index ++;
					
					if (index < 0) {
						index = childs.length - 1;
					} 
					
					if (index >= childs.length) {
						index = 0;
					}
					
					$(childs[index]).trigger('mouseover');
				}
				
				return ;	
			}
			
			if (!BC.lib.isEmpty(lastKeywords) && lastKeywords == val) {
				return ;
			}
			lastKeywords = val;
			
			if (cache[val]) {
				return generateContent(cache[val]);
			}
			
			$.ajax({
				type : 'GET',
				url : BC.lib.url.get('search', 'ajax', {q : val, random : 1, cache : 0, t : new Date().valueOf()}),
				success : function(data){
					if (typeof(data) == 'undefined') {
						return ;
					}
					data = JSON.parse(data);
					generateContent(data);
				}
			});
			
		});
		
	}
};

BC.action = {
		
	//首页/列表页
	'index' : function()
	{
		BC.pub();
		
		//lazyload
		 $(".container").find("img").lazyload({
			effect:"fadeIn"
		});  
		 
		$(".nav-tabs li a").click(function(c) {
            var b = $(c.target).attr("aria-controls");
            $("#" + b).find("img").each(function(e, f) {
                var d = $(f).attr("data-original");
                $(f).attr("src", d)
            })
        }); 
		
		BC.model.user.login();
		
		BC.action.playhistory();
		
		var nextPage = function(targetPage)
		{
			var href = location.href,
				isStatic = BC.lib.isStaticModel() && href.indexOf('.' + g_aCfg.static_file_extendtion) > 0,
				target = '';
				
			if (BC.lib.isInt(targetPage, true)) {
				targetPage = Math.max(targetPage, 1);
				if (isStatic) {
					target = href.replace(/\_\d+\./gi, '_' + targetPage + '.')	
				} else {
					var re = /\/p\/\d+/gi;
					if (re.test(href)) {
						target = href.replace(/\/p\/\d+/gi, '\/p\/' + targetPage);
					} else {
						target = href + '/p/' + targetPage;
					}
				}
				location.href = target;		
			}	
		};
		
		
		$('#btn_next_page').on('click', function(e){
			e.preventDefault();
			nextPage(parseInt(e.target.getAttribute('p')) + 1);
		});
		
		$('body').keyup(function(e){
			var k = e.keyCode,
				p = parseInt($('#btn_next_page').attr('p'));
			if (k == 39 || k == 37) {
				p = k == 39 ? (p + 1) : (p -1);
				nextPage(p);
			}
		});
		
		$('#refresh').on('click', function(e){
			e.preventDefault();
			window.location.reload();
		});
		
		$('#s_submit').on('click', function(){
			var keywords = $('#inputSuccess4').val();
			if (keywords) {
				$('#search_form').submit();
			}
		});
		
	},
	
	//内容页
	'item' : function(id, topType, vname, isBlank=1)
	{
		BC.pub();
		BC.model.user.login();
		//评论列表
		BC.model.comment.index(id);
		
		$('#tab_comment_hot').on('click', function(){
			BC.model.comment.index_hot(id);
		});
		
		//影片详情
		$('#btn_desc').on('click', function(){
			var text = $(this).text();
			var bak = $($(this).siblings().get(0));
			var bak_html = bak.html();
			
			if (text == '展开') {
				$(this).text('隐藏');
				$($(this).siblings().get(0)).hide();
				$($(this).siblings().get(1)).show();
				
			} else {
				$(this).text('展开');
				$($(this).siblings().get(0)).show();
				$($(this).siblings().get(1)).hide();
			}
			
		});
		
		//评分
		$('#point_stars').find('a').each(function(i, obj){
			$(obj).on('click', function(e){
				var p = 2 * parseInt($(e.target).html());
				var COOKIE_NAME = 'isPointed_' + id; 
				
				if ($.cookie(COOKIE_NAME) == id) {
					BC.lib.tips('对不起，您已经评过分了！');
					return false;
				} else {
					$.cookie(COOKIE_NAME, id , { path: '/', expires: 30*86400 }); 
				}
				
				BC.model.BC.pointer(id, p, function(data){
					if (data.s == 1) {
						BC.lib.tips("评分成功，您已评" + p + "分了");	
						var point_num = $('#point_num');
						point_num.html(parseInt(point_num.html())+1); 	
						
						if (BC.lib.isNumber(data.d.newpoint, !0)) {
							var a = (data.d.newpoint + "").split(".");
							$("#point_cont").html(BC.lib.isEmpty(a[0]) ? "0" : a[0] + "<em>." + (a[1] ? a[1] : "0") + "</em>");
						}
					} else {
						BC.lib.tips();
					}
				});
			});
		});
		
		//评论数
		$.ajax({
			url : BC.lib.url.get('item', 'getCommentNum', {'id' : id}), 
			success : function(data) {
				var data = JSON.parse(data);
				$('#comm-num').html(data.d.num);
			}
		});
		
		
		//内容页图片懒加载
		$(".movie-info").find("img").lazyload({ 
			effect: "fadeIn"
		});  
		
		
		BC.action.list(topType, vname, id, isBlank);
	},
	
	'playItem' : function(id, topType, vname)
	{
		BC.pub();
		BC.model.user.login();
		
		$('#tab_comment_hot').on('click', function(){
			BC.model.comment.index_hot(id);
		});
	},
	
	'addHistory' : function(vname,vid) {
		BC.action.playhistory.set(vname, vid);
		return true;
	},
	
	'list' : function(topType, vname, vid, isBlank)
	{		
		
		//播放列表
		var pages, generate, pagesize = 30, playdata = [], _this = arguments.callee;
		if (topType == 4) {
			//pagesize = 20;
		}
		
		(function() {
			var a = 0;
			$.each(g_PlayUrl.split("#"),function(i, b) {
				var d = b.split("[$]"), e = {};
				var f = d[1].split("[&]");
				e = {u: d[0], s: f[0]};
				a++;
				playdata.push(e);
			});
		})();
		
		pages = Math.ceil(playdata.length / pagesize);
		if (topType == 1) {
			$('#p-t-menus').hide();
		}
		
		var generate = {
			
			'nowPage' : 1,
			
			'pageNum' : 5,
			
			'createItem' : function(index) 
			{
				var a = $('<a></a>');
				
				a.html(playdata[index].s);
				a.attr('title', playdata[index].s);
				if (!BC.lib.isNnormalModel()) {
					a.attr('href', playdata[index].u);		
				}		
			
				a.attr('hideFocus', 'true');
				a.attr('index', index);
				
				/**
				 * 点击播放影片，添加选中状态
				 */
				a.on('click', function(event){
					event.preventDefault();
					var target = $(event.target);
					
					target.parent().children('.active').removeClass('active');
					target.addClass('active');
					_this.play(topType, vid, index, isBlank);
				});
				
				return a;
			},
			
			'createTabItem' : function(page)
			{
				generate.nowPage = page;
				var menus = $('#p-t-menus');
				var als = menus.children();
				als.hide();
				
				var start = 1,step = 1;
				var p_links = $('#p-links');
				p_links.html('');
				for (var p = 1, start, end; p <= pages; p++) {
					start = (p - 1) * pagesize + 1;
					end = Math.min(p * pagesize, playdata.length);	
				
					var div = $('<div class="off"></div>');
					div.attr('id', 'p-links-p-' + p); 
					for (var i = start, j = end; i <= j; i = i + step) {
						div.append(generate.createItem(i - 1));
					}
					p_links.append(div);
				}
				
				$('#p-links-p-' + page).show();
				
				/**
				 * 首页
				 */
				var objS = $('<a>');
				objS.attr('class', 'prev glyphicon glyphicon-fast-backward');
				objS.attr('p', 1);
				objS.attr('href', '#');
				objS.on('click', function(){
					generate.createTabItem(1);
				});
				menus.append(objS);
	
				var startPage,endPage;
				if (generate.nowPage < 3) {
					startPage = 1;
					endPage = pages-1 > generate.pageNum ? generate.pageNum : pages;
				} else {
					startPage = generate.nowPage >= pages ? pages - 3 : generate.nowPage - 2;
					var t = startPage + generate.pageNum;
					endPage = t > pages ? pages : t - 1;
				}
    			
    			/**
    			 * 所有页数
    			 */
				for (var i=startPage; i<=endPage; i++){
					var a = $('<a>');
					a.attr('p', i);
					a.attr('href', '#');
					a.html(i);
					if (i==generate.nowPage){
						a.attr('class', 'on');
					}
					
					a.on('click', function(e){
						generate.createTabItem(e.target.innerHTML);
					})
					
					menus.append(a);
				}
				
				/**
				 * 尾页
				 */
				var objE = $('<a>');
				objE.attr('class', 'prev glyphicon glyphicon-fast-forward');
				menus.append(objE);
				
				objE.on('click', function(){
					generate.createTabItem(pages);
				});
			}
		};
		
		generate.createTabItem(1);
			
		_this.play = function(id, vid, num, isBlank)
		{	
			//var _u = BC.lib.url.get('item', 'play', {'id' : id, 'vid' : vid, 'n' : num});	
			BC.action.addHistory(vname, vid);
			var _u = '/vod-play-id-'+id+'-vid-'+vid+'-num-'+num+'.html';
			isBlank ? window.open(_u) : (self.location.href=_u);
		};
		
		return generate;
	},
	
	'playhistory' : function()
	{
		var self = arguments.callee;
		
		//local			  
		var local = self.local = {

			get : function(localPlaylist)
			{
				var localPlaylist = localPlaylist || localStorage.getItem('playlist');
				localPlaylist = JSON.parse(localPlaylist);
				
				var dl_list = $('#dl_list');
				
				if (!dl_list.length) {
					dl_list = $('<dl>');
					dl_list.attr('class', 'f');
					dl_list.attr('id', 'dl_list');
				}
				
				$('.history-list').html('');
				dl_list.html('');
				
				if ( localPlaylist && localPlaylist.length > 0) {
					var len = localPlaylist.length-1;
					for(var j=len; j>=0; j--) {	

						var dd = $('<dd>'),
							_a = $('<a>'),
							_span = $('<span>'),
							_i = $('<i class="glyphicon glyphicon-remove"></i>');
						
						_a.attr('href', BC.lib.url.getItemLink(localPlaylist[j].i));
						_a.attr('target', '_blank');
						_a.html(decodeURIComponent(localPlaylist[j].n));
						_span.append(_i);
						_span.attr('mid', localPlaylist[j].i);
						
						dd.append(_a);
						dd.append(_span);
						
						/**
						 * 删除单个dom对象
						 * @param {Object} arg
						 */
						(function(arg){
							_span.on('click', function(){
								local.clear($(this).attr('mid'));
								var l = dl_list.children('dd').length-1;
								dl_list.children('dd').get(l-arg).remove();
							});
						})(j);
						
						
						dl_list.append(dd);
					}
				} else {
					dl_list.html('<p>暂无播放记录</p>');
				}
				
				$('.history-list').append(dl_list);
			},
			
			set : function(action, data)
			{
				action = action || 'insert';
				var storage = window.localStorage;
				//添加
				if (action == 'insert') {
					
					var playlist = [];
					
					var oldPlaylist = storage.getItem('playlist');
					
					if (oldPlaylist) {
						oldPlaylist = JSON.parse(oldPlaylist);
						
						for(var j=0; j<oldPlaylist.length; j++){
							if (data['i'] == oldPlaylist[j].i) {
								oldPlaylist.splice(j, 1);
							}
						}
						
						oldPlaylist.push(data);
						playlist = oldPlaylist;
						
						if (playlist.length > 10) {
							playlist.splice(0,1);
						}
					} else {
						playlist.push(data);	
					}
					
					storage.setItem('playlist', JSON.stringify(playlist));
				} else {
					//删除
					local.clear();
				}
				
				return this;
			},
			
			clear : function(mid)
			{
				var mid = mid || 0;
				
				var storage = window.localStorage;
				var playlist = storage.getItem('playlist');
				playlist = JSON.parse(playlist);
				
				if (mid == 0) {
					return false;
				}
				
				for(var j=0; j<playlist.length; j++) {
					
					if (mid == playlist[j].i) {
						playlist.splice(j, 1);
					}
				}
				
				storage.setItem('playlist', JSON.stringify(playlist));
			},
			
			more : function()
			{
				var dl_more = $('<dl>'),
					dd_more = $('<dd>'),
				 	a_more = $('<a>');
				 	
				dd_more.attr('class', 'more');
				a_more.html('清空记录');
				dd_more.append(a_more);
				dl_more.append(dd_more);	
				
				dd_more.on('click', function(){
					window.localStorage.setItem('playlist', '[]');
					$('#dl_list').html('<p>暂无播放记录</p>');
				});

				$('.history-list').append(dl_more);
			}
		};
		
		//net
		var net = self.net = {

			get : function(callback)
			{
				var that = this;
				$.ajax({
					type : 'GET',
					url : BC.lib.url.get('member', 'history', {'ac' : 'get'}),
					success : function(ret){
						if (ret) {
							ret = JSON.parse(ret);
							return callback(ret);
						}
					}
				});
			},
			
			set : function(action, mid)
			{
				action = action || 'insert';
				$.ajax({
					type : 'GET',
					url : BC.lib.url.get('member', 'history', {'ac' : 'add', 'mid' : mid}),
					success : function(ret){}
				});
				
			},
			
			getLocal : function(remotePlaylist)
			{
				var remotePlaylist = JSON.parse(remotePlaylist);
				
				var dl_list = $('#dl_list');
				
				if (!dl_list.length) {
					dl_list = $('<dl>');
					dl_list.attr('class', 'f');
					dl_list.attr('id', 'dl_list');
				}
				
				$('.history-list').html('');
				dl_list.html('');
				
				if ( remotePlaylist && remotePlaylist.length > 0 ) {
					var len = remotePlaylist.length;
					for(var j=0; j<len; j++) {	

						var dd = $('<dd>'),
							_a = $('<a>'),
							_span = $('<span>'),
							_i = $('<i class="glyphicon glyphicon-remove"></i>');
						
						_a.attr('href', BC.lib.url.getItemLink(remotePlaylist[j].i));
						_a.attr('target', '_blank');
						_a.html(decodeURIComponent(remotePlaylist[j].n));
						_span.append(_i);
						_span.attr('mid', remotePlaylist[j].i);
						
						dd.append(_a);
						dd.append(_span);
						
						/**
						 * 删除单个dom对象
						 * @param {Object} arg
						 */
						(function(arg){
							_span.on('click', function(e){
								$(e.target).parent().parent().remove();
								net.clear($(this).attr('mid'));
							});
						})(j);
						
						
						dl_list.append(dd);
					}
				} else {
					dl_list.html('<p>暂无播放记录</p>');
				}
				
				$('.history-list').append(dl_list);
			},
			
			clear : function(mid)
			{
				var mid = mid || 0;
				$.ajax({
					type : 'GET',
					url : BC.lib.url.get('member', 'history', {'ac' : 'dele', 'mid' : mid}),
					success : function(ret){}
				});
				
			},
			
			more : function()
			{			
				var dl_more = $('<dl>'),
					dd_more = $('<dd>'),
				 	a_more = $('<a>');

				dd_more.attr('class', 'more');
				a_more.attr('href', BC.lib.url.get('member', 'index'));
				a_more.html('查看更多');
				dd_more.append(a_more);
				dl_more.append(dd_more);
				

				$('.history-list').append(dl_more);
			}
		};
		
		var uievent = self.uievent = 
		{
			/**
			 * 获取远程用户个人中心播放记录
			 * @param {Object} date
			 * @param {Object} data
			 */
			createlist : function(date, data)
			{
				var playList = $('<div class="row playlist">'); 
				for(var i=0; i<data.length; i++) {
					
					var n = decodeURIComponent(data[i]['n']);
					var _url =  BC.lib.url.getItemLink(data[i]['i']);
					var md = $('<div class="col-xs-2">');
					var _a = $('<a class="small-pic"  target="_blank" title="'+ n +'">');
					var _img = $('<img>');
					var _p = $('<p class="small-pic-text">');
					var __a = $('<a>'+ n +'</a>');
					
					_a.attr('href', _url);
					__a.attr('href', _url);
					_a.attr('target', '_blank');
					__a.attr('target', '_blank');
					
					_img.attr('src', 'http://bcplayer.image.alimmdn.com' + data[i]['pic']);
					_img.attr('alt', n);
					md.append(_a);
					_a.append(_img);
					_p.append(__a);
					md.append(_p);
					
					playList.append(md);	

					(function(arg){
						
						var _i = $('<i>');
						_i.attr('class', 'glyphicon glyphicon-remove');
	
						md.hover(function(){
							$(this).append(_i);	
							_i.on('click', function(){ //动态绑定
								self.net.clear(data[arg]['i']);
								_i.parent().remove();
							})
						}, function(){
							_i.remove();
						});
						
					})(i);
				}
				
				var h1 = $('<h4>'+ BC.lib.parseDate(date) +'</h4>');
				
				$('#playlist').append(h1);
				$('#playlist').append(playList);
			},
			
			init : function()
			{
				//如果用户是登录状态，则获取用户服务器上的观看记录
				if (BC.model.user.isLogin()) {
					self.net.get(function(data){
	
						var values = [];
						var local = [];
					    
					    /**
					     * 获取远程本地最近10条播放记录
					     */
					    for (var property in data.d) {					    	
					      values.push(data.d[property]);
					    }
					   
					    for(var i=values.length-1; i>0; i--){
					    	for(var j=0; j<values[i].length; j++){
					    		if (local.length > 9){
					    			continue;
					    		}
					    		local.push(values[i][j]);
					    	}
					    }
						
						/**
						 * JSON对象无法逆序循环，这里通过temp for循环逆序输出
						 */
						var temp = [];
						
					  	for(var obj in data.d) {
					  		temp.push(obj);
					  	}
					  
				    	
				    	if (!temp) {
				    		$('#playlist').html('<h4>暂无播放记录</h4>');
				    		return;
				    	}
				    	
				    	$('#playlist').html('');
						
				    	for(var i=temp.length-1; i>=0; i--) {
				    		self.uievent.createlist(temp[i], data.d[temp[i]]);
				    	}
				    	
						self.net.getLocal(JSON.stringify(local));
						self.net.more();					
						
					});
					
				} else {
					//本地记录
					self.local.get();
					self.local.more();					
				}				
			}
		};
		
		self.set = function(vname, vid)
		{
			if (BC.model.user.isLogin()) {
				self.net.set('insert', vid);
			} else {
				var pic  = $('.info-img').attr('src');
				self.local.set('insert', {'n' : encodeURIComponent(vname),'i' : vid, 't' : new Date().valueOf(), 'pic' : pic});
			}
		};
		
		self.show = function(isRemote)
		{
			var isRemote = isRemote || 0;
			$('#history').hover(function(){
				if (isRemote) {
					uievent.init();					
				}
			   $('#history_list').show();
			},function(){
			    $('#history_list').hide();
			});
			
		}
		
		if (BC.model.user.isLogin() ){
			uievent.init();	
			$('#history').hover(function(){
			   $('#history_list').show();
			},function(){
			    $('#history_list').hide();
			});
		} else{
			$('#history').hover(function(){
				uievent.init();
			   $('#history_list').show();
			},function(){
			    $('#history_list').hide();
			});
		}
		
	},
	
	'player' : function(vid, num)
	{	
		$.ajax({
			type : 'POST',
			url : BC.lib.url.get('item', 'ajax4'),
			data : {'vid' : vid, 'n' : num},
			success : function(ret){
				data = JSON.parse(ret);
				if (data.s == 1) {
					$('#a1').html('<iframe width="100%" height="100%" allowTransparency="true" frameborder="0" scrolling="no" src="' + data.d.src + '"></iframe>');
				}
			}
		});	
	}
	
};

