///import editor.js
///import core/utils.js
///import core/EventBase.js
///import core/browser.js
///import core/dom/dom.js
///import core/dom/domUtils.js
///import core/dom/Selection.js
///import core/dom/dtd.js
(function () {
    baidu.editor.Editor = Editor;

    var editor = baidu.editor,
        utils = editor.utils,
        EventBase = editor.EventBase,
        domUtils = editor.dom.domUtils,
        Selection = editor.dom.Selection,
        ie = editor.browser.ie,
        uid = 0,
        browser = editor.browser,
        dtd = editor.dom.dtd;

    /**
     * 编辑器类
     * @public
     * @class
     * @extends baidu.editor.EventBase
     * @name baidu.editor.Editor
     * @param {Object} options
     * @config {String}         id     将编辑器渲染到容器的id
     * @config {String}         initialStyle     编辑器内部样式
     * @config {String}         initialContent   初始化编辑器的内容
     * @config {String}         iframeCssUrl   要引入css的url
     * @config {String}         removeFormatTags   清除格式删除的标签
     * @config {String}         removeFormatAttributes   清除格式删除的属性
     * @config {String}         enterTag   编辑器回车标签。p或br
     * @config {Number}         maxUndoCount   最多可以回退的次数
     * @config {Number}         maxInputCount   当输入的字符数超过该值时，保存一次现场
     * @config {String}         selectedTdClass   设定选中td的样式名称
     * @config {Boolean}         pasteplain   是否纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
     * @config {String}         textarea   提交表单时，服务器获取编辑器提交内容的所用的参数
     * @config {Boolean}         focus   初始化时，是否让编辑器获得焦点true或false
     * @config {String}         indentValue   初始化时，首行缩进距离
     */
    function Editor( options ) {
        var me = this;
        me.uid = uid ++;
        EventBase.call( me );
        me.commands = {};
        me.options = utils.extend( options || {}, UEDITOR_CONFIG, true );
        me.initPlugins();
    }

   
    Editor.prototype = /**@lends baidu.editor.Editor.prototype*/{

        /**
         * 渲染编辑器的DOM到指定容器，必须且只能调用一次
         * @public
         * @function
         * @param {Element|String} container
         */
        render : function ( container ) {
            if (container.constructor === String) {
                container = document.getElementById(container);
            }
            var iframeId = 'baidu_editor_' + this.uid;
            container.innerHTML = '<iframe id="' + iframeId + '"' +
             'width="100%" height="100%" scroll="no" frameborder="0"></iframe>';
            // firefox4 getComputedStyle不能在domReady之前使用
//            if (domUtils.getComputedStyle(container, 'position') != 'absolute') {
//                container.style.position = 'relative';
//            }
            container.style.overflow = 'hidden';
            var iframe = document.getElementById( iframeId ),
                doc = iframe.contentWindow.document;
            this._setup( doc );
        },

        _setup: function ( doc ) {
            var options = this.options,
                me = this;
            doc.open();
            var html = ( ie ? '' : '<!DOCTYPE html>' ) +
                '<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title>' +
                ( options.iframeCssUrl ? '<link rel="stylesheet" type="text/css" href="' + utils.unhtml( options.iframeCssUrl ) + '"/>' : '' ) +
                '<style type="text/css">' +
                ( ie && browser.version < 9 ? 'body' : 'html' ) + '{ word-wrap: break-word;word-break: break-all;cursor: text; height: 100%; font-size:'+this.options.defaultFontSize+';font-family:'+this.options.defaultFontFamily+';}\n' + ( options.initialStyle ||' ' ) +
                '</style></head><body></body></html>';
            doc.write( html );
            doc.close();
            if ( ie ) {
                doc.body.disabled = true;
                doc.body.contentEditable = true;
                doc.body.disabled = false;
            } else {
                doc.body.contentEditable = true;
                doc.body.spellcheck = false;
            }
            this.document = doc;
            this.window = doc.defaultView || doc.parentWindow;

            this.iframe = this.window.frameElement;
            if (this.options.minFrameHeight) {
                this.setHeight(this.options.minFrameHeight);
            }
            this.body = doc.body;
            this.selection = new Selection( doc );
            this._initEvents();
            if(this.options.initialContent){
                if(me.options.autoClearinitialContent){
                    var oldExecCommand = me.execCommand;
                    me.execCommand = function(){
                        me.fireEvent('firstBeforeExecCommand');
                        oldExecCommand.apply(me,arguments)
                    };
                    this.setDefaultContent(this.options.initialContent);
                }else
                    this.setContent(this.options.initialContent);
            }
            //为form提交提供一个隐藏的textarea
              for(var form = this.iframe.parentNode;!domUtils.isBody(form);form = form.parentNode){
                    
                    if(form.tagName == 'FORM'){
                        domUtils.on(form,'submit',function(){
                            
                            var textarea = document.getElementById('ueditor_textarea_' + me.options.textarea);
                            
                            if(!textarea){
                                textarea = document.createElement('textarea');
                                textarea.setAttribute('name',me.options.textarea);
                                textarea.id = 'ueditor_textarea_' + me.options.textarea;
                                textarea.style.display = 'none';
                                this.appendChild(textarea);
                            }
                            textarea.value = me.getContent();
    
                        });
                        break;
                    }
                }
            //编辑器不能为空内容
            
            if(domUtils.getChildCount(this.body,function(node){return !domUtils.isBr(node)}) == 0){
                this.body.innerHTML = '<p>'+(browser.ie?'':'<br/>')+'</p>';
            }
            //如果要求focus, 就把光标定位到内容的最后
            if(me.options.focus){
                
                var range = this.selection.getRange(),
                    first = this.body.firstChild;

             
                setTimeout(function(){
                    range.setStartBefore(first).setCursor(false,true);
                    me._selectionChange()
                });


            }

            
            this.fireEvent( 'ready' );


        },
        /**
         * 创建textarea,同步编辑的内容到textarea,为后台获取内容做准备
         * @public
         * @function
         */

        sync : function(){
             for(var form = this.iframe.parentNode;!domUtils.isBody(form);form = form.parentNode){
                if(form.tagName == 'FORM'){

                        var textarea = document.getElementById('ueditor_textarea_' + this.options.textarea);

                        if(!textarea){
                            textarea = document.createElement('textarea');
                            textarea.setAttribute('name',this.options.textarea);
                            textarea.id = 'ueditor_textarea_' + this.options.textarea;
                            textarea.style.display = 'none';
                            form.appendChild(textarea);
                        }
                        textarea.value = this.getContent();

                    break;
                }
            }
        },
        /**
         * 设置编辑器高度
         * @public
         * @function
         * @param {Number} height    高度
         */
        setHeight: function (height){
            if (height !== parseInt(this.iframe.parentNode.style.height)){
                this.iframe.parentNode.style.height = height + 'px';
            }
        },

        /**
         * 获取编辑器内容
         * @public
         * @function
         * @returns {String}
         */
        getContent : function () {
            this.fireEvent( 'beforegetcontent' );
            var reg = new RegExp( domUtils.fillChar, 'g' ),
                html = this.document.body.innerHTML.replace(reg,'');
            this.fireEvent( 'aftergetcontent' );
            if (this.serialize) {
                var node = this.serialize.parseHTML(html);
                node = this.serialize.transformOutput(node);
                html = this.serialize.toHTML(node);
            }
            return html;
        },

        /**
         * 获取编辑器中的文本内容
         * @public
         * @function
         * @returns {String}
         */
        getContentTxt : function(){
            var reg,space_str;
            space_str = browser.ie ? "\r\t\n":"&#nbsp;\r\t\n";
            reg = new RegExp( '['+ space_str + domUtils.fillChar + ']', 'g' );
            return this.document.body[browser.ie ? 'innerText':'textContent'].replace(reg,'')
        },
        /**
         * 设置编辑器内容
         * @public
         * @function
         * @param {String} html
         */
        setContent : function ( html ) {
            var me = this;
            me.fireEvent( 'beforesetcontent' );
            var serialize = this.serialize;
            if (serialize) {
                var node = serialize.parseHTML(html);
                node = serialize.transformInput(node);
                node = serialize.filter(node);
                html = serialize.toHTML(node);
            }
            this.document.body.innerHTML = html;
            //给文本或者inline节点套p标签
            if(me.options.enterTag == 'p'){
                var child = this.body.firstChild,
                    p = me.document.createElement('p'),
                    tmpNode;

                while(child){
                    if(child.nodeType ==3 || child.nodeType == 1 && dtd.p[child.tagName]){
                        tmpNode = child.nextSibling;

                        p.appendChild(child);
                        child = tmpNode;
                        if(!child){
                            me.body.appendChild(p)
                        }
                    }else{
                        if(p.firstChild){
                            me.body.insertBefore(p,child);
                            p = me.document.createElement('p')

                            
                        }
                        child = child.nextSibling
                    }


                }   
            }
            me.adjustTable && me.adjustTable(me.body);
            me.fireEvent( 'aftersetcontent' );
            me._selectionChange();
        },

        /**
         * 让编辑器获得焦点
         * @public
         * @function
         */
        focus : function () {

            domUtils.getWindow( this.document ).focus();

        },

        /**
         * 加载插件
         * @private
         * @function
         * @param {Array} plugins
         */
        initPlugins : function ( plugins ) {
            var fn,originals = baidu.editor.plugins;
            if ( plugins ) {
                for ( var i = 0,pi; pi = plugins[i++]; ) {
                    if ( utils.indexOf( this.options.plugins, pi ) == -1 && (fn = baidu.editor.plugins[pi]) ) {
                        this.options.plugins.push( pi );
                        fn.call( this )
                    }
                }
            } else {

                plugins = this.options.plugins;

                if ( plugins ) {
                    for ( i = 0; pi = originals[plugins[i++]]; ) {
                        pi.call( this )
                    }
                } else {
                    this.options.plugins = [];
                    for ( pi in originals ) {
                        this.options.plugins.push( pi );
                        originals[pi].call( this )
                    }
                }
            }


        },
         /**
         * 初始化事件，绑定selectionchange
         * @private
         * @function
         */
        _initEvents : function () {
            var me = this,
                doc = this.document,
                win = domUtils.getWindow( doc );

           // var _selectionChange = utils.defer( utils.bind( me._selectionChange, me ), 250, true );
            me._proxyDomEvent = utils.bind( me._proxyDomEvent, me );
            domUtils.on( doc, ['click',  'contextmenu','mousedown','keydown', 'keyup','keypress', 'mouseup', 'mouseover', 'mouseout', 'selectstart'], me._proxyDomEvent );

            domUtils.on( win, ['focus', 'blur'], me._proxyDomEvent );

            domUtils.on( doc, ['mouseup','keydown'], function(evt){

                //特殊键不触发selectionchange
                if(evt.type == 'keydown' && (evt.ctrlKey || evt.metaKey || evt.shiftKey || evt.altKey)){
                    return;
                }
                if(evt.button == 2)return;
                me._selectionChange(250, true);
            });

             //处理拖拽
            //ie ff不能从外边拖入
            //chrome只针对从外边拖入的内容过滤
            var innerDrag = 0,source = browser.ie ? me.body : me.document,dragoverHandler;

            domUtils.on(source,'dragstart',function(){
                innerDrag = 1;
            });

            domUtils.on(source,browser.webkit ? 'dragover' : 'drop',function(){
                return browser.webkit ?
                    function(){
                        clearTimeout( dragoverHandler );
                        dragoverHandler = setTimeout( function(){
                            if(!innerDrag){
                                var sel = me.selection,
                                    range = sel.getRange();
                                if(range){
                                    var common = range.getCommonAncestor();
                                    if(common && me.serialize){
                                        var f = me.serialize,
                                            node =
                                                f.filter(
                                                    f.transformInput(
                                                        f.parseHTML(
                                                            f.word(common.innerHTML)
                                                        )
                                                    )
                                                )
                                        common.innerHTML = f.toHTML(node)
                                    }

                                }
                            }
                            innerDrag = 0;
                        }, 200 );
                    } :
                    function(e){

                        if(!innerDrag){
                            e.preventDefault ? e.preventDefault() :(e.returnValue = false) ;

                        }
                        innerDrag = 0;
                    }

            }());

        },
        _proxyDomEvent: function ( evt ) {

            return this.fireEvent( evt.type.replace( /^on/, '' ), evt );
        },

        _selectionChange : function ( delay ,byUser) {

            var me = this;
            clearTimeout(this._selectionChangeTimer);
            this._selectionChangeTimer = setTimeout(function(){
                me.selection.cache();
                if ( me.selection._cachedRange && me.selection._cachedStartElement ) {
                    me.fireEvent( 'beforeselectionchange' );
                    // true代表由用户交互造成的selectionchange.
                    me.fireEvent( 'selectionchange', byUser );
                    me.selection.clear();
                }
            }, delay || 50);

        },

        _callCmdFn: function ( fnName, args ) {
            var cmdName = args[0].toLowerCase(),
                cmd, cmdFn;
            cmdFn = ( cmd = this.commands[cmdName] ) && cmd[fnName] ||
                ( cmd = baidu.editor.commands[cmdName]) && cmd[fnName];
            if ( cmd && !cmdFn && fnName == 'queryCommandState' ) {
                return false;
            } else if ( cmdFn ) {
                return cmdFn.apply( this, args );
            }
        },

        /**
         * 执行命令
         * @public
         * @function
         * @param {String} cmdName 执行的命令名
         * 
         */
        execCommand : function ( cmdName ) {
            cmdName = cmdName.toLowerCase();
            var me = this,
                result,
                cmd = me.commands[cmdName] || baidu.editor.commands[cmdName];
            if ( !cmd || !cmd.execCommand ) {
                return;
            }

            if ( !cmd.notNeedUndo && !me.__hasEnterExecCommand ) {
                me.__hasEnterExecCommand = true;
                me.fireEvent( 'beforeexeccommand', cmdName );
                result = this._callCmdFn( 'execCommand', arguments );
                me.fireEvent( 'afterexeccommand', cmdName );
                me.__hasEnterExecCommand = false;
            } else {
                result = this._callCmdFn( 'execCommand', arguments );
            }
            me._selectionChange();
            return result;
        },

        /**
         * 查询命令的状态
         * @public
         * @function
         * @param {String} cmdName 执行的命令名
         * @returns {Number|*} -1 : disabled, false : normal, true : enabled.
         * 
         */
        queryCommandState : function ( cmdName ) {
            return this._callCmdFn( 'queryCommandState', arguments );
        },

        /**
         * 查询命令的值
         * @public
         * @function
         * @param {String} cmdName 执行的命令名
         * @returns {*}
         */
        queryCommandValue : function ( cmdName ) {
            return this._callCmdFn( 'queryCommandValue', arguments );
        },
        /**
         * 检查编辑区域中是否有内容
         * @public
         * @function
         * @returns {Boolean} true 有,false 没有
         */
        hasContents : function(){
            var cont = this.body[browser.ie ? 'innerText' : 'textContent'],
                reg = new RegExp('[ \t\n\r'+domUtils.fillChar+']','g');

            return !!cont.replace(reg,'').length
        },
        /**
         * 从新设置
         * @public
         * @function
         */
        reset : function(){
            this.fireEvent('reset');
        },
        /**
         * 设置默认内容
         * @function
         * @param    {String}    cont     要存入的内容
         */
        setDefaultContent : function(){
             function clear(type){
                var me = this;
                
                if(me.document.getElementById('initContent')){
                    me.document.body.innerHTML = '<p>'+(baidu.editor.browser.ie ? '' : '<br/>')+'</p>';
                    var range = me.selection.getRange();

                    me.removeListener('firstBeforeExecCommand',clear);
                    me.removeListener('focus',clear);
                    setTimeout(function(){
                        range.setStart(me.document.body.firstChild,0).collapse(true).select(true);
                        me._selectionChange();
                    })


                }
            }
            return function (cont){
                var me = this;
                me.document.body.innerHTML = '<p id="initContent">'+cont+'</p>';
                me.addListener('firstBeforeExecCommand',clear);
                me.addListener('mousedown',clear);
            }


        }()

    };
    utils.inherits( Editor, EventBase );
})();
