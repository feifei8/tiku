var Util=require("./util.js"),init=!1,ValuesWidget={getOption:function($container){var initValue;try{try{initValue=JSON.parse($container.attr("data-values-widget"))}catch(e){eval("initValue = "+$container.attr("data-values-widget"))}}catch(t){initValue={}}return $.extend({},initValue)},init:function(){if(init)return void alert("ValuesWidget already init");init=!0;var calcValues=function(t){var e=[];t.find(".item input").each(function(t,a){e.push($(a).val())}),console.log(e),t.find("[data-values]").val(JSON.stringify(e))};$(document).on("change","[data-values-widget] input[type=text]",function(){var t=$(this).closest("[data-values-widget]");calcValues(t)}),$(document).on("click","[data-values-widget] .add",function(){var t=$(this).closest("[data-values-widget]"),e=(ValuesWidget.getOption(t),['<div class="item">','<input type="text" value="" />','<a class="delete" href="javascript:;">x</a>',"</div>"].join(""));return t.find(".list").append(e),t.find("input").trigger("change"),!1}),$(document).on("click","[data-values-widget] .delete",function(){var t=$(this).closest("[data-values-widget]");return $(this).closest(".item").remove(),calcValues(t),!1});var ValuesWidgetForJquery=function(obj){var $container=$(obj);this.init=function(){var currentValue=[];try{currentValue=JSON.parse($container.find("[data-values]").val())}catch(e){try{eval("value = "+$container.find("[data-values]").val())}catch(t){currentValue=[]}}this.val(currentValue)},this.val=function(value){var currentValue=[];try{currentValue=JSON.parse($container.find("[data-values]").val())}catch(e){try{eval("value = "+$container.find("[data-values]").val())}catch(t){currentValue=[]}}if(void 0===value)return currentValue;$container.find(".list").find(".item").remove();for(var $html,i=0;i<value.length;i++)$html=$(['<div class="item">','<input type="text" value="" />','<a class="delete" href="javascript:;">x</a>',"</div>"].join("")),$html.find("input").val(value[i]),$container.find(".list").append($html);$container.find("[data-values]").val(JSON.stringify(value))}};$.fn.extend({valuesWidget:function(){return new ValuesWidgetForJquery(this)}}),$(function(){$("[data-values-widget]").valuesWidget().init()})}};module.exports=ValuesWidget;