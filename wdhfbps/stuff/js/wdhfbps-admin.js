/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder
* Version                 : 1.0
* File                    : wdhfbps.admin.js
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Admin Javascript.
*/

var $jWDH = jQuery.noConflict();

$jWDH(document).ready(function(){
    
    // Adding $_GET variables
    
    var $_GET = {};

    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
        function decode(s) {
            return decodeURIComponent(s.split("+").join(" "));
        }

        $_GET[decode(arguments[1])] = decode(arguments[2]);
    });
    
    //wdh-slider-preview
    
    // Loading Pages
    
    var currentPage = $_GET['page'];
    
    switch(currentPage){
        case "wdhfbps":
            wdhShowCategories();
            break;
        case "wdhfbps-payments":
            wdhShowPayments();
            break;
        default:
            wdhShowCategories();
            break;
    }
   
    var allCategoryButtons = $jWDH('.wdhfbps-cat-but'),
        allForms = $jWDH('.wdhfbps-delete-customer'),
        allFields = $jWDH('.wdhfbps-field');
    
    // Close PopUP
    $jWDH('.close').click(function(){
        $jWDH('.WDHFBPS-backend-popup').fadeOut(500);
    });
    
    // Adding open/close action
    // Fields Settings 
    $jWDH('.wdhfbps-head').unbind('click');
    $jWDH('.wdhfbps-head').bind('click',function(){

        var isOpen = $jWDH(this).hasClass('wdhfbps-open'),
            id = 0;

        if (isOpen === true){

                id = $jWDH(this).attr('id').split('field-title-')[1];
                // Change Button
                $jWDH(this).removeClass('wdhfbps-open');
                $jWDH(this).addClass('wdhfbps-close');
                // Show 
                $jWDH('#field-content-'+id).slideDown(500);
            
        } else {

                id = $jWDH(this).attr('id').split('field-title-')[1];
                 // Change Button
                $jWDH(this).removeClass('wdhfbps-close');
                $jWDH(this).addClass('wdhfbps-open');
                $jWDH('#field-content-'+id).slideUp(300);
            
        }
    });

});

// General Settings
function wdhfbpsChangeLanguage(language){
    $jWDH.post(ajaxurl, {action: 'wdhfbps_change_language',
                         language: language}, function(data){        data = $jWDH.trim(data);
        window.location.reload();
    });
}

// Customer Details
function wdhfbpsShowMore(id){
    $jWDH('#wdhfbps-customer-'+id+' .fields .wdhfbps-customer-details').slideDown(500);
    $jWDH('#wdhfbps-customer-'+id+' .plus').css('display','none');
    $jWDH('#wdhfbps-customer-'+id+' .minus').css('display','block');
}

function wdhfbpsHideMore(id){
    $jWDH('#wdhfbps-customer-'+id+' .fields .wdhfbps-customer-details').slideUp(200);
    $jWDH('#wdhfbps-customer-'+id+' .plus').css('display','block');
    $jWDH('#wdhfbps-customer-'+id+' .minus').css('display','none');
}

function wdhfbpsShowAll(id){
    var customersClick = 'wdhfbpsHideAll('+id+');';
    $jWDH('#category-'+id+' .plus').eq(0).css('display','none');
    $jWDH('#category-'+id+' .minus').eq(0).css('display','block');
    $jWDH('#category-'+id+' .text-button').eq(0).attr('onclick',customersClick);
    $jWDH('#wdhfbps-show-category-'+id).fadeIn(300);
    $jWDH('#wdhfbps-show-category-'+id).addClass('wdhfbps-hide-category-all');
    $jWDH('#wdhfbps-show-category-'+id).removeClass('wdhfbps-show-category-all');
    $jWDH('#category-text-button-'+id).addClass('selected');
}

function wdhfbpsShowAllSecond(id){
    var customersClick = 'wdhfbpsHideAll('+id+');';
    $jWDH('#category-'+id+' .plus').eq(0).css('display','none');
    $jWDH('#category-'+id+' .minus').eq(0).css('display','block');
    $jWDH('#category-'+id+' .text-button').eq(0).attr('onclick',customersClick);
    $jWDH('#wdhfbps-show-category-'+id).fadeIn(300);
}

function wdhfbpsHideAll(id){
    var customersClick = 'wdhfbpsShowAll('+id+');';
    $jWDH('#category-'+id+' .plus').eq(0).css('display','block');
    $jWDH('#category-'+id+' .minus').eq(0).css('display','none');
    $jWDH('#category-'+id+' .text-button').eq(0).attr('onclick',customersClick);
    $jWDH('#wdhfbps-show-category-'+id).fadeOut(100);
    $jWDH('#wdhfbps-show-category-'+id).addClass('wdhfbps-show-category-all');
    $jWDH('#wdhfbps-show-category-'+id).removeClass('wdhfbps-hide-category-all');
    $jWDH('#category-text-button-'+id).removeClass('selected');
}

// Forms list
function wdhfbpsShowForms(id){
    var customersClick = 'wdhfbpsHideForms('+id+');';
    wdhfbpsShowAll(id);
    $jWDH('#customers-list-category-'+id).slideDown(300);
    $jWDH('#category-'+id+' .plus').eq(0).css('display','none');
    $jWDH('#category-'+id+' .minus').eq(0).css('display','block');
    $jWDH('#category-'+id+' .text-button').eq(0).attr('onclick',customersClick);
    
}

