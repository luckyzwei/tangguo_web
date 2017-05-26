(function (){
    var utils = baidu.editor.utils;
    var editorui = baidu.editor.ui;

    var _Dialog = editorui.Dialog;
    editorui.Dialog = function (options){
        var dialog = new _Dialog(options);
        dialog.addListener('hide', function (){
            if (dialog.editor) {
                var editor = dialog.editor;
                try {
                    if(baidu.editor.browser.ie){
                        editor.selection._bakIERange.select();
                    } else {
                        editor.focus()
                    }
                } catch(ex){}
            }
        });
        return dialog;
    };

    var k, cmd;

    var btnCmds = ['Undo', 'Redo','FormatMatch',
        'Bold', 'Italic', 'Underline',
        'StrikeThrough', 'Subscript', 'Superscript','Source','Indent','Outdent',
//        'Imageleft', 'ImageRight', 'ImageChar',
        'BlockQuote','PastePlain','PageBreak',
        'SelectAll', 'Print', 'Preview', 'Horizontal', 'RemoveFormat','Time','Date','Unlink',
        'InsertParagraphBeforeTable','InsertRow','InsertCol','MergeRight','MergeDown','DeleteRow',
        'DeleteCol','SplittoRows','SplittoCols','SplittoCells','MergeCells','DeleteTable'];
    k = btnCmds.length;
    while (k --) {
        cmd = btnCmds[k];
        editorui[cmd] = function (cmd){
            return function (editor, title){
                title = title || editor.options.labelMap[cmd.toLowerCase()] || '';
                var ui = new editorui.Button({
                    className: 'edui-for-' + cmd.toLowerCase(),
                    title: title,
                    onclick: function (){
                        editor.execCommand(cmd);
                    },
                    showText: false
                });
                editor.addListener('selectionchange', function (){
                    var state = editor.queryCommandState(cmd.toLowerCase());
                    if (state == -1) {
                        ui.setDisabled(true);
                        ui.setChecked(false);
                    } else {
                        ui.setDisabled(false);
                        ui.setChecked(state);
                    }
                });
                return ui;
            };
        }(cmd);
    }
    editorui.ClearDoc = function(editor, title){
        var cmd = "ClearDoc";
        title = title || editor.options.labelMap[cmd.toLowerCase()] || '';
        var ui = new editorui.Button({
            className: 'edui-for-' + cmd.toLowerCase(),
            title: title,
            onclick: function (){
                if(confirm('确定清空文档吗？')){
                    editor.execCommand('cleardoc');
                }
            }
        });
        return ui;
    };

    editorui.Justify = function (editor, side, title){
        side = (side || 'left').toLowerCase();
        title = title || editor.options.labelMap['justify'+side.toLowerCase()] || '';
        var ui = new editorui.Button({
            className: 'edui-for-justify' + side.toLowerCase(),
            title: title,
            onclick: function (){
                editor.execCommand('Justify', side);
            }
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('Justify');
            ui.setDisabled(state == -1);
            var value = editor.queryCommandValue('Justify');
            ui.setChecked(value == side);
        });
        return ui;
    };
    editorui.JustifyLeft = function (editor, title){
        return editorui.Justify(editor, 'left', title);
    };
    editorui.JustifyCenter = function (editor, title){
        return editorui.Justify(editor, 'center', title);
    };
    editorui.JustifyRight = function (editor, title){
        return editorui.Justify(editor, 'right', title);
    };
    editorui.JustifyJustify = function (editor, title){
        return editorui.Justify(editor, 'justify', title);
    };

    editorui.Directionality = function (editor, side, title){
        side = (side || 'left').toLowerCase();
        title = title || editor.options.labelMap['directionality'+side.toLowerCase()] || '';
        var ui = new editorui.Button({
            className: 'edui-for-directionality' + side.toLowerCase(),
            title: title,
            onclick: function (){
                editor.execCommand('directionality', side);
            },
            type : side
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('directionality');
            ui.setDisabled(state == -1);
            var value = editor.queryCommandValue('directionality');
            ui.setChecked(value == ui.type);
        });
        return ui;
    };
    editorui.DirectionalityLtr = function (editor, title){
        return new editorui.Directionality(editor, 'ltr', title);
    };
    editorui.DirectionalityRtl = function (editor, title){
        return new editorui.Directionality(editor, 'rtl', title);
    };
    var colorCmds = ['BackColor', 'ForeColor'];
    k = colorCmds.length;
    while (k --) {
        cmd = colorCmds[k];
        editorui[cmd] = function (cmd){
            return function (editor, title){
                title = title || editor.options.labelMap[cmd.toLowerCase()] || '';
                var ui = new editorui.ColorButton({
                    className: 'edui-for-' + cmd.toLowerCase(),
                    color: 'default',
                    title: title,
                    onpickcolor: function (t, color){
                        editor.execCommand(cmd, color);
                    },
                    onpicknocolor: function (){
                        editor.execCommand(cmd, 'default');
                        this.setColor('transparent');
                        this.color = 'default';
                    },
                    onbuttonclick: function (){
                        editor.execCommand(cmd, this.color);
                    }
                });
                editor.addListener('selectionchange', function (){
                    var state = editor.queryCommandState(cmd);
                    if (state == -1) {
                        ui.setDisabled(true);
                    } else {
                        ui.setDisabled(false);
                    }
                });
                return ui;
            };
        }(cmd);
    }

    //不需要确定取消按钮的dialog
    var dialogNoButton = ['SearchReplace','Help','Spechars'];
    k = dialogNoButton.length;
    while(k --){
        cmd = dialogNoButton[k];
        editorui[cmd] = function (cmd){
            cmd = cmd.toLowerCase();
            return function (editor, iframeUrl, title){
                iframeUrl = iframeUrl || editor.options.iframeUrlMap[cmd.toLowerCase()] || 'about:blank';
                iframeUrl = editor.ui.mapUrl(iframeUrl);
                title = title || editor.options.labelMap[cmd.toLowerCase()] || '';
                var dialog = new editorui.Dialog({
                    iframeUrl: iframeUrl,
                    autoReset: true,
                    draggable: true,
                    editor: editor,
                    className: 'edui-for-' + cmd,
                    title: title,
                    onok: function (){},
                    oncancel: function (){},
                    onclose: function (t, ok){
                        if (ok) {
                            return this.onok();
                        } else {
                            return this.oncancel();
                        }
                    }
                });
                dialog.render();
                var ui = new editorui.Button({
                    className: 'edui-for-' + cmd,
                    title: title,
                    onclick: function (){
                        dialog.open();
                    }
                });
                editor.addListener('selectionchange', function (){
                    var state = editor.queryCommandState('inserthtml');
                    if (state == -1) {
                        ui.setDisabled(true);
                    } else {
                        ui.setDisabled(false);
                    }
                });
                return ui;
            };
        }(cmd);
    }

    var dialogCmds = ['Anchor','Link', 'Image', 'Map', 'GMap', 'Video','TableSuper','Code','InsertFrame'];
    
    k = dialogCmds.length;
    while (k --) {
        cmd = dialogCmds[k];
        editorui[cmd] = function (cmd){
            cmd = cmd.toLowerCase();
            return function (editor, iframeUrl, title){
                iframeUrl = iframeUrl || editor.options.iframeUrlMap[cmd.toLowerCase()] || 'about:blank';
                iframeUrl = editor.ui.mapUrl(iframeUrl);
                title = title || editor.options.labelMap[cmd.toLowerCase()] || '';
                var dialog = new editorui.Dialog({
                    iframeUrl: iframeUrl,
                    autoReset: true,
                    draggable: true,
                    editor: editor,
                    className: 'edui-for-' + cmd,
                    title: title,
                    buttons: [{
                        className: 'edui-okbutton',
                        label: '确认',
                        onclick: function (){
                            dialog.close(true);
                        }
                    }, {
                        className: 'edui-cancelbutton',
                        label: '取消',
                        onclick: function (){
                            dialog.close(false);
                        }
                    }],
                    onok: function (){},
                    oncancel: function (){},
                    onclose: function (t, ok){
                        if (ok) {
                            return this.onok();
                        } else {
                            return this.oncancel();
                        }
                    }
                });
                dialog.render();
                var ui = new editorui.Button({
                    className: 'edui-for-' + cmd,
                    title: title,
                    onclick: function (){
                        dialog.open();
                    }
                });
                editor.addListener('selectionchange', function (){
                    var state = editor.queryCommandState(cmd);
                    if (state == -1) {
                        ui.setDisabled(true);
                    } else {
                        ui.setDisabled(false);
                    }
                });
                return ui;
            };
        }(cmd);
    }

    editorui.FontFamily = function (editor, list, title){
        list = list || editor.options.listMap['fontfamily'] || [];
        title = title || editor.options.labelMap['fontfamily'] || '';
        var items = [];
        for (var i=0; i<list.length; i++) {
            var font = list[i];
            var fonts = editor.options.fontMap[font];
            var value = '"' + font + '"';
            var regex = new RegExp(font, 'i');
            if (fonts) {
                value = '"' + fonts.join('","') + '"';
                regex = new RegExp(fonts.join('[^\\s]|'), 'i');
            }
            items.push({
                label: font,
                value: value,
                regex: regex,
                renderLabelHtml: function (){
                    return '<div class="edui-label %%-label" style="font-family:' +
                        utils.unhtml(this.value) + '">' + (this.label || '') + '</div>';
                }
            });
        }
        var ui = new editorui.Combox({
            items: items,
            onselect: function (t,index){
                editor.execCommand('FontFamily', this.items[index].value);
            },
            onbuttonclick: function (){
                this.showPopup();
            },
            title: title,
            className: 'edui-for-fontfamily',
            indexByValue: function (value){
                value = value.replace(/,/, '|').replace(/"/g, '');
                for (var i=0; i<this.items.length; i++) {
                    var item = this.items[i];
                    if (item.regex.test(value)) {
                        return i;
                    }
                }
                return -1;
            }
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('FontFamily');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
////                if (ready) {
////                    ui.setValue(editor.options.labelMap['fontfamily'] || '字体');
////                } else {
////                    var value = editor.queryCommandValue('FontFamily');
////                    ui.setValue(value);
////                }
//                var value = editor.queryCommandValue('FontFamily');

                ui.setValue( editor.queryCommandValue('FontFamily'));
            }
        });
        return ui;
    };

    editorui.FontSize = function (editor, list, title){
        list = list || editor.options.listMap['fontsize'] || [];
        title = title || editor.options.labelMap['fontsize'] || '';
        var items = [];
        for (var i=0; i<list.length; i++) {
            var size = list[i] + 'px';
            items.push({
                label: size,
                value: size,
                renderLabelHtml: function (){
                    return '<div class="edui-label %%-label" style="font-size:' +
                        this.value + '">' + (this.label || '') + '</div>';
                }
            });
        }
        var ui = new editorui.Combox({
            items: items,
            title: title,
            onselect: function (t,index){
                editor.execCommand('FontSize', this.items[index].value);
            },
            onbuttonclick: function (){
                this.showPopup();
            },
            className: 'edui-for-fontsize'
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('FontSize');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
//                if (ready) {
//                    ui.setValue(editor.options.labelMap['fontsize'] || '字号');
//                } else {
//                    var value = editor.queryCommandValue('FontSize');
//                    ui.setValue(value);
//                }
                 var value = editor.queryCommandValue('FontSize');
                    ui.setValue(value);
            }
        });
        return ui;
    };
    editorui.RowSpacing = function (editor, list, title){
        list = list || editor.options.listMap['rowspacing'] || [];
        title = title || editor.options.labelMap['rowspacing'] || '';
        var items = [];
        for (var i=0; i<list.length; i++) {
            var item = list[i].split(':');
            var tag = item[0];
            var value = item[1];
            items.push({
                label: tag,
                value: value,
                renderLabelHtml: function (){
                    return '<div class="edui-label %%-label" style="font-size:12px">' + (this.label || '') + '</div>';
                }
            });
        }
        var ui = new editorui.Combox({
            items: items,
            title: title,
            onselect: function (t,index){
                editor.execCommand('RowSpacing', this.items[index].value);
            },
            onbuttonclick: function (){
                this.showPopup();
            },
            className: 'edui-for-rowspacing'
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('RowSpacing');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
//                if (ready) {
//                    ui.setValue(editor.options.labelMap['rowspacing'] || '行间距');
//                } else {
//                    var value = editor.queryCommandValue('RowSpacing');
//                    ui.setValue(value);
//                }
                var value = editor.queryCommandValue('RowSpacing');
                    ui.setValue(value);
            }
        });
        return ui;
    };
