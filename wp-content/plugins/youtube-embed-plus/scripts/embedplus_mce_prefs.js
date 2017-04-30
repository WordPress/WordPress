(function() {
    
    tinymce.create('tinymce.plugins.Embedplus_youtubeprefs', {
        init : function(ed, url) {
            var plep = new Image();
            plep.src = url+'/../images/btnprefsoff.png';
            ed.addButton('embedplus_youtubeprefs', {
                title : "YouTube Settings Page Shortcut (Opens new tab to leave this editor tab intact)",
                onclick : function(ev) {                    
                    window.open(eppluginadminurl);
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "YouTube Settings",
                author : 'EmbedPlus',
                authorurl : 'http://www.embedplus.com/',
                infourl : 'http://www.embedplus.com/',
                version : epversion
            };
        }
    });
    
    tinymce.PluginManager.add('embedplus_youtubeprefs', tinymce.plugins.Embedplus_youtubeprefs);

})();