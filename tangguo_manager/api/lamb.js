var lamb = lamb || {};
lamb.extend = lamb.extend || {};
lamb.$ = function(id){return document.getElementById(id)};
/**
 *	@class lamb.utils工具类
 **/
(function(){
	lamb.utils = {};
	var bridge = new (function(){});
	var utils = lamb.utils = {
		/**
		 * 判断val是否在数组arr中
		 * @param {array} arr
		 * @param {mix} val
		 * @param {boolean} bStrict = false
		 * @return {boolean}
		 */
		inArray : function(arr, val, bStrict/*=false*/)
		{
			bStrict = !!bStrict;
			if (bStrict) {
				for (var i = 0, j = arr.length; i < j; i++) {
					if (arr[i] === val){ return true; }
				}
			}
			else {
				for (var i = 0, j = arr.length; i < j; i++) {
					if (arr[i] == val){ return true;}
				}
			}
			return false;
		},
		
		/**
		 * 移除数组arr中索引index的项
		 * @param {array} arr
		 * @param {int} index
		 */
		removeItem : function(arr, index)
		{
			arr.splice(Math.max(0, Math.min(arr.length-1, index)), 1);
		},
		
		/**
		 * 获取字符串str字节长度
		 * @param {string} str
		 * @return {int}
		 */
		getByteLen : function(str)
		{
			return (str + '').replace(/[^\x00-\xff]/gi,'11').length;
		},
		
		isEmpty : function(str)
		{
			return (str + '').length <= 0;
		},
		
		isArray : function(array)
		{
			return array && array.constructor === Array;
		},
		
		/**
		 * 为正则表达式字符串str转义
		 * @param {string} str
		 * @param {string} delimiter
		 * @return {string}
		 */
		regexpQuote : function(str, delimiter/*=''*/)
		{
			return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
		},
		
		/**
		 * 修剪字符串str两端空格
		 * @param {string} str
		 * @return {string}
		 */
		trim : function(str)
		{
			return (str + '').replace(/(^\s*)|(\s*$)/gi, '');
		},
		
		/**
		 * 扩展replace方法，在字符串str查找search，并替换所有
		 * @param {string} str
		 * @param {string} search
		 * @param {string} replaceMent
		 * @param {boolean} bIgnore是否区分大小写默认false
		 * @return {string}
		 */
		replaceEx : function(str, search, replaceMent, bIgnore/*=false*/)
		{
			return (str + '').replace(new RegExp(utils.regexpQuote(search), bIgnore ? 'g' : 'gi'), replaceMent);
		},
		
		/**
		 * 判断是否为整数或者正负整数
		 * @param {number} num
		 * @param {boolean} bPostive是否区分正负默认为false
		 * @return {number}
		 */
		isInt : function(num, bPostive/*=false*/)
		{
			return (bPostive ? (/^\d+$/gi) : (/^-?\d+$/gi)).test(num + '');
		},
		
		/**
		 * 判断是否为数字或者正负实数
		 * @param {number} num
		 * @param {boolean} bPostive是否区分正负默认为false
		 * @return {number}
		 */
		isNumber : function (num, bPostive/*=false*/)
		{
			return (bPostive ? (/^((\d+\.\d+)|(\d+))$/gi) : (/^-?((\d+\.\d+)|(\d+))$/gi)).test(num + '');
		},
		
		/**
		 * 遍历集合coll,并逐步调用函数fn
		 * @param {Object} coll
		 * @param {function} fn
		 * @param {boolean} bOject 要遍历的是否为对象类型 默认为false
		 */
		each : function(coll, fn, bObject/*=false*/)
		{
			if (!bObject) {
				for (var i = 0, j = coll.length; i < j; i++ ) {
					if (fn(coll[i], i, coll) === false) {break;}
				}
			}
			else {
				for (var k in coll) {
					if (fn(coll[k], k, coll) === false) {break;}
				}
			}
		},
		
		/**
		 * 修改函数f的this为对象o，既为对象o绑定函数f
		 * @param {object} o
		 * @param {function} f
		 * @param .... 可选，为函数f指定参数，eg: bind(o, f, 1, 2) => f(1, 2)
		 * @return {function} 
		 */
		bind : function (o, f)
		{
			var args	=	Array.prototype.slice(arguments,2);
			return function()
			{
				f.apply(o,args.concat(Array.apply(null,arguments)));
			}			
		},
		
		/**
		 * 生成事件回调函数，并绑定对象o为函数执行者，传递event对参数
		 */
		bindAsEventListener : function (o, f)
		{
			var args	=	Array.prototype.slice(arguments,2);
			return function(event)
			{
				f.apply(o,args.concat([event]));
			}			
		},
		
		/**
		 * 创建类函数
		 * @param {object} _class
		 */
		$Class : function(_class)
		{
			var _fBridge = function(){this.initialize.apply(this, arguments)};
			_fBridge.prototype = _class;
			return _fBridge;
		},
		
		/**
		 * 将sourceClass对象的属性复制到targetClass中
		 * @param {object} targetClass
		 * @param {object} sourceClass
		 * @param {boolean} bReserveOwnProperty是否保留targetClass原有的属性 默认为false
		 * @return {object} targetClass
		 */
		extend : function(targetClass, sourceClass, bReserveOwnProperty/*=false*/)
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
		},
		
		/**
		 * 以obj为原型创建实例
		 * @param {object} obj
		 */
		makeInstance : function(obj)
		{
			bridge.prototype = obj;
			obj = new bridge;
			bridge.prototype = null;
			return obj;
		},
		
		/**
		 * subClass继承superClass
		 * @param {object} subClass
		 * @param {object} superClass
		 * @return {object}扩展后的新对象
		 */
		inherits : function(subClass, superClass)
		{
			var oldPrototype = subClass.prototype,
				newPrototype = utils.makeInstance(superClass.prototype);
			utils.extend(newPrototype, oldPrototype, true);
			subClass.prototype = newPrototype;
			return (newPrototype.constructor = subClass);
		},
		
		/**
		 * 改进setTimeout闭包中的this所有者
		 *
		 * @param function f
		 * @param int t
		 * @param object o
		 * @return timerid
		 */
		setTimeout : function(f, t, o)
		{
			return setTimeout(utils.bind(o, f), t);
		},
		
		/**
		 * 解析完整的URL，解析后的结果包含
		 * scheme - 协议 hostname - 主机名不包括端口号 host - 主机名包括端口号
		 * port - 端口号 pathname - 路径名 search - 查询字符串 query - 包括解析search的查询参数对象
		 *
		 * @param strign url
		 * @param boolean isParseQuery = false
		 * @return object
		 */
		parseUrl : function(url, isParseQuery/*= false*/)
		{
			var ret = {}, pos,
				host, port, search = '';
			isParseQuery = !!isParseQuery;
			if ((pos = url.indexOf('://')) > 0) {
				ret.scheme = url.substr(0, pos).toLowerCase();
				url = url.substr(pos + 3);
				
				if (url.length > 0) {
					host = (pos = url.indexOf('/')) < 0 ? url : url.substr(0, pos);
					url =  pos < 0 ? '' : url.substr(pos);
					ret.host = host;
					
					if ((pos = host.lastIndexOf(':')) > 0) {
						ret.hostname = host.substr(0, pos);
						if (lamb.utils.isInt(port = host.substr(pos + 1))) {
							ret.port = port;
						}
					} else {
						ret.hostname = host;
						ret.port = 80;
					}
					
					if (url.length > 0) {
						if ((pos = url.indexOf('?')) >= 0) {
							ret.pathname = url.substr(0, pos);
							search = url.substr(pos + 1);
						} else {
							ret.pathname = url;
						}
						
						ret.search = search;
						
						if (search.length > 0 && isParseQuery) {
							ret.query = lamb.utils.parseStr(search);
						}
					}
				}
			}
			return ret;
		},
		
		/**
		 * 解析查询字符串返回字典map 
		 *
		 * @param string query
		 * @return object
		 */
		parseStr : function(query /*=location.search*/)
		{
			query = query || location.search;
			var ret = {}, pos;
			
			if (query.substr(0, 1) == '?') {
				query = query.substr(1);	
			}
			query = query.split('&');
			for (var i = 0; i < query.length; i ++) {
				var temp = query[i];
				if ((pos = query[i].indexOf('=')) < 0) {
					continue ;
				}
				ret[temp.substr(0, pos)] = temp.substr(pos + 1);
			}
			return ret;
		}
	};
})();

