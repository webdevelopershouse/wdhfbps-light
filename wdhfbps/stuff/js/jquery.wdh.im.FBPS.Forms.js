/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder
* Version                 : 1.0
* File                    : jquery.wdh.im.fbps.forms.js
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Forms Javascript.
*/

if (typeof ajaxurl === 'undefined'){
          var ajaxurl = window.ajaxurl;
      }
var $jWDH = jQuery.noConflict(),
    request_url = ajaxurl;
    window.CopyFieldID = 0;
    
$jWDH.fn.extend({
    // Forms
    wdhfbpsForms: function(type,data){
        var id = $jWDH(this)['selector'];
        
        switch(type) {
            case "display-forms":
                $jWDH(id).wdhfbpsDisplayForms(data);
                break;
        }
    },
    // Display Forms
    wdhfbpsDisplayForms: function(data){
        var id = $jWDH(this)['selector'],
            category   = $jWDH.trim(data),
            catID      = '',
            catName    = '',
            catListHTML= [];
    
            if (category.indexOf("@@") !== -1) {
                catID = category.split('@@')[0];
                catName = category.split('@@')[1];
            } else {
                catID = category;
                catName = category;
            }
            
            if (typeof catID != undefined && catID != null && catID != '') {
                // Generatin HTML
                catListHTML.push('<div class="wdhfbps-category-all" id="category-'+catID+'">');
                catListHTML.push('  <div class="new-line">');
                catListHTML.push('      <div class="category-button" id="category-buttons-for-forms-'+catID+'">');
                catListHTML.push('          <div class="text-button" id="category-text-button-'+catID+'">');
                catListHTML.push(           catName);
                catListHTML.push('          </div>');
                catListHTML.push('          <div class="tools wdhfbps-cat-but wdh-tooltip" id="category-'+catID+'-tools" onclick="wdhfbpsShowFieldsSettings('+catID+');"><span class="wdh-information">'+window.WDHFBPS_FBPS_CATEGORY_SETTINGS+'</span></div>');
                catListHTML.push('          <div class="visual-editor wdhfbps-cat-but wdh-tooltip" id="category-'+catID+'-visual-editor" onclick="wdhfbpsShowVisualEditor('+catID+');"><span class="wdh-information">'+window.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR+'</span></div>');
                catListHTML.push('          <div class="messages wdhfbps-cat-but wdh-tooltip" id="category-'+catID+'-messages" onclick="wdhfbpsShowMessagesSettings('+catID+');"><span class="wdh-information">'+window.WDHFBPS_FBPS_CATEGORY_MESSAGES+'</span></div>');
                catListHTML.push('      </div>');
                catListHTML.push('  </div>');
                                    // Form Content
                catListHTML.push('  <div class="wdhfbps-show-category-all" id="wdhfbps-show-category-'+catID+'">');
                                        // Forms Settings
                catListHTML.push('      <div class="wdhfbps-category-settings" id="category-settings-'+catID+'">');
                catListHTML.push('          <div class="wdhfbps-loader-off"></div>');                            
                catListHTML.push('      </div>');
                                        // Visual Editor
                catListHTML.push('      <div class="wdhfbps-category-settings wdhfbps-visual-editor" id="visual-editor-'+catID+'">');
                catListHTML.push('          <div class="wdhfbps-loader-off"></div>');                            
                catListHTML.push('      </div>');
                                        // Messages
                catListHTML.push('      <div class="wdhfbps-category-settings" id="messages-category-'+catID+'">');
                catListHTML.push('          <div class="wdhfbps-loader-off"></div>');                            
                catListHTML.push('      </div>');
                                        // Forms List
                catListHTML.push('      <div class="wdhfbps-customers-list" id="customers-list-category-'+catID+'">');
                catListHTML.push('          <div class="wdhfbps-loader-off"></div>');    
                catListHTML.push('      </div>');
                catListHTML.push('  </div>');
                catListHTML.push('</div>');

                // Adding HTML
                $jWDH(id).append(catListHTML.join(''));
                
                wdhfbpsShowFieldsSettings(catID);
            }
            
            // Add Tooltip
            $jWDH('.wdh-tooltip').hover(function(){
                var html = $jWDH(this).find('.wdh-information').html(),
                    width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                $jWDH(this).find('.wdh-information').css('width',width);
                $jWDH(this).find('.wdh-information').fadeIn(300);

            },
            function(){
                $jWDH(this).find('.wdh-information').fadeOut(100);
            });
        
    },
    // Display Fields
   wdhfbpsDisplayFields: function(formID){
       var id = $jWDH(this)['selector'];
       
       $jWDH('#wdhfbps-loader-fieldsCP').addClass('wdhfbps-loader');
       
       $jWDH.form(ajaxurl, {action: 'wdhfbps_show_fields_cp',
                            form_id: formID}, function(data){
            
            $jWDH('#wdhfbps-loader-fieldsCP').removeClass('wdhfbps-loader');
            $jWDH(id).html(data);
            // Add Tooltip
            $jWDH('.wdh-tooltip').hover(function(){
                var html = $jWDH(this).find('.wdh-information').html(),
                    width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                $jWDH(this).find('.wdh-information').css('width',width);
                $jWDH(this).find('.wdh-information').fadeIn(300);

            },
            function(){
                $jWDH(this).find('.wdh-information').fadeOut(100);
            });
        });
   },
   wdhfbpsVisualEditor: function(formID){
       var id = $jWDH(this)['selector'];
       
       // ----------------------------------- //
       // -------- Form Actions ----------- //
       // ----------------------------------- //
       $jWDH(id).resizable({
            maxWidth: 940,
            minWidth: 240,
            grid: [1, 10000],
            stop: function(event, ui){
                var width = ui.size.width,
                    height = ui.size.height;
            
                $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_size_new',form_id:formID,width:width,height:height}, function(data){
                    
                });
            }
       });
       
       // ----------------------------------- //
       // -------- Fields Actions ----------- //
       // ----------------------------------- //
       
       // Sort Fields
       $jWDH('.wdhfbps-form-container').sortable({
               update: function( event, ui ) {
                    var currentElemnt = ui.item.context.id,
                        elements = $jWDH('.wdhfbps-is-sortable').length,
                        i=0,
                        positions = [],
                        element = [];
                    
                    for(i=0;i<elements;i++){
                        element = {
                            "id": $jWDH('.wdhfbps-is-sortable').eq(i).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                            "position": i       
                        };
                        positions.push(element);
                    }

                    $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_position_new',positions:positions}, function(data){
                    
                   });
               },
       });
       $jWDH(".wdhfbps-form-container").disableSelection();
       
       
       // Resize Field
       $jWDH('.wdhfbps-form-field-container').resizable({
            maxWidth: 910,
            minWidth: 200,
            minHeight: 20,
            stop: function(event, ui){
                var width = ui.size.width,
                    height = ui.size.height,
                    formWidth = $jWDH('#wdhfbps-form-'+formID).width(),
                    fieldData = $jWDH(this).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                    fieldID = fieldData.split('-')[0],
                    width=parseInt(width*100/formWidth);
               
                $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).css('width',width+'%');
                //$jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).css('min-height',height+'px');
                $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_size_new',field_id:fieldID,width:width,height:height}, function(data){
                });
                
            },
       });
       
        // Sort Label / Input
       $jWDH('.wdhfbps-form-field-container').sortable({
            update: function( event, ui ) {
                var currentElement = ui.item.context.id,
                    i=0,
                    positions = [],
                    elementType = 'label',
                    element = [],
                    firstElement = 0,
                    secondElement = 0,
                    position = 0,
                    fieldData = '',
                    fieldID = 0,
                    labelID = '',
                    inputID = '',
                    elements = $jWDH('.wdhfbps-is-label-input-sortable').length;
                    
                    
                    if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-0-type-label')){
                        position = 1;
                        elementType = 'label';
                        fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-label-id-'+formID+'-');
                        fieldID = fieldData[1];
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position       
                        };
                        
                        $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-0-type-label');
                        $jWDH('#'+currentElement).addClass('wdhfbps-field-position-1-type-label');
                        
                        positions.push(element);
                        
                        position = 0;
                        inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-input';
                        elementType = 'input';
                        
                        $jWDH('#'+inputID).removeClass('wdhfbps-field-position-1-type-input');
                        $jWDH('#'+inputID).addClass('wdhfbps-field-position-0-type-input');
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position      
                        };
                        
                        positions.push(element);

                    } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-1-type-label')){
                        position = 0;
                        elementType = 'label';
                        fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-label-id-'+formID+'-');
                        fieldID = fieldData[1];
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position       
                        };
                        
                        $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-1-type-label');
                        $jWDH('#'+currentElement).addClass('wdhfbps-field-position-0-type-label');
                        
                        positions.push(element);
                        
                        position = 1;
                        inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-input';
                        elementType = 'input';
                        
                        $jWDH('#'+inputID).removeClass('wdhfbps-field-position-0-type-input');
                        $jWDH('#'+inputID).addClass('wdhfbps-field-position-1-type-input');
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position      
                        };
                        
                        positions.push(element);

                    } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-1-type-input')){
                        position = 0;
                        elementType = 'input';
                        fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-input-id-'+formID+'-');
                        fieldID = fieldData[1];
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position       
                        };
                        
                        $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-1-type-input');
                        $jWDH('#'+currentElement).addClass('wdhfbps-field-position-0-type-input');
                        
                        positions.push(element);
                        
                        position = 1;
                        inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-label';
                        elementType = 'label';
                        
                        $jWDH('#'+inputID).removeClass('wdhfbps-field-position-0-type-label');
                        $jWDH('#'+inputID).addClass('wdhfbps-field-position-1-type-label');
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position      
                        };
                        
                        positions.push(element);

                    } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-0-type-input')){
                        position = 1;
                        elementType = 'input';
                        fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-input-id-'+formID+'-');
                        fieldID = fieldData[1];
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position       
                        };
                        
                        $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-0-type-input');
                        $jWDH('#'+currentElement).addClass('wdhfbps-field-position-1-type-input');
                        
                        positions.push(element);
                        
                        position = 0;
                        inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-label';
                        elementType = 'label';
                        
                        $jWDH('#'+inputID).removeClass('wdhfbps-field-position-1-type-label');
                        $jWDH('#'+inputID).addClass('wdhfbps-field-position-0-type-label');
                        
                        element = {
                            "id": fieldID,
                            "type": elementType,
                            "position": position      
                        };
                        
                        positions.push(element);

                    }
                $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_input_position_new',field_id:fieldID,positions:positions}, function(data){
               });
            }
        });
       $jWDH(".wdhfbps-form-field-container" ).disableSelection();
       
       // Resize Label
       $jWDH('.wdhfbps-form-field-label-container').resizable({
            maxWidth: 910,
            minWidth: 100,
            minHeight: 20,
            stop: function(event, ui){
                var width = ui.size.width,
                    height = ui.size.height,
                    fieldData = $jWDH(this).attr('id').split('wdh-form-field-label-id-'+formID+'-')[1],
                    fieldID = fieldData.split('-')[0],
                    fieldWidth = $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).width(),
                    width=parseInt(width*100/fieldWidth);
                $jWDH(this).css('width',width+'%');
                $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_label_size_new',field_id:fieldID,width:width,height:height}, function(data){

                });
            },
       });
       
       // Resize Input
       $jWDH('.wdhfbps-form-field-input-container').resizable({
            maxWidth: 910,
            minWidth: 100,
            minHeight: 20,
            stop: function(event, ui){
                var width = ui.size.width,
                    height = ui.size.height,
                    fieldData = $jWDH(this).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                    fieldID = fieldData.split('-')[0],
                    fieldWidth = $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).width(),
                    width=parseInt(width*100/fieldWidth);

                $jWDH(this).css('width',width+'%'); 
                $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID+'-input').css('width',width+'%');
                $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_input_size_new',field_id:fieldID,width:width,height:height}, function(data){

                });

                // Input Height
                $jWDH('#wdh-form-field-value-id-'+formID+'-'+fieldID).css({'height':height});
            },
       });
       
       $jWDH('.wdhfbps-form-field-input-container').attr('id');
       $jWDH('.wdhfbps-form-field-input-container').css({'min-height':'20px'});
       $jWDH('.wdh-get-value').css({'width':'100%'});
        
   }
});

