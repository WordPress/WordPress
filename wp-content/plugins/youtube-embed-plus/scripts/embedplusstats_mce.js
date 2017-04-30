    (function() {    
        tinymce.create('tinymce.plugins.Embedplusstats_youtubeprefs', {
            init : function(ed, url) {
                var plep = new Image();
                plep.src = url+'/../btn_embedplusstats.png';
                ed.addButton('embedplusstats_youtubeprefs', {
                    title : 'How much are your visitors actually watching the videos you post? Click here to start using this popular feature from EmbedPlus Labs »',
                    onclick : function(ev) {
                        window.open(epbasesite + '/dashboard/pro-easy-video-analytics.aspx?ref=wysiwygbutton&prokey=' + epprokey + '&domain=' + escape(window.location.toString()), '_blank');
                    }
                });
                       
            },
            createControl : function(n, cm) {
                return null;
            },
            getInfo : function() {
                return {
                    longname : "Embedplus Video Analytics Dashboard",
                    author : 'EmbedPlus',
                    authorurl : 'http://www.embedplus.com/',
                    infourl : 'http://www.embedplus.com/',
                    version : epversion
                };
            }
        });
        tinymce.PluginManager.add('embedplusstats_youtubeprefs', tinymce.plugins.Embedplusstats_youtubeprefs);
    
    })();