//    editorui.Underline = function (editor, list, title){
//        list = list || editor.options.listMap['underline'] || [];
//        title = title || editor.options.labelMap['underline'] || '';
//        var items = [];
//        for (var i=0; i<list.length; i++) {
//            var size = list[i] ;
//            items.push({
//                label: size,
//                value: size,
//                renderLabelHtml: function (){
//                    return '<div class="edui-label %%-label" style="text-decoration:' +
//                        this.value + '">' + (this.label || '') + '</div>';
//                }
//            });
//        }
//        var ui = new editorui.Combox({
//            items: items,
//            title: title,
//            onselect: function (t,index){
//                editor.execCommand('UnderLine', this.items[index].value);
//            },
//            onbuttonclick: function (){
//                this.showPopup();
//            },
//            className: 'edui-for-underline'
//        });
//        editor.addListener('selectionchange', function (){
//            var state = editor.queryCommandState('Underline');
//            if (state == -1) {
//                ui.setDisabled(true);
//            } else {
//                ui.setDisabled(false);
//                var value = editor.queryCommandValue('Underline');
//                ui.setValue(value);
//            }
//        });
//        return ui;
//    };
    editorui.Paragraph = function (editor, list, title){
        list = list || editor.options.listMap['paragraph'] || [];
        title = title || editor.options.labelMap['paragraph'] || '';
        var items = [];
        for (var i=0; i<list.length; i++) {
            var item = list[i].split(':');
            var tag = item[0];
            var label = item[1];
            items.push({
                label: label,
                value: tag,
                renderLabelHtml: function (){
                    return '<div class="edui-label %%-label"><span class="edui-for-' + this.value + '">' + (this.label || '') + '</span></div>';
                }
            });
        }
        var ui = new editorui.Combox({
            items: items,
            title: title,
            className: 'edui-for-paragraph',
            onselect: function (t,index){
                editor.execCommand('Paragraph', this.items[index].value);
            },
            onbuttonclick: function (){
                this.showPopup();
            }
        });
        editor.addListener('selectionchange', function (t,ready){
            var state = editor.queryCommandState('Paragraph');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
//                var value = editor.queryCommandValue('Paragraph');
//                if (value && !ready) {
//                    ui.setValue(value);
//                } else {
//                    ui.setValue(editor.options.labelMap['paragraph'] || '格式');
//                }
                var value = editor.queryCommandValue('Paragraph');
                 ui.setValue(value);
            }
        });
        return ui;
    };

    editorui.InsertTable = function (editor, iframeUrl, title){
        iframeUrl = iframeUrl || editor.options.iframeUrlMap['inserttable'] || 'about:blank';
        iframeUrl = editor.ui.mapUrl(iframeUrl);
        title = title || editor.options.labelMap['inserttable'] || '';
        var dialog = new editorui.Dialog({
            iframeUrl: iframeUrl,
            autoReset: true,
            draggable: true,
            editor: editor,
            className: 'edui-for-inserttable',
            title: title,
            buttons: [{
                className: 'edui-okbutton',
                label: '确认',
                onclick: function (){
                    dialog.close(true);
                }
            }, {
                className: 'edui-cancelbutton',
                label: '取消',
                onclick: function (){
                    dialog.close(false);
                }
            }],
            onok: function (){},
            oncancel: function (){},
            onclose: function (t,ok){
                if (ok) {
                    return this.onok();
                } else {
                    return this.oncancel();
                }
            }
        });
        dialog.render();
        editor.tableDialog = dialog;
        var ui = new editorui.TableButton({
            title: title,
            className: 'edui-for-inserttable',
            onpicktable: function (t,numCols, numRows){
                editor.execCommand('InsertTable', {numRows:numRows, numCols:numCols});
            },
            onmore: function (){
                dialog.open();
            },
            onbuttonclick: function (){
                dialog.open();
            }
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('inserttable');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
            }
        });
        return ui;
    };

    editorui.InsertOrderedList = function (editor, title){
        title = title || editor.options.labelMap['insertorderedlist'] || '';
        var _onMenuClick = function(){
            editor.execCommand("InsertOrderedList", this.value);
        };
        var ui = new editorui.MenuButton({
            className : 'edui-for-insertorderedlist',
            title : title,
            items :
                [{
                    label: '1,2,3...',
                    value: 'decimal',
                    onclick : _onMenuClick
                },{
                    label: 'a,b,c ...',
                    value: 'lower-alpha',
                    onclick : _onMenuClick
                },{
                    label: 'i,ii,iii...',
                    value: 'lower-roman',
                    onclick : _onMenuClick
                },{
                    label: 'A,B,C',
                    value: 'upper-alpha',
                    onclick : _onMenuClick
                },{
                    label: 'I,II,III...',
                    value: 'upper-roman',
                    onclick : _onMenuClick
                }],
            onbuttonclick: function (){
                editor.execCommand("InsertOrderedList", this.value);
            }
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('InsertOrderedList');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
                var value = editor.queryCommandValue('InsertOrderedList');
                ui.setValue(value);
                 ui.setChecked(state)
            }
        });
        return ui;
    };

    editorui.InsertUnorderedList = function (editor, title){
        title = title || editor.options.labelMap['insertunorderedlist'] || '';
        var _onMenuClick = function(){
            editor.execCommand("InsertUnorderedList", this.value);
        };
        var ui = new editorui.MenuButton({
            className : 'edui-for-insertunorderedlist',
            title: title,
            items:
                [{
                    label: '○ 小圆圈',
                    value: 'circle',
                    onclick : _onMenuClick
                },{
                    label: '● 小圆点',
                    value: 'disc',
                    onclick : _onMenuClick
                },{
                    label: '■ 小方块',
                    value: 'square',
                    onclick : _onMenuClick
                }],
            onbuttonclick: function (){
                editor.execCommand("InsertUnorderedList", this.value);
            }
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('InsertUnorderedList');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
                var value = editor.queryCommandValue('InsertUnorderedList');
                ui.setValue(value);
                ui.setChecked(state)
            }
        });
        return ui;
    };

    editorui.FullScreen = function (editor, title){
        title = title || editor.options.labelMap['fullscreen'] || '';
        return new editorui.Button({
            className: 'edui-for-fullscreen',
            title: title,
            onclick: function (){
                if (editor.ui) {
                    editor.ui.setFullScreen(!editor.ui.isFullScreen());
                }
                this.setChecked(editor.ui.isFullScreen());
            }
        });
    };

    
    editorui.MultiMenu = function(editor, iframeUrl, title){
        title = title || editor.options.labelMap['multiMenu'] || '';
        iframeUrl = iframeUrl || editor.options.iframeUrlMap['multimenu'] || 'about:blank';
        iframeUrl = editor.ui.mapUrl(iframeUrl);
        var ui = new editorui.MultiMenuPop({
            title: title,
            editor: editor,
            className: 'edui-for-multimenu',
            iframeUrl: iframeUrl
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('inserthtml');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
            }
        });
        return ui;
    };

    // 覆盖了之前的Date.
    editorui.Date = function (editor){
        var ui = new editorui.DateButton({
            onpickdate: function (t, date){
                editor.execCommand('inserthtml', date);
            },
            onbuttonclick: function (){
                editor.execCommand('date');
            },
            className: 'edui-for-date'
        });
        editor.addListener('selectionchange', function (){
            var state = editor.queryCommandState('inserthtml');
            if (state == -1) {
                ui.setDisabled(true);
            } else {
                ui.setDisabled(false);
            }
        });
        return ui;
    };


})();