/**
 * @class lamb.cookie
 */
lamb.cookie = 
{
		set:function(sName,sValue,iExpireSecond,sDomain,sPath)
		{
			sDomain=sDomain||'';
			sPath=sPath||'/';
			var sCookie='';
			sCookie=sName+'='+sValue;//escape(sValue);
			if(!isNaN(iExpireSecond))
			{
				var date=new Date();
				date.setTime(date.getTime()+iExpireSecond*1000);
				sCookie+=';expires='+date.toGMTString();
			}
			if(!lamb.utils.isEmpty(sDomain)) sCookie+=';domain='+sDomain;
			if(!lamb.utils.isEmpty(sPath)) 	 sCookie+=';path=/';
			document.cookie=sCookie;
		},
		get:function(sName)
		{
			var sCookie=document.cookie;
			var re=new RegExp('^'+sName+'\=','g');
			if(lamb.utils.isEmpty(sCookie)) return '';
			var aCookie=sCookie.split(';');
			for(var i=0;i<aCookie.length;i++)
			{
				var s = lamb.utils.trim(aCookie[i]);
				if(re.test(s)) return s.split('=')[1];
			}
			return '';
		}
};

/**
 *	@class lamb.browser
 */
(function(){
    var agent = navigator.userAgent.toLowerCase(),
        opera = window.opera,
        browser = {
        /**
         * 检测浏览器是否为IE
         * @name lamb.browser.ie
         * @property    检测浏览器是否为IE
         * @grammar     lamb.browser.ie
         * @return     {Boolean}    返回是否为ie浏览器
         */
        ie		: !!window.ActiveXObject,

        /**
         * 检测浏览器是否为Opera
         * @name lamb.browser.opera
         * @property    检测浏览器是否为Opera
         * @grammar     lamb.browser.opera
         * @return     {Boolean}    返回是否为opera浏览器
         */
        opera	: ( !!opera && opera.version ),

        /**
         * 检测浏览器是否为WebKit内核
         * @name lamb.browser.webkit
         * @property    检测浏览器是否为WebKit内核
         * @grammar     lamb.browser.webkit
         * @return     {Boolean}    返回是否为WebKit内核
         */
        webkit	: ( agent.indexOf( ' applewebkit/' ) > -1 ),

        /**
         * 检测是否为Adobe AIR
         * @name lamb.browser.air
         * @property    检测是否为Adobe AIR
         * @grammar     lamb.browser.air
         * @return     {Boolean}    返回是否为Adobe AIR
         */
        air		: ( agent.indexOf( ' adobeair/' ) > -1 ),

        /**
         * 检查是否为Macintosh系统
         * @name lamb.browser.mac
         * @property    检查是否为Macintosh系统
         * @grammar     lamb.browser.mac
         * @return     {Boolean}    返回是否为Macintosh系统
         */
        mac	: ( agent.indexOf( 'macintosh' ) > -1 ),

        /**
         * 检查浏览器是否为quirks模式
         * @name lamb.browser.quirks
         * @property    检查浏览器是否为quirks模式
         * @grammar     lamb.browser.quirks
         * @return     {Boolean}    返回是否为quirks模式
         */
        quirks : ( document.compatMode == 'BackCompat' )
    };

    /**
     * 检测浏览器是否为Gecko内核，如Firefox
     * @name lamb.browser.gecko
     * @property    检测浏览器是否为Gecko内核
     * @grammar     lamb.browser.gecko
     * @return     {Boolean}    返回是否为Gecko内核
     */
    browser.gecko = ( navigator.product == 'Gecko' && !browser.webkit && !browser.opera );

    var version = 0;

    // Internet Explorer 6.0+
    if ( browser.ie )
    {
        version = parseFloat( agent.match( /msie (\d+)/ )[1] );
		if (agent.indexOf('boie9') > 0) {
			version = 9;
		}
        /**
         * 检测浏览器是否为 IE8 浏览器
         * @name lamb.browser.IE8
         * @property    检测浏览器是否为 IE8 浏览器
         * @grammar     lamb.browser.IE8
         * @return     {Boolean}    返回是否为 IE8 浏览器
         */
        browser.ie8 = !!document.documentMode;

        /**
         * 检测浏览器是否为 IE8 模式
         * @name lamb.browser.ie8Compat
         * @property    检测浏览器是否为 IE8 模式
         * @grammar     lamb.browser.ie8Compat
         * @return     {Boolean}    返回是否为 IE8 模式
         */
        browser.ie8Compat = document.documentMode == 8;

        /**
         * 检测浏览器是否运行在 兼容IE7模式
         * @name lamb.browser.ie7Compat
         * @property    检测浏览器是否为兼容IE7模式
         * @grammar     lamb.browser.ie7Compat
         * @return     {Boolean}    返回是否为兼容IE7模式
         */
        browser.ie7Compat = ( ( version == 7 && !document.documentMode )
                || document.documentMode == 7 );

        /**
         * 检测浏览器是否IE6模式或怪异模式
         * @name lamb.browser.ie6Compat
         * @property    检测浏览器是否IE6 模式或怪异模式
         * @grammar     lamb.browser.ie6Compat
         * @return     {Boolean}    返回是否为IE6 模式或怪异模式
         */
        browser.ie6Compat = ( version < 7 || browser.quirks );

    }

    // Gecko.
    if ( browser.gecko )
    {
        var geckoRelease = agent.match( /rv:([\d\.]+)/ );
        if ( geckoRelease )
        {
            geckoRelease = geckoRelease[1].split( '.' );
            version = geckoRelease[0] * 10000 + ( geckoRelease[1] || 0 ) * 100 + ( geckoRelease[2] || 0 ) * 1;
        }
    }
    /**
     * 检测浏览器是否为chrome
     * @name lamb.browser.chrome
     * @property    检测浏览器是否为chrome
     * @grammar    	lamb.browser.chrome
     * @return     {Boolean}    返回是否为chrome浏览器
     */
    if (/chrome\/(\d+\.\d)/i.test(agent)) {
        browser.chrome = + RegExp['\x241'];
    }
    /**
     * 检测浏览器是否为safari
     * @name lamb.browser.safari
     * @property    检测浏览器是否为safari
     * @grammar     lamb.browser.safari
     * @return     {Boolean}    返回是否为safari浏览器
     */
    if(/(\d+\.\d)?(?:\.\d)?\s+safari\/?(\d+\.\d+)?/i.test(agent) && !/chrome/i.test(agent)){
    	browser.safari = + (RegExp['\x241'] || RegExp['\x242']);
    }


    // Opera 9.50+
    if ( browser.opera )
        version = parseFloat( opera.version() );

    // WebKit 522+ (Safari 3+)
    if ( browser.webkit )
        version = parseFloat( agent.match( / applewebkit\/(\d+)/ )[1] );

    /**
     * 浏览器版本
     *
     * gecko内核浏览器的版本会转换成这样(如 1.9.0.2 -> 10900).
     *
     * webkit内核浏览器版本号使用其build号 (如 522).
     * @name lamb.browser.version
     * @grammar     lamb.browser.version
     * @return     {Boolean}    返回浏览器版本号
     * @example
     * if ( lamb.browser.ie && <b>lamb.browser.version</b> <= 6 )
     *     alert( "Ouch!" );
     */
    browser.version = version;

    /**
     * 是否是兼容模式的浏览器
     * @name lamb.browser.isCompatible
     * @grammar     lamb.browser.isCompatible
     * @return     {Boolean}    返回是否是兼容模式的浏览器
     * @example
     * if ( lamb.browser.isCompatible )
     *     alert( "Your browser is pretty cool!" );
     */
    browser.isCompatible =
        !browser.mobile && (
        ( browser.ie && version >= 6 ) ||
        ( browser.gecko && version >= 10801 ) ||
        ( browser.opera && version >= 9.5 ) ||
        ( browser.air && version >= 1 ) ||
        ( browser.webkit && version >= 522 ) ||
        false );
    browser.ie_version = browser.ie ? browser.version : '';
    lamb.browser = browser;
    if(browser.ie && browser.version == 6) 
    {
    	try {
    		document.execCommand("BackgroundImageCache", false, true);
    	} catch(e) {}
    } 
})();

