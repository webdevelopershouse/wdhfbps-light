/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder
* Version                 : 1.0
* File                    : wdhfbps.shortcode.button.js
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Shorcode JS
*/

(function(){
    tinymce.create('tinymce.plugins.WDHFBPS', {
        init:function(ed, url){
            
            if (typeof WDHFBPS_PLUGIN != 'undefined') {
                var wdhfbps_title = JSON.parse(WDHFBPS_PLUGIN)['title'],
                    wdhfbps_url = JSON.parse(WDHFBPS_PLUGIN)['url'];

                    // ADD Button  
                    ed.addButton('WDHFBPS', {
                        title: wdhfbps_title,
                        image: wdhfbps_url+'stuff/images/small-logo.png',
                        onclick: function() {
                            window.tinyMCE.activeEditor.selection.setContent('[wdhfbps-light]');
                        }
                    });
            }
    
        },

        createControl:function(n, cm){// Init Combo Box.
            return null;
        },

        getInfo:function(){
            return {longname  : 'Synoptic WordPress Responsive Visual Form Builder',
                    author    : 'Web Developers House',
                    authorurl : 'http://www.wdh.im',
                    infourl   : 'http://www.wdh.im',
                    version   : '1.0'};
        }
    });

    tinymce.PluginManager.add('WDHFBPS', tinymce.plugins.WDHFBPS);
})();