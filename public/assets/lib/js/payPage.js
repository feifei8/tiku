var $=require("jquery"),UrlWatcher=require("./../../lib/js/urlWatcher.js"),JqueryQrcode=require("./../../lib/js/jqueryQrcode.js"),Form=require("./../../lib/js/form.js"),PayPage=function(e){var a=$.extend({payTypeSelector:"",paySubmitSelector:"",payTypeActiveClass:"",dialog:null,device:"pc|mobile",server:"?",autoClickPayType:null,postData:function(){return{}}},e),t=$(a.payTypeSelector),i=$(a.paySubmitSelector);$(function(){setTimeout(function(){a.autoClickPayType?(t.filter('[data-type="'+a.autoClickPayType+'"]').addClass(a.payTypeActiveClass),i.click()):t.filter(":visible").eq(0).addClass(a.payTypeActiveClass)},100)}),t.on("click",function(){return t.removeClass(a.payTypeActiveClass),$(this).addClass(a.payTypeActiveClass),!1}),i.on("click",function(){var e=null;t.each(function(t,i){$(i).hasClass(a.payTypeActiveClass)&&(e=$(i).attr("data-type"))}),a.dialog.loadingOn();var i=$.extend(a.postData(),{payType:e});return $.post(a.server,i,function(t){a.dialog.loadingOff(),Form.defaultCallback(t,{success:function(a){s(e,a)}},a.dialog)}),!1});var c=function(e){if("pc"==a.device){var t=window.open(e.data.payRedirect,"_blank");!t||t.closed||void 0===t.closed?window.parent.location.href=e.data.payRedirect:a.dialog.confirm("付款成功?",function(){window.parent.location.href=e.data.successRedirect})}else"mobile"==a.device&&(window.location.href=e.data.payRedirect)},o=function(e){var t=new UrlWatcher({url:e.data.watchUrl,data:{},requestFinish:function(i){Form.defaultCallback(i,{success:function(a){var i=a.data,c=$("#wechatPayStatus");"new"==i.status?(c.html("等待支付..."),t.next()):"payed"==i.status?(c.html('<i style="color:green;font-size:16px;" class="uk-icon-check-circle"></i> 支付成功，即将跳转...'),setTimeout(function(){window.parent.location.href=e.data.successRedirect},2e3)):"expired"==i.status&&c.html("订单已过期")}},a.dialog)}});a.dialog.dialogContent('<div style="padding:20px;line-height:40px;"><div style="width:200px;height:200px;" data-qrcode-pop></div><div class="uk-text-center">微信扫描上方二维码支付</div><div class="uk-text-center" id="wechatPayStatus">等待支付...</div></div>',{shadeClose:!1,closeBtn:!0,openCallback:function(){$("[data-qrcode-pop]").qrcode({width:200,height:200,correctLevel:0,text:e.data.codeUrl}),t.start()}})},n=function(e){wx.miniProgram.navigateTo({url:"/pages/webview_handle?type=pay&url="+e.data.successRedirect+"&payload="+e.data.json})},r=function(e){var t=JSON.parse(e.data.json);WeixinJSBridge.invoke("getBrandWCPayRequest",t,function(t){"get_brand_wcpay_request:ok"==t.err_msg?(alert("支付成功"),a.dialog.loadingOn(),window.location.href=e.data.successRedirect):"get_brand_wcpay_request:cancel"==t.err_msg?alert("支付已取消"):alert("支付失败 "+JSON.stringify(t))})},l=function(e){if("pc"==a.device){var t=window.open(e.data.payRedirect,"_blank");!t||t.closed||void 0===t.closed?window.parent.location.href=e.data.payRedirect:a.dialog.confirm("付款成功?",function(){window.parent.location.href=e.data.successRedirect})}else"mobile"==a.device&&(window.location.href=e.data.payRedirect)},d=function(e){window.location.href=e.data.successRedirect},s=function(e,a){"alipay"==e||"alipay_web"==e?c(a):"wechat"==e?o(a):"wechat_mobile"==e?r(a):"wechat_mini_program"==e?n(a):"wechat_manual"==e||"alipay_manual"==e?l(a):"offline_pay"==e&&d(a)}};module.exports=PayPage;