/**
 * @class lamb.event 
 */
(function(){
	var addEvent,removeEvent,
		addIframeEvent, removeIframeEvent, _id=1;
	var oEvent=function(event)
	{
		//return event;
		event.pageX = event.clientX + lamb.dom.getScrollLeft();
		event.pageY = event.clientY + lamb.dom.getScrollTop();
		event.target = event.srcElement;
		event.stopPropagation = oEvent.stopPropagation;
		event.preventDefault = oEvent.preventDefault;
		if (event.type == "mouseout") {
			event.relatedTarget = event.toElement
		} else if (event.type == "mouseover") {
			event.relatedTarget = event.fromElement
		}
		return event;
	};
	
	oEvent.stopPropagation	=	function(){this.returnValue=false;};
	oEvent.preventDefault	=	function(){this.cancelBubble=true};
	
	if(lamb.browser.ie)
	{
		addEvent=function(oElement,sType,fHandler)
		{
			var sOnType='on'+sType;
			if (!oElement) {
				return false;
			}
			if(!fHandler.id) fHandler.id=_id++;
			if(!oElement.events)oElement.events={};
			var oHandler=oElement.events[sType];
			if(!oHandler)
			{
				oHandler=oElement.events[sType]={};
				if(oElement[sOnType]) oHandler[0]=oElement[sOnType];
			}
			oHandler[fHandler.id]=fHandler;
			oElement[sOnType]=handleEvent;
			return this;
		};
		
		removeEvent=function(oElement,sType,fHandler)
		{
			try{
				if(oElement.events[sType]&&fHandler.id&&oElement.events[sType][fHandler.id]) {
				delete oElement.events[sType][fHandler.id];
				}
			}
			catch(e){}
			return this;
		};
		
		addIframeEvent = function(dom, eventname, handler)
		{
			dom.attachEvent('on' + eventname, handler);
			return this;
		};
		
		removeIframeEvent = function(dom, eventname, handler)
		{
			dom.detachEvent('on' + eventname, handler);
			return this;
		};
		
		fireEvent = function(dom, eventname)
		{
			dom.fireEvent('on' + eventname);
			return this;
		};
	}
	else
	{
		addEvent=function(oElement,sType,fHandler)
		{
			oElement.addEventListener(sType,fHandler,false);
			return this;
		};
		
		removeEvent=function(oElement,sType,fHandler)
		{
			oElement.removeEventListener(sType,fHandler,false);
			return this;
		};
		
		fireEvent = function(dom, eventname)
		{
          	var evt = document.createEvent('HTMLEvents');
			evt.initEvent(eventname, true, true);
			dom.dispatchEvent(evt);		
			return this;
		};
		
		addIframeEvent = addEvent;
		
		removeIframeEvent = removeEvent;
	}
	
	function handleEvent()
	{
		var returnValue=true,event=oEvent(window.event);
		var oHandler=this.events[event.type];
		for(var o in oHandler)
		{
			if(oHandler[o](event)===false) returnValue=false;
		}
		
		return returnValue;
	}

	lamb.event = {build:oEvent,addEvent:addEvent,removeEvent:removeEvent, addIframeEvent : addIframeEvent, removeIframeEvent : removeIframeEvent, fireEvent : fireEvent};	
})();

/**
 * @class lamb.dom
 */
(function(){
	lamb.dom = lamb.dom || {};
	var dom = lamb.dom = 
	{
		/**
		 * dom的根节点
		 */
		doc : function(){return document.compatMode=='CSS1Compat'?document.documentElement:document.body},
		
		/**
		 * 获取页面的长度
		 */
		getPageHeight : function() 
		{
			return Math.max(dom.scrollHeight, dom.offsetHeight);
		},
		
		/**
		 * 获取页面的宽度
		 * */
		getPageWidth : function()
		{
			return Math.max(dom.srcollWidth, dom.offsetWidth);
		},
				
		/**
		 * 获取可视区域的宽度
		 */
		getClientWidth : function()
		{
			return document.documentElement.clientWidth;
		},
		
		/**
		 * 获取可视区域的高度
		 */
		 getClientHeight : function()
		 {
			return document.documentElement.clientHeight;
		 },
		
		getScrollTop : function(domNode)
		{
			var doc = domNode ? domNode.ownerDocument : document;
			return Math.max(doc.documentElement.scrollTop, doc.body.scrollTop);
		},
		
		getScrollLeft : function(domNode)
		{
			var doc = domNode ? domNode.ownerDocument: document;
			return Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft)			
		},
		
		/**
		 * 获取元素的子节点不包括空白字符，兼容fixfox
		 * @param {dom} domNode要获取的dom元素对象
		 * @return {domArray}子节点集合
		 */
		getChildNodes : function(domNode)
		{
			var nodes	=	[],
				childs	,
				i		=	0,
				j;
			childs		=	domNode.childNodes;
			for(j=childs.length;i<j;i++){
				if(childs[i].nodeName=='#text') continue;
				nodes.push(childs[i]);
			}
			return nodes;			
		},
		
		/**
		 * 获取domNode元素的各方位距页面的尺寸距离
		 */
		pageRect : function(domNode)
		{
			var left = 0,
				top = 0,
				right = 0,
				bottom = 0;
			if (!domNode.getBoundingClientRect) {
				var n = domNode;
				while (n) {
					left += n.offsetLeft,
					top += n.offsetTop;
					n = n.offsetParent;
				}
				right = left + domNode.offsetWidth;
				bottom = top + domNode.offsetHeight;
			} else {
				var rect = domNode.getBoundingClientRect();
				left = right = dom.getScrollLeft(domNode);
				top = bottom = dom.getScrollTop(domNode);
				left += rect.left;
				right += rect.right;
				top += rect.top;
				bottom += rect.bottom;
			}
			return {
				"left": left,
				"top": top,
				"right": right,
				"bottom": bottom
			};			
		},
		
		/**
		 * 获取domNode距可视区域的各方位的尺寸距离
		 */
		clientRect : function(domNode)
		{
			var rect = dom.pageRect(domNode),
				sLeft = dom.getScrollLeft(domNode),
				sTop = dom.getScrollTop(domNode);
			rect.left -= sLeft;
			rect.right -= sLeft;
			rect.top -= sTop;
			rect.bottom -= sTop;
			return rect;			
		},
		
		/**
		 * 为domNode增加css样式
		 */
		addClass : function(domNode, css)
		{
			var c = domNode.className + '';
			if (c.indexOf(css) < 0 ) {
				domNode.className = (c ? c + ' ' : '') + css;
			}
			return this;
		},
		
		/**
		 * 为domNode移除css样式
		 */
		removeClass : function(domNode, css)
		{
			var c	=	domNode.className;
			domNode.className = c==css?'' : lamb.utils.replaceEx(c, css, '');
			return this;
		},
		
		/**
		 * 为domNode增加style样式
		 */
		style : function(domNode, oCss)
		{
			for(var k in oCss)
				domNode.style[k] = oCss[k];	
			return this;
		}
	};
	
	if (lamb.browser.ie) {
		dom.contains	=	function(s,t){
			return s!=t && s.contains(t);
		};
		dom.setFilter	=	function(node,v){
			node.style.filter	=	'alpha(opacity='+v+')';	
		};	
	}
	else {
		dom.contains	=	function(s,t){
			return !! (s.compareDocumentPosition(t) & 16);
		};
		dom.setFilter	=	function(node,v){
			node.style.opacity	=	v/100;
		};	
	}
	
})();