function wdhfbpsHideForms(id){
    var customersClick = 'wdhfbpsShowForms('+id+');';
    wdhfbpsHideAll(id);
    $jWDH('#customers-list-category-'+id).slideUp(100);
    $jWDH('#category-'+id+' .plus').eq(0).css('display','block');
    $jWDH('#category-'+id+' .minus').eq(0).css('display','none');
    $jWDH('#category-'+id+' .text-button').eq(0).attr('onclick',customersClick);
}

// Categories

function wdhShowCategories(){
    // Display Loader
    if (window.jQuery) {  
    // jQuery is loaded  
   }
    wdhLoaderMessage('wdhfbps-loader-categories', 'display', window.WDHFBPS_FBPS_CUSTOM_POSTS_LOADING);
    // Load Forms
    $jWDH.post(ajaxurl, {action: 'wdhfbps_show_categories'}, function(data){
        // Display Forms
        $jWDH('#all-forms').wdhfbpsForms('display-forms',data);    

        // Display Loader
        wdhLoaderMessage('wdhfbps-loader-categories', 'hide', window.WDHFBPS_FBPS_CUSTOM_POSTS_LOADED);
        
    });

}

/* Fields Settings*/

// Display New Field Box
function wdhfbpsNewField(catID){
    $jWDH('#wdhfbps-new-field-box').fadeIn(500);
    $jWDH('#field-category-id').val(catID);
}

