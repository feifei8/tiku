!function(e){"use strict";var t,a={bridge:null,version:"0.0.0",pluginType:"unknown",disabled:null,outdated:null,unavailable:null,deactivated:null,overdue:null,ready:null},n={},r=null,i=0,o={},l=0,s={},c=function(){var e,t,a,n,r="ZeroClipboard.swf";if(!document.currentScript||!(n=document.currentScript.src)){var i=document.getElementsByTagName("script");if("readyState"in i[0])for(e=i.length;e--&&("interactive"!==i[e].readyState||!(n=i[e].src)););else if("loading"===document.readyState)n=i[i.length-1].src;else{for(e=i.length;e--;){if(!(a=i[e].src)){t=null;break}if(a=a.split("#")[0].split("?")[0],a=a.slice(0,a.lastIndexOf("/")+1),null==t)t=a;else if(t!==a){t=null;break}}null!==t&&(n=t)}}return n&&(n=n.split("#")[0].split("?")[0],r=n.slice(0,n.lastIndexOf("/")+1)+r),r}(),u=function(){var e=/\-([a-z])/g,t=function(e,t){return t.toUpperCase()};return function(a){return a.replace(e,t)}}(),d=function(t,a){var n,r;return e.getComputedStyle?n=e.getComputedStyle(t,null).getPropertyValue(a):(r=u(a),n=t.currentStyle?t.currentStyle[r]:t.style[r]),"cursor"!==a||n&&"auto"!==n||"a"!==t.tagName.toLowerCase()?n:"pointer"},f=function(t){t||(t=e.event);var a;this!==e?a=this:t.target?a=t.target:t.srcElement&&(a=t.srcElement),F.activate(a)},p=function(e,t,a){e&&1===e.nodeType&&(e.addEventListener?e.addEventListener(t,a,!1):e.attachEvent&&e.attachEvent("on"+t,a))},h=function(e,t,a){e&&1===e.nodeType&&(e.removeEventListener?e.removeEventListener(t,a,!1):e.detachEvent&&e.detachEvent("on"+t,a))},v=function(e,t){if(!e||1!==e.nodeType)return e;if(e.classList)return e.classList.contains(t)||e.classList.add(t),e;if(t&&"string"==typeof t){var a=(t||"").split(/\s+/);if(1===e.nodeType)if(e.className){for(var n=" "+e.className+" ",r=e.className,i=0,o=a.length;i<o;i++)n.indexOf(" "+a[i]+" ")<0&&(r+=" "+a[i]);e.className=r.replace(/^\s+|\s+$/g,"")}else e.className=t}return e},y=function(e,t){if(!e||1!==e.nodeType)return e;if(e.classList)return e.classList.contains(t)&&e.classList.remove(t),e;if(t&&"string"==typeof t||void 0===t){var a=(t||"").split(/\s+/);if(1===e.nodeType&&e.className)if(t){for(var n=(" "+e.className+" ").replace(/[\n\t]/g," "),r=0,i=a.length;r<i;r++)n=n.replace(" "+a[r]+" "," ");e.className=n.replace(/^\s+|\s+$/g,"")}else e.className=""}return e},g=function(){var e,t,a,n=1;return"function"==typeof document.body.getBoundingClientRect&&(e=document.body.getBoundingClientRect(),t=e.right-e.left,a=document.body.offsetWidth,n=Math.round(t/a*100)/100),n},m=function(t,a){var n={left:0,top:0,width:0,height:0,zIndex:T(a)-1};if(t.getBoundingClientRect){var r,i,o,l=t.getBoundingClientRect();"pageXOffset"in e&&"pageYOffset"in e?(r=e.pageXOffset,i=e.pageYOffset):(o=g(),r=Math.round(document.documentElement.scrollLeft/o),i=Math.round(document.documentElement.scrollTop/o));var s=document.documentElement.clientLeft||0,c=document.documentElement.clientTop||0;n.left=l.left+r-s,n.top=l.top+i-c,n.width="width"in l?l.width:l.right-l.left,n.height="height"in l?l.height:l.bottom-l.top}return n},b=function(e,t){return null==t||t&&!0===t.cacheBust?(-1===e.indexOf("?")?"?":"&")+"noCache="+(new Date).getTime():""},w=function(t){var a,n,r,i,o="",l=[];if(t.trustedDomains&&("string"==typeof t.trustedDomains?i=[t.trustedDomains]:"object"==typeof t.trustedDomains&&"length"in t.trustedDomains&&(i=t.trustedDomains)),i&&i.length)for(a=0,n=i.length;a<n;a++)if(i.hasOwnProperty(a)&&i[a]&&"string"==typeof i[a]){if(!(r=z(i[a])))continue;if("*"===r){l=[r];break}l.push.apply(l,[r,"//"+r,e.location.protocol+"//"+r])}return l.length&&(o+="trustedOrigins="+encodeURIComponent(l.join(","))),!0===t.forceEnhancedClipboard&&(o+=(o?"&":"")+"forceEnhancedClipboard=true"),o},x=function(e,t,a){if("function"==typeof t.indexOf)return t.indexOf(e,a);var n,r=t.length;for(void 0===a?a=0:a<0&&(a=r+a),n=a;n<r;n++)if(t.hasOwnProperty(n)&&t[n]===e)return n;return-1},C=function(e){if("string"==typeof e)throw new TypeError("ZeroClipboard doesn't accept query strings.");return"number"!=typeof e.length?[e]:e},O=function(t,a,n,r){r?e.setTimeout(function(){t.apply(a,n)},0):t.apply(a,n)},T=function(e){var t,a;return e&&("number"==typeof e&&e>0?t=e:"string"==typeof e&&(a=parseInt(e,10))&&!isNaN(a)&&a>0&&(t=a)),t||("number"==typeof M.zIndex&&M.zIndex>0?t=M.zIndex:"string"==typeof M.zIndex&&(a=parseInt(M.zIndex,10))&&!isNaN(a)&&a>0&&(t=a)),t||0},D=function(){var e,t,a,n,r,i=arguments[0]||{};for(e=1,t=arguments.length;e<t;e++)if(null!=(a=arguments[e]))for(n in a)if(a.hasOwnProperty(n)){if(i[n],r=a[n],i===r)continue;void 0!==r&&(i[n]=r)}return i},z=function(e){if(null==e||""===e)return null;if(""===(e=e.replace(/^\s+|\s+$/g,"")))return null;var t=e.indexOf("//");e=-1===t?e:e.slice(t+2);var a=e.indexOf("/");return e=-1===a?e:-1===t||0===a?null:e.slice(0,a),e&&".swf"===e.slice(-4).toLowerCase()?null:e||null},k=function(){var e=function(e,t){var a,n,r;if(null!=e&&"*"!==t[0]&&("string"==typeof e&&(e=[e]),"object"==typeof e&&"number"==typeof e.length))for(a=0,n=e.length;a<n;a++)if(e.hasOwnProperty(a)&&(r=z(e[a]))){if("*"===r){t.length=0,t.push("*");break}-1===x(r,t)&&t.push(r)}};return function(t,a){var n=z(a.swfPath);null===n&&(n=t);var r=[];e(a.trustedOrigins,r),e(a.trustedDomains,r);var i=r.length;if(i>0){if(1===i&&"*"===r[0])return"always";if(-1!==x(t,r))return 1===i&&t===n?"sameDomain":"always"}return"never"}}(),N=function(e){if(null==e)return[];if(Object.keys)return Object.keys(e);var t=[];for(var a in e)e.hasOwnProperty(a)&&t.push(a);return t},E=function(e){if(e)for(var t in e)e.hasOwnProperty(t)&&delete e[t];return e},I=function(){try{return document.activeElement}catch(e){}return null},P=function(e,t){for(var a={},n=0,r=t.length;n<r;n++)t[n]in e&&(a[t[n]]=e[t[n]]);return a},j=function(e,t){var a={};for(var n in e)-1===x(n,t)&&(a[n]=e[n]);return a},L=function(e){var t={},a={};if("object"==typeof e&&e){for(var n in e)if(n&&e.hasOwnProperty(n)&&"string"==typeof e[n]&&e[n])switch(n.toLowerCase()){case"text/plain":case"text":case"air:text":case"flash:text":t.text=e[n],a.text=n;break;case"text/html":case"html":case"air:html":case"flash:html":t.html=e[n],a.html=n;break;case"application/rtf":case"text/rtf":case"rtf":case"richtext":case"air:rtf":case"flash:rtf":t.rtf=e[n],a.rtf=n}return{data:t,formatMap:a}}},S=function(e,t){if("object"!=typeof e||!e||"object"!=typeof t||!t)return e;var a={};for(var n in e)if(e.hasOwnProperty(n)){if("success"!==n&&"data"!==n){a[n]=e[n];continue}a[n]={};var r=e[n];for(var i in r)i&&r.hasOwnProperty(i)&&t.hasOwnProperty(i)&&(a[n][t[i]]=r[i])}return a},A=function(e){return function(t){return e.call(t,0)}}(e.Array.prototype.slice);!function(){function e(e){var t=e.match(/[\d]+/g);return t.length=3,t.join(".")}function t(e){return!!e&&(e=e.toLowerCase())&&(/^(pepflashplayer\.dll|libpepflashplayer\.so|pepperflashplayer\.plugin)$/.test(e)||"chrome.plugin"===e.slice(-13))}function n(a){a&&(l=!0,a.version&&(u=e(a.version)),!u&&a.description&&(u=e(a.description)),a.filename&&(c=t(a.filename)))}var r,i,o,l=!1,s=!1,c=!1,u="";if(navigator.plugins&&navigator.plugins.length)r=navigator.plugins["Shockwave Flash"],n(r),navigator.plugins["Shockwave Flash 2.0"]&&(l=!0,u="2.0.0.11");else if(navigator.mimeTypes&&navigator.mimeTypes.length)o=navigator.mimeTypes["application/x-shockwave-flash"],r=o&&o.enabledPlugin,n(r);else if("undefined"!=typeof ActiveXObject){s=!0;try{i=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7"),l=!0,u=e(i.GetVariable("$version"))}catch(t){try{i=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6"),l=!0,u="6.0.21"}catch(t){try{i=new ActiveXObject("ShockwaveFlash.ShockwaveFlash"),l=!0,u=e(i.GetVariable("$version"))}catch(e){s=!1}}}}a.disabled=!0!==l,a.outdated=u&&parseFloat(u)<11,a.version=u||"0.0.0",a.pluginType=c?"pepper":s?"activex":l?"netscape":"unknown"}();var F=function(e){if(!(this instanceof F))return new F(e);if(this.id=""+i++,o[this.id]={instance:this,elements:[],handlers:{}},e&&this.clip(e),"boolean"!=typeof a.ready&&(a.ready=!1),!F.isFlashUnusable()&&null===a.bridge){var t=this,n=M.flashLoadTimeout;"number"==typeof n&&n>=0&&setTimeout(function(){"boolean"!=typeof a.deactivated&&(a.deactivated=!0),!0===a.deactivated&&F.emit({type:"error",name:"flash-deactivated",client:t})},n),a.overdue=!1,$()}};F.prototype.setText=function(e){return F.setData("text/plain",e),this},F.prototype.setHtml=function(e){return F.setData("text/html",e),this},F.prototype.setRichText=function(e){return F.setData("application/rtf",e),this},F.prototype.setData=function(){return F.setData.apply(F,A(arguments)),this},F.prototype.clearData=function(){return F.clearData.apply(F,A(arguments)),this},F.prototype.setSize=function(e,t){return U(e,t),this};var B=function(e){!0===a.ready&&a.bridge&&"function"==typeof a.bridge.setHandCursor?a.bridge.setHandCursor(e):a.ready=!1};F.prototype.destroy=function(){this.unclip(),this.off(),delete o[this.id]};var H=function(){var e,t,a,n=[],r=N(o);for(e=0,t=r.length;e<t;e++)(a=o[r[e]].instance)&&a instanceof F&&n.push(a);return n};F.version="2.0.0-beta.5";var M={swfPath:c,trustedDomains:e.location.host?[e.location.host]:[],cacheBust:!0,forceHandCursor:!1,forceEnhancedClipboard:!1,zIndex:999999999,debug:!1,title:null,autoActivate:!0,flashLoadTimeout:3e4};F.isFlashUnusable=function(){return!!(a.disabled||a.outdated||a.unavailable||a.deactivated)},F.config=function(e){"object"==typeof e&&null!==e&&D(M,e);{if("string"!=typeof e||!e){var t={};for(var a in M)M.hasOwnProperty(a)&&("object"==typeof M[a]&&null!==M[a]?"length"in M[a]?t[a]=M[a].slice(0):t[a]=D({},M[a]):t[a]=M[a]);return t}if(M.hasOwnProperty(e))return M[e]}},F.destroy=function(){F.deactivate();for(var e in o)if(o.hasOwnProperty(e)&&o[e]){var t=o[e].instance;t&&"function"==typeof t.destroy&&t.destroy()}var n=a.bridge;if(n){var r=X(n);r&&("activex"===a.pluginType&&"readyState"in n?(n.style.display="none",function e(){if(4===n.readyState){for(var t in n)"function"==typeof n[t]&&(n[t]=null);n.parentNode.removeChild(n),r.parentNode&&r.parentNode.removeChild(r)}else setTimeout(e,10)}()):(n.parentNode.removeChild(n),r.parentNode&&r.parentNode.removeChild(r))),a.ready=null,a.bridge=null,a.deactivated=null}F.clearData()},F.activate=function(e){t&&(y(t,M.hoverClass),y(t,M.activeClass)),t=e,v(e,M.hoverClass),Z();var n=M.title||e.getAttribute("title");if(n){var r=X(a.bridge);r&&r.setAttribute("title",n)}var i=!0===M.forceHandCursor||"pointer"===d(e,"cursor");B(i)},F.deactivate=function(){var e=X(a.bridge);e&&(e.removeAttribute("title"),e.style.left="0px",e.style.top="-9999px",U(1,1)),t&&(y(t,M.hoverClass),y(t,M.activeClass),t=null)},F.state=function(){return{browser:P(e.navigator,["userAgent","platform","appName"]),flash:j(a,["bridge"]),zeroclipboard:{version:F.version,config:F.config()}}},F.setData=function(e,t){var a;if("object"==typeof e&&e&&void 0===t)a=e,F.clearData();else{if("string"!=typeof e||!e)return;a={},a[e]=t}for(var r in a)r&&a.hasOwnProperty(r)&&"string"==typeof a[r]&&a[r]&&(n[r]=a[r])},F.clearData=function(e){void 0===e?(E(n),r=null):"string"==typeof e&&n.hasOwnProperty(e)&&delete n[e]};var $=function(){var t,n,r=document.getElementById("global-zeroclipboard-html-bridge");if(!r){var i=k(e.location.host,M),o="never"===i?"none":"all",l=w(M),s=M.swfPath+b(M.swfPath,M);r=R();var c=document.createElement("div");r.appendChild(c),document.body.appendChild(r);var u=document.createElement("div"),d="activex"===a.pluginType;u.innerHTML='<object id="global-zeroclipboard-flash-bridge" name="global-zeroclipboard-flash-bridge" width="100%" height="100%" '+(d?'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"':'type="application/x-shockwave-flash" data="'+s+'"')+">"+(d?'<param name="movie" value="'+s+'"/>':"")+'<param name="allowScriptAccess" value="'+i+'"/><param name="allowNetworking" value="'+o+'"/><param name="menu" value="false"/><param name="wmode" value="transparent"/><param name="flashvars" value="'+l+'"/></object>',t=u.firstChild,u=null,t.ZeroClipboard=F,r.replaceChild(t,c)}t||(t=document["global-zeroclipboard-flash-bridge"],t&&(n=t.length)&&(t=t[n-1]),t||(t=r.firstChild)),a.bridge=t||null},R=function(){var e=document.createElement("div");return e.id="global-zeroclipboard-html-bridge",e.className="global-zeroclipboard-container",e.style.position="absolute",e.style.left="0px",e.style.top="-9999px",e.style.width="1px",e.style.height="1px",e.style.zIndex=""+T(M.zIndex),e},X=function(e){for(var t=e&&e.parentNode;t&&"OBJECT"===t.nodeName&&t.parentNode;)t=t.parentNode;return t||null},Z=function(){if(t){var e=m(t,M.zIndex),n=X(a.bridge);n&&(n.style.top=e.top+"px",n.style.left=e.left+"px",n.style.width=e.width+"px",n.style.height=e.height+"px",n.style.zIndex=e.zIndex+1),U(e.width,e.height)}},U=function(e,t){var n=X(a.bridge);n&&(n.style.width=e+"px",n.style.height=t+"px")};F.emit=function(t){var i,o,l,s,c,u,d,f,p;if("string"==typeof t&&t&&(i=t),"object"==typeof t&&t&&"string"==typeof t.type&&t.type&&(i=t.type,o=t),i){if(t=J(i,o),q(t),"ready"===t.type&&!0===a.overdue)return F.emit({type:"error",name:"flash-overdue"});if(l=!/^(before)?copy$/.test(t.type),t.client)V.call(t.client,t,l);else for(s=t.target&&t.target!==e&&!0===M.autoActivate?W(t.target):H(),c=0,u=s.length;c<u;c++)d=D({},t,{client:s[c]}),V.call(s[c],d,l);return"copy"===t.type&&(p=L(n),f=p.data,r=p.formatMap),f}};var V=function(t,a){var n=o[this.id]&&o[this.id].handlers[t.type];if(n&&n.length){var r,i,l,s,c=this;for(r=0,i=n.length;r<i;r++)l=n[r],s=c,"string"==typeof l&&"function"==typeof e[l]&&(l=e[l]),"object"==typeof l&&l&&"function"==typeof l.handleEvent&&(s=l,l=l.handleEvent),"function"==typeof l&&O(l,s,[t],a)}return this},G={ready:"Flash communication is established",error:{"flash-disabled":"Flash is disabled or not installed","flash-outdated":"Flash is too outdated to support ZeroClipboard","flash-unavailable":"Flash is unable to communicate bidirectionally with JavaScript","flash-deactivated":"Flash is too outdated for your browser and/or is configured as click-to-activate","flash-overdue":"Flash communication was established but NOT within the acceptable time limit"}},J=function(e,n){if(e||n&&n.type){n=n||{},e=(e||n.type).toLowerCase(),D(n,{type:e,target:n.target||t||null,relatedTarget:n.relatedTarget||null,currentTarget:a&&a.bridge||null});var i=G[n.type];return"error"===n.type&&n.name&&i&&(i=i[n.name]),i&&(n.message=i),"ready"===n.type&&D(n,{target:null,version:a.version}),"error"===n.type&&(n.target=null,/^flash-(outdated|unavailable|deactivated|overdue)$/.test(n.name)&&D(n,{version:a.version,minimumVersion:"11.0.0"})),"copy"===n.type&&(n.clipboardData={setData:F.setData,clearData:F.clearData}),"aftercopy"===n.type&&(n=S(n,r)),n.target&&!n.relatedTarget&&(n.relatedTarget=Y(n.target)),n}},Y=function(e){var t=e&&e.getAttribute&&e.getAttribute("data-clipboard-target");return t?document.getElementById(t):null},q=function(e){var r=e.target||t;switch(e.type){case"error":x(e.name,["flash-disabled","flash-outdated","flash-deactivated","flash-overdue"])&&D(a,{disabled:"flash-disabled"===e.name,outdated:"flash-outdated"===e.name,unavailable:"flash-unavailable"===e.name,deactivated:"flash-deactivated"===e.name,overdue:"flash-overdue"===e.name,ready:!1});break;case"ready":var i=!0===a.deactivated;D(a,{disabled:!1,outdated:!1,unavailable:!1,deactivated:!1,overdue:i,ready:!i});break;case"copy":var o,l,s=e.relatedTarget;!n["text/html"]&&!n["text/plain"]&&s&&(l=s.value||s.outerHTML||s.innerHTML)&&(o=s.value||s.textContent||s.innerText)?(e.clipboardData.clearData(),e.clipboardData.setData("text/plain",o),l!==o&&e.clipboardData.setData("text/html",l)):!n["text/plain"]&&e.target&&(o=e.target.getAttribute("data-clipboard-text"))&&(e.clipboardData.clearData(),e.clipboardData.setData("text/plain",o));break;case"aftercopy":F.clearData(),r&&r!==I()&&r.focus&&r.focus();break;case"mouseover":v(r,M.hoverClass);break;case"mouseout":!0===M.autoActivate&&F.deactivate();break;case"mousedown":v(r,M.activeClass);break;case"mouseup":y(r,M.activeClass)}};F.prototype.on=function(e,t){var n,r,i,l={},s=o[this.id]&&o[this.id].handlers;if("string"==typeof e&&e)i=e.toLowerCase().split(/\s+/);else if("object"==typeof e&&e&&void 0===t)for(n in e)e.hasOwnProperty(n)&&"string"==typeof n&&n&&"function"==typeof e[n]&&this.on(n,e[n]);if(i&&i.length){for(n=0,r=i.length;n<r;n++)e=i[n].replace(/^on/,""),l[e]=!0,s[e]||(s[e]=[]),s[e].push(t);if(l.ready&&a.ready&&F.emit({type:"ready",client:this}),l.error){var c=["disabled","outdated","unavailable","deactivated","overdue"];for(n=0,r=c.length;n<r;n++)if(a[c[n]]){F.emit({type:"error",name:"flash-"+c[n],client:this});break}}}return this},F.prototype.off=function(e,t){var a,n,r,i,l,s=o[this.id]&&o[this.id].handlers;if(0===arguments.length)i=N(s);else if("string"==typeof e&&e)i=e.split(/\s+/);else if("object"==typeof e&&e&&void 0===t)for(a in e)e.hasOwnProperty(a)&&"string"==typeof a&&a&&"function"==typeof e[a]&&this.off(a,e[a]);if(i&&i.length)for(a=0,n=i.length;a<n;a++)if(e=i[a].toLowerCase().replace(/^on/,""),(l=s[e])&&l.length)if(t)for(r=x(t,l);-1!==r;)l.splice(r,1),r=x(t,l,r);else s[e].length=0;return this},F.prototype.handlers=function(e){var t,a=null,n=o[this.id]&&o[this.id].handlers;if(n){if("string"==typeof e&&e)return n[e]?n[e].slice(0):null;a={};for(t in n)n.hasOwnProperty(t)&&n[t]&&(a[t]=n[t].slice(0))}return a},F.prototype.clip=function(e){e=C(e);for(var t=0;t<e.length;t++)if(e.hasOwnProperty(t)&&e[t]&&1===e[t].nodeType){e[t].zcClippingId?-1===x(this.id,s[e[t].zcClippingId])&&s[e[t].zcClippingId].push(this.id):(e[t].zcClippingId="zcClippingId_"+l++,s[e[t].zcClippingId]=[this.id],!0===M.autoActivate&&p(e[t],"mouseover",f));var a=o[this.id].elements;-1===x(e[t],a)&&a.push(e[t])}return this},F.prototype.unclip=function(e){var t=o[this.id];if(!t)return this;var a,n=t.elements;e=void 0===e?n.slice(0):C(e);for(var r=e.length;r--;)if(e.hasOwnProperty(r)&&e[r]&&1===e[r].nodeType){for(a=0;-1!==(a=x(e[r],n,a));)n.splice(a,1);var i=s[e[r].zcClippingId];if(i){for(a=0;-1!==(a=x(this.id,i,a));)i.splice(a,1);0===i.length&&(!0===M.autoActivate&&h(e[r],"mouseover",f),delete e[r].zcClippingId)}}return this},F.prototype.elements=function(){var e=o[this.id];return e&&e.elements?e.elements.slice(0):[]};var W=function(e){var t,a,n,r,i,l=[];if(e&&1===e.nodeType&&(t=e.zcClippingId)&&s.hasOwnProperty(t)&&(a=s[t])&&a.length)for(n=0,r=a.length;n<r;n++)(i=o[a[n]].instance)&&i instanceof F&&l.push(i);return l};M.hoverClass="zeroclipboard-is-hover",M.activeClass="zeroclipboard-is-active","function"==typeof define&&define.amd?define(function(){return F}):"object"==typeof module&&module&&"object"==typeof module.exports&&module.exports?module.exports=F:e.ZeroClipboard=F,e.ZeroClipboard=F}(function(){return this}());