// --------------------------------
// ------- Edit Form Panel --------
// --------------------------------
// Show / Hide
function wdhfbpsEditForm(type, id){
    $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
    $jWDH('.wdhfbps-edit-box').animate({'bottom':'0px'},500);
    
    if (type == '0') {
        wdhfbpsLoadEditForm(0, id);
    } else {
        wdhfbpsLoadEditForm(1, id);
    }
}

function wdhfbpsSwitchEditForm(type, id){
    if (type == '0') {
        $jWDH('#wdhfbps-edit-header-text-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-header-box-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-text-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-text-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-box-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-box-'+id).removeClass('wdhfbps-selected');
        });
    } else {
        $jWDH('#wdhfbps-edit-header-box-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-header-text-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-box-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-box-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-text-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-text-'+id).removeClass('wdhfbps-selected');
        });
    }
}

// Switch Text / Box Settings 
function wdhfbpsLoadEditForm(type, id){
    if (type == '0' || type == 0) {
        $jWDH.post(ajaxurl, {action: 'wdhfbps_edit_form',form_id:id}, function(data){

            if (data) {
                $jWDH('.wdhfbps-edit-box').html(''); 
                $jWDH('.wdhfbps-edit-box').append(data).fadeIn(300,function(){
                    $jWDH(this).find('.wdhfbps-close').css({'bottom':'201px'}).fadeIn(300);
                    wdhfbpsSwitchEditForm('1', id);
                    
                    // Adding Tooltip
                    if (typeof $jWDH.ui !== "undefined") {
                        $jWDH('.wdhfbps-cat-but').tooltip();
                    }
                    // Add Tooltip
                    $jWDH('.wdh-tooltip').hover(function(){
                        var html = $jWDH(this).find('.wdh-information').html(),
                            width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                        $jWDH(this).find('.wdh-information').css('width',width);
                        $jWDH(this).find('.wdh-information').fadeIn(300);

                    },
                    function(){
                        $jWDH(this).find('.wdh-information').fadeOut(100);
                    });
                });
            }
        });
    
    } else {
        $jWDH('.wdhfbps-form-edit-box .wdhfbps-close').css({'bottom':'-220px'}).fadeOut(100);
        $jWDH('.wdhfbps-edit-box').animate({'bottom':'-220px'},500,function(){
            $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
        });
    }
}

