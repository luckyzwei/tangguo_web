document.writeln("<div id=\"xg_box\"></div>");
if(XgPlayer.Second<1){
	XgPlayer.Second=1;
}
var browser;
var installflag=1;
function $XghdInstall(){
	$$("xg_box").style.display="none";
	if(installflag==1){
	document.writeln('<iframe border="0" src="'+XgPlayer.Installpage+'" marginWidth="0" frameSpacing="0" marginHeight="0" frameBorder="0" noResize scrolling="no" width="'+XgPlayer.Width+'" height="'+XgPlayer.Height+'" vspale="0" ></iframe>');
	installflag=0;
	}
}
var AdsBeta6 = {
	'Start': function() {
		$$('buffer').style.display = 'block';
		if(xiguaPlayer.IsBuffing()){
			$$('buffer').height = XgPlayer.Height-80;
		}else{
			$$('buffer').height = XgPlayer.Height-60;
		}
	},
	'End': function() {
		if(!XgPlayer.Second){
			$$('buffer').style.display = 'none';
			$$('xiguaPlayer').style.display = 'block';
			xiguaPlayer.height = XgPlayer.Height;
		}
	},
	'Status' : function() {
		if(xiguaPlayer.IsPlaying()){
			this.End();
		}else{
			this.Start();
		}
	}
}
function $$(id){
	return document.getElementById(id);
}
function $Showhtml(){
	 browser = navigator.appName;
	if(browser == "Netscape"|| browser == "Opera"){
		if(/iPad|iPhone/i.test(navigator.userAgent))
		{
			setTimeout($PlayerIOS,1000);
		}
		if(/Android/i.test(navigator.userAgent))
		{
			$PlayerAndroid();
		}
		if(isIE()){
		return $PlayerIe();
		}else{
			return $PlayerNt();
		}
	}else if(browser == "Microsoft Internet Explorer"){
		return $PlayerIe();
	}
	else{
		alert('��ʹ��IE�ں�������ۿ���վӰƬ!');
	}	
}
    function isIE() {
        if (!!window.ActiveXObject || "ActiveXObject" in window)  {
			browser = "Microsoft Internet Explorer";
            return true;  
		}
        return false;  
    }  


function installapp(){  
		return function(){  
			var clickedAt = +new Date;  	
			setTimeout(function()
			{  
				try{if(isxg()){return;}}catch(e){;}
				  if (+new Date - clickedAt < 1500)
				  {
				    alert("����Ϊ��ת��ƻ���̵�����\"�Ϲϲ�����\"����װ�ɹ�������ˢ�±�ҳ����в���");
					setTimeout(function(){
						var surl="https://itunes.apple.com/cn/app/id1130681156";
						top.location.href=surl;
					},3000);
				  } 
			}, 500);
		};  
	}  
	
function $PlayerIOS(){
	var newurl="#";
	if(typeof(XgPlayer)!='undefined'){
		newurl = XgPlayer['Url'].replace("ftp://","xg://");
	}
	else if(typeof(Player)!='undefined'){
		newurl = Player['Url'].replace("ftp://","xg://");
	}
	var xuanjipage = top.location.href;
	if(typeof(XgPlayer['XuanJiPage'])!='undefined') xuanjipage = XgPlayer['XuanJiPage'];
	if(typeof(XgPlayer['MobiAd'])!='undefined')top.location.href = newurl+"|"+XgPlayer['MobiAd']+"|"+xuanjipage;
	else top.location.href = newurl;
	//installapp()();
}



