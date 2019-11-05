var $=require("jquery"),layer=require("./../../layer/layer.js"),layerCss=require("./../../layer/layer.css"),Util=require("./util.js"),Dialog={device:"pc",loadingOn:function(e){if(e=e||null){var l=layer.open({type:1,content:'<div style="padding:10px;height:32px;box-sizing:content-box;"><div class="layui-layer-ico16" style="display:inline-block;margin-right:10px;"></div><div style="display:inline-block;line-height:32px;vertical-align:top;font-size:13px;">'+e+"</div></div>",shade:[.3,"#000"],closeBtn:!1,title:!1,area:["auto","auto"]});$("#layui-layer"+l).attr("type","loading")}else layer.load(2)},loadingOff:function(){layer.closeAll("loading")},tipSuccess:function(e){var l=2e3;e&&e.length>10&&(l=1e3*parseInt(e.length/5)),layer.msg(e,{icon:1,shade:.3,time:l,shadeClose:!0})},tipError:function(e){var l=2e3;e.length>10&&(l=1e3*parseInt(e.length/5)),layer.msg(e,{icon:2,shade:.3,time:l,shadeClose:!0})},tipPopoverShow:function(e,l){var t=$(e).data("popover-dialog");t&&layer.close(t),t=layer.tips(l,e,{tips:[1,"#333"]}),$(e).data("popover-dialog",t)},tipPopoverHide:function(e){var l=$(e).data("popover-dialog");l&&layer.close(l)},alertSuccess:function(e,l){layer.alert(e,{icon:1,closeBtn:0},function(e){layer.close(e),l&&l()});try{document.activeElement.blur();var t=$("#layui-layer"+index),i=t.find(".layui-layer-btn0");i.attr("tabindex",0).css({outline:"none"}).get(0).focus(),t.on("keypress",function(){i.click()})}catch(e){}},alertError:function(e,l){var t=layer.alert(e,{icon:2,closeBtn:0},function(e){layer.close(e),l&&l()});try{document.activeElement.blur();var i=$("#layui-layer"+t),n=i.find(".layui-layer-btn0");n.attr("tabindex",0).css({outline:"none"}).get(0).focus(),i.on("keypress",function(){n.click()})}catch(e){}return t},confirm:function(e,l,t,i){i=i||{icon:3,title:"提示"},l=l||!1,t=t||!1,layer.confirm(e,i,function(e){layer.close(e),l&&l()},function(e){layer.close(e),t&&t()})},dialog:function(e,l){var t=$.extend({title:null,width:"600px",height:"80%",shadeClose:!0,closeCallback:function(){}},l);return layer.open({type:2,title:"玩命加载中...",shadeClose:t.shadeClose,shade:.5,maxmin:!0,area:[t.width,t.height],scrollbar:!1,content:e,success:function(e,l){if(null!==t.title)return void layer.title(t.title,l);try{var i=$(e).find("iframe")[0].contentWindow.document.title;layer.title(i,l)}catch(e){}},end:function(){t.closeCallback()}})},dialogContent:function(e,l){var t=$.extend({closeBtn:!0,width:"auto",height:"auto",shade:[.3,"#000"],shadeClose:!0,openCallback:function(){},closeCallback:function(){}},l);return layer.open({shade:t.shade,type:1,title:!1,zindex:2019,closeBtn:t.closeBtn,shadeClose:t.shadeClose,scrollbar:!1,content:e,area:[t.width,t.height],success:function(){t.openCallback()},end:function(){t.closeCallback()}})},dialogClose:function(e){layer.close(e)},dialogCloseAll:function(){layer.closeAll()},input:function(e,l){var t=$.extend({label:"请输入",width:"200px",height:"auto",defaultValue:""},l),i=t.defaultValue,n=!1,a=Dialog.dialogContent(['<div id="dialog-input-box" style="width:',t.width,";height:",t.height,';background:#FFF;border-radius:3px;">','<div style="padding:10px 10px 0 10px;">',t.label,"</div>",'<div style="padding:10px;"><input type="text" style="border:1px solid #CCC;height:30px;line-height:30px;padding:0 5px;width:100%;display:block;box-sizing:border-box;outline:none;border-radius:2px;" value="',Util.specialchars(t.defaultValue),'" /></div>','<div style="cursor:pointer;padding:10px;text-align:center;color:#40AFFE;line-height:20px;border-top:1px solid #EEE;cursor:default;" class="ok">确定</div>',"</div>"].join(""),{openCallback:function(){$("#dialog-input-box").find(".ok").on("click",function(){n=!0,Dialog.dialogClose(a)}),$("#dialog-input-box").find("input").on("change",function(){i=$(this).val()})},closeCallback:function(){n&&e&&e(i)}})}};module.exports=Dialog;