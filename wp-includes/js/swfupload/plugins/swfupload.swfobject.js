/*
	SWFUpload.SWFObject Plugin

	Summary:
		This plugin uses SWFObject to embed SWFUpload dynamically in the page.  SWFObject provides accurate Flash Player detection and DOM Ready loading.
		This plugin replaces the Graceful Degradation plugin.

	Features:
		* swfupload_load_failed_hander event
		* swfupload_pre_load_handler event
		* minimum_flash_version setting (default: "9.0.28")
		* SWFUpload.onload event for early loading

	Usage:
		Provide handlers and settings as needed.  When using the SWFUpload.SWFObject plugin you should initialize SWFUploading
		in SWFUpload.onload rather than in window.onload.  When initialized this way SWFUpload can load earlier preventing the UI flicker
		that was seen using the Graceful Degradation plugin.

		<script type="text/javascript">
			var swfu;
			SWFUpload.onload = function () {
				swfu = new SWFUpload({
					minimum_flash_version: "9.0.28",
					swfupload_pre_load_handler: swfuploadPreLoad,
					swfupload_load_failed_handler: swfuploadLoadFailed
				});
			};
		</script>
		
	Notes:
		You must provide set minimum_flash_version setting to "8" if you are using SWFUpload for Flash Player 8.
		The swfuploadLoadFailed event is only fired if the minimum version of Flash Player is not met.  Other issues such as missing SWF files, browser bugs
		 or corrupt Flash Player installations will not trigger this event.
		The swfuploadPreLoad event is fired as soon as the minimum version of Flash Player is found.  It does not wait for SWFUpload to load and can
		 be used to prepare the SWFUploadUI and hide alternate content.
		swfobject's onDomReady event is cross-browser safe but will default to the window.onload event when DOMReady is not supported by the browser.
		 Early DOM Loading is supported in major modern browsers but cannot be guaranteed for every browser ever made.
*/