// --------------------------------
// ------- Edit Form Field Panel --
// --------------------------------

// Generate Edit Field 
function genEditFormFieldBox(type,formID,fieldID){
    var editFormFieldHTML = new Array();
    
    $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
    $jWDH('.wdhfbps-edit-box').animate({'bottom':'0px'},500);
    
    
    switch(type){
        case "0" || 0:
            wdhfbpsEditFormField(0, formID, fieldID);
            break;
        case 0:
            wdhfbpsEditFormField(0, formID, fieldID);
            break;
        case "1":
            wdhfbpsEditFormFieldLabel(0, formID, fieldID);
            break;
        case 1:
            wdhfbpsEditFormFieldLabel(0, formID, fieldID);
            break;
        case "2":
            wdhfbpsEditFormFieldInput(0, formID, fieldID);
            break;
        case 2:
            wdhfbpsEditFormFieldInput(0, formID, fieldID);
            break;
        case "3":
            wdhfbpsEditFormFieldAll(0, formID, fieldID);
            break;
        case 3:
            wdhfbpsEditFormFieldAll(0, formID, fieldID);
            break;
        case "4":
            wdhfbpsEditFormFieldLabelAll(0, formID, fieldID);
            break;
        case 4:
            wdhfbpsEditFormFieldLabelAll(0, formID, fieldID);
            break;
        case "5":
            wdhfbpsEditFormFieldInputAll(0, formID, fieldID);
            break;
        case 5:
            wdhfbpsEditFormFieldInputAll(0, formID, fieldID);
            break;
        default:
            wdhfbpsEditFormField(0, formID, fieldID);
            break;
    }
}

// Show / Hide Field Box Settings
function wdhfbpsEditFormField(type, formID, fieldID){
    
    if (type == '0' || type == 0) {
        $jWDH.post(ajaxurl, {action: 'wdhfbps_edit_form_field',form_id:formID,field_id:fieldID}, function(data){

            if (data) {
                $jWDH('.wdhfbps-edit-box').html(''); 
                $jWDH('.wdhfbps-edit-box').append(data).fadeIn(300,function(){
                    $jWDH(this).find('.wdhfbps-close').css({'bottom':'201px'}).fadeIn(300);
                    
                    // Adding Tooltip
                    if (typeof $jWDH.ui !== "undefined") {
                        $jWDH('.wdhfbps-cat-but').tooltip();
                    }
                    // Add Tooltip
                    $jWDH('.wdh-tooltip').hover(function(){
                        var html = $jWDH(this).find('.wdh-information').html(),
                            width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                        $jWDH(this).find('.wdh-information').css('width',width);
                        $jWDH(this).find('.wdh-information').fadeIn(300);

                    },
                    function(){
                        $jWDH(this).find('.wdh-information').fadeOut(100);
                    });
                });
            }
        });
    
    } else {
        $jWDH('.wdhfbps-form-edit-box .wdhfbps-close').css({'bottom':'-220px'}).fadeOut(100);
        $jWDH('.wdhfbps-edit-box').animate({'bottom':'-220px'},500,function(){
            $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
        });
    }
}

