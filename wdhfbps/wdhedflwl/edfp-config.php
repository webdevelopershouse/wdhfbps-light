<?php
//Project Name: WDH - Edit Database Field LIGHT (Wordpress Library)
//Project Version: 1.0
//Project URL: http://www.wdh.im/projects/edit-database-field-light-wordpress-library/
//Author: WDH - Web Developers House
//Author URL: http://www.wdh.im/
//File: edfp-config.php
//File Description: Configuration File
//File Version: 1.0
//Last Update File : 28.09.2014


    global $wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $wdhForm, $wdhSettings;
    
    // Website EDFP URL
    $wdhFIELD['wdhedfp_url'] = WP_PLUGIN_URL.'/wdhfbps/wdhedflwl/'; // set WP_THEME_URL instead of WP_PLUGIN_URL if you use in Wordpress THEME
    
    // Language
    $wdhFIELD['language']    = 'en';  // set current language : af, al, ar, az, bs, by, bg, ca, cn, cr, cz, dk, du, en, eo, et, fl, fi, fr, gl, de, gr, ha, he, hi, hu, is, id, ir, it, ja, ko, lv, lt, mk, mg, ma, no, pe, pl, pt, ro, ru, sr, sk, sl, sp, sw, se, th, tr, uk, ur, vi, we, yi
    // CSS Template
    $wdhFIELD['template']    = 'black_yellow';  // for templates/standard/standard.php

    // Default Mysql Connection
    $wdhDB['table'] = 'your mysql table';
    // Encrypt Database Login Details
    $wdhDB['key']      = '#W3bD3v3l@p3rsH@us3#'; // You can change it but never leave it empty 
    
    // Field name that you to display
    $wdhFIELD['field_name'] = 'your field name';
    $wdhFIELD['json_value'] = ''; // set empty if you not use json
    $wdhFIELD['value']      = ''; // set empty if you not use default value 
    $wdhFIELD['edit']       = true; // set true if you to be editable
    $wdhFIELD['auto_add']   = true; // Set true if you want to auto add field if not found
    $wdhFIELD['class']      = ''; // add CSS class to displayed field
    // Find field where condition: first field name = first field value AND second field name = second field value
    $wdhFIELD['conditions'] = array(
        0 => array(
            'field_label' => 'first field name',
            'field_value' => 'first field value',
            'field_condition' => '' // Allways must be EMPTY
        ),
        1 => array(
            'field_label' => 'second field name',
            'field_value' => 'second field value',
            'field_condition' => 'AND' // condition between
        )
    );

    // INPUT
    $wdhINPUT['type']         = 'text'; // text , textarea, select , slider, switch on/off button, colorpicker;
    $wdhINPUT['values']       = 'Value1|Value2|Value3';
    $wdhINPUT['slider_min']   = 300; // set slider min
    $wdhINPUT['slider_max']   = 800; // set slider max
    $wdhINPUT['slider_range'] = 10; // set slider step

    // TOOLTIP
    $wdhTOOLTIP['position'] = 'right';

    // FILTER
    $wdhFILTER['is_required']     = true;
    $wdhFILTER['is_email']        = false;
    $wdhFILTER['is_url']          = false;
    $wdhFILTER['is_phone']        = false;
    $wdhFILTER['is_alpha']        = false;
    $wdhFILTER['is_numeric']      = false;
    $wdhFILTER['is_alphanumeric'] = false;
    $wdhFILTER['is_date']         = false;
    $wdhFILTER['is_unique']       = false;
    
    // Include Language File
    include_once 'languages/'.$wdhFIELD['language'].'.php';

    // ==== DO NOT REMOVE ==== // 
    // JAVASCRIPT HOOKS ON CHANGE
    $wdhINPUT['js_wdhedfp_onchange']   = ''; // Return value is window.valueNow - Write every javascript code you want with escape ( use instead of " this \" and $jWDH instead of $ or jQuery).
    // Example : console.log(\"Value: \"+window.valueNow);
    // JAVASCRIPT HOOKS AFTER SAVE
    $wdhINPUT['js_wdhedfp_after_save'] = 'console.log(\"Value: \"+window.valueNow);'; // Return value is window.valueNow - Write every javascript code you want with escape ( use instead of " this \" and $jWDH instead of $ or jQuery).
    // Example : console.log(\"Value: \"+window.valueNow);

    
    
