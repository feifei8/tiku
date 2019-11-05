!function(t){var a=new Array,e=new Array;t.fn.doAutosize=function(a){var e=t(this).data("minwidth"),i=t(this).data("maxwidth"),n="",r=t(this),u=t("#"+t(this).data("tester_id"));if(n!==(n=r.val())){var d=n.replace(/&/g,"&amp;").replace(/\s/g," ").replace(/</g,"&lt;").replace(/>/g,"&gt;");u.html(d);var o=u.width(),s=o+a.comfortZone>=e?o+a.comfortZone:e;(s<r.width()&&s>=e||s>e&&s<i)&&r.width(s)}},t.fn.resetAutosize=function(a){var e=t(this).data("minwidth")||a.minInputWidth||t(this).width(),i=t(this).data("maxwidth")||a.maxInputWidth||t(this).closest(".tagsinput").width()-a.inputPadding,n=t(this),r=t("<tester/>").css({position:"absolute",top:-9999,left:-9999,width:"auto",fontSize:n.css("fontSize"),fontFamily:n.css("fontFamily"),fontWeight:n.css("fontWeight"),letterSpacing:n.css("letterSpacing"),whiteSpace:"nowrap"}),u=t(this).attr("id")+"_autosize_tester";!t("#"+u).length>0&&(r.attr("id",u),r.appendTo("body")),n.data("minwidth",e),n.data("maxwidth",i),n.data("tester_id",u),n.css("width",e)},t.fn.addTag=function(i,n){return n=jQuery.extend({focus:!1,callback:!0},n),this.each(function(){var r=t(this).attr("id"),u=t(this).val().split(a[r]);if(""==u[0]&&(u=new Array),i=jQuery.trim(i),n.unique){var d=t(this).tagExist(i);1==d&&t("#"+r+"_tag").addClass("not_valid")}else var d=!1;if(""!=i&&1!=d){if(t("<span>").addClass("tag").append(t("<span>").text(i).append("&nbsp;&nbsp;"),t("<a>",{href:"#",title:"Removing tag",text:"x"}).click(function(){return t("#"+r).removeTag(escape(i))})).insertBefore("#"+r+"_addTag"),u.push(i),t("#"+r+"_tag").val(""),n.focus?t("#"+r+"_tag").focus():t("#"+r+"_tag").blur(),t.fn.tagsInput.updateTagsField(this,u),n.callback&&e[r]&&e[r].onAddTag){var o=e[r].onAddTag;o.call(this,i)}if(e[r]&&e[r].onChange){var s=u.length,o=e[r].onChange;o.call(this,t(this),u[s-1])}}}),!1},t.fn.removeTag=function(n){return n=unescape(n),this.each(function(){var r=t(this).attr("id"),u=t(this).val().split(a[r]);for(t("#"+r+"_tagsinput .tag").remove(),str="",i=0;i<u.length;i++)u[i]!=n&&(str=str+a[r]+u[i]);if(t.fn.tagsInput.importTags(this,str),e[r]&&e[r].onRemoveTag){e[r].onRemoveTag.call(this,n)}}),!1},t.fn.tagExist=function(e){var i=t(this).attr("id"),n=t(this).val().split(a[i]);return jQuery.inArray(e,n)>=0},t.fn.importTags=function(a){var e=t(this).attr("id");t("#"+e+"_tagsinput .tag").remove(),t.fn.tagsInput.importTags(this,a)},t.fn.tagsInput=function(i){var r=jQuery.extend({interactive:!0,defaultText:"add a tag",minChars:0,width:"300px",height:"100px",autocomplete:{selectFirst:!1},hide:!0,delimiter:",",unique:!0,removeWithBackspace:!0,placeholderColor:"#666666",autosize:!0,comfortZone:20,inputPadding:12},i),u=0;return this.each(function(){if(void 0===t(this).attr("data-tagsinput-init")){t(this).attr("data-tagsinput-init",!0),r.hide&&t(this).hide();var i=t(this).attr("id");i&&!a[t(this).attr("id")]||(i=t(this).attr("id","tags"+(new Date).getTime()+u++).attr("id"));var d=jQuery.extend({pid:i,real_input:"#"+i,holder:"#"+i+"_tagsinput",input_wrapper:"#"+i+"_addTag",fake_input:"#"+i+"_tag"},r);a[i]=d.delimiter,(r.onAddTag||r.onRemoveTag||r.onChange)&&(e[i]=new Array,e[i].onAddTag=r.onAddTag,e[i].onRemoveTag=r.onRemoveTag,e[i].onChange=r.onChange);var o='<div id="'+i+'_tagsinput" class="tagsinput"><div id="'+i+'_addTag">';if(r.interactive&&(o=o+'<input id="'+i+'_tag" value="" data-default="'+r.defaultText+'" />'),o+='</div><div class="tags_clear"></div></div>',t(o).insertAfter(this),t(d.holder).css("width",r.width),t(d.holder).css("min-height",r.height),t(d.holder).css("height",r.height),""!=t(d.real_input).val()&&t.fn.tagsInput.importTags(t(d.real_input),t(d.real_input).val()),r.interactive){if(t(d.fake_input).val(t(d.fake_input).attr("data-default")),t(d.fake_input).css("color",r.placeholderColor),t(d.fake_input).resetAutosize(r),t(d.holder).bind("click",d,function(a){t(a.data.fake_input).focus()}),t(d.fake_input).bind("focus",d,function(a){t(a.data.fake_input).val()==t(a.data.fake_input).attr("data-default")&&t(a.data.fake_input).val(""),t(a.data.fake_input).css("color","#000000")}),void 0!=r.autocomplete_url){autocomplete_options={source:r.autocomplete_url};for(attrname in r.autocomplete)autocomplete_options[attrname]=r.autocomplete[attrname];void 0!==jQuery.Autocompleter?(t(d.fake_input).autocomplete(r.autocomplete_url,r.autocomplete),t(d.fake_input).bind("result",d,function(a,e,n){e&&t("#"+i).addTag(e[0]+"",{focus:!0,unique:r.unique})})):void 0!==jQuery.ui.autocomplete&&(t(d.fake_input).autocomplete(autocomplete_options),t(d.fake_input).bind("autocompleteselect",d,function(a,e){return t(a.data.real_input).addTag(e.item.value,{focus:!0,unique:r.unique}),!1}))}else t(d.fake_input).bind("blur",d,function(a){var e=t(this).attr("data-default");return""!=t(a.data.fake_input).val()&&t(a.data.fake_input).val()!=e?a.data.minChars<=t(a.data.fake_input).val().length&&(!a.data.maxChars||a.data.maxChars>=t(a.data.fake_input).val().length)&&t(a.data.real_input).addTag(t(a.data.fake_input).val(),{focus:!0,unique:r.unique}):(t(a.data.fake_input).val(t(a.data.fake_input).attr("data-default")),t(a.data.fake_input).css("color",r.placeholderColor)),!1});t(d.fake_input).bind("keypress",d,function(a){if(n(a))return a.preventDefault(),a.data.minChars<=t(a.data.fake_input).val().length&&(!a.data.maxChars||a.data.maxChars>=t(a.data.fake_input).val().length)&&t(a.data.real_input).addTag(t(a.data.fake_input).val(),{focus:!0,unique:r.unique}),t(a.data.fake_input).resetAutosize(r),!1;a.data.autosize&&t(a.data.fake_input).doAutosize(r)}),d.removeWithBackspace&&t(d.fake_input).bind("keydown",function(a){if(8==a.keyCode&&""==t(this).val()){a.preventDefault();var e=t(this).closest(".tagsinput").find(".tag:last").text(),i=t(this).attr("id").replace(/_tag$/,"");e=e.replace(/[\s]+x$/,""),t("#"+i).removeTag(escape(e)),t(this).trigger("focus")}}),t(d.fake_input).blur(),d.unique&&t(d.fake_input).keydown(function(a){(8==a.keyCode||String.fromCharCode(a.which).match(/\w+|[áéíóúÁÉÍÓÚñÑ,\/]+/))&&t(this).removeClass("not_valid")})}}}),this},t.fn.tagsInput.updateTagsField=function(e,i){var n=t(e).attr("id");t(e).val(i.join(a[n]))},t.fn.tagsInput.importTags=function(n,r){t(n).val("");var u=t(n).attr("id"),d=r.split(a[u]);for(i=0;i<d.length;i++)t(n).addTag(d[i],{focus:!1,callback:!1});if(e[u]&&e[u].onChange){e[u].onChange.call(n,n,d[i])}};var n=function(a){var e=!1;return 13==a.which||("string"==typeof a.data.delimiter?a.which==a.data.delimiter.charCodeAt(0)&&(e=!0):t.each(a.data.delimiter,function(t,i){a.which==i.charCodeAt(0)&&(e=!0)}),e)}}(jQuery);