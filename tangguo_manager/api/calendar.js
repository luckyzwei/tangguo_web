var showcalendar,refreshcalendar,showdiv,_cancelBubble,settime,
	yy = null,
	mm = null,
	hh = null,
	ii = null;
function $(id){return document.getElementById(id)}
(function(){
	var controlid = null,
		currdate = null,
		startdate = null,
		enddate  = null,
		currday = null,
		addtime = false,
		today = new Date(),
		lastcheckedyear = false,
		lastcheckedmonth = false,
		getposition		=	function(obj) {
			 var r = [];
			 r['x'] = obj.offsetLeft;
			 r['y'] = obj.offsetTop;
			 while(obj = obj.offsetParent) {
			 r['x'] += obj.offsetLeft;
			 r['y'] += obj.offsetTop;
			 }
			 return r;
		},	
		loadcalendar	=	function() {
			s = '';
			s += '<div id="calendar" style="display:none; position:absolute; z-index:9;" onclick="_cancelBubble(event)">';
			if ($B.browser.ie){
				s += '<iframe width="200" height="160" src="about:blank" style="position: absolute;z-index:-1;"></iframe>';
			}
			s += '<div style="width: 200px;"><table class="tableborder" cellspacing="0" cellpadding="0" width="100%" style="text-align: center">';
		///
			s += '<tr align="center" class="header"><td class="header"><a href="#" onclick="refreshcalendar(yy, mm-1);return false" title="上一月"><<</a></td><td colspan="5" style="text-align: center" class="header"><a href="#" onclick="showdiv(\'year\');_cancelBubble(event);return false" title="点击选择年份" id="year"></a>  -  <a id="month" title="点击选择月份" href="#" onclick="showdiv(\'month\');_cancelBubble(event);return false"></a></td><td class="header"><A href="#" onclick="refreshcalendar(yy, mm+1);return false" title="下一月">>></A></td></tr>';
			s += '<tr class="category"><td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td></tr>';
			for(var i = 0; i < 6; i++) {
				s += '<tr class="altbg2">';
				for(var j = 1; j <= 7; j++)
					s += "<td id=d" + (i * 7 + j) + " height=\"19\">0</td>";
				s += "</tr>";
			}
			s += '<tr id="hourminute"><td colspan="7" align="center"><input type="text" size="1" value="" id="hour" onKeyUp=\'this.value=this.value > 23 ? 23 : zerofill(this.value);controlid.value=controlid.value.replace(/\\d+(\:\\d+)/ig, this.value+"$1")\'> 点 <input type="text" size="1" value="" id="minute" onKeyUp=\'this.value=this.value > 59 ? 59 : zerofill(this.value);controlid.value=controlid.value.replace(/(\\d+\:)\\d+/ig, "$1"+this.value)\'> 分</td></tr>';
			s += '</table></div></div>';///
			s += '<div id="calendar_year" onclick="_cancelBubble(event)"><div class="col">';
			for(var k = 2011; k <= 2013; k++) {
				s += k != 2011 && k % 10 == 0 ? '</div><div class="col">' : '';
				s += '<a href="#" onclick="refreshcalendar(' + k + ', mm);$A(\'calendar_year\').style.display=\'none\';return false"><span' + (today.getFullYear() == k ? ' class="today"' : '') + ' id="calendar_year_' + k + '">' + k + '</span></a><br />';
			}
			s += '</div></div>';
			s += '<div id="calendar_month" onclick="_cancelBubble(event)">';
			for(var k = 1; k <= 12; k++) {
					s += '<a href="#" onclick="refreshcalendar(yy, ' + (k - 1) + ');$A(\'calendar_month\').style.display=\'none\';return false"><span' + (today.getMonth()+1 == k ? ' class="today"' : '') + ' id="calendar_month_' + k + '">' + k + ( k < 10 ? ' ' : '') + ' 月</span></a><br />';
			}
			s += '</div>';
			var nElement = document.createElement("div");
			nElement.innerHTML=s;
			nElement.id		=	'calendar_cont';
			document.body.appendChild(nElement);
			$E.addEvent(document.body,'click', function(event) {
				$A('calendar').style.display = 'none';
				$A('calendar_year').style.display = 'none';
				$A('calendar_month').style.display = 'none';
			});
			$A('calendar').onclick = function(event) {
				_cancelBubble(event);
				$A('calendar_year').style.display = 'none';
				$A('calendar_month').style.display = 'none';
			}
		},
		parsedate	=	function(s) {
			/(\d+)\-(\d+)\-(\d+)\s*(\d*):?(\d*)/.exec(s);
			var m1 = (RegExp.$1 && RegExp.$1 > 1899 && RegExp.$1 < 2101) ? parseFloat(RegExp.$1) : today.getFullYear();
			var m2 = (RegExp.$2 && (RegExp.$2 > 0 && RegExp.$2 < 13)) ? parseFloat(RegExp.$2) : today.getMonth() + 1;
			var m3 = (RegExp.$3 && (RegExp.$3 > 0 && RegExp.$3 < 32)) ? parseFloat(RegExp.$3) : today.getDate();
			var m4 = (RegExp.$4 && (RegExp.$4 > -1 && RegExp.$4 < 24)) ? parseFloat(RegExp.$4) : 0;
			var m5 = (RegExp.$5 && (RegExp.$5 > -1 && RegExp.$5 < 60)) ? parseFloat(RegExp.$5) : 0;
			/(\d+)\-(\d+)\-(\d+)\s*(\d*):?(\d*)/.exec("0000-00-00 00\:00");
			return new Date(m1, m2 - 1, m3, m4, m5);
		};
		settime		=	function(d){
			$A('calendar').style.display = 'none';
			controlid.value = yy + "-" + zerofill(mm + 1) + "-" + zerofill(d) + (addtime ? ' ' + zerofill($('hour').value) + ':' + zerofill($('minute').value) : '');		
		};
		_cancelBubble	=	function(event){
			e = event ? event : window.event ;
			if($B.browser.ie) {
			 e.cancelBubble = true;
			} else {
			 e.stopPropagation();
			}
		};
		refreshcalendar		=	function(y, m) {
			var x = new Date(y, m, 1);
			var mv = x.getDay();
			var d = x.getDate();
			var dd = null;
			yy = x.getFullYear();
			mm = x.getMonth();
			$A("year").innerHTML = yy;
			$A("month").innerHTML = mm + 1 > 9  ? (mm + 1) : '0' + (mm + 1);
			for(var i = 1; i <= mv; i++){
			 dd = $A("d" + i);
			 dd.innerHTML = " ";
			 dd.className = "";
			}
			while(x.getMonth() == mm) {
				 dd = $A("d" + (d + mv));
				 dd.innerHTML = '<a href="###" onclick="settime(' + d + ');return false">' + d + '</a>';
				 if(x.getTime() < today.getTime() || (enddate && x.getTime() > enddate.getTime()) || (startdate && x.getTime() < startdate.getTime())) {
					dd.className = 'expire';
				} else {
					dd.className = 'default';
				}
				if(x.getFullYear() == today.getFullYear() && x.getMonth() == today.getMonth() && x.getDate() == today.getDate()) {
					dd.className = 'today';
					dd.firstChild.title = '今天';
				}
				if(x.getFullYear() == currday.getFullYear() && x.getMonth() == currday.getMonth() && x.getDate() == currday.getDate()) {
					dd.className = 'checked';
				}
				x.setDate(++d);
			}
			while(d + mv <= 42) {
				 dd = $A("d" + (d + mv));
				 dd.innerHTML = " ";
				 d++;
			}
			if(addtime) {
				$A('hour').value = zerofill(hh);
				$A('minute').value = zerofill(ii);
			}
		},
		showdiv		=	function(id) {
			var p = getposition($A(id));
			$A('calendar_' + id).style.left = p['x']+'px';
			$A('calendar_' + id).style.top = (p['y'] + 16)+'px';
			$A('calendar_' + id).style.display = 'block';
		},
		zerofill	=	function(s) {
			var s = parseFloat(s.toString().replace(/(^[\s0]+)|(\s+$)/g, ''));
			s = isNaN(s) ? 0 : s;
			return (s < 10 ? '0' : '') + s.toString();
		};
		$B.ready(loadcalendar);
		showcalendar = function (event, controlid1, addtime1, startdate1, enddate1){
			controlid = controlid1;
			addtime = addtime1;
			startdate = startdate1 ? parsedate(startdate1) : false;
			enddate = enddate1 ? parsedate(enddate1) : false;
			currday = controlid.value ? parsedate(controlid.value) : today;
			hh = currday.getHours();
			ii = currday.getMinutes();
			var p = getposition(controlid);
			$A('calendar').style.display = 'block';
			$A('calendar').style.left = p['x']+'px';
			$A('calendar').style.top = (p['y'] + 20)+'px';
			_cancelBubble(event);
			refreshcalendar(currday.getFullYear(), currday.getMonth());
			if(lastcheckedyear != false) {
			$A('calendar_year_' + lastcheckedyear).className = 'default';
			$A('calendar_year_' + today.getFullYear()).className = 'today';
			}
			if(lastcheckedmonth != false) {
			$A('calendar_month_' + lastcheckedmonth).className = 'default';
			$A('calendar_month_' + (today.getMonth() + 1)).className = 'today';
			}
			$A('calendar_year_' + currday.getFullYear()).className = 'checked';
			$A('calendar_month_' + (currday.getMonth() + 1)).className = 'checked';
			$A('hourminute').style.display = addtime ? '' : 'none';
			lastcheckedyear = currday.getFullYear();
			lastcheckedmonth = currday.getMonth() + 1;
		}
		
})();