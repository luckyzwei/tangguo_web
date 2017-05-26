///import core
///import commands\inserthtml.js
///commands 图片
/**
 * Created by .
 * User: zhuwenxuan
 * Date: 11-8-25
 * Time: 下午2:03
 * To change this template use File | Settings | File Templates.
 */

/*
* for(o in opt){
                    str += o +&quot;=&#39;&quot;+opt[o]+&quot;&#39; &quot;;
                }
                str += &quot;/&gt;&quot;;
                this.execCommand(&quot;inserthtml&quot;,str);
* */
(function (){
    var domUtils = baidu.editor.dom.domUtils;
    baidu.editor.commands['insertimage'] = {
        execCommand : function (cmd, opt){
            var range = this.selection.getRange(),
                    img = range.getClosedNode();
            if(img && /img/ig.test( img.tagName )){
                if(img.className != "edui-faked-video" ){
                    domUtils.setAttributes(img,opt);
                }
            }else{
                var str = "<img ",o;
                for(o in opt){
                    str += o +"='"+opt[o]+"' ";
                }
                str += "/>";
                this.execCommand("inserthtml",str);
            }
        }
    };
})();
