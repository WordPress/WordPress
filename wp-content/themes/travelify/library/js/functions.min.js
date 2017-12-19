/* Theme front end features */

/* Mobile menu
/*! http://tinynav.viljamis.com v1.1 by @viljamis */

!function(e,t,a){e.fn.tinyNav=function(n){var i=e.extend({active:"current-menu-item",header:!1},n);return this.each(function(){a++;var n=e(this),l="tinynav",r=l+a,o=".l_"+r,c=e("<select/>").addClass(l+" "+r);if(n.is("ul,ol")){var s="";n.addClass("l_"+r).find("a").each(function(){s+='<option value="'+e(this).attr("href")+'">';var t;for(t=0;t<e(this).parents("ul, ol").length-1;t++)s+="- ";s+=e(this).text()+"</option>"}),c.append(s),i.header||c.find(":eq("+e(o+" li").index(e(o+" li."+i.active))+")").attr("selected",!0),c.change(function(){t.location.href=e(this).val()}),e(o).after(c),i.label&&c.before(e("<label/>").attr("for",r).addClass(l+"_label "+r+"_label").append(i.label))}})}}(jQuery,this,0),jQuery(function(){jQuery("#main-nav .root").tinyNav({active:"current-menu-item"})});

/**
 * Animated back to top
 */

jQuery(document).ready(function(){jQuery(".back-to-top").hide();jQuery(function(){jQuery(window).scroll(function(){if(jQuery(this).scrollTop()>1e3){jQuery(".back-to-top").fadeIn()}else{jQuery(".back-to-top").fadeOut()}});jQuery(".back-to-top a").click(function(){jQuery("body,html,header").animate({scrollTop:0},1e3);return false})})})