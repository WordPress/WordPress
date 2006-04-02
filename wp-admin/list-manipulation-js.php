<?php
require_once('admin.php');
header('Content-type: text/javascript; charset=' . get_settings('blog_charset'), true);
?>
addLoadEvent(function(){theList=new listMan();});
function deleteSomething(what,id,message){if(!message)message="<?php printf(__('Are you sure you want to delete this %s?'),"'+what+'"); ?>";if(confirm(message))return theList.ajaxDelete(what,id);else return false;}
function dimSomething(what,id,dimClass){return theList.ajaxDimmer(what,id,dimClass);}

function WPAjax(file, responseEl){//class WPAjax extends sack
	this.getResponseElement=function(r){var p=document.getElementById(r+'-p');if(!p){p=document.createElement('span');p.id=r+'ajax-response-p';document.getElementById(r).appendChild(p);}this.myResponseElement=p;	}
	this.parseAjaxResponse=function(){
		if(isNaN(this.response)){this.myResponseElement.innerHTML='<div class="error">'+this.response+'</div>';return false;}
		this.response=parseInt(this.response,10);
		if(-1==this.response){this.myResponseElement.innerHTML="<?php _e("You don't have permission to do that."); ?>";return false;}
		else if(0==this.response){this.myResponseElement.innerHTML="<?php _e("Something odd happened. Try refreshing the page? Either that or what you tried to change never existed in the first place."); ?>";return false;}
		return true;
	}
	this.parseAjaxResponseXML=function(){
		if(this.responseXML&&typeof this.responseXML=='object')return true;
		if(isNaN(this.response)){this.myResponseElement.innerHTML='<div class="error">'+this.response+'</div>';return false;}
		var r=parseInt(this.response,10);
		if(-1==r){this.myResponseElement.innerHTML="<?php _e("You don't have permission to do that."); ?>";}
		else if(0==r){this.myResponseElement.innerHTML="<?php _e("Invalid Entry."); ?>";}
		return false;
	}
	this.init(file,responseEl);
}	WPAjax.prototype=new sack;
	WPAjax.prototype.init=function(f,r){
		this.URLString=this.encVar('cookie', document.cookie);
		this.requestFile=f;this.getResponseElement(r);this.method='POST';
		this.onLoading=function(){this.myResponseElement.innerHTML="<?php _e('Sending Data...'); ?>";};
		this.onLoaded=function(){this.myResponseElement.innerHTML="<?php _e('Data Sent...'); ?>";};
		this.onInteractive=function(){this.myResponseElement.innerHTML="<?php _e('Processing Data...'); ?>";};
	}

function listMan(theListId){
	this.theList=null;
	this.ajaxRespEl=null;
	this.inputData='';this.clearInputs=new Array();
	this.topAdder=0;this.alt='alternate';this.recolorPos;this.reg_color='#FFFFFF';this.alt_color='#F1F1F1';
	var listType;var listItems;
	self.aTrap=0;

	this.ajaxAdder=function(what,where,onComplete,update){//for TR, server must wrap TR in TABLE TBODY. this.makeEl cleans it
		if(self.aTrap)return;self.aTrap=1;setTimeout('aTrap=0',300);
		this.ajaxAdd=new WPAjax('admin-ajax.php',this.ajaxRespEl?this.ajaxRespEl:'ajax-response');
		if(this.ajaxAdd.failed)return true;
		this.grabInputs(where);
		var tempObj=this;
		this.ajaxAdd.onCompletion=function(){
			if(!this.parseAjaxResponseXML())return;
			var newItems=this.responseXML.getElementsByTagName(what);
			if(tempObj.topAdder)tempObj.recolorPos=0;
			if(newItems){for (c=0;c<newItems.length;c++){
				var id=parseInt(getNodeValue(newItems[c],'id'),10);
				var exists=document.getElementById(what+'-'+id);
				if(exists)tempObj.replaceListItem(exists.id,getNodeValue(newItems[c],'newitem'),newItems.length,update);
				else tempObj.addListItem(getNodeValue(newItems[c],'newitem'),newItems.length);
			}}
			this.myResponseElement.innerHTML='';tempObj.inputData='';
			for(var i=0;i<tempObj.clearInputs.length;i++){try{var theI=document.getElementById(tempObj.clearInputs[i]);if(theI.tagName.match(/select/i))theI.selectedIndex=0;else theI.value='';}catch(e){}}
			if(onComplete&&typeof onComplete=='function')onComplete();
			tempObj.recolorList(tempObj.recolorPos,1000)
		}
		this.ajaxAdd.runAJAX('action='+(update?'update-':'add-')+what+this.inputData);
		return false;
	}
	this.ajaxUpdater=function(what,where,onComplete){return this.ajaxAdder(what,where,onComplete,true);}
	this.ajaxDelete=function(what,id,onComplete){
		if(self.aTrap)return;self.aTrap=1;setTimeout('aTrap=0',300);
		this.ajaxDel=new WPAjax('admin-ajax.php',this.ajaxRespEl?this.ajaxRespEl:'ajax-response');
		if(this.ajaxDel.failed)return true;
		var tempObj=this;
		this.ajaxDel.onCompletion=function(){if(this.parseAjaxResponse()){tempObj.removeListItem(what.replace('-as-spam','')+'-'+id);this.myResponseElement.innerHTML='';if(onComplete&&typeof onComplete=='function')onComplete();tempObj.recolorList(tempObj.recolorPos,1000)}};
		this.ajaxDel.runAJAX('action=delete-'+what+'&id='+id);
		return false;
	}
	this.ajaxDimmer=function(what,id,dimClass,onComplete){
		if(self.aTrap)return;self.aTrap=1;setTimeout('aTrap=0',300);
		this.ajaxDim=new WPAjax('admin-ajax.php',this.ajaxRespEl?this.ajaxRespEl:'ajax-response');
		if(this.ajaxDim.failed)return true;
		var tempObj=this;
		this.ajaxDim.onCompletion=function(){if(this.parseAjaxResponse()){tempObj.dimItem(what+'-'+id,dimClass);this.myResponseElement.innerHTML='';if(onComplete&&typeof onComplete=='function')onComplete();}};
		this.ajaxDim.runAJAX('action=dim-'+what+'&id='+id);
		return false;
	}
	this.makeEl=function(h){var fakeItem=document.createElement('div');fakeItem.innerHTML=h;var r=fakeItem.firstChild;while(r.tagName.match(/(table|tbody)/i)){r=r.firstChild;}return r;}
	this.addListItem=function(h,tot){
		newItem=this.makeEl(h);
		if(this.topAdder){var firstItem=this.theList.getElementsByTagName('table'==listType?'tr':'li')[0];listItems.unshift(newItem.id);this.recolorPos++}
		else{listItems.push(newItem.id);this.recolorPos=listItems.length;}
		if(this.alt&&!((tot-this.recolorPos)%2))newItem.className+=' '+this.alt;
		if(firstItem)firstItem.parentNode.insertBefore(newItem,firstItem);
		else this.theList.appendChild(newItem);
		Fat.fade_element(newItem.id);
	}
	this.removeListItem=function(id,noFade){
		if(!noFade)Fat.fade_element(id,null,700,'#FF3333');
		var theItem=document.getElementById(id);
		if(!noFade){var func=encloseFunc(function(a){a.parentNode.removeChild(a);},theItem);setTimeout(func,705);}
		else{theItem.parentNode.removeChild(theItem);}
		var pos=this.getListPos(id);
		listItems.splice(pos,1);
	}
	this.replaceListItem=function(id,h,tot,update){
		if(!update){this.removeListItem(id,true);this.addListItem(h,tot);return;}
		var newItem=this.makeEl(h);
		var oldItem=document.getElementById(id);
		var pos=this.getListPos(oldItem.id);if(this.alt&&!(pos%2))newItem.className+=' '+this.alt;
		oldItem.parentNode.replaceChild(newItem,oldItem);
		Fat.fade_element(newItem.id);
	}
	this.dimItem=function(id,dimClass,noFade){
		var theItem=document.getElementById(id);
		if(theItem.className.match(dimClass)){if(!noFade)Fat.fade_element(id,null,700,null);theItem.className=theItem.className.replace(dimClass,'');}
		else{if(!noFade)Fat.fade_element(id,null,700,'#FF3333');theItem.className=theItem.className+' '+dimClass;}
	}
	this.grabInputs=function(elId){//text,password,hidden,textarea,select
		var theItem=document.getElementById(elId);
		var inputs=new Array();
		inputs.push(theItem.getElementsByTagName('input'),theItem.getElementsByTagName('textarea'),theItem.getElementsByTagName('select'));
		for(var a=0;a<inputs.length;a++){
			for(var i=0;i<inputs[a].length;i++){
				if('action'==inputs[a][i].name)continue;
				if('text'==inputs[a][i].type||'password'==inputs[a][i].type||'hidden'==inputs[a][i].type||inputs[a][i].tagName.match(/textarea/i)){
					this.inputData+='&'+inputs[a][i].name+'='+encodeURIComponent(inputs[a][i].value);
				}else if(inputs[a][i].tagName.match(/select/i)){
					this.inputData+='&'+inputs[a][i].name+'='+encodeURIComponent(inputs[a][i].options[inputs[a][i].selectedIndex].value);
				}
			}	
		}
	}
	this.getListPos=function(id,n){for(var i=0;i<listItems.length;i++){if(id==listItems[i]){var pos=i;break;}}if(!n){if(pos<this.recolorPos)this.recolorPos=pos;}return pos;}
	this.getListItems=function(){
		if(this.theList)return;
		listItems=new Array();
		if(theListId){this.theList=document.getElementById(theListId);if(!this.theList)return false;}
		else{this.theList=document.getElementById('the-list');if(this.theList)theListId='the-list';}
		if(this.theList){
			var items=this.theList.getElementsByTagName('tr');listType='table';
			if(!items[0]){items=this.theList.getElementsByTagName('li');listType='list';}
			for(var i=0;i<items.length;i++){listItems.push(items[i].id);}
			this.recolorPos=listItems.length;
		}
	}
	this.recolorList=function(pos,dur){
		if(!this.alt)return;if(!pos)pos=0;this.recolorPos=listItems.length;
		for(var i=pos;i<listItems.length;i++){var e=document.getElementById(listItems[i]);if(i%2)e.className=e.className.replace(this.alt,'fade-'+this.alt_color.slice(1));else e.className+=' '+this.alt+' fade-'+this.reg_color.slice(1);e.style.backgroundColor='';}
		Fat.fade_all(dur);
		var func=encloseFunc(function(l){for(var i=0;i<l.length;i++){var e=document.getElementById(l[i]);e.className=e.className.replace(/fade-[a-f0-9]{6}/i,'');}},listItems);
		setTimeout(func,dur+5);
	}
	this.getListItems();
}
//No submit unless eval(code) returns true.
function killSubmit(code,e){if(!e){if(window.event)e=window.event;else return;}var t=e.target?e.target:e.srcElement;if(('text'==t.type&&e.keyCode==13)||('submit'==t.type&&'click'==e.type)){if(!eval(code)){e.returnValue=false;e.cancelBubble=true;return false;}}}
//Pretty func from ALA http://www.alistapart.com/articles/gettingstartedwithajax
function getNodeValue(tree,el){return tree.getElementsByTagName(el)[0].firstChild.nodeValue;}
//Generic but lame JS closure
function encloseFunc(f){var a=arguments[1];return function(){f(a);}}
