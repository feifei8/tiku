var Util=require("./util.js"),init=!1,AttrWidget={getOption:function($container){var initValue;try{try{initValue=JSON.parse($container.attr("data-attr-widget"))}catch(e){eval("initValue = "+$container.attr("data-attr-widget"))}}catch(t){initValue={}}return $.extend({},initValue)},init:function(){if(init)return void alert("AttrWidget already init");init=!0;var calcValues=function(t){var a=[];t.find(".item").each(function(t,e){a.push({name:$(e).find(".name").val(),value:$(e).find(".value").val()})}),t.find("[data-attr]").val(JSON.stringify(a)),t.trigger("attr-widget.change",[a])};$(document).on("change","[data-attr-widget] input[type=text]",function(){var t=$(this).closest("[data-attr-widget]");calcValues(t)}),$(document).on("click","[data-attr-widget] .add",function(){var t=$(this).closest("[data-attr-widget]"),a=(AttrWidget.getOption(t),['<div class="item">','<input type="text" class="name" value="" />',"<span>:</span>",'<input type="text" class="value" value="" />','<a class="delete" href="javascript:;">x</a>',"</div>"].join(""));return t.find(".list").append(a),calcValues(t),!1}),$(document).on("click","[data-attr-widget] .delete",function(){var t=$(this).closest("[data-attr-widget]");return $(this).closest(".item").remove(),calcValues(t),!1});var AttrWidgetForJquery=function(obj){var $container=$(obj);this.init=function(){var currentValue=[];try{currentValue=JSON.parse($container.find("[data-attr]").val())}catch(e){try{eval("currentValue = "+$container.find("[data-attr]").val())}catch(t){currentValue=[]}}this.val(currentValue)},this.val=function(value){var currentValue=[];try{currentValue=JSON.parse($container.find("[data-attr]").val())}catch(e){try{eval("value = "+$container.find("[data-attr]").val())}catch(t){currentValue=[]}}if(void 0===value)return currentValue;$container.find(".list").html("");for(var $html,i=0;i<value.length;i++)$html=$(['<div class="item">','<input type="text" class="name" value="" />',"<span>:</span>",'<input type="text" class="value" value="" />','<a class="delete" href="javascript:;">x</a>',"</div>"].join("")),$html.find("input.name").val(value[i].name),$html.find("input.value").val(value[i].value),$container.find(".list").append($html);$container.find("[data-attr]").val(JSON.stringify(value)),$container.trigger("attr-widget.change",[value])}};$.fn.extend({attrWidget:function(){return new AttrWidgetForJquery(this)}}),$(function(){$("[data-attr-widget]").attrWidget().init()})}};module.exports=AttrWidget;