function wdhfbpsAddField(){
    var name = $jWDH('#field-name').val(),
        catID = $jWDH('#field-category-id').val(),
        formID = $jWDH('#field-form-id').val(),
        popupContentHTML = $jWDH('#wdhfbps-new-field-box .content').html(),
        popupLoaderHTML = '<div class="wdhfbps-loader">&nbsp;</div>',
        newFIELDHTML = new Array(),
        errorTEXT = '';

        if(name.length < 1){
            errorTEXT= window.WDHFBPS_FBPS_ERROR_TEXT+' '+window.WDHFBPS_FBPS_CATEGORY_FIELD_NAME;
        }
        
        if (errorTEXT === ""){
            
            // Adding loader
            $jWDH('#wdhfbps-new-field-box .content').html(popupLoaderHTML);
            $jWDH('#wdhfbps-new-field-box .content').addClass('small-content');

            // Adding category
            $jWDH.post(ajaxurl, {action: 'wdhfbps_add_field',
                                 name: name,
                                 cat_id: catID,
                                 form_id: formID}, function(data){      data = $jWDH.trim(data);
                   var id = data.split('#@#')[0],
                       fieldName = data.split('#@#')[1],
                       fieldNameTranslation = data.split('#@#')[2],
                       returndata = data.split('#@#')[3],
                       fieldTitleParagraph = data.split('#@#')[7];
                       fieldNameTranslation = wdhfbpsReplace('"',"#",fieldNameTranslation);
                       
                   var fieldValuesTranslation = data.split('#@#')[4];
                       fieldValuesTranslation = wdhfbpsReplace('"',"#",fieldValuesTranslation);
                       
                    var fieldTitleParagraphTranslation = data.split('#@#')[6],
                        fieldTitleParagraphTranslation = wdhfbpsReplace('"',"#",fieldTitleParagraphTranslation);

                if (returndata === 'success'){
                    // Remove
                    $jWDH('#wdhfbps-fields-no-fields-'+catID).remove();
                    newFIELDHTML.push('<div class="wdhfbps-field-settings wdhfbps-field3 wdhfbps-is-sortable" id="wdhfbps-field-'+id+'">');
                    newFIELDHTML.push(' <div class="wdhfbps-head wdhfbps-open" id="field-title-'+id+'">');
                    newFIELDHTML.push('     <div class="wdhfbps-head-title">'+fieldName+'</div>');
                    newFIELDHTML.push('     <div id="field-loader-'+id+'" class="wdhfbps-loader">&nbsp;</div>');
                    newFIELDHTML.push('     <div id="field-success-'+id+'" class="wdhfbps-success">[ '+window.WDHFBPS_FBPS_CATEGORY_FIELD_SAVED+' ]</div>');
                    newFIELDHTML.push(' </div>');
                    newFIELDHTML.push(' <div class="wdhfbps-content" id="field-content-'+id+'">');
                    newFIELDHTML.push('     <div class="box">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(             window.WDHFBPS_FBPS_CATEGORY_FIELD_NAME+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('         <input type="text" name="field-name-'+id+'" id="field-name-'+id+'" onkeyup="wdhfbpsPreviewName('+id+',this.value);" onblur="wdhfbpsPreviewName('+id+',this.value);" class="field-box wdhfbps-field-name" value="'+fieldName+'" />');
                    newFIELDHTML.push('         <input type="hidden" name="field-name-translation-'+id+'" id="field-name-translation-'+id+'" value="'+fieldNameTranslation+'" />');
                    newFIELDHTML.push('         <input type="hidden" name="field-values-translation-'+id+'" id="field-values-translation-'+id+'" value="'+fieldValuesTranslation+'" />');
                    newFIELDHTML.push('         <input type="hidden" name="field-category-'+id+'" id="field-category-'+id+'" value="'+catID+'" />');
                    newFIELDHTML.push('         <input type="hidden" name="field-title-paragraph-translation-'+id+'" id="field-title-paragraph-translation-'+id+'" value="'+fieldTitleParagraphTranslation+'" />');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('     <div class="box">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(             window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('         <div class="check-box">');
                    newFIELDHTML.push('             <select name="field-type-'+id+'" id="field-type-'+id+'" onchange="wdhfbpsChangeFieldType('+id+',this.value);" class="field-box" style="margin-top: 2px;">');
                    newFIELDHTML.push('                 <option disabled="disabled">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_COMMON+'</option>');
                    newFIELDHTML.push('                 <option value="text">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_TEXT+'</option>');
                    newFIELDHTML.push('                 <option value="title">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_TITLE+'</option>');
                    newFIELDHTML.push('                 <option value="paragraph">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_PARAGRAPH+'</option>');
                    newFIELDHTML.push('                 <option value="username">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_USERNAME+'</option>');
                    newFIELDHTML.push('                 <option value="password">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_PASSWORD+'</option>');
                    newFIELDHTML.push('                 <option value="textarea">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_TEXTAREA+'</option>');
                    newFIELDHTML.push('                 <option value="select">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_SELECT+'</option>');
                    newFIELDHTML.push('                 <option value="radio">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_RADIO+'</option>');
                    newFIELDHTML.push('                 <option value="checkbox">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_CHECKBOX+'</option>');
                    newFIELDHTML.push('                 <option value="captcha">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_CAPTCHA+'</option>');
                    newFIELDHTML.push('                 <option value="submit">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_SUBMIT+'</option>');
                    newFIELDHTML.push('             </select>');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('             <div class="check-box">&nbsp;</div>');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="box">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(              window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('         <select name="field-filter-'+id+'" id="field-filter-'+id+'" class="field-box">');
                    newFIELDHTML.push('             <option value="no">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_NO+'</option>');
                    newFIELDHTML.push('             <option value="is_email">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_EMAIL+'</option>');
                    newFIELDHTML.push('             <option value="is_phone">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_PHONE+'</option>');
                    newFIELDHTML.push('             <option value="is_url">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_URL+'</option>');
                    newFIELDHTML.push('             <option value="is_date">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_DATE+'</option>');
                    newFIELDHTML.push('             <option value="is_alpha">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_ALPHA+'</option>');
                    newFIELDHTML.push('             <option value="is_numeric">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_NUMERIC+'</option>');
                    newFIELDHTML.push('             <option value="is_alphanumeric">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_ALPHANUMERIC+'</option>');
                    newFIELDHTML.push('             <option value="is_unique">'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_UNIQUE+'</option>');
                    newFIELDHTML.push('         </select>');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="box">');
                    newFIELDHTML.push('         <div class="check-box">');
                    newFIELDHTML.push('             <div style="width:350px; margin-left:157px; float:left; margin-bottom:5px; margin-top:10px;">');
                    newFIELDHTML.push('                 <input type="checkbox" id="field-is-required-'+id+'" checked="checked" /> '+window.WDHFBPS_FBPS_CATEGORY_FIELD_CAN_BE_EMPTY);
                    newFIELDHTML.push('             </div>');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="box wdhfbps-title-paragraph-selected" style="display:none;">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(              window.WDHFBPS_FBPS_CATEGORY_FIELD_YOUR_TEXT+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('         <textarea name="field-title-paragraph-'+id+'" id="field-title-paragraph-'+id+'" onkeyup="wdhfbpsPreviewTitleParagraph('+id+',this.value);" onblur="wdhfbpsPreviewTitleParagraph('+id+',this.value);"  onpaste="wdhfbpsPreviewTitleParagraph('+id+',this.value);" class="field-box">'+fieldTitleParagraph+'</textarea>');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="box wdhfbps-select-radio-checkbox-selected" style="display:none;">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(              window.WDHFBPS_FBPS_CATEGORY_FIELD_VALUES_LIST+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('     <input type="text" name="field-values-list-'+id+'" id="field-values-list-'+id+'" onkeyup="wdhfbpsPreviewValues('+id+',this.value);" onblur="wdhfbpsPreviewValues('+id+',this.value);" class="field-box" placeholder="'+window.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_VALUES+'"/>');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="box wdhfbps-map-video-selected" style="display:none;">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(              window.WDHFBPS_FBPS_CATEGORY_FIELD_WIDTH+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('         <input type="text" name="field-width-'+id+'" id="field-width-'+id+'" class="field-small-box" value="100" /> %');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="box wdhfbps-map-video-selected">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(              window.WDHFBPS_FBPS_CATEGORY_FIELD_HEIGHT+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('         <input type="text" name="field-height-'+id+'" id="field-height-'+id+'" class="field-small-box" value="20" /> px');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="box wdhfbps-map-selected" style="display:none;">');
                    newFIELDHTML.push('         <div class="title-box">');
                    newFIELDHTML.push(              window.WDHFBPS_FBPS_CATEGORY_FIELD_ZOOM+' :');
                    newFIELDHTML.push('         </div>');
                    newFIELDHTML.push('         <input type="text" name="field-zoom-'+id+'" id="field-zoom-'+id+'" class="field-small-box" value="12" />');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push('     <div class="wdhfbps-buttons">');
                    newFIELDHTML.push('         <input type="button" class="wdhfbps-button" id="field-submit-'+id+'" onclick="wdhfbpsSaveField('+id+');" value="'+window.WDHFBPS_FBPS_CUSTOMER_SAVE+'"/>');
                    newFIELDHTML.push('         <input type="button" class="wdhfbps-button" id="field-delete-'+id+'" onclick="wdhfbpsDeleteField('+id+');" value="'+window.WDHFBPS_FBPS_CATEGORY_FIELD_DELETE+'" />');
                    newFIELDHTML.push('     </div>');
                    newFIELDHTML.push(' </div>');
                    newFIELDHTML.push('</div>');

                    // Adding New Profile
                    $jWDH('#wdhfbps-fields-'+catID).append(newFIELDHTML.join(''));
                    // Hide Popup
                    $jWDH('.WDHFBPS-backend-popup').fadeOut(500,function(){
                        //wdhfbpsShowFieldsSettings(id);
                    });

                    // Clear name
                    $jWDH('#field-name').val('');

                    // Adding form back
                    $jWDH('#wdhfbps-new-field-box .content').removeClass('small-content');
                    // Hide Popup
                    $jWDH('.WDHFBPS-backend-popup').fadeOut(500,function(){

                        // Adding form back
                        $jWDH('#wdhfbps-new-field-box .content').html(popupContentHTML);
                        // Change Button
                        $jWDH('#field-title-'+id).removeClass('wdhfbps-open');
                        $jWDH('#field-title-'+id).addClass('wdhfbps-close');
                        // Show Field
                        $jWDH('#field-content-'+id).slideDown(500);

                        // Adding open/close action
                        // Fields Settings 
                        $jWDH('.wdhfbps-head').unbind('click');
                        $jWDH('.wdhfbps-head').bind('click',function(){

                            var isOpen = $jWDH(this).hasClass('wdhfbps-open'),
                                id = 0;

                            if (isOpen === true){

                                
                                    id = $jWDH(this).attr('id').split('field-title-')[1];
                                    // Change Button
                                    $jWDH(this).removeClass('wdhfbps-open');
                                    $jWDH(this).addClass('wdhfbps-close');
                                    // Show 
                                    $jWDH('#field-content-'+id).slideDown(500);
                                
                            } else {

                                    id = $jWDH(this).attr('id').split('field-title-')[1];
                                     // Change Button
                                    $jWDH(this).removeClass('wdhfbps-close');
                                    $jWDH(this).addClass('wdhfbps-open');
                                    $jWDH('#field-content-'+id).slideUp(300);
                                
                            }
                        });
                        
                         // Sortable Fields
                        $jWDH( ".wdhfbps-fields-move" ).sortable({
                            update: function( event, ui ) {
                                var currentElemnt = ui.item.context.id,
                                    elements = $jWDH('.wdhfbps-is-sortable').length,
                                    i=0,
                                    positions = [],
                                    element = [];
                                for(i=0;i<elements;i++){
                                    element = {
                                        "id": $jWDH('.wdhfbps-is-sortable').eq(i).attr('id').split('-')[2],
                                        "position": i       
                                    };
                                    positions.push(element);
                                }

                                $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_position_new',form_id:catID,positions:positions}, function(data){
                                });
                            }
                        });
                        $jWDH( ".wdhfbps-fields-move" ).disableSelection();
                    });
                }
            });
        
    } else {
        alert(errorTEXT);
    }
}

// == > Change Field Type
function wdhfbpsChangeFieldType(fieldID,fieldType){
    $jWDH('#field-content-'+fieldID+' .wdhfbps-title-paragraph-selected').css('display','none');
    
    // Select , Radio , Checkbox
    if (fieldType === 'select' || fieldType === 'radio' || fieldType === 'checkbox'){
        $jWDH('#field-content-'+fieldID+' .wdhfbps-select-radio-checkbox-selected').css('display','block');
    } else {
        $jWDH('#field-content-'+fieldID+' .wdhfbps-select-radio-checkbox-selected').css('display','none');
    }
    
    // Title , Paragraph
    if (fieldType === 'title' || fieldType === 'paragraph'){
        $jWDH('#field-content-'+fieldID+' .wdhfbps-title-paragraph-selected').css('display','block');
        $jWDH('#field-content-'+fieldID+' .wdhfbps-filter-selected').css('display','none');
        $jWDH('#field-content-'+fieldID+' .wdhfbps-required-selected').css('display','none');
    } else {
        $jWDH('#field-content-'+fieldID+' .wdhfbps-title-paragraph-selected').css('display','none');
        $jWDH('#field-content-'+fieldID+' .wdhfbps-filter-selected').css('display','block');
        $jWDH('#field-content-'+fieldID+' .wdhfbps-required-selected').css('display','block');
    }
}

// Preview Name
function wdhfbpsPreviewName(fieldID,value){
    var valueHTMl = value,
        fieldTranslation = JSON.parse(wdhfbpsReplace('#','"',$jWDH('#field-name-translation-'+fieldID).val())),
        fieldCategory = $jWDH('#field-category-'+fieldID).val(),
        fieldLanguage = $jWDH('#category-settings-language-'+fieldCategory).val(),
        newTranslation = '',
        i = 0;

    newTranslation += '{';
    
    $jWDH.each(fieldTranslation,function(key){
        
        if (i < 1){
        
            if (key === fieldLanguage){

               newTranslation += '#'+key+'#: '+'#'+value+'#';

            } else {
                
                newTranslation += '#'+key+'#: '+'#'+fieldTranslation[key]+'#';
                
            }
        } else {
            
            if (key === fieldLanguage){

               newTranslation += ', #'+key+'#: '+'#'+value+'#';

            } else {
                
                newTranslation += ', #'+key+'": '+'#'+fieldTranslation[key]+'#';
                
            }
            
        }
        
        i++;
    });
    
    newTranslation += '}';
    
    // Set New Translation Name 
    $jWDH('#field-name-translation-'+fieldID).val(newTranslation);
    $jWDH('#wdhfbps-field-'+fieldID+' .wdhfbps-head-title').html(valueHTMl);
}


// Preview Values
function wdhfbpsPreviewValues(fieldID,value){
    var valueHTMl = value,
        fieldTranslation = JSON.parse(wdhfbpsReplace('#','"',$jWDH('#field-values-translation-'+fieldID).val())),
        fieldCategory = $jWDH('#field-category-'+fieldID).val(),
        fieldLanguage = $jWDH('#category-settings-language-'+fieldCategory).val(),
        newTranslation = '',
        i = 0;

    newTranslation += '{';
    
    $jWDH.each(fieldTranslation,function(key){
        
        if (i < 1){
        
            if (key === fieldLanguage){

               newTranslation += '#'+key+'#: '+'#'+value+'#';

            } else {
                
                newTranslation += '#'+key+'#: '+'#'+fieldTranslation[key]+'#';
                
            }
        } else {
            
            if (key === fieldLanguage){

               newTranslation += ', #'+key+'#: '+'#'+value+'#';

            } else {
                
                newTranslation += ', #'+key+'": '+'#'+fieldTranslation[key]+'#';
                
            }
            
        }
        
        i++;
    });
    
    newTranslation += '}';
    
    // Set New Translation Vaues 
    $jWDH('#field-values-translation-'+fieldID).val(newTranslation);
}

// Preview Title Paragraph
function wdhfbpsPreviewTitleParagraph(fieldID,value){
    var valueHTMl = value,
        fieldTranslation = JSON.parse(wdhfbpsReplace('#','"',$jWDH('#field-title-paragraph-translation-'+fieldID).val())),
        fieldCategory = $jWDH('#field-category-'+fieldID).val(),
        fieldLanguage = $jWDH('#category-settings-language-'+fieldCategory).val(),
        newTranslation = '',
        i = 0;

    newTranslation += '{';
    
    $jWDH.each(fieldTranslation,function(key){
        
        if (i < 1){
        
            if (key === fieldLanguage){

               newTranslation += '#'+key+'#: '+'#'+value+'#';

            } else {
                
                newTranslation += '#'+key+'#: '+'#'+fieldTranslation[key]+'#';
                
            }
        } else {
            
            if (key === fieldLanguage){

               newTranslation += ', #'+key+'#: '+'#'+value+'#';

            } else {
                
                newTranslation += ', #'+key+'": '+'#'+fieldTranslation[key]+'#';
                
            }
            
        }
        
        i++;
    });
    
    newTranslation += '}';
    
    // Set New Translation Display Value 
    $jWDH('#field-title-paragraph-translation-'+fieldID).val(newTranslation);
}

// Preview Name and Values List
function wdhfbpsChangeName(catID,language){
    var field = $jWDH('#category-settings-'+catID+' .wdhfbps-field-name');
    
    field.each(function(key){

        var fieldID = $jWDH(field[key]).attr('id').split('field-name-')[1],
            fieldTranslation = JSON.parse(wdhfbpsReplace('#','"',$jWDH('#field-name-translation-'+fieldID).val())),
            fieldName = '',
            valuesTranslation = JSON.parse(wdhfbpsReplace('#','"',$jWDH('#field-values-translation-'+fieldID).val())),
            fieldValue = '',
            valueHTMl = '';

        $jWDH.each(fieldTranslation,function(keysec){

                if (keysec === language){

                   fieldName = fieldTranslation[keysec];

                }
        });
        
        $jWDH.each(valuesTranslation,function(keysec){

                if (keysec === language){

                   fieldValue = valuesTranslation[keysec];

                }
        });
        
         valueHTMl = fieldName;
    
        // Set New Name 
        $jWDH('#field-name-'+fieldID).val(fieldName);
        $jWDH('#wdhfbps-field-'+fieldID+' .wdhfbps-head-title').html(valueHTMl);
        // Set Values List
        $jWDH('#field-values-list-'+fieldID).val(fieldValue);
    });
   
}

// Save Field
function wdhfbpsSaveField(fieldID){
    var fieldName = $jWDH('#field-name-'+fieldID).val(),
        fieldNameTranslation = $jWDH('#field-name-translation-'+fieldID).val(),
        fieldType = $jWDH('#field-type-'+fieldID).val(),
        fieldFilter = $jWDH('#field-filter-'+fieldID).val(),
        fieldRequired = $jWDH('#field-is-required-'+fieldID).prop('checked'),
        fieldValuesList = $jWDH('#field-values-list-'+fieldID).val(),
        fieldValuesTranslation = $jWDH('#field-values-translation-'+fieldID).val(),
        fieldTitleParagraphTranslation = $jWDH('#field-title-paragraph-translation-'+fieldID).val(),
        fieldWidth = $jWDH('#field-width-'+fieldID).val(),
        fieldHeight = $jWDH('#field-height-'+fieldID).val(),
        fieldZoom = $jWDH('#field-zoom-'+fieldID).val(),
        fieldDisplay = $jWDH('#field-display-'+fieldID).val(),
        fieldDisplayLabel = $jWDH('#field-display-label-'+fieldID).prop('checked'),
        fieldDisplayValue = $jWDH('#field-display-value-'+fieldID).prop('checked'),
        fieldDisplayPosition = $jWDH('#field-display-position-'+fieldID).val(),
        fieldFee = $jWDH('#field-fee-'+fieldID).val(),
        is_email = false,
        is_url = false,
        is_phone = false,
        is_alpha = false,
        is_numeric = false,
        is_alphanumeric = false,
        is_unique = false;
    
        
    if (fieldFilter === 'is_email'){
        is_email = true;
    } 
    
    if (fieldFilter === 'is_url'){
        is_url = true;
    }
    
    if (fieldFilter === 'is_phone'){
        is_phone = true;
    }
    
    if (fieldFilter === 'is_alpha'){
        is_alpha = true;
    }
    
    if (fieldFilter === 'is_numeric'){
        is_numeric = true;
    }
    
    if (fieldFilter === 'is_alphanumeric'){
        is_alphanumeric = true;
    }
    
    if (fieldFilter === 'is_unique'){
        is_unique = true;
    }
    
    // Inverse values Required
    if (fieldRequired === false){
        fieldRequired = 'true';
    } else {
        fieldRequired = 'false';
    }
    
    // Adding Loader 
    $jWDH('#field-success-'+fieldID).css('display','none');
    $jWDH('#field-loader-'+fieldID).css('display','block');
    // Saving Data
    $jWDH.post(ajaxurl, {action: 'wdhfbps_save_field',
                         id: fieldID,
                         fee_id: fieldFee,
                         name: fieldNameTranslation,
                         edit_type: fieldType,
                         is_required: fieldRequired,
                         is_email: is_email,
                         is_url: is_url,
                         is_phone: is_phone,
                         is_alpha: is_alpha,
                         is_numeric: is_numeric,
                         is_alphanumeric: is_alphanumeric,
                         is_unique: is_unique,
                         width: fieldWidth,
                         height: fieldHeight,
                         zoom: fieldZoom,
                         display: fieldDisplay,
                         display_field_label:fieldDisplayLabel,
                         display_field_value:fieldDisplayValue,
                         display_position: fieldDisplayPosition,
                         title_paragraph: fieldTitleParagraphTranslation,
                         values_list: fieldValuesTranslation
                        }, function(data){console.log(data);
        
        data = $jWDH.trim(data,fieldValuesList);
        
        if (data === 'success'){
            $jWDH('#field-loader-'+fieldID).css('display','none');
            $jWDH('#field-success-'+fieldID).fadeIn(500);
        }
    });
}

// Change Field Display Type
function wdhChangeFieldDisplayType(fieldID,value){
    if (value === 'in_content'){
        $jWDH('#field-display-position-content-'+fieldID).css('display','block');
    } else {
        $jWDH('#field-display-position-content-'+fieldID).css('display','none');
    }
}

// Show Fields Settings
function wdhfbpsShowFieldsSettings(catId){
    wdhfbpsShowAllSecond(catId);

    $jWDH('#category-'+catId+'-tools').addClass('selected');
    $jWDH('#category-'+catId+'-visual-editor').removeClass('selected');
    $jWDH('#category-'+catId+'-messages').removeClass('selected');
    $jWDH('#category-settings-'+catId).slideDown(500);
    $jWDH('#visual-editor-'+catId).slideUp(300);
    $jWDH('#messages-category-'+catId).slideUp(300);
    $jWDH('#customers-list-category-'+catId).slideUp(300);
    
    // Load Data if is not Loaded
    if (!$jWDH('#category-settings-'+catId).hasClass('data-loaded')) {
        $jWDH('#category-settings-'+catId+' .wdhfbps-loader-off').css('display','block');
        $jWDH('#category-settings-new-'+catId).remove();
        // Get Data 
        $jWDH.post(ajaxurl, {action: 'wdhfbps_display_form_settings',cat_id:catId}, function(data){

            if (data) {
                //$jWDH('#category-settings-'+catId).addClass('data-loaded');
                $jWDH('#category-settings-'+catId+' .wdhfbps-loader-off').css('display','none');
                var dataNew = new Array();
                dataNew.push('<div style="width:100%;float:left;display:none;" class="wdhfbps-loader-off"></div>');
                dataNew.push('<div style="width:100%;float:left;display:none;" id="category-settings-new-'+catId+'">');
                dataNew.push(   data);
                dataNew.push('</div>');
                $jWDH('#category-settings-'+catId).html(dataNew.join(''));
                $jWDH('#category-settings-new-'+catId).fadeIn("slow");
                
                // Adding Events
                var allFields = $jWDH('.wdhfbps-field');
                
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
                
                // FIX Image CSS
                $jWDH('.uploadify').css({"float": "left","margin-top":"9px","margin-right":"5px"});
                
                // Fields Settings 
                $jWDH('.wdhfbps-head').unbind('click');
                $jWDH('.wdhfbps-head').bind('click',function(){

                    var isOpen = $jWDH(this).hasClass('wdhfbps-open'),
                        id =0;
                        
                    if (isOpen === true){

                      
                            id = $jWDH(this).attr('id').split('field-title-')[1];
                            // Change Button
                            $jWDH(this).removeClass('wdhfbps-open');
                            $jWDH(this).addClass('wdhfbps-close');
                            // Show 
                            $jWDH('#field-content-'+id).slideDown(500);
                        
                    } else {

                            id = $jWDH(this).attr('id').split('field-title-')[1];
                             // Change Button
                            $jWDH(this).removeClass('wdhfbps-close');
                            $jWDH(this).addClass('wdhfbps-open');
                            $jWDH('#field-content-'+id).slideUp(300);
                        
                    }
                });

                // Sortable Fields
                $jWDH( ".wdhfbps-fields-move" ).sortable({
                    update: function( event, ui ) {
                        var currentElemnt = ui.item.context.id,
                            elements = $jWDH('.wdhfbps-is-sortable').length,
                            i=0,
                            positions = [],
                            element = [];
                        
                        for(i=0;i<elements;i++){
                            element = {
                                "id": $jWDH('.wdhfbps-is-sortable').eq(i).attr('id').split('-')[2],
                                "position": i       
                            };
                            positions.push(element);
                        }
                        
                        $jWDH.post(ajaxurl, {action: 'wdhfbps_update_form_fields_position',form_id:catId,positions:positions}, function(data){
                            
                        });
                    }
                });
                $jWDH( ".wdhfbps-fields-move" ).disableSelection();
                
            }
        });
    }
}

// Show Visual Editor
function wdhfbpsShowVisualEditor(catId){
    wdhfbpsShowAllSecond(catId);

    $jWDH('#category-'+catId+'-tools').removeClass('selected');
    $jWDH('#category-'+catId+'-visual-editor').addClass('selected');
    $jWDH('#category-'+catId+'-messages').removeClass('selected');
    $jWDH('#category-settings-'+catId).slideUp(300);
    $jWDH('#visual-editor-'+catId).slideDown(500);
    $jWDH('#messages-category-'+catId).slideUp(300);
    $jWDH('#customers-list-category-'+catId).slideUp(300);
    
    // Load Data if is not Loaded
    if (!$jWDH('#visual-editor-'+catId).hasClass('data-loaded')) {
        $jWDH('#visual-editor-'+catId+' .wdhfbps-loader-off').css('display','block');
        $jWDH('#visual-editor-new-'+catId).remove();
        // Get Data 
        $jWDH.post(ajaxurl, {action: 'wdhfbps_display_form_visual_editor',cat_id:catId}, function(data){

            if (data) {
                //$jWDH('#visual-editor-'+catId).addClass('data-loaded');
                $jWDH('#visual-editor-'+catId+' .wdhfbps-loader-off').css('display','none');
                var dataNew = new Array();
                dataNew.push('<div style="width:100%;float:left;display:none;" class="wdhfbps-loader-off"></div>');
                dataNew.push('<div style="width:100%;float:left;display:none;" id="visual-editor-new-'+catId+'">');
                dataNew.push(   data);
                dataNew.push('</div>');
                $jWDH('#visual-editor-'+catId).html(dataNew.join(''));
                $jWDH('#visual-editor-new-'+catId).fadeIn("slow");
                // Adding Events
                var allFields = $jWDH('.wdhfbps-field');
                
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
                
                // Add Edit Form Button
                $jWDH('.wdhfbps-form-container').hover(function(){
                    $jWDH(this).find('.wdhfbps-form-edit-buttons').eq(0).fadeIn(300);
                },
                function(){
                    $jWDH(this).find('.wdhfbps-form-edit-buttons').eq(0).fadeOut(100);
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

                // Adding Visual Editor Events
                $jWDH('#wdhfbps-form-'+catId).wdhfbpsVisualEditor(catId);
                
                // ADD Colorpicker
                $jWDH('.wdh-colorpicker-preview').click(function(){
                    window.colorpickerID = $jWDH(this).attr('id');
                });
                $jWDH('.wdh-colorpicker-preview').ColorPicker({color:'ffffff',
                    onChange: function(hsb, hex, rgb){
                        $jWDH('.wdh-colorpicker-preview').css('background','#'+hex);
                        $jWDH('.wdh-colorpicker-preview').val(hex);
                        $jWDH('.wdh-colorpicker-preview').ColorPickerSetColor(hex);
                    },
                    onSubmit:function(hsb, hex, rgb){
                        // Hide colorpicker
                        $jWDH('.colorpicker').css('display','none');
                        // Saving data ( in background )
                        var idMOD = window.colorpickerID.split('wdh-form-field-colorpicker-id-'),
                            id = '#wdh-form-field-value-id-'+idMOD[1];
                            $jWDH(id).val(hex);
                    }
                });
                $jWDH('.colorpicker').css('z-index','10');
                
            }
        });
    }
}

// Show Messages
function wdhfbpsShowMessagesSettings(catId){
    wdhfbpsShowAllSecond(catId);

    $jWDH('#category-'+catId+'-tools').removeClass('selected');
    $jWDH('#category-'+catId+'-visual-editor').removeClass('selected');
    $jWDH('#category-'+catId+'-messages').addClass('selected');
    $jWDH('#category-settings-'+catId).slideUp(300);
    $jWDH('#visual-editor-'+catId).slideUp(300);
    $jWDH('#messages-category-'+catId).slideDown(500);
    $jWDH('#customers-list-category-'+catId).slideUp(300);
    
    // Load Data if is not Loaded
    if (!$jWDH('#messages-category-'+catId).hasClass('data-loaded')) {
        $jWDH('#messages-category-'+catId+' .wdhfbps-loader-off').css('display','block');
        // Get Data 
        $jWDH.post(ajaxurl, {action: 'wdhfbps_display_form_messages',cat_id:catId}, function(data){

            if (data) {
                $jWDH('#messages-category-'+catId).addClass('data-loaded');
                $jWDH('#messages-category-'+catId+' .wdhfbps-loader-off').css('display','none');
                var dataNew = new Array();
                dataNew.push('<div style="width:100%;float:left;display:none;" id="messages-category-new-'+catId+'">');
                dataNew.push(   data);
                dataNew.push('</div>');
                $jWDH('#messages-category-'+catId).append(dataNew.join(''));
                $jWDH('#messages-category-new-'+catId).fadeIn("slow");
                
                // Adding Events
                var allFields = $jWDH('.wdhfbps-field');
                
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
            }
        });
    }
}

function wdhfbpsAddShortCode(fieldID, type){
    
    var shortcodeHTML = '',
        language      = $jWDH('#WDHFBPS-language').val();
    
    switch(type) {
        case "field-label":
            shortcodeHTML = '[wdhfbps-label id=\"'+fieldID+'\" lang=\"'+language+'\"]';
            break;
        case "field-value":
            shortcodeHTML = '[wdhfbps-value id=\"'+fieldID+'\" lang=\"'+language+'\"]';
            break;
        default:
            shortcodeHTML = '[wdhfbps-value id=\"'+fieldID+'\" lang=\"'+language+'\"]';
            break;
    }
    // Adding ShortCode
    window.tinyMCE.activeEditor.selection.setContent(shortcodeHTML);
    // Go to Content
    $jWDH('html, body').animate({
        scrollTop: $jWDH("#title").offset().top
    }, 500);
    
}

// Delete Field
function wdhfbpsDeleteField(fieldID){
    if (confirm(window.WDHFBPS_FBPS_CATEGORY_FIELD_DELETE_CONFIRMATION)){
        $jWDH('#field-loader-'+fieldID).css('display','block');
        $jWDH.post(ajaxurl, {action: 'wdhfbps_delete_field',
                             id: fieldID}, function(data){        data = $jWDH.trim(data);

            if (data === 'success'){
                $jWDH('#wdhfbps-field-'+fieldID).slideUp(100);
            }
        });
    }
}

// Change Language for fields in Forms
function wdhfbpsChangeLanguageFields(language,formID){
    // Removing current Fields
    $jWDH('#wdhfbps-fields-content-all-form .WDHFBPS-backend-box').remove();
    $jWDH('#wdhfbps-fields-content-all-form .wdhfbps-loader').css('display','block');
    
    // Reloading Fields
    $jWDH.post(ajaxurl, {action: 'wdhfbps_show_fields_by_language',
                         language: language,
                         formID: formID}, function(data){ 
        $jWDH('#wdhfbps-fields-content-all-form .wdhfbps-loader').before(data);
    });
    
}

// ON/OFF Custom Fields
function wdhfbpsShowHideFields(id,status){
    if (status === 'true') {
        $jWDH('#wdhfbps-custom-fields-all-display-for-'+id).slideDown(500);
    } else {
        $jWDH('#wdhfbps-custom-fields-all-display-for-'+id).slideUp(100);
    }
}

function wdhfbpsEscapeRegExp(str) {
  return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

function wdhfbpsReplace(find, replace, str){
    return str.replace(new RegExp(wdhfbpsEscapeRegExp(find), 'g'), replace);
}

// Cookies 
function wdhCreateCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function wdhReadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function wdhEraseCookie(name) {
    wdhCreateCookie(name,"",-1);
}

function wdhLoaderMessage(id, action, message){
    if (action === 'display'){       
        $jWDH('#'+id).addClass('wdhfbps-loader-off');
        $jWDH('#'+id).html(message);
        $jWDH('#'+id).stop(true, true).animate({'opacity':1}, 400);
    }
    else{
        $jWDH('#'+id).removeClass('wdhfbps-loader-off');
        $jWDH('#'+id).html(message);
        
        timeOut = setTimeout(function(){
            $jWDH('#'+id).stop(true, true).animate({'opacity':0}, 400, function(){
                $jWDH('#'+id).html('');
            });
        }, 2200);
    }
}
