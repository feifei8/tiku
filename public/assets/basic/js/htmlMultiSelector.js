webpackJsonp([8],{0:function(t,e,o){var i=(o(1),o(67));o(68);"api"in window||(window.api={}),window.api.htmlMultiSelector=i},10:function(t,e){t.exports=function(){var t=[];return t.toString=function(){for(var t=[],e=0;e<this.length;e++){var o=this[e];o[2]?t.push("@media "+o[2]+"{"+o[1]+"}"):t.push(o[1])}return t.join("")},t.i=function(e,o){"string"==typeof e&&(e=[[null,e,""]]);for(var i={},n=0;n<this.length;n++){var a=this[n][0];"number"==typeof a&&(i[a]=!0)}for(n=0;n<e.length;n++){var l=e[n];"number"==typeof l[0]&&i[l[0]]||(o&&!l[2]?l[2]=o:o&&(l[2]="("+l[2]+") and ("+o+")"),t.push(l))}},t}},16:function(t,e,o){function i(t,e){for(var o=0;o<t.length;o++){var i=t[o],n=h[i.id];if(n){n.refs++;for(var a=0;a<n.parts.length;a++)n.parts[a](i.parts[a]);for(;a<i.parts.length;a++)n.parts.push(d(i.parts[a],e))}else{for(var l=[],a=0;a<i.parts.length;a++)l.push(d(i.parts[a],e));h[i.id]={id:i.id,refs:1,parts:l}}}}function n(t){for(var e=[],o={},i=0;i<t.length;i++){var n=t[i],a=n[0],l=n[1],r=n[2],s=n[3],d={css:l,media:r,sourceMap:s};o[a]?o[a].parts.push(d):e.push(o[a]={id:a,parts:[d]})}return e}function a(t,e){var o=v(),i=y[y.length-1];if("top"===t.insertAt)i?i.nextSibling?o.insertBefore(e,i.nextSibling):o.appendChild(e):o.insertBefore(e,o.firstChild),y.push(e);else{if("bottom"!==t.insertAt)throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");o.appendChild(e)}}function l(t){t.parentNode.removeChild(t);var e=y.indexOf(t);e>=0&&y.splice(e,1)}function r(t){var e=document.createElement("style");return e.type="text/css",a(t,e),e}function s(t){var e=document.createElement("link");return e.rel="stylesheet",a(t,e),e}function d(t,e){var o,i,n;if(e.singleton){var a=b++;o=g||(g=r(e)),i=c.bind(null,o,a,!1),n=c.bind(null,o,a,!0)}else t.sourceMap&&"function"==typeof URL&&"function"==typeof URL.createObjectURL&&"function"==typeof URL.revokeObjectURL&&"function"==typeof Blob&&"function"==typeof btoa?(o=s(e),i=p.bind(null,o),n=function(){l(o),o.href&&URL.revokeObjectURL(o.href)}):(o=r(e),i=u.bind(null,o),n=function(){l(o)});return i(t),function(e){if(e){if(e.css===t.css&&e.media===t.media&&e.sourceMap===t.sourceMap)return;i(t=e)}else n()}}function c(t,e,o,i){var n=o?"":i.css;if(t.styleSheet)t.styleSheet.cssText=x(e,n);else{var a=document.createTextNode(n),l=t.childNodes;l[e]&&t.removeChild(l[e]),l.length?t.insertBefore(a,l[e]):t.appendChild(a)}}function u(t,e){var o=e.css,i=e.media;if(i&&t.setAttribute("media",i),t.styleSheet)t.styleSheet.cssText=o;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(o))}}function p(t,e){var o=e.css,i=e.sourceMap;i&&(o+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(i))))+" */");var n=new Blob([o],{type:"text/css"}),a=t.href;t.href=URL.createObjectURL(n),a&&URL.revokeObjectURL(a)}var h={},f=function(t){var e;return function(){return"undefined"==typeof e&&(e=t.apply(this,arguments)),e}},m=f(function(){return/msie [6-9]\b/.test(self.navigator.userAgent.toLowerCase())}),v=f(function(){return document.head||document.getElementsByTagName("head")[0]}),g=null,b=0,y=[];t.exports=function(t,e){e=e||{},"undefined"==typeof e.singleton&&(e.singleton=m()),"undefined"==typeof e.insertAt&&(e.insertAt="bottom");var o=n(t);return i(o,e),function(t){for(var a=[],l=0;l<o.length;l++){var r=o[l],s=h[r.id];s.refs--,a.push(s)}if(t){var d=n(t);i(d,e)}for(var l=0;l<a.length;l++){var s=a[l];if(0===s.refs){for(var c=0;c<s.parts.length;c++)s.parts[c]();delete h[s.id]}}}};var x=function(){var t=[];return function(e,o){return t[e]=o,t.filter(Boolean).join("\n")}}()},53:function(t,e){t.exports=function(){throw new Error("define cannot be used indirect")}},67:function(t,e,o){var i;(function(n,a){(function(){var a=0,l=function(t){if("undefined"==typeof n)return void alert("HtmlMultiSelector require jQuery");var e={container:null,seperator:",",dynamic:!1,server:"/path/to/data",data:[],maxLevel:0,fixedLevel:0,lang:{close:"取消",done:"确定",pleaseSelect:"请选择"},callback:{change:function(t,e){},done:function(){},close:function(){}},selectorValue:"[data-value]",selectorTitle:"[data-title]",valueKey:"id",parentValueKey:"pid",titleKey:"title",sortKey:"sort",rootParentValue:0,optionItemHeightInEm:2,serverMethod:"get",serverDataType:"json",serverResponseHandle:function(t){return"object"!=typeof t?(alert("ErrorResponse:"+t),[]):"code"in t&&"data"in t?0!=t.code?(alert("ErrorResponseCode:"+t.code),[]):t.data:(alert("ErrorResponseObject:"+t.toString()),[])}};this.opt=n.extend(e,t),this.dom={container:n(this.opt.container),dialog:null,dialogItems:null},this.data={items:[],value:[],title:[]},this.opt.dynamic||(this.data.items=this.dataConvert(this.opt.data)),this.init()};l.prototype={init:function(){this.initDialog(),this.initEvent(),this.initValues()},initEvent:function(){var t=this,e="undefined"!=typeof window.document.ontouchstart,o=e?"touchstart":"mousedown",i=e?"touchend":"mouseup",a=e?"touchmove":"mousemove";this.dom.dialog.on(o,"[data-btn-close]",function(){return t.close(),!1}),this.dom.dialog.on(o,"[data-btn-done]",function(){return t.done(),!1});var l=null,r=0,s=0,d=0,c=0;this.dom.dialog.on(o,"[data-option-level]",function(o){s=e?o.originalEvent.targetTouches[0].clientY:o.clientY,l=n(this).find("[data-options]"),c=l.find("div").length;var i=parseFloat(l.css("top"));i||(i=0),d=n(this).find("[data-grid]").height()+2,r=i*t.opt.optionItemHeightInEm/d}),this.dom.dialog.on(a,document,function(o){if(o.preventDefault(),null!=l){var i,n,a,u;i=e?o.originalEvent.targetTouches[0].clientY:o.clientY,n=i-s,a=n*t.opt.optionItemHeightInEm/d,u=r+a,u<-(c-1)*t.opt.optionItemHeightInEm?l.css({top:-((c-1)*t.opt.optionItemHeightInEm)+"em"}):u>0?l.css({top:"0"}):l.css({top:u+"em"})}}),this.dom.dialog.on(i,document,function(e){if(null!=l){var o=parseFloat(l.css("top"));r=o*t.opt.optionItemHeightInEm/d,r=parseInt(r/t.opt.optionItemHeightInEm-.5)*t.opt.optionItemHeightInEm,l.animate({top:r+"em"});var i=-parseInt(r/t.opt.optionItemHeightInEm);l.find("[data-value]").removeAttr("data-selected"),n(l.find("[data-value]").get(i)).attr("data-selected",!0),t.dom.dialog.trigger("dialog.category.change",[l.closest("[data-option-level]")]),l=null}}),this.dom.dialog.on("dialog.category.change",function(e,o){var i=o.find("[data-options] > [data-selected]"),n=parseInt(o.attr("data-option-level")),a=i.attr("data-value");0==t.opt.maxLevel||n<t.opt.maxLevel?t.opt.dynamic?""==a?(t.renderClear(n+1),t.syncVal()):t.sendAsyncRequest(a,function(e){t.data.items=e,t.render(n+1,a),t.syncVal()}):(0==t.opt.maxLevel||n<t.opt.maxLevel)&&(""==a?(t.renderClear(n+1),t.syncVal()):(t.render(n+1,a),t.syncVal())):t.syncVal()})},initDialog:function(){var t="html-multi-selector-"+ ++a;if(this.dom.dialog=n(['<div class="html-multi-selector-container" id="',t,'">','   <div class="html-multi-selector-box html-multi-selector-slide-in-up">','       <div class="html-multi-selector-btn-box">','           <div class="html-multi-selector-btn" data-btn-close>',this.opt.lang.close,"</div>",'           <div class="html-multi-selector-btn" data-btn-done>',this.opt.lang.done,"</div>","       </div>",'       <div class="html-multi-selector-mask">','           <div class="html-multi-selector-roll" data-items>',"           </div>","       </div>","   </div>","</div>"].join("")),this.dom.dialog.appendTo("body"),this.dom.dialogItems=this.dom.dialog.find("[data-items]"),this.opt.fixedLevel>0)for(var e=0;e<this.opt.fixedLevel;e++)this.setLevelOption(e+1,[])},initValues:function(){var t=this,e=[],o=[];if(this.dom.container.length){var i,n=this.dom.container.find(this.opt.selectorValue);if(n.length>0){var a=n.val();i=a.split(this.opt.seperator);for(var l=0;l<i.length;l++)i[l]&&e.push(i[l])}else{a=this.dom.container.find(this.opt.selectorTitle).val(),i=a.split(this.opt.seperator);for(var l=0;l<i.length;l++)i[l]&&o.push(i[l])}}this.opt.dynamic&&e.length?this.val(e):this.opt.dynamic&&o.length?this.titleVal(o):this.opt.dynamic?this.sendAsyncRequest(0,function(e){t.data.items=e,t.render(1,t.opt.rootParentValue)}):e.length?this.val(e):o.length?this.titleVal(o):t.render(1,t.opt.rootParentValue)},sendAsyncRequest:function(t,e,o){o=o||null;var i={},a=this,l=this.opt.sortKey;i[this.opt.parentValueKey]=t,i[this.opt.titleKey]=o,n.ajax({type:this.opt.serverMethod,url:this.opt.server,dataType:this.opt.serverDataType,timeout:3e4,data:i,success:function(t){var o=a.opt.serverResponseHandle(t);o.sort(function(t,e){return t[l]-e[l]});var i=a.dataConvert(t.data);e(i)},error:function(){alert("请求出现错误 T_T")}})},dataConvert:function(t){for(var e=this,o=[],i=0;i<t.length;i++)o.push({parentValue:t[i][e.opt.parentValueKey],value:t[i][e.opt.valueKey],title:t[i][e.opt.titleKey]});return o},render:function(t,e){var o=this;this.dom.dialogItems.find("[data-option-level]").each(function(e,i){var a=parseInt(n(i).attr("data-option-level"));a>=t&&(t>1?o.opt.fixedLevel?n(i).find("[data-options]").html("").css("top",0):n(i).remove():n(i).find("[data-options]").html("").css("top",0))});for(var i=[],a=this.data.items,l=0;l<a.length;l++)a[l].parentValue==e&&i.push({value:a[l].value,title:a[l].title});i.length&&this.setLevelOption(t,i)},renderClear:function(t){var e=this;this.dom.dialogItems.find("[data-option-level]").each(function(o,i){var a=parseInt(n(i).attr("data-option-level"));a>=t&&(e.opt.fixedLevel?n(i).find("[data-options]").html("").css("top",0):n(i).remove())})},setLevelOption:function(t,e){var o=this.dom.dialogItems.find("[data-option-level="+t+"]");o.length||(o=n(['<div data-option-level="',t,'">','   <div class="html-multi-selector-gallery" data-options></div>','   <div class="html-multi-selector-grid" data-grid></div>',"</div>"].join("")),this.dom.dialogItems.append(o));var i=[];i.push('<div data-value="">'+this.opt.lang.pleaseSelect+"</div>");for(var a=0;a<e.length;a++)i.push('<div data-value="'+e[a].value+'">'+e[a].title+"</div>");o.find("[data-options]").html(i.join(""))},getLevelValue:function(t){var e=this.dom.dialogItems.find("[data-option-level="+t+"]");if(!e.length)return null;var o=e.find("[data-options]"),i=o.find("[data-value][data-selected]");return i.length?i.attr("data-value"):null},setLevelValue:function(t,e){var o=this.dom.dialogItems.find("[data-option-level="+t+"]");if(o.length){var i=o.find("[data-options]"),a=i.find('[data-value="'+e+'"]');if(a.length){var l=i.find("[data-value]").index(a);i.find("[data-value]").removeAttr("data-selected"),n(i.find("[data-value]").get(l)).attr("data-selected",!0),i.css({top:-(l*this.opt.optionItemHeightInEm)+"em"})}}},setLevelTitle:function(t,e){var o=this.dom.dialogItems.find("[data-option-level="+t+"]");if(o.length){var i=o.find("[data-options]"),a=null;if(i.find("[data-value]").each(function(t,o){n(o).text()==e&&(a=n(o))}),a){var l=i.find("[data-value]").index(a);i.find("[data-value]").removeAttr("data-selected"),n(i.find("[data-value]").get(l)).attr("data-selected",!0),i.css({top:-(l*this.opt.optionItemHeightInEm)+"em"})}}},close:function(){this.dom.dialog.hide(),this.opt.callback.close&&this.opt.callback.close.call(this)},open:function(){this.dom.dialog.show()},done:function(){this.dom.dialog.hide(),this.opt.callback.done&&this.opt.callback.done.call(this)},val:function(t){var e=this;if(void 0==t)return e.data.value;var o=t;if(!o.length)return void e.render(1,e.opt.rootParentValue);var i=function(){e.render(1,e.opt.rootParentValue);for(var t=0;t<o.length;t++){var i=t+1,n=o[t];e.setLevelValue(i,n),(0==e.opt.maxLevel||i+1<=e.opt.maxLevel)&&e.render(i+1,n)}e.syncVal()};if(e.opt.dynamic){var n=[e.opt.rootParentValue];n=n.concat(o),e.sendAsyncRequest(n.join(e.opt.seperator),function(t){e.data.items=t,i()})}else i()},titleVal:function(t){var e=this;if(void 0==t)return e.data.title;var o=t;if(!o.length)return void e.render(1,e.opt.rootParentValue);var i=function(){e.render(1,e.opt.rootParentValue);for(var t=0;t<o.length;t++){var i=t+1,n=o[t];e.setLevelTitle(i,n),(0==e.opt.maxLevel||i+1<=e.opt.maxLevel)&&e.render(i+1,e.getLevelValue(i))}e.syncVal()};e.opt.dynamic?e.sendAsyncRequest(null,function(t){e.data.items=t,i()},o.join(e.opt.seperator)):i()},syncVal:function(){var t=this;t.dom.container.find("[data-value]").val(""),t.dom.container.find("[data-title]").val("");var e=[],o=[];t.dom.dialogItems.find("[data-option-level]").each(function(t,i){var a=n(i).find("[data-value][data-selected]"),l=a.attr("data-value");l&&(e.push(l),o.push(a.html()))}),t.data.value=e,t.data.title=o,t.dom.container.find("[data-value]").each(function(o,i){var a=n(i).attr("data-for-level");a?(a=parseInt(a),a<=e.length&&n(i).val(e[a-1])):n(i).val(e.join(t.opt.seperator))}),t.dom.container.find("[data-title]").each(function(e,i){var a=n(i).attr("data-for-level");a?(a=parseInt(a),a<=o.length&&n(i).val(o[a-1])):n(i).val(o.join(t.opt.seperator))}),t.opt.callback.change&&t.opt.callback.change.call(this,e,o)}},"undefined"!=typeof t&&"object"==typeof e&&o(53).cmd?t.exports=l:(i=function(){return l}.call(e,o,e,t),!(void 0!==i&&(t.exports=i)))}).call(function(){return this||("undefined"!=typeof window?window:a)}())}).call(e,o(1),function(){return this}())},68:function(t,e,o){var i=o(69);"string"==typeof i&&(i=[[t.id,i,""]]);o(16)(i,{});i.locals&&(t.exports=i.locals)},69:function(t,e,o){e=t.exports=o(10)(),e.push([t.id,'.html-multi-selector-container{font-family:Helvetica Neue,Helvetica,Arial,sans-serif;font-size:14px;background-color:rgba(0,0,0,.2);position:fixed;top:0;left:0;width:100%;height:100%;z-index:10000;overflow:hidden;animation-fill-mode:both;display:none}.html-multi-selector-box{vertical-align:middle;background-color:#d5d8df;color:#000;margin:0;height:auto;width:100%;position:absolute;left:0;bottom:0;z-index:10001;overflow:hidden;transform:translateZ(0)}.html-multi-selector-box.html-multi-selector-slide-in-up{animation:slideInUp .3s}.html-multi-selector-mask{-webkit-mask:-webkit-linear-gradient(bottom,#debb47 50%,rgba(36,142,36,0));padding:0}.html-multi-selector-mask .html-multi-selector-roll{display:-ms-flexbox;display:flex;width:100%;height:auto;overflow:hidden;background-color:transparent;-webkit-mask:-webkit-linear-gradient(top,#debb47 50%,rgba(36,142,36,0))}.html-multi-selector-mask .html-multi-selector-roll>div{font-size:1em;height:12em;float:left;background-color:transparent;position:relative;overflow:hidden;-ms-flex:1;flex:1}.html-multi-selector-mask .html-multi-selector-roll>div .html-multi-selector-gallery{width:100%;float:left;position:absolute;z-index:10002;margin-top:4em}.html-multi-selector-mask .html-multi-selector-roll>div .html-multi-selector-gallery>div{height:2em;line-height:2em;text-align:center;display:-ms-flexbox;display:flex;line-clamp:1;-ms-flex-direction:column;flex-direction:column;overflow:hidden}.html-multi-selector-mask .html-multi-selector-roll>div .html-multi-selector-grid{position:relative;top:4em;width:100%;height:2em;margin:0;box-sizing:border-box;z-index:0;border-top:1px solid #abaeb5;border-bottom:1px solid #abaeb5}.html-multi-selector-btn-box{display:-ms-flexbox;display:flex;-ms-flex-pack:justify;justify-content:space-between;-ms-flex-align:stretch;align-items:stretch;background-color:#f1f2f4;position:relative}.html-multi-selector-btn-box .html-multi-selector-btn-box:after,.html-multi-selector-btn-box .html-multi-selector-btn-box:before{content:"";position:absolute;height:1px;width:100%;display:block;background-color:#96979b;z-index:15;transform:scaleY(.33)}.html-multi-selector-btn-box .html-multi-selector-btn-box:before{left:0;top:0;transform-origin:50% 20%}.html-multi-selector-btn-box .html-multi-selector-btn-box:after{left:0;bottom:0;transform-origin:50% 70%}.html-multi-selector-btn-box .html-multi-selector-btn{color:#0575f2;font-size:1em;line-height:1em;text-align:center;padding:.8em 1em}',""])}});