function $PlayerAndroid(){
	var finalurl;
	var newurl="#";
	var isChrome = window.navigator.userAgent.indexOf("Chrome") !== -1;
	if(typeof(XgPlayer)!='undefined'){
                newurl = XgPlayer['Url'];
	} else if(typeof(Player)!='undefined'){
                newurl = Player['Url'];
	}
        
	var xuanjipage = top.location.href;
	if(typeof(XgPlayer['XuanJiPage'])!='undefined'){
            xuanjipage = XgPlayer['XuanJiPage'];
        }
            
	if(typeof(XgPlayer['MobiAd'])!='undefined'){
            finalurl = newurl+"|"+XgPlayer['MobiAd']+"|"+xuanjipage;
        } else{
            finalurl = newurl;
        }
        
        var array = finalurl.split("//");
        finalurl = array[1];
        
        if (typeof AlipayWallet !== 'object') {
                AlipayWallet = {};
            }

            (function () {
                var ua = navigator.userAgent.toLowerCase(),
                        locked = false,
                        domLoaded = document.readyState === 'complete',
                        delayToRun;

                function customClickEvent() {
                    var clickEvt;
                    if (window.CustomEvent) {
                        clickEvt = new window.CustomEvent('click', {
                            canBubble: true,
                            cancelable: true
                        });
                    } else {
                        clickEvt = document.createEvent('Event');
                        clickEvt.initEvent('click', true, true);
                    }

                    return clickEvt;
                }

                var noIntentTest = /aliapp|360 aphone|weibo|windvane|ucbrowser|baidubrowser/.test(ua);
                var hasIntentTest = /chrome|samsung/.test(ua);
                var isAndroid = /android|adr/.test(ua) && !(/windows phone/.test(ua));
                var canIntent = !noIntentTest && hasIntentTest && isAndroid;

                AlipayWallet.open = function (params, jumpUrl) {
                    if (!domLoaded && (ua.indexOf('360 aphone') > -1 || canIntent)) {
                        var arg = arguments;
                        delayToRun = function () {
                            AlipayWallet.open.apply(null, arg);
                            delayToRun = null;
                        };
                        return;
                    }

                    // ���������������ظ�����
                    if (locked) {
                        return;
                    }
                    locked = true;

                    var o;
                    // �����ݴ�
                    if (typeof params === 'object') {
                        o = params;
                    } else {
                        o = {
                            params: params,
                            jumpUrl: jumpUrl
                        };
                    }

                    // �Ƿ�ΪRC����
                    var isRc = '';

                    // �Ƿ���re��
                    var isRe = '';
                    if (typeof o.isRe === 'undefined') {
                        o.isRe = !!isRe;
                    }
                    var tstart=new Date();
                    // ����app��scheme
                    var schemePrefix = 'xg';

                    //��Ƶ��ַ,�滻�ɴ�����ʵ�ʵ�ַ
                    var address = finalurl;

                    if (!canIntent) {
                        var alipaysUrl = schemePrefix + '://' + address;

                        //�ٶ��������֧��xg,֧��ftpЭ��
                        var isBaidu = window.navigator.userAgent.indexOf("baidubrowser") !== -1;
                        if (isBaidu) {
                            alipaysUrl = "ftp://" + address;
                        }

                        var ifr = document.createElement('iframe');
                        ifr.src = alipaysUrl;
                        ifr.style.display = 'none';
                        document.body.appendChild(ifr);
                    } else {
                        // android �� chrome �����ͨ�� intent Э�黽��Ǯ��
                        var intentUrl = 'intent://' + address + '#Intent;scheme=' + schemePrefix + ';package=tv.danmaku.ijk.media.demo' + ';end';

                        //���¼������ܼ������а汾�Ĺȸ������,����汾�Ĺȸ��������Safari��֧�ַ�input��ǩ��click�¼�,dispatchEvent��׽����,����ʹ��<intput type="button">�ĸ�ʽ��׽click�¼�
                        var openIntentLink = document.createElement('a');
                        openIntentLink.id = 'openIntentLink';
                        openIntentLink.style.display = 'none';
                        document.body.appendChild(openIntentLink);

                        openIntentLink.href = intentUrl;
                        // ִ��click
                        openIntentLink.dispatchEvent(customClickEvent());
                    }

                    // �ӳ��Ƴ���������Ǯ����IFRAME����ת������ҳ
                    setTimeout(function () {
                        //�ض����ַ,����ȥ�����滻�ɵ������ص�������,֧������ҳ��û���ж�app�Ƿ����,�����ת������ҳ��
                        var nElapsedMS=new Date()- tstart;
                        if(window.navigator.userAgent.indexOf("baidubrowser") !== -1 && nElapsedMS>1100)
                        {//ȥ����װ�������Ժ�ٶ��������������apk�Ŀ�
                            return;
                        }
                    }, 1000)

                    // ��������������ʱ���ڱ��ظ�����
                    setTimeout(function () {
                        locked = false;
                    }, 2500)
                }

                if (!domLoaded) {
                    document.addEventListener('DOMContentLoaded', function () {
                        domLoaded = true;
                        if (typeof delayToRun === 'function') {
                            delayToRun();
                        }
                    }, false);
                }
            })();
            
            (function () {
                var schemeParam = '';

                if (!location.hash) {
                    AlipayWallet.open({
                        params: schemeParam,
                        jumpUrl: '',
                        openAppStore: false
                    });
                }
            })();
}

