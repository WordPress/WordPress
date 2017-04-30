(function() {
    var is_epy_pro = (epprokey && epprokey.length > 0);
    tinymce.create('tinymce.plugins.Embedpluswiz_youtubeprefs', {
        init : function(ed, url) {
            var plep = new Image();
            plep.src = url+'/../images/wizbutton.png';
            ed.addButton('embedpluswiz_youtubeprefs', {
                title : is_epy_pro ? 'Visual YouTube Wizard - An easier embedding option' : "YouTube Settings Shortcut (Opens new tab)",
                onclick : function(ev) {
                    
                    if (is_epy_pro)
                    {                    
                        modalw = Math.round(jQuery(window).width() *.9);
                        modalh = Math.round(jQuery(window).height() *.8);
                        ed.windowManager.open({
                            title : "Visual YouTube Wizard - An easier embedding option",
                            file : epbasesite + '/wpembedcode-simple.aspx?blogwidth=' + epblogwidth + '&domain=' + escape(window.location.toString()) + '&prokey=' + escape(epprokey) + '&myytdefaults=' + escape(myytdefaults),
                            width : 950,
                            height : modalh,
                            inline : true,
                            resizable: true,
                            scrollbars: true
                        }, {
                            plugin_url : url, // Plugin absolute URL
                            some_custom_arg : '' // Custom argument
                        });
                    }
                    else
                    {
                        window.open(eppluginadminurl);
                    }
                    
                    
                    
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Visual YouTube Wizard - An easier embedding option",
                author : 'EmbedPlus',
                authorurl : 'http://www.embedplus.com/',
                infourl : 'http://www.embedplus.com/',
                version : epversion
            };
        }
    });
    tinymce.PluginManager.add('embedpluswiz_youtubeprefs', tinymce.plugins.Embedpluswiz_youtubeprefs);


})();