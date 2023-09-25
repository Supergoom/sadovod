const MAX_URL_LEN=1024,MAX_TITLE_LEN=512,MAX_LEN_PATH=128,TIMEOUT_CLICK=50,TIMEOUT_SAME_CLICKS=1e3,DELTA_SAME_CLICKS=2,CHECK_URL_PARAM="_ym_status-check",BATTERY_INFO="bt",HID_NAME="hitId",NET_MAP=["other","none","unknown","wifi","ethernet","bluetooth","cellular","wimax","mixed"],TAGS_MAP=["A","B","BIG","BODY","BUTTON","DD","DIV","DL","DT","EM","FIELDSET","FORM","H1","H2","H3","H4","H5","H6","HR","I","IMG","INPUT","LI","OL","P","PRE","SELECT","SMALL","SPAN","STRONG","SUB","SUP","TABLE","TBODY","TD","TEXTAREA","TFOOT","TH","THEAD","TR","U","UL","ABBR","AREA","BLOCKQUOTE","CAPTION","CENTER","CITE","CODE","CANVAS","DFN","EMBED","FONT","INS","KBD","LEGEND","LABEL","MAP","OBJECT","Q","S","SAMP","STRIKE","TT","ARTICLE","AUDIO","ASIDE","FOOTER","HEADER","MENU","METER","NAV","PROGRESS","SECTION","TIME","VIDEO","NOINDEX","NOBR","MAIN","svg","circle","clippath","ellipse","defs","foreignobject","g","glyph","glyphref","image","line","lineargradient","marker","mask","path","pattern","polygon","polyline","radialgradient","rect","set","text","textpath","title"];var metrikaStorage={id:0,loaded:!1,"page-url":prepareUrl(getPageUrl()),"page-ref":prepareUrl(getReferrer())};function sendHit(e,t,n){hitExt("watch",{browserInfo:{pv:!0,ar:!0},counterId:e,pageParams:{referrer:t&&void 0!==t.referrer?t.referrer:getReferrer(),title:t&&void 0!==t.title?t.title:getTitle(),url:t&&void 0!==t.url?t.url:getPageUrl()},params:n})}function sendClick(e,t,n){const r=getElementPosition(t),a=getElementXY(t.target),o=getElementSize(t.target),i=[],[c,d]=getPerformanceInfo();addParam(i,"rn",getRandom()),addParam(i,"x",Math.floor(65535*(r.x-a.left)/(o.width||1))),addParam(i,"y",Math.floor(65535*(r.y-a.top)/(o.height||1))),addParam(i,"t",Math.floor(d()/100)),addParam(i,"p",getElementPath(t.target)),addParam(i,"X",r.x),addParam(i,"Y",r.y),hitExt("clmap",{browserInfo:{},counterId:e,pageParams:{},params:n},{"pointer-click":i.join(":"),"force-urlencoded":1})}function reachGoal(e,t,n){var r,a;t?(r=getPageUrl(),a=`goal://${getHost()}/${t}`):(r=getReferrer(),a=getPageUrl()),hitExt("watch",{browserInfo:{ar:!0},counterId:e,pageParams:{referrer:r,url:a},params:n})}function sendFile(e,t,n){t&&hitExt("watch",{browserInfo:{ar:!0,dl:!0,ln:!0},counterId:e,pageParams:{referrer:getReferrer(),title:n,url:t}})}function sendParams(e,t){t&&hitExt("watch",{browserInfo:{ar:!0,pa:!0},counterId:e,pageParams:{},params:t})}function userParams(e,t){t&&hitExt("watch",{browserInfo:{ar:!0,pa:!0},counterId:e,pageParams:{},params:{__ymu:t}})}function notBounce(e,t){hitExt("watch",{browserInfo:{ar:!0,nb:!0},counterId:e,pageParams:{},params:t})}function hitExt(e,t,n){const{browserInfo:r,counterId:a,pageParams:o,params:i}=t,c={"browser-info":getBrowserInfo(a,r,o.title),rn:getRandom(),ut:o.ut};if(o.url&&(c["page-url"]=prepareUrl(o.url)),o.referrer&&(c["page-ref"]=prepareUrl(o.referrer)),i&&(c["site-info"]=JSON.stringify(i)),n)for(var d in n)c[d]=n[d];sendData(e,a,c)}function sendData(e,t,n){const r="https://mc.yandex.ru/"+e+"/"+t+"?"+queryStringify(n);"undefined"!=typeof navigator&&navigator.sendBeacon&&navigator.sendBeacon(r," ")||("undefined"!=typeof fetch?fetch(r,{credentials:"include"}).catch(function(){}):"undefined"!=typeof Image&&((new Image).src=r))}function queryStringify(e){return Object.keys(e).filter(t=>e[t]||0===e[t]).map(t=>encodeURIComponent(t)+"="+encodeURIComponent(e[t])).join("&")}function prepareUrl(e){return truncate(e,MAX_URL_LEN)}function addParam(e,t,n){(n||0===n)&&e.push(t+":"+(!0===n?"1":n))}function getBrowserInfo(e,t,n){const r=[];t&&Object.keys(t).forEach(e=>addParam(r,e,t[e])),!0!==metrikaStorage.loaded&&(addParam(r,"cn","1"),addParam(r,"en",getDocumentEncoding()),addParam(r,"la",getDocumentLanguage()),addParam(r,"hid",getHid()),addParam(r,"fu",isFalseURL()),addParam(r,"rn",getRandom()),addParam(r,"c",cookieEnabled()),addParam(r,"s",getScreenSize()),addParam(r,"dp",getDesktopFlag()),addParam(r,"nt",getNetType()),addParam(r,"ns",getNavigationStart()),addParam(r,"fp",getFirstPaintTime()),addParam(r,"t",truncate(n,MAX_TITLE_LEN)),metrikaStorage.id=e,metrikaStorage.loaded=!0),addParam(r,"v","1030"),addParam(r,"v","10ym9geic8i73flogxj2lsv"),addParam(r,"w",getClientSize()),addParam(r,"u",getClientID()),addParam(r,"i",getClientTime()),addParam(r,"z",getClientTimeZone());const a=getSeconds();return addParam(r,"et",a),addParam(r,"st",a),r.join(":")}function getHost(){return window&&window.location?window.location.hostname:""}function getPageUrl(){return window&&window.location?window.location.href:""}function getReferrer(){return document?document.referrer:""}function getTitle(){return document?document.title:""}function cookieEnabled(){return!!navigator&&navigator.cookieEnabled}function getScreenSize(){return screen?[screen.width,screen.height,screen.colorDepth].join("x"):""}function getClientSize(){return window?[window.innerWidth,window.innerHeight].join("x"):""}function getClientTime(){const e=new Date;return[e.getFullYear(),e.getMonth()+1,e.getDate(),e.getHours(),e.getMinutes(),e.getSeconds()].map(function(e){return e.toString().padStart(2,"0")}).join("")}function getClientTimeZone(){return-(new Date).getTimezoneOffset()}function getClientID(){var e=getCookie("_ym_uid");return e||setCookie("_ym_uid",e=""+Date.now()+Math.floor(Math.floor(1e6*Math.random()))),e}function getPerformanceInfo(){const e=window.performance||window.webkitPerformance;if(e){const n=e.timing.navigationStart;var t=e.now;return t&&(t=t.bind(e)),[n,t]}return!1}function getNavigationStart(){const e=getPerformanceInfo();return e||!1}function getFirstPaintTime(){const e=window.performance||window.webkitPerformance,[t]=getPerformanceInfo();if("getEntriesByType"in e){const t=e.getEntriesByType("paint");return t.length&&"first-contentful-paint"==t[0].name?t[0].startTime:void 0}if(chrome&&"loadTimes"in chrome){const e=chrome.loadTimes().firstPaintTime;if(t&&e)return 1e3*e-t}const n=e.timing.msFirstPaint;if(n)return n-t}function getFromStart(){var e;const[t,n]=getPerformanceInfo();return e=t&&n?n():getMs(timeState)-timeState.initTime,Math.round(e)}function getHid(){let e=metrikaStorage[HID_NAME];return e||(e=getRandom(),metrikaStorage[HID_NAME]=e),e}function isFalseURL(){const e=/\/$/;if(!metrikaStorage)return null;const t=getReferrer().replace(e,""),n=(metrikaStorage["page-ref"]||"").replace(e,""),r=getPageUrl(),a=metrikaStorage["page-url"],o=r.href!==a,i=t!==n;let c=0;return o&&i?c=3:i?c=1:o&&(c=2),c}function getDocumentEncoding(){return(document.characterSet||document.charset||"").toLowerCase()}function getDocumentLanguage(){return navigator.language||navigator.userLanguage||navigator.browserLanguage||navigator.systemLanguage||""}function getDesktopFlag(){var e=metrikaStorage[BATTERY_INFO],t={v:!1,p:void 0};if(!e){try{t.p=navigator.getBattery&&navigator.getBattery.call(window.navigator)}catch(e){}metrikaStorage[BATTERY_INFO]=t,t.p&&t.p.then&&t.p.then(function(e){t.v=e.charging&&0===e.chargingTime})}return t.v}function getNetType(){if(!("connection"in navigator))return null;const e=navigator.connection.type,t=NET_MAP.indexOf(e);return-1===t?e:t}function getElementPath(e,t){var n="",r=e;const a=getCachedTags();for(var o=r.nodeName||r.tagName||"*";r&&r.parentNode&&0!=["BODY","HTML"].indexOf(o);)n+=a[o]||"*",n+=getElementNeighborPosition(r,t)||"",o=(r=r.parentElement).nodeName||r.tagName||"*";return truncate(n,MAX_LEN_PATH)}function getCachedTags(){let e=";".charCodeAt(0);const t={};for(let n=0;n<TAGS_MAP.length;n+=1)t[TAGS_MAP[n]]=String.fromCharCode(e),e+=1;return t}function getElementPosition(e){const t=getBody(),n=getDocumentScroll();return{x:e.pageX||e.clientX+n.x-(t.clientLeft||0)||0,y:e.pageY||e.clientY+n.y-(t.clientTop||0)||0}}function getElementXY(e){const t=e.getBoundingClientRect();if(t){const e=getDocumentScroll();return{left:Math.round(t.left+e.x),top:Math.round(t.top+e.y)}}let n=0,r=0;for(;e;)n+=e.offsetLeft,r+=e.offsetTop,e=e.offsetParent;return{left:n,top:r}}function getElementSize(e){if(e===getBody()||e===document.documentElement)return getDocumentSize();const t=e.getBoundingClientRect();return t||{width:e.offsetWidth,height:e.offsetHeight}}function getElementNeighborPosition(e,t){const n=getElementParent(e);if(n){const r=n.childNodes,a=e&&e.nodeName;let o=0;for(let n=0;n<r.length;n+=1){if(a===(r[n]&&r[n].nodeName)){if(e===r[n])return o;t&&r[n]===t||(o+=1)}}}return 0}function getElementParent(e){if(!e||e===document.documentElement)return null;if(e===getBody())return document.documentElement;let t=null;try{t=e.parentNode}catch(e){}return t}function getDocumentScroll(e){const t=getBody();return{x:window.pageXOffset||document.documentElement&&document.documentElement.scrollLeft||t&&t.scrollLeft||0,y:window.pageYOffset||document.documentElement&&document.documentElement.scrollTop||t&&t.scrollTop||0}}function getDocumentSize(){const e=getBody(),[t,n]=getViewportSize();return{width:Math.max(e.scrollWidth,t),height:Math.max(e.scrollHeight,n)}}function getViewportSize(){const e=getVisualViewportSize();if(e){const[t,n,r]=e;return[Math.round(t*r),Math.round(n*r)]}const t=getBody();return[t.clientWidth||window.innerWidth,t.clientHeight||window.innerHeight]}function getVisualViewportSize(){const e=window.visualViewport.width,t=window.visualViewport.height,n=window.visualViewport.scale;return e&&t?[Math.floor(e),Math.floor(t),n]:null}function getBody(){return document.body||document.getElementsByTagName("body")[0]}function setCookie(e,t){var n=new Date;n.setTime(n.getTime()+31536e6),document.cookie=e+"="+t+"; expires="+n.toGMTString()+"; path=/; domain="+window.location.hostname+"; samesite=Strict;"}function getCookie(e){return document.cookie.length>0&&(start=document.cookie.indexOf(e+"="),-1!=start)?(start=start+e.length+1,end=document.cookie.indexOf(";",start),-1==end&&(end=document.cookie.length),unescape(document.cookie.substring(start,end))):""}function getRandom(){return Math.floor(Math.random()*(1<<30))}function getSeconds(){return Math.round(Date.now()/1e3)}function truncate(e,t){return(e||"").slice(0,t)}function responceTestRequest(e=!1){var t=window.location.search.substr(1).split("&").reduce(function(e,t){const[n,r]=t.split("=");return{...e,[n]:r}},{});t[CHECK_URL_PARAM]&&parseInt(t[CHECK_URL_PARAM])==metrikaStorage.id&&alert("Работает")}jQuery(function(e){sendHit(metrikaID,{},{userID:userID,UserPage:UserPage}),setTimeout(()=>{notBounce(metrikaID,{userID:userID,UserPage:UserPage})},25e3),responceTestRequest(),e(document).trigger("loaded.yametrika");var t=0,n={x:0,y:0},r=!0;e(document).on("click.yametrika",function(a){const[o,i]=getPerformanceInfo(),c=i()-t;if(c>50){const o=getElementPosition(a),d=Math.abs(n.x-o.x),g=Math.abs(n.y-o.y);r!==a.target&&d>2&&g>2&&c>1e3&&(e(a.target).attr("ym-disable-clickmap")&&e(a.target).attr("ym-clickmap-ignore")||(t=i(),n=o,r=a.target,sendClick(metrikaID,a,{userID:userID,UserPage:UserPage})))}}),e(document).on("hashchange.yametrika",function(){sendHit(metrikaID,{},{userID:userID,UserPage:UserPage})}),e(document).on("beforeunload.yametrika, unload.yametrika",function(){sendHit(metrikaID,{},{userID:userID,UserPage:UserPage})})});