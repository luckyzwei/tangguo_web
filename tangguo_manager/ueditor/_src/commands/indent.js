///import core
///import commands\paragraph.js
///commands 首行缩进
/**
 * 首行缩进
 * @function
 * @name baidu.editor.execCommand
 * @param   {String}   cmdName     outdent取消缩进，indent缩进
 */
(function (){
    var domUtils = baidu.editor.dom.domUtils;
    baidu.editor.commands['outdent'] = baidu.editor.commands['indent'] = {
        execCommand : function(cmd) {
             var me = this,
                 value = cmd.toLowerCase() == 'outdent' ? '0em' : (me.options.indentValue || '2em');
             me.execCommand('Paragraph','p',{'textIndent':value});
        }

    };
})();