/**
 * @class lamb.ready
 */
(function(){
	var _isReady = false,
		_aReadyFnCache = [],
		_isReadyBound = false,
		_timer = null,
		_ready, _bindReady, ready;
	_ready = function()
	{
		var it,
			i,
			j;
		if(_timer){
			clearInterval(_timer);
			_timer = null;
		}
		if(_isReady) {return ;}
		_isReady		=	true;
		for(i=0,j=_aReadyFnCache.length;i<j;i++) {
			_aReadyFnCache[i]();
		}
		_aReadyFnCache=	[];
	};
	_bindReady = function()
	{
		if(_isReadyBound) {return ;}
		_isReadyBound	=	true;
		if(document.addEventListener){
			document.addEventListener("DOMContentLoaded", _ready, false);	
		}else if (document.attachEvent){
			document.attachEvent("onreadystatechange", function(){
				if((/loaded|complete/).test(document.readyState)) { _ready();}
			});
			if(window == window.top){
				_timer = setInterval(function(){
					try{
						_isReady || document.documentElement.doScroll('left');
					}catch(e){return ;}
					_ready();
				},5);
			}else{
				lamb.event.addEvent(window, 'load', _ready);
			}
		}
	};
	ready = function(fn)
	{
		_bindReady();
		_isReady?fn.call():_aReadyFnCache.push(fn);
	};
	lamb.ready = ready;
})();

/**
 * @class lamb.ajax
 */

lamb.ajax = function(option)
{
	var _option,
		request,
		headers,
		k, f;
	_option = {
		'url'		:	'',
		'type'		:	'GET',
		'dataType'	:	'text',
		'error'		:	function(){},
		'success'	:	function(){},
		'async'		:	true,//默认异步
		'params'	:	'',
		'headers'	:	{},
		'load'		:	function(){}
	};
	f = function()
	{
		var result;
		if(request.readyState==4)
		{
			if(request.status==200)
			{
				(_option.success)(_option.dataType=='text'?request.responseText:request.responseXml);
				return true;
			}
			else
			{
				(_option.error)(request.status);
				return false;
			}
		}
		(_option.load)(request.readyState);
	};
	
	lamb.utils.extend(_option, option);
	request = window.ActiveXObject ? new window.ActiveXObject('Microsoft.XMLHTTP') : new window.XMLHttpRequest();
	request.onreadystatechange = f;
	request.open(_option.type,_option.url,_option.async);
	headers = _option.headers;
	for(k in headers) {
		if(headers.hasOwnProperty(k)) {
			request.setRequestHeader(headers[k]['name'],headers[k]['value']);
		}
	}
	
	if(_option.type=='GET') {
		request.send(null);
	}
	else {
		request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var param = [];
		for (var k in _option.params) {
			if (_option.params.hasOwnProperty(k)) {
				param.push(k + '=' + encodeURIComponent(_option.params[k]));
			}
		}
		request.send(param.join('&'));
	}
	
	if(!_option.async && lamb.browser.gecko) {request.onreadystatechange = f};
};

/**
 * @object remoteAjax
 */
lamb.remoteAjax = {

	'_frames' : [],
	
	'_forms' : [],
	
	/** 
	 * @param object option
	 * @return lamb.remoteAjax
	 */
	'get' : function(option) 
	{
		var opt = {
			'url' : '',
			'error' : function(){},
			'success' : function(){},
			'timeout' : 20,
			'frame_opt' : 0, //0 - optimize 1- new other-id
			'domain' : ''
		}, timer = null, _frame;
		$F.extend(opt, option);
		if (opt.domain) {
			document.domain = opt.domain;
		}
		_frame = this.getElement(1, opt.frame_opt);
		_frame.isWorking = 1;
		_frame._loadhandler && lamb.event.removeIframeEvent(_frame, 'load', _frame._loadhandler)
		_frame._loadhandler = (function(_frame, opt)
		{
			return function()
			{
				timer && clearTimeout(timer);
				if (_frame.isWorking) {
					_frame.isWorking = 0;
					var doc = _frame.contentDocument || _frame.contentWindow.document;
					opt.success(doc.body.innerHTML);
				}
			};
		})(_frame, opt);
		lamb.event.addIframeEvent(_frame, 'load', _frame._loadhandler);
		_frame.src = opt.url;
		if (opt.timeout > 0) {
			timer = setTimeout((function(_frame, opt){
					return function()
					{
						_frame.isWorking = 0;
						opt.error();
					}
					})(_frame, opt), opt.timeout * 1000);
		}
		return this;
	},
	
	/**
	 * @param object option
	 * @return lamb.remoteAjax
	 */
	'post' : function(option) 
	{
		var opt = {
			'url' : '',
			'params' : {},
			'error' : '',
			'success' : '',
			'timeout' : 20,
			'frame_opt' : 0,
			'form_opt' : 0,
			'domain' : ''
		}, form, _frame, timer = null;
		$F.extend(opt, option);
		if (opt.domain) {
			document.domain = opt.domain;
		}		
		form = this.getElement(2, opt.form_opt);
		_frame = this.getElement(1, opt.frame_opt);
		_frame.isWorking = 1;
		form.innerHTML = '';
		for (var key in opt.params) {
			if (!key) {
				continue;
			}
			var input = document.createElement('input');
			with(input) {
				type = 'hidden';
				name = key;
				value = opt.params[key];	
			}
			form.appendChild(input);
		}
		form.target = _frame.name;
		form.action = opt.url;
		_frame._loadhandler && lamb.event.removeIframeEvent(_frame, 'load', _frame._loadhandler)
		_frame._loadhandler = (function(_frame, opt)
		{
			return function()
			{
				timer && clearTimeout(timer);
				if (_frame.isWorking) {
					_frame.isWorking = 0;
					var doc = _frame.contentDocument || _frame.contentWindow.document;
					opt.success(doc.body.innerHTML);
				}
			};
		})(_frame, opt);
		lamb.event.addIframeEvent(_frame, 'load', _frame._loadhandler);
		form.submit();
		if (opt.timeout > 0) {
			timer = setTimeout((function(_frame, opt){
					return function()
					{
						_frame.isWorking = 0;
						opt.error();
					}
					})(_frame, opt), opt.timeout * 1000);
		}		
	},
	
	/**
	 * @param int type 1 - frame 2 - form
	 * @param int | string | dom option 0 - optimize 1 - new  string - id dom - frameDom
	 */
	'getElement' : function(type, option) 
	{
		var ret = null,
			caches = this._frames, tagname = 'iframe';
		if (type == 2) {
			caches = this._forms;
			tagname = 'form';
		} 
		switch (typeof option) {
			case 'number':
				{
					//get cache frames
					if (option == 0) {
						for (var i = 0, j = caches.length; i < j; i ++) {
							if (!caches[i].isWorking) {
								ret = caches[i];
								break;
							} 
						}
						if (ret) {
							break;
						}
					}
					//create new iframe	
					ret = document.createElement(tagname)
					if (type == 2) {
						with (ret) {
							method = 'post';
							style.width = '0px';
							style.height = '0px';
							style.overflow = 'hidden';
						}
					} else {
						with(ret) {
							width = 500;
							height = 500;
							frameBorder = 0;
							scrolling = 'no';
							name = this.getUniqueName();
							id = name;
						}
					}
					document.body.appendChild(ret);
					caches.push(ret);
				}
				break;
			case 'string':
				ret = $A(option);
				break;
			case 'object':
				ret = option;
		}
		if (!ret.id) {
			ret.id = this.getUniqueName();
		}
		if (!ret.name) {
			ret.name = ret.id;
		}
		return ret;
	},
	
	'clearCacheElements' : function(type)
	{
		type == 2 ? (this._forms = []) : (this._frames = []);
		return this;
	},
	
	'getUniqueName' : function()
	{
		return '__iframe__name_' + new Date().valueOf() + Math.random();
	}
};