// Switch Text / Box Settings 
function wdhfbpsSwitchEditFormField(type, id){
    if (type == '0') {
        $jWDH('#wdhfbps-edit-field-header-text-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-header-box-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-text-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-text-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-box-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-box-'+id).removeClass('wdhfbps-selected');
        });
    } else {
        $jWDH('#wdhfbps-edit-field-header-box-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-header-text-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-box-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-box-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-text-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-text-'+id).removeClass('wdhfbps-selected');
        });
    }
}

// Show / Hide Field Box Settings
function wdhfbpsEditFormFieldAll(type, formID, fieldID){
    
    if (type == '0' || type == 0) {
        $jWDH.post(ajaxurl, {action: 'wdhfbps_edit_form_field_all',form_id:formID,field_id:fieldID}, function(data){

            if (data) {
                $jWDH('.wdhfbps-edit-box').html(''); 
                $jWDH('.wdhfbps-edit-box').append(data).fadeIn(300,function(){
                    $jWDH(this).find('.wdhfbps-close').css({'bottom':'201px'}).fadeIn(300);
                    
                    // Adding Tooltip
                    if (typeof $jWDH.ui !== "undefined") {
                        $jWDH('.wdhfbps-cat-but').tooltip();
                    }
                    // Add Tooltip
                    $jWDH('.wdh-tooltip').hover(function(){
                        var html = $jWDH(this).find('.wdh-information').html(),
                            width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                        $jWDH(this).find('.wdh-information').css('width',width);
                        $jWDH(this).find('.wdh-information').fadeIn(300);

                    },
                    function(){
                        $jWDH(this).find('.wdh-information').fadeOut(100);
                    });
                });
            }
        });
    
    } else {
        $jWDH('.wdhfbps-form-edit-box .wdhfbps-close').css({'bottom':'-220px'}).fadeOut(100);
        $jWDH('.wdhfbps-edit-box').animate({'bottom':'-220px'},500,function(){
            $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
        });
    }
}

// Switch Text / Box Settings 
function wdhfbpsSwitchEditFormFieldAll(type, id){
    if (type == '0') {
        $jWDH('#wdhfbps-edit-field-header-text-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-header-box-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-text-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-text-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-box-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-box-'+id).removeClass('wdhfbps-selected');
        });
    } else {
        $jWDH('#wdhfbps-edit-field-header-box-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-header-text-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-box-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-box-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-text-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-text-'+id).removeClass('wdhfbps-selected');
        });
    }
}

// Show / Hide Field Label Box Settings
function wdhfbpsEditFormFieldLabel(type, formID, fieldID){
    
    $jWDH.post(ajaxurl, {action: 'wdhfbps_edit_form_field_label',form_id:formID,field_id:fieldID}, function(data){
        
        if (type == '0' || type == 0) {
            $jWDH('.wdhfbps-edit-box').html(''); 
            $jWDH('.wdhfbps-edit-box').append(data).fadeIn(300,function(){
                $jWDH(this).find('.wdhfbps-close').css({'bottom':'201px'}).fadeIn(300);
                    
                    // Adding Tooltip
                    if (typeof $jWDH.ui !== "undefined") {
                        $jWDH('.wdhfbps-cat-but').tooltip();
                    }
                    // Add Tooltip
                    $jWDH('.wdh-tooltip').hover(function(){
                        var html = $jWDH(this).find('.wdh-information').html(),
                            width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                        $jWDH(this).find('.wdh-information').css('width',width);
                        $jWDH(this).find('.wdh-information').fadeIn(300);

                    },
                    function(){
                        $jWDH(this).find('.wdh-information').fadeOut(100);
                    });
            });
        } else {
            $jWDH('.wdhfbps-form-edit-box .wdhfbps-close').css({'bottom':'-220px'}).fadeOut(100);
            $jWDH('.wdhfbps-edit-box').animate({'bottom':'-220px'},500,function(){
                $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
            });
        }
    });
}

// Switch Text / Box Settings 
function wdhfbpsSwitchEditFormFieldLabel(type, id){
    if (type == '0') {
        $jWDH('#wdhfbps-edit-field-label-header-text-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-label-header-box-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-label-text-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-label-text-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-label-box-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-label-box-'+id).removeClass('wdhfbps-selected');
        });
    } else {
        $jWDH('#wdhfbps-edit-field-label-header-box-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-label-header-text-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-label-box-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-label-box-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-label-text-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-label-text-'+id).removeClass('wdhfbps-selected');
        });
    }
}

// Show / Hide Field Label Box Settings
function wdhfbpsEditFormFieldLabelAll(type, formID, fieldID){
    
    $jWDH.post(ajaxurl, {action: 'wdhfbps_edit_form_field_label_all',form_id:formID,field_id:fieldID}, function(data){
        
        if (type == '0' || type == 0) {
            $jWDH('.wdhfbps-edit-box').html(''); 
            $jWDH('.wdhfbps-edit-box').append(data).fadeIn(300,function(){
                $jWDH(this).find('.wdhfbps-close').css({'bottom':'201px'}).fadeIn(300);
                    
                    // Adding Tooltip
                    if (typeof $jWDH.ui !== "undefined") {
                        $jWDH('.wdhfbps-cat-but').tooltip();
                    }
                    // Add Tooltip
                    $jWDH('.wdh-tooltip').hover(function(){
                        var html = $jWDH(this).find('.wdh-information').html(),
                            width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                        $jWDH(this).find('.wdh-information').css('width',width);
                        $jWDH(this).find('.wdh-information').fadeIn(300);

                    },
                    function(){
                        $jWDH(this).find('.wdh-information').fadeOut(100);
                    });
            });
        } else {
            $jWDH('.wdhfbps-form-edit-box .wdhfbps-close').css({'bottom':'-220px'}).fadeOut(100);
            $jWDH('.wdhfbps-edit-box').animate({'bottom':'-220px'},500,function(){
                $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
            });
        }
    });
}

