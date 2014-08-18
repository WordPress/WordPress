(function() {    
    
    tinymce.create('tinymce.plugins.Embedplus_youtubeprefs_wiz', {
        init : function(ed, url) {
            var plep = new Image();
            plep.src = url+'/../images/btnwizoff.png';
            ed.addButton('embedplus_youtubeprefs_wiz', {
                title : 'Visual YouTube Search Tool and Wizard - An easier embedding option',
                onclick : function(ev) {

                    modalw = Math.round(jQuery(window).width() *.9);
                    modalh = Math.round(jQuery(window).height() *.8);
                    window.ytembedplus_wizard_window =
                    ed.windowManager.open({
                        title : "Visual YouTube Search Tool and Wizard - An easier embedding option",
                        file : epbasesite + '/wpembedcode-simple-search.aspx?pluginversion=' + epversion + '&blogwidth=' + epblogwidth + '&domain=' + escape(window.location.toString()) + '&prokey=' + escape(epprokey) + '&myytdefaults=' + escape(myytdefaults),
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
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Visual YouTube Search Tool and Wizard - An easier embedding option",
                author : 'EmbedPlus',
                authorurl : 'http://www.embedplus.com/',
                infourl : 'http://www.embedplus.com/',
                version : epversion
            };
        }
    });    

    tinymce.PluginManager.add('embedplus_youtubeprefs_wiz', tinymce.plugins.Embedplus_youtubeprefs_wiz);


})();