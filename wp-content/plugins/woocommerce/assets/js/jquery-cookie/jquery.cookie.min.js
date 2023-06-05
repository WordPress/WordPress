/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?e(require("jquery")):e(jQuery)}(function(a){var o=/\+/g;function s(e){return x.raw?e:encodeURIComponent(e)}function m(e,n){e=x.raw?e:function(e){0===e.indexOf('"')&&(e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return e=decodeURIComponent(e.replace(o," ")),x.json?JSON.parse(e):e}catch(n){}}(e);return"function"==typeof n?n(e):e}var x=a.cookie=function(e,n,o){var i,t;if(n!==undefined&&"function"!=typeof n)return"number"==typeof(o=a.extend({},x.defaults,o)).expires&&(i=o.expires,(t=o.expires=new Date).setTime(+t+864e5*i)),document.cookie=[s(e),"=",(t=n,s(x.json?JSON.stringify(t):String(t))),o.expires?"; expires="+o.expires.toUTCString():"",o.path?"; path="+o.path:"",o.domain?"; domain="+o.domain:"",o.secure?"; secure":""].join("");for(var r=e?undefined:{},c=document.cookie?document.cookie.split("; "):[],u=0,f=c.length;u<f;u++){var d=c[u].split("="),p=(p=d.shift(),x.raw?p:decodeURIComponent(p)),d=d.join("=");if(e&&e===p){r=m(d,n);break}e||(d=m(d))===undefined||(r[p]=d)}return r};x.defaults={},a.removeCookie=function(e,n){return a.cookie(e)!==undefined&&(a.cookie(e,"",a.extend({},n,{expires:-1})),!a.cookie(e))}});