// Switch Text / Box Settings 
function wdhfbpsSwitchEditFormFieldLabelAll(type, id){
    if (type == '0') {
        $jWDH('#wdhfbps-edit-field-label-header-text-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-label-header-box-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-label-text-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-label-text-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-label-box-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-label-box-'+id).removeClass('wdhfbps-selected');
        });
    } else {
        $jWDH('#wdhfbps-edit-field-label-header-box-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-label-header-text-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-label-box-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-label-box-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-label-text-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-label-text-'+id).removeClass('wdhfbps-selected');
        });
    }
}

// Show / Hide Field Input Box Settings
function wdhfbpsEditFormFieldInput(type, formID, fieldID){
    
    $jWDH.post(ajaxurl, {action: 'wdhfbps_edit_form_field_input',form_id:formID,field_id:fieldID}, function(data){
        
        if (type == '0' || type == 0) {
            $jWDH('.wdhfbps-edit-box').html(''); 
            $jWDH('.wdhfbps-edit-box').append(data).fadeIn(300,function(){
                $jWDH(this).find('.wdhfbps-close').css({'bottom':'201px'}).fadeIn(300);
                    
                    // Adding Tooltip
                    if (typeof $jWDH.ui !== "undefined") {
                        $jWDH('.wdhfbps-cat-but').tooltip();
                    }
                    // Add Tooltip
                    $jWDH('.wdh-tooltip').hover(function(){
                        var html = $jWDH(this).find('.wdh-information').html(),
                            width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                        $jWDH(this).find('.wdh-information').css('width',width);
                        $jWDH(this).find('.wdh-information').fadeIn(300);

                    },
                    function(){
                        $jWDH(this).find('.wdh-information').fadeOut(100);
                    });
            });
        } else {
            $jWDH('.wdhfbps-form-edit-box .wdhfbps-close').css({'bottom':'-220px'}).fadeOut(100);
            $jWDH('.wdhfbps-edit-box').animate({'bottom':'-220px'},500,function(){
                $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
            });
        }
    });
}

// Switch Text / Box Settings 
function wdhfbpsSwitchEditFormFieldInput(type, id){
    if (type == '0') {
        $jWDH('#wdhfbps-edit-field-input-header-text-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-input-header-box-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-input-text-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-input-text-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-input-box-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-input-box-'+id).removeClass('wdhfbps-selected');
        });
    } else {
        $jWDH('#wdhfbps-edit-field-input-header-box-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-input-header-text-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-input-box-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-input-box-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-input-text-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-input-text-'+id).removeClass('wdhfbps-selected');
        });
    }
}

// Show / Hide Field Input Box Settings
function wdhfbpsEditFormFieldInputAll(type, formID, fieldID){
    
    $jWDH.post(ajaxurl, {action: 'wdhfbps_edit_form_field_input_all',form_id:formID,field_id:fieldID}, function(data){
        
        if (type == '0' || type == 0) {
            $jWDH('.wdhfbps-edit-box').html(''); 
            $jWDH('.wdhfbps-edit-box').append(data).fadeIn(300,function(){
                $jWDH(this).find('.wdhfbps-close').css({'bottom':'201px'}).fadeIn(300);
                    
                    // Adding Tooltip
                    if (typeof $jWDH.ui !== "undefined") {
                        $jWDH('.wdhfbps-cat-but').tooltip();
                    }
                    // Add Tooltip
                    $jWDH('.wdh-tooltip').hover(function(){
                        var html = $jWDH(this).find('.wdh-information').html(),
                            width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                        $jWDH(this).find('.wdh-information').css('width',width);
                        $jWDH(this).find('.wdh-information').fadeIn(300);

                    },
                    function(){
                        $jWDH(this).find('.wdh-information').fadeOut(100);
                    });
            });
        } else {
            $jWDH('.wdhfbps-form-edit-box .wdhfbps-close').css({'bottom':'-220px'}).fadeOut(100);
            $jWDH('.wdhfbps-edit-box').animate({'bottom':'-220px'},500,function(){
                $jWDH('.wdhfbps-edit-box').html('<div class="wdhfbps-loader" style="margin-top:85px;"></div>');
            });
        }
    });
}

// Switch Text / Box Settings 
function wdhfbpsSwitchEditFormFieldInputAll(type, id){
    if (type == '0') {
        $jWDH('#wdhfbps-edit-field-input-header-text-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-input-header-box-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-input-text-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-input-text-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-input-box-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-input-box-'+id).removeClass('wdhfbps-selected');
        });
    } else {
        $jWDH('#wdhfbps-edit-field-input-header-box-'+id).addClass('wdhfbps-selected');
        $jWDH('#wdhfbps-edit-field-input-header-text-'+id).removeClass('wdhfbps-selected');
        $jWDH('#wdhfbps-field-input-box-'+id).slideDown(500, function(){
            $jWDH('#wdhfbps-field-input-box-'+id).addClass('wdhfbps-selected');
        });
        $jWDH('#wdhfbps-field-input-text-'+id).slideUp(300, function(){
            $jWDH('#wdhfbps-field-input-text-'+id).removeClass('wdhfbps-selected');
        });
    }
}

// Copy Field Design
function copyFormFieldBox(fieldID){
    window.CopyFieldID = fieldID;
}

