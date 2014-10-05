/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder
* Version                 : 1.0
* File                    : jquery.wdh.im.FBPS.Forms.js
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Admin Javascript.
*/


if (typeof ajaxurl === 'undefined'){
          var ajaxurl = window.ajaxurl;
      }
var $jWDH = jQuery.noConflict(),
    request_url = ajaxurl;


function wdhChangeSiteLanguage(language){
    var website = window.location.href,
        websiteNew = '';
        
    if (website.indexOf("?lang=") != -1) {
        websiteNew = website.split('?lang=')[0]+'?lang='+language;
    } else if (website.indexOf("&lang=") != -1) {
        websiteNew = website.split('&lang=')[0]+'&lang='+language;
    } else if(website.indexOf("?") != -1) {
        websiteNew = website+'&lang='+language;
    } else {
        websiteNew = website+'?lang='+language;
    }

    location.href = websiteNew;
}
