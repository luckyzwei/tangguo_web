
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
 * ����
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
 * ���ú���
 */
BC.lib = {
	/**
	 * �Ƿ�̬ģʽ
	 */
	'isStaticModel' : function()
	{
		return g_aCfg.site_mode == 1;
	},
	
	'testFunc' : function()
	{
		layer.msg('�������ڲ����У������ڴ�');
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
	 * ����ʱ�䣨�������ۣ�
	 */
	'parseTime' : function(iTime)
	{
		var oDate = new Date(),
			iCurrent = parseInt(oDate.valueOf() / 1000),
			iSeconds = iCurrent - iTime,
			sReturn = '';
		
		if(iSeconds <60) {
			sReturn = '�ո�';
		} else if (iSeconds<3600) {
			sReturn = Math.ceil(iSeconds / 60) + '����ǰ';
		} else if (iSeconds < 3600 * 24) {
			sReturn = Math.ceil(iSeconds / 3600)+'Сʱǰ';
		} else if(iSeconds < 3600 * 24 * 7) {
			sReturn = Math.ceil(iSeconds / (3600 * 24)) + '��ǰ';
		} else {
			sReturn = $B.extend.date.format('Y-m-d', iTime);
		}
		return sReturn ;
	},
	
	/**
	 * �ж��ַ����Ƿ�Ϊ��
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
		 * ��ȡurl
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
		 * ƴ�Ӳ���
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
	 * ��ز���
	 */
	'BC' : {
		/**
		 * ��������
		 * param object
		 * callback ������ϻص�����
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
		 * ����
		 */
		'request' : {
			
			
		},
		
		/**
		 * ui����
		 */
		'ue' : {
			
			/**
			 *  ��½����
			 *  @param {Object} callback
			 */
			login : function(callback)
			{
			},
			
			/**
			 * ע�ᵯ��
			 * @param {Object} callback
			 */
			reg : function(callback)
			{
			},

			/**
			 * �˳���¼��
			 */
			logout : function()
			{
				
			},
			
			/**
			 * ��½�ɹ���
			 */
			loginOK : function()
			{
				
			}
		},
		
		/**
		 * �Ƿ��½
		 */
		'isLogin' : function(ret)
		{
			
			
		}
	},
	
	/**
	 * ����
	 */
	'comment' : {
			
		'mid' : 0,
		
		/**
		 * ���������б�
		 */
		'createCommentList' : function()
		{
			var warp =  $('<div></div>');
			warp.attr('class', 'w-comment-list');
			
			return warp;
		},
		
		/**
		 * �������¥��������Ϣ
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
		 * ������¥����
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
			
			
			var dom_rep = $('<a href="#" class="comment-reply">�ظ�</a>');
			var dom_down = $('<a href="#" class="comment-down">��(<span>'+ ret.down+'</span>)</a>');
			var dom_up = $('<a href="#" class="comment-up">��(<span>'+ ret.up +'</span>)</a>')
			
			/*���ۻظ�*/
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
					var dom_btm_sub = $('<span>�ύ</span>');
					var dom_r = $(reply_textarea[0]);
					dom_r.append(dom_text_area);
					dom_r.append(dom_btm_sub);
				
					dom_btm_sub.on('click', function(_e){
						var _that = $(_e.target).parent();
						var commid =  _that.attr('commid');
						var COOKIE_NAME = 'comment_reply_mid' + commid;
						
						if ($.cookie(COOKIE_NAME) == commid) {
							BC.model.comment.tips('ЪϢһ���������۰ɣ�');
							return false;
						} else {
							$.cookie(COOKIE_NAME, commid , { path: '/', expires: 300 }); 
						}
						
						var uid = _that.attr('uid');
						var area = _that.find('textarea')[0];
						area = $(area); 
						if (area.val() == '') {
							BC.model.comment.tips('�ܸ�˵Щʲô�ɣ�');
							return false;
						}
						
						if (area.val().length > 2000) {
							BC.model.comment.tips('�ظ����ݲ��ܳ���2000�����֣�');
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
			
			/*���۲�*/
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
					BC.model.comment.tips('���Ѿ��ȹ��ˣ�');
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
			
			/*������*/
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
					BC.model.comment.tips('���Ѿ�������ˣ�');
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
		 * �����ҳ����
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
		 * ���۲�
		 * @params commid ����id
		 * @params isDown �Ƕ����ǲ� Ĭ�ϲ�
		 * @callback �ص���ʾ����״̬
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
		 * �������ۻ�ظ�
		 * @params uid �û�id
		 * @params msg ��������
		 * @params commid �ظ�����id
		 * @callback �ص�
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
		 * �б���ʾ
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
					more.html('���޸�������...');
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
		 * ������Ⱦ�����б�
		 */
		'reload' : function()
		{
			$("#_warp").html('');
			$("#more_comment").remove();
			BC.model.comment.index(BC.model.comment.mid);
		},
	
		/**
		 * ����..
		 */
		'createMore' : function(id)
		{
			var id = id || 'more_comment';
			var more = $('<p></p>');
			more.attr('class', 'w-more');
			more.attr('id', id);
			more.html('�鿴����');
			
			return more;
		},
		
		'tips' : function(text)
		{
			var text = text || 'ϵͳ��æ�����Ժ����ԣ�';
			var tip = $('#tips');
			tip.html(text);
			tip.fadeIn(1500).fadeOut(1500);
		},
		
		/**
		 * �����б����
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
				 * ��ҳ���ظ���
				 */
				more.on('click', function(){
					var commentItemList = $('.w-item-con-self');
					var commid = $(commentItemList[commentItemList.length-1]).attr('commid'); //ȡ���һ����¼��id��������
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
 * ����ִ�к���
 */
BC.pub.components = {
	
	/**
	 * �ض���
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
	
	//��ҳ/�б�ҳ
	'index' : function()
	{
		
	},
	
	//����ҳ
	'item' : function(id)
	{
		//BC.pub();
		//�����б�
		BC.model.comment.index(id);
	}
};

