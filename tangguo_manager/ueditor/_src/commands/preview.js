///import core
///commands 预览
/**
 * 预览
 * @function
 * @name baidu.editor.execCommand
 * @param   {String}   cmdName     preview预览编辑器内容
 */
baidu.editor.commands['preview'] = {
    execCommand : function(){
        var me = this;
        var w = window.open('', '_blank', "");
        var d = w.document,
            utils = baidu.editor.utils;
        d.open();
        d.write('<html><head><link rel="stylesheet" type="text/css" href="' + utils.unhtml( this.options.iframeCssUrl ) + '"/><title></title></head><body>' +
            me.getContent() +
            '</body></html>');
        d.close();
    },
    notNeedUndo : 1
};
//baidu.editor.contextMenuItems.push({
//    label : '预览',
//    cmdName : 'preview'
//});
