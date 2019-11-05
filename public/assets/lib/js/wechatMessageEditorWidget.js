var Editor=require("./editor.js"),editorGlobalId=0,emotions=[{key:"[微笑]",val:"01"},{key:"[撇嘴]",val:"02"},{key:"[色]",val:"03"},{key:"[发呆]",val:"04"},{key:"[得意]",val:"05"},{key:"[流泪]",val:"06"},{key:"[害羞]",val:"07"},{key:"[闭嘴]",val:"08"},{key:"[睡]",val:"09"},{key:"[大哭]",val:"10"},{key:"[尴尬]",val:"11"},{key:"[发怒]",val:"12"},{key:"[调皮]",val:"13"},{key:"[呲牙]",val:"14"},{key:"[惊讶]",val:"15"},{key:"[难过]",val:"16"},{key:"[酷]",val:"17"},{key:"[冷汗]",val:"18"},{key:"[抓狂]",val:"19"},{key:"[吐]",val:"20"},{key:"[偷笑]",val:"21"},{key:"[愉快]",val:"22"},{key:"[白眼]",val:"23"},{key:"[傲慢]",val:"24"},{key:"[饥饿]",val:"25"},{key:"[困]",val:"26"},{key:"[惊恐]",val:"27"},{key:"[流汗]",val:"28"},{key:"[憨笑]",val:"29"},{key:"[悠闲]",val:"30"},{key:"[奋斗]",val:"31"},{key:"[咒骂]",val:"32"},{key:"[疑问]",val:"33"},{key:"[嘘]",val:"34"},{key:"[晕]",val:"35"},{key:"[疯了]",val:"36"},{key:"[衰]",val:"37"},{key:"[骷髅]",val:"38"},{key:"[敲打]",val:"39"},{key:"[再见]",val:"40"},{key:"[擦汗]",val:"41"},{key:"[抠鼻]",val:"42"},{key:"[鼓掌]",val:"43"},{key:"[糗大了]",val:"44"},{key:"[坏笑]",val:"45"},{key:"[左哼哼]",val:"46"},{key:"[右哼哼]",val:"47"},{key:"[哈欠]",val:"48"},{key:"[鄙视]",val:"49"},{key:"[委屈]",val:"50"},{key:"[快哭了]",val:"51"},{key:"[阴险]",val:"52"},{key:"[亲亲]",val:"53"},{key:"[吓]",val:"54"},{key:"[可怜]",val:"55"},{key:"[菜刀]",val:"56"},{key:"[西瓜]",val:"57"},{key:"[啤酒]",val:"58"},{key:"[篮球]",val:"59"},{key:"[乒乓]",val:"60"},{key:"[咖啡]",val:"61"},{key:"[饭]",val:"62"},{key:"[猪头]",val:"63"},{key:"[玫瑰]",val:"64"},{key:"[凋谢]",val:"65"},{key:"[嘴唇]",val:"66"},{key:"[爱心]",val:"67"},{key:"[心碎]",val:"68"},{key:"[蛋糕]",val:"69"},{key:"[闪电]",val:"70"},{key:"[炸弹]",val:"71"},{key:"[刀]",val:"72"},{key:"[足球]",val:"73"},{key:"[瓢虫]",val:"74"},{key:"[便便]",val:"75"},{key:"[月亮]",val:"76"},{key:"[太阳]",val:"77"},{key:"[礼物]",val:"78"},{key:"[拥抱]",val:"79"},{key:"[强]",val:"80"},{key:"[弱]",val:"81"},{key:"[握手]",val:"82"},{key:"[胜利]",val:"83"},{key:"[抱拳]",val:"84"},{key:"[勾引]",val:"85"},{key:"[拳头]",val:"86"},{key:"[差劲]",val:"87"},{key:"[爱你]",val:"88"},{key:"[NO]",val:"89"},{key:"[OK]",val:"90"},{key:"[爱情]",val:"91"},{key:"[飞吻]",val:"92"},{key:"[跳跳]",val:"93"},{key:"[发抖]",val:"94"},{key:"[怄火]",val:"95"},{key:"[转圈]",val:"96"},{key:"[磕头]",val:"97"},{key:"[回头]",val:"98"},{key:"[跳绳]",val:"99"},{key:"[投降]",val:"100"},{key:"[激动]",val:"101"},{key:"[乱舞]",val:"102"},{key:"[献吻]",val:"103"},{key:"[左太极]",val:"104"},{key:"[右太极]",val:"105"}],editorHtml=['<div class="wechat-message-editor">','   <div class="editor">','       <div class="input">','           <script type="text/plain" data-content style="width:400px;"><\/script>',"       </div>","   </div>",'   <textarea style="display:none;" data-wechat-message-editor-content></textarea>',"</div>"].join(""),WechatMessageEditorWidget=function(e,a){var l=$.extend({toolbars:[["wechatcustomemotion","link"]]},a),t=!1,v=this,y=null,k=$(e),i="wechat-message-editor-"+ ++editorGlobalId,n=i+"-content";v.ueditor=null,v.value=null;v.val=function(e){if(void 0===e){var a=v.ueditor.getContent();return a=a.replace(/<a.*?href="(.*?)".*?>(.*?)<\/a>/g,"[a|$1]$2[/a]"),a=a.replace(/<img.*?data-key="\[(.*?)\]".*?>/g,"[$1]"),a=a.replace(/<\/p>/g,"\n"),a=a.replace(/<.*?>/g,""),a=a.replace(/\[\/a\]/g,"</a>"),a=a.replace(/\[a\|(.*?)]/g,'<a href="$1">'),a=a.replace(/&nbsp;/g," "),v.value=a,v.value}a=e,a=a.replace(/\[(.*?)\]/,function(e){for(var a=0;a<emotions.length;a++)if(emotions[a].key==e){var l=emotions[a].key,t=emotions[a].val;return'<img data-key="'+l+'" data-val="'+t+'" src="/assets/lib/img/emotion/'+t+'@2x.png" height="20" style="vertical-align:middle;" />'}return e}),a=a.replace(/\n/g,"</p><p>"),a="<p>"+a+"</p>";var l=function(){t?v.ueditor.setContent(a,!1):setTimeout(function(){l()},100)};l()},v.isReady=function(){return t},v.sync=function(){y.val(v.val())},function(){var e=$(editorHtml);e.attr("id",i),e.find("[data-content]").attr("id",i+"-content");var a=k.html();k.html(e.prop("outerHTML")),y=k.find("textarea").attr("name",k.attr("data-wechat-message-editor-widget")),y.val(a),k.show(),v.ueditor=Editor.raw.getEditor(n,{toolbars:l.toolbars,autoClearinitialContent:!0,wordCount:!1,elementPathEnabled:!1,initialFrameHeight:100,enableAutoSave:!1,pasteplain:!0,autoHeightEnabled:!1}),v.ueditor.ready(function(){t=!0}),v.val(y.val()),v.ueditor.addListener("contentchange",function(){v.sync()})}()};module.exports=WechatMessageEditorWidget;