/*
Project Name: WDH - Form Generator ( EDF LIGHT Extension )
Project Version: 1.0
Project URL: http://www.wdh.im/projects/form-generator-edf-light-extesion/
Author: WDH - Web Developers House
Author URL: http://www.wdh.im/
File Path: js/jquery.wdh.im.formgenerator.js
File Description: WDH - Form Generator Scripts 
File Version: 1.0
Last Update File : 04.10.2014
*/
        
var $jWDH = jQuery.noConflict();
    window.isStop = false;
    //request_url = window.ajaxurl;
var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();
  
// Adding $_GET variables

var $_GET = {};

document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
    function decode(s) {
        return decodeURIComponent(s.split("+").join(" "));
    }

    $_GET[decode(arguments[1])] = decode(arguments[2]);
});

$jWDH(document).ready(function(){
    
    // Adding Referal Cookie
    if (typeof $_GET['ref'] != 'undefined') {
        wdhEraseCookie('wdh-affiliate-username');
        wdhCreateCookie('wdh-affiliate-username',$_GET['ref'],365);
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
    
    // Radio Add Option
    $jWDH('.wdh-get-value-radio-option').click(function(){
        var value = $jWDH(this).val(),
            dataRadio = $jWDH(this).attr('id').split('wdh-form-field-value-id-radio-')[1],
            radioFormID = dataRadio.split('-')[0],
            radioFieldID = dataRadio.split('-')[1],
            radioFieldTable = dataRadio.split('-')[2],
            radioFieldName = dataRadio.split('-')[3];
            $jWDH('#wdh-form-field-value-id-'+radioFormID+'-'+radioFieldID+'-'+radioFieldTable+'-'+radioFieldName).val(value);
    });
    
    // Checkbox Add Option
    $jWDH('.wdh-get-value-checkbox-option').click(function(){
        var value = $jWDH(this).val(),
            status = $jWDH(this).val(),
            valueOLD = '',
            valueNew = '',
            dataRadio = $jWDH(this).attr('id').split('wdh-form-field-value-id-checkbox-')[1],
            radioFormID = dataRadio.split('-')[0],
            radioFieldID = dataRadio.split('-')[1],
            radioFieldTable = dataRadio.split('-')[2],
            radioFieldName = dataRadio.split('-')[3];
            
            if (valueOLD !=""){
               valueNew =  valueOLD+','+value;
            } else {
               valueNew =  value;
            }
            
            var valueCheckbox = $jWDH(this).val(),
                valueCheckboxAll = $jWDH('#wdh-form-field-value-id-'+radioFormID+'-'+radioFieldID+'-'+radioFieldTable+'-'+radioFieldName).val(),
                isCheckedCheckbox = $jWDH(this).prop('checked'),
                valueNew = new Array();



             // CHECKED GENERATE VALUES
             if (isCheckedCheckbox && (typeof isCheckedCheckbox !== 'undefined')){

                 if (valueCheckboxAll.search(valueCheckbox) == -1 ){

                     if (valueCheckboxAll != ""){
                         valueNew.push(valueCheckboxAll);
                         valueNew.push(valueCheckbox);
                     } else {
                         valueNew.push(valueCheckbox);
                     }
                 } else {
                     valueNew.push(valueCheckboxAll);
                 }

                 $jWDH(this).attr('checked','checked');
             } 
             else { // UNCHECKED GENERATE VALUES
                     var valueRegen = valueCheckboxAll.split(",");
                     $jWDH.each(valueRegen,function(key){
                         if (valueCheckbox != valueRegen[key]){
                             valueNew.push(valueRegen[key]);
                         }
                     });
                     $jWDH(this).attr('checked','');
             }
            
            $jWDH('#wdh-form-field-value-id-'+radioFormID+'-'+radioFieldID+'-'+radioFieldTable+'-'+radioFieldName).val(valueNew.join(','));
    });
    
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
    
    // ADD POPUP EVENT
    $jWDH('.wdh-edfp-show-popup').click(function(){
        var formMOD = $jWDH(this).attr('id'),
            formID = formMOD.split('wdh-edfp-show-form-id-')[1];
            
        $jWDH('#wdh-edfp-popup-form-id-'+formID).css('display','block');
        $jWDH('#wdh-edfp-form-id-'+formID).css('position','absolute');
        
        var widthBox = $jWDH('#wdh-edfp-form-id-'+formID).width(),
            heightBox = $jWDH('#wdh-edfp-form-id-'+formID).height(),
            width = $jWDH(document).width()/2 - widthBox/2,
            height = $jWDH(document).height()/2 - heightBox/2;
            
        $jWDH('#wdh-edfp-form-id-'+formID).css('top',60);
        $jWDH('#wdh-edfp-popup-form-id-'+formID).css('height',$jWDH(document).height());
    });
    
    // CLOSE POPUP EVENT
    $jWDH('.wdh-close').click(function(){
        $jWDH('.wdh-edfp-popup').fadeOut(300);
    });
    
});

$jWDH.fn.extend({
    wdhGenerateForm: function(wdhUPLOAD_json,wdhDB_json,wdhERROR_json,wdhSettings_json,wdhINPUT_json){ // Form Generator
        var id = $jWDH(this)['selector'],
            formID = id.split('#wdh-edfp-form-id-')[1],
            submitID = $jWDH(this).wdhGetSubmitFieldID(formID),
            fields = $jWDH('.wdh-form-field-value-'+formID),
            errorHTML = '',
            field_type = 'text',
            failed_login = JSON.parse($jWDH.trim(wdhSettings_json))['form_msg_failed'],
            error_is_required = JSON.parse($jWDH.trim(wdhERROR_json))['is_required'],
            error_is_email = JSON.parse($jWDH.trim(wdhERROR_json))['is_email'],
            error_is_url = JSON.parse($jWDH.trim(wdhERROR_json))['is_url'],
            error_is_phone = JSON.parse($jWDH.trim(wdhERROR_json))['is_phone'],
            error_is_alpha = JSON.parse($jWDH.trim(wdhERROR_json))['is_alpha'],
            error_is_numeric = JSON.parse($jWDH.trim(wdhERROR_json))['is_numeric'],
            error_is_alphanumeric = JSON.parse($jWDH.trim(wdhERROR_json))['is_alphanumeric'],
            error_is_date = JSON.parse($jWDH.trim(wdhERROR_json))['is_date'],
            error_is_unique = JSON.parse($jWDH.trim(wdhERROR_json))['is_unique'],
            error_password = JSON.parse($jWDH.trim(wdhERROR_json))['password'],
            error_captcha = JSON.parse($jWDH.trim(wdhERROR_json))['captcha'],
            // JS HOOKS
            js_wdhedfp_onchange = JSON.parse($jWDH.trim(wdhINPUT_json))['js_wdhedfp_onchange'],
            js_wdhedfp_after_save = JSON.parse($jWDH.trim(wdhSettings_json))['js_wdhedfp_after_save'],
            // FORM
            form_mode   = JSON.parse($jWDH.trim(wdhSettings_json))['form_mode'];
    
        // Responsive Design
        var formWidth = parseInt(window.formWidth),
            containerWidth = parseInt($jWDH(id).parent().width()),
            containerBig = $jWDH(id).parent().parent(),
            popupForm = $jWDH('.wdh-edfp-popup form'),
            popupFormWidth = parseInt(popupForm.width()),
            windowWidth = parseInt($jWDH(window).width())-20,
            videoContainer = $jWDH('.wdh-video-player-responsive'),
            videoPlayerContainer = $jWDH('.wdh-video-player-responsive .wdh-video-player');
        
        if (formWidth > containerWidth){
            // Form Resize
            $jWDH(id).width(containerWidth);
        }

        if ($jWDH('.wdh-edfp-popup').length > 0) {
            // Align Popup Form
            var marginLeftPopup = ($jWDH(window).width())/2-20-$jWDH('.wdh-edfp-popup form').eq(0).width()/2;
                
                if (popupFormWidth < windowWidth){
                    $jWDH('.wdh-edfp-popup form').eq(0).css('left',marginLeftPopup);
                } else {
                    $jWDH('.wdh-edfp-popup form').eq(0).css('left','0px');
                }
            
            // Resize Popup Form
            if (popupFormWidth > windowWidth){
                // Form Resize
                $jWDH(id).width(windowWidth);
            } else {
                // Form Resize
                $jWDH(id).width(popupFormWidth);
            }
        }
        
        $jWDH(window).resize(function(){
            formWidth = parseInt(window.formWidth);
            containerWidth = parseInt($jWDH(id).parent().width()),
            popupFormWidth = parseInt(window.formWidth),
            windowWidth = parseInt($jWDH(window).width())-20;
            
            if (formWidth > containerWidth){
                // Form Resize
                $jWDH(id).width(containerWidth);
            } else {
                // Form Resize
                $jWDH(id).width(formWidth);
            }
            
            if ($jWDH('.wdh-edfp-popup').length > 0) {
               
                // Resize Popup Form
                if (popupFormWidth > windowWidth){
                    // Form Resize
                    $jWDH(id).width(windowWidth);
                } else {
                    // Form Resize
                    $jWDH(id).width(popupFormWidth);
                }

                var marginLeftPopup = $jWDH(window).width()/2-10-$jWDH('.wdh-edfp-popup form').eq(0).width()/2;
                
                    if (popupFormWidth < windowWidth){
                        $jWDH('.wdh-edfp-popup form').eq(0).css('left',marginLeftPopup);
                    } else {
                        $jWDH('.wdh-edfp-popup form').eq(0).css('left','0px');
                    }
            }
        });
        
        // ADD ON CHANGE EVENT
        // Parse Fields
        fields.each(function(key){
            var fieldIDhtml = $jWDH(fields[key]).attr('id');
            
            // Get Fields ID, Table
            if (!$jWDH(fields[key]).hasClass('wdh-submit-btn')) {
                
                // Get All except Confirm Password
                if (!$jWDH(fields[key]).hasClass('wdh-confirm-password')) {
                    
                    var fieldDATA = $jWDH(fields[key]).attr('id').split('wdh-form-field-value-id-'+formID+'-')[1],
                        fieldID = fieldDATA.split('-')[0],
                        fieldTable = fieldDATA.split('-')[1],
                        fieldName = fieldDATA.split('-')[2],
                        fieldValue = $jWDH(fields[key]).val(),
                        fieldLabel = $jWDH('#wdh-form-field-label-id-'+formID+'-'+fieldID+'-'+fieldTable+'-'+fieldName).html(),
                        id = $jWDH(this).attr('id'),
                        error = 0,
                        errorHTML = '';
                        
                        // ADD CHANGE EVENT
                        $jWDH('#'+id).on('keyup blur change paste',function(){
                            // SAFE TAGS
                            var value = wdhSafeTags($jWDH(this).val());
                            
                            $jWDH(this).val(value);
                            
                            
                            // ------------------- LIVE FILTERS
                            
                            // IS REQUIRED
                            if ($jWDH(this).hasClass('wdh-filter-is-required')) {

                                if(value.length < 1){
                                    errorHTML= error_is_required;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }

                            // IS EMAIL
                            if ($jWDH(this).hasClass('wdh-filter-is-email')) {

                                if($jWDH(document).wdhIsEmail(value) == false || value.length < 1){
                                    errorHTML= error_is_email;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }

                            //wdh-filter-is-unique

                            // IS Url
                            if ($jWDH(this).hasClass('wdh-filter-is-url')) {

                                value = value.replace('http://','');
                                value = value.replace('https://','');
                                if($jWDH(document).wdhIsUrl(value) == false || value.length < 1){
                                    errorHTML= error_is_url;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }

                            // IS Phone
                            if ($jWDH(this).hasClass('wdh-filter-is-phone')) {

                                if($jWDH(document).wdhIsPhone(value) == false || value.length < 1){
                                    errorHTML= error_is_phone;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }
                            
                            // Username
                            if ($jWDH(this).hasClass('wdh-filter-is-username')) {
                                field_type = 'username';
                            }
                            
                            // IS User Type
                            if ($jWDH(this).hasClass('wdh-filter-is-user-type')) {
                                field_type = 'user_type';
                            }
                            
                            // IS Alpha
                            if ($jWDH(this).hasClass('wdh-filter-is-alpha')) {
                                
                                if($jWDH(document).wdhIsAlpha(value) == false || value.length < 1){
                                    errorHTML= error_is_alpha;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }

                            // IS AlphaNumeric
                            if ($jWDH(this).hasClass('wdh-filter-is-alphanumeric')) {

                                if($jWDH(document).wdhIsAlphaNumeric(value) == false || value.length < 1){
                                    errorHTML= error_is_alphanumeric;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    field_type = 'alphanumeric';
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }

                            // IS Numeric
                            if ($jWDH(this).hasClass('wdh-filter-is-numeric')) {

                                field_type = 'numeric';

                                if($jWDH(document).wdhIsNumeric(value) == false || value.length < 1){
                                    errorHTML= error_is_numeric;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }

                                // IS Date
                            if ($jWDH(this).hasClass('wdh-filter-is-date')) {

                                if($jWDH(document).wdhIsDate(value) == false || value.length < 1){
                                    errorHTML= error_is_date;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';

                            }
                            
                            // ------------------- LIVE FILTERS
                            
                            
                                
                            // IS UNIQUE
                            if ($jWDH(this).hasClass('wdh-filter-is-unique')) {
                                
                                $jWDH(document).wdhIsUnique(value,formID,fieldID,fieldTable,fieldName,id,submitID,field_type,error_is_unique,form_mode);
                                
                            }
                            
                            window.valueNow = $jWDH(this).val();
                            setTimeout(js_wdhedfp_onchange, 0);
                        });
                        
                }
            }
        });
                
        
            
        if (submitID > 0){
            // Add Submit Event
            $jWDH('#wdh-form-field-value-id-'+formID+'-'+submitID).click(function(event){
                
              event.preventDefault();  
                
              var fieldSend = [],
                  fieldListSmall = [],
                  fieldList = [],
                  currentTable = '',
                  fieldsDB = {
                      
                  };  
                  
                // Get Field Values
                var i = 0,
                   errorHTML = '',
                   error = 0,
                   field_type = 'text',
                   is_required = false;
                   
                // Show Content
                $jWDH('#wdh-edfp-form-loader-id-'+formID).css('display','block');
                $jWDH('.wdh-field').css({'visibility':'hidden','display':'block'});
                   
                // Parse Fields
                fields.each(function(key){
                    var fieldIDhtml = $jWDH(fields[key]).attr('id'),
                        field_type = 'text';
                    
                    // Get Fields ID, Table
                    if (!$jWDH(fields[key]).hasClass('wdh-submit-btn')) {
                        // Get All except Confirm Password
                        if (!$jWDH(fields[key]).hasClass('wdh-confirm-password')) {
                            
                            var fieldDATA = $jWDH(fields[key]).attr('id').split('wdh-form-field-value-id-'+formID+'-')[1],
                                fieldID = fieldDATA.split('-')[0],
                                fieldTable = fieldDATA.split('-')[1],
                                fieldName = fieldDATA.split('-')[2],
                                fieldValue = $jWDH(fields[key]).val(),
                                fieldLabel = $jWDH('#wdh-form-field-label-id-'+formID+'-'+fieldID+'-'+fieldTable+'-'+fieldName).html();
                                
                                // IS REQUIRED
                                if ($jWDH(this).hasClass('wdh-filter-is-required')) {
                                    
                                    is_required = true;
                                    
                                    if(fieldValue.length < 1){
                                        errorHTML= error_is_required;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                } else {
                                    is_required = false;
                                }
                                
                                // IS EMAIL
                                if ($jWDH(this).hasClass('wdh-filter-is-email')) {
                                    
                                    field_type = 'email';
                                    
                                    if($jWDH(document).wdhIsEmail(fieldValue) == false || fieldValue.length < 1){
                                        errorHTML= error_is_email;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                }
                                
                                // Username
                                if ($jWDH(this).hasClass('wdh-filter-is-username')) {
                                    field_type = 'username';
                                }
                                
                                // IS User Type
                                if ($jWDH(this).hasClass('wdh-filter-is-user-type')) {

                                    field_type = 'user_type';
                                }
                                
                                // IS UNIQUE
                                if ($jWDH(this).hasClass('wdh-filter-is-unique')) {

                                    $jWDH(document).wdhIsUnique(fieldValue,formID,fieldID,fieldTable,fieldName,id,submitID,field_type,error_is_unique);

                                } 
                                
                                // IS Url
                                if ($jWDH(this).hasClass('wdh-filter-is-url')) {
                                    
                                    field_type = 'link';
                                    
                                    fieldValue = fieldValue.replace('http://','');
                                    fieldValue = fieldValue.replace('https://','');
                                    if($jWDH(document).wdhIsUrl(fieldValue) == false || fieldValue.length < 1){
                                        errorHTML= error_is_url;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                }
                                
                                // IS Phone
                                if ($jWDH(this).hasClass('wdh-filter-is-phone')) {
                                    
                                    field_type = 'phone';
                                    
                                    if($jWDH(document).wdhIsPhone(fieldValue) == false || fieldValue.length < 1){
                                        errorHTML= error_is_phone;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                }
                                
                                // IS Alpha
                                if ($jWDH(this).hasClass('wdh-filter-is-alpha')) {
                                    
                                    field_type = 'alpha';
                                        
                                    if($jWDH(document).wdhIsAlpha(fieldValue) == false || fieldValue.length < 1){
                                        errorHTML= error_is_alpha;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                }
                                
                                // IS AlphaNumeric
                                if ($jWDH(this).hasClass('wdh-filter-is-alphanumeric')) {
                                    
                                    if($jWDH(document).wdhIsAlphaNumeric(fieldValue) == false || fieldValue.length < 1){
                                        errorHTML= error_is_alphanumeric;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                        field_type = 'alphanumeric';
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                }
                                
                                // IS Numeric
                                if ($jWDH(this).hasClass('wdh-filter-is-numeric')) {
                                    
                                    field_type = 'numeric';
                                        
                                    if($jWDH(document).wdhIsNumeric(fieldValue) == false || fieldValue.length < 1){
                                        errorHTML= error_is_numeric;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                    
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                }
                                    
                                // IS Date
                                if ($jWDH(this).hasClass('wdh-filter-is-date')) {
                                    
                                    field_type = 'date';
                                        
                                    if($jWDH(document).wdhIsDate(fieldValue) == false || fieldValue.length < 1){
                                        errorHTML= error_is_date;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                               
                                }
                                
                                // IS PASSWORD
                                if ($jWDH(this).hasClass('wdh-filter-is-password')){
                                   field_type = 'password';
                                }
                                
                                // IS CAPTCHA
                                if ($jWDH(this).hasClass('wdh-filter-is-captcha')) {
                                    field_type = 'captcha';
                                    
                                    if (parseInt($jWDH('#wdh-form-field-value-id-'+formID+'-'+fieldID+'-'+fieldTable+'-'+fieldName).val()) != parseInt($jWDH('#wdh-form-field-recaptcha-id-'+formID+'-'+fieldID+'-'+fieldTable+'-'+fieldName).val())) {
                                        errorHTML = error_captcha;
                                        error++;
                                    }
                                    
                                    if (errorHTML != ""){ 
                                        var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                        $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                        var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                        $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                    } else {
                                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                    }
                                     
                                    // RESET ERROR
                                    errorHTML = '';
                                }
                                
                                if (currentTable != fieldTable) {
                                    currentTable = fieldTable;
                                }
                                
                                if ($jWDH(this).hasClass('wdh-filter-is-captcha')) {
                                    error_captcha
                                }
                                if (typeof fieldListSmall[currentTable] == "undefined") {
                                    fieldListSmall[currentTable] = [];
                                    fieldsDB[currentTable] = {};
                                }
                                
                                var wdhPostID = $jWDH('#wdh-form-post-id-'+formID).val();
                                
                                if (field_type != 'captcha') {
                                    // Send Data
                                    fieldLabel = fieldLabel.replace('<span class="star">*</span>','');
                                    fieldSend = {
                                        "form_id": formID,
                                        "post_id": wdhPostID,
                                        "field_id": fieldID,
                                        "field_name": fieldName,
                                        "field_label": fieldLabel,
                                        "field_value": fieldValue,
                                        "field_table": fieldTable,
                                        "field_type": field_type,
                                        "field_required": is_required
                                    }

                                    fieldListSmall[currentTable].push(fieldSend);
                                    fieldsDB[currentTable][i] = fieldSend;
                                    i++;
                                }
                                
                        } else { // Get Confirm Password
                            var fieldDATA = $jWDH(fields[key]).attr('id').split('wdh-form-field-value-id-second-'+formID+'-')[1],
                                fieldID = fieldDATA.split('-')[0],
                                fieldTable = fieldDATA.split('-')[1],
                                fieldName = fieldDATA.split('-')[2],
                                fieldValue = $jWDH(fields[key]).val();
                                
                                // Validating Password
                                
                                if ($jWDH('#wdh-form-field-value-id-'+formID+'-'+fieldID+'-'+fieldTable+'-'+fieldName).val() != fieldValue || fieldValue.length < 1){
                                    errorHTML= error_password;
                                    error++;
                                }

                                if (errorHTML != ""){ 
                                    var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField);
                                    $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                    var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());
                                        
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                    $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                                } else {
                                    $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                }

                                // RESET ERROR
                                errorHTML = '';
                                
                        }
                    }
                });
                
                
                // PUT FIELDS in FIELDS LIST
                fieldList.push(fieldListSmall);
                
                // ADD HIDDEN VALUES 
                var jsonFields = JSON.stringify(fieldsDB);
                $jWDH('#wdh-form-fields-id-'+formID).val(jsonFields);
                $jWDH('#wdh-form-settings-id-'+formID).val(wdhSettings_json);
                
                // Validation
                if (error < 1) {
                    
                    // Pay Now
                    if ((payment_active == true || payment_active == 'true') && parseFloat(payment_price) > 0){
                        $jWDH('#wdh-edfp-form-id-'+formID).off('submit').submit();
                        return true;
                    }
                    // Sending Data
                    $jWDH.post(request_url,
                                  {action:"wdh_insert_fields_db",
                                   "form_id": formID,
                                   "fields":fieldsDB,
                                   "settings": wdhSettings_json
                                  }, function(data){
                        window.valueNow = fieldsDB;
                        if (data == 'success') {
                            setTimeout(js_wdhedfp_after_save, 0);
                            $jWDH('#wdh-edfp-form-content-id-'+formID).fadeOut(300);
                            $jWDH('#wdh-edfp-form-success-id-'+formID).fadeIn(500);
                            
                            // Show Content
                            $jWDH('#wdh-edfp-form-loader-id-'+formID).css('display','none');
                        } else if(data == 'no_login') {
                            alert(failed_login);
                            $jWDH('#wdh-edfp-form-loader-id-'+formID).css('display','none');
                            // Show Content
                            $jWDH('#wdh-edfp-form-loader-id-'+formID).css('display','none');
                            $jWDH('.wdh-field').css({'visibility':'visible','display':'block'});
                        }
                    });
                } else {
                    // Show Content
                    $jWDH('#wdh-edfp-form-loader-id-'+formID).css('display','none');
                    $jWDH('.wdh-field').css({'visibility':'visible','display':'block'});
                }
                
            });
        }
    },
    wdhGetSubmitFieldID: function(formID){ // Get Submit ID
        var fields = $jWDH('.wdh-form-field-value-'+formID),
            submitID = 0;

        // Parse Fields
        fields.each(function(key){
            // Get Submit ID
            if ($jWDH(fields[key]).hasClass('wdh-submit-btn')) {
                submitID = $jWDH(fields[key]).attr('id').split('wdh-form-field-value-id-'+formID+'-')[1];
            }
            
        });
        
        return submitID;
    },
    wdhIsUnique: function (value,formID,fieldID,table,name,id,submitID,field_type,error_is_unique,form_mode){
        
        if (value.length > 0) {
            var errorHTML = '';
            
            // Disabled Submit
            $jWDH('#wdh-form-field-value-id-'+formID+'-'+submitID).attr('disabled','disabled');
            $jWDH('#wdh-form-field-value-id-'+formID+'-'+submitID).addClass('disabled');
            
            delay(function(){
            $jWDH.post(request_url,
                {value:value,
                 name:name,
                 table:table,
                 field_type: field_type,
                 form_mode: form_mode,
                 action:'wdh_check_if_exist'
                }, function(data){

                    if (data == 'ok') {
                        // Enable Submit
                        $jWDH('#wdh-form-field-value-id-'+formID+'-'+submitID).removeAttr('disabled');
                        $jWDH('#wdh-form-field-value-id-'+formID+'-'+submitID).removeClass('disabled');
                        
                        // Remove Error
                        $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                        
                    } else {
                        
                            errorHTML= error_is_unique;
                            if (errorHTML != ""){ 
                                var widthField = parseInt($jWDH('#wdh-form-field-id-'+fieldID).width());
                                $jWDH('#wdh-form-field-error-'+fieldID).css('left',widthField); 
                                $jWDH('#wdh-form-field-error-'+fieldID).html(errorHTML);
                                var widthBox = $jWDH(document).wdhTextWidth($jWDH('#wdh-form-field-error-'+fieldID).html());

                                $jWDH('#wdh-form-field-error-'+fieldID).css('width',widthBox);
                                $jWDH('#wdh-form-field-error-'+fieldID).fadeIn();
                            } else {
                                $jWDH('#wdh-form-field-error-'+fieldID).css('display','none');
                                // Enabled Submit
                                $jWDH('#wdh-form-field-value-id-'+formID+'-'+submitID).attr('disabled','disabled');

                            }

                            // RESET ERROR
                            errorHTML = '';
                    }
            });
            }, 500);
        }
    }
});

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
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function wdhEraseCookie(name) {
    wdhCreateCookie(name,"",-1);
}