/**
 * @class lamb.extend.CDialog
 */
(function(){
	var CDialog = lamb.utils.$Class({
		//@Property
			//public
			//private
			_m_option			:	null,
		//@Constructor
			initialize			:	function(option)
			{
				var _option;
				_option = {
					'html'		:	'',
					'child_dom'	:	null,
					'opacity'	:	40,
					'frontID'	:	'frontDiv',
					'bgID'		:	'bgDiv',
					'fix'		:	true,
					'f_zindex'	:	9999,
					'zindex'	:	999,
					'oncreate'	:	function(){},
					'onclose'	:	function(){},
					'custom_close_fuc': null
				};
				lamb.utils.extend(_option, option);
				this._m_option = _option;
			},
		//@Method
			//public
			showModal			:	function()
			{
				var	oBg,
					$setBgSize,
					oContent,
					iOpacity,
					sInnerHTML,
					of;
				of				=	this._createFront();	
				iOpacity		=	this._m_option.opacity;
				oContent		=	document.documentElement;
				oBg				=	document.createElement('div');		
				oBg.setAttribute('id',this._m_option.bgID);
				oBg.style.position='absolute';		
				($setBgSize=function(){oBg.style.width=oContent.scrollWidth+'px';
				oBg.style.height=Math.max(document.getElementsByTagName('html')[0].offsetHeight,document.body.scrollHeight,document.documentElement.scrollHeight)+'px';
				oBg.style.width='100%';
				})();
				oBg.style.zIndex = this._m_option.zindex;
				oBg.style.top=oBg.style.left=0;
				oBg.style.backgroundColor='#000';
				oBg.style.filter='alpha(opacity='+iOpacity+')';
				oBg.style.opacity=iOpacity/100;
				if(lamb.browser.ie)
				{
					sInnerHTML='<!--[if lt IE 6]>';
					sInnerHTML+='<div style="position:absolute;z-index:-1; top:0; left:0;">';
					sInnerHTML+='<iframe style="filter:alpha(opacity=40); width:100%; height:100%;">';
					sInnerHTML+='</iframe></div>';
					sInnerHTML+='<![endif]-->';
					oBg.innerHTML=sInnerHTML;	
				}
				lamb.event.addEvent(window,'resize',$setBgSize);
				document.body.appendChild(oBg);
				document.body.appendChild(of);
				(this._getSetFrontPosFunc(of, this._m_option.fix))();
				window.focus();
				this._m_option.oncreate(this, of ,oBg);
			},
			showModalLess		:	function()
			{
				if(!lamb.$(this._m_option.frontID))
				{
					var of;
					of = this._createFront();
					document.body.appendChild(of);
					(this._getSetFrontPosFunc(of, this._m_option.fix))();
					this._m_option.oncreate(this, of ,null);
					of = null;
				}
			},
			close				:	function()
			{
				if (!this._m_option.custom_close_fuc) {
					try{
						document.body.removeChild(lamb.$(this._m_option.frontID));
						document.body.removeChild(lamb.$(this._m_option.bgID));
					}catch(e){}
				}
				else {
					this._m_option.custom_close_fuc(this);
				}
				this._m_option.onclose(this);
			},
			setTop			:	function(i)
			{
				lamb.$(this._m_option.frontID).style.top=i+'px';
			},
			//private
			_createFront		:	function()
			{
				var DOMFront,
					fSetPos,
					oContent,
					top,
					iWidth, isFixed;
				iWidth		=	this._m_option.width;
				oContent	=	lamb.dom.doc();
				DOMFront	=	document.createElement('div');
				top			=	this._m_option.top;
				with(DOMFront)
				{
					setAttribute('id',this._m_option.frontID);
					style.zIndex = this._m_option.f_zindex;
					if(this._m_option.html){
						innerHTML=this._m_option.html;
					}
					else if(this._m_option.child_dom){
						appendChild(this._m_option.child_dom);
					}
				}
				isFixed = this._m_option.fix
				fSetPos = this._getSetFrontPosFunc(DOMFront, this._m_option.fix);
				if(isFixed) {
					DOMFront.style.position = lamb.browser.ie_version != 6 ? 'fixed' : 'absolute';
					if(lamb.browser.ie_version == 6) lamb.event.addEvent(window,'scroll',fSetPos);
				}
				else {
					DOMFront.style.position='absolute';
				}
				lamb.event.addEvent(window,'resize',fSetPos);
				return DOMFront;
			},
			'_getSetFrontPosFunc' : function(DOMFront, isFixed)
			{
				return function()
				{
					var iClientWidth = lamb.dom.getClientWidth(),
						iClientHeight = lamb.dom.getClientHeight(),
						iFrontWidth = DOMFront.offsetWidth,
						iFrontHeight = DOMFront.offsetHeight,
						iLeft = iTop = 0;
						//alert(document.body.clientHeight);
					if (iClientWidth > iFrontWidth) {
						iLeft = parseInt((iClientWidth - iFrontWidth) / 2);	
					}
					if (iClientHeight > iFrontHeight) {
						iTop = parseInt((iClientHeight - iFrontHeight) / 2);	
					}
					DOMFront.style.left = lamb.dom.getScrollLeft() + iLeft + 'px';
					if (lamb.browser.ie && lamb.browser.version == 6) {
						DOMFront.style.top = (lamb.dom.getScrollTop() + iTop) + 'px';
					}
					else {
						DOMFront.style.top = (isFixed ? iTop :(lamb.dom.getScrollTop() + iTop)) + 'px';
					}
				};			
			}
		});
	lamb.extend.CDialog = CDialog;
})();

/**
 * @class lamb.extend.CAnimation
 */
