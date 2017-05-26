
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
	}
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
	
	'testFunc' : function()
	{
		layer.msg('功能正在测试中，敬请期待');
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
			sReturn = $B.extend.date.format('Y-m-d', iTime);
		}
		return sReturn ;
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
				str = $F.replaceEx(str, this.encode_map[repl], repl);
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
			return '/?' + this.param_name + '=' + controllor + this.delimiter + 
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
		}
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
		}
		
	},
	'user' : {
		/**
		 * 请求
		 */
		'request' : {
			
			
		},
		
		/**
		 * ui操作
		 */
		'ue' : {
			
			/**
			 *  登陆弹框
			 *  @param {Object} callback
			 */
			login : function(callback)
			{
			},
			
			/**
			 * 注册弹框
			 * @param {Object} callback
			 */
			reg : function(callback)
			{
			},

			/**
			 * 退出登录后
			 */
			logout : function()
			{
				
			},
			
			/**
			 * 登陆成功后
			 */
			loginOK : function()
			{
				
			}
		},
		
		/**
		 * 是否登陆
		 */
		'isLogin' : function(ret)
		{
			
			
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
			_p_date.html(new Date(parseInt(head.time) * 1000).toLocaleString().replace(/\//g, '-').substr(0,9));
			
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
			
			/*评论回复*/
			dom_rep.on('click', function(e){
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
							BC.model.comment.tips('歇息一会再来评论吧！');
							return false;
						} else {
							$.cookie(COOKIE_NAME, commid , { path: '/', expires: 300 }); 
						}
						
						var uid = _that.attr('uid');
						var area = _that.find('textarea')[0];
						area = $(area); 
						if (area.val() == '') {
							BC.model.comment.tips('总该说些什么吧！');
							return false;
						}
						
						if (area.val().length > 2000) {
							BC.model.comment.tips('回复内容不能超过2000个汉字！');
							return false;
						}
						
						BC.model.comment.add(uid, area.val(), commid, function(data){
							if(data.s == 1) {
								BC.model.comment.reload();
							} else {
								BC.model.comment.tips();
							}
						});
					});
				}
			});
			
			/*评论踩*/
			dom_down.on('click', function(e){
				e.preventDefault();
				var that = $(e.target);
				if (parseInt(that.html()) >= 0) {
					that = that.parent();
				}
				that = that.parent();
				var commid = that.attr('commid');
				var COOKIE_NAME = 'commid_' + commid; 
				if ($.cookie(COOKIE_NAME) == commid) {
					BC.model.comment.tips('您已经踩过了！');
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
			
			/*评论赞*/
			dom_up.on('click', function(e){
				e.preventDefault();
				var that = $(e.target);
				if (parseInt(that.html()) >= 0) {
					that = that.parent();
				}
				that = that.parent();
				var commid = that.attr('commid');
				var COOKIE_NAME = 'commid_up_' + commid; 
				if ($.cookie(COOKIE_NAME) == commid) {
					BC.model.comment.tips('您已经点过赞了！');
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
						BC.model.comment.tips();
					}
				});
			});
					
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
		 * 发表评论或回复
		 * @params uid 用户id
		 * @params msg 评论内容
		 * @params commid 回复评论id
		 * @callback 回调
		 */
		'add' : function(uid, msg, commid, callback)
		{
			$.ajax({
				type: 'POST',
				url : BC.lib.url.get('item', 'add'),
				data: {'uid' : uid, 'msg' : msg, 'commid' : commid, 'mid' : BC.model.comment.mid},
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
		'show' : function(url, callback)
		{
			callback = callback || function(){};

			var _warp = $('#_warp');
			BC.model.comment.get(url, function(data){
				if (data.s == 0) {
					var more = $('<p></p>');
					more.attr('id', 'nomore');
					more.attr('class', 'w-more');
					more.html('暂无更多评论...');
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
		
		'tips' : function(text)
		{
			var text = text || '系统繁忙，请稍后再试！';
			var tip = $('#tips');
			tip.html(text);
			tip.fadeIn(1500).fadeOut(1500);
		},
		
		/**
		 * 评论列表入口
		 */
		'index' : function(bcid)
		{
			var mid = mid || 74795;
			var com_body = $('#com-body');
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
				
				/**
				 * 分页加载更多
				 */
				more.on('click', function(){
					var commentItemList = $('.w-item-con-self');
					var commid = $(commentItemList[commentItemList.length-1]).attr('commid'); //取最后一条记录的id，用来分
					BC.model.comment.show(BC.lib.url.get('item', 'list', {'mid' : mid, 'maxid' : commid}), function(__more, __warp){
						if (__more == 0) {
							more.remove();
						}
						_warp.append(__warp);
					});
				});				
			});

		}
	}
};

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
	}
};

BC.action = {
	
	//首页/列表页
	'index' : function()
	{
		
	},
	
	//内容页
	'item' : function(id)
	{
		//BC.pub();
		//评论列表
		BC.model.comment.index(id);
	}
};

