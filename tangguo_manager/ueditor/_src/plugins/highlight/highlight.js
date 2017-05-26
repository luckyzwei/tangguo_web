///import core
///import commands/inserthtml.js
///commands 插入代码
(function(){
    Array.prototype.contains = function(key,isCase){
        for(var i=0;i<this.length;i++){
            if(isCase && this[i] == key){
                return this[i];
            }else if(this[i].toLowerCase() == key.toLowerCase()){
                return this[i].toLowerCase();
            }
        }
        return false;
    }
    //补空格
    function addSpace(linenum){
        if(linenum<10){
            return "&nbsp;&nbsp;";
        }else if(linenum>=10 && linenum<100){
            return "&nbsp;";
        }else if(linenum>=100 && linenum<1000){
            return "";
        }
    }
    //给对象绑定属性
    function CLASS_HIGHLIGHT(code,syntax){
        //获得需要转换的代码
        this._codetxt = code;
        switch(syntax && syntax.toLowerCase()){
            case "sql":
                //是否大小写敏感
                this._caseSensitive = false;
                //得到关键字哈希表
                this._keywords = "COMMIT,DELETE,INSERT,LOCK,ROLLBACK,SELECT,TRANSACTION,READ,ONLY,WRITE,USE,ROLLBACK,SEGMENT,ROLE,EXCEPT,NONE,UPDATE,DUAL,WORK,COMMENT,FORCE,FROM,WHERE,INTO,VALUES,ROW,SHARE,MODE,EXCLUSIVE,UPDATE,ROW,NOWAIT,TO,SAVEPOINT,UNION,UNION,ALL,INTERSECT,MINUS,START,WITH,CONNECT,BY,GROUP,HAVING,ORDER,UPDATE,NOWAIT,IDENTIFIED,SET,DROP,PACKAGE,CREATE,REPLACE,PROCEDURE,FUNCTION,TABLE,RETURN,AS,BEGIN,DECLARE,END,IF,THEN,ELSIF,ELSE,WHILE,CURSOR,EXCEPTION,WHEN,OTHERS,NO_DATA_FOUND,TOO_MANY_ROWS,CURSOR_ALREADY_OPENED,FOR,LOOP,IN,OUT,TYPE,OF,INDEX,BINARY_INTEGER,RAISE,ROWTYPE,VARCHAR2,NUMBER,LONG,DATE,RAW,LONG RAW,CHAR,INTEGER,MLSLABEL,CURRENT,OF,DEFAULT,CURRVAL,NEXTVAL,LEVEL,ROWID,ROWNUM,DISTINCT,ALL,LIKE,IS,NOT,NULL,BETWEEN,ANY,AND,OR,EXISTS,ASC,DESC,ABS,CEIL,COS,COSH,EXP,FLOOR,LN,LOG,MOD,POWER,ROUND,SIGN,SIN,SINH,SQRT,TAN,TANH,TRUNC,CHR,CONCAT,INITCAP,LOWER,LPAD,LTRIM,NLS_INITCAP,NLS_LOWER,NLS_UPPER,REPLACE,RPAD,RTRIM,SOUNDEX,SUBSTR,SUBSTRB,TRANSLATE,UPPER,ASCII,INSTR,INSTRB,LENGTH,LENGTHB,NLSSORT,ADD_MONTHS,LAST_DAY,MONTHS_BETWEEN,NEW_TIME,NEXT_DAY,ROUND,SYSDATE,TRUNC,CHARTOROWID,CONVERT,HEXTORAW,RAWTOHEX,ROWIDTOCHAR,TO_CHAR,TO_DATE,TO_LABEL,TO_MULTI_BYTE,TO_NUMBER,TO_SINGLE_BYTE,DUMP,GREATEST,GREATEST_LB,LEAST,LEAST_UB,NVL,UID,USER,USERENV,VSIZE,AVG,COUNT,GLB,LUB,MAX,MIN,STDDEV,SUM,VARIANCE".split(",");
                //得到内建对象哈希表
                this._commonObjects = [""];
                //标记
                this._tags = [""];
                //得到分割字符
                this._wordDelimiters = "　 ,.?!;:\\/<>(){}[]\"'\r\n\t=+-|*%@#$^&";
                //引用字符
                this._quotation = ["'"];
                //行注释字符
                this._lineComment = "--";
                //转义字符
                this._escape = "";
                //多行引用开始
                this._commentOn = "/*";
                //多行引用结束
                this._commentOff = "*/";
                //忽略词
                this._ignore = "";
                //是否处理标记
                this._dealTag = false;
                break;
            case "c#":
                //是否大小写敏感
                this._caseSensitive = true;
                //得到关键字哈希表
                this._keywords = "abstract,as,base,bool,break,byte,case,catch,char,checked,class,const,continue,decimal,default,delegate,do,double,else,enum,event,explicit,extern,false,finally,fixed,float,for,foreach,get,goto,if,implicit,in,int,interface,internal,is,lock,long,namespace,new,null,object,operator,out,override,params,private,protected,public,readonly,ref,return,sbyte,sealed,short,sizeof,stackalloc,static,set,string,struct,switch,this,throw,true,try,typeof,uint,ulong,unchecked,unsafe,ushort,using,value,virtual,void,volatile,while".split(",");
                //得到内建对象哈希表
                this._commonObjects = "String,Boolean,DateTime,Int32,Int64,Exception,DataTable,DataReader".split(",");
                //标记
                this._tags = [""];
                //得到分割字符
                this._wordDelimiters = "　 ,.?!;:\\/<>(){}[]\"'\r\n\t=+-|*%@#$^&";
                //引用字符
                this._quotation = ["\""];
                //行注释字符
                this._lineComment = "//";
                //转义字符
                this._escape = "\\";
                //多行引用开始
                this._commentOn = "/*";
                //多行引用结束
                this._commentOff = "*/";
                //忽略词
                this._ignore = "";
                //是否处理标记
                this._dealTag = false;
                break;
            case "java":
                //是否大小写敏感
                this._caseSensitive = true;
                //得到关键字哈希表
                this._keywords = "abstract,boolean,break,byte,case,catch,char,class,const,continue,default,do,double,else,extends,final,finally,float,for,goto,if,implements,import,instanceof,int,interface,long,native,new,package,private,protected,public,return,short,static,strictfp,super,switch,synchronized,this,throw,throws,transient,try,void,volatile,while".split(",");
                //得到内建对象哈希表
                this._commonObjects = "String,Boolean,DateTime,Int32,Int64,Exception,DataTable,DataReader".split(",");
                //标记
                this._tags = [""];
                //得到分割字符
                this._wordDelimiters = "　 ,.?!;:\\/<>(){}[]\"'\r\n\t=+-|*%@#$^&";
                //引用字符
                this._quotation = ["\""];
                //行注释字符
                this._lineComment = "//";
                //转义字符
                this._escape = "\\";
                //多行引用开始
                this._commentOn = "/*";
                //多行引用结束
                this._commentOff = "*/";
                //忽略词
                this._ignore = "";
                //是否处理标记
                this._dealTag = false;
                break;
            case "vbs":
            case "vb":
                //是否大小写敏感
                this._caseSensitive = false;
                //得到关键字哈希表
                this._keywords = "And,ByRef,ByVal,Call,Case,Class,Const,Dim,Do,Each,Else,ElseIf,Empty,End,Eqv,Erase,Error,Exit,Explicit,False,For,Function,Get,If,Imp,In,Is,Let,Loop,Mod,Next,Not,Nothing,Null,On,Option,Or,Private,Property,Public,Randomize,ReDim,Resume,Select,Set,Step,Sub,Then,To,True,Until,Wend,While,Xor,Anchor,Array,Asc,Atn,CBool,CByte,CCur,CDate,CDbl,Chr,CInt,CLng,Cos,CreateObject,CSng,CStr,Date,DateAdd,DateDiff,DatePart,DateSerial,DateValue,Day,Dictionary,Document,Element,Err,Exp,FileSystemObject,Filter,Fix,Int,Form,FormatCurrency,FormatDateTime,FormatNumber,FormatPercent,GetObject,Hex,Hour,InputBox,InStr,InstrRev,IsArray,IsDate,IsEmpty,IsNull,IsNumeric,IsObject,Join,LBound,LCase,Left,Len,Link,LoadPicture,Location,Log,LTrim,RTrim,Trim,Mid,Minute,Month,MonthName,MsgBox,Navigator,Now,Oct,Replace,Right,Rnd,Round,ScriptEngine,ScriptEngineBuildVersion,ScriptEngineMajorVersion,ScriptEngineMinorVersion,Second,Sgn,Sin,Space,Split,Sqr,StrComp,String,StrReverse,Tan,Time,TextStream,TimeSerial,TimeValue,TypeName,UBound,UCase,VarType,Weekday,WeekDayName,Year".split(",");
                //得到内建对象哈希表
                this._commonObjects = "String,Number,Boolean,Date,Integert,Long,Double,Single".split(",");
                //标记
                this._tags = [""];
                //得到分割字符
                this._wordDelimiters = "　 ,.?!;:\\/<>(){}[]\"'\r\n\t=+-|*%@#$^&";
                //引用字符
                this._quotation = ["\""];
                //行注释字符
                this._lineComment = "'";
                //转义字符
                this._escape = "";
                //多行引用开始
                this._commentOn = "";
                //多行引用结束
                this._commentOff = "";
                //忽略词
                this._ignore = "<!--";
                //是否处理标记
                this._dealTag = false;
                break;
            case "javascript":
                //是否大小写敏感
                this._caseSensitive = true;
                //得到关键字哈希表
                this._keywords = "function,void,this,boolean,while,if,return,new,true,false,try,catch,throw,null,else,int,long,do,var".split(",");
                //得到内建对象哈希表
                this._commonObjects = "String,Number,Boolean,RegExp,Error,Math,Date".split(",");
                //标记
                this._tags = [""];
                //得到分割字符
                this._wordDelimiters = "　 ,.?!;:\\/<>(){}[]\"'\r\n\t=+-|*%@#$^&";
                //引用字符
                this._quotation = ["\"","'"];
                //行注释字符
                this._lineComment = "//";
                //转义字符
                this._escape = "\\";
                //多行引用开始
                this._commentOn = "/*";
                //多行引用结束
                this._commentOff = "*/";
                //忽略词
                this._ignore = "<!--";
                break;
            case "css":
            case "html":
                //是否大小写敏感
                this._caseSensitive = true;
                //得到关键字哈希表
                this._keywords = "function,void,this,boolean,while,if,return,new,true,false,try,catch,throw,null,else,int,long,do,var".split(",");
                //得到内建对象哈希表
                this._commonObjects = "String,Number,Boolean,RegExp,Error,Math,Date".split(",");
                //标记
                this._tags = "html,head,body,title,style,script,language,input,select,div,span,button,img,iframe,frame,frameset,table,tr,td,caption,form,font,meta,textarea".split(",");
                //得到分割字符
                this._wordDelimiters = "　 ,.?!;:\\/>(){}[]\"'\r\n\t+-|*%@#$^&";
                //引用字符
                this._quotation = ["\"","'"];
                //行注释字符
                this._lineComment = "//";
                //转义字符
                this._escape = "\\";
                //多行引用开始
                this._commentOn = "/*";
                //多行引用结束
                this._commentOff = "*/";
                //忽略词
                this._ignore = "<!--";
                //是否处理标记
                this._dealTag = true;
                break;
            case "xml":
            default:
                //是否大小写敏感
                this._caseSensitive = true;
                //得到关键字哈希表
                this._keywords = "!DOCTYPE,?xml,script,version,encoding".split(",");
                //得到内建对象哈希表
                this._commonObjects = [""];
                //标记
                this._tags = [""];
                //得到分割字符
                this._wordDelimiters = "　 ,.;:\\/<>(){}[]\"'\r\n\t=+-|*%@#$^&";
                //引用字符
                this._quotation = ["\"","'"];
                //行注释字符
                this._lineComment = "";
                //转义字符
                this._escape = "\\";
                //多行引用开始
                this._commentOn = "<!--";
                //多行引用结束
                this._commentOff = "-->";
                //忽略词
                this._ignore = "<!--";
                //是否处理标记
                this._dealTag = true;
                break;
        }
        this.highlight = function() {
            var codeArr = new Array();
            var word_index = 0;
            var htmlTxt = new Array();
            //得到分割字符数组(分词)
            for (var i = 0; i < this._codetxt.length; i++) {
                if (this._wordDelimiters.indexOf(this._codetxt.charAt(i)) == -1) {        //找不到关键字
                    if (!codeArr[word_index]) {
                        codeArr[word_index] = "";
                    }
                    codeArr[word_index] += this._codetxt.charAt(i);
                } else {
                    if (codeArr[word_index])
                        word_index++;
                    codeArr[word_index++] = this._codetxt.charAt(i);
                }
            }
            var quote_opened = false;                 //引用标记
            var slash_star_comment_opened = false;    //多行注释标记
            var slash_slash_comment_opened = false;   //单行注释标记
            var line_num = 1;                         //行号
            var quote_char = "";                      //引用标记类型
            var tag_opened = false;                   //标记开始

            htmlTxt[htmlTxt.length] = '<span style=" text-align: right;padding:2px 10px  0;border-right:5px solid #ccc;margin:-2px 10px 0 0;color:#000;">' + line_num + '.' + addSpace(line_num) + '</span>';
            //按分割字，分块显示
            for (var i=0; i <=word_index; i++){
                //处理空行（由于转义带来）
                if(typeof(codeArr[i])=="undefined"||codeArr[i].length==0){
                    continue;
                }
                //处理空格
                if (codeArr[i] == " "){
                    htmlTxt[htmlTxt.length] = ("&nbsp;");
                //处理关键字
                } else if (!slash_slash_comment_opened&&!slash_star_comment_opened && !quote_opened && this.isKeyword(codeArr[i])){
                    htmlTxt[htmlTxt.length] = ("<span style='color:#0000FF;'>" + codeArr[i] + "</span>");
                //处理普通对象
                } else if (!slash_slash_comment_opened&&!slash_star_comment_opened && !quote_opened && this.isCommonObject(codeArr[i])){
                    htmlTxt[htmlTxt.length] = ("<span style='color:#808000;'>" + codeArr[i] + "</span>");
                //处理标记
                } else if (!slash_slash_comment_opened&&!slash_star_comment_opened && !quote_opened && tag_opened && this.isTag(codeArr[i])){
                    htmlTxt[htmlTxt.length] = ("<span style='color:#0000FF;'>" + codeArr[i] + "</span>");
                //处理换行
                } else if (codeArr[i] == "\n"){
                    if (slash_slash_comment_opened){
                        htmlTxt[htmlTxt.length] = ("</span>");
                        slash_slash_comment_opened = false;
                    }
                    line_num++;
                    htmlTxt[htmlTxt.length] = ('<br/><span style="text-align: right;padding:4px 10px  0;border-right:5px solid #ccc;margin:-5px 10px 0 0;color:#000;">' + line_num + '.' + addSpace(line_num) + '</span>');

                //处理双引号（引号前不能为转义字符）
                } else if (this._quotation.contains(codeArr[i])&&!slash_star_comment_opened&&!slash_slash_comment_opened){
                    if (quote_opened){
                        //是相应的引号
                        if(quote_char==codeArr[i]){
                            if(tag_opened){
                                htmlTxt[htmlTxt.length] = (codeArr[i]+"</span><span style='color:#808000;'>");
                            } else {
                                htmlTxt[htmlTxt.length] = (codeArr[i]+"</span>");
                            }
                            quote_opened = false;
                            quote_char = "";
                        } else {
                            htmlTxt[htmlTxt.length] = codeArr[i].replace(/\</g,"&lt;");
                        }
                    } else {
                        if(tag_opened){
                            htmlTxt[htmlTxt.length] = ("</span><span style='color:#FF00FF;'>"+codeArr[i]);
                        } else {
                            htmlTxt[htmlTxt.length] = ("<span style='color:#FF00FF;'>"+codeArr[i]);
                        }
                        quote_opened = true;
                        quote_char = codeArr[i];
                    }
                //处理转义字符
                } else if(codeArr[i] == this._escape){
                    htmlTxt[htmlTxt.length] = (codeArr[i]);
                    if(i<word_index-1){
                        if(codeArr[i+1].charCodeAt(0)>=32&&codeArr[i+1].charCodeAt(0)<=127){
                            htmlTxt[htmlTxt.length] = codeArr[i+1].substr(0,1).replace("&","&amp;").replace(/\</g,"&lt;");
                            codeArr[i+1] = codeArr[i+1].substr(1);
                        }
                    }
                //处理Tab
                } else if (codeArr[i] == "\t") {
                    htmlTxt[htmlTxt.length] = ("&nbsp;&nbsp;&nbsp;&nbsp;");
                //处理多行注释的开始
                } else if (this.isStartWith(this._commentOn,codeArr,i)&&!slash_slash_comment_opened && !slash_star_comment_opened&&!quote_opened){
                    slash_star_comment_opened = true;
                    htmlTxt[htmlTxt.length] = ("<span style='color:#008000;'>" + this._commentOn.replace(/\</g,"&lt;"));
                    i = i + this._commentOn.length-1;
                //处理单行注释
                } else if (this.isStartWith(this._lineComment,codeArr,i)&&!slash_slash_comment_opened && !slash_star_comment_opened&&!quote_opened){
                    slash_slash_comment_opened = true;
                    htmlTxt[htmlTxt.length] = ("<span style='color:#008000;'>" + this._lineComment);
                    i = i + this._lineComment.length-1;
                //处理忽略词
                } else if (this.isStartWith(this._ignore,codeArr,i)&&!slash_slash_comment_opened && !slash_star_comment_opened&&!quote_opened){
                    slash_slash_comment_opened = true;
                    htmlTxt[htmlTxt.length] = ("<span style='color:#008000;'>" + this._ignore.replace(/\</g,"&lt;"));
                    i = i + this._ignore.length-1;
                //处理多行注释结束
                } else if (this.isStartWith(this._commentOff,codeArr,i)&&!quote_opened&&!slash_slash_comment_opened){
                    if (slash_star_comment_opened) {
                        slash_star_comment_opened = false;
                        htmlTxt[htmlTxt.length] = (this._commentOff +"</span>");
                        i = i + this._commentOff.length-1;
                    }
                //处理左标记
                } else if (this._dealTag&&!slash_slash_comment_opened && !slash_star_comment_opened&&!quote_opened&&codeArr[i] == "<") {
                    htmlTxt[htmlTxt.length] = "&lt;<span style='color:#808000;'>";
                    tag_opened = true;
                //处理右标记
                } else if (this._dealTag&&tag_opened&&codeArr[i] == ">") {
                    htmlTxt[htmlTxt.length] = "</span>&gt;";
                    tag_opened = false;
                //处理HTML转义符号
                } else if (codeArr[i] == "&") {
                    htmlTxt[htmlTxt.length] = "&amp;";
                } else {
                    htmlTxt[htmlTxt.length] = codeArr[i].replace(/</g,"&lt;");
                }
            }
            htmlTxt[htmlTxt.length] = ("");
            this._codetxt = htmlTxt.join("");
        }
        this.isStartWith = function(str,code,index){
            if(str){
                for(var i=0;i<str.length;i++){
                    if(this._caseSensitive){
                        if(str.charAt(i)!=code[index+i]||(index+i>=code.length)){
                            return false;
                        }
                    } else {
                        if(str.charAt(i).toLowerCase()!=code[index+i].toLowerCase()||(index+i>=code.length)){
                            return false;
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        }
        this.isKeyword = function(val) {
            return this._keywords.contains(val,this._caseSensitive);
        }
        this.isCommonObject = function(val) {
            return this._commonObjects.contains(val,this._caseSensitive);
        }
        this.isTag = function(val) {
            return this._tags.contains(val);
        }
        this.transform = function(){
            this._codetxt = this._codetxt.replace(/&nbsp;/ig," ").replace(/<br\/>|<br>/ig,"\n").replace(/<[^>]*>/ig,"").replace(/&lt;/ig,"<").replace(/&gt;/ig,">").replace(/&amp;/ig,"&").replace(/([0-9]+\.\s*)/ig,function($1){
                 var arr = $1.split(".");
                 if(arr[0]<10){
                     return arr[1].replace(/\s{2}/,"");
                 }else if(arr[0]<100){
                     return arr[1].replace(/\s{1}/,"");
                 }else{
                     return arr[1];
                 }
            });
        }
    }
    baidu.editor.plugins['highlight'] = function() {
        var me = this,domUtils = baidu.editor.dom.domUtils;
        me.commands['highlightcode'] = {
            execCommand: function (cmdName, code, syntax) {
                if(code && syntax){
                    var highlight = new CLASS_HIGHLIGHT(code,syntax);
                    highlight.highlight();
                    me.execCommand('inserthtml', "<pre _syntax='"+syntax+"'>"+highlight._codetxt+"</pre>");
                    for(var i=0,pr,pres = domUtils.getElementsByTagName(me.document,"pre");pr=pres[i++];){
                        pr.style.overflowX = "auto";
                    }
                }else{
                    var range = this.selection.getRange(),
                       start = domUtils.findParentByTagName(range.startContainer, 'pre', true),
                       end = domUtils.findParentByTagName(range.endContainer, 'pre', true);
                    if(start && end && start === end){
                        if(domUtils.isBody(start.parentNode)){
                            var p = me.document.createElement('p');
                            p.innerHTML = baidu.editor.browser.ie ? '' : '<br/>';
                            me.body.insertBefore(p,start);
                            range.setStart(p,0)
                        }else{
                            range.setStartBefore(start)
                        }
                        range.setCursor();
                        domUtils.remove(start);
                    }
                }

            },
            queryCommandState: function(){
                 var range = this.selection.getRange(),
                    start = domUtils.findParentByTagName(range.startContainer, 'pre', true),
                    end =  domUtils.findParentByTagName(range.endContainer, 'pre', true);
                return start && end && start === end ? 0 : -1;
            }
        };
        me.addListener("beforegetcontent",function(){
            for(var i=0,pr,pres = domUtils.getElementsByTagName(me.document,"pre");pr=pres[i++];){
                var highlight = new CLASS_HIGHLIGHT(pr.innerHTML.replace(/\r\n/ig,""));
                highlight.transform();
                pr.innerHTML = "";
                pr.appendChild(me.document.createTextNode(highlight._codetxt));
            }
        });
        me.addListener("aftersetcontent",function(){
            for(var i=0,pr,pres = domUtils.getElementsByTagName(me.document,"pre");pr=pres[i++];){
                var highlight = new CLASS_HIGHLIGHT(pr.innerHTML,pr.getAttribute("_syntax"));
                highlight.transform();
                highlight.highlight();
                pr.innerHTML = highlight._codetxt;
                pr.style.overflowX = "auto";
            }
        })
    };
})();