(function(){
	var CAnimation	=	lamb.utils.$Class({
		//@Property
		    //public
		    m_iBegin            :   0,
		    m_iFrames           :   -1,
		    m_iCurrentFrame     :   1,
		    m_iLength           :   -1,
		    m_iExtval1          :   undefined,
		    m_iExtval2          :   undefined,
		    //private
			_m_timer			:	null,
		    _m_fTween           :   null,
		//@Constructor
			initialize          :   function(iType,t,c,d,b/*=0*/,a/*=undefined*/,p/*=undefined*/)
			{
			   iType    && (this._m_fTween = this.getTweenFn(iType));
			   this.setOptions(t,c,d,b,a,p);
			},
		//@Method
		    //public
		    start               :   function(iIntval,fn)
		    {
		        var that    =   this,
		        	core;

		        core = function()
		          {
		             var iRet   =   that._m_fTween(that.m_iCurrentFrame++,that.m_iBegin,that.m_iLength,
		            		 that.m_iFrames,that.m_iExtval1,that.m_iExtval2);
		             fn(iRet);
		             if(that.m_iCurrentFrame<=that.m_iFrames) {
		                that._m_timer = window.setTimeout(core,iIntval);
		             }
		             else {
		                that.m_iCurrentFrame = 0;
		             }
		          };
		          that._m_timer = window.setTimeout(core,iIntval);
		    },
		    setOptions          :   function(t,c,d,b/*=0*/,a/*=undefined*/,p/*=undefined*/)
		    {
		        if(a) this.m_iExtval1   =   a;
		        if(p) this.m_iExtval2   =   p;
		        if(b) this.m_iBegin     =   b;
		        if(t) this.m_iCurrentFrame= t;
		        this.m_iLength          =   c;
		        this.m_iFrames          =   d;
		    },
			setAniType          :   function(iType)
			{
			    this._m_fTween      =   this.getTweenFn(iType);
			},
			stop				:	function()
			{
				if(this._m_timer){
					window.clearTimeout(this._m_timer);	
					this.m_iCurrentFrame	=	0;
				}
			},
			getTweenFn          :   function(iType)
			{
			    var fn      =   null,
			        k,
			        _k,
			        isExist =   false,
			        arr,
			        tween   =   CAnimation.Tween;
			    for(k in CAnimation.CONST_TYPE)
			    {
			        if(CAnimation.CONST_TYPE[k] == iType){
			            iType   =   k;
			            isExist =   true;
			            break;
			        }
			    }
			    
			    iType   =   iType + '';
			    
			    if(isExist){
			        arr     =   iType.split('_');
			        main:
			        for(k in tween)
			        {
			            if(k.toUpperCase() == arr[0]){
			                if(typeof(tween[k]) == 'object'){
			                    if(arr[1])
			                    {
			                        for(_k in tween[k])
			                        {
			                            if(_k.toUpperCase() == ('EASE'+arr[1]) ){
			                                fn  =   tween[k][_k];
			                                break main;
			                            }
			                        }
			                    }
			                }
			                else{
			                    fn  =   tween[k];
			                    break;
			                }
			            }
			        }
			    }
			    
			    return fn;
			}
		});
		CAnimation.CONST_TYPE   ={
		    LINEAR          :   0x1,
		    QUAD_IN         :   0x2,
		    QUAD_OUT        :   0x4,
		    QUAD_INOUT      :   0x8,
		    CUBIC_IN        :   0x10,
		    CUBIC_OUT       :   0x20,
		    CUBIC_INOUT     :   0x40,
		    QUART_IN        :   0x80,
		    QUART_OUT       :   0x100,
		    QUART_INOUT     :   0x200,
		    QUINT_IN        :   0x400,
		    QUINT_OUT       :   0x800,
		    QUINT_INOUT     :   0x1000,
		    SINE_IN         :   0x2000,
		    SINE_OUT        :   0x4000,
		    SINE_INOUT      :   0x8000,
		    EXPO_IN         :   0x10000,
		    EXPO_OUT        :   0x20000,
		    EXPO_INOUT      :   0x40000,
		    CIRC_IN         :   0x80000,
		    CRIC_OUT        :   0x100000,
		    CRIC_INOUT      :   0x200000,
		    ELASTIC_IN      :   0x400000,
		    ELASTIC_OUT     :   0x800000,
		    ELASTIC_INOUT   :   0x1000000,
		    BACK_IN         :   0x2000000,
		    BACK_OUT        :   0x4000000,
		    BACK_INOUT      :   0x8000000,
		    BOUNCE_IN       :   0x10000000,
		    BOUNCE_OUT      :   0x20000000,
		    BOUNCE_INOUT    :   0x40000000
		};
		CAnimation.Tween        = {
			Linear: function(t,b,c,d){ return c*t/d + b; },
			Quad: {
				easeIn: function(t,b,c,d){
					return c*(t/=d)*t + b;
				},
				easeOut: function(t,b,c,d){
					return -c *(t/=d)*(t-2) + b;
				},
				easeInOut: function(t,b,c,d){
					if ((t/=d/2) < 1){ return c/2*t*t + b};
					return -c/2 * ((--t)*(t-2) - 1) + b;
				}
			},
			Cubic: {
				easeIn: function(t,b,c,d){
					return c*(t/=d)*t*t + b;
				},
				easeOut: function(t,b,c,d){
					return c*((t=t/d-1)*t*t + 1) + b;
				},
				easeInOut: function(t,b,c,d){
					if ((t/=d/2) < 1){ return c/2*t*t*t + b };
					return c/2*((t-=2)*t*t + 2) + b;
				}
			},
			Quart: {
				easeIn: function(t,b,c,d){
					return c*(t/=d)*t*t*t + b;
				},
				easeOut: function(t,b,c,d){
					return -c * ((t=t/d-1)*t*t*t - 1) + b;
				},
				easeInOut: function(t,b,c,d){
					if ((t/=d/2) < 1) { return c/2*t*t*t*t + b };
					return -c/2 * ((t-=2)*t*t*t - 2) + b;
				}
			},
			Quint: {
				easeIn: function(t,b,c,d){
					return c*(t/=d)*t*t*t*t + b;
				},
				easeOut: function(t,b,c,d){
					return c*((t=t/d-1)*t*t*t*t + 1) + b;
				},
				easeInOut: function(t,b,c,d){
					if ((t/=d/2) < 1) { return c/2*t*t*t*t*t + b };
					return c/2*((t-=2)*t*t*t*t + 2) + b;
				}
			},
			Sine: {
				easeIn: function(t,b,c,d){
					return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
				},
				easeOut: function(t,b,c,d){
					return c * Math.sin(t/d * (Math.PI/2)) + b;
				},
				easeInOut: function(t,b,c,d){
					return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
				}
			},
			Expo: {
				easeIn: function(t,b,c,d){
					return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
				},
				easeOut: function(t,b,c,d){
					return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
				},
				easeInOut: function(t,b,c,d){
					if (t==0) { return b };
					if (t==d) { return b+c };
					if ((t/=d/2) < 1) { return c/2 * Math.pow(2, 10 * (t - 1)) + b };
					return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
				}
			},
			Circ: {
				easeIn: function(t,b,c,d){
					return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
				},
				easeOut: function(t,b,c,d){
					return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
				},
				easeInOut: function(t,b,c,d){
					if ((t/=d/2) < 1) { return -c/2 * (Math.sqrt(1 - t*t) - 1) + b };
					return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
				}
			},
			Elastic: {
				easeIn: function(t,b,c,d,a,p){
					if (t==0) { return b };  if ((t/=d)==1) { return b+c };  if (!p) { p=d*.3 };
					if (!a || a < Math.abs(c)) { a=c; var s=p/4; }
					else { var s = p/(2*Math.PI) * Math.asin (c/a) };
					return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
				},
				easeOut: function(t,b,c,d,a,p){
					if (t==0) { return b;}  if ((t/=d)==1) { return b+c;}  if (!p){ p=d*.3};
					if (!a || a < Math.abs(c)) { a=c; var s=p/4; }
					else { var s = p/(2*Math.PI) * Math.asin (c/a) };
					return (a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b);
				},
				easeInOut: function(t,b,c,d,a,p){
					if (t==0) { return b; } if ((t/=d/2)==2){ return b+c; }  if (!p) { p=d*(.3*1.5) };
					if (!a || a < Math.abs(c)) { a=c; var s=p/4; }
					else { var s = p/(2*Math.PI) * Math.asin (c/a) };
					if (t < 1) { return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b };
					return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
				}
			},
			Back: {
				easeIn: function(t,b,c,d,s){
					if (s == undefined) { s = 1.70158 };
					return c*(t/=d)*t*((s+1)*t - s) + b;
				},
				easeOut: function(t,b,c,d,s){
					if (s == undefined) { s = 1.70158; }
					return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
				},
				easeInOut: function(t,b,c,d,s){
					if (s == undefined) s = 1.70158; 
					if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
					return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
				}
			},
			Bounce: {
				easeIn: function(t,b,c,d){
					return c - CAnimation.Tween.Bounce.easeOut(d-t, 0, c, d) + b;
				},
				easeOut: function(t,b,c,d){
					if ((t/=d) < (1/2.75)) {
						return c*(7.5625*t*t) + b;
					} else if (t < (2/2.75)) {
						return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
					} else if (t < (2.5/2.75)) {
						return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
					} else {
						return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
					}
				},
				easeInOut: function(t,b,c,d){
					if (t < d/2) { return CAnimation.Tween.Bounce.easeIn(t*2, 0, c, d) * .5 + b; }
					else return CAnimation.Tween.Bounce.easeOut(t*2-d, 0, c, d) * .5 + c*.5 + b;
				}
			}
		};
	lamb.extend.CAnimation = CAnimation;
})();

