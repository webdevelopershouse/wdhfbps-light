/*
Project Name: WDH - Edit Database Field LIGHT (Wordpress Library)
Project Version: 1.0
Project URL: http://www.wdh.im/projects/edit-database-field-light-wordpress-library/
Author: WDH - Web Developers House
Author URL: http://www.wdh.im/
File Path: js/jquery.wdh.im.tooltip.js
File Description: WDH - Tooltip Scripts 
File Version: 1.0
Last Update File : 03.10.2014
*/

var $jWDH = jQuery.noConflict();

$jWDH.fn.extend({
    wdhTooltip:function (ttType){
        var id = $jWDH(this)['selector'],
            BoxText = $jWDH(id).text(),
            ttText = $jWDH(id).attr('title'),
            boxW = $jWDH(id).outerWidth(true),
            boxH = $jWDH(id).outerHeight(true),
            ttW = $jWDH(id).wdhTextWidth(ttText),
            ttH =$jWDH(id).wdhTextHeight(ttText),
            ttArrowClass = 'wdh-tooltip-'+ttType+'-arrow',
            idMod = id.replace("#",""),
            ttDivBoxHTML = '<div id="wdh-tooltip-box-'+idMod+'" class="wdh-tooltip-box">'+ttText+'</div>',
            ttDivArrowHTML = '<div id="wdh-tooltip-arrow-'+idMod+'" class="'+ttArrowClass+'">&nbsp;</div>',
            ttDivHTML = ttDivBoxHTML+ttDivArrowHTML,
            curHTML = $jWDH(id).html(),
            DivAllHTML = curHTML+ttDivHTML,
            ttBoxLeft,ttBoxTop,ttArrowLeft,ttArrowTop;
            
            
            // Calculating position
            switch(ttType){
                case "top":
                    
                    if (boxW<1){
                        boxW = $jWDH(id).wdhTextWidth(BoxText);
                    }
                    
                    if (boxH<1){
                        boxH = $jWDH(id).wdhTextHeight(BoxText);
                    }
                    ttArrowLeft = boxW/2-6;
                    ttArrowTop = -5;
                    ttBoxLeft = 0;
                    ttBoxTop = -ttH-15;
                    break;
                case "bottom":
                    
                    if (boxW<1){
                        boxW = $jWDH(id).wdhTextWidth(BoxText);
                    }
                    
                    if (boxH<1){
                        boxH = $jWDH(id).wdhTextHeight(BoxText);
                    }
                    
                    ttArrowLeft = boxW/2-6;
                    ttArrowTop = boxH;
                    ttBoxLeft = 0;
                    ttBoxTop = ttArrowTop+7;
                    break;
                case "right":
                    
                    if (boxW<1){
                        boxW = $jWDH(id).wdhTextWidth(BoxText);
                    }
                    
                    if (boxH<1){
                        boxH = $jWDH(id).wdhTextHeight(BoxText);
                    }
                    ttW = ttW+10;
                    ttArrowLeft = boxW+5;
                    ttArrowTop = boxH/2-ttH/2;
                    ttBoxLeft = ttArrowLeft+7;
                    ttBoxTop = ttArrowTop-boxH/2;
                    break;
                case "left":
                    
                    if (boxW<1){
                        boxW = $jWDH(id).wdhTextWidth(BoxText);
                    }
                    
                    if (boxH<1){
                        boxH = $jWDH(id).wdhTextHeight(BoxText);
                    }
                    ttW = ttW+10;
                    ttArrowLeft = -10;
                    ttArrowTop = boxH/2-ttH/2;
                    ttBoxLeft = ttW+19;
                    ttBoxLeft = -ttBoxLeft;
                    ttBoxTop = ttArrowTop-boxH/2;
                    break;
            }
           
            // Add Tooltip
            $jWDH(id).hover(function(){
                    // Deleting Tooltip
                    $jWDH('#wdh-tooltip-box-'+idMod).remove();
                    $jWDH('#wdh-tooltip-arrow-'+idMod).remove();

                    // Insert Tooltip HTML
                    $jWDH(id).html(DivAllHTML);
                    // Remove title
                    $jWDH(id).attr('title','');

                    $jWDH(id).css({'position':'relative'});

                    // Customize Tooltip Box
                    $jWDH('#wdh-tooltip-box-'+idMod).css({
                        'width':ttW,
                        'height':ttH,
                        'left':ttBoxLeft,
                        'top': ttBoxTop
                    });

                    // Customize Tooltip Arrow
                    $jWDH('#wdh-tooltip-arrow-'+idMod).css({
                        'left':ttArrowLeft,
                        'top':ttArrowTop
                    });

                    $jWDH('#wdh-tooltip-box-'+idMod).fadeIn(800);
                    $jWDH('#wdh-tooltip-arrow-'+idMod).fadeIn(800);

                },
                function(){
                    //Hide Tooltip
                    $jWDH('#wdh-tooltip-box-'+idMod).hide();
                    $jWDH('#wdh-tooltip-arrow-'+idMod).hide();
                    
                    // Adding back title
                    $jWDH(id).attr('title',ttText);
                }
            );
    },
    
    wdhTextWidth:function(text){
            calc = '<span style="display:none">' + text + '</span>';
            $jWDH('body').append(calc);
        var width = $jWDH('body').find('span:last').outerWidth();
        $jWDH('body').find('span:last').remove();
        return width;
    },
            
    wdhTextHeight:function(text){
            calc = '<span style="display:none">' + text + '</span>';
            $jWDH('body').append(calc);
        var height = $jWDH('body').find('span:last').outerHeight();
        $jWDH('body').find('span:last').remove();
        return height;
    }        
});