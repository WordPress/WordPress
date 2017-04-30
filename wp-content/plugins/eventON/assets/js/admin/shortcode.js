jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.eventon_shortcode_button', {
         init : function(ed, url) {
             ed.addButton('eventon_shortcode_button', {
                title : 'Add EventON Calendar',
                onclick : function() {
                  $('.eventon_popup.eventon_shortcode').addClass('active').show().animate({'margin-top':'0px','opacity':1}).fadeIn();

                  $('html, body').animate({scrollTop:0}, 700);
                  $('#evo_popup_bg').show();
                }
             });
          },
          createControl : function(n, cm) {
             return null;
          },
          getInfo : function() {
             return {
                longname : "EventON Shortcode",
                author : 'Ashan Jay',
                authorurl : 'http://www.ashanjay.com',
                version : "1.0"
             };
          }  
    });

    tinymce.PluginManager.add('eventon_shortcode_button', tinymce.plugins.eventon_shortcode_button);


});