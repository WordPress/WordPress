/*!
 * jQuery postMessage - v0.5 - 9/11/2009
 * http://benalman.com/projects/jquery-postmessage-plugin/
 *
 * Copyright (c) 2009 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($){var b,d,j=1,a,f=this,g=!1,h="postMessage",c="addEventListener",e,i=f[h]&&!$.browser.opera;$[h]=function(k,m,l){if(!m){return}k=typeof k==="string"?k:$.param(k);l=l||parent;if(i){l[h](k,m.replace(/([^:]+:\/\/[^\/]+).*/,"$1"))}else{if(m){l.location=m.replace(/#.*$/,"")+"#"+(+new Date)+(j++)+"&"+k}}};$.receiveMessage=e=function(m,l,k){if(i){if(m){a&&e();a=function(n){if((typeof l==="string"&&n.origin!==l)||($.isFunction(l)&&l(n.origin)===g)){return g}m(n)}}if(f[c]){f[m?c:"removeEventListener"]("message",a,g)}else{f[m?"attachEvent":"detachEvent"]("onmessage",a)}}else{b&&clearInterval(b);b=null;if(m){k=typeof l==="number"?l:typeof k==="number"?k:100;b=setInterval(function(){var o=document.location.hash,n=/^#?\d+&/;if(o!==d&&n.test(o)){d=o;m({data:o.replace(n,"")})}},k)}}}})(jQuery);