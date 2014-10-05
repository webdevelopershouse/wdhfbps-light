/*
Project Name: WDH - Edit Database Field LIGHT (Wordpress Library)
Project Version: 1.0
Project URL: http://www.wdh.im/projects/edit-database-field-light-wordpress-library/
Author: WDH - Web Developers House
Author URL: http://www.wdh.im/
File Path: js/jquery.wdh.im.edfp.js
File Description: WDH - Edit Database Field LIGHT Scripts 
File Version: 1.0
Last Update File : 03.10.2014
*/
      if (typeof ajaxurl === 'undefined'){
          var ajaxurl = window.ajaxurl;
      }
var $jWDH = jQuery.noConflict(),
    request_url = ajaxurl;
    
$jWDH(document).ready(function(){
    // Add Tooltip
    $jWDH('.wdh-tooltip').unbind('hover');
    $jWDH('.wdh-tooltip').bind('hover',function(){
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
});    
    
$jWDH.fn.extend({
    // ALL
    wdhEditDbField:function (wdhDB_json,wdhFIELD_json,wdhINPUT_json,wdhTOOLTIP_json,wdhFILTER_json,wdhERROR_json,wdhUPLOAD_json,valueNow,idField){
        var id = $jWDH(this)['selector'],
            currHtml = $jWDH(id).html(),
            dbTable = JSON.parse($jWDH.trim(wdhDB_json))['table'],
            dbfieldName = JSON.parse($jWDH.trim(wdhFIELD_json))['field_name'],
            conditions = JSON.parse($jWDH.trim(wdhFIELD_json))['conditions'],
            editis = JSON.parse($jWDH.trim(wdhFIELD_json))['edit'],
            tokenis = JSON.parse($jWDH.trim(wdhFIELD_json))['token'],
            inputType = JSON.parse($jWDH.trim(wdhINPUT_json))['type'],
            tooltipType = JSON.parse($jWDH.trim(wdhTOOLTIP_json))['position'],
            tooltipTitle = JSON.parse($jWDH.trim(wdhTOOLTIP_json))['text'],
            submitText = JSON.parse($jWDH.trim(wdhINPUT_json))['save_button'],
            valuesList = JSON.parse($jWDH.trim(wdhINPUT_json))['values'],
            slider_min = JSON.parse($jWDH.trim(wdhINPUT_json))['slider_min'],
            slider_max = JSON.parse($jWDH.trim(wdhINPUT_json))['slider_max'],
            slider_range = JSON.parse($jWDH.trim(wdhINPUT_json))['slider_range'],
            filter_is_required = JSON.parse($jWDH.trim(wdhFILTER_json))['is_required'],
            filter_is_email = JSON.parse($jWDH.trim(wdhFILTER_json))['is_email'],
            filter_is_url = JSON.parse($jWDH.trim(wdhFILTER_json))['is_url'],
            filter_is_phone = JSON.parse($jWDH.trim(wdhFILTER_json))['is_phone'],
            filter_is_alpha = JSON.parse($jWDH.trim(wdhFILTER_json))['is_alpha'],
            filter_is_numeric = JSON.parse($jWDH.trim(wdhFILTER_json))['is_numeric'],
            filter_is_alphanumeric = JSON.parse($jWDH.trim(wdhFILTER_json))['is_alphanumeric'],
            filter_is_date = JSON.parse($jWDH.trim(wdhFILTER_json))['is_date'],
            filter_is_unique = JSON.parse($jWDH.trim(wdhFILTER_json))['is_unique'],
            error_is_required = JSON.parse($jWDH.trim(wdhERROR_json))['is_required'],
            error_is_email = JSON.parse($jWDH.trim(wdhERROR_json))['is_email'],
            error_is_url = JSON.parse($jWDH.trim(wdhERROR_json))['is_url'],
            error_is_phone = JSON.parse($jWDH.trim(wdhERROR_json))['is_phone'],
            error_is_alpha = JSON.parse($jWDH.trim(wdhERROR_json))['is_alpha'],
            error_is_numeric = JSON.parse($jWDH.trim(wdhERROR_json))['is_numeric'],
            error_is_alphanumeric = JSON.parse($jWDH.trim(wdhERROR_json))['is_alphanumeric'],
            error_is_date = JSON.parse($jWDH.trim(wdhERROR_json))['is_date'],
            error_is_unique = JSON.parse($jWDH.trim(wdhERROR_json))['is_unique'],
            js_wdhedfp_onchange = JSON.parse($jWDH.trim(wdhINPUT_json))['js_wdhedfp_onchange'],
            js_wdhedfp_after_save = JSON.parse($jWDH.trim(wdhINPUT_json))['js_wdhedfp_after_save'],
            errorTEXT = '',
            errorHTML = '',
            wdbfieldvalue = idField,
            startFormHTML = '<form>',
            inputHTML = '<input type="text" class="wdh-input" value="'+valueNow+'">',
            textareaHTML = '<textarea class="wdh-textarea">'+valueNow+'</textarea>',
            fieldHTML = '',
            valueSelect = '',
            submitHTML = '<input type="button" class="wdh-submit" value="'+submitText+'">',
            endFormHTML = '</form>',
            loaderHTML = '<div class="wdh-loader">&nbsp;</div>',
            conditionAll = '',
            condition = new Array(),
            wfieldvalue = '',
            valueRadio = valueNow,
            valueCheckboxNew =  valueNow,
            oldValue = valueNow,
            confirmValueNow = '',
            i = 0;

            // Get Unique ID by conditions
            $jWDH.each(conditions,function(key){
                
                if (i < 1){
                    wfieldvalue += conditions[key]['field_label']+'-'+conditions[key]['field_value'];
                } else {
                    wfieldvalue += '-'+conditions[key]['field_label']+'-'+conditions[key]['field_value'];
                }
                i++;
            });
        
        // Input type
        switch (inputType){
            case "text":
                fieldHTML = inputHTML;
                break;
            case "textarea":
                fieldHTML = textareaHTML;
                break;
            case "select":
                valueSelect = valuesList.split("|");
                fieldHTML  = '<select class="wdh-select">'; // $('input[name="genderS"]:checked').val();
                             var valueNext = $jWDH(id+' .wdh-select').val(),
                                 selecLabel = '',
                                 selecValue = '';
                             $jWDH.each(valueSelect,function(key){
                                 
                                 if (valueSelect[key].indexOf("@@") != -1) {
                                    selecLabel = valueSelect[key].split('@@')[0];
                                    selecValue = valueSelect[key].split('@@')[1];
                                 } else {
                                    selecLabel = valueSelect[key];
                                    selecValue = valueSelect[key];
                                 }
                                 
                                 if (valueNow == selecValue){
                                    fieldHTML += '<option value="'+selecValue+'" selected>'+selecLabel+'</option>';
                                 } else {
                                    fieldHTML += '<option value="'+selecValue+'">'+selecLabel+'</option>';
                                 }
                             });
                fieldHTML += '</select>';
                break;
            case "slider":
                
                if (valueNow.indexOf("..") != -1) {
                    valueNow = 0;
                }
                fieldHTML = '<div id="wdh-slider-'+wfieldvalue+'" class="wdh-slider">&nbsp;</div><input type="text" id="wdh-slider-value-'+wfieldvalue+'" class="wdh-slider-value" value="'+valueNow+'" disabled>';
                break;
        }
        
        if (editis == true) {
        // Adding ToolTip
        if (inputType != 'html_editor'){
            if (typeof $jWDH.ui !== "undefined") {
                //$jWDH(id).tooltip();
            }
        }
        // Adding Edit event
            $jWDH(id).click(function(){ 
            // Hide Tooltip
            $jWDH('.ui-tooltip').css('display','none');
            // Adding Input
            $jWDH(id).html(startFormHTML+fieldHTML+submitHTML+endFormHTML);
            
            // JAVASCRIPT HOOK - ON CHANGE TEXT, TEXTAREA, RADIO, SELECT, MAP, VIDEO, DATE, SLIDER
            if (inputType == "text"){
                $jWDH(id+' .wdh-input').keyup(function(){
                    valueNow = $jWDH(id+' .wdh-input').val();
                    // Values For Hook
                    window.valueNow = valueNow;
                    // Adding Hook
                    setTimeout(js_wdhedfp_onchange, 0);
                });
            }
            
            if (inputType == "textarea"){
                $jWDH(id+' .wdh-textarea').keyup(function(){
                    valueNow = $jWDH(id+' .wdh-textarea').val();
                    // Values For Hook
                    window.valueNow = valueNow;
                    // Adding Hook
                    setTimeout(js_wdhedfp_onchange, 0);
                });
            }
            
            if (inputType == "select"){
                $jWDH(id+' .wdh-select').change(function(){
                    valueNow = $jWDH(id+' .wdh-select').val();
                    // Values For Hook
                    window.valueNow = valueNow;
                    // Adding Hook
                    setTimeout(js_wdhedfp_onchange, 0);
                });
            }
            
            // Remove Hover
            $jWDH(id).unbind('mouseenter mouseleave');
            // Remove click 
            $jWDH(id).unbind('click');
            
            
            // Adding slider 
            if (inputType == 'slider'){
                //$jWDH('#wdh-slider-'+wfieldvalue).append('<div class="wdh-slider-selected">&nbsp;</div>');
                $jWDH('#wdh-slider-'+wfieldvalue).slider({min: slider_min, max: slider_max, value: valueNow, step: slider_range,slide:function(event,ui){
                        $jWDH('#wdh-slider-value-'+wfieldvalue).val(ui.value);
                        // Values For Hook
                        window.valueNow = ui.value;
                        // Adding Hook
                        setTimeout(js_wdhedfp_onchange, 0);
                }});
            }
            
            // Adding save event
            $jWDH(id+' .wdh-submit').click(function(event){
               
                var submit = $jWDH(id+' .wdh-submit'),
                    submitHTMLis = submit.html();
                // New value
                switch (inputType){
                    case "text":
                        valueNow = $jWDH(id+' .wdh-input').val();
                        break;
                    case "textarea":
                        valueNow = $jWDH(id+' .wdh-textarea').val();
                        break;
                    case "select":
                        valueNow = $jWDH(id+' .wdh-select').val();
                        break;
                    case "slider":
                        valueNow = $jWDH('#wdh-slider-value-'+wfieldvalue).val();
                        break;
                }
                
                if (inputType != 'html_editor'){
                    valueNow = wdhSafeTags(valueNow);
                }
                errorTEXT = '';
                                        
                // Filters 

                // Is Required 
                if (filter_is_required == true || filter_is_required == 'true'){

                    if(valueNow.length < 1 || valueNow =='...............'){
                        errorTEXT= error_is_required;
                    }
                }

                if (inputType == "text" || inputType == "textarea"){
                    // Is email
                    if (filter_is_email == true || filter_is_email == 'true'){

                        if($jWDH(document).wdhIsEmail(valueNow) == false){
                            errorTEXT= error_is_email;
                        }
                    }

                    // Is url
                    if (filter_is_url == true || filter_is_url == 'true'){
                        valueNow = valueNow.replace('http://','');
                        valueNow = valueNow.replace('https://','');
                        if($jWDH(document).wdhIsUrl(valueNow) == false){
                            errorTEXT= error_is_url;
                        }
                    }

                    // Is Phone
                    if (filter_is_phone == true || filter_is_phone == 'true'){
                        if($jWDH(document).wdhIsPhone(valueNow) == false){
                            errorTEXT= error_is_phone;
                        }
                    }

                    // Is Alpha
                    if (filter_is_alpha == true || filter_is_alpha == 'true'){
                        if($jWDH(document).wdhIsAlpha(valueNow) == false){
                            errorTEXT= error_is_alpha;
                        }
                    }

                    // Is AlphaNumeric
                    if (filter_is_alphanumeric == true || filter_is_alphanumeric == 'true'){
                        if($jWDH(document).wdhIsAlphaNumeric(valueNow) == false){
                            errorTEXT= error_is_alphanumeric;
                        }
                    }
                }
                
                if (inputType == "text" || inputType == "textarea" || inputType == "slider"){
                    
                    // Is Numeric
                    if (filter_is_numeric == true || filter_is_numeric == 'true'){
                        if($jWDH(document).wdhIsNumeric(valueNow) == false){
                            errorTEXT= error_is_numeric;
                        }
                    }
                }
                
                
                // Is Unique
                if (oldValue == valueNow){
                    filter_is_unique = false;
                }
                
                // Adding error message
                if (errorTEXT != ""){
                    var errorWidth = $jWDH(document).wdhTextWidth(errorTEXT)+40,
                        errorHeight = $jWDH(document).wdhTextHeight(errorTEXT)+5,
                        errorRight = -errorWidth-18;
                        $jWDH(id+' form').css('position','relative'); 
                        errorHTML = '<div class="error-arrow">&nbsp;</div><div class="error-box">'+errorTEXT+'</div>';
                        // Adding HTML Error
                        submit.after(errorHTML);
                        // Adding dimension box
                        $jWDH(id+' .error-box').css('width',errorWidth);
                        $jWDH(id+' .error-box').css('height',errorHeight);
                        $jWDH(id+' .error-box').css('right',errorRight);
                        // Display error box
                        $jWDH(id+' .error-arrow').css('display','block');
                        $jWDH(id+' .error-box').css('display','block');
                    
                }
                
                if (errorTEXT == ""){
                    // Adding loader
                    $jWDH(id).html(loaderHTML);
                    // Saving data
                    $jWDH.post(ajaxurl,
                              {
                               action: 'wdh_edit_field_db',
                               wdhDB_json:wdhDB_json,
                               wdhFIELD_json:wdhFIELD_json,
                               value:valueNow,
                               type:inputType,
                               is_unique:filter_is_unique
                              }, function(data){
                                  data = $jWDH.trim(data);
                                  
                            if (data != 'wrong' && data != 'field_exist') {
                                if (data == 'success' || inputType == 'map' || inputType == 'video'){
                                // Removing HTML Error
                                $jWDH(id+' .error-arrow').remove();
                                $jWDH(id+' .error-box').remove();
                                
                                // Adding Value
                                $jWDH(id).html(valueNow);
                                
                                // Values For Hook
                                window.valueNow = valueNow;
                                // Adding Hook
                                
                                setTimeout(js_wdhedfp_after_save, 0);
                                $jWDH(id).attr('title',tooltipTitle);
                                $jWDH(id).wdhEditDbField(wdhDB_json,wdhFIELD_json,wdhINPUT_json,wdhTOOLTIP_json,wdhFILTER_json,wdhERROR_json,wdhUPLOAD_json,valueNow,idField);
                            }
                            } else if (data == 'field_exist'){
                                alert(error_is_unique);
                                location.reload();
                            }
                        });
                }
            });
            
        });
        }
        
        
    },
    // SWITCH
    wdhEditDbFieldSwitch:function (wdhDB_json,wdhFIELD_json,wdhINPUT_json,wdhTOOLTIP_json,wdhFILTER_json,wdhERROR_json,wdhUPLOAD_json,valueNow,idField){
        var id = $jWDH(this)['selector'],
            currHtml = $jWDH(id).html(),
            dbTable = JSON.parse($jWDH.trim(wdhDB_json))['table'],
            dbfieldName = JSON.parse($jWDH.trim(wdhFIELD_json))['field_name'],
            conditions = JSON.parse($jWDH.trim(wdhFIELD_json))['conditions'],
            inputType = JSON.parse($jWDH.trim(wdhINPUT_json))['type'],
            tooltipType = JSON.parse($jWDH.trim(wdhTOOLTIP_json))['position'],
            tooltipTitle = JSON.parse($jWDH.trim(wdhTOOLTIP_json))['text'],
            js_wdhedfp_onchange = JSON.parse($jWDH.trim(wdhINPUT_json))['js_wdhedfp_onchange'],
            js_wdhedfp_after_save = JSON.parse($jWDH.trim(wdhINPUT_json))['js_wdhedfp_after_save'],
            wdbfieldvalue = idField,
            conditionAll = '',
            condition = new Array(),
            wfieldvalue = '',
            valueRadio = valueNow,
            valueCheckboxNew = valueNow,
            i = 0;
    
            // Adding ToolTip
            if (typeof $jWDH.ui !== "undefined") {
                //$jWDH(id).tooltip();
                //$jWDH('.onoffswitch').tooltip();
            }
            
            // Adding save event for Switch
            $jWDH(id).click(function(event){
            // Get Checked value
            var isChecked = $jWDH(id).prop('checked');
                
            if (isChecked && (typeof isChecked !== 'undefined')){
                valueNow = 'true';
                $jWDH(id).attr('checked','');
            } else {
                valueNow = 'false';
            }
            
            window.valueNow = valueNow;
            // Adding Hook
            setTimeout(js_wdhedfp_onchange, 0);
            
                // Disabled until save value
                $jWDH(id).attr('disabled','disabled');
                // Saving data
                $jWDH.post(ajaxurl,
                          {
                           action: 'wdh_edit_field_db',
                           wdhDB_json:wdhDB_json,
                           wdhFIELD_json:wdhFIELD_json,
                           value:valueNow,
                           type:inputType,
                           is_unique:false
                          }, function(data){
                        data = $jWDH.trim(data);
                        if (data == 'success'){
                            // Enable again
                            $jWDH(id).attr('disabled',false);
                            // Values For Hook
                            window.valueNow = valueNow;
                            // Adding Hook
                            setTimeout(js_wdhedfp_after_save, 0);
                        }
                    });
            });
            
            
    },
    // COLORPICKER
    wdhEditDbFieldColorPicker:function (wdhDB_json,wdhFIELD_json,wdhINPUT_json,wdhTOOLTIP_json,wdhFILTER_json,wdhERROR_json,wdhUPLOAD_json,valueNow,idField){
        var id = $jWDH(this)['selector'],
            currHtml = $jWDH(id).html(),
            dbTable = JSON.parse($jWDH.trim(wdhDB_json))['table'],
            dbfieldName = JSON.parse($jWDH.trim(wdhFIELD_json))['field_name'],
            conditions = JSON.parse($jWDH.trim(wdhFIELD_json))['conditions'],
            inputType = JSON.parse($jWDH.trim(wdhINPUT_json))['type'],
            tooltipType = JSON.parse($jWDH.trim(wdhTOOLTIP_json))['position'],
            tooltipTitle = JSON.parse($jWDH.trim(wdhTOOLTIP_json))['text'],
            js_wdhedfp_onchange = JSON.parse($jWDH.trim(wdhINPUT_json))['js_wdhedfp_onchange'],
            js_wdhedfp_after_save = JSON.parse($jWDH.trim(wdhINPUT_json))['js_wdhedfp_after_save'],
            wdbfieldvalue = idField,
            conditionAll = '',
            condition = new Array(),
            wfieldvalue = '',
            valueRadio = valueNow,
            i = 0;
            
            // Adding ToolTip
            if (typeof $jWDH.ui !== "undefined") {
                //$jWDH(id).tooltip();
            } else {
                //$jWDH(id).wdhTooltip(tooltipType);
            }
            
            // Adding colorpicker
            if (inputType == 'colorpicker'){
               
                $jWDH(id).ColorPicker({color:valueNow,
                    onChange: function(hsb, hex, rgb){
                        $jWDH(id).css('background','#'+hex);
                        $jWDH(id).val(hex);
                        $jWDH(id).ColorPickerSetColor(hex);
                        // Values For Hook
                        window.valueNow = hex;
                        // Adding Hook
                        setTimeout(js_wdhedfp_onchange, 0);
                    },
                    onSubmit:function(hsb, hex, rgb){
                        // Hide colorpicker
                        $jWDH('.colorpicker').css('display','none');
                        // Saving data ( in background )
                        $jWDH.post(ajaxurl,
                                  {
                                   action: 'wdh_edit_field_db',
                                   wdhDB_json:wdhDB_json,
                                   wdhFIELD_json:wdhFIELD_json,
                                   value:hex,
                                   type:inputType,
                                   is_unique:false
                                  }, function(data){

                                data = $jWDH.trim(data);                        
                                if (data == 'success'){
                                    // Values For Hook
                                    window.valueNow = hex;
                                    // Adding Hook
                                    setTimeout(js_wdhedfp_after_save, 0);
                                }
                        });
                    }
                });
                $jWDH('.colorpicker').css('z-index','10');
            }
            
    },
    //=== Filters 

    // Is Email 
    wdhIsEmail: function(val){
        
        if(!val.match(/\S+@\S+\.\S+/)){ // Jaymon's / Squirtle's solution
          // do something
          return false;
        }
        
        if( val.indexOf(' ')!=-1 || val.indexOf('..')!=-1){
          // do something
          return false;
        }
        return true;
    },
    
    // Is Url
    wdhIsUrl: function(str) {
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        
        if(!pattern.test(str)) {
          return false;
        } else {
          return true;
        }
    },
            
    // Is Phone Number
    wdhIsPhone: function(phoneNumber){
        var phoneno = /^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;
        
        if (phoneNumber.match(phoneno)) {  
            return true;  
        } else {  
            return false;  
        } 
    },
            
    // Is Alpha
    wdhIsAlpha: function(x) {
    var alphaOnly=true;
        if (x!='') {
            for (c=0; c<x.length; c++) {
            if (x.substr(c,1).match(/[^a-zA-Z]/) != null) {
                alphaOnly=false;
                break;
                }
            }
        }
        return alphaOnly;
    },
            
    // Is Numeric
    wdhIsNumeric: function(input){
        var RE = /^-{0,1}\d*\.{0,1}\d+$/;
        return (RE.test(input));
    },
    
    // Is AlphaNumeric
    wdhIsAlphaNumeric: function validateCode(TCode){
        if( /[^a-zA-Z0-9]/.test( TCode ) ) {
           return false;
        }
        return true;     
    },
            
    // Object to String
    wdhObjtoStr: function(string) {
        var params = { string_value:string };
        return $jWDH.param( params, true );
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

function wdhescapeRegExp(str) {
  return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

function wdhReplace(find, replace, str){
    return str.replace(new RegExp(wdhescapeRegExp(find), 'g'), replace);
}

function wdhEncodeURIComponent (str) {
  return wdhEncodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
}

function wdhSafeTags(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') ;
}