/*	SWFObject v2.0 rc4 <http://code.google.com/p/swfobject/>
	Copyright (c) 2007 Geoff Stearns, Michael Williams, and Bobby van der Sluis
	This software is released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
*/
var swfobject=function(){var X="undefined",P="object",a="visibility:visible",e="visibility:hidden",B="Shockwave Flash",h="ShockwaveFlash.ShockwaveFlash",V="application/x-shockwave-flash",K="SWFObjectExprInst",G=window,g=document,N=navigator,f=[],H=[],Q=null,L=null,S=false,C=false;var Y=function(){var l=typeof g.getElementById!=X&&typeof g.getElementsByTagName!=X&&typeof g.createElement!=X&&typeof g.appendChild!=X&&typeof g.replaceChild!=X&&typeof g.removeChild!=X&&typeof g.cloneNode!=X,t=[0,0,0],n=null;if(typeof N.plugins!=X&&typeof N.plugins[B]==P){n=N.plugins[B].description;if(n){n=n.replace(/^.*\s+(\S+\s+\S+$)/,"$1");t[0]=parseInt(n.replace(/^(.*)\..*$/,"$1"),10);t[1]=parseInt(n.replace(/^.*\.(.*)\s.*$/,"$1"),10);t[2]=/r/.test(n)?parseInt(n.replace(/^.*r(.*)$/,"$1"),10):0}}else{if(typeof G.ActiveXObject!=X){var o=null,s=false;try{o=new ActiveXObject(h+".7")}catch(k){try{o=new ActiveXObject(h+".6");t=[6,0,21];o.AllowScriptAccess="always"}catch(k){if(t[0]==6){s=true}}if(!s){try{o=new ActiveXObject(h)}catch(k){}}}if(!s&&o){try{n=o.GetVariable("$version");if(n){n=n.split(" ")[1].split(",");t=[parseInt(n[0],10),parseInt(n[1],10),parseInt(n[2],10)]}}catch(k){}}}}var v=N.userAgent.toLowerCase(),j=N.platform.toLowerCase(),r=/webkit/.test(v)?parseFloat(v.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,i=false,q=j?/win/.test(j):/win/.test(v),m=j?/mac/.test(j):/mac/.test(v);/*@cc_on i=true;@if(@_win32)q=true;@elif(@_mac)m=true;@end@*/return{w3cdom:l,pv:t,webkit:r,ie:i,win:q,mac:m}}();var d=function(){if(!Y.w3cdom){return }J(I);if(Y.ie&&Y.win){try{g.write("<script id=__ie_ondomload defer=true src=//:><\/script>");var i=b("__ie_ondomload");if(i){i.onreadystatechange=function(){if(this.readyState=="complete"){this.parentNode.removeChild(this);U()}}}}catch(j){}}if(Y.webkit&&typeof g.readyState!=X){Q=setInterval(function(){if(/loaded|complete/.test(g.readyState)){U()}},10)}if(typeof g.addEventListener!=X){g.addEventListener("DOMContentLoaded",U,null)}M(U)}();function U(){if(S){return }if(Y.ie&&Y.win){var m=W("span");try{var l=g.getElementsByTagName("body")[0].appendChild(m);l.parentNode.removeChild(l)}catch(n){return }}S=true;if(Q){clearInterval(Q);Q=null}var j=f.length;for(var k=0;k<j;k++){f[k]()}}function J(i){if(S){i()}else{f[f.length]=i}}function M(j){if(typeof G.addEventListener!=X){G.addEventListener("load",j,false)}else{if(typeof g.addEventListener!=X){g.addEventListener("load",j,false)}else{if(typeof G.attachEvent!=X){G.attachEvent("onload",j)}else{if(typeof G.onload=="function"){var i=G.onload;G.onload=function(){i();j()}}else{G.onload=j}}}}}function I(){var l=H.length;for(var j=0;j<l;j++){var m=H[j].id;if(Y.pv[0]>0){var k=b(m);if(k){H[j].width=k.getAttribute("width")?k.getAttribute("width"):"0";H[j].height=k.getAttribute("height")?k.getAttribute("height"):"0";if(O(H[j].swfVersion)){if(Y.webkit&&Y.webkit<312){T(k)}}else{if(H[j].expressInstall&&!C&&O("6.0.65")&&(Y.win||Y.mac)){D(H[j])}else{c(k)}}}}A("#"+m,a)}}function T(m){var k=m.getElementsByTagName(P)[0];if(k){var p=W("embed"),r=k.attributes;if(r){var o=r.length;for(var n=0;n<o;n++){if(r[n].nodeName.toLowerCase()=="data"){p.setAttribute("src",r[n].nodeValue)}else{p.setAttribute(r[n].nodeName,r[n].nodeValue)}}}var q=k.childNodes;if(q){var s=q.length;for(var l=0;l<s;l++){if(q[l].nodeType==1&&q[l].nodeName.toLowerCase()=="param"){p.setAttribute(q[l].getAttribute("name"),q[l].getAttribute("value"))}}}m.parentNode.replaceChild(p,m)}}function F(i){if(Y.ie&&Y.win&&O("8.0.0")){G.attachEvent("onunload",function(){var k=b(i);for(var j in k){if(typeof k[j]=="function"){k[j]=function(){}}}k.parentNode.removeChild(k)})}}function D(j){C=true;var o=b(j.id);if(o){if(j.altContentId){var l=b(j.altContentId);if(l){L=l}}else{L=Z(o)}if(!(/%$/.test(j.width))&&parseInt(j.width,10)<310){j.width="310"}if(!(/%$/.test(j.height))&&parseInt(j.height,10)<137){j.height="137"}g.title=g.title.slice(0,47)+" - Flash Player Installation";var n=Y.ie&&Y.win?"ActiveX":"PlugIn",k=g.title,m="MMredirectURL="+G.location+"&MMplayerType="+n+"&MMdoctitle="+k,p=j.id;if(Y.ie&&Y.win&&o.readyState!=4){var i=W("div");p+="SWFObjectNew";i.setAttribute("id",p);o.parentNode.insertBefore(i,o);o.style.display="none";G.attachEvent("onload",function(){o.parentNode.removeChild(o)})}R({data:j.expressInstall,id:K,width:j.width,height:j.height},{flashvars:m},p)}}function c(j){if(Y.ie&&Y.win&&j.readyState!=4){var i=W("div");j.parentNode.insertBefore(i,j);i.parentNode.replaceChild(Z(j),i);j.style.display="none";G.attachEvent("onload",function(){j.parentNode.removeChild(j)})}else{j.parentNode.replaceChild(Z(j),j)}}function Z(n){var m=W("div");if(Y.win&&Y.ie){m.innerHTML=n.innerHTML}else{var k=n.getElementsByTagName(P)[0];if(k){var o=k.childNodes;if(o){var j=o.length;for(var l=0;l<j;l++){if(!(o[l].nodeType==1&&o[l].nodeName.toLowerCase()=="param")&&!(o[l].nodeType==8)){m.appendChild(o[l].cloneNode(true))}}}}}return m}function R(AE,AC,q){var p,t=b(q);if(typeof AE.id==X){AE.id=q}if(Y.ie&&Y.win){var AD="";for(var z in AE){if(AE[z]!=Object.prototype[z]){if(z=="data"){AC.movie=AE[z]}else{if(z.toLowerCase()=="styleclass"){AD+=' class="'+AE[z]+'"'}else{if(z!="classid"){AD+=" "+z+'="'+AE[z]+'"'}}}}}var AB="";for(var y in AC){if(AC[y]!=Object.prototype[y]){AB+='<param name="'+y+'" value="'+AC[y]+'" />'}}t.outerHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+AD+">"+AB+"</object>";F(AE.id);p=b(AE.id)}else{if(Y.webkit&&Y.webkit<312){var AA=W("embed");AA.setAttribute("type",V);for(var x in AE){if(AE[x]!=Object.prototype[x]){if(x=="data"){AA.setAttribute("src",AE[x])}else{if(x.toLowerCase()=="styleclass"){AA.setAttribute("class",AE[x])}else{if(x!="classid"){AA.setAttribute(x,AE[x])}}}}}for(var w in AC){if(AC[w]!=Object.prototype[w]){if(w!="movie"){AA.setAttribute(w,AC[w])}}}t.parentNode.replaceChild(AA,t);p=AA}else{var s=W(P);s.setAttribute("type",V);for(var v in AE){if(AE[v]!=Object.prototype[v]){if(v.toLowerCase()=="styleclass"){s.setAttribute("class",AE[v])}else{if(v!="classid"){s.setAttribute(v,AE[v])}}}}for(var u in AC){if(AC[u]!=Object.prototype[u]&&u!="movie"){E(s,u,AC[u])}}t.parentNode.replaceChild(s,t);p=s}}return p}function E(k,i,j){var l=W("param");l.setAttribute("name",i);l.setAttribute("value",j);k.appendChild(l)}function b(i){return g.getElementById(i)}function W(i){return g.createElement(i)}function O(k){var j=Y.pv,i=k.split(".");i[0]=parseInt(i[0],10);i[1]=parseInt(i[1],10);i[2]=parseInt(i[2],10);return(j[0]>i[0]||(j[0]==i[0]&&j[1]>i[1])||(j[0]==i[0]&&j[1]==i[1]&&j[2]>=i[2]))?true:false}function A(m,j){if(Y.ie&&Y.mac){return }var l=g.getElementsByTagName("head")[0],k=W("style");k.setAttribute("type","text/css");k.setAttribute("media","screen");if(!(Y.ie&&Y.win)&&typeof g.createTextNode!=X){k.appendChild(g.createTextNode(m+" {"+j+"}"))}l.appendChild(k);if(Y.ie&&Y.win&&typeof g.styleSheets!=X&&g.styleSheets.length>0){var i=g.styleSheets[g.styleSheets.length-1];if(typeof i.addRule==P){i.addRule(m,j)}}}return{registerObject:function(l,i,k){if(!Y.w3cdom||!l||!i){return }var j={};j.id=l;j.swfVersion=i;j.expressInstall=k?k:false;H[H.length]=j;A("#"+l,e)},getObjectById:function(l){var i=null;if(Y.w3cdom&&S){var j=b(l);if(j){var k=j.getElementsByTagName(P)[0];if(!k||(k&&typeof j.SetVariable!=X)){i=j}else{if(typeof k.SetVariable!=X){i=k}}}}return i},embedSWF:function(n,u,r,t,j,m,k,p,s){if(!Y.w3cdom||!n||!u||!r||!t||!j){return }r+="";t+="";if(O(j)){A("#"+u,e);var q=(typeof s==P)?s:{};q.data=n;q.width=r;q.height=t;var o=(typeof p==P)?p:{};if(typeof k==P){for(var l in k){if(k[l]!=Object.prototype[l]){if(typeof o.flashvars!=X){o.flashvars+="&"+l+"="+k[l]}else{o.flashvars=l+"="+k[l]}}}}J(function(){R(q,o,u);A("#"+u,a)})}else{if(m&&!C&&O("6.0.65")&&(Y.win||Y.mac)){A("#"+u,e);J(function(){var i={};i.id=i.altContentId=u;i.width=r;i.height=t;i.expressInstall=m;D(i);A("#"+u,a)})}}},getFlashPlayerVersion:function(){return{major:Y.pv[0],minor:Y.pv[1],release:Y.pv[2]}},hasFlashPlayerVersion:O,createSWF:function(k,j,i){if(Y.w3cdom&&S){return R(k,j,i)}else{return undefined}},createCSS:function(j,i){if(Y.w3cdom){A(j,i)}},addDomLoadEvent:J,addLoadEvent:M,getQueryParamValue:function(m){var l=g.location.search||g.location.hash;if(m==null){return l}if(l){var k=l.substring(1).split("&");for(var j=0;j<k.length;j++){if(k[j].substring(0,k[j].indexOf("="))==m){return k[j].substring((k[j].indexOf("=")+1))}}}return""},expressInstallCallback:function(){if(C&&L){var i=b(K);if(i){i.parentNode.replaceChild(L,i);L=null;C=false}}}}}();

	
var SWFUpload;
if (typeof(SWFUpload) === "function") {
	SWFUpload.onload = function () {};
	
	swfobject.addDomLoadEvent(function () {
		if (typeof(SWFUpload.onload) === "function") {
			SWFUpload.onload.call(window);
		}
	});
	
	SWFUpload.prototype.initSettings = (function (oldInitSettings) {
		return function () {
			if (typeof(oldInitSettings) === "function") {
				oldInitSettings.call(this);
			}

			this.ensureDefault = function (settingName, defaultValue) {
				this.settings[settingName] = (this.settings[settingName] == undefined) ? defaultValue : this.settings[settingName];
			};

			this.ensureDefault("minimum_flash_version", "9.0.28");
			this.ensureDefault("swfupload_pre_load_handler", null);
			this.ensureDefault("swfupload_load_failed_handler", null);

			delete this.ensureDefault;

		};
	})(SWFUpload.prototype.initSettings);


	SWFUpload.prototype.loadFlash = function (oldLoadFlash) {
		return function () {
			var hasFlash = swfobject.hasFlashPlayerVersion(this.settings.minimum_flash_version);
			
			if (hasFlash) {
				this.queueEvent("swfupload_pre_load_handler");
				if (typeof(oldLoadFlash) === "function") {
					oldLoadFlash.call(this);
				}
			} else {
				this.queueEvent("swfupload_load_failed_handler");
			}
		};
		
	}(SWFUpload.prototype.loadFlash);
			
	SWFUpload.prototype.displayDebugInfo = function (oldDisplayDebugInfo) {
		return function () {
			if (typeof(oldDisplayDebugInfo) === "function") {
				oldDisplayDebugInfo.call(this);
			}
			
			this.debug(
				[
					"SWFUpload.SWFObject Plugin settings:", "\n",
					"\t", "minimum_flash_version:                      ", this.settings.minimum_flash_version, "\n",
					"\t", "swfupload_pre_load_handler assigned:     ", (typeof(this.settings.swfupload_pre_load_handler) === "function").toString(), "\n",
					"\t", "swfupload_load_failed_handler assigned:     ", (typeof(this.settings.swfupload_load_failed_handler) === "function").toString(), "\n",
				].join("")
			);
		};	
	}(SWFUpload.prototype.displayDebugInfo);
}