// Plugins 
// -----------------------------------
// FORM GENERATOR 
// -----------------------------------
// Attention : set true only if you have Form Generator and Payment plugin for EDFP 
$wdhFIELD['WDH_FORM_GENERATOR']     = true;        // set true if you want to enable formgenerator 


// -----------------------------------
// GLOBAL WPBP 
// -----------------------------------
$wdhSettings['wpdb']                = '';        // set your form width 

// Form Settings
//-----------------------------------

$wdhSettings['form_id']             = 1;            // set form id (number from 1 to 99999...)
$wdhSettings['form_type']           = 'normal';     // normal or popup
$wdhSettings['form_mode']           = 'normal';     // normal: save in database + send emails , contact: only send emails, login: only login ( save login data in cookie );
$wdhSettings['form_design']         = 'fixed';      // fixed or responsive design
$wdhSettings['form_popup_button']   = 'Show Form';  // text button for display popup
$wdhSettings['form_class']          = '';           // add form css class
$wdhSettings['form_css']            = array();      // set css propeties example array('color' => '#000');
$wdhSettings['form_width']          = '100';        // set your form width 

// Sent Message
//-----------------------------------

$wdhSettings['form_msg_sent']   = 'Congratualations your data has been sent.';  // Set succesfully sent form message 
$wdhSettings['form_msg_failed'] = 'Message was not sent.';                      // Set failed sent form message 
$wdhSettings['form_msg_class']  = '';                                           // add form class to sent message container
$wdhSettings['form_msg_css']    = array();                                      // set css propeties example array('color' => '#000');

// Email Messages
//-----------------------------------

$wdhSettings['sender_email']             = 'sender@yourwebsite.com';        // Set email sender 
$wdhSettings['admin_email_notification'] = true;                            // Set true if you want to send admin notification when somebody register / contact
$wdhSettings['admin_email']              = 'admin@yourwebsite.com';         // Set admin email where you want to be notify
$wdhSettings['admin_subject']            = 'Your admin subject.';           // Set admin email notification subject
$wdhSettings['admin_email_template']     = 'standard';                      // Select html mail template ( your_path/wdhedfp/plugins/formgenerator/standard/ )
$wdhSettings['user_email_notification']  = true;                            // Set true if you want to send user fields when use a form
$wdhSettings['user_email_subject']       = 'Your user subject.';            // Set user email notification subject

// JS HOOK AFTER FORM SENT DATA
//-----------------------------------
$wdhSettings['js_wdhedfp_after_save']    = ''; // Return fields array is window.valueNow - Write every javascript code you want with escape ( use instead of " this \" and $jWDH instead of $ or jQuery).
// Example : $wdhSettings['js_wdhedfp_after_save'] = 'console.log(\"Value: \"+window.valueNow);';


// WEBSITE REQUEST -------------------------------------------------------------
$wdhSettings['WDH_WEBSITE_URL'] = $wdhFIELD['wdhedfp_url'];     // Don't Modify
// -----------------------------------------------------------------------------

//--- Form Field 
//-----------------------------------
 // DB Settings
$wdhFIELD['name']            = '';      // db field name
$wdhFIELD['table']           = '';      // field db table 
// ID
$wdhFIELD['id']              = '';      // field id (number from 1 to 99999...)
// Field Settings
$wdhFIELD['field_class']     = '';      // field css class
$wdhFIELD['field_css']       = '';        // field css
// Label Settings
$wdhFIELD['label']           = '';      // field label
$wdhFIELD['second_label']    = '';      // second field label is used for re-password label
$wdhFIELD['value']           = '';      // field value / only for hidden & price field type
$wdhFIELD['link']            = '';      // field link  / only for link field type
$wdhFIELD['class']           = '';      // field label class
$wdhFIELD['css']             = array(); // css for field label
$wdhFIELD['label_position']  = 0;       // display label position : 0 - display before input
// Input Settings
$wdhINPUT['type']            = 'text';  // text , textarea, select , radio button, date , slider, checkbox, switch on/off button, map, video, colorpicker, password, image, file, html_editor;
$wdhINPUT['class']           = '';      // input class
$wdhINPUT['css']             = array(); // css for input
$wdhINPUT['values']          = 'label 1@@ value 1|label 2@@ value 2'; // select, radio, checkbox
// Tooltip 
$wdhTOOLTIP['text']         = 'Click here to edit.';    // set your field tooltip text ( leave it blank if you don't want tooltip ) 

?>