function $PlayerNt(){
	if (navigator.plugins) {
		var install = true;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'XiGua Yingshi Plugin'){
				install = false;break;
			}
		}
		if(!install){
			player = '<div style="width:'+XgPlayer.Width+'px;height:'+XgPlayer.Height+'px;overflow:hidden;position:relative"><iframe src="'+XgPlayer.Buffer+'" scrolling="no" width="100%" height="100%" frameborder="0" marginheight="0" marginwidth="0" name="buffer" id="buffer" style="position:absolute;z-index:2;top:0px;left:0px"></iframe><object  width="'+XgPlayer.Width+'" height="'+XgPlayer.Height+'" type="application/xgyingshi-activex" progid="xgax.player.1" param_URL="'+XgPlayer.Url+'" param_NextCacheUrl="'+XgPlayer.NextcacheUrl+'" param_LastWebPage="'+XgPlayer.LastWebPage+'" param_NextWebPage="'+XgPlayer.NextWebPage+'" param_OnPause="onPause" param_OnFirstBufferingStart="onFirstBufferingStart" param_OnFirstBufferingEnd="onFirstBufferingEnd" param_OnPlayBufferingStart="onPlayBufferingStart" param_OnPlayBufferingEnd="onPlayBufferingEnd" param_OnComplete="onComplete" param_Autoplay="1" id="xiguaPlayer" name="xiguaPlayer"></object></div>';
			if(XgPlayer.Second){
				setTimeout("onAdsEnd()",XgPlayer.Second*1000);
			}	
			return player;
		}
	}
	return '<iframe border="0" src="'+XgPlayer.Installpage+'" marginWidth="0" frameSpacing="0" marginHeight="0" frameBorder="0" noResize scrolling="no" width="'+XgPlayer.Width+'" height="'+XgPlayer.Height+'" vspale="0" ></iframe>';
}
function $PlayerIe(){
	playerhtml = '<iframe src="'+XgPlayer.Buffer+'" id="buffer" width="'+XgPlayer.Width+'" height="'+(XgPlayer.Height-80)+'" scrolling="no" frameborder="0" style="position:absolute;z-index:9;"></iframe><object classid="clsid:BEF1C903-057D-435E-8223-8EC337C7D3D0"  style="display:none" width="'+XgPlayer.Width+'" height="'+XgPlayer.Height+'" id="xiguaPlayer" name="xiguaPlayer" onerror="$XghdInstall();"><param name="URL" value="'+XgPlayer.Url+'"/><param name="NextCacheUrl" value="'+XgPlayer.NextcacheUrl+'"><param name="LastWebPage" value="'+XgPlayer.LastWebPage+'"><param name="NextWebPage" value="'+XgPlayer.NextWebPage+'"><param name="OnPlay" value="onPlay"/><param name="OnPause" value="onPause"/><param name="OnFirstBufferingStart" value="onFirstBufferingStart"/><param name="OnFirstBufferingEnd" value="onFirstBufferingEnd"/><param name="OnPlayBufferingStart" value="onPlayBufferingStart"/><param name="OnPlayBufferingEnd" value="onPlayBufferingEnd"/><param name="OnComplete" value="onComplete"/><param name="Autoplay" value="1"/></object>';
	return playerhtml;
}
function $PlayerIeBack(){
	if(browser == "Microsoft Internet Explorer"){
		if(xiguaPlayer.URL != undefined){
			if(XgPlayer.Second){
				setTimeout("onAdsEnd()",XgPlayer.Second*1000);
			}	
		}
xiguaPlayer.ConfigurePlayer('url', XgPlayer.Url);
	}
}
//beta7�沥�����ص�����
var onPlay = function(){
	$$('buffer').style.display = 'none';
	//ǿ�ƻ����浹��ʱ
	if(XgPlayer.Second&&xiguaPlayer.IsPlaying()){
		xiguaPlayer.Play();
	}
}
var onPause = function(){
	$$('buffer').height = XgPlayer.Height-63;
	$$('buffer').style.display = 'block';
}
var onFirstBufferingStart = function(){
	$$('buffer').height = Player.Height-80;
	$$('buffer').style.display = 'block';
}
var onFirstBufferingEnd = function(){
	if(XgPlayer.Second){
		xiguaPlayer.Play();
	}else{
		$$('buffer').style.display = 'none';
	}
}
var onPlayBufferingStart = function(){
	$$('buffer').height = XgPlayer.Height-80;
	$$('buffer').style.display = 'block';
}
var onPlayBufferingEnd = function(){
	$$('buffer').style.display = 'none';
}
var onComplete = function(){
	onPause();
}
var onAdsEnd = function(){
	XgPlayer.Second = 0;
	$$('buffer').style.display = 'none';
	xiguaPlayer.style.display = 'block';
	setInterval("adshow()",1000);
}
  function adshow(){
	  if(xiguaPlayer.IsPlaying()){
		$$('buffer').style.display = 'none';
	  }else if(xiguaPlayer.IsBuffing()){
		$$('buffer').height = XgPlayer.Height-63;
	    $$('buffer').style.display = 'block';
	  }else if(xiguaPlayer.IsPause()){
		$$('buffer').height = XgPlayer.Height-63;
	    $$('buffer').style.display = 'block';
	  }else{
	    $$('buffer').height = XgPlayer.Height-63;
	    $$('buffer').style.display = 'block';
	  }
  }
var install = true;
playerhtml=$Showhtml();
$$("xg_box").innerHTML=playerhtml;
$PlayerIeBack();