/**
 * @class lamb.extend.ImageLazyLoad
 */
lamb.extend.ImageLazyLoad = function(option)
{
	var _container,
		imgList,
		isWindow,
		setting		=	{
			'parent'	:	null,
			'replace'	:	'',
			'container'	:	'',
			'onbegin'	:	function(){},
			'onend'		:	function(){},
			'onhide'	:	function(obj){},
			'onpevshow'	:	function(obj){},
			'onshow'	:	function(obj){obj.src	=	obj.getAttribute('_src')}
		},
		_cont,
		getContRect, getObjectRect, isRectInter, _load;
		
	lamb.utils.extend(setting, option);
	isWindow	=	setting.parent?false:true;
	_cont		=	setting.parent;
	imgList		=	setting['container'].getElementsByTagName('img');
	_container	=	isWindow?Dom.doc:_cont;

	getContRect = function()
	{
		return {
			'left'	:	lamb.dom.getScrollLeft(_cont),
			'top'	:	lamb.dom.getScrollTop(_cont),
			'width'	:	_container.clientWidth,
			'height':	_container.clientHeight
		};	
	};
	
	getObjectRect = function(node)
	{
		var rect = lamb.dom.pageRect(node);
		return {
			'left'	:	rect.left,
			'top'	:	rect.top,
			'width'	:	rect.right-rect.left,
			'height':	rect.bottom-rect.top
		};
	};

	isRectInter = function(r1,r2)
	{
		var w1,w2,h1,h2,w,h;
		w1	=	r1.left + r1.width/2;
		w2	=	r2.left + r2.width/2;
		h1	=	r1.top	+ r1.height/2;
		h2	=	r2.top	+ r2.height/2;
		w	=	(r1.width+r2.width)/2;
		h	=	(r1.height+r2.height)/2;
		return Math.abs(w1-w2)<w&&Math.abs(h1-h2)<h;
	};
	
	_load = function()
	{
		var windowClient	=	getContRect(),
			a				=	[];
		setting.onbegin();
		lamb.utils.each(imgList,function(it,i){
			setting.onpevshow(it);
			var objectClient = getObjectRect(it);
			if(isRectInter(windowClient,objectClient)){
				setting.onshow(it);	
			}else{
				a.push(it);
				setting.onhide(it);
			}
		});
		imgList = a;
		if(imgList.length==0){
			lamb.event.removeEvent(isWindow?window:_cont,'scroll',_load);
			lamb.event.removeEvent(_cont || window,'resize',_load);
			setting.onend();
			return ;
		}
	};	
	
	if(setting['replace']){
		lamb.utils.each(imgList,function(it,i){
			lamb.event.addEvent(it,'error',function(){
				it.src	=	setting['replace'];
			});
		});
	}
	if(imgList.length>0){
		lamb.event.addEvent(isWindow?window:_cont,'scroll',_load);
		lamb.event.addEvent(_cont || window,'resize',_load);		
		_load();
	}
};

/**
 * @class lamb.extend.drag
 * @param {object} option
 * 	drager : 触发拖到的对象
 *  moveObject : 移动的对象  当moveObject为null时，drager就是moveObject
 */
lamb.extend.drag = function(option)
{
	var aOptions	=	{
		'drager'	:	null,
		'moveObject':	null,
		'onstart'	:	function(){},
		'onmove'	:	function(){},
		'onstop'	:	function(){},
		'isLimit'	:	false,
		'isLockX'	:	false,
		'isLockY'	:	false,
		'maxleft'	:	0,
		'maxright'	:	99999,
		'maxtop'	:	0,
		'maxbottom'	:	99999,
		'container'	:	null,
		'isFixFrame':	false
	};
	lamb.utils.extend(aOptions, option);
	var fStart,fMove,fStop,
		x,y,ml,mt;
	fStart			=	function(event)
	{
		if(lamb.browser.ie){
			lamb.event.addEvent(aOptions.drager, 'losecapture', fStart);
			aOptions.drager.setCapture();		
		}
		else{
			lamb.event.addEvent(window, 'blur', fStart);
			event.preventDefault();
		}
		ml=parseInt(aOptions.drager.style.marginLeft) || 0;
		mt=parseInt(aOptions.drager.style.marginTop) || 0;
		x = event.clientX - aOptions.drager.offsetLeft;
		y = event.clientY - aOptions.drager.offsetTop;
		lamb.event.addEvent(document, 'mousemove', fMove);
		lamb.event.addEvent(document,'mouseup',fStop);
		if(aOptions.isFixFrame){
			var aFrames	=	aOptions.drager.getElementsByTagName('iframe');
			for(i=0,j=aFrames.length;i<j;i++) aFrames[i].style.display	=	'none';
		}		
		aOptions.onstart(event);
	};
	fMove			=	function(event)
	{
		var iX,iY,
			iMaxLeft,iMaxTop,iMaxRight,iMaxBottom;
		iX 				= event.clientX-x;	
		iY 				= event.clientY-y;
		try{
			window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
		}catch(e){}
		if(aOptions.isLimit)
		{
			iMaxLeft 	= aOptions.maxleft;
			iMaxRight	= aOptions.maxright;
			iMaxTop		= aOptions.maxtop;
			iMaxBottom 	= aOptions.maxbottom;
			if(aOptions.container){
				iMaxLeft = Math.max(iMaxLeft,0);	
				iMaxTop = Math.max(iMaxTop,0);
				iMaxRight = Math.min(iMaxRight,aOptions.container.clientWidth);
				iMaxBottom = Math.min(iMaxBottom,aOptions.container.clientHeight);
			}
			iX = Math.max(iMaxLeft,Math.min(iX,iMaxRight-aOptions.drager.offsetWidth));
			iY = Math.max(iMaxTop,Math.min(iY,iMaxBottom-aOptions.drager.offsetHeight));
		}
		iX	=	iX-ml;
		iY	=	iY-mt;
		if(!aOptions.isLockX){
			aOptions.drager.style.left = iX+'px';
		}
		if(!aOptions.isLockY){
			aOptions.drager.style.top = iY+'px';
		}
		aOptions.onmove({event:event,x:iX,y:iY});	
	};
	fStop			=	function(event)
	{
		lamb.event.removeEvent(document,'mousemove',fMove);
		lamb.event.removeEvent(document,'mouseup',fStop);
		if(lamb.browser.ie){
			lamb.event.removeEvent(aOptions.drager,'losecapture',fStart);		
			aOptions.drager.releaseCapture();
		}
		else{
			lamb.event.removeEvent(window,'blur',fStart);
		}
		if(aOptions.isFixFrame){
			var aFrames	=	aOptions.drager.getElementsByTagName('iframe');
			for(i=0,j=aFrames.length;i<j;i++) { aFrames[i].style.display	=	'' };
		}		
		aOptions.onstop(event);	
	};
	lamb.event.addEvent(aOptions.drager,'mousedown',fStart);
	aOptions.moveObject	&& (aOptions.drager	=	aOptions.moveObject);
};

