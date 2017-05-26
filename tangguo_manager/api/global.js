function checkAll(name)
{
	var oa=document.getElementsByName(name);
	for(i=0;i<oa.length;i++)
	{
		oa[i].checked?oa[i].checked=false:oa[i].checked=true;
	}
}

var simpleUpload = function(){};

simpleUpload.prototype=new function()
{
	var oInputSave=fShow=null;
	
	this.initialize=function(oInput,fHander,oInputSavePath,fShowHander)
	{
		$E.addEvent(oInput,'change',fHander);
		oInputSave=oInputSavePath;
		fShow=fShowHander;
	}

	this.uploadSucc=function(path)
	{
		oInputSave.value=path;
		fShow(path);
	}

	this.deleteImg=function()
	{
		oInputSave.value='';
	}
}
var CDynamicAjax	=	$F.$Class({
	m_strLastValue		:	'',								 
	m_aOptions			:	null,
	'initialize'		:	function(aOptions)
	{
		this.m_aOptions	=	{
			'url'		:	function(s){},
			'success'	:	function(s,l,c){},
			'className'	:	''
		};
		$F.extend(this.m_aOptions, aOptions);
	},
	create				:	function(domParent)
	{
		var domInput,
			_create;
		_create				=	function()
		{
			var strValue	=	$F.trim(event.srcElement.value),
				that		=	this,
				ls;
			if(!$F.isEmpty(strValue) && strValue!=this.m_strLastValue){
				ls			=	that.m_strLastValue;
				$B.ajax({
					 'url'		:	this.m_aOptions['url'](strValue),
					 'success'	:	function(s){that.m_aOptions['success'](s,ls,strValue)},
					 'error'	:	function(){domParent.innerHTML	=	that.m_strLastValue;}
				});
			}
			else{
				domParent.innerHTML	=	this.m_strLastValue;
			}
			this.m_strLastValue = '';
			this._m_bFinish=true;
		}
		if(!domParent.childNodes[0] || domParent.childNodes[0].tagName!='INPUT'){
			this.m_strLastValue	=	domParent.innerHTML;
			domParent.innerHTML	=	'';
			domInput			=	document.createElement('input');
			domInput.setAttribute('type','text');
			domInput.className=this.m_aOptions['className'];
			domInput.value=this.m_strLastValue;
			domParent.appendChild(domInput);
			domInput.focus();
			$E.addEvent(domInput,'blur',$F.bind(this,_create))
		}
	}
});
var selectLocalShow = {
	
	'bind' : function(domSelect, key, searchLists)
	{
		$E.addEvent(domSelect, 'change', function(){
			selectLocalShow.main(domSelect.options[domSelect.selectedIndex].value, key, searchLists);
		});
		return this;
	},
	
	'main' : function(value, key, searchLists)
	{
		$F.each(searchLists, function(dom) {
			var attr;
			if ((attr = dom.getAttribute(key))) {
				if ($F.inArray((attr + '').split(','), value)) {
					dom.style.display = '';		
				} else {
					dom.style.display = 'none';	
				}
			}						  
		});	
		return this;
	}
};
var Router = {	
	'delimiter' : g_aCfg.router.url_delimiter,
	'param_name' : g_aCfg.router.url_param_name,
	'encode_map' : {'/' : '~@:@~'},
	'encode' : function (str)
	{
		for (var repl in this.encode_map) {
			str = $F.replaceEx(str, repl, this.encode_map[repl]);	
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
	'get' : function(controllor, action, params)
	{
		controllor = controllor || '';
		action = action || '';
		params = params || {};
		var params = this.make(params);
		return '?' + this.param_name + '=' + controllor + this.delimiter + 
				action + (params ? (this.delimiter + params) : '');
	},
	'make' : function(params)
	{
		var ret = [];
		for (var name in params) {
			ret.push(this.encode(name));
			ret.push(this.encode(params[name]));
		}
		return ret.join(this.delimiter);
	},
	'bindForm' : function(isBind, control, action, form, includeBtn/*=false*/)
	{
		var that = this,
			core = function(){
				event.returnValue = false;
				location.href = '?' + that.param_name + '=' + that.encode(control) + that.delimiter + 
					that.encode(action) + that.delimiter + that.bindFormCore(form, includeBtn);	
			};
		isBind ? (form.onsubmit = core) : core();
	},
	'bindFormCore' : function(form, includeBtn /*=false*/)
	{
		includeBtn = !!includeBtn;
		var cols = form.getElementsByTagName('input'),
			params = {};
		
		for( var i = 0, j = cols.length; i < j; i ++) {
			if (!includeBtn && $F.inArray(['button', 'submit', 'reset'], cols[i].type)) {
				continue ;	
			}
			if (cols[i].name) {
				params[cols[i].name] = cols[i].value;
			}
		}
		
		cols = form.getElementsByTagName('select');
		for (var i = 0, j = cols.length; i < j; i++) {
			if (cols[i].name) {
				params[cols[i].name] = cols[i].value;
			}	
		}
		return this.make(params);
	}
}