// Paste Field Design
function pasteFormFieldBox(fieldID, formID){
    
    if (window.CopyFieldID < 1) {
        alert(window.WDHFBPS_FBPS_CATEGORY_FIELD_NO_COPY_FIELD);
    } else {
        $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).after('<div id="wdhfbps-form-'+formID+'-temporary-field-'+fieldID+'"><div class="wdhfbps-loader" style="margin-top:5px;display:block;"></div></div>');
        $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).remove();
        $jWDH.post(ajaxurl, {action: 'wdhfbps_paste_form_field',form_id:formID,copy_field_id:window.CopyFieldID,paste_field_id:fieldID}, function(data){

            if(data) {
                $jWDH('#wdhfbps-form-'+formID+'-temporary-field-'+fieldID).after(data);
                $jWDH('#wdhfbps-form-'+formID+'-temporary-field-'+fieldID).remove();
                
                //---------------------------
                //--- Add Buttons Actions ---
                //---------------------------

                // Add Tooltip
                $jWDH('.wdh-tooltip').hover(function(){
                    var html = $jWDH(this).find('.wdh-information').html(),
                        width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                    $jWDH(this).find('.wdh-information').css('width',width);
                    $jWDH(this).find('.wdh-information').fadeIn(300);

                },
                function(){
                    $jWDH(this).find('.wdh-information').fadeOut(100);
                });

                // Add Edit Field Button
                $jWDH('.wdhfbps-form-field-container').hover(function(){
                    $jWDH(this).find('.wdhfbps-form-edit-buttons-each').eq(0).css('display','block');
                    $jWDH(this).find('.wdhfbps-form-move-field-button').eq(0).css('display','block');
                },
                function(){
                    $jWDH(this).find('.wdhfbps-form-edit-buttons-each').eq(0).css('display','none');
                    $jWDH(this).find('.wdhfbps-form-move-field-button').eq(0).css('display','none');
                });

                // ----------------------------------- //
                // -------- Fields Actions ----------- //
                // ----------------------------------- //

                // Sort Fields
                $jWDH('.wdhfbps-form-container').sortable({
                        update: function( event, ui ) {
                             var currentElemnt = ui.item.context.id,
                                 elements = $jWDH('.wdhfbps-is-sortable').length,
                                 i=0,
                                 positions = [],
                                 element = [];

                             for(i=0;i<elements;i++){
                                 element = {
                                     "id": $jWDH('.wdhfbps-is-sortable').eq(i).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                                     "position": i       
                                 };
                                 positions.push(element);
                             }

                             $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_position_new',form_id:formID,positions:positions}, function(data){
                        

                $jWDH(".wdhfbps-form-container").disableSelection();
                            });
                        },
                });
                $jWDH(".wdhfbps-form-container").disableSelection();


                // Resize Field
                $jWDH('.wdhfbps-form-field-container').resizable({
                     maxWidth: 910,
                     minWidth: 200,
                     minHeight: 20,
                     stop: function(event, ui){
                         var width = ui.size.width,
                             height = ui.size.height,
                             formWidth = $jWDH('#wdhfbps-form-'+formID).width(),
                             fieldData = $jWDH(this).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                             fieldID = fieldData.split('-')[0],
                             width=parseInt(width*100/formWidth);

                         $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).css('width',width+'%');
                         $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_size_new',field_id:fieldID,width:width,height:height}, function(data){
                            
                         });

                     },
                });

                 // Sort Label / Input
                $jWDH('.wdhfbps-form-field-container').sortable({
                    update: function( event, ui ) {
                        var currentElement = ui.item.context.id,
                            i=0,
                            positions = [],
                            elementType = 'label',
                            element = [],
                            firstElement = 0,
                            secondElement = 0,
                            position = 0,
                            fieldData = '',
                            fieldID = 0,
                            labelID = '',
                            inputID = '',
                            elements = $jWDH('.wdhfbps-is-label-input-sortable').length;

                            if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-0-type-label')){
                                position = 1;
                                elementType = 'label';
                                fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-label-id-'+formID+'-');
                                fieldID = fieldData[1];

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position       
                                };

                                $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-0-type-label');
                                $jWDH('#'+currentElement).addClass('wdhfbps-field-position-1-type-label');

                                positions.push(element);

                                position = 0;
                                inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-input';
                                elementType = 'input';

                                $jWDH('#'+inputID).removeClass('wdhfbps-field-position-1-type-input');
                                $jWDH('#'+inputID).addClass('wdhfbps-field-position-0-type-input');

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position      
                                };

                                positions.push(element);

                            } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-1-type-label')){
                                position = 0;
                                elementType = 'label';
                                fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-label-id-'+formID+'-');
                                fieldID = fieldData[1];

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position       
                                };

                                $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-1-type-label');
                                $jWDH('#'+currentElement).addClass('wdhfbps-field-position-0-type-label');

                                positions.push(element);

                                position = 1;
                                inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-input';
                                elementType = 'input';

                                $jWDH('#'+inputID).removeClass('wdhfbps-field-position-0-type-input');
                                $jWDH('#'+inputID).addClass('wdhfbps-field-position-1-type-input');

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position      
                                };

                                positions.push(element);

                            } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-1-type-input')){
                                position = 0;
                                elementType = 'input';
                                fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-input-id-'+formID+'-');
                                fieldID = fieldData[1];

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position       
                                };

                                $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-1-type-input');
                                $jWDH('#'+currentElement).addClass('wdhfbps-field-position-0-type-input');

                                positions.push(element);

                                position = 1;
                                inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-label';
                                elementType = 'label';

                                $jWDH('#'+inputID).removeClass('wdhfbps-field-position-0-type-label');
                                $jWDH('#'+inputID).addClass('wdhfbps-field-position-1-type-label');

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position      
                                };

                                positions.push(element);

                            } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-0-type-input')){
                                position = 1;
                                elementType = 'input';
                                fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-input-id-'+formID+'-');
                                fieldID = fieldData[1];

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position       
                                };

                                $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-0-type-input');
                                $jWDH('#'+currentElement).addClass('wdhfbps-field-position-1-type-input');

                                positions.push(element);

                                position = 0;
                                inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-label';
                                elementType = 'label';

                                $jWDH('#'+inputID).removeClass('wdhfbps-field-position-1-type-label');
                                $jWDH('#'+inputID).addClass('wdhfbps-field-position-0-type-label');

                                element = {
                                    "id": fieldID,
                                    "type": elementType,
                                    "position": position      
                                };

                                positions.push(element);

                            }
                        
                        $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_input_position_new',form_id:formID,field_id:fieldID,positions:positions}, function(data){
                        
                       });
                    }
                });
                $jWDH(".wdhfbps-form-field-container" ).disableSelection();

                // Resize Label
                $jWDH('.wdhfbps-form-field-label-container').resizable({
                     maxWidth: 910,
                     minWidth: 100,
                     minHeight: 20,
                     stop: function(event, ui){
                         var width = ui.size.width,
                             height = ui.size.height,
                             fieldData = $jWDH(this).attr('id').split('wdh-form-field-label-id-'+formID+'-')[1],
                             fieldID = fieldData.split('-')[0],
                             fieldWidth = $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).width(),
                             width=parseInt(width*100/fieldWidth);
                         $jWDH(this).css('width',width+'%');
                         $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_label_size_new',field_id:fieldID,width:width,height:height}, function(data){
                             
                         });
                     },
                });

                // Resize Input
                $jWDH('.wdhfbps-form-field-input-container').resizable({
                     maxWidth: 910,
                     minWidth: 100,
                     minHeight: 20,
                     stop: function(event, ui){
                         var width = ui.size.width,
                             height = ui.size.height,
                             fieldData = $jWDH(this).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                             fieldID = fieldData.split('-')[0],
                             fieldWidth = $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).width(),
                             width=parseInt(width*100/fieldWidth);
                             
                         $jWDH(this).css('width',width+'%'); 
                         $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID+'-input').css('width',width+'%');
                         $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_input_size_new',field_id:fieldID,width:width,height:height}, function(data){
                             
                         });

                         // Input Height
                         $jWDH('#wdh-form-field-value-id-'+formID+'-'+fieldID).css({'height':height});
                     },
                });

                $jWDH('.wdhfbps-form-field-input-container').attr('id');
                $jWDH('.wdhfbps-form-field-input-container').css({'min-height':'20px'});
                $jWDH('.wdh-get-value').css({'width':'100%'});
                
            }
        });
    }
}