/**
 * @class lamb.extend.dragByCopy
 */
lamb.extend.dragByCopy = function(option)
{
	var aOptions	=	{
		'drager'		:	null,
		'moveObject'	:	null,
		'borderStyle'	:	'2px dotted #ccc',
		'onstart'		:	function(){},
		'onmove'		:	function(){},
		'onstop'		:	function(){},
		'isLockX'		:	false,
		'isLockY'		:	false
	};
	lamb.utils.extend(aOptions, option);
	var x,y,ml,mt,domTemp,
		fStart,fMove,fStop;
	fStart			=	function(event)
	{
		var rect;
		domTemp				=	document.createElement('div');
		domTemp.style.width	=	aOptions.drager.offsetWidth+'px';
		domTemp.style.height=	aOptions.drager.offsetHeight+'px';
		domTemp.style.border=	aOptions.borderStyle;
		domTemp.style.position=	'absolute';
		/*domTemp.style.backgroundColor='#fff';*/
		domTemp.style.zIndex=	9999999999;
		/*lamb.dom.setFilter(domTemp,10);*/
		rect				=	lamb.dom.pageRect(aOptions.drager);
		domTemp.style.left	=	rect.left+'px';
		domTemp.style.top	=	rect.top+'px';
		document.body.appendChild(domTemp);
		ml					=	parseInt(aOptions.drager.style.marginLeft) || 0;
		mt					=	parseInt(aOptions.drager.style.marginTop) || 0;
		x					=	event.clientX-aOptions.drager.offsetLeft;
		y					=	event.clientY-aOptions.drager.offsetTop;		
		lamb.event.addEvent(document,'mousemove',fMove);
		lamb.event.addEvent(document,'mouseup',fStop);
		aOptions.onstart(event);
	};
	fMove			=	function(event)
	{
		window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
		var ix	=	event.clientX-x,
			iy	=	event.clientY-y;
		if(!aOptions.isLockX){	
			domTemp.style.left		=	ix+'px';
		}
		if(!aOptions.isLockY){
			domTemp.style.top		=	iy+'px';
		}
		aOptions.onmove({event:event,x:ix,y:iy});
	};
	fStop			=	function(event)
	{
		lamb.event.removeEvent(document,'mousemove',fMove);
		lamb.event.removeEvent(document,'mouseup',fStop);
		aOptions.drager.style.left	=	(parseInt(domTemp.style.left)-ml)+'px';
		aOptions.drager.style.top	=	(parseInt(domTemp.style.top)-mt)+'px';
		document.body.removeChild(domTemp);
		aOptions.onstop(event);
	};
	lamb.event.addEvent(aOptions.drager,'mousedown',fStart);
	aOptions.moveObject && (aOptions.drager = aOptions.moveObject);
};

/**
 * @object lamb.json
 */
lamb.json = {};
(function(){
	function f(n) {
        return n < 10 ? '0' + n: n
    }
    if (typeof Date.prototype.toJSON !== 'function') {
        Date.prototype.toJSON = function(key) {
            return isFinite(this.valueOf()) ? this.getUTCFullYear() + '-' + f(this.getUTCMonth() + 1) + '-' + f(this.getUTCDate()) + 'T' + f(this.getUTCHours()) + ':' + f(this.getUTCMinutes()) + ':' + f(this.getUTCSeconds()) + 'Z': null
        };
        String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function(key) {
            return this.valueOf()
        }
    }
	var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
    escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
    gap,
    indent,
    meta = {
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"': '\\"',
        '\\': '\\\\'
    },
    rep;
	function quote(string) {
        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, 
        function(a) {
            var c = meta[a];
            return typeof c === 'string' ? c: '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice( - 4)
        }) + '"': '"' + string + '"'
    }
	function str(key, holder) {
        var i,
        k,
        v,
        length,
        mind = gap,
        partial,
        value = holder[key];
        if (value && typeof value === 'object' && typeof value.toJSON === 'function') {
            value = value.toJSON(key)
        }
        if (typeof rep === 'function') {
            value = rep.call(holder, key, value)
        }
        switch (typeof value) {
        case 'string':
            return quote(value);
        case 'number':
            return isFinite(value) ? String(value) : 'null';
        case 'boolean':
        case 'null':
            return String(value);
        case 'object':
            if (!value) {
                return 'null'
            }
            gap += indent;
            partial = [];
            if (Object.prototype.toString.apply(value) === '[object Array]') {
                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null'
                }
                v = partial.length === 0 ? '[]': gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']': '[' + partial.join(',') + ']';
                gap = mind;
                return v
            }
            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    k = rep[i];
                    if (typeof k === 'string') {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ': ':') + v)
                        }
                    }
                }
            } else {
                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ': ':') + v)
                        }
                    }
                }
            }
            v = partial.length === 0 ? '{}': gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}': '{' + partial.join(',') + '}';
            gap = mind;
            return v
        }
    }
	if (typeof lamb.json.stringify !== 'function') {
        lamb.json.stringify = function(value, replacer, space) {
            var i;
            gap = '';
            indent = '';
            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' '
                }
            } else if (typeof space === 'string') {
                indent = space
            }
            rep = replacer;
            if (replacer && typeof replacer !== 'function' && (typeof replacer !== 'object' || typeof replacer.length !== 'number')) {
                throw new Error('lamb.json.stringify')
            }
            return str('', {
                '': value
            })
        }
    }
	if (typeof lamb.json.parse !== 'function') {
        lamb.json.parse = function(text, reviver) {
            var j;
            function walk(holder, key) {
                var k,
                v,
                value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v
                            } else {
                                delete value[k]
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value)
            }
            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, 
                function(a) {
                    return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice( - 4)
                })
            }
            if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                j = eval('(' + text + ')');
                return typeof reviver === 'function' ? walk({
                    '': j
                },
                '') : j
            }
            throw new SyntaxError('lamb.json.parse')
        }
    }	
})();

/**
 * init
 */
(function(){
	window.$A || (window.$A = lamb.$);
	window.$B || (window.$B = lamb);
	window.$C || (window.$C = lamb.cookie);
	window.$D || (window.$D = lamb.dom);
	window.$E || (window.$E = lamb.event);
	window.$F || (window.$F = lamb.utils);
})();