// Duplicate Field
function duplicateFormFieldBox(fieldID, formID){
    
    $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).after('<div id="wdhfbps-form-'+formID+'-temporary-field-'+fieldID+'"><div class="wdhfbps-loader" style="margin-top:5px;display:block;"></div></div>');
    $jWDH.post(ajaxurl, {action: 'wdhfbps_duplicate_form_field',form_id:formID,field_id:fieldID}, function(data){
        
        if(data) {
            $jWDH('#wdhfbps-form-'+formID+'-temporary-field-'+fieldID).after(data);
            $jWDH('#wdhfbps-form-'+formID+'-temporary-field-'+fieldID).remove();

            //---------------------------
            //--- Add Buttons Actions ---
            //---------------------------

            // Add Tooltip
            $jWDH('.wdh-tooltip').hover(function(){
                var html = $jWDH(this).find('.wdh-information').html(),
                    width = $jWDH(this).find('.wdh-information').wdhTextWidth(html);
                $jWDH(this).find('.wdh-information').css('width',width);
                $jWDH(this).find('.wdh-information').fadeIn(300);

            },
            function(){
                $jWDH(this).find('.wdh-information').fadeOut(100);
            });

            // Add Edit Field Button
            $jWDH('.wdhfbps-form-field-container').hover(function(){
                $jWDH(this).find('.wdhfbps-form-edit-buttons-each').eq(0).css('display','block');
                $jWDH(this).find('.wdhfbps-form-move-field-button').eq(0).css('display','block');
            },
            function(){
                $jWDH(this).find('.wdhfbps-form-edit-buttons-each').eq(0).css('display','none');
                $jWDH(this).find('.wdhfbps-form-move-field-button').eq(0).css('display','none');
            });
            
            // ----------------------------------- //
            // -------- Fields Actions ----------- //
            // ----------------------------------- //

            // Sort Fields
            $jWDH('.wdhfbps-form-container').sortable({
                    update: function( event, ui ) {
                         var currentElemnt = ui.item.context.id,
                             elements = $jWDH('.wdhfbps-is-sortable').length,
                             i=0,
                             positions = [],
                             element = [];

                         for(i=0;i<elements;i++){
                             element = {
                                 "id": $jWDH('.wdhfbps-is-sortable').eq(i).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                                 "position": i       
                             };
                             positions.push(element);
                         }

                         $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_position_new',form_id:formID,positions:positions}, function(data){
                         

            $jWDH(".wdhfbps-form-container").disableSelection();
                        });
                    },
            });
            $jWDH(".wdhfbps-form-container").disableSelection();


            // Resize Field
            $jWDH('.wdhfbps-form-field-container').resizable({
                 maxWidth: 910,
                 minWidth: 200,
                 minHeight: 20,
                 stop: function(event, ui){
                     var width = ui.size.width,
                         height = ui.size.height,
                         formWidth = $jWDH('#wdhfbps-form-'+formID).width(),
                         fieldData = $jWDH(this).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                         fieldID = fieldData.split('-')[0],
                         width=parseInt(width*100/formWidth);

                     $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).css('width',width+'%');
                     $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_size_new',field_id:fieldID,width:width,height:height}, function(data){
                         
                     });

                 },
            });

             // Sort Label / Input
            $jWDH('.wdhfbps-form-field-container').sortable({
                update: function( event, ui ) {
                    var currentElement = ui.item.context.id,
                        i=0,
                        positions = [],
                        elementType = 'label',
                        element = [],
                        firstElement = 0,
                        secondElement = 0,
                        position = 0,
                        fieldData = '',
                        fieldID = 0,
                        labelID = '',
                        inputID = '',
                        elements = $jWDH('.wdhfbps-is-label-input-sortable').length;

                        if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-0-type-label')){
                            position = 1;
                            elementType = 'label';
                            fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-label-id-'+formID+'-');
                            fieldID = fieldData[1];

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position       
                            };

                            $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-0-type-label');
                            $jWDH('#'+currentElement).addClass('wdhfbps-field-position-1-type-label');

                            positions.push(element);

                            position = 0;
                            inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-input';
                            elementType = 'input';

                            $jWDH('#'+inputID).removeClass('wdhfbps-field-position-1-type-input');
                            $jWDH('#'+inputID).addClass('wdhfbps-field-position-0-type-input');

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position      
                            };

                            positions.push(element);

                        } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-1-type-label')){
                            position = 0;
                            elementType = 'label';
                            fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-label-id-'+formID+'-');
                            fieldID = fieldData[1];

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position       
                            };

                            $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-1-type-label');
                            $jWDH('#'+currentElement).addClass('wdhfbps-field-position-0-type-label');

                            positions.push(element);

                            position = 1;
                            inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-input';
                            elementType = 'input';

                            $jWDH('#'+inputID).removeClass('wdhfbps-field-position-0-type-input');
                            $jWDH('#'+inputID).addClass('wdhfbps-field-position-1-type-input');

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position      
                            };

                            positions.push(element);

                        } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-1-type-input')){
                            position = 0;
                            elementType = 'input';
                            fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-input-id-'+formID+'-');
                            fieldID = fieldData[1];

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position       
                            };

                            $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-1-type-input');
                            $jWDH('#'+currentElement).addClass('wdhfbps-field-position-0-type-input');

                            positions.push(element);

                            position = 1;
                            inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-label';
                            elementType = 'label';

                            $jWDH('#'+inputID).removeClass('wdhfbps-field-position-0-type-label');
                            $jWDH('#'+inputID).addClass('wdhfbps-field-position-1-type-label');

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position      
                            };

                            positions.push(element);

                        } else if($jWDH('#'+currentElement).hasClass('wdhfbps-field-position-0-type-input')){
                            position = 1;
                            elementType = 'input';
                            fieldData = $jWDH('#'+currentElement).attr('id').split('wdh-form-field-input-id-'+formID+'-');
                            fieldID = fieldData[1];

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position       
                            };

                            $jWDH('#'+currentElement).removeClass('wdhfbps-field-position-0-type-input');
                            $jWDH('#'+currentElement).addClass('wdhfbps-field-position-1-type-input');

                            positions.push(element);

                            position = 0;
                            inputID = 'wdhfbps-form-'+formID+'-field-'+fieldID+'-label';
                            elementType = 'label';

                            $jWDH('#'+inputID).removeClass('wdhfbps-field-position-1-type-label');
                            $jWDH('#'+inputID).addClass('wdhfbps-field-position-0-type-label');

                            element = {
                                "id": fieldID,
                                "type": elementType,
                                "position": position      
                            };

                            positions.push(element);

                        }
                    
                    $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_input_position_new',form_id:formID,field_id:fieldID,positions:positions}, function(data){
                    
                   });
                }
            });
            $jWDH(".wdhfbps-form-field-container" ).disableSelection();

            // Resize Label
            $jWDH('.wdhfbps-form-field-label-container').resizable({
                 maxWidth: 910,
                 minWidth: 100,
                 minHeight: 20,
                 stop: function(event, ui){
                     var width = ui.size.width,
                         height = ui.size.height,
                         fieldData = $jWDH(this).attr('id').split('wdh-form-field-label-id-'+formID+'-')[1],
                         fieldID = fieldData.split('-')[0],
                         fieldWidth = $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).width(),
                         width=parseInt(width*100/fieldWidth);
                     $jWDH(this).css('width',width+'%');
                     $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_label_size_new',field_id:fieldID,width:width,height:height}, function(data){
                         
                     });
                 },
            });

            // Resize Input
            $jWDH('.wdhfbps-form-field-input-container').resizable({
                 maxWidth: 910,
                 minWidth: 100,
                 minHeight: 20,
                 stop: function(event, ui){
                     var width = ui.size.width,
                         height = ui.size.height,
                         fieldData = $jWDH(this).attr('id').split('wdhfbps-form-'+formID+'-field-')[1],
                         fieldID = fieldData.split('-')[0],
                         fieldWidth = $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).width(),
                         width=parseInt(width*100/fieldWidth);
                        
                     $jWDH(this).css('width',width+'%'); 
                     $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID+'-input').css('width',width+'%');
                     $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_input_size_new',field_id:fieldID,width:width,height:height}, function(data){
                       
                     });

                     // Input Height
                     $jWDH('#wdh-form-field-value-id-'+formID+'-'+fieldID).css({'height':height});
                 },
            });

            $jWDH('.wdhfbps-form-field-input-container').attr('id');
            $jWDH('.wdhfbps-form-field-input-container').css({'min-height':'20px'});
            $jWDH('.wdh-get-value').css({'width':'100%'});

        }
    });
}

// Duplicate Field
function deleteFormFieldBox(fieldID, formID){
    $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).css('display','none');
    $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).after('<div id="wdhfbps-form-'+formID+'-temporary-field-'+fieldID+'"><div class="wdhfbps-loader" style="margin-top:5px;display:block;"></div></div>');
    
    if (confirm(window.WDHFBPS_FBPS_CATEGORY_FIELD_DELETE_CONFIRMATION)){
        $jWDH.post(ajaxurl, {action: 'wdhfbps_delete_field',id:fieldID}, function(data){

            if (data == 'success'){
                $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).remove();
                $jWDH('#wdhfbps-form-'+formID+'-temporary-field-'+fieldID).remove();
            }
        });
    } else {
        $jWDH('#wdhfbps-form-'+formID+'-temporary-field-'+fieldID).remove();
        $jWDH('#wdhfbps-form-'+formID+'-field-'+fieldID).css